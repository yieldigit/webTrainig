<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    use HasFactory;
    protected $fillable = ['name', 'level'];
    /*Relation with user */
    public function users()
    {
        return $this->hasMany(User::class);
    }
}
