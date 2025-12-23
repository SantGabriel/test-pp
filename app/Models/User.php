<?php

namespace App\Models;

use App\Enum\UserType;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property string $name
 * @property string $email
 * @property string $cpf_cnpj
 * @property string|null $password
 * @property float $balance
 * @property UserType $type
 */
class User extends Model
{
    public $timestamps = false;

    protected $table = 'account';
    protected $fillable = [
        'name',
        'email',
        'cpf_cnpj',
        'password',
        'balance',
        'type',
    ];

    protected $casts = [
        'balance' => 'float',
    ];
}
