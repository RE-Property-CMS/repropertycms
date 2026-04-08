<?php

namespace App\Http\Controllers;

use App\Jobs\SendContactFormEmail;
use Illuminate\Http\Request;

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
        $validated = $request->validate([
            'name'  => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'required|string|max:50',
        ]);

        $user = [
            'name'    => $validated['name'],
            'email'   => $validated['email'],
            'phone'   => $validated['phone'],
            'messege' => $request->input('messege', ''),
        ];

        $agent = session('agent');

        dispatch(new SendContactFormEmail($user, $agent));

        return response()->json([
            'success' => 1,
            'email'   => $user['email'],
            'message' => 'Sent successfully.',
        ]);
    }
}
