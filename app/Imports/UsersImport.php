<?php

namespace App\Imports;

use App\Models\User;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\Importable;
use Illuminate\Support\Facades\Hash;
use Maatwebsite\Excel\Concerns\WithProgressBar;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Mail;
use Maatwebsite\Excel\Concerns\WithStartRow;
use App\Mail\SendCreateUser;
use App\Traits\APICalls;

class UsersImport implements ToModel, WithStartRow
{
    use APICalls;

    public function model(array $row)
    {
        //Exist user
        $userExist = User::where('email', $row[2])->first();

        if($row[5]){
            $passwordText = $row[6];
            $password = Hash::make($row[6]);
        } else {
            $randomString = Str::random(10);
            $passwordText = $randomString;
            $password = Hash::make($randomString);
        }

        if(!$userExist){
            return new User([
                'name'     => $row[0],
                'last_name'    => $row[1],
                'email'    => $row[2],
                'lang'    => $row[3],
                'password' => $password,
            ]);

            //Crear usuario remoto
            $data = $this->createUser($row[0],$row[1],$row[2],$request->gender,$request->locale);

            if($data['data']['createRespondent']){
                //Buscar el usuario creado y enviar el correo
                $user = User::where('email', $row[2])->first();
                $user->account_id = $data['data']['createRespondent']['respondent']['id'];
                $user->syncRoles($row[4]);

                Mail::to($user->email)->send(new SendCreateUser($user,$passwordText));
            }
        }
    }

    public function startRow(): int
    {
        return 2;
    }
}
