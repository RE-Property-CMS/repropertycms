@php $ta = $ta ?? false; $rows = $rows ?? 3; @endphp
<div style="margin-bottom:16px;">
    <label style="display:block;font-size:11px;font-weight:600;color:#6b7280;text-transform:uppercase;letter-spacing:.05em;margin-bottom:5px;background:none;">{{ $l }}</label>
    @if($ta)
        <textarea name="{{ $n }}" rows="{{ $rows }}"
                  style="display:block;width:100%;padding:9px 13px;border:1px solid #d1d5db;border-radius:8px;font-size:13px;color:#111827;background:#fff;outline:none;resize:vertical;font-family:inherit;line-height:1.55;">{{ $v }}</textarea>
    @else
        <input type="text" name="{{ $n }}" value="{{ $v }}"
               style="display:block;width:100%;padding:9px 13px;border:1px solid #d1d5db;border-radius:8px;font-size:13px;color:#111827;background:#fff;outline:none;">
    @endif
</div>
