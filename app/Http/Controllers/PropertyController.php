<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;

class PropertyController extends Controller
{
    public function details($unique_url)
    {
        return view('property', compact('unique_url'));
    }

    public function shareProperty($unique_url)
    {
        request()->merge(['share' => true]);
        return view('property', compact('unique_url'));
    }

    public function Contact_Form(Request $request)
    {
        $data = [];

        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'email' => 'required',
            'phone' => 'required',
        ]);
        if ($validator->fails()) {
            $data['success'] = 0;
            $data['messege'] = $validator->errors(); // Error response
        } else {

            // Collect details from session for sending in email
            $user['name'] = $request['name'];
            $user['email'] = $request['email'];
            $user['phone'] = $request['phone'];
            $user['messege'] = $request['messege'];

            $agent = session('agent');
            $agent['userEmail'] = $user['email'];

            $data = ['user' => $user, 'agent' => $agent];

            // Sending mail to user
            Mail::send('mail.contact-user', $data, function ($message) use ($user) {
                $message->to($user['email']);
                $message->subject('Your contact request received.');
            });

            // Sending mail to agent
            Mail::send('mail.contact-agent', $data, function ($message) use ($agent) {
                $message->to($agent->email);
                $message->replyTo($agent['userEmail']);
                $message->subject('New contact request received on '.config('app.name'));
            });

            $data = [];
            $data['success'] = 1;
            $data['email'] = $request['email'];
            $data['message'] = 'Sent successfully.';
        }

        return response()->json($data);
    }
}
