<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Section extends Model
{
    protected $table = 'sections';
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'title', 'content',
    ];

    protected $appends = [
        'bookmarked',
    ];

    public function chapter() : BelongsTo
    {
        return $this->belongsTo(Chapter::class, 'chapter_id');
    }

    public function bookmarks() : HasMany
    {
        return $this->hasMany(Bookmark::class, 'section_id');
    }

    public function getBookmarkedAttribute()
    {
        if(!auth()->user()) {
            return false;
        }
        return auth()->user()->bookmarks()->where('section_id', $this->id)->exists();
    }

    public static function search(string $search) {
        $columns = ["id", "title", "content"];

        // If the search is empty, return everything
        if (empty(trim($search))) {
            return static::select($columns)->get();
        }
        // If the search contains something, we perform the fuzzy search 
        else {
            $fuzzySearch = implode("%", str_split($search)); // e.g. test -> t%e%s%t
            $fuzzySearch = "%$fuzzySearch%"; // test -> %t%e%s%t%s%
            $fuzzySearch = implode("%", str_split(str_replace(" ", "", $search)));

            return static::select($columns)->where("title", "like", $fuzzySearch)->where("content", "like", $fuzzySearch)->get();
        }
    }
}
