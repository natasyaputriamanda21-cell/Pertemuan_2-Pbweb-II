<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Models\Loan;
use App\Models\Member;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class LoanController extends Controller
{
    /**
     * Menampilkan daftar peminjaman.
     */
    public function index()
    {
        // Mengambil data peminjaman beserta anggota dan buku
        $loans = Loan::with(['member', 'book'])
            ->orderByDesc('id')
            ->paginate(10);

        return inertia('Loans/Index', [
            'loans' => $loans,
        ]);
    }

    /**
     * Menampilkan form peminjaman.
     */
    public function create()
    {
        // Mengambil seluruh anggota
        $members = Member::orderBy('nama')
            ->get(['id', 'nama', 'nis_nim']);

        // Mengambil buku yang stoknya masih tersedia
        $books = Book::where('stok', '>', 0)
            ->orderBy('judul')
            ->get(['id', 'judul', 'stok']);

        return inertia('Loans/Create', [
            'members' => $members,
            'books' => $books,
        ]);
    }

    /**
     * Menyimpan transaksi peminjaman.
     */
    public function store(Request $request)
    {
        // 1. Validasi anggota dan buku
        $validated = $request->validate([
            'member_id' => 'required|exists:members,id',
            'book_id' => 'required|exists:books,id',
        ], [
            'member_id.required' => 'Anggota wajib dipilih.',
            'member_id.exists' => 'Anggota yang dipilih tidak ditemukan.',
            'book_id.required' => 'Buku wajib dipilih.',
            'book_id.exists' => 'Buku yang dipilih tidak ditemukan.',
        ]);

        // 2. Memeriksa stok buku di server
        $book = Book::findOrFail($validated['book_id']);

        if ($book->stok < 1) {
            return back()->withErrors([
                'book_id' => 'Stok buku ini sudah habis. Pilih buku lain.',
            ]);
        }

        // Memeriksa jumlah buku yang sedang dipinjam anggota
$jumlahPinjaman = Loan::where(
    'member_id',
    $validated['member_id']
)
->where('status', 'dipinjam')
->count();

// Membatasi maksimal 3 buku untuk satu anggota
if ($jumlahPinjaman >= 3) {
    return back()->withErrors([
        'member_id' => 'Anggota sudah meminjam maksimal 3 buku.',
    ]);
}

        // 3. Menyimpan peminjaman dan mengurangi stok
        DB::transaction(function () use ($validated, $book) {

            Loan::create([
                'member_id' => $validated['member_id'],
                'book_id' => $validated['book_id'],
                'tanggal_pinjam' => now()->toDateString(),
                'tanggal_kembali_rencana' => now()->addDays(14)->toDateString(),
                'status' => 'dipinjam',
            ]);

            // Mengurangi stok buku sebanyak 1
            $book->decrement('stok');

        });

        // 4. Kembali ke halaman daftar peminjaman
        return redirect()->route('loans.index')
            ->with('success', 'Peminjaman berhasil disimpan. Stok buku berkurang 1.');
    }
    /**
 * Memproses pengembalian buku dan menghitung denda.
 */
public function kembalikan(Loan $loan)
{
    // 1. Memeriksa apakah buku sudah dikembalikan.
    if ($loan->status === 'dikembalikan') {

        // Jika sudah, proses ditolak.
        return back()->withErrors([
            'loan' => 'Buku ini sudah dikembalikan sebelumnya.',
        ]);
    }

    // 2. Menghitung keterlambatan sebelum data diubah.
    $hariTerlambat = $loan->hari_terlambat;

    // Mengambil jumlah denda.
    $denda = $loan->perkiraan_denda;

    // 3. Menjalankan perubahan secara bersamaan.
    DB::transaction(function () use ($loan, $denda) {

        // Mengubah status menjadi dikembalikan.
        $loan->update([
            'status' => 'dikembalikan',

            // Mencatat tanggal pengembalian.
            'tanggal_kembali' => today()->toDateString(),

            // Menyimpan jumlah denda.
            'denda' => $denda,
        ]);

        // Menambahkan stok buku sebanyak satu.
        $loan->book->increment('stok');

    });

    // 4. Membuat pesan keberhasilan.
    $pesan = 'Buku berhasil dikembalikan. Stok buku bertambah 1.';

    // Jika terlambat, tambahkan informasi denda.
    if ($denda > 0) {

        $pesan .= ' Terlambat '.$hariTerlambat.' Hari, denda Rp'
            .number_format($denda, 0, ',', '.').'.';
    }

    // 5. Kembali ke halaman sebelumnya.
    return back()->with('success', $pesan);
}
}