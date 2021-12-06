<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Announcement extends Model
{
    protected $table = 'announcements';
    protected $appends = [
        'created_date_formatted',
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

    public function getCreatedDateFormattedAttribute()
    {
        return $this->created_at->shortRelativeDiffForHumans();
    }
}
