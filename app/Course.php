<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Course extends Model
{
    protected $table = 'courses';
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $guarded = [];

    /**
     * The students that belong to the course.
     */
    public function students()
    {
        return $this->belongsToMany(User::class);
    }

    /**
     * The annoucements that belong to the course.
     */
    public function announcements()
    {
        return $this->hasMany(Announcement::class)->orderBy('created_at', 'DESC');
    }

    /**
     * The annoucements that belong to the course.
     */
    public function activities()
    {
        return $this->hasMany(Activity::class);
    }

    /**
     * The annoucements that belong to the course.
     */
    public function quizzes()
    {
        return $this->hasMany(Quiz::class);
    }

    /**
     * The students that belong to the course.
     */
    public function teachers()
    {
        return $this->belongsTo(User::class);
    }
}
