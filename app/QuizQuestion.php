<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class QuizQuestion extends Model
{
    protected $table = 'quiz_questions';
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $guarded = [];

    protected $appends = ['type_name'];

    public function quiz()
    {
        return $this->belongsTo(Quiz::class);
    }

    public function getTypeNameAttribute()
    {
        $options = [
            'text' => 'Text Input',
            'multiple_choice' => 'Multiple Choice',
            'multiple_answers' => 'Multiple Answers',
            'file_upload' => 'File Upload',
        ];
        return $options[$this->type];
    }

    public function answer()
    {
        return $this->hasOne(QuizAnswer::class, 'question_id');
    }
}
