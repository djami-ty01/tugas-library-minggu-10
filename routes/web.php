<?php

use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\FileUploadController; // <-- PERBAIKAN 1: Impor Controller Upload
use App\Http\Controllers\LoginController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\StrukController;
use App\Models\Product;
use Illuminate\Support\Facades\Route;

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

    // <-- PERBAIKAN 2: Ditambahkan ; di akhir baris & ditambahkan Route POST untuk proses upload
    Route::get('/upload', [FileUploadController::class, 'create'])->name('file.upload.form');
    Route::post('/upload', [FileUploadController::class, 'upload'])->name('file.upload');

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

    Route::get('/orders/{order}/invoice', [StrukController::class, 'invoice'])
        ->name('orders.invoice');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::post('/logout', [LoginController::class, 'logout'])
        ->name('logout');

    // PERBAIKAN 3: Route login yang duplikat di bagian bawah ini sudah dihapus
});

require __DIR__.'/auth.php';