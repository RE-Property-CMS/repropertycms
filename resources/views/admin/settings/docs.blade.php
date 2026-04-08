@extends('admin.layouts.default')

@section('title', 'Help & Documentation')

@section('content')

<div class="d-flex align-items-center justify-content-between my-4 flex-wrap page-heading">
    <div>
        <h5 class="mb-0">Help &amp; Documentation</h5>
        <p class="text-sm text-gray-500 mb-0 mt-1">Developer reference for RePropertyCMS — architecture, patterns, deployment, and conventions.</p>
    </div>
    <a href="{{ route('admin.settings.index') }}"
       style="display:inline-flex;align-items:center;gap:6px;padding:7px 14px;background:white;border:1px solid #e5e7eb;border-radius:8px;font-size:13px;font-weight:500;color:#374151;text-decoration:none;">
        <i class="fas fa-arrow-left" style="font-size:11px;"></i> Back to Settings
    </a>
</div>

<div style="display:grid;grid-template-columns:240px 1fr;gap:24px;align-items:start;">

    {{-- Table of Contents sidebar --}}
    <div style="position:sticky;top:20px;background:white;border:1px solid #e5e7eb;border-radius:12px;padding:16px;box-shadow:0 1px 3px rgba(0,0,0,0.05);">
        <div style="font-size:11px;font-weight:700;letter-spacing:.08em;text-transform:uppercase;color:#9ca3af;margin-bottom:10px;">Contents</div>
        <nav id="toc" style="display:flex;flex-direction:column;gap:2px;"></nav>
    </div>

    {{-- Markdown content --}}
    <div style="background:white;border:1px solid #e5e7eb;border-radius:12px;padding:32px 36px;box-shadow:0 1px 3px rgba(0,0,0,0.05);min-width:0;">
        <div id="docs-content"></div>
    </div>

</div>

{{-- Raw markdown passed from controller --}}
<script id="raw-markdown" type="text/plain">{{ $markdown }}</script>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/marked@9/marked.min.js"></script>
<style>
/* ── Docs content styles ──────────────────────────── */
#docs-content h1 { font-size:1.6rem;font-weight:700;color:#111827;border-bottom:2px solid #e5e7eb;padding-bottom:10px;margin:0 0 20px; }
#docs-content h2 { font-size:1.2rem;font-weight:700;color:#111827;border-bottom:1px solid #f3f4f6;padding-bottom:6px;margin:32px 0 14px; }
#docs-content h3 { font-size:1rem;font-weight:600;color:#1f2937;margin:22px 0 10px; }
#docs-content h4 { font-size:.9rem;font-weight:600;color:#374151;margin:16px 0 8px; }
#docs-content p  { font-size:.875rem;color:#4b5563;line-height:1.7;margin:0 0 12px; }
#docs-content ul,#docs-content ol { font-size:.875rem;color:#4b5563;padding-left:20px;margin:0 0 12px;line-height:1.7; }
#docs-content li { margin-bottom:3px; }
#docs-content a  { color:#2563eb;text-decoration:none; }
#docs-content a:hover { text-decoration:underline; }
#docs-content strong { color:#111827;font-weight:600; }
#docs-content code {
    font-size:.8rem;background:#f3f4f6;color:#dc2626;
    padding:1px 5px;border-radius:4px;font-family:monospace;
}
#docs-content pre {
    background:#1e293b;border-radius:8px;padding:16px;
    overflow-x:auto;margin:12px 0 16px;
}
#docs-content pre code {
    background:none;color:#e2e8f0;font-size:.8rem;
    padding:0;border-radius:0;
}
#docs-content table {
    width:100%;border-collapse:collapse;font-size:.8rem;
    margin:12px 0 16px;
}
#docs-content th {
    background:#f9fafb;font-weight:600;color:#374151;
    text-align:left;padding:8px 12px;
    border:1px solid #e5e7eb;
}
#docs-content td {
    padding:7px 12px;border:1px solid #e5e7eb;
    color:#4b5563;vertical-align:top;
}
#docs-content tr:hover td { background:#f9fafb; }
#docs-content blockquote {
    border-left:4px solid #e5e7eb;margin:0 0 12px;
    padding:8px 16px;background:#f9fafb;border-radius:0 6px 6px 0;
}
#docs-content blockquote p { margin:0;color:#6b7280;font-style:italic; }
#docs-content hr { border:none;border-top:1px solid #e5e7eb;margin:24px 0; }

/* ── TOC styles ──────────────────────────────────── */
#toc a {
    display:block;padding:4px 8px;border-radius:6px;
    font-size:.78rem;color:#6b7280;text-decoration:none;
    transition:background .15s,color .15s;
    white-space:nowrap;overflow:hidden;text-overflow:ellipsis;
}
#toc a:hover { background:#f3f4f6;color:#111827; }
#toc a.toc-h2 { font-weight:600;color:#374151;margin-top:4px; }
#toc a.toc-h3 { padding-left:16px;font-size:.75rem; }
</style>
<script>
document.addEventListener('DOMContentLoaded', function () {
    const raw = document.getElementById('raw-markdown').textContent;
    const container = document.getElementById('docs-content');
    const toc = document.getElementById('toc');

    // Render markdown
    container.innerHTML = marked.parse(raw);

    // Add IDs to headings and build TOC
    const headings = container.querySelectorAll('h1, h2, h3');
    headings.forEach(function (h, i) {
        const id = 'doc-' + i + '-' + h.textContent.toLowerCase().replace(/[^a-z0-9]+/g, '-');
        h.id = id;
        h.style.scrollMarginTop = '20px';

        if (h.tagName === 'H1' || h.tagName === 'H2' || h.tagName === 'H3') {
            const a = document.createElement('a');
            a.href = '#' + id;
            a.textContent = h.textContent;
            a.className = 'toc-' + h.tagName.toLowerCase();
            toc.appendChild(a);
        }
    });
});
</script>
@endpush

@endsection
