<?php
namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

use Tymon\JWTAuth\Facades\JWTAuth;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Tymon\JWTAuth\Exceptions\JWTException;

class AuthController extends Controller
{


    public function __construct()
    {
        $this->middleware('auth:api', ['except' => ['login','register']]);
    }
    /**
     * Register a new user
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'gender' => 'required|string|in:male,female',
            'date_of_birth' => 'required|date',
            'username' => 'required|string|unique:users|max:255',
            'password' => 'required|string|min:8',
            'profile_photo' => 'image|max:2048',
            'role_id' => 'required|exists:roles,id',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $requestData = $request->all();
        $requestData['password'] = Hash::make($requestData['password']);
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
            $requestData['profile_photo'] = $filename;
        }
        $user = User::create($requestData);
        $token = JWTAuth::fromUser($user);

        return response()->json(['user' => $user, 'access_token' => $token], 201);
    }

    /**
     * Login user and create token
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function login(Request $request)
    {
        $credentials = $request->only('username', 'password');

        try {
            if (!$token = JWTAuth::attempt($credentials)) {
                return response()->json(['error' => 'Invalid credentials'], 401);
            }
        } catch (JWTException $e) {
            return response()->json(['error' => 'Could not create token'], 500);
        }

        return response()->json(compact('token'));
    }

    /**
     * Logout user (Revoke the token)
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function logout()
    {
        JWTAuth::invalidate(JWTAuth::getToken());

        return response()->json(['message' => 'Logout successful']);
    }

    /**
     * Refresh access token
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function refresh()
    {
        try {
            $newToken = JWTAuth::parseToken()->refresh();
        } catch (JWTException $e) {
            return response()->json(['error' => 'Could not refresh token'], 500);
        }

        return response()->json(compact('newToken'));
    }

}
