<?php

namespace App\Http\Controllers;

use App\Bookmark;
use Illuminate\Http\Request;

class BookmarkController extends Controller
{
    public function index(Request $request)
    {
        $bookmarks = auth()->user()->bookmarks()
            ->join('sections', 'bookmarks.section_id', '=', 'sections.id')
           ->select('bookmarks.id', 'sections.title', 'sections.id as section_id', 'sections.chapter_id')
           ->orderBy('bookmarks.created_at')
           ->get();

        return response()->json($bookmarks);
    }

    public function bookmark(Request $request, $id)
    {
        $rules = [
            'bookmark' => 'boolean',
        ];
        $this->validate($request, $rules);

        if ($request->input('bookmark')) {
            auth()->user()->bookmarks()->updateOrCreate(['section_id' => $id]);
        } else {
            auth()->user()->bookmarks()->where('section_id', $id)->delete();
        }

        return response()->json('Bookmark updated successfully');
    }

    public function destroy($id)
    {
        $chapter = Bookmark::findOrFail($id);
        $chapter->delete();

        return response()->json('bookmark removed successfully');
    }
}
