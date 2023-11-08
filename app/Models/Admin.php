<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Admin extends Model
{
    use HasFactory;
    protected $primaryKey = 'admin_id';
    protected $guarded = [];

    public $incrementing = false;

    public $hidden = ['password', 'email', 'created_at', 'updated_at'];
}
