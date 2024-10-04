<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Models\User;
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

    public function update(UpdateUserRequest $request, string $id): JsonResponse
    {
        dd(1);
        $user = User::find($id);
        
        if (!$user) return response()->json(['message' => 'User not found'], 404);
        
        $authenticatedUser = Auth::user();

        if ($authenticatedUser->id !== $user->id && !$authenticatedUser->isAdmin()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }
        
        $user->update($request->validated());

        return response()->json($user, 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
