<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

/**
 * @property int $id
 * @property string $userFrom
 * @property string $userToReceive
 * @property Carbon $date
 * @property float $value
 */
class Transfer extends Model
{
    use HasFactory;
    public $timestamps = false;
    protected $table = 'transfer';
    protected $fillable = [
        'userFrom',
        'userToReceive',
        'date',
        'value',
    ];

    protected $casts = [
        'value' => 'float',
    ];

//    public function userFrom(): HasOne
//    {
//        return $this->hasOne(User::class, 'id', 'userFrom');
//    }
//    public function userToReceive(): HasOne
//    {
//        return $this->hasOne(User::class, 'id', 'userToReceive');
//    }
}
