<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Support\Str;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, HasRoles;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'last_name',
        'lang',
        'email',
        'account_id',
        'platform',
        'password',
        'user_id'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    protected static function boot()
    {
        parent::boot();
        static::creating(function ($model) {
            $model->uuid = Str::uuid();
        });
    }

    public function institution()
    {
        return $this->hasOne(User::class,'id','user_id');
    }

    public function getFullNameAttribute()
    {
        if($this->name_institution){
            return $this->name_institution;
        } else {
            return $this->name.' '.$this->last_name;
        }
    }

    public function getEmailCutAttribute()
    {
        if(strlen($this->email) > 20){
            return substr($this->email,0,25).'...';
        } else {
            return $this->email;
        }

    }

    public function getRolAttribute()
    {
        foreach ($this->getRoleNames() as $role){
            $rol = $role;
        }

        if($rol == 'respondent'){
            return 'Evaluado';
        } else if($rol == 'institution'){
            return 'Institución';
        } else if($rol == 'administrator'){
            return 'Admin';
        } else if($rol == 'coordinator'){
            return 'Coordinador';
        }
    }

    public function getAvatarUrlAttribute()
    {
        if (!$this->avatar) {
            return asset('images/default.png');
        }

        return asset('storage/' . $this->avatar);
    }
}
