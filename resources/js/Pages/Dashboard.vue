<script setup>

// Mengambil layout utama.
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';

// Mengambil komponen grafik.
import BarChart from '@/Components/BarChart.vue';

// Mengambil Head dari Inertia.
import { Head } from '@inertiajs/vue3';

// Menerima data dari DashboardController.
defineProps({

    // Data statistik.
    statistik: Object,

    // Data grafik.
    grafik: Object,

});

// Mengubah angka menjadi format rupiah.
function formatRupiah(angka) {

    return 'Rp' + Number(angka).toLocaleString('id-ID');

}

</script>

<template>

    <!-- Judul halaman -->
    <Head title="Dashboard" />

    <AuthenticatedLayout>

        <!-- Header Dashboard -->
        <template #header>

            <h2 class="text-xl font-semibold leading-tight text-gray-800">
                Dashboard
            </h2>

        </template>

        <div class="p-4">

            <!-- Susunan empat kartu -->
            <div class="grid grid-cols-1 gap-4 md:grid-cols-2 xl:grid-cols-4">

                <!-- KARTU 1: JUMLAH BUKU -->
                <div
                    class="rounded-lg border-l-4 border-blue-500 bg-white p-5 shadow-sm"
                >

                    <div class="text-sm text-gray-500">
                        Judul Buku
                    </div>

                    <div class="text-3xl font-bold text-gray-800">
                        {{ statistik.total_buku }}
                    </div>

                    <div class="text-xs text-gray-500">
                        Total stok: {{ statistik.total_stok }} eksemplar
                    </div>

                </div>

                <!-- KARTU 2: ANGGOTA -->
                <div
                    class="rounded-lg border-l-4 border-green-500 bg-white p-5 shadow-sm"
                >

                    <div class="text-sm text-gray-500">
                        Anggota
                    </div>

                    <div class="text-3xl font-bold text-gray-800">
                        {{ statistik.total_anggota }}
                    </div>

                    <div class="text-xs text-gray-500">
                        Terdaftar di perpustakaan
                    </div>

                </div>

                <!-- KARTU 3: SEDANG DIPINJAM -->
                <div
                    class="rounded-lg border-l-4 border-yellow-500 bg-white p-5 shadow-sm"
                >

                    <div class="text-sm text-gray-500">
                        Sedang Dipinjam
                    </div>

                    <div class="text-3xl font-bold text-gray-800">
                        {{ statistik.sedang_dipinjam }}
                    </div>

                    <div
                        class="text-xs"
                        :class="statistik.terlambat > 0
                            ? 'text-red-600'
                            : 'text-gray-500'"
                    >

                        {{ statistik.terlambat }} terlambat

                    </div>

                </div>

                <!-- KARTU 4: TOTAL DENDA -->
                <div
                    class="rounded-lg border-l-4 border-red-500 bg-white p-5 shadow-sm"
                >

                    <div class="text-sm text-gray-500">
                        Total Denda
                    </div>

                    <div class="text-3xl font-bold text-gray-800">
                        {{ formatRupiah(statistik.total_denda) }}
                    </div>

                    <div class="text-xs text-gray-500">
                        Dari buku yang sudah dikembalikan
                    </div>

                </div>

            </div>

            <!-- BAGIAN GRAFIK -->
            <div class="mt-4 rounded-lg bg-white p-5 shadow-sm">

                <h3 class="mb-3 font-semibold text-gray-800">
                    Peminjaman 12 Bulan Terakhir
                </h3>

                <!-- Memanggil komponen BarChart -->
                <BarChart
                    :label="grafik.label"
                    :jumlah="grafik.jumlah"
                    judul="Jumlah peminjaman"
                />

            </div>

        </div>

    </AuthenticatedLayout>

</template>