<?php

use Illuminate\Support\Facades\Route;

$packages = [
    [
        'receipt' => 'TKI-DEN-260607101500',
        'status' => 'Dalam Perjalanan',
        'destination' => 'Gianyar',
        'latest_location' => 'Keluar Hub Denpasar',
        'driver' => 'Made Driver',
        'updated_at' => '07 Juni 2026, 10:15 WITA',
        'assignment_status' => 'Dalam Perjalanan',
        'admin_note' => 'Rute Gianyar pagi, prioritas diterima hari ini.',
        'timeline' => [
            ['status' => 'Terdaftar', 'location' => 'Hub Denpasar', 'time' => '09:30 WITA'],
            ['status' => 'Diangkut Driver', 'location' => 'Hub Denpasar', 'time' => '10:00 WITA'],
            ['status' => 'Dalam Perjalanan', 'location' => 'Keluar Hub Denpasar', 'time' => '10:15 WITA'],
        ],
        'proof' => null,
    ],
    [
        'receipt' => 'TKI-DEN-260607132000',
        'status' => 'Sampai Tujuan',
        'destination' => 'Sanur',
        'latest_location' => 'Alamat penerima, Sanur',
        'driver' => 'Kadek Driver',
        'updated_at' => '07 Juni 2026, 13:20 WITA',
        'assignment_status' => 'Selesai',
        'admin_note' => 'Pastikan foto bukti terlihat jelas.',
        'timeline' => [
            ['status' => 'Terdaftar', 'location' => 'Hub Denpasar', 'time' => '08:40 WITA'],
            ['status' => 'Diangkut Driver', 'location' => 'Hub Denpasar', 'time' => '09:10 WITA'],
            ['status' => 'Sampai Tujuan', 'location' => 'Alamat penerima, Sanur', 'time' => '13:20 WITA'],
        ],
        'proof' => [
            'photo' => '/images/proof-placeholder.svg',
            'time' => '07 Juni 2026, 13:20 WITA',
            'location' => 'Alamat penerima, Sanur',
            'note' => 'Diterima langsung oleh penerima.',
        ],
    ],
    [
        'receipt' => 'TKI-DEN-260607150500',
        'status' => 'Diangkut Driver',
        'destination' => 'Ubud',
        'latest_location' => 'Hub Denpasar',
        'driver' => 'Made Driver',
        'updated_at' => '07 Juni 2026, 15:05 WITA',
        'assignment_status' => 'Ditugaskan',
        'admin_note' => 'Bawa bersama paket Gianyar jika rute memungkinkan.',
        'timeline' => [
            ['status' => 'Terdaftar', 'location' => 'Hub Denpasar', 'time' => '14:30 WITA'],
            ['status' => 'Diangkut Driver', 'location' => 'Hub Denpasar', 'time' => '15:05 WITA'],
        ],
        'proof' => null,
    ],
];

$drivers = [
    ['id' => 'drv-1', 'name' => 'Made Driver', 'active' => 2],
    ['id' => 'drv-2', 'name' => 'Kadek Driver', 'active' => 1],
    ['id' => 'drv-3', 'name' => 'Wayan Driver', 'active' => 0],
];

Route::get('/', function () use ($packages) {
    $receipt = request('receipt', 'TKI-DEN-260607101500');
    $selected = collect($packages)->firstWhere('receipt', strtoupper($receipt));

    return view('home', [
        'receipt' => $receipt,
        'selected' => $selected,
        'packages' => $packages,
    ]);
})->name('home');

Route::get('/tracking', function () use ($packages) {
    $receipt = request('receipt');
    $selected = $receipt ? collect($packages)->firstWhere('receipt', strtoupper($receipt)) : null;

    return view('tracking', [
        'receipt' => $receipt,
        'selected' => $selected,
        'packages' => $packages,
    ]);
})->name('tracking');

Route::get('/login', fn () => view('login'))->name('login');

Route::get('/admin', function () use ($packages, $drivers) {
    return view('admin.dashboard', [
        'packages' => $packages,
        'drivers' => $drivers,
    ]);
})->name('admin.dashboard');

Route::get('/admin/packages', function () use ($packages) {
    return view('admin.packages', ['packages' => $packages]);
})->name('admin.packages');

Route::get('/admin/assignments', function () use ($packages, $drivers) {
    return view('admin.assignments', [
        'packages' => $packages,
        'drivers' => $drivers,
    ]);
})->name('admin.assignments');

Route::get('/admin/proofs', function () use ($packages) {
    return view('admin.proofs', [
        'packages' => array_filter($packages, fn (array $package): bool => $package['proof'] !== null),
    ]);
})->name('admin.proofs');

Route::get('/driver', function () use ($packages) {
    return view('driver.index', [
        'packages' => array_filter($packages, fn (array $package): bool => $package['driver'] === 'Made Driver'),
    ]);
})->name('driver.index');

Route::get('/driver/proof/{receipt}', function (string $receipt) use ($packages) {
    $package = collect($packages)->firstWhere('receipt', strtoupper($receipt));

    abort_if(! $package, 404);

    return view('driver.proof', ['package' => $package]);
})->name('driver.proof');
