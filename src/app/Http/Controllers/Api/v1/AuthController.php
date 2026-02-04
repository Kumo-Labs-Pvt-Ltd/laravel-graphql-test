<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Controller;
use App\Http\Resources\Api\v1\User\UserResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

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

                $success['token']    = $user->createToken('LedgerApp')->plainTextToken;
                $success['id']       = $user->id;
                $success['name']     = $user->name;
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
                'trace' => $e->getTraceAsString()
            ]);

            return $this->sendError(
                'Something went wrong. Please try again later.',
                $e->getMessage(),
                500
            );
        }
    }
}
