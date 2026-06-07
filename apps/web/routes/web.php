<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;

function guardRole(string $role)
{
    if (session('auth_role') === $role) {
        return null;
    }

    return redirect()
        ->route('login', ['redirect' => request()->path()])
        ->with('auth_notice', 'Silakan login dulu untuk membuka halaman tersebut.');
}

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

Route::post('/login', function (Request $request) {
    $data = $request->validate([
        'email' => ['required', 'email'],
        'password' => ['required', 'string'],
        'role' => ['required', 'in:admin,driver'],
        'redirect' => ['nullable', 'string'],
    ]);

    session()->regenerate();
    session([
        'auth_role' => $data['role'],
        'auth_email' => $data['email'],
        'auth_name' => $data['role'] === 'admin' ? 'Admin Hub' : 'Made Driver',
    ]);

    $redirect = trim($data['redirect'] ?? '', '/');
    if ($redirect !== '' && str_starts_with($redirect, $data['role'])) {
        return redirect('/' . $redirect);
    }

    return redirect()->route($data['role'] === 'admin' ? 'admin.dashboard' : 'driver.index');
})->name('login.submit');

Route::post('/logout', function () {
    session()->forget(['auth_role', 'auth_email', 'auth_name']);
    session()->invalidate();
    session()->regenerateToken();

    return redirect()->route('login')->with('auth_notice', 'Anda sudah logout.');
})->name('logout');

Route::get('/admin', function () use ($packages, $drivers) {
    if ($redirect = guardRole('admin')) {
        return $redirect;
    }

    return view('admin.dashboard', [
        'packages' => $packages,
        'drivers' => $drivers,
    ]);
})->name('admin.dashboard');

Route::get('/admin/packages', function () use ($packages) {
    if ($redirect = guardRole('admin')) {
        return $redirect;
    }

    return view('admin.packages', ['packages' => $packages]);
})->name('admin.packages');

Route::get('/admin/assignments', function () use ($packages, $drivers) {
    if ($redirect = guardRole('admin')) {
        return $redirect;
    }

    return view('admin.assignments', [
        'packages' => $packages,
        'drivers' => $drivers,
    ]);
})->name('admin.assignments');

Route::get('/admin/proofs', function () use ($packages) {
    if ($redirect = guardRole('admin')) {
        return $redirect;
    }

    return view('admin.proofs', [
        'packages' => array_filter($packages, fn (array $package): bool => $package['proof'] !== null),
    ]);
})->name('admin.proofs');

Route::get('/driver', function () use ($packages) {
    if ($redirect = guardRole('driver')) {
        return $redirect;
    }

    return view('driver.index', [
        'packages' => array_filter($packages, fn (array $package): bool => $package['driver'] === 'Made Driver'),
    ]);
})->name('driver.index');

Route::get('/driver/proof/{receipt}', function (string $receipt) use ($packages) {
    if ($redirect = guardRole('driver')) {
        return $redirect;
    }

    $package = collect($packages)->firstWhere('receipt', strtoupper($receipt));

    abort_if(! $package, 404);

    return view('driver.proof', ['package' => $package]);
})->name('driver.proof');
