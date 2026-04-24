<?php

return [
    'name' => env('BOOTH_NAME', env('APP_NAME', 'Kasir Booth')),
    'address' => env('BOOTH_ADDRESS', 'Area booth utama'),
    'city' => env('BOOTH_CITY', 'Makassar'),
    'phone' => env('BOOTH_PHONE', '-'),
    'logo' => env('BOOTH_LOGO', 'images/branding/booth-logo.svg'),
    'receipt_footer' => env('BOOTH_RECEIPT_FOOTER', 'Terima kasih sudah belanja di booth kami.'),
    'receipt_paper' => env('BOOTH_RECEIPT_PAPER', '80'),
];
