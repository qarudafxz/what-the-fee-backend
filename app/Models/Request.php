<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Request extends Model
{
    use HasFactory;

    protected $primaryKey = 'request_id';

    protected $guarded = [];

    public function admin()
    {
        return $this->belongsTo(Admin::class, 'admin_id');
    }

    public function payment()
    {
        return $this->hasOne(Payment::class, 'ar_no');
    }
}
