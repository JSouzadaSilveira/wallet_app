<?php

namespace App\Http\Controllers;

use App\Models\Account;
use App\Models\Transaction;
use GuzzleHttp\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TransactionController extends Controller
{
    public function index(Request $request) {
        $query = Transaction::query();

        if ($request->has('source_account_id')) {
            $query->where('source_account_id', $request->source_account_id);
        }

        if ($request->has('destination_account_id')) {
            $query->where('destination_account_id', $request->destination_account_id);
        }

        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        $sortBy = $request->get('sort_by', 'created_at');
        $sortOrder = $request->get('sort_order', 'desc');

        $query->orderBy($sortBy, $sortOrder);

        $transactions = $query->paginate($request->get('per_page', 10));
        return response()->json($transactions);
    }

    public function store(Request $request) {
        $request->validate([
            'source_account_id' => 'required|exists:accounts,id',
            'destination_account_id' => 'required|exists:accounts,id',
            'amount' => 'required|numeric|min:0',
        ]);

        DB::beginTransaction();

        $ipAddress = $request->ip();
        $location = $this->getLocationFromIp($ipAddress);

        $transaction = Transaction::create([
            'source_account_id' => $request->source_account_id,
            'destination_account_id' => $request->destination_account_id,
            'amount' => $request->amount,
            'status' => 'pending',
            'ip_address' => $ipAddress,
            'location' => $location,
        ]);

        try {
            $sourceAccount = Account::find($request->source_account_id);
            $destinationAccount = Account::find($request->destination_account_id);

            if ($sourceAccount->balance < $request->amount) {
                $transaction->update(['status' => 'failed']);
                return response()->json(['message' => 'Insufficient funds'], 400);
            }

            $sourceAccount->balance -= $request->amount;
            $destinationAccount->balance += $request->amount;

            $sourceAccount->save();
            $destinationAccount->save();

            $transaction->update(['status' => 'success']);

            DB::commit();

            return response()->json($transaction, 201);
        } catch (\Exception $e) {
            DB::rollBack();

            $sourceAccount->balance += $request->amount;
            $sourceAccount->save();

            $transaction->update(['status' => 'failed', 'success' => false]);

            return response()->json(['message' => 'Transaction failed', 'error' => $e->getMessage()], 500);
        }
    }

    public function show(string $id) {
        $transaction = Transaction::find($id);

        if (!$transaction) {
            return response()->json(['message' => 'Transaction not found'], 404);
        }

        return response()->json($transaction);
    }

    private function getLocationFromIp($ip) {
        $client = new Client();
        $response = $client->request('GET', "http://ip-api.com/json/{$ip}");

        if ($response->getStatusCode() === 200) {
            $data = json_decode($response->getBody(), true);
            return $data['city'] . ', ' . $data['regionName'] . ', ' . $data['country'];
        }

        return null;
    }
}
