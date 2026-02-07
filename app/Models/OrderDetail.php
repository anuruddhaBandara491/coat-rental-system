<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

class OrderDetail extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id',
        'item_id',
        'user_id',
        'rent_or_sale_price',
        'trouser',
        'coat',
        'west',
        'national',
    ];

    public function coats(): HasOne
    {
        return $this->hasOne(Item::class, 'id', 'coat');
    }

    public function trousers(): HasOne
    {
        return $this->hasOne(Item::class, 'id', 'trouser');
    }

    public function wests(): HasOne
    {
        return $this->hasOne(Item::class, 'id', 'west');
    }

    public function nationals(): HasOne
    {
        return $this->hasOne(Item::class, 'id', 'national');
    }

    public function order(): HasOne
    {
        return $this->hasOne(Order::class, 'id', 'order_id');
    }
}
