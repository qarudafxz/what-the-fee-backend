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

    public function permissions()
    {
        return $this->hasOne(Permission::class, 'admin_id', 'admin_id');
    }

    public function requests()
    {
        return $this->hasMany(Request::class, 'admin_id', 'admin_id');
    }
}
