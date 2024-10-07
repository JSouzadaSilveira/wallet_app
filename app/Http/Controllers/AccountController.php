<?php
namespace App\Http\Controllers;

use App\Models\Account;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class AccountController extends Controller
{
    public function index() {
        $accounts = Account::all();
        return response()->json($accounts);
    }

    public function show($id) {
        $account = Account::find($id);
        
        if (!$account) {
            return response()->json(['message' => 'Account not found'], 404);
        }

        return response()->json($account);
    }

    public function store(Request $request) {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required|exists:users,id',
            'account_number' => 'required|unique:accounts',
            'balance' => 'required|numeric',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $account = Account::create($request->all());
        return response()->json($account, 201);
    }

    public function update(Request $request, $id) {
        $account = Account::find($id);
        
        if (!$account) {
            return response()->json(['message' => 'Account not found'], 404);
        }

        $validator = Validator::make($request->all(), [
            'account_number' => 'sometimes|required|unique:accounts,account_number,' . $id,
            'balance' => 'sometimes|required|numeric',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $account->update($request->all());
        return response()->json($account);
    }

    public function destroy($id) {
        $account = Account::find($id);
        
        if (!$account) {
            return response()->json(['message' => 'Account not found'], 404);
        }

        $account->delete();
        return response()->json(['message' => 'Account deleted successfully']);
    }
}
