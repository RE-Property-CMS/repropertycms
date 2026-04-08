@extends('admin.layouts.default')

@section('title', 'Brand & Appearance')

@section('content')

    <div class="d-flex align-items-center justify-content-between my-4 flex-wrap page-heading">
        <div>
            <h5 class="mb-0">Brand & Appearance</h5>
            <nav style="font-size:13px;color:#6b7280;margin-top:4px;display:flex;align-items:center;gap:6px;">
                <a href="{{ route('admin.settings.index') }}" style="color:#9ca3af;text-decoration:none;">Settings</a>
                <svg style="width:12px;height:12px;color:#d1d5db;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                <span>Brand</span>
            </nav>
        </div>
    </div>

    @if(session('success'))
        <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 5000)" x-transition
            style="display:flex;align-items:flex-start;gap:12px;padding:14px 16px;background:#f0fdf4;border:1px solid #bbf7d0;border-left:4px solid #16a34a;border-radius:10px;margin-bottom:20px;">
            <svg style="width:18px;height:18px;color:#16a34a;flex-shrink:0;margin-top:1px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
            </svg>
            <div style="flex:1;">
                <div style="font-weight:600;font-size:13px;color:#15803d;">Brand Settings Saved</div>
                <div style="font-size:13px;color:#166534;">{{ session('success') }}</div>
            </div>
            <button @click="show=false" style="color:#86efac;background:none;border:none;cursor:pointer;font-size:18px;line-height:1;padding:0;">&times;</button>
        </div>
    @endif

    @if($errors->any())
        <div style="display:flex;align-items:flex-start;gap:12px;padding:14px 16px;background:#fef2f2;border:1px solid #fecaca;border-left:4px solid #dc2626;border-radius:10px;margin-bottom:20px;">
            <svg style="width:18px;height:18px;color:#dc2626;flex-shrink:0;margin-top:1px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v4m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/>
            </svg>
            <div>
                <div style="font-weight:600;font-size:13px;color:#dc2626;margin-bottom:4px;">Please fix the following:</div>
                <ul style="margin:0;padding-left:16px;font-size:12px;color:#991b1b;">
                    @foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach
                </ul>
            </div>
        </div>
    @endif

    <form method="POST" action="{{ route('admin.settings.brand.save') }}" enctype="multipart/form-data" style="max-width:960px;margin:0 auto;">
        @csrf

        {{-- Card header --}}
        <div style="background:linear-gradient(135deg,#5b21b6 0%,#8b5cf6 100%);padding:20px 24px;display:flex;align-items:center;gap:14px;border-radius:12px 12px 0 0;">
            <div style="width:46px;height:46px;background:rgba(255,255,255,0.2);border-radius:10px;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                <svg style="width:22px;height:22px;color:white;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M7 21a4 4 0 01-4-4V5a2 2 0 012-2h4a2 2 0 012 2v12a4 4 0 01-4 4zm0 0h12a2 2 0 002-2v-4a2 2 0 00-2-2h-2.343M11 7.343l1.657-1.657a2 2 0 012.828 0l2.829 2.829a2 2 0 010 2.828l-8.486 8.485M7 17h.01"/>
                </svg>
            </div>
            <div style="flex:1;">
                <div style="color:white;font-weight:600;font-size:16px;">Brand & Appearance</div>
                <div style="color:rgba(255,255,255,0.75);font-size:13px;margin-top:2px;">Colors, typography, logo, and visual identity applied globally</div>
            </div>
            <span style="background:rgba(255,255,255,0.2);color:white;font-size:12px;padding:4px 12px;border-radius:9999px;font-weight:500;border:1px solid rgba(255,255,255,0.3);flex-shrink:0;">
                <svg style="width:12px;height:12px;display:inline;margin-right:4px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>Always Active
            </span>
        </div>

        <div style="background:white;border:1px solid #e5e7eb;border-top:none;border-radius:0 0 12px 12px;box-shadow:0 1px 4px rgba(0,0,0,0.06);">

            {{-- Colors & Fonts grid --}}
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:0;border-bottom:1px solid #f3f4f6;">

                {{-- Colors --}}
                <div style="padding:24px;border-right:1px solid #f3f4f6;">
                    <div style="font-size:11px;font-weight:600;color:#6b7280;text-transform:uppercase;letter-spacing:0.08em;margin-bottom:18px;display:flex;align-items:center;gap:7px;">
                        <svg style="width:13px;height:13px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21a4 4 0 01-4-4V5a2 2 0 012-2h4a2 2 0 012 2v12a4 4 0 01-4 4zm0 0h12a2 2 0 002-2v-4a2 2 0 00-2-2h-2.343M11 7.343l1.657-1.657a2 2 0 012.828 0l2.829 2.829a2 2 0 010 2.828l-8.486 8.485M7 17h.01"/></svg>
                        Color Palette
                    </div>

                    @php
                        $colors = [
                            ['name' => 'primary_color',   'label' => 'Primary',    'hint' => 'Buttons, links, main accents',    'default' => '#28ADC4'],
                            ['name' => 'secondary_color', 'label' => 'Secondary',  'hint' => 'Headings & secondary elements',   'default' => '#0069a6'],
                            ['name' => 'accent_color',    'label' => 'Accent',     'hint' => 'Table headers, highlights',       'default' => '#008a8f'],
                            ['name' => 'accent_2_color',  'label' => 'Accent 2',   'hint' => 'Secondary accent (purple tones)', 'default' => '#6f4693'],
                            ['name' => 'sidebar_color',   'label' => 'Sidebar',    'hint' => 'Sidebar/navigation background',   'default' => '#152636'],
                        ];
                    @endphp

                    <div style="display:flex;flex-direction:column;gap:14px;">
                        @foreach($colors as $color)
                            @php $val = old($color['name'], $brand->{$color['name']} ?? $color['default']); @endphp
                            <div style="display:flex;align-items:center;gap:12px;">
                                <div style="flex:1;">
                                    <div style="font-size:13px;font-weight:500;color:#374151;">{{ $color['label'] }}</div>
                                    <div style="font-size:11px;color:#9ca3af;margin-top:1px;">{{ $color['hint'] }}</div>
                                </div>
                                <div style="display:flex;align-items:center;gap:8px;flex-shrink:0;">
                                    <div style="position:relative;width:38px;height:38px;border-radius:8px;overflow:hidden;border:2px solid #e5e7eb;cursor:pointer;"
                                        onclick="document.getElementById('{{ $color['name'] }}').click()">
                                        <div id="{{ $color['name'] }}_preview" style="position:absolute;inset:0;background:{{ $val }};"></div>
                                        <input type="color" name="{{ $color['name'] }}" id="{{ $color['name'] }}"
                                            value="{{ $val }}"
                                            oninput="document.getElementById('{{ $color['name'] }}_hex').value=this.value; document.getElementById('{{ $color['name'] }}_preview').style.background=this.value;"
                                            style="position:absolute;inset:0;width:100%;height:100%;opacity:0;cursor:pointer;border:none;padding:0;">
                                    </div>
                                    <input type="text" id="{{ $color['name'] }}_hex" value="{{ $val }}"
                                        oninput="if(/^#[0-9A-Fa-f]{6}$/.test(this.value)){document.getElementById('{{ $color['name'] }}').value=this.value;document.getElementById('{{ $color['name'] }}_preview').style.background=this.value;}"
                                        style="width:80px;padding:7px 8px;border:1px solid #d1d5db;border-radius:6px;font-size:12px;font-family:monospace;outline:none;text-transform:uppercase;"
                                        onfocus="this.style.borderColor='#8b5cf6';this.style.boxShadow='0 0 0 3px rgba(139,92,246,0.1)'"
                                        onblur="this.style.borderColor='#d1d5db';this.style.boxShadow='none'">
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                {{-- Fonts --}}
                <div style="padding:24px;">
                    <div style="font-size:11px;font-weight:600;color:#6b7280;text-transform:uppercase;letter-spacing:0.08em;margin-bottom:18px;display:flex;align-items:center;gap:7px;">
                        <svg style="width:13px;height:13px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h8m-8 6h16"/></svg>
                        Typography
                    </div>

                    <div style="display:flex;flex-direction:column;gap:14px;margin-bottom:18px;">
                        <div>
                            <label style="display:block;font-size:13px;font-weight:500;color:#374151;margin-bottom:3px;">Body Font</label>
                            <p style="font-size:11px;color:#9ca3af;margin:0 0 6px;">Main paragraph text across all pages</p>
                            <select name="font_body"
                                style="width:100%;padding:8px 12px;border:1px solid #d1d5db;border-radius:8px;font-size:13px;outline:none;transition:border-color 0.15s;"
                                onfocus="this.style.borderColor='#8b5cf6';this.style.boxShadow='0 0 0 3px rgba(139,92,246,0.1)'"
                                onblur="this.style.borderColor='#d1d5db';this.style.boxShadow='none'">
                                @foreach(['Lato','Nunito','Poppins','Roboto','Open Sans','Inter','Raleway','Montserrat','Source Sans 3','Ubuntu'] as $font)
                                    <option value="{{ $font }}" {{ ($brand->font_body ?? 'Lato') === $font ? 'selected' : '' }}>{{ $font }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label style="display:block;font-size:13px;font-weight:500;color:#374151;margin-bottom:3px;">Heading Font</label>
                            <p style="font-size:11px;color:#9ca3af;margin:0 0 6px;">H1–H6 elements and property titles</p>
                            <select name="font_heading"
                                style="width:100%;padding:8px 12px;border:1px solid #d1d5db;border-radius:8px;font-size:13px;outline:none;transition:border-color 0.15s;"
                                onfocus="this.style.borderColor='#8b5cf6';this.style.boxShadow='0 0 0 3px rgba(139,92,246,0.1)'"
                                onblur="this.style.borderColor='#d1d5db';this.style.boxShadow='none'">
                                @foreach(['Julius Sans One','Playfair Display','Merriweather','Cinzel','Cormorant Garamond','Josefin Sans','Montserrat','Lato','Nunito','Poppins'] as $font)
                                    <option value="{{ $font }}" {{ ($brand->font_heading ?? 'Julius Sans One') === $font ? 'selected' : '' }}>{{ $font }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label style="display:block;font-size:13px;font-weight:500;color:#374151;margin-bottom:3px;">Admin Panel Font</label>
                            <p style="font-size:11px;color:#9ca3af;margin:0 0 6px;">Used inside the admin and agent portals</p>
                            <select name="font_admin"
                                style="width:100%;padding:8px 12px;border:1px solid #d1d5db;border-radius:8px;font-size:13px;outline:none;transition:border-color 0.15s;"
                                onfocus="this.style.borderColor='#8b5cf6';this.style.boxShadow='0 0 0 3px rgba(139,92,246,0.1)'"
                                onblur="this.style.borderColor='#d1d5db';this.style.boxShadow='none'">
                                @foreach(['Lato','Nunito','Poppins','Roboto','Open Sans','Inter','Raleway','Montserrat','Source Sans 3'] as $font)
                                    <option value="{{ $font }}" {{ ($brand->font_admin ?? 'Lato') === $font ? 'selected' : '' }}>{{ $font }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    {{-- Font preview --}}
                    <div style="background:#f9fafb;border:1px solid #e5e7eb;border-radius:8px;padding:14px;">
                        <div style="font-size:11px;color:#9ca3af;margin-bottom:8px;font-weight:500;text-transform:uppercase;letter-spacing:0.06em;">Live Preview (saved fonts)</div>
                        <div style="font-family:var(--font-heading);font-size:17px;color:#111827;margin-bottom:4px;">The quick brown fox</div>
                        <div style="font-family:var(--font-body);font-size:13px;color:#6b7280;line-height:1.5;">The quick brown fox jumps over the lazy dog. Pack my box with five dozen liquor jugs.</div>
                    </div>
                </div>
            </div>

            {{-- Identity & Media --}}
            <div style="padding:24px;">
                <div style="font-size:11px;font-weight:600;color:#6b7280;text-transform:uppercase;letter-spacing:0.08em;margin-bottom:18px;display:flex;align-items:center;gap:7px;">
                    <svg style="width:13px;height:13px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                    Identity & Media
                </div>

                <div style="display:grid;grid-template-columns:1fr 1fr;gap:24px;">

                    {{-- Logo --}}
                    <div style="background:#f9fafb;border:1px solid #e5e7eb;border-radius:10px;padding:18px;">
                        <div style="font-size:13px;font-weight:600;color:#374151;margin-bottom:4px;">Site Logo</div>
                        <div style="font-size:12px;color:#9ca3af;margin-bottom:14px;">Shown in the sidebar, admin panel and agent portal. PNG or SVG recommended.</div>

                        @if($brand && $brand->logo_path)
                            <div style="background:white;border:1px solid #e5e7eb;border-radius:8px;padding:14px;margin-bottom:12px;display:inline-flex;align-items:center;gap:10px;">
                                <img src="{{ asset($brand->logo_path) }}" alt="Current Logo" style="height:40px;max-width:160px;object-fit:contain;">
                                <span style="font-size:11px;color:#9ca3af;">Current</span>
                            </div>
                        @endif

                        <label style="display:flex;align-items:center;gap:8px;padding:10px 14px;border:2px dashed #d1d5db;border-radius:8px;cursor:pointer;transition:border-color 0.15s;background:white;"
                            onmouseover="this.style.borderColor='#8b5cf6'" onmouseout="this.style.borderColor='#d1d5db'">
                            <svg style="width:18px;height:18px;color:#9ca3af;flex-shrink:0;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/></svg>
                            <span style="font-size:13px;color:#6b7280;" id="logo_label">{{ $brand && $brand->logo_path ? 'Upload new logo' : 'Choose file' }}</span>
                            <input type="file" name="logo" accept="image/png,image/jpeg,image/jpg,image/gif,image/svg+xml,image/webp"
                                style="display:none;" onchange="document.getElementById('logo_label').textContent=this.files[0]?this.files[0].name:'Choose file'">
                        </label>
                        <p style="font-size:11px;color:#9ca3af;margin-top:6px;">Max 2MB — PNG or SVG with transparent background recommended</p>
                    </div>

                    {{-- Favicon --}}
                    <div style="background:#f9fafb;border:1px solid #e5e7eb;border-radius:10px;padding:18px;">
                        <div style="font-size:13px;font-weight:600;color:#374151;margin-bottom:4px;">Favicon</div>
                        <div style="font-size:12px;color:#9ca3af;margin-bottom:14px;">Browser tab icon. ICO or PNG format. Recommended size 32×32 or 64×64 px.</div>

                        @if($brand && $brand->favicon_path)
                            <div style="background:white;border:1px solid #e5e7eb;border-radius:8px;padding:14px;margin-bottom:12px;display:inline-flex;align-items:center;gap:10px;">
                                <img src="{{ asset($brand->favicon_path) }}" alt="Current Favicon" style="height:32px;width:32px;object-fit:contain;">
                                <span style="font-size:11px;color:#9ca3af;">Current</span>
                            </div>
                        @endif

                        <label style="display:flex;align-items:center;gap:8px;padding:10px 14px;border:2px dashed #d1d5db;border-radius:8px;cursor:pointer;transition:border-color 0.15s;background:white;"
                            onmouseover="this.style.borderColor='#8b5cf6'" onmouseout="this.style.borderColor='#d1d5db'">
                            <svg style="width:18px;height:18px;color:#9ca3af;flex-shrink:0;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/></svg>
                            <span style="font-size:13px;color:#6b7280;" id="favicon_label">{{ $brand && $brand->favicon_path ? 'Upload new favicon' : 'Choose file' }}</span>
                            <input type="file" name="favicon" accept=".ico,image/png,image/jpeg,image/svg+xml"
                                style="display:none;" onchange="document.getElementById('favicon_label').textContent=this.files[0]?this.files[0].name:'Choose file'">
                        </label>
                        <p style="font-size:11px;color:#9ca3af;margin-top:6px;">Max 512KB — ICO or PNG preferred</p>
                    </div>

                </div>
            </div>

            {{-- Social Media Links --}}
            <div style="padding:24px;border-top:1px solid #f3f4f6;">
                <div style="font-size:11px;font-weight:600;color:#6b7280;text-transform:uppercase;letter-spacing:0.08em;margin-bottom:18px;display:flex;align-items:center;gap:7px;">
                    <svg style="width:13px;height:13px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"/></svg>
                    Social Media &amp; Links
                </div>
                <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;">
                    @php
                        $socials = [
                            ['name' => 'website_url',   'label' => 'Website URL',   'placeholder' => 'https://yourwebsite.com',          'icon' => 'M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064'],
                            ['name' => 'instagram_url', 'label' => 'Instagram URL', 'placeholder' => 'https://instagram.com/yourhandle',   'icon' => 'M16 11.37A4 4 0 1112.63 8 4 4 0 0116 11.37zm1.5-4.87h.01'],
                            ['name' => 'facebook_url',  'label' => 'Facebook URL',  'placeholder' => 'https://facebook.com/yourpage',      'icon' => 'M18 2h-3a5 5 0 00-5 5v3H7v4h3v8h4v-8h3l1-4h-4V7a1 1 0 011-1h3z'],
                            ['name' => 'twitter_url',   'label' => 'X / Twitter URL','placeholder' => 'https://x.com/yourhandle',          'icon' => 'M23 3a10.9 10.9 0 01-3.14 1.53 4.48 4.48 0 00-7.86 3v1A10.66 10.66 0 013 4s-4 9 5 13a11.64 11.64 0 01-7 2c9 5 20 0 20-11.5a4.5 4.5 0 00-.08-.83A7.72 7.72 0 0023 3z'],
                            ['name' => 'linkedin_url',  'label' => 'LinkedIn URL',  'placeholder' => 'https://linkedin.com/company/yours', 'icon' => 'M16 8a6 6 0 016 6v7h-4v-7a2 2 0 00-2-2 2 2 0 00-2 2v7h-4v-7a6 6 0 016-6zM2 9h4v12H2z M4 6a2 2 0 100-4 2 2 0 000 4z'],
                        ];
                    @endphp
                    @foreach($socials as $social)
                        <div>
                            <label style="display:flex;align-items:center;gap:6px;font-size:13px;font-weight:500;color:#374151;margin-bottom:6px;">
                                <svg style="width:14px;height:14px;color:#6b7280;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $social['icon'] }}"/></svg>
                                {{ $social['label'] }}
                            </label>
                            <input type="url" name="{{ $social['name'] }}"
                                value="{{ old($social['name'], $brand->{$social['name']} ?? '') }}"
                                placeholder="{{ $social['placeholder'] }}"
                                style="width:100%;padding:8px 12px;border:1px solid #d1d5db;border-radius:8px;font-size:13px;outline:none;box-sizing:border-box;"
                                onfocus="this.style.borderColor='#8b5cf6';this.style.boxShadow='0 0 0 3px rgba(139,92,246,0.1)'"
                                onblur="this.style.borderColor='#d1d5db';this.style.boxShadow='none'">
                            @error($social['name'])
                                <span style="font-size:11px;color:#dc2626;">{{ $message }}</span>
                            @enderror
                        </div>
                    @endforeach
                </div>
                <p style="font-size:11px;color:#9ca3af;margin-top:12px;">These links appear in email footers and other branded communications. Leave blank to omit.</p>
            </div>

            {{-- Footer --}}
            <div style="display:flex;align-items:center;justify-content:space-between;padding:16px 24px;border-top:1px solid #f3f4f6;">
                <a href="{{ route('admin.settings.index') }}" style="font-size:13px;color:#6b7280;text-decoration:none;display:inline-flex;align-items:center;gap:6px;"
                    onmouseover="this.style.color='#374151'" onmouseout="this.style.color='#6b7280'">
                    <svg style="width:14px;height:14px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                    Back to Settings
                </a>
                <button type="submit"
                    style="display:inline-flex;align-items:center;gap:7px;padding:9px 22px;background:#7c3aed;color:white;border:none;border-radius:8px;font-size:13px;font-weight:500;cursor:pointer;transition:background 0.15s;"
                    onmouseover="this.style.background='#6d28d9'" onmouseout="this.style.background='#7c3aed'">
                    <svg style="width:14px;height:14px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    Save Brand Settings
                </button>
            </div>
        </div>

    </form>

@stop

@push('scripts')
<script src="//unpkg.com/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
<script>
    document.querySelectorAll('input[type="text"][id$="_hex"]').forEach(function(input) {
        input.addEventListener('change', function() {
            var val = this.value.trim();
            if (/^#[0-9A-Fa-f]{6}$/.test(val)) {
                var colorId = this.id.replace('_hex', '');
                document.getElementById(colorId).value = val;
                document.getElementById(colorId + '_preview').style.background = val;
            }
        });
    });
</script>
@endpush
