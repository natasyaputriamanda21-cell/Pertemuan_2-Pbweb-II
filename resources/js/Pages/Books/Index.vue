
<script setup>
// Mengambil layout dashboard yang sudah dibuat pada Pertemuan 5
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Link, router } from '@inertiajs/vue3';
import { ref, computed, watch, onBeforeUnmount } from 'vue';
import axios from 'axios';

const props = defineProps({
    books: Object,
    search: String,
});

// Isi kotak pencarian
const keyword = ref(props.search ?? '');

// Hasil pencarian dari server
const hasilCari = ref(null);
const sedangMencari = ref(false);
const pesanError = ref('');

// Timer debounce dan pembatal permintaan
let timer = null;
let permintaan = null;

// Data yang ditampilkan di tabel
const daftarBuku = computed(() => hasilCari.value ?? props.books.data);

// Mengirim permintaan AJAX ke /books/search
async function ambilHasil() {
    if (permintaan) {
        permintaan.abort();
    }

    permintaan = new AbortController();

    sedangMencari.value = true;
    pesanError.value = '';

    try {
        const respons = await axios.get(route('books.search'), {
            params: { q: keyword.value.trim() },
            signal: permintaan.signal,
        });

        hasilCari.value = respons.data;
        sedangMencari.value = false;

    } catch (error) {
        if (axios.isCancel(error) || error.code === 'ERR_CANCELED') {
            return;
        }

        sedangMencari.value = false;
        pesanError.value = 'Pencarian gagal. Muat ulang halaman, lalu coba lagi.';
    }
}

// Dijalankan setiap kali isi kotak pencarian berubah
watch(keyword, (nilai) => {
    clearTimeout(timer);

    // Jika kotak pencarian dikosongkan
    if (nilai.trim() === '') {
        if (permintaan) {
            permintaan.abort();
        }

        hasilCari.value = null;
        sedangMencari.value = false;
        pesanError.value = '';
        return;
    }

    // Tunggu 300 milidetik setelah pengguna berhenti mengetik
    timer = setTimeout(ambilHasil, 300);
});

// Menghapus buku
function hapus(id) {
    if (confirm('Yakin ingin menghapus buku ini?')) {
        router.delete(route('books.destroy', id), {
            onSuccess: () => {
                if (keyword.value.trim() !== '') {
                    ambilHasil();
                }
            },
        });
    }
}

// Menghentikan timer dan permintaan saat halaman ditinggalkan
onBeforeUnmount(() => {
    clearTimeout(timer);

    if (permintaan) {
        permintaan.abort();
    }
});
</script>

<template>
    <AuthenticatedLayout>
        <h1>Data Buku</h1>

        <!-- Flash message -->
        <div
            v-if="$page.props.flash.success"
            class="alert alert-success"
            style="color: red; margin-bottom: 10px;"
        >
            {{ $page.props.flash.success }}
        </div>

        <!-- Link menuju form tambah buku -->
        <Link href="/books/create">+ Tambah Buku</Link>

        <!-- Kotak pencarian -->
        <input
            v-model="keyword"
            type="text"
            placeholder="Ketik judul atau pengarang atau penerbit..."
        >

        <!-- Penanda pencarian -->
        <span v-if="sedangMencari">
            Mencari...
        </span>

        <!-- Pesan jika pencarian gagal -->
        <p v-if="pesanError">
            {{ pesanError }}
        </p>

        <table>
            <thead>
                <tr>
                    <th>Kode</th>
                    <th>Judul</th>
                    <th>Pengarang</th>
                    <th>Stok</th>
                    <th>Aksi</th>
                </tr>
            </thead>

            <tbody>
                <!-- Menampilkan daftar buku -->
                <tr v-for="book in daftarBuku" :key="book.id">
                    <td>{{ book.kode_buku }}</td>
                    <td>{{ book.judul }}</td>
                    <td>{{ book.pengarang }}</td>
                    <td>{{ book.stok }}</td>

                    <td>
                        <!-- Link edit buku -->
                        <Link :href="`/books/${book.id}/edit`">
                            Edit
                        </Link>

                        <!-- Tombol hapus buku -->
                        <button @click="hapus(book.id)">
                            Hapus
                        </button>
                    </td>
                </tr>

                <!-- Pesan jika tidak ada data -->
                <tr v-if="daftarBuku.length === 0">
                    <td colspan="5">
                        Tidak ada buku yang ditemukan.
                    </td>
                </tr>
            </tbody>
        </table>
    </AuthenticatedLayout>
</template>
