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
        'active',
        'channel_id',
        'department_id',
        'report_id',
        'cost_type',
        'content',
    ];

    public function User()
    {
        return $this->belongsTo(User::class, 'user_id');
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
}
