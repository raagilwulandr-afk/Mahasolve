<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $guarded = ['id'];

    public function customer() {
        return $this->belongsTo(User::class, 'customer_id');
    }

    public function provider() {
        return $this->belongsTo(User::class, 'provider_id');
    }

    public function service() {
        return $this->belongsTo(Service::class, 'service_id');
    }

    public function chats() {
        return $this->hasMany(OrderChat::class)->oldest();
    }
}
