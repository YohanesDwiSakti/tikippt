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

$locations = [
    [
        'name' => 'Hub TIKI Denpasar',
        'type' => 'Hub utama',
        'address' => 'Jl. Teuku Umar Barat, Denpasar',
        'hours' => '08.00-17.00 WITA',
        'phone' => '0361 000 101',
        'services' => ['Drop paket', 'Ambil paket', 'Komplain resi'],
    ],
    [
        'name' => 'Gerai Sanur',
        'type' => 'Gerai',
        'address' => 'Jl. Danau Tamblingan, Sanur',
        'hours' => '09.00-16.00 WITA',
        'phone' => '0361 000 202',
        'services' => ['Drop paket', 'Cek ongkir'],
    ],
    [
        'name' => 'Gerai Ubud',
        'type' => 'Gerai',
        'address' => 'Jl. Raya Ubud, Gianyar',
        'hours' => '09.00-16.00 WITA',
        'phone' => '0361 000 303',
        'services' => ['Drop paket', 'Ambil paket'],
    ],
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

Route::get('/tracking', function () use ($packages, $locations) {
    $receipt = request('receipt');
    $selected = $receipt ? collect($packages)->firstWhere('receipt', strtoupper($receipt)) : null;
    $activeTab = request('tab', 'resi');
    if (! in_array($activeTab, ['resi', 'harga', 'lokasi'], true)) {
        $activeTab = 'resi';
    }

    $origin = request('origin', 'Denpasar');
    $destination = request('destination', 'Gianyar');
    $weight = max((float) request('weight', 1), 0);
    $length = max((float) request('length', 0), 0);
    $width = max((float) request('width', 0), 0);
    $height = max((float) request('height', 0), 0);
    $volumeWeight = $length > 0 && $width > 0 && $height > 0 ? ceil(($length * $width * $height) / 6000) : 0;
    $chargeableWeight = max(1, ceil(max($weight, $volumeWeight)));
    $zoneRate = match (strtolower($destination)) {
        'sanur' => 9000,
        'gianyar' => 12000,
        'ubud' => 14000,
        'tabanan' => 15000,
        default => 16000,
    };

    $locationQuery = strtolower(trim(request('q', '')));
    $filteredLocations = $locationQuery === ''
        ? $locations
        : array_filter($locations, fn (array $location): bool =>
            str_contains(strtolower($location['name']), $locationQuery)
            || str_contains(strtolower($location['address']), $locationQuery)
            || str_contains(strtolower($location['type']), $locationQuery)
        );

    return view('tracking', [
        'receipt' => $receipt,
        'selected' => $selected,
        'packages' => $packages,
        'activeTab' => $activeTab,
        'origin' => $origin,
        'destination' => $destination,
        'weight' => request('weight', '1'),
        'length' => request('length', ''),
        'width' => request('width', ''),
        'height' => request('height', ''),
        'chargeableWeight' => $chargeableWeight,
        'volumeWeight' => $volumeWeight,
        'rates' => [
            ['service' => 'REG', 'label' => 'Regular', 'eta' => '2-3 hari', 'price' => $zoneRate * $chargeableWeight],
            ['service' => 'ONS', 'label' => 'Over Night Service', 'eta' => '1 hari', 'price' => ($zoneRate + 8000) * $chargeableWeight],
            ['service' => 'ECO', 'label' => 'Ekonomi', 'eta' => '3-5 hari', 'price' => max(8000, $zoneRate - 3000) * $chargeableWeight],
        ],
        'query' => request('q', ''),
        'locations' => $filteredLocations,
    ]);
})->name('tracking');

Route::get('/cek-ongkir', function () {
    return redirect(route('tracking', array_merge(request()->query(), ['tab' => 'harga'])));
})->name('shipping');

Route::get('/lokasi', function () {
    return redirect(route('tracking', array_merge(request()->query(), ['tab' => 'lokasi'])));
})->name('locations');

Route::get('/cek-lokasi', function () {
    return redirect(route('tracking', array_merge(request()->query(), ['tab' => 'lokasi'])));
});

Route::get('/support', fn () => view('support'))->name('support');

Route::get('/about', fn () => view('about'))->name('about');

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
