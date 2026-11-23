<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { useForm } from '@inertiajs/vue3';

// Menerima daftar anggota dan buku dari LoanController
defineProps({
    members: Array,
    books: Array,
});

// Membuat form peminjaman
const form = useForm({
    member_id: '',
    book_id: '',
});

// Fungsi menyimpan transaksi peminjaman
function submit() {
    form.post(route('loans.store'));
}
</script>

<template>
    <AuthenticatedLayout>
        <div class="p-4">

            <h1 class="text-xl font-bold">
                Pinjam Buku
            </h1>

            <form
                @submit.prevent="submit"
                class="mt-3 space-y-3"
            >

                <!-- Dropdown anggota -->
                <div>

                    <label class="block">
                        Anggota
                    </label>

                    <select v-model="form.member_id">

                        <option value="" disabled>
                            -- Pilih anggota --
                        </option>

                        <option
                            v-for="member in members"
                            :key="member.id"
                            :value="member.id"
                        >
                            {{ member.nama }} ({{ member.nis_nim }})
                        </option>

                    </select>

                    <!-- Pesan error anggota -->
                    <div
                        v-if="form.errors.member_id"
                        class="text-red-600"
                    >
                        {{ form.errors.member_id }}
                    </div>

                </div>

                <!-- Dropdown buku -->
                <div>

                    <label class="block">
                        Buku
                    </label>

                    <select v-model="form.book_id">

                        <option value="" disabled>
                            -- Pilih buku --
                        </option>

                        <option
                            v-for="book in books"
                            :key="book.id"
                            :value="book.id"
                        >
                            {{ book.judul }} (stok: {{ book.stok }})
                        </option>

                    </select>

                    <!-- Pesan error buku -->
                    <div
                        v-if="form.errors.book_id"
                        class="text-red-600"
                    >
                        {{ form.errors.book_id }}
                    </div>

                    <!-- Jika stok buku kosong -->
                    <div
                        v-if="books.length === 0"
                        class="text-yellow-700"
                    >
                        Belum ada buku dengan stok tersedia.
                    </div>

                </div>

                <!-- Keterangan tanggal -->
                <p class="text-gray-500">
                    Tanggal pinjam adalah hari ini.
                    Jatuh tempo otomatis 14 hari setelah tanggal pinjam.
                </p>

                <!-- Tombol simpan -->
                <button
                    type="submit"
                    :disabled="form.processing"
                >
                    Simpan Peminjaman
                </button>

            </form>

        </div>
    </AuthenticatedLayout>
</template>