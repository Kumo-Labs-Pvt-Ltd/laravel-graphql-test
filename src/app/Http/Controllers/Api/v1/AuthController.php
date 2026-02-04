<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Resources\Api\v1\User\UserResource;
use App\Models\Organization;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class AuthController extends BaseController
{
    /**
     * User Login
     *
     * Authenticate a user and return an access token.
     *
     * @unauthenticated
     */
    public function login(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'email' => 'required|string|email',
                'password' => 'required|string',
            ]);

            if ($validator->fails()) {
                return $this->sendError('Validation Failed.', $validator->errors(), 422);
            }

            if (Auth::attempt(['email' => $request->email, 'password' => $request->password])) {
                $user = Auth::user();

                $success['token'] = $user->createToken('LedgerApp')->plainTextToken;
                $success['id'] = $user->id;
                $success['name'] = $user->name;
                $success['email'] = $user->email;
                // $success['role']    = $user->role->name;

                return $this->sendResponse('Logged In Successfully', ['token' => $success['token'], 'user' => new UserResource($user)], 200);
            } else {
                return $this->sendError('These Credentials Do Not Match Our Records.', null, 401);
            }
        } catch (\Throwable $e) {
            Log::error('Login failed', [
                'error' => $e->getMessage(),
                'email' => $request->email ?? null,
                'ip' => $request->ip(),
                'trace' => $e->getTraceAsString(),
            ]);

            return $this->sendError(
                'Something went wrong. Please try again later.',
                $e->getMessage(),
                500
            );
        }
    }

    /**
     * User Register
     *
     * Register a user and return it with an access token.
     *
     * @unauthenticated
     */
    public function register(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'name' => 'required|string|max:255',
                'email' => 'required|string|email|unique:users,email',
                'password' => 'required|string|min:6|confirmed',
            ]);

            if ($validator->fails()) {
                return $this->sendError('Validation Failed.', $validator->errors(), 422);
            }

            // Create Organization
            $organization = Organization::create([
                'name' => $request->name,
                'slug' => Str::slug($request->name).'-'.Str::random(6),
                'joined_date' => now(),
                'is_active' => true,
            ]);

            // Create User
            $user = User::create([
                'organization_id' => $organization->id,
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'is_active' => true,
            ]);

            // Create Token
            $token = $user->createToken('LedgerApp')->plainTextToken;

            return $this->sendResponse(
                'Registered Successfully',
                [
                    'token' => $token,
                    'user' => new UserResource($user),
                ],
                201
            );

        } catch (\Throwable $e) {
            Log::error('Registration failed', [
                'error' => $e->getMessage(),
                'email' => $request->email ?? null,
                'ip' => $request->ip(),
                'trace' => $e->getTraceAsString(),
            ]);

            return $this->sendError(
                'Something went wrong. Please try again later.',
                null,
                500
            );
        }
    }
}
