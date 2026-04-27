<?php

namespace App\Http\Controllers;

use App\Jobs\SendContactFormEmail;
use App\Models\Page;
use Illuminate\Http\Request;

class PropertyController extends Controller
{
    public function details($unique_url)
    {
        // Check for a published custom page with this slug before falling through
        // to the property lookup. System pages (home, demo-landing) have their
        // own dedicated routes and are excluded here.
        $page = Page::where('slug', $unique_url)
            ->where('action', true)
            ->whereNull('key')
            ->first();

        if ($page && $page->html) {
            $title       = e($page->meta_title ?: $page->title);
            $description = e($page->meta_description ?? '');
            $keywords    = e($page->meta_keywords ?? '');
            $css         = $page->css ?? '';
            $meta        = '';
            if ($description) $meta .= "<meta name=\"description\" content=\"{$description}\">\n";
            if ($keywords)    $meta .= "<meta name=\"keywords\" content=\"{$keywords}\">\n";

            $html = <<<HTML
            <!DOCTYPE html>
            <html lang="en">
            <head>
                <meta charset="UTF-8">
                <meta name="viewport" content="width=device-width, initial-scale=1.0">
                <title>{$title}</title>
                {$meta}
                <script src="https://cdn.tailwindcss.com"></script>
                <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css"/>
                <style>{$css}</style>
            </head>
            <body>
                {$page->html}
            </body>
            </html>
            HTML;

            return response($html)->header('Content-Type', 'text/html; charset=utf-8');
        }

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
