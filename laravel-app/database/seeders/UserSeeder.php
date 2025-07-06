<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Role;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $owner = User::create([
            'name' => 'Demo Owner',
            'email' => 'owner@example.com',
            'password' => bcrypt('password'),
        ]);


        $guest = User::create([
            'name' => 'Demo Guest',
            'email' => 'guest@example.com',
            'password' => bcrypt('password'),
        ]);

        $ownerRole = Role::where('name', 'Owner')->first();
        $guestRole = Role::where('name', 'Guest')->first();

        if ($ownerRole) {
            $owner->roles()->attach($ownerRole->id);
        }

        if ($guestRole) {
            $guest->roles()->attach($guestRole->id);
        }

        $anotherOwner = User::create([
            'name' => 'Second Owner',
            'email' => 'owner2@example.com',
            'password' => bcrypt('password123'),
        ]);

        if ($ownerRole) {
            $anotherOwner->roles()->attach($ownerRole->id);
        }
    }
}
