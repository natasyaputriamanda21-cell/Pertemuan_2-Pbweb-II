<script setup>
// Mengambil ref dari Vue untuk membuat data reaktif.
import { ref } from 'vue';

// Mengambil layout utama.
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';

// Mengambil Link dan router dari Inertia.
import { Link, router } from '@inertiajs/vue3';

// Mengambil data peminjaman dari controller.
defineProps({
    loans: Object,
});

// Mengatur status proses pengembalian.
const memproses = ref(false);

// Mengubah angka menjadi format rupiah.
function formatRupiah(angka) {
    return 'Rp' + Number(angka).toLocaleString('id-ID');
}

// Fungsi ketika tombol Kembalikan ditekan.
function kembalikan(loan) {

    // Menampilkan konfirmasi.
    if (!confirm(
        'Proses pengembalian buku "' + loan.book.judul + '"?'
    )) {
        return;
    }

    // Mengirim permintaan PATCH.
    router.patch(
        route('loans.kembalikan', loan.id),
        {},
        {
            preserveScroll: true,

            // Saat proses dimulai.
            onStart: () => {
                memproses.value = true;
            },

            // Saat proses selesai.
            onFinish: () => {
                memproses.value = false;
            },
        }
    );
}
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

            <!-- Pesan error -->
            <div
                v-if="$page.props.errors.loan"
                class="my-2 text-red-600"
            >
                {{ $page.props.errors.loan }}
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
                            Buku
                        </th>

                        <th class="border px-3 py-2">
                            Tanggal Pinjam
                        </th>

                        <th class="border px-3 py-2">
                            Jatuh Tempo
                        </th>

                        <th class="border px-3 py-2">
                            Tanggal Kembali
                        </th>

                        <th class="border px-3 py-2">
                            Status
                        </th>

                        <th class="border px-3 py-2">
                            Denda
                        </th>

                        <th class="border px-3 py-2">
                            Aksi
                        </th>
                    </tr>
                </thead>

                <tbody>

                    <!-- Menampilkan semua data peminjaman -->
                    <tr
                        v-for="loan in loans.data"
                        :key="loan.id"
                    >

                        <!-- Nama anggota -->
                        <td class="border px-3 py-2">
                            {{ loan.member.nama }}
                        </td>

                        <!-- Judul buku -->
                        <td class="border px-3 py-2">
                            {{ loan.book.judul }}
                        </td>

                        <!-- Tanggal pinjam -->
                        <td class="border whitespace-nowrap px-3 py-2">
                            {{ loan.tanggal_pinjam }}
                        </td>

                        <!-- Tanggal jatuh tempo -->
                        <td class="border whitespace-nowrap px-3 py-2">
                            {{ loan.tanggal_kembali_rencana }}
                        </td>

                        <!-- Tanggal kembali -->
                        <td class="border whitespace-nowrap px-3 py-2">
                            {{ loan.tanggal_kembali || '-' }}
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

                        <!-- Informasi denda -->
                        <td class="border px-3 py-2">

                            <!-- Jika sudah dikembalikan -->
                            <span
                                v-if="loan.status === 'dikembalikan'"
                            >
                                {{ formatRupiah(loan.denda) }}
                            </span>

                            <!-- Jika terlambat -->
                            <span
                                v-else-if="loan.hari_terlambat > 0"
                                class="text-red-600"
                            >
                                Terlambat {{ loan.hari_terlambat }} hari
                                ({{ formatRupiah(loan.perkiraan_denda) }})
                            </span>

                            <!-- Jika belum terlambat -->
                            <span v-else>
                                -
                            </span>

                        </td>

                        <!-- Tombol pengembalian -->
                        <td class="border px-3 py-2">

                            <button
                                v-if="loan.status === 'dipinjam'"
                                type="button"
                                :disabled="memproses"
                                class="text-blue-700 underline"
                                @click="kembalikan(loan)"
                            >
                                Kembalikan
                            </button>

                            <span v-else>
                                -
                            </span>

                        </td>

                    </tr>

                    <!-- Jika data kosong -->
                    <tr v-if="loans.data.length === 0">

                        <td
                            colspan="8"
                            class="border px-3 py-2"
                        >
                            Belum ada data peminjaman.
                        </td>

                    </tr>

                </tbody>

            </table>

            <!-- Navigasi halaman -->
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