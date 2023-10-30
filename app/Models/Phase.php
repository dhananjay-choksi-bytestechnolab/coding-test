<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Phase extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
    ];
    
    protected $appends = ['total_task_count']; // Specify the attribute to append

    function tasks()
    {
        return $this->hasMany(Task::class);
    }

    // Define an accessor to get the total count of tasks
    public function getTotalTaskCountAttribute()
    {
        return $this->tasks->count();
    }

    protected static function boot()
    {
        parent::boot();

        static::deleting(function ($phase) {
            // Delete all related tasks when the Phase is deleted
            $phase->tasks()->delete();
        });
    }
}
