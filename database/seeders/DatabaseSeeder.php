<?php

namespace Database\Seeders;
use Spatie\Permission\Models\Role;
use App\Models\User;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $administrator = Role::create([
            'name' => 'administrator'
        ]);
        $institution = Role::create([
            'name' => 'institution'
        ]);

        $coordinator = Role::create([
            'name' => 'coordinator'
        ]);

        $respondent = Role::create([
            'name' => 'respondent'
        ]);
        
        $user = new User();
        $user->name = 'Braulio Miramontes';
        $user->email = 'braulio@felamedia.com';
        $user->password = bcrypt('password');
        $user->assignRole($administrator); 
        $user->save();

        $user = new User();
        $user->name = 'Universidad Autonoma de Nayarit';
        $user->email = 'institucion@felamedia.com';
        $user->password = bcrypt('password');
        $user->assignRole($institution);
        $user->save();

        /*
        $user = new User();
        $user->name = 'Estudiante';
        $user->email = 'encuestado@felamedia.com';
        $user->password = bcrypt('password');
        $user->account_id = 121212;
        $user->assignRole($respondent); 
        $user->save();
        */
    }
}
