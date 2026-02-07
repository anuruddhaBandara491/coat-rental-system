<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

class TempOrderDetail extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'rent_or_sale_price',
        'trouser',
        'coat',
        'west',
        'national',
    ];

    public function coat(): HasOne
    {
        return $this->hasOne(Item::class, 'id', 'coat');
    }

    public function trouser(): HasOne
    {
        return $this->hasOne(Item::class, 'id', 'trouser');
    }

    public function west(): HasOne
    {
        return $this->hasOne(Item::class, 'id', 'west');
    }

    public function national(): HasOne
    {
        return $this->hasOne(Item::class, 'id', 'national');
    }
}
