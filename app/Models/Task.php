<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;
use App\Models\{
    Phase
};

class Task extends Model
{

    protected $fillable = [
        'name',
        'phase_id',
        'user_id',
        'due_date',
        'completed_at'
    ];

    protected $appends = ['status', 'completed_this_week', 'completed_this_month'];

    use HasFactory;

    public function getStatusAttribute()
    {
        // Check if completed_at is not null
        if (!is_null($this->completed_at)) {
            return 'completed';
        }

        // Check if the current date is greater than due_date and completed_at is null
        if (Carbon::now() > $this->due_date && is_null($this->completed_at)) {
            return 'due';
        }

        // Default status if none of the conditions are met
        return 'in_progress';
    }

    public function getCompletedThisWeekAttribute()
    {
        return $this->completed_at && Carbon::parse($this->completed_at)->isCurrentWeek();
    }

    public function getCompletedThisMonthAttribute()
    {
        return $this->completed_at && Carbon::parse($this->completed_at)->isCurrentMonth();
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($task) {
            // Get the new value of 'phase_id'
            $newPhaseId = $task->phase_id;

            $newPhase = Phase::find($newPhaseId);
            if (
                !empty($newPhase->name) &&
                ($newPhase->name == "completion")
            ) {
                $task->completed_at = Carbon::now();
            }
        });
        
        static::updating(function ($task) {
            // Get the previous (original) value of 'phase_id'
            $previousPhaseId = $task->getOriginal('phase_id');

            // Get the previous (original) value of 'completed_at'
            $previousCompletedAt = $task->getOriginal('completed_at');

            // Get the new value of 'phase_id'
            $newPhaseId = $task->phase_id;

            $newPhase = Phase::find($newPhaseId);
            if (
                !empty($newPhase->name) && // verify New Phase Name not empty
                ($newPhase->name == "completion") && // Verify New Phase is completion
                ($previousPhaseId != $newPhaseId) && // Verify Phase is updating
                empty($previousCompletedAt) // Verify completed_at only updated once
            ) {
                $task->completed_at = Carbon::now();
            }
        });
    }

    function user()
    {
        return $this->belongsTo(User::class);
    }

    function phase()
    {
        return $this->belongsTo(Phase::class);
    }
}
