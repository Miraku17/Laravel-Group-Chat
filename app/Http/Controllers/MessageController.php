<?php

namespace App\Http\Controllers;

use App\Models\Message;
use Illuminate\Http\Request;
use App\Events\MessageSent;
use App\Events\ChatReset;

class MessageController extends Controller
{
    public function sendMessage(Request $request)
    {
        $message = Message::create([
            'username' => $request->username,
            'message' => $request->message,
        ]);

        broadcast(new MessageSent($message))->toOthers();

        return response()->json(['status' => 'Message Sent!']);
    }

    public function getMessages()
    {
        return Message::orderBy('created_at', 'desc')->take(10)->get();
    }

    // public function resetChat()
    // {
    //     Message::truncate();
    //     broadcast(new ChatReset());
    //     return response()->json(['status' => 'Chat reset successfully']);
    // }
}