@extends('layouts.app')

@section('title', $judul)

@section('content')

    <h1>Selamat datang di {{ $judul }}</h1>

    <p>
        Ini adalah halaman utama aplikasi Sistem Informasi
        Peminjaman Buku Perpustakaan Mini.
    </p>

@endsection