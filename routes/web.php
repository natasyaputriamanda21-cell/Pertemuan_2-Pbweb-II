<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use Inertia\Inertia;
use App\Http\Controllers\BookController;
use App\Http\Controllers\MemberController;
use App\Http\Controllers\LoanController;

Route::get('/', [HomeController::class, 'index']);
Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
});

Route::get('/tentang', [HomeController::class, 'tentang']);
Route::get('/dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::get('/kontak', [HomeController::class, 'kontak']);
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

Route::middleware(['auth'])->group(function(){
   Route::get('/books/search', [BookController::class, 'search'])
        ->name('books.search');
    Route::get('books/cek-kode', [BookController::class, 'cekKode'])
        ->name('books.cek-kode');    
    // Route AJAX pencarian anggota
    Route::get('members/search', [MemberController::class, 'search'])
        ->name('members.search');  
        // Route AJAX pengecekan NIS/NIM
    Route::get('members/cek-nis', [MemberController::class, 'cekNis'])
        ->name('members.cek-nis');
    
 

    Route::resource('books', BookController::class);
    Route::resource('members', MemberController::class);// baris baru: 7 alamat CRUD untuk  anggota
    Route::resource('loans', LoanController::class)
    ->only(['index', 'create', 'store']);
  


}) ;  

});

require __DIR__.'/auth.php';