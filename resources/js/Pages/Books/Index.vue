
<script setup>
// Mengambil layout dashboard yang sudah dibuat pada Pertemuan 5 (lengkap dengan Sidebar)
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
const pesanError = '';

// Timer untuk debounce dan pembatal permintaan Axios
let timer = null;
let permintaan = null;

// Data yang ditampilkan di tabel
const daftarBuku = computed(() => hasilCari.value ?? props.books.data);

// Mengirim permintaan AJAX ke /books/search
async function ambilHasil() {
    // Batalkan permintaan sebelumnya
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
        if (axios.isCancel(error)) {
            return;
        }

        sedangMencari.value = false;
        pesanError.value = 'Pencarian gagal. Muat ulang halaman, lalu coba lagi.';
    }
}

// Pencarian otomatis setiap kali isi kotak pencarian berubah
watch(keyword, (nilai) => {
    clearTimeout(timer);

    if (nilai.trim() === '') {
        if (permintaan) {
            permintaan.abort();
        }

        hasilCari.value = null;
        sedangMencari.value = false;
        pesanError.value = '';
        return;
    }

    timer = setTimeout(ambilHasil, 300);
});

// Function hapus buku
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

// Saat halaman ditinggalkan
onBeforeUnmount(() => {
    clearTimeout(timer);

    if (permintaan) {
        permintaan.abort();
    }
});
</script>

<template>
    <AuthenticatedLayout>
        <div class="p-4">
            <h1 class="text-xl font-bold">Data Buku</h1>

            <!-- Flash message -->
            <div v-if="$page.props.flash.success" class="my-2 text-green-700">
                {{ $page.props.flash.success }}
            </div>

            <div class="my-3 flex items-center gap-3">
                <!-- Tombol tambah buku -->
                <Link href="/books/create">
                    + Tambah Buku
                </Link>

                <!-- Pencarian buku -->
                <input
                    v-model="keyword"
                    type="text"
                    placeholder="Ketik judul atau pengarang..."
                >

                <!-- Status pencarian -->
                <span v-if="sedangMencari" class="text-gray-500">
                    Mencari...
                </span>
            </div>

            <!-- Pesan error -->
            <div v-if="pesanError" class="my-2 text-red-600">
                {{ pesanError }}
            </div>

            <!-- Tabel buku -->
            <table class="border-collapse">
                <thead>
                    <tr>
                        <th class="border px-3 py-2">Kode</th>
                        <th class="border px-3 py-2">Judul</th>
                        <th class="border px-3 py-2">Pengarang</th>
                        <th class="border px-3 py-2">Stok</th>
                        <th class="border px-3 py-2">Aksi</th>
                    </tr>
                </thead>

                <tbody>
                    <tr
                        v-for="book in daftarBuku"
                        :key="book.id"
                    >
                        <td class="border px-3 py-2">
                            {{ book.kode_buku }}
                        </td>

                        <td class="border px-3 py-2">
                            {{ book.judul }}
                        </td>

                        <td class="border px-3 py-2">
                            {{ book.pengarang }}
                        </td>

                        <td class="border px-3 py-2">
                            {{ book.stok }}
                        </td>

                        <td class="border px-3 py-2">
                            <Link
                                :href="`/books/${book.id}/edit`"
                                class="mr-3"
                            >
                                Edit
                            </Link>

                            <button @click="hapus(book.id)">
                                Hapus
                            </button>
                        </td>
                    </tr>

                    <!-- Jika data tidak ditemukan -->
                    <tr v-if="daftarBuku.length === 0">
                        <td colspan="5" class="border px-3 py-2">
                            Tidak ada data yang ditemukan.
                        </td>
                    </tr>
                </tbody>
            </table>

            <!-- Pagination -->
            <div
                v-if="hasilCari === null"
                class="mt-3 flex gap-3"
            >
                <template
                    v-for="link in books.links"
                    :key="link.label"
                >
                    <Link
                        v-if="link.url"
                        :href="link.url"
                        v-html="link.label"
                    />

                    <span
                        v-else
                        v-html="link.label"
                        class="text-gray-400"
                    />
                </template>
            </div>
        </div>
    </AuthenticatedLayout>
</template>