<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Comment extends Model
{
    protected $fillable = [
        'project_id',
        'user_id',
        'message',
        'task_id',
    ];

    /**
     * Backward-compatible accessor for comment attribute.
     */
    public function getCommentAttribute()
    {
        return $this->message;
    }

    public function setCommentAttribute($value)
    {
        $this->attributes['message'] = $value;
    }

    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function task()
    {
        return $this->belongsTo(Task::class);
    }
}
