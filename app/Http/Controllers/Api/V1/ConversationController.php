<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Facades\Chat;
use App\Models\File\File;

class ConversationController extends Controller
{
    /**
     * Create a new controller instance.
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $threads = Chat::getAllConversations();

        return response()->json([
            'threads' => $threads
        ]);
    }

    public function chat($id)
    {
        $conversation = Chat::getConversationMessageById($id);

        return response()->json([
            'conversation' => $conversation
        ]);
    }

    public function send(Request $request)
    {
        Chat::sendConversationMessage($request->input('conversationId'), $request->input('text'));
    }

    public function sendFilesInConversation(Request $request)
    {
        Chat::sendFilesInConversation($request->input('conversationId') , $request->file('files'));
    }
}