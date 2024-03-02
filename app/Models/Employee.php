<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Employee extends Model
{
    use HasFactory;

    protected $fillable = ['first_name','last_name', 'email','salary','department_id', 'hire_date','employee_address_id'];


    public function addresses()
    {
        return $this->hasMany(Address::class);
    }

}
