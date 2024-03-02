<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Addresses extends Model
{
    use HasFactory;
    protected $table = 'employee_addresses';
    protected $fillable = ['address','address_type'];

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

}
