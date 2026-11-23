<?php

namespace App\Models;

// Carbon digunakan untuk menghitung tanggal.
use Illuminate\Support\Carbon;

// Attribute digunakan untuk membuat atribut virtual.
use Illuminate\Database\Eloquent\Casts\Attribute;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Loan extends Model
{
    // Mengaktifkan fitur factory.
    use HasFactory;

    // Tarif denda sebesar Rp1.000 per hari.
    public const DENDA_PER_HARI = 2000;

    // Kolom yang boleh diisi melalui create() dan update().
    protected $fillable = [
        'book_id',
        'member_id',
        'tanggal_pinjam',
        'tanggal_kembali_rencana',
        'tanggal_kembali',
        'status',
        'denda',
    ];

    // Mendaftarkan atribut virtual agar dikirim ke Vue.
    protected $appends = [
        'hari_terlambat',
        'perkiraan_denda'
    ];

    // Relasi peminjaman dengan buku.
    public function book()
    {
        return $this->belongsTo(Book::class);
    }

    // Relasi peminjaman dengan anggota.
    public function member()
    {
        return $this->belongsTo(Member::class);
    }

    /**
     * Menghitung jumlah hari keterlambatan.
     */
    protected function hariTerlambat(): Attribute
    {
        return Attribute::make(get: function () {

            // Mengambil tanggal jatuh tempo.
            $jatuhTempo = Carbon::parse(
                $this->tanggal_kembali_rencana
            )->startOfDay();

            // Jika sudah dikembalikan, gunakan tanggal kembali.
            // Jika belum, gunakan tanggal hari ini.
            $acuan = $this->tanggal_kembali
                ? Carbon::parse($this->tanggal_kembali)->startOfDay()
                : today();

            // Jika belum melewati jatuh tempo, tidak terlambat.
            if ($acuan->lessThanOrEqualTo($jatuhTempo)) {
                return 0;
            }

            // Menghitung selisih hari keterlambatan.
            return (int) $jatuhTempo->diffInDays($acuan);
        });
    }

    /**
     * Menghitung perkiraan denda.
     */
    protected function perkiraanDenda(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->hari_terlambat * self::DENDA_PER_HARI
        );
    }
}