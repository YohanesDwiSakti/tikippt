<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\URL;
use Illuminate\Http\Request;
use App\Support\SupabaseGateway;

if (app()->environment('production')) {
    URL::forceScheme('https');
}

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

$locations = [];

Route::get('/', function (SupabaseGateway $supabase) {
    $receipt = request('receipt', '');
    $selected = $receipt !== '' ? $supabase->packageByReceipt($receipt) : null;

    return view('home', [
        'receipt' => $receipt,
        'selected' => $selected,
        'packages' => [],
    ]);
})->name('home');

Route::get('/tracking', function (SupabaseGateway $supabase) use ($locations) {
    $receipt = request('receipt');
    $selected = $receipt ? $supabase->packageByReceipt($receipt) : null;
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
        'packages' => [],
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

Route::post('/login', function (Request $request, SupabaseGateway $supabase) {
    $data = $request->validate([
        'email' => ['required', 'email'],
        'password' => ['required', 'string'],
        'role' => ['required', 'in:admin,driver'],
        'redirect' => ['nullable', 'string'],
    ]);

    try {
        $login = $supabase->login($data['email'], $data['password'], $data['role']);
    } catch (\Throwable) {
        return back()->withErrors(['email' => __('messages.login.error')])->withInput($request->except('password'));
    }

    session()->regenerate();
    session([
        'auth_id' => $login['profile']['id'],
        'auth_role' => $login['profile']['role'],
        'auth_email' => $login['profile']['email'],
        'auth_name' => $login['profile']['name'],
        'auth_token' => $login['access_token'],
    ]);

    $redirect = trim($data['redirect'] ?? '', '/');
    if ($redirect !== '' && str_starts_with($redirect, $data['role'])) {
        return redirect('/' . $redirect);
    }

    return redirect()->route($data['role'] === 'admin' ? 'admin.dashboard' : 'driver.index');
})->name('login.submit');

Route::match(['GET', 'POST'], '/logout', function () {
    session()->forget(['auth_id', 'auth_role', 'auth_email', 'auth_name', 'auth_token']);
    session()->invalidate();
    session()->regenerateToken();

    return redirect()->route('login')->with('auth_notice', 'Anda sudah logout.');
})->name('logout');

Route::get('/admin', function (SupabaseGateway $supabase) {
    if ($redirect = guardRole('admin')) {
        return $redirect;
    }

    return view('admin.dashboard', [
        'packages' => $supabase->packages(),
        'drivers' => $supabase->drivers(),
    ]);
})->name('admin.dashboard');

Route::get('/admin/packages', function (SupabaseGateway $supabase) {
    if ($redirect = guardRole('admin')) {
        return $redirect;
    }

    return view('admin.packages', ['packages' => $supabase->packages()]);
})->name('admin.packages');

Route::post('/admin/packages', function (Request $request, SupabaseGateway $supabase) {
    if ($redirect = guardRole('admin')) {
        return $redirect;
    }

    $data = $request->validate([
        'receipt' => ['required', 'string', 'max:80'],
        'status' => ['required', 'in:Terdaftar,Diangkut Driver,Dalam Perjalanan,Sampai Tujuan,Gagal Dikirim,Cancel'],
        'destination' => ['required', 'string', 'max:160'],
        'latest_location' => ['required', 'string', 'max:160'],
        'note' => ['nullable', 'string', 'max:500'],
    ]);

    try {
        $supabase->savePackage($data, session('auth_id'));
    } catch (\Throwable $error) {
        return back()->withErrors(['receipt' => $error->getMessage()])->withInput();
    }

    return redirect()->route('admin.packages')->with('status', 'Paket berhasil disimpan.');
})->name('admin.packages.store');

Route::get('/admin/assignments', function (SupabaseGateway $supabase) {
    if ($redirect = guardRole('admin')) {
        return $redirect;
    }

    return view('admin.assignments', [
        'packages' => $supabase->packages(),
        'drivers' => $supabase->drivers(),
    ]);
})->name('admin.assignments');

Route::post('/admin/assignments', function (Request $request, SupabaseGateway $supabase) {
    if ($redirect = guardRole('admin')) {
        return $redirect;
    }

    $data = $request->validate([
        'driver_id' => ['required', 'string'],
        'receipts' => ['required', 'array', 'min:1'],
        'receipts.*' => ['required', 'string'],
        'note' => ['nullable', 'string', 'max:500'],
    ]);

    try {
        $supabase->assignPackages($data['receipts'], $data['driver_id'], session('auth_id'), $data['note'] ?? null);
    } catch (\Throwable $error) {
        return back()->withErrors(['driver_id' => $error->getMessage()])->withInput();
    }

    return redirect()->route('admin.assignments')->with('status', 'Paket berhasil diassign ke driver.');
})->name('admin.assignments.store');

Route::get('/admin/proofs', function (SupabaseGateway $supabase) {
    if ($redirect = guardRole('admin')) {
        return $redirect;
    }

    return view('admin.proofs', [
        'packages' => array_filter($supabase->packages(), fn (array $package): bool => $package['proof'] !== null),
    ]);
})->name('admin.proofs');

Route::get('/driver', function (SupabaseGateway $supabase) {
    if ($redirect = guardRole('driver')) {
        return $redirect;
    }

    return view('driver.index', [
        'packages' => $supabase->driverPackages(session('auth_id')),
    ]);
})->name('driver.index');

Route::get('/driver/proof/{receipt}', function (string $receipt, SupabaseGateway $supabase) {
    if ($redirect = guardRole('driver')) {
        return $redirect;
    }

    $package = collect($supabase->driverPackages(session('auth_id')))->firstWhere('receipt', strtoupper($receipt));

    abort_if(! $package, 404);

    return view('driver.proof', ['package' => $package]);
})->name('driver.proof');

Route::post('/driver/proof/{receipt}', function (string $receipt, Request $request, SupabaseGateway $supabase) {
    if ($redirect = guardRole('driver')) {
        return $redirect;
    }

    $data = $request->validate([
        'photo' => ['required', 'image', 'max:4096'],
        'delivered_at' => ['required', 'date'],
        'delivered_location' => ['required', 'string', 'max:160'],
        'latitude' => ['nullable', 'numeric'],
        'longitude' => ['nullable', 'numeric'],
        'note' => ['nullable', 'string', 'max:500'],
    ]);

    try {
        $supabase->submitProof($receipt, session('auth_id'), $data + ['photo_url' => ''], $request->file('photo'));
    } catch (\Throwable $error) {
        return back()->withErrors(['photo' => $error->getMessage()])->withInput();
    }

    return redirect()->route('driver.index')->with('status', 'Bukti berhasil dikirim.');
})->name('driver.proof.store');
