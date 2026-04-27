<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\Page;
use Illuminate\Http\Request;

class PagesController extends Controller
{
    // ─── List ────────────────────────────────────────────────────────────────

    public function index()
    {
        // System pages always shown at top
        $systemPages = [];
        foreach (Page::SYSTEM_KEYS as $key => $label) {
            $systemPages[$key] = [
                'label' => $label,
                'page'  => Page::where('key', $key)->first(),
            ];
        }

        $customPages = Page::whereNull('key')->latest()->paginate(15);

        return view('admin.pages.index', compact('systemPages', 'customPages'));
    }

    // ─── Create ───────────────────────────────────────────────────────────────

    public function create(Request $request)
    {
        // If a system page preset is requested (first-time edit), auto-create the
        // DB record and redirect to the edit page so it behaves like a normal edit.
        $preset = $request->query('preset');
        if ($preset && array_key_exists($preset, Page::SYSTEM_KEYS)) {
            $existing = Page::where('key', $preset)->first();
            if ($existing) {
                return redirect()->route('admin.pages.edit', $existing->id);
            }

            $page = Page::create([
                'title'   => Page::SYSTEM_KEYS[$preset],
                'key'     => $preset,
                'action'  => true,
                'slug'    => $preset,
                'content' => '',
            ]);

            return redirect()->route('admin.pages.edit', $page->id);
        }

        return view('admin.pages.editor', [
            'page'        => null,
            'initialHtml' => null,
            'formAction'  => route('admin.pages.store'),
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'title'     => 'required|string|max:255',
            'gjs_html'  => 'nullable|string',
            'gjs_css'   => 'nullable|string',
            'gjs_data'  => 'nullable|string',
        ]);

        Page::create([
            'title'            => $request->title,
            'content'          => '',
            'html'             => $request->gjs_html,
            'css'              => $request->gjs_css,
            'gjs_data'         => $request->gjs_data,
            'action'           => true,
            'meta_title'       => $request->meta_title,
            'meta_description' => $request->meta_description,
            'meta_keywords'    => $request->meta_keywords,
        ]);

        return redirect()->route('admin.pages.lists')
            ->with('success', 'Page created successfully.');
    }

    // ─── Edit ─────────────────────────────────────────────────────────────────

    public function edit(int $id)
    {
        $page = Page::findOrFail($id);

        // System pages use a simple field-based content editor (not GrapesJS).
        if ($page->isSystemPage()) {
            $content = ($page->gjs_data) ? (json_decode($page->gjs_data, true) ?: []) : [];
            return view('admin.pages.content-editor', compact('page', 'content'));
        }

        return view('admin.pages.editor', [
            'page'        => $page,
            'initialHtml' => null,
            'formAction'  => route('admin.pages.update', $page->id),
        ]);
    }

    public function update(Request $request, int $id)
    {
        $page = Page::findOrFail($id);

        // System pages: store all submitted content fields as JSON.
        if ($page->isSystemPage()) {
            $content = $request->except(['_token', '_method', 'action']);
            $page->update([
                'action'   => $request->boolean('action', true),
                'gjs_data' => json_encode(array_filter($content, fn($v) => $v !== null)),
            ]);
            return redirect()->route('admin.pages.lists')
                ->with('success', 'Content saved successfully.');
        }

        // Custom pages: GrapesJS save.
        $request->validate([
            'title'    => 'required|string|max:255',
            'gjs_html' => 'nullable|string',
            'gjs_css'  => 'nullable|string',
            'gjs_data' => 'nullable|string',
        ]);

        $page->update([
            'title'            => $request->title,
            'content'          => $page->content ?? '',
            'html'             => $request->gjs_html,
            'css'              => $request->gjs_css,
            'gjs_data'         => $request->gjs_data,
            'action'           => $request->boolean('action', true),
            'meta_title'       => $request->meta_title,
            'meta_description' => $request->meta_description,
            'meta_keywords'    => $request->meta_keywords,
        ]);

        return redirect()->route('admin.pages.lists')
            ->with('success', 'Page saved successfully.');
    }

    // ─── Delete ───────────────────────────────────────────────────────────────

    public function destroy(int $id)
    {
        $page = Page::findOrFail($id);

        if ($page->isSystemPage()) {
            return redirect()->route('admin.pages.lists')
                ->with('error', 'System pages cannot be deleted. You can disable them instead.');
        }

        $page->delete();

        return redirect()->route('admin.pages.lists')
            ->with('success', 'Page deleted.');
    }

}
