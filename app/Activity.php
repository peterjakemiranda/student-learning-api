<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

class Activity extends Model
{
    protected $table = 'activities';
    protected $appends = [
        'due_date_formatted'
    ];
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $guarded = [];

    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    public function answers()
    {
        return $this->hasMany(ActivityAnswer::class);
    }

    public function getDueDateFormattedAttribute()
    {
        return $this->due_date ? Carbon::parse($this->due_date)->shortRelativeDiffForHumans() : null;
    }
}
