<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('editor-title', 'Page Editor') — {{ config('app.name') }}</title>

    {{-- GrapesJS --}}
    <link rel="stylesheet" href="https://unpkg.com/grapesjs@0.21.13/dist/css/grapes.min.css">

    {{-- FontAwesome (toolbar icons) --}}
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css"/>

    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
        html, body { height: 100%; overflow: hidden; font-family: system-ui, -apple-system, sans-serif; }

        /* ── Top toolbar ── */
        #pb-toolbar {
            display: flex;
            align-items: center;
            gap: 12px;
            height: 52px;
            padding: 0 16px;
            background: #18181b;
            border-bottom: 1px solid #27272a;
            z-index: 200;
            position: relative;
        }
        #pb-toolbar .pb-back {
            display: flex; align-items: center; gap: 6px;
            color: #a1a1aa; font-size: 13px; text-decoration: none;
            padding: 6px 10px; border-radius: 6px; transition: background .15s;
        }
        #pb-toolbar .pb-back:hover { background: #27272a; color: #fff; }
        #pb-toolbar .pb-title-wrap {
            flex: 1; display: flex; align-items: center; gap: 10px;
        }
        #pb-title {
            background: #27272a; border: 1px solid #3f3f46; border-radius: 7px;
            color: #f4f4f5; font-size: 14px; padding: 6px 12px;
            outline: none; width: 260px;
            transition: border-color .15s;
        }
        #pb-title:focus { border-color: #3b82f6; }
        #pb-key-badge {
            font-size: 11px; font-weight: 600; letter-spacing: .04em;
            background: #1d4ed8; color: #bfdbfe;
            padding: 3px 9px; border-radius: 99px;
            white-space: nowrap;
        }
        #pb-toolbar .pb-divider { width: 1px; height: 28px; background: #3f3f46; }

        /* Meta drawer toggle */
        #pb-meta-toggle {
            display: flex; align-items: center; gap: 5px;
            background: transparent; border: 1px solid #3f3f46; border-radius: 7px;
            color: #a1a1aa; font-size: 13px; padding: 5px 11px; cursor: pointer;
            transition: background .15s, color .15s;
        }
        #pb-meta-toggle:hover, #pb-meta-toggle.open { background: #27272a; color: #f4f4f5; }

        /* Save button */
        #pb-save {
            display: flex; align-items: center; gap: 6px;
            background: #2563eb; color: #fff; border: none;
            border-radius: 7px; padding: 7px 16px; font-size: 13px;
            font-weight: 600; cursor: pointer; transition: background .15s;
            white-space: nowrap;
        }
        #pb-save:hover { background: #1d4ed8; }
        #pb-save:disabled { background: #374151; color: #9ca3af; cursor: default; }

        /* ── Meta drawer ── */
        #pb-meta-drawer {
            display: none;
            background: #18181b; border-bottom: 1px solid #27272a;
            padding: 14px 20px;
            gap: 16px;
            flex-wrap: wrap;
            position: relative; z-index: 190;
        }
        #pb-meta-drawer.open { display: flex; }
        .pb-meta-field { display: flex; flex-direction: column; gap: 4px; min-width: 200px; flex: 1; }
        .pb-meta-field label { font-size: 11px; font-weight: 600; letter-spacing: .05em; text-transform: uppercase; color: #71717a; }
        .pb-meta-field input, .pb-meta-field textarea, .pb-meta-field select {
            background: #27272a; border: 1px solid #3f3f46; border-radius: 6px;
            color: #f4f4f5; font-size: 13px; padding: 6px 10px; outline: none;
            transition: border-color .15s;
        }
        .pb-meta-field input:focus, .pb-meta-field textarea:focus { border-color: #3b82f6; }
        .pb-meta-field textarea { resize: vertical; min-height: 52px; }
        .pb-status-check { display: flex; align-items: center; gap: 8px; cursor: pointer; }
        .pb-status-check input { width: 15px; height: 15px; accent-color: #22c55e; }
        .pb-status-check span { font-size: 13px; color: #d4d4d8; }

        /* ── GrapesJS canvas area ── */
        #gjs {
            position: absolute;
            top: 52px; /* toolbar height */
            left: 0; right: 0; bottom: 0;
        }
        /* When meta drawer is open, pushed down via JS */
        #gjs.drawer-open {
            top: 52px; /* JS will set actual value based on drawer height */
        }

        /* Override GrapesJS defaults to look clean */
        .gjs-cv-canvas { background: #f0f0f0; }
    </style>
</head>
<body>

{{-- ── Toolbar ───────────────────────────────────────────────────────────── --}}
<div id="pb-toolbar">
    <a href="{{ route('admin.pages.lists') }}" class="pb-back">
        <i class="fa fa-chevron-left fa-xs"></i> Pages
    </a>
    <div class="pb-divider"></div>
    <div class="pb-title-wrap">
        <input type="text" id="pb-title" placeholder="Page title…"
               value="{{ $page->title ?? '' }}" autocomplete="off">
        @if(isset($page) && $page->key)
            <span id="pb-key-badge">
                <i class="fa fa-lock fa-xs mr-1"></i>{{ $page->key }}
            </span>
        @endif
    </div>
    <button id="pb-meta-toggle" type="button" onclick="toggleMeta()">
        <i class="fa fa-gear fa-sm"></i> SEO &amp; Settings
    </button>
    <button id="pb-save" type="button" onclick="savePage()">
        <i class="fa fa-floppy-disk fa-sm"></i> Save Page
    </button>
</div>

{{-- ── Meta drawer ─────────────────────────────────────────────────────────── --}}
<div id="pb-meta-drawer">
    <div class="pb-meta-field">
        <label>Meta Title</label>
        <input type="text" id="pb-meta-title" placeholder="SEO title (optional)"
               value="{{ $page->meta_title ?? '' }}" autocomplete="off">
    </div>
    <div class="pb-meta-field" style="flex:2;">
        <label>Meta Description</label>
        <textarea id="pb-meta-desc" placeholder="SEO description (optional)">{{ $page->meta_description ?? '' }}</textarea>
    </div>
    <div class="pb-meta-field">
        <label>Meta Keywords</label>
        <input type="text" id="pb-meta-keywords" placeholder="keyword1, keyword2"
               value="{{ $page->meta_keywords ?? '' }}" autocomplete="off">
    </div>
    <div class="pb-meta-field" style="justify-content:flex-end;min-width:auto;">
        <label>Status</label>
        <label class="pb-status-check">
            <input type="checkbox" id="pb-action" {{ ($page->action ?? true) ? 'checked' : '' }}>
            <span>Published</span>
        </label>
    </div>
</div>

{{-- ── Hidden form (submitted by JS on save) ───────────────────────────────── --}}
<form id="pb-form" method="POST" action="{{ $formAction }}">
    @csrf
    <input type="hidden" name="title"            id="f-title">
    <input type="hidden" name="action"            id="f-action" value="1">
    <input type="hidden" name="meta_title"        id="f-meta-title">
    <input type="hidden" name="meta_description"  id="f-meta-desc">
    <input type="hidden" name="meta_keywords"     id="f-meta-keywords">
    <input type="hidden" name="gjs_html"          id="f-html">
    <input type="hidden" name="gjs_css"           id="f-css">
    <input type="hidden" name="gjs_data"          id="f-data">
    @yield('form-extras')
</form>

{{-- ── GrapesJS canvas ──────────────────────────────────────────────────────── --}}
<div id="gjs">@yield('gjs-placeholder')</div>

{{-- ── Scripts ──────────────────────────────────────────────────────────────── --}}
<script src="https://unpkg.com/grapesjs@0.21.13/dist/grapes.min.js"></script>
<script src="https://unpkg.com/grapesjs-blocks-basic@1.0.2/dist/index.js"></script>

<script>
// ── Meta drawer toggle ────────────────────────────────────────────────────────
function toggleMeta() {
    const drawer  = document.getElementById('pb-meta-drawer');
    const toggle  = document.getElementById('pb-meta-toggle');
    const gjs     = document.getElementById('gjs');
    const toolbar = document.getElementById('pb-toolbar');

    const isOpen = drawer.classList.toggle('open');
    toggle.classList.toggle('open', isOpen);

    // Adjust GrapesJS top offset
    const offset = toolbar.offsetHeight + (isOpen ? drawer.offsetHeight : 0);
    gjs.style.top = offset + 'px';
    if (window.gjsEditor) window.gjsEditor.refresh();
}

// ── Init GrapesJS ─────────────────────────────────────────────────────────────
const gjsConfig = {
    container: '#gjs',
    height: '100%',
    width: 'auto',
    fromElement: false,
    storageManager: false,
    plugins: ['gjs-blocks-basic'],
    pluginsOpts: {
        'gjs-blocks-basic': { flexGrid: true }
    },
    // Inject Tailwind + FontAwesome into the canvas iframe so blocks render correctly
    canvas: {
        styles: [
            'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css',
        ],
        scripts: [
            'https://cdn.tailwindcss.com',
        ],
    },
    @yield('gjs-init-data')
};

@yield('gjs-extra-config')

window.gjsEditor = grapesjs.init(gjsConfig);
const editor = window.gjsEditor;

// ── Custom "Demo Form" block (visible only on demo-landing) ───────────────────
@if(isset($page) && $page->key === 'demo-landing')
editor.BlockManager.add('demo-form-block', {
    label: '<i class="fa fa-wpforms" style="font-size:18px;"></i><br>Demo Form',
    category: 'System',
    content: {
        tagName: 'div',
        attributes: { 'data-demo': 'form' },
        components: [],
        style: {
            'background': '#eff6ff',
            'border': '2px dashed #3b82f6',
            'border-radius': '12px',
            'padding': '32px 24px',
            'text-align': 'center',
            'color': '#1d4ed8',
            'font-family': 'system-ui, sans-serif',
            'font-size': '14px',
            'font-weight': '600',
        },
        content: '<i class="fa fa-lock" style="margin-right:8px;"></i> Demo Access Form (injected by system)',
    },
    attributes: { title: 'Drag to place the demo sign-up form' },
});
@endif

// ── Save handler ──────────────────────────────────────────────────────────────
function savePage() {
    const btn = document.getElementById('pb-save');
    btn.disabled = true;
    btn.innerHTML = '<i class="fa fa-spinner fa-spin fa-sm"></i> Saving…';

    document.getElementById('f-title').value         = document.getElementById('pb-title').value;
    document.getElementById('f-meta-title').value    = document.getElementById('pb-meta-title').value;
    document.getElementById('f-meta-desc').value     = document.getElementById('pb-meta-desc').value;
    document.getElementById('f-meta-keywords').value = document.getElementById('pb-meta-keywords').value;
    document.getElementById('f-action').value        = document.getElementById('pb-action').checked ? '1' : '0';
    document.getElementById('f-html').value          = editor.getHtml();
    document.getElementById('f-css').value           = editor.getCss();
    document.getElementById('f-data').value          = JSON.stringify(editor.getProjectData());

    document.getElementById('pb-form').submit();
}

// Keyboard shortcut: Ctrl/Cmd + S
document.addEventListener('keydown', function (e) {
    if ((e.ctrlKey || e.metaKey) && e.key === 's') {
        e.preventDefault();
        savePage();
    }
});
</script>

@stack('gjs-scripts')

</body>
</html>
