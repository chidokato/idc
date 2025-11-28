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

    // Quan hệ cha
    public function parent()
    {
        return $this->belongsTo(Department::class, 'parent');
    }

    // Quan hệ con
    public function children()
    {
        return $this->hasMany(Department::class, 'parent');
    }

    // Lấy từng cấp
    public function getHierarchyLevelsAttribute()
    {
        $level3 = $this; // chính nó
        $level2 = $level3->parentDepartment;
        $level1 = $level2?->parentDepartment;

        return [
            'level1' => $level1?->name,
            'level2' => $level2?->name,
            'level3' => $level3?->name,
        ];
    }

}
