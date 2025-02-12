<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    protected $fillable = ['user_id','warung_id', 'status', 'total_harga'];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function warung()
    {
        return $this->belongsTo(Warung::class);
    }
    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }
}
