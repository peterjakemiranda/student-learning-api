<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

class ActivityAnswer extends Model
{
    protected $table = 'activity_answers';
    protected $appends = [
        'submitted_date_formatted'
    ];
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $guarded = [];

    public function activity()
    {
        return $this->belongsTo(Activity::class);
    }

    public function getSubmittedDateFormattedAttribute()
    {
        return $this->updated_at ? Carbon::parse($this->updated_at)->shortRelativeDiffForHumans() : null;
    }
}
