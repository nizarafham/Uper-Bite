<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Warung extends Model
{
    use HasFactory;

    protected $fillable = ['nama', 'lokasi', 'penjual_id'];

    public function penjual()
    {
        return $this->belongsTo(User::class, 'penjual_id');
    }

    public function menus()
    {
        return $this->hasMany(Menu::class);
    }
}

