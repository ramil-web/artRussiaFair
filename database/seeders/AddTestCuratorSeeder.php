<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class AddTestCuratorSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $users = [
            [
                "username" => "manager_2",
                "email"    => "manager_2@mail.ru",
                "password" => "manager_2"
            ],

            [
                "username" => "manager_3",
                "email"    => "manager_3@mail.ru",
                "password" => "manager_3"
            ]
        ];
        $userIds = [];

        foreach ($users as $user) {
            $user = User::create([
                'username' => $user['username'],
                'email'    => $user['email'],
                'password' => $user['password']
            ]);
            $userIds[] = $user->id;
        }
        User::find($userIds[0])->assignRole('manager');
        User::find($userIds[1])->assignRole('manager');
    }
}
