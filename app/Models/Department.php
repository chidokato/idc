<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Department extends Model
{
    protected $fillable = [
        'name',
        'code',
        'description',
        'user_id',
        'parent',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function parentDepartment()
    {
        return $this->belongsTo(Department::class, 'parent');
    }

    public function children()
    {
        return $this->hasMany(Department::class, 'parent');
    }
}
