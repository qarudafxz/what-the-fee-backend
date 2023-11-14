<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Expense extends Model
{
    use HasFactory;

    protected $primaryKey = 'expense_id';
    protected $guarded = [];

    public function colleges()
    {
        return $this->belongsTo(College::class, 'college_id', 'college_id');
    }
}
