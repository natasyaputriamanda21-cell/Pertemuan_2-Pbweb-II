
<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Link, router } from '@inertiajs/vue3';
import { ref } from 'vue';

// Data buku dan kata pencarian dari controller
const props = defineProps({
    books: Object,
    search: {
        type: String,
        default: '',
    },
});

// Nilai awal kotak pencarian
const keyword = ref(props.search || '');

// Function untuk mencari buku
function cari() {
    router.get('/books', {
        search: keyword.value,
    }, {
        preserveState: true,
        preserveScroll: true,
    });
}

// Function untuk menghapus buku
function hapus(id) {
    if (confirm('Yakin ingin menghapus buku ini?')) {
        router.delete(route('books.destroy', id));
    }
}
</script>

<template>
    <AuthenticatedLayout>
        <h1>Data Buku</h1>

        <!-- Flash message berhasil -->
        <div v-if="$page.props.flash?.success">
            {{ $page.props.flash.success }}
        </div>

        <!-- Flash message gagal -->
        <div v-if="$page.props.flash?.error">
            {{ $page.props.flash.error }}
        </div>

        <!-- Tombol tambah dan kotak pencarian -->
        <div style="display: flex; gap: 10px; margin-bottom: 15px; align-items: center; flex-wrap: wrap;">
            <Link href="/books/create">
                + Tambah Buku
            </Link>

            <form @submit.prevent="cari">
                <input
                    v-model="keyword"
                    type="text"
                    placeholder="Cari kode, judul, atau pengarang..."
                    style="border: 1px solid #999; padding: 6px 10px; width: 280px;"
                />

                <button
                    type="submit"
                    style="margin-left: 5px;"
                >
                    Cari
                </button>
            </form>
        </div>

        <!-- Tabel data buku -->
        <table>
            <thead>
                <tr>
                    <th>Kode</th>
                    <th>Judul</th>
                    <th>Pengarang</th>
                    <th>Tahun</th>
                    <th>Stok</th>
                    <th>Aksi</th>
                </tr>
            </thead>

            <tbody>
                <tr
                    v-for="book in (props.books?.data || [])"
                    :key="book.id"
                >
                    <td>{{ book.kode_buku }}</td>
                    <td>{{ book.judul }}</td>
                    <td>{{ book.pengarang }}</td>
                    <td>{{ book.tahun }}</td>
                    <td>{{ book.stok }}</td>

                    <td>
                        <!-- Tombol edit -->
                        <Link :href="`/books/${book.id}/edit`">
                            Edit
                        </Link>

                        <!-- Tombol hapus -->
                        <button
                            type="button"
                            @click="hapus(book.id)"
                        >
                            Hapus
                        </button>
                    </td>
                </tr>

                <!-- Tampilan jika data kosong -->
                <tr v-if="(props.books?.data || []).length === 0">
                    <td colspan="6">
                        Data buku tidak ditemukan.
                    </td>
                </tr>
            </tbody>
        </table>
    </AuthenticatedLayout>
</template>
