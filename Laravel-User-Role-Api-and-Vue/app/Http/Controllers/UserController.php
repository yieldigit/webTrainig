<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
    public function index()
    {
        $users = User::with('role')->get();
        return UserResource::collection($users);
    }
    
    
    

    public function store(Request $request)
    {
        if (!Auth::check()) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }
        $rules = [
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'gender' => 'required|string|in:male,female',
            'date_of_birth' => 'required|date',
            'username' => 'required|string|unique:users|max:255',
            'password' => 'required|string|min:8',
            'profile_photo' => 'image|max:2048',
            'role_id' => 'required|exists:roles,id',
            'phone_number' => [
                'required',
                'numeric',
                'unique:users',
            ],
        ];

        $validatedData = $request->validate($rules);

        // Créer un nouvel utilisateur avec les données validées
        $user = new User([
            'first_name' => $validatedData['first_name'],
            'last_name' => $validatedData['last_name'],
            'gender' => $validatedData['gender'],
            'date_of_birth' => $validatedData['date_of_birth'],
            'username' => $validatedData['username'],
            'password' => Hash::make($validatedData['password']),
            'phone_number' => $validatedData['phone_number'],
            'role_id' => $validatedData['role_id'],
        ]);

        // Enregistrer l'image de profil si elle est fournie
        if ($request->hasFile('profile_photo')) {
            $file = $request->file('profile_photo');
            $extension = $file->getClientOriginalExtension();
            $allowedExtensions = ['jpg', 'jpeg', 'png'];
            if (!in_array($extension, $allowedExtensions)) {
                return response()->json(['error' => 'Invalid file type. Only JPG, JPEG, and PNG files are allowed.'], 400);
            }
            $filename = time() . '_' . str_replace(' ', '', $file->getClientOriginalName());
            $path = $file->storeAs('public/profile-photos', $filename);
            $user->profile_photo = $filename;
        }

        $user->save();

        // Retourner la réponse appropriée
        return response()->json(['message' => 'Utilisateur créé avec succès'], 201);
    }


    public function show($id)
    {
        $user = User::with('role')->findOrFail($id);
        return new UserResource($user);
    }
    

    public function update(Request $request, $id)
    {
        $rules = [
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'gender' => 'required|string|in:male,female',
            'date_of_birth' => 'required|date',
            'username' => 'required|string|unique:App\Models\User,username|max:255',
            'profile_photo' => 'image|max:2048',
            'role_id' => 'required|exists:roles,id',
            'phone_number' => [
                'required',
                'numeric',

            ],
        ];

        $validatedData = $request->validate($rules);

        $user = User::findOrFail($id);
        if ($request->hasFile('profile_photo')) {
            $file = $request->file('profile_photo');
            $extension = $file->getClientOriginalExtension();
            $allowedExtensions = ['jpg', 'jpeg', 'png'];
            if (!in_array($extension, $allowedExtensions)) {
                return response()->json(['error' => 'Invalid file type. Only JPG, JPEG, and PNG files are allowed.'], 400);
            }
            // Supprimer l'ancienne image de profil si elle existe
            if ($user->profile_photo && Storage::exists('public/profile-photos/' . $user->profile_photo)&&$request->profile_photo) {
                Storage::delete('public/profile-photos/' . $user->profile_photo);
            }
            $filename = time() . '_' . str_replace(' ', '', $file->getClientOriginalName());
            $path = $file->storeAs('public/profile-photos', $filename);
            $user->profile_photo = $filename;
        }
        $user->update([
            'first_name' => $validatedData['first_name'],
            'last_name' => $validatedData['last_name'],
            'gender' => $validatedData['gender'],
            'date_of_birth' => $validatedData['date_of_birth'],
            'username' => $validatedData['username'],
            'profile_photo' => $filename,
            'role_id' => $validatedData['role_id'],
            'phone_number' => $validatedData['phone_number'],
        ]);


        return response()->json(['message' => 'User updated successfully', 'user' => $user]);
    }

    public function destroy($id)
    {
        $user = User::findOrFail($id);

        // Supprimer l'image de profil du stockage s'il en existe une
        if ($user->profile_photo) {
            Storage::delete($user->profile_photo);
        }

        $user->delete();
        return response()->json(['message' => 'User deleted successfully']);
    }
}
