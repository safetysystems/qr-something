<?php

namespace App\Services;

use App\Models\User;
use App\Models\Workplace;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class RegistrationService
{
    /**
     * @param  array<string, mixed>  $data
     */
    public function register(array $data): User
    {
        return DB::transaction(function () use ($data): User {
            $user = User::query()->create([
                'name' => $data['name'],
                'email' => $data['email'],
                'password' => $data['password'],
            ]);

            $workplace = Workplace::query()->create([
                'uuid' => (string) Str::uuid(),
                'name' => $data['workplace_name'],
                'code' => $this->generateUniqueWorkplaceCode($data['workplace_name']),
                'primary_contact_name' => $data['name'],
                'primary_contact_email' => $data['email'],
            ]);

            $workplace->users()->attach($user->id, ['role' => 'owner']);

            return $user->fresh();
        });
    }

    private function generateUniqueWorkplaceCode(string $workplaceName): string
    {
        $base = Str::of($workplaceName)
            ->slug('-')
            ->value();

        $base = $base !== '' ? $base : 'workplace';
        $base = Str::limit($base, 40, '');
        $candidate = $base;
        $suffix = 2;

        while (Workplace::query()->where('code', $candidate)->exists()) {
            $suffixValue = '-'.$suffix;
            $candidate = Str::limit($base, 40 - strlen($suffixValue), '').$suffixValue;
            $suffix++;
        }

        return $candidate;
    }
}
