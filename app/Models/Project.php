<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Project extends Model
{
    protected $fillable = [
        'title',
        'description',
        'proposal_file_path',
        'client_id',
        'status',
        'ui_link',
        'ui_status',
    ];

    /**
     * Backward-compatible accessor for name attribute.
     */
    public function getNameAttribute()
    {
        return $this->title;
    }

    public function setNameAttribute($value)
    {
        $this->attributes['title'] = $value;
    }

    public function client()
    {
        return $this->belongsTo(User::class, 'client_id');
    }

    /**
     * All assigned team members (PM, Developers, Designers, QA).
     */
    public function users()
    {
        return $this->belongsToMany(User::class, 'project_user');
    }

    public function team()
    {
        return $this->users();
    }

    public function developers()
    {
        return $this->belongsToMany(User::class, 'project_user')->where('role', 'developer');
    }

    public function designers()
    {
        return $this->belongsToMany(User::class, 'project_user')->where('role', 'designer');
    }

    public function qas()
    {
        return $this->belongsToMany(User::class, 'project_user')->where('role', 'qa');
    }

    public function pms()
    {
        return $this->belongsToMany(User::class, 'project_user')->where('role', 'pm');
    }

    public function tasks()
    {
        return $this->hasMany(Task::class);
    }

    public function comments()
    {
        return $this->hasMany(Comment::class);
    }
}
