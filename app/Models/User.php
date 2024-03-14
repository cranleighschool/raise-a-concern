<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'sso_type',
        'sso_id',
        'username',
        'updated_at',
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
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }


    /**
     * @param string $email
     * @param string $ssoType
     * @param string $name
     * @param string $username
     * @param int $ssoId
     * @return $this
     */
    public static function create(string $email, string $ssoType, string $name, string $username, int $ssoId): Model
    {
        return self::query()->firstOrCreate(
            [
                'email' => $email,
            ],
            [
                'sso_type' => $ssoType,
                'sso_id' => $ssoId,
                'name' => $name,
                'username' => $username,
            ]
        );
    }
}
