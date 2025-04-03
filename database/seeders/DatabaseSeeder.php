<?php

namespace Database\Seeders;
use Spatie\Permission\Models\Role;
use App\Models\User;
use Ramsey\Uuid\Uuid;
use App\Models\Config;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $administrator = Role::create([
            'name' => 'administrator',
            'uuid' => Uuid::uuid4()
        ]);

        $institution = Role::create([
            'name' => 'institution',
            'uuid' => Uuid::uuid4()
        ]);

        $coordinator = Role::create([
            'name' => 'coordinator',
            'uuid' => Uuid::uuid4()
        ]);

        $respondent = Role::create([
            'name' => 'respondent',
            'uuid' => Uuid::uuid4()
        ]);

        $config = new Config();
        $config->save();

        $user = new User();
        $user->name = 'Braulio Miramontes';
        $user->email = 'braulio@felamedia.com';
        $user->password = bcrypt('password');
        $user->assignRole('administrator');
        $user->save();

        $user = new User();
        $user->name = 'Jorge Fela';
        $user->email = 'jorge@felamedia.com';
        $user->password = bcrypt('password');
        $user->assignRole('administrator');
        $user->save();

        $user = new User();
        $user->name = 'Magda Vargas';
        $user->email = 'magda@magdavargas.com';
        $user->password = bcrypt('M@gd@2025!');
        $user->assignRole('administrator');
        $user->save();

        $user = new User();
        $user->name = 'Institution';
        $user->email = 'institution@example.com';
        $user->password = bcrypt('password');
        $user->assignRole('institution');
        $user->save();

    }
}
