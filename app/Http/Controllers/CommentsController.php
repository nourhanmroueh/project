<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Comments;
use \Validator;
use App\Http\Resources\ItemsResource;
use Symfony\Component\HttpKernel\Exception\HttpException;
use App\Http\Controllers\ItemsController;
use App\Models\Items;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class CommentsController extends Controller {

    public function addComments(Request $request) {
        if (Auth::check()) {

            $validatedData = Validator::make($request->all(), [
                        'user_id' => 'required',
                        'item_id' => 'required',
            ]);
            if ($validatedData->fails()) {
                return $this->sendError('Validation Error', $validatedData->errors());
            }
            if ($request->image) {
                $request->validate([
                    'image' => 'image|mimes:jpg,png,jpeg,gif,svg|max:2048',
                ]);
                $cmt['image'] = $request->image;
            }
            if ($request->audio) {
                $request->validate([
                    'audio' => 'mimes:audio/mpeg,mpga,mp3,wav|max:2048',
                ]);
                $cmt['audio'] = $request->audio;
            }
            if ($request->video) {
                $request->validate([
                    'video' => 'mimes:mpeg,ogg,mp4,webm,3gp,gif,mov|max:2048',
                ]);
                $cmt['video'] = $request->video;
            }
            $data = [];
            foreach ($cmt as $k => $v) {
                $comment = new Comments();
                $comment->user_id = $request->user_id;
                $comment->item_id = $request->item_id;
                $comment->comment = $request->file($k)->store('public/' . $k . '/comments');
                $comment->updated_at = null;
                if ($comment->save()) {
                    $item = Items::find($request->item_id);
                    $item->last_commented_date = date('Y-m-d H:i:s');
                    $item->save();
                    $data[] = $comment;
                }
            }
            return $this->sendResponse($data, 'Comments added successfully.');
        } else {
            return $this->sendError('Authentication Error');
        }
    }

    public function fetchComments(Request $request) {
        $comments = DB::table('comments')
                ->get();
        return $this->sendResponse($comments, 'Comments added successfully.');
    }

}
