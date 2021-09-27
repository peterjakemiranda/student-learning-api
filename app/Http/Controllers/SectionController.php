<?php

namespace App\Http\Controllers;

use App\Section;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SectionController extends Controller
{
    public function index(Request $request)
    {
        $chapterId = $request->input('chapter_id');
        if ($chapterId) {
            $sections = Section::where('chapter_id', $chapterId)->get();
        } elseif ($search = $request->input('search')) {
            $fuzzySearch = implode('%', str_split($search)); // e.g. test -> t%e%s%t
            $fuzzySearch = "%$fuzzySearch%";
            $sections = Section::select(DB::raw('id, title, chapter_id, substr(content, 1, 128) as content'))->where('title', 'like', $fuzzySearch)->where('content', 'like', $fuzzySearch)->get();
        } else {
            $sections = Section::all();
        }

        return response()->json($sections);
    }

    public function store(Request $request)
    {
        $rules = [
            'title' => 'nullable|string',
            'content' => 'required|string',
            'chapter_id' => 'required|integer|exists:chapters,id',
        ];
        $this->validate($request, $rules);

        $section = new Section();
        $section->title = $request->title;
        $section->content = $request->content;
        $section->chapter_id = $request->input('chapter_id');
        $section->save();

        return response()->json($section);
    }

    public function show($id)
    {
        $section = Section::findOrFail($id);

        return response()->json($section);
    }

    public function update(Request $request, $id)
    {
        $rules = [
            'title' => 'nullable|string',
            'content' => 'required|string',
            'chapter_id' => 'required|integer|chapters,id',
        ];
        $this->validate($request, $rules);

        $section = Section::findOrFail($id);

        $section->title = $request->input('title');
        $section->content = $request->input('content');
        $section->chapter_id = $request->input('chapter_id');
        $section->save();

        return response()->json($section);
    }

    public function destroy($id)
    {
        $section = Section::findOrFail($id);
        $section->delete();

        return response()->json('section removed successfully');
    }
}
