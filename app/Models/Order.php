<?php

namespace App\Models;

use App\User;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    //

    public function user()
    {
        return $this->belongsTo(User::class, 'client_id');
    }

    public function products()
    {
        return $this->belongsToMany(Product::class);
    }

    public function transactions()
    {
        return $this->hasMany(Transaction::class);
    }
}
