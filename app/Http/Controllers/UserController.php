<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Models\User;
use GuzzleHttp\Client;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function index(Request $request): JsonResponse {
        $query = User::query();

        if ($request->has('search') && !empty($request->input('search'))) {
            $search = $request->input('search');
            $query->where('name', 'like', '%' . $search . '%')
                  ->orWhere('email', 'like', '%' . $search . '%')
                  ->orWhere('document', 'like', '%' . $search . '%');
        }

        if ($request->has('sort') && in_array($request->input('sort'), ['name', 'email', 'document'])) {
            $sortDirection = $request->input('direction', 'asc');
            $query->orderBy($request->input('sort'), $sortDirection);
        }

        $users = $query->paginate(10);

        return response()->json($users, 200);
    }

    public function store(StoreUserRequest $request): JsonResponse {
        $this->validateZip($request->zip, $request->state, $request->city);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'document' => $request->document,
            'phone' => $request->phone,
            'address' => $request->address,
            'city' => $request->city,
            'state' => $request->state,
            'zip' => $request->zip
        ]);

        return response()->json($user, 201);
    }

    public function show(string $id): JsonResponse {
        try {
            $user = User::findOrFail($id);
            return response()->json($user, 200);

        } catch (ModelNotFoundException $e) {
            return response()->json(['message' => 'User not found'], 404);
        }
    }

    public function update(UpdateUserRequest $request, string $id): JsonResponse {
        $user = User::find($id);
        
        if (!$user) return response()->json(['message' => 'User not found'], 404);
        
        $authenticatedUser = Auth::user();

        if ($authenticatedUser->id !== $user->id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }
        
        $user->update($request->validated());

        return response()->json($user, 200);
    }

    public function destroy(string $id): JsonResponse {
        $user = User::find($id);

        if (!$user) return response()->json(['message' => 'User not found'], 404);

        $authenticatedUser = Auth::user();

        if ($authenticatedUser->id !== $user->id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $user->delete();

        return response()->json(['message' => 'User deleted'], 200);
    }

    private function validateZip($cep, $state, $city): void {
        $client = new Client();
        $response = $client->request('GET', "https://viacep.com.br/ws/{$cep}/json/");
    
        if ($response->getStatusCode() !== 200) {
            throw new \Exception('Invalid CEP format.');
        }
    
        $data = json_decode($response->getBody(), true);
    
        if (isset($data['erro']) && $data['erro']) {
            throw new \Exception('CEP not found.');
        }
    
        $normalizedState = mb_strtolower(trim($state));
        $normalizedCity = mb_strtolower(trim($this->removeAccents($city)));
        
        $responseState = mb_strtolower(trim($data['uf']));
        $responseCity = mb_strtolower(trim($this->removeAccents($data['localidade'])));
    
        if ($responseState !== $normalizedState || $responseCity !== $normalizedCity) {
            throw new \Exception('State or city does not match the provided CEP.');
        }
    }

    private function removeAccents($string) {
        $unwanted_array = [
            'á', 'à', 'ã', 'â', 'ä', 'å',
            'é', 'è', 'ê', 'ë',
            'í', 'ì', 'î', 'ï',
            'ó', 'ò', 'õ', 'ô', 'ö',
            'ú', 'ù', 'û', 'ü',
            'ç',
            'Á', 'À', 'Ã', 'Â', 'Ä', 'Å',
            'É', 'È', 'Ê', 'Ë',
            'Í', 'Ì', 'Î', 'Ï',
            'Ó', 'Ò', 'Õ', 'Ô', 'Ö',
            'Ú', 'Ù', 'Û', 'Ü',
            'Ç'
        ];
    
        $replacement_array = [
            'a', 'a', 'a', 'a', 'a', 'a',
            'e', 'e', 'e', 'e',
            'i', 'i', 'i', 'i',
            'o', 'o', 'o', 'o', 'o',
            'u', 'u', 'u', 'u',
            'c',
            'a', 'a', 'a', 'a', 'a', 'a',
            'e', 'e', 'e', 'e',
            'i', 'i', 'i', 'i',
            'o', 'o', 'o', 'o', 'o',
            'u', 'u', 'u', 'u',
            'c'
        ];
    
        return str_replace($unwanted_array, $replacement_array, $string);
    }
}
