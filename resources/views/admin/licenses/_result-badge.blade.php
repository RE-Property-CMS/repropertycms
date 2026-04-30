@php
$badges = [
    'success'              => ['bg-green-100 text-green-700',  'fa-circle-check',       'Success'],
    'invalid_key'          => ['bg-red-100 text-red-700',      'fa-circle-xmark',       'Invalid Key'],
    'revoked'              => ['bg-orange-100 text-orange-700','fa-ban',                'Revoked'],
    'expired'              => ['bg-gray-100 text-gray-600',    'fa-clock',              'Expired'],
    'domain_limit_reached' => ['bg-amber-100 text-amber-700',  'fa-triangle-exclamation','Limit Reached'],
];
[$cls, $icon, $label] = $badges[$result] ?? ['bg-gray-100 text-gray-500', 'fa-question', $result];
@endphp
<span class="inline-flex items-center gap-1 text-xs font-semibold {{ $cls }} px-2 py-0.5 rounded-full">
    <i class="fa {{ $icon }} fa-xs"></i> {{ $label }}
</span>
