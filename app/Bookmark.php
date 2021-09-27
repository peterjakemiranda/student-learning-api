<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Bookmark extends Model
{
    protected $table = 'bookmarks';
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $guarded = [];

    public function section() : BelongsTo
    {
        return $this->belongsTo(Section::class);
    }

    public function user() : BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
