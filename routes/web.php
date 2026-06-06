<?php

use App\Http\Controllers\PanganController;
use Illuminate\Support\Facades\Route;

// Halaman Utama (Menampilkan Data)
Route::get('/', [PanganController::class, 'index'])->name('home');

// Proses Autentikasi
Route::post('/register', [PanganController::class, 'register'])->name('register');
Route::post('/login', [PanganController::class, 'login'])->name('login');
Route::post('/logout', [PanganController::class, 'logout'])->name('logout');

// Proses Donasi (Hanya bisa diakses jika sudah login)
Route::middleware(['auth'])->group(function () {
    //Rute untuk HOREKA membuat donasi baru
    Route::post('/donations/store', [PanganController::class, 'storeDonation'])->name('donations.store');

    // Rute untuk Komunitas mengklaim donasi
    Route::post('/donations/claim/{id}', [PanganController::class, 'claimDonation'])->name('donations.claim');
});
