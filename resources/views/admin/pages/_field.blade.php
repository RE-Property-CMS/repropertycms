@php $cols = $cols ?? 12; $type = $type ?? 'input'; $rows = $rows ?? 3; @endphp
<div class="col-md-{{ $cols }}">
    <label class="block text-xs font-semibold text-gray-600 mb-1">{{ $label }}</label>
    @if($type === 'textarea')
        <textarea name="{{ $name }}" rows="{{ $rows }}"
                  class="form-control w-full text-sm"
                  style="border:1px solid #d1d5db;border-radius:8px;padding:8px 12px;font-size:13px;">{{ $val }}</textarea>
    @else
        <input type="text" name="{{ $name }}" value="{{ $val }}"
               class="form-control w-full text-sm"
               style="border:1px solid #d1d5db;border-radius:8px;padding:8px 12px;font-size:13px;">
    @endif
</div>
