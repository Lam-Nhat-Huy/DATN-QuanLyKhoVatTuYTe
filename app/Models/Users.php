<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Users extends Model
{
    protected $primaryKey = 'code';

    public $incrementing = false;

    protected $keyType = 'string';

    use HasFactory, SoftDeletes;

    protected $fillable = [
        'code',
        'first_name',
        'last_name',
        'avatar',
        'email',
        'phone',
        'password',
        'birth_day',
        'gender',
        'address',
        'position',
        'isAdmin',
        'status',
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    public function receipts()
    {
        return $this->hasMany(Receipts::class, 'created_by', 'code');
    }

    public function exports()
    {
        return $this->hasMany(Exports::class, 'created_by', 'code');
    }
}