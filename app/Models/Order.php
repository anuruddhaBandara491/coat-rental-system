<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'invoice_number',
        'start_date',
        'end_date',
        'sub_total',
        'payment_received',
        'remaining_payment',
        'payment_method',
        'remark',
        'status',
        'user_id',
        'customer_id',
    ];

    public function details(): HasMany
    {
        return $this->hasMany(OrderDetail::class, 'order_id', 'id');
    }

    public function customer(): HasOne
    {
        return $this->hasOne(Customer::class, 'id', 'customer_id');
    }

    public function statuses(): HasOne
    {
        return $this->hasOne(OrderStatus::class, 'id', 'status');
    }

    public function user(): HasOne
    {
        return $this->hasOne(User::class, 'id', 'user_id');
    }
}
