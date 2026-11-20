<?php

namespace App\Http\Controllers;

use App\Models\Book;
use Illuminate\Http\Request;
use Inertia\Inertia;

class BookController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');

        $books = Book::when($search, function ($query, $search) {
            $query->where(function ($q) use ($search) {
                $q->where('kode_buku', 'like', "%{$search}%")
                    ->orWhere('judul', 'like', "%{$search}%")
                    ->orWhere('pengarang', 'like', "%{$search}%");
            });
        })->paginate(10)->withQueryString();

        return Inertia::render('Books/Index', [
            'books' => $books,
            'search' => $search,
        ]);
    }

    /**
     * Endpoint JSON untuk live search buku.
     */
    public function search(Request $request)
    {
        // Mengambil kata kunci dari parameter q
        $kataKunci = $request->query('q', '');

        // Mencari berdasarkan kode buku, judul, atau pengarang
        $books = Book::where(function ($query) use ($kataKunci) {
            $query->where('kode_buku', 'like', "%{$kataKunci}%")
                ->orWhere('judul', 'like', "%{$kataKunci}%")
                ->orWhere('pengarang', 'like', "%{$kataKunci}%")
                ->orWhere('penerbit', 'like', "%{$kataKunci}%");
        })
            ->latest()
            ->limit(10)
            ->get([
                'id',
                'kode_buku',
                'judul',
                'pengarang',
                'Penerbit',
                'stok'
            ]);

        // Mengembalikan hasil pencarian dalam bentuk JSON
        return response()->json($books);
    }
    /**
 * Endpoint JSON untuk memeriksa ketersediaan kode buku.
 */
public function cekKode(Request $request)
{
    $kode = $request->query('kode_buku', '');

    $sudahDipakai = Book::where('kode_buku', $kode)
        ->exists();

    return response()->json([
        'tersedia' => ! $sudahDipakai,
    ]);
}

    public function create()
    {
        // Menampilkan halaman tambah buku
        return Inertia::render('Books/Create');
    }

    public function store(Request $request)
    {
        // Validasi data buku
        $validated = $request->validate([
            'kode_buku' => 'required|unique:books,kode_buku',
            'judul' => 'required|max:255',
            'pengarang' => 'required|max:255',
            'penerbit' => 'nullable|max:255',
            'tahun' => 'nullable|digits:4',
            'stok' => 'required|integer|min:0',
        ]);

        // Simpan buku baru
        Book::create($validated);

        return redirect()
            ->route('books.index')
            ->with('success', 'Buku berhasil ditambahkan.');
    }

    public function show(Book $book)
    {
        //
    }

    public function edit(Book $book)
    {
        // Menampilkan form edit dengan data buku
        return Inertia::render('Books/Edit', [
            'book' => $book,
        ]);
    }

    public function update(Request $request, Book $book)
    {
        // Validasi data buku
        $validated = $request->validate([
            'kode_buku' => 'required|unique:books,kode_buku,' . $book->id,
            'judul' => 'required|max:255',
            'pengarang' => 'required|max:255',
            'penerbit' => 'nullable|max:255',
            'tahun' => 'nullable|digits:4',
            'stok' => 'required|integer|min:0',
        ]);

        // Perbarui data buku
        $book->update($validated);

        return redirect()
            ->route('books.index')
            ->with('success', 'Buku berhasil diperbarui.');
    }

    public function destroy(Book $book)
    {
        try {
            $book->delete();

            return redirect()
                ->route('books.index')
                ->with('success', 'Buku berhasil dihapus.');
        } catch (\Throwable $e) {
            return redirect()
                ->route('books.index')
                ->with('error', 'Buku gagal dihapus.');
        }
    }
}
