<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HomeOwner extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'first_name',
        'initial',
        'last_name',
    ];
}
