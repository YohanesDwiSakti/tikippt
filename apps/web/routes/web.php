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

Route::get('/language/{locale}', function (string $locale) {
    abort_unless(in_array($locale, ['id', 'en'], true), 404);

    session(['locale' => $locale]);

    return redirect()->back();
})->name('language.switch');

$packages = [];

$drivers = [];

$locations = [];

Route::get('/', function () use ($packages) {
    $receipt = request('receipt', '');
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

    $origin = request('origin', '');
    $destination = request('destination', '');
    $weight = max((float) request('weight', 0), 0);
    $length = max((float) request('length', 0), 0);
    $width = max((float) request('width', 0), 0);
    $height = max((float) request('height', 0), 0);
    $volumeWeight = $length > 0 && $width > 0 && $height > 0 ? ceil(($length * $width * $height) / 6000) : 0;
    $chargeableWeight = ceil(max($weight, $volumeWeight));

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
        'weight' => request('weight', ''),
        'length' => request('length', ''),
        'width' => request('width', ''),
        'height' => request('height', ''),
        'chargeableWeight' => $chargeableWeight,
        'volumeWeight' => $volumeWeight,
        'rates' => [],
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
        'auth_name' => $data['email'],
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
        'packages' => $packages,
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
