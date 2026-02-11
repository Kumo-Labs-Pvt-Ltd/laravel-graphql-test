<?php

namespace App\GraphQL\Mutations;

use App\Models\Organization;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class Register
{
    public function __invoke($_, array $args)
    {
        $input = $args['input'];

        return DB::transaction(function () use ($input) {
            // Create Organization
            $organization = Organization::create([
                'name' => $input['name'],
                'slug' => Str::slug($input['name']) . '-' . Str::random(6),
                'joined_date' => now(),
                'is_active' => true,
            ]);

            // Create User
            $user = User::create([
                'organization_id' => $organization->id,
                'name' => $input['name'],
                'email' => $input['email'],
                'password' => Hash::make($input['password']),
                'is_active' => true,
            ]);

            // Create Token
            $token = $user->createToken('LedgerApp')->plainTextToken;

            return [
                'token' => $token,
                'user' => $user,
            ];
        });
    }
}