<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

class Quiz extends Model
{
    protected $table = 'quizzes';
    protected $appends = [
        'started_date_formatted',
        'stopped_date_formatted'
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
        return $this->hasMany(QuizAnswer::class);
    }

    public function score()
    {
        return $this->hasOne(QuizScore::class, 'quiz_id');
    }

    public function getStartedDateFormattedAttribute()
    {
        return $this->started_date ? Carbon::parse($this->started_date)->shortRelativeDiffForHumans() : null;
    }

    public function getStoppedDateFormattedAttribute()
    {
        return $this->stopped_date ? Carbon::parse($this->stopped_date)->shortRelativeDiffForHumans() : null;
    }
    
}
