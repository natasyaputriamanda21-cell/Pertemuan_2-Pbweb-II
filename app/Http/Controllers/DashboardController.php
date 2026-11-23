<?php

namespace App\Http\Controllers;

// Mengambil Model Book.
use App\Models\Book;

// Mengambil Model Loan.
use App\Models\Loan;

// Mengambil Model Member.
use App\Models\Member;

class DashboardController extends Controller
{
    /**
     * Menyiapkan data untuk Dashboard.
     */
    public function index()
    {
        // Menghitung seluruh data buku.
        $totalBuku = Book::count();

        // Menghitung seluruh stok buku.
        $totalStok = (int) Book::sum('stok');

        // Menghitung seluruh anggota.
        $totalAnggota = Member::count();

        // Menghitung peminjaman yang belum dikembalikan.
        $sedangDipinjam = Loan::where(
            'status',
            'dipinjam'
        )->count();

        // Menghitung peminjaman yang sudah terlambat.
        $terlambat = Loan::where('status', 'dipinjam')
            ->where(
                'tanggal_kembali_rencana',
                '<',
                today()->toDateString()
            )
            ->count();

        // Menghitung seluruh denda.
        $totalDenda = (int) Loan::sum('denda');

        // Daftar nama bulan.
        $namaBulan = [
            'Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun',
            'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'
        ];

        // Menyiapkan array label grafik.
        $label = [];

        // Menyiapkan array jumlah peminjaman.
        $jumlah = [];

        // Mengambil data enam bulan terakhir.
        for ($i = 5; $i >= 0; $i--) {

            // Mengambil tanggal awal bulan.
            $awal = today()
                ->startOfMonth()
                ->subMonths($i);

            // Mengambil tanggal akhir bulan.
            $akhir = $awal->copy()->endOfMonth();

            // Menambahkan nama bulan pada label.
            $label[] = $namaBulan[$awal->month - 1]
                .' '.$awal->year;

            // Menghitung jumlah peminjaman pada bulan tersebut.
            $jumlah[] = Loan::whereBetween(
                'tanggal_pinjam',
                [
                    $awal->toDateString(),
                    $akhir->toDateString(),
                ]
            )->count();
        }

        // Mengirim data ke halaman Dashboard.vue.
        return inertia('Dashboard', [

            // Data statistik untuk kartu Dashboard.
            'statistik' => [

                'total_buku' => $totalBuku,

                'total_stok' => $totalStok,

                'total_anggota' => $totalAnggota,

                'sedang_dipinjam' => $sedangDipinjam,

                'terlambat' => $terlambat,

                'total_denda' => $totalDenda,

            ],

            // Data grafik enam bulan.
            'grafik' => [

                'label' => $label,

                'jumlah' => $jumlah,

            ],

        ]);
    }
}