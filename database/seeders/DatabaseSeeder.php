<?php

namespace Database\Seeders;
use Spatie\Permission\Models\Role;
use App\Models\User;
use Ramsey\Uuid\Uuid;

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
        
        $user = new User();
        $user->name = 'Braulio Miramontes';
        $user->email = 'braulio@felamedia.com';
        $user->password = bcrypt('password');
        $user->assignRole('administrator'); 
        $user->save();

        $user = new User();
        $user->name = 'Universidad Autonoma de Nayarit';
        $user->email = 'institucion@felamedia.com';
        $user->password = bcrypt('password');
        $user->assignRole('institution');
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
