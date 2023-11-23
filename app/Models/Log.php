<?php

namespace App\Models;

use App\Http\Controllers\Api\AdminController;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Log extends Model
{
    use HasFactory;

    protected $primaryKey = 'logs_id';
    protected $guarded = [];

    public function admin()
    {
        return $this->hasOne(AdminController::class, 'admin_id', 'admin_id');
    }
}
