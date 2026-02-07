<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

class Customer extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'branch_id',
        'first_name',
        'last_name',
        'email',
        'nic',
        'title',
        'phone',
        'phone1',
        'address',
        'province',
        'district',
    ];

    public function branches(): HasOne
    {
        return $this->hasOne(Branch::class, 'id', 'branch_id');
    }
}
