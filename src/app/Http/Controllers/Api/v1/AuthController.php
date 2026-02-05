<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Resources\Api\v1\User\UserResource;
use App\Mail\ResetPasswordMail;
use App\Models\Organization;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
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
                'password' => 'required|string|min:8|confirmed',
            ]);

            if ($validator->fails()) {
                return $this->sendError('Validation Failed.', $validator->errors(), 422);
            }

            $result = DB::transaction(function () use ($request) {

                // Create Organization
                $organization = Organization::create([
                    'name' => $request->name,
                    'slug' => Str::slug($request->name) . '-' . Str::random(6),
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

                return [
                    'token' => $token,
                    'user' => $user,
                ];
            });

            return $this->sendResponse(
                'Registered Successfully',
                [
                    'token' => $result['token'],
                    'user' => new UserResource($result['user']),
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

    /**
     * Forgot Password
     *
     * Send a password reset link to the user's email.
     *
     * @unauthenticated  // If this is a public endpoint
     */
    public function forgotPassword(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'email' => 'required|email|exists:users,email',
            ]);

            if ($validator->fails()) {
                return $this->sendError('Validation Failed.', $validator->errors(), 422);
            }

            $user = User::where('email', $request->email)->first();

            // Generate random token
            $token = Str::random(64);

            // Delete any existing tokens for this email
            DB::table('password_reset_tokens')->where('email', $request->email)->delete();

            // Store token in database
            DB::table('password_reset_tokens')->insert([
                'email' => $request->email,
                'token' => Hash::make($token), // Store hashed token
                'created_at' => Carbon::now(),
            ]);

            // Create reset URL for frontend
            $resetUrl = config('app.site_url') . '/reset-password?token=' . $token . '&email=' . urlencode($request->email);

            // Send email with reset link
            Mail::to($user->email)->send(new ResetPasswordMail($user, $resetUrl));

            return $this->sendResponse(
                'Password reset link sent to your email',
                null,
                200
            );
        } catch (\Throwable $e) {
            Log::error('Forgot password failed', [
                'error' => $e->getMessage(),
                'email' => $request->email ?? null,
                'ip' => $request->ip(),
                'trace' => $e->getTraceAsString(),
            ]);

            return $this->sendError(
                'Failed to send reset link. Please try again later',
                null,
                500
            );
        }
    }

    /**
     * Reset Password
     *
     * Reset the user's password using the provided token.
     *
     * @unauthenticated  // If this is a public endpoint
     */
    public function resetPassword(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'email' => 'required|email|exists:users,email',
                'token' => 'required|string',
                'password' => 'required|string|min:8|confirmed',
            ]);

            if ($validator->fails()) {
                return $this->sendError('Validation Failed.', $validator->errors(), 422);
            }

            // Find token record
            $resetRecord = DB::table('password_reset_tokens')
                ->where('email', $request->email)
                ->first();

            if (!$resetRecord) {
                return $this->sendError('Invalid or expired reset token', null, 400);
            }

            // Verify token matches
            if (!Hash::check($request->token, $resetRecord->token)) {
                return $this->sendError('Invalid reset token', null, 400);
            }

            // Check if token is expired (60 minutes)
            if (Carbon::parse($resetRecord->created_at)->addMinutes(60)->isPast()) {
                DB::table('password_reset_tokens')->where('email', $request->email)->delete();

                return $this->sendError('Reset token has expired. Please request a new one', null, 400);
            }

            // Update user password
            $user = User::where('email', $request->email)->first();
            $user->update([
                'password' => Hash::make($request->password),
            ]);

            // Delete the used token
            DB::table('password_reset_tokens')->where('email', $request->email)->delete();

            $token = $user->createToken('LedgerApp')->plainTextToken;

            return $this->sendResponse(
                'Password reset successfully',
                ['token' => $token, 'user' => new UserResource($user)],
                200
            );
        } catch (\Throwable $e) {
            Log::error('Reset password failed', [
                'error' => $e->getMessage(),
                'email' => $request->email ?? null,
                'ip' => $request->ip(),
                'trace' => $e->getTraceAsString(),
            ]);

            return $this->sendError(
                'Failed to reset password. Please try again later',
                null,
                500
            );
        }
    }

    /**
     * Verify Reset Token
     *
     * Verify if the provided password reset token is valid.
     *
     * @unauthenticated
     */
    public function verifyToken(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email|exists:password_reset_tokens,email',
            'token' => 'required|string',
        ]);

        if ($validator->fails()) {
            return $this->sendError('Validation Failed.', $validator->errors(), 422);
        }

        $resetRecord = DB::table('password_reset_tokens')
            ->where('email', $request->email)
            ->first();

        // Check if token is expired (60 minutes)
        if (Carbon::parse($resetRecord->created_at)->addMinutes(60)->isPast()) {
            DB::table('password_reset_tokens')->where('email', $request->email)->delete();

            return $this->sendError('Reset token has expired. Please request a new one', null, 400);
        }

        if (!$resetRecord) {
            return $this->sendError('Password Reset Link Has Been Expired', null, 400);
        }

        if (!Hash::check($request->token, $resetRecord->token)) {
            return $this->sendError('Password Reset Link Has Been Expired', null, 400);
        }

        return $this->sendResponse(
            'Reset token is valid',
            null,
            200
        );
    }

    /**
     * Logout
     *
     * Logs user out by revoking the current access token.
     */
    public function logout()
    {
        try {
            auth('sanctum')->user()->currentAccessToken()->delete();

            return $this->sendResponse('User Logged Out', null, 200);
        } catch (\Throwable $e) {
            return $this->sendError(
                'Something went wrong. Please try again later.',
                null,
                500
            );
        }
    }

    /**
     * Profile
     *
     * Returns the currently authenticated user's profile
     */
    public function profile()
    {
        try {
            $user = auth('sanctum')->user();

            if (!$user) {
                return $this->sendError('Unauthenticated', null, 401);
            }

            return $this->sendResponse(
                'User profile retrieved successfully',
                new UserResource($user),
                200
            );
        } catch (\Throwable $e) {
            return $this->sendError(
                'Something went wrong. Please try again later.',
                null,
                500
            );
        }
    }
}
