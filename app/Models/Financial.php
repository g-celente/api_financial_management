<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Financial extends Model
{

    use HasFactory;

    protected $fillable = ['data', 'tipo', 'valor', 'categoria', 'descricao', 'user_id', 'category_id'];
}
