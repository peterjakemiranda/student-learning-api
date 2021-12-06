<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class QuizScore extends Model
{
    protected $table = 'quiz_scores';
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $guarded = [];

    public function quiz()
    {
        return $this->belongsTo(Quiz::class);
    }
}
