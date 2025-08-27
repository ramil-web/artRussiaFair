<?php

namespace Database\Seeders;

use App\Models\User;
use Hash;
use Illuminate\Database\Seeder;
use App\Models\Role;
class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(): void
    {
        $users = $this->getUsers();

        foreach ($users as $user) {
            User::firstOrCreate(
                ['username' => 'admin'], // условия поиска
                [
                    'email' => 'admin@synergy.ru',
                    'password' => Hash::make('secret'),
                ]
            );
        }
//dd(Role::findByName('super_admin','admin'));

        User::find(1)->assignRole('super_admin');
        User::find(2)->assignRole('manager');
        User::find(3)->assignRole('commission');
       $rol= Role::findByName('participant');
        User::find(4)->assignRole($rol);
        $rol= Role::findByName('resident');
        User::find(5)->assignRole($rol);
    }

    private function getUsers(): array
    {
        return json_decode($this->getFile(), true);
    }


    private function getPath(): string
    {
        return 'database/seeders/json_resources/users.json';
    }

    private function getFile(): bool|string
    {
        return file_get_contents($this->getPath());
    }
}
