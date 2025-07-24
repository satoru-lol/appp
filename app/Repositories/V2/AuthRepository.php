<?php

namespace App\Repositories\V2;

use App\Models\User;
use App\Models\Introduction;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class AuthRepository
{
    public function findUserByEmail(string $email): ?User
    {
        return User::where('email', $email)->first();
    }

    public function findUserByPhone(string $phone): ?User
    {
        return User::where('phone', $phone)->first();
    }

    public function createUser(array $data): User
    {
        return User::create([
            'firstname' => $data['firstname'],
            'lastname' => $data['lastname'],
            'email' => $data['email'],
            'phone' => $data['phone'] ?? null,
            'password' => Hash::make($data['password']),
            'email_verified_at' => now(),
        ]);
    }

    public function updateUserPassword(User $user, string $password): bool
    {
        return $user->update([
            'password' => Hash::make($password)
        ]);
    }

    public function verifyUserPhone(User $user): bool
    {
        return $user->update([
            'phone_verified_at' => now()
        ]);
    }

    public function verifyUserEmail(User $user): bool
    {
        return $user->update([
            'email_verified_at' => now()
        ]);
    }

    public function createIntroduction(array $data): Introduction
    {
        return Introduction::create($data);
    }

    public function findIntroductionByEmail(string $email): ?Introduction
    {
        return Introduction::where('email', $email)->first();
    }

    public function findIntroductionByPhone(string $phone): ?Introduction
    {
        return Introduction::where('phone', $phone)->first();
    }

    public function updateIntroduction(Introduction $introduction, array $data): bool
    {
        return $introduction->update($data);
    }

    public function createPasswordReset(string $email, string $token): void
    {
        DB::table('password_resets')->where('email', $email)->delete();
        DB::table('password_resets')->insert([
            'email' => $email,
            'token' => Hash::make($token),
            'created_at' => now(),
        ]);
    }

    public function findPasswordReset(string $email): ?object
    {
        return DB::table('password_resets')
            ->where('email', $email)
            ->first();
    }

    public function deletePasswordReset(string $email): void
    {
        DB::table('password_resets')->where('email', $email)->delete();
    }

    public function isEmailTaken(string $email, ?int $excludeUserId = null): bool
    {
        $query = User::where('email', $email);
        
        if ($excludeUserId) {
            $query->where('id', '!=', $excludeUserId);
        }
        
        return $query->exists();
    }

    public function isPhoneTaken(string $phone, ?int $excludeUserId = null): bool
    {
        $query = User::where('phone', $phone);
        
        if ($excludeUserId) {
            $query->where('id', '!=', $excludeUserId);
        }
        
        return $query->exists();
    }

    public function updateUserLastLogin(User $user): bool
    {
        return $user->update([
            'last_login_at' => now()
        ]);
    }

    public function getUserByVerificationCode(string $code): ?User
    {
        return User::where('verification_code', $code)->first();
    }

    public function setVerificationCode(User $user, string $code): bool
    {
        return $user->update([
            'verification_code' => $code
        ]);
    }

    public function clearVerificationCode(User $user): bool
    {
        return $user->update([
            'verification_code' => null
        ]);
    }

    public function getUserStats(): array
    {
        return [
            'total_users' => User::count(),
            'verified_users' => User::whereNotNull('email_verified_at')->count(),
            'phone_verified_users' => User::whereNotNull('phone_verified_at')->count(),
            'recent_registrations' => User::where('created_at', '>=', now()->subDays(7))->count(),
        ];
    }
}