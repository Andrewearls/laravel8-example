<?php

namespace App\Http\Controllers;

use App\Http\Requests\UserMessageRequest;
use App\Http\Resources\MessageResource;
use App\Jobs\MessageGPT as OutgoingMessage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MessagingController extends Controller
{

    /**
     * Return messaging view.
     *
     * @param Request
     * @return view
     */
    public function index(Request $request)
    {
        // should be able to grab this from the request. User has been authed.
        $user    = Auth::user();
        $channel = $user->id;
        return view('message.inbox', ['channel' => $channel]);
    }

    /**
     * Receive messege from user.
     * Sanatize message.
     * Trigger MessageGPT event.
     * Return success.
     *
     * @param Request
     * @return success
     */
    public function messageGPT(UserMessageRequest $request)
    {
        $validated = $request->safe()->collect();
        $message   = $validated['message'];
        // Sanatize

        // User
        $user = Auth::user();

        // Trigger Event
        OutgoingMessage::dispatch($message, $user);

        return response(200);
    }

    /**
     * Get all messages.
     *
     * @return json messages
     */
    public function getAll(Request $request)
    {

        $response = MessageResource::collection($request->user()->messages());
        // dd($response);

        return $response;
    }
}
