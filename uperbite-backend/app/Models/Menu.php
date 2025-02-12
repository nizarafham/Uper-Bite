<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Menu extends Model
{
    use HasFactory;

    protected $fillable = ['warung_id', 'nama', 'harga', 'kategori'];

    public function warung()
    {
        return $this->belongsTo(Warung::class);
    }
}
