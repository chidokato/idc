<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'user',
        'post_id',
        'expected_costs',
        'actual_costs',
        'rate',
        'days',
        'active',
        'channel_id',
        'department_id',
        'department_lv1',
        'department_lv2',
        'report_id',
        'cost_type',
        'content',
    ];

    public function User()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function handler()
    {
        return $this->belongsTo(User::class, 'user'); // người sử dụng tác vụ
    }

    public function Post()
    {
        return $this->belongsTo(Post::class, 'post_id');
    }

    public function Channel()
    {
        return $this->belongsTo(Channel::class, 'channel_id');
    }

    public function Report()
    {
        return $this->belongsTo(Report::class, 'report_id');
    }
    public function department()
    {
        return $this->belongsTo(Department::class, 'department_id');
    }

}
