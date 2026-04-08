@props(['url'])
<tr>
<td class="header">
<a href="{{ $url }}" style="display: inline-block;">
@php $brandLogo = cache('brand_settings')?->logo_path; @endphp
<img src="{{ $brandLogo ? asset($brandLogo) : asset('images/logo-placeholder.png') }}" class="logo" alt="{{ config('app.name') }}">
</a>
</td>
</tr>