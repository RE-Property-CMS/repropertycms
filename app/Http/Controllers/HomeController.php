<?php

namespace App\Http\Controllers;

use App\Models\Page;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Application;

class HomeController extends Controller
{
    public function index()
    {
        $page = Page::where('key', 'home')->where('action', true)->first();

        if ($page && $page->html) {
            return response($this->buildFullHtml($page))
                ->header('Content-Type', 'text/html; charset=utf-8');
        }

        return view('home');
    }

    /**
     * @return Factory|View|Application
     */
    public function termsAndConditions()
    {
        return view('site.terms-and-conditons');
    }

    // ─── Helpers ─────────────────────────────────────────────────────────────

    private function buildFullHtml(Page $page): string
    {
        $title       = e($page->meta_title ?: $page->title);
        $description = e($page->meta_description ?? '');
        $keywords    = e($page->meta_keywords ?? '');
        $css         = $page->css ?? '';
        $html        = $page->html;

        return <<<HTML
        <!DOCTYPE html>
        <html lang="en">
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>{$title}</title>
            {$this->metaTags($description, $keywords)}
            <script src="https://cdn.tailwindcss.com"></script>
            <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css"/>
            <style>{$css}</style>
        </head>
        <body>
            {$html}
        </body>
        </html>
        HTML;
    }

    private function metaTags(string $description, string $keywords): string
    {
        $out = '';
        if ($description) {
            $out .= "<meta name=\"description\" content=\"{$description}\">\n";
        }
        if ($keywords) {
            $out .= "<meta name=\"keywords\" content=\"{$keywords}\">\n";
        }
        return $out;
    }
}
