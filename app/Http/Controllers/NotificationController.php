<?php

namespace App\Http\Controllers;

use App\Notification;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    public function index(Request $request)
    {
        $query = Notification::latest()->where('user_id', auth()->id());

        if (!$request->input('limit')) {
            return response()->json($query->get());
        }

        $this->setPagination($request->input('limit'));
        $pagination = $query->paginate($this->getPagination());
        $data = [
            'data' => $pagination->items(),
            'pagination' => [
                'last_page' => $pagination->lastPage(),
                'current_page' => $pagination->currentPage(),
                'limit' => $pagination->perPage(),
                'total_count' => $pagination->total(),
            ],
        ];

        return response()->json($data);
    }

    public function count(Request $request)
    {
        $count = Notification::where('user_id', auth()->id())
            ->where('read',0)->count();

        return response()->json(['count' => $count]);
    }

    public function read(Request $request, $id)
    {
        Notification::where('id', $id)->update(['read' => 1]);

        return response()->json('Success');
    }
}
