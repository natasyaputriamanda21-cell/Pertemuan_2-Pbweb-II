<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Link } from '@inertiajs/vue3';

// Menerima data peminjaman dari LoanController
defineProps({
    loans: Object,
});
</script>

<template>
    <AuthenticatedLayout>
        <div class="p-4">

            <h1 class="text-xl font-bold">
                Data Peminjaman
            </h1>

            <!-- Pesan sukses -->
            <div
                v-if="$page.props.flash.success"
                class="my-2 text-green-700"
            >
                {{ $page.props.flash.success }}
            </div>

            <!-- Tombol tambah peminjaman -->
            <div class="my-3">
                <Link href="/loans/create">
                    + Pinjam Buku
                </Link>
            </div>

            <!-- Tabel peminjaman -->
            <table class="border-collapse">
                <thead>
                    <tr>
                        <th class="border px-3 py-2">
                            Anggota
                        </th>

                        <th class="border px-3 py-2">
                            Kode Buku
                        </th>

                        <th class="border px-3 py-2">
                            Buku
                        </th>

                        <th class="border px-3 py-2">
                            Tanggal Pinjam
                        </th>

                        <th class="border px-3 py-2">
                            Jatuh Tempo
                        </th>

                        <th class="border px-3 py-2">
                            Status
                        </th>
                    </tr>
                </thead>

                <tbody>

                    <tr
                        v-for="loan in loans.data"
                        :key="loan.id"
                    >

                        <!-- Nama anggota -->
                        <td class="border px-3 py-2">
                            {{ loan.member.nama }}
                        </td>

                        <td class="border px-3 py-2">
                            {{ loan.book.kode_buku }}
                        </td>

                        <!-- Judul buku -->
                        <td class="border px-3 py-2">
                            {{ loan.book.judul }}
                        </td>

                        <!-- Tanggal pinjam -->
                        <td class="border px-3 py-2">
                            {{ loan.tanggal_pinjam }}
                        </td>

                        <!-- Tanggal jatuh tempo -->
                        <td class="border px-3 py-2">
                            {{ loan.tanggal_kembali_rencana }}
                        </td>

                        <!-- Status peminjaman -->
                        <td class="border px-3 py-2">

                            <span
                                :class="loan.status === 'dipinjam'
                                    ? 'text-yellow-700'
                                    : 'text-green-700'"
                            >
                                {{ loan.status }}
                            </span>

                        </td>

                    </tr>

                    <!-- Jika belum ada transaksi -->
                    <tr v-if="loans.data.length === 0">
                        <td
                            colspan="6"
                            class="border px-3 py-2"
                        >
                            Belum ada data peminjaman.
                        </td>
                    </tr>

                </tbody>
            </table>

            <!-- Pagination -->
            <div class="mt-3 flex gap-3">

                <template
                    v-for="link in loans.links"
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