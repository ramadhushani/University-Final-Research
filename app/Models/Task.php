<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
    protected $fillable = [
        'project_id',
        'title',
        'description',
        'assigned_to',
        'status',
    ];

    /**
     * Backward-compatible accessor for client_update attribute.
     */
    public function getClientUpdateAttribute()
    {
        return $this->description;
    }

    public function setClientUpdateAttribute($value)
    {
        $this->attributes['description'] = $value;
    }

    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    public function assignedUser()
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    /**
     * Compatibility alias.
     */
    public function developer()
    {
        return $this->assignedUser();
    }

    public function user()
    {
        return $this->assignedUser();
    }

    public function comments()
    {
        return $this->hasMany(Comment::class);
    }
}
