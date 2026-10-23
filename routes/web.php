<?php

use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ProfileController;
use App\Models\Product;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\StrukController;

Route::get('/', function () {
    return view('welcome');
});

Route::middleware('guest')->group(function () {
    Route::get('/login', function () {
        return view('auth.login');
    })->name('login');

    Route::post('/login-process', [LoginController::class, 'login'])
        ->name('login.process');

    Route::get('/register', [RegisteredUserController::class, 'create'])
        ->name('register');

    Route::post('/register-store', [RegisteredUserController::class, 'store'])
        ->name('register.store');
});

Route::middleware('auth')->group(function () {
    Route::get('/dashboard', function () {
        return match (request()->user()->role) {
            'admin' => redirect()->route('admin'),
            'kasir' => redirect()->route('kasir'),
            default => abort(403, 'Role akun tidak dikenali.'),
        };
    })->name('dashboard');

    Route::get('/admin', function () {
        return view('admin');
    })->middleware('cek.role:admin')->name('admin');

    Route::resource('products', ProductController::class)
        ->middleware('cek.role:admin');

    Route::get('/kasir', function () {
        $products = Product::where('is_active', true)->orderBy('name')->get();

        return view('kasir', compact('products'));
    })->middleware('cek.role:kasir')->name('kasir');

    Route::get('/struk', [StrukController::class, 'index'])
        ->middleware('cek.role:kasir')->name('struk.index');
    Route::post('/struk/cetak', [StrukController::class, 'print'])
        ->middleware('cek.role:kasir')->name('struk.cetak');
    Route::get('/struk/cetak', [StrukController::class, 'index'])
        ->middleware('cek.role:kasir')->name('struk.cetak.form');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::post('/logout', [LoginController::class, 'logout'])
        ->name('logout');

        Route::get('/login', function () {
    return view('welcome'); // atau halaman login Anda
})->name('login');
});

require __DIR__.'/auth.php';