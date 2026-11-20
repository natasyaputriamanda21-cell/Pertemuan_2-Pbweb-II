<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { useForm } from '@inertiajs/vue3';
import axios from 'axios';
import { ref, watch, onBeforeUnmount } from 'vue';

// useForm membuat objek form yang mengelola nilai input dan pesan error
const form = useForm({
    kode_buku: '',
    judul: '',
    pengarang: '',
    penerbit: '',
    tahun: '',
    stok: 0,
});

// Status validasi realtime kode buku
const statusKode = ref('');

let timerKode = null;
let permintaanKode = null;

// Memeriksa apakah kode buku sudah digunakan
async function periksaKode() {

    if (permintaanKode) {
        permintaanKode.abort();
    }

    const controller = new AbortController();
    permintaanKode = controller;

    statusKode.value = 'memeriksa';

    try {

        const respons = await axios.get(route('books.cek-kode'), {
            params: {
                kode_buku: form.kode_buku.trim(),
            },
            signal: controller.signal,
        });

        // Menghindari hasil permintaan lama digunakan
        if (permintaanKode !== controller) {
            return;
        }

        statusKode.value = respons.data.tersedia
            ? 'tersedia'
            : 'terpakai';

    } catch (error) {

        if (axios.isCancel(error) || error.code === 'ERR_CANCELED') {
            return;
        }

        statusKode.value = 'gagal';
    }
}

// Memeriksa kode buku setiap kali pengguna mengetik
watch(() => form.kode_buku, (nilai) => {

    clearTimeout(timerKode);

    if (permintaanKode) {
        permintaanKode.abort();
        permintaanKode = null;
    }

    if (!nilai.trim()) {
        statusKode.value = '';
        return;
    }

    statusKode.value = 'menunggu';

    // Memberikan jeda sebelum pemeriksaan kode buku
    timerKode = setTimeout(() => {
        periksaKode();
    }, 500);

});

// Membersihkan timer dan permintaan ketika halaman ditinggalkan
onBeforeUnmount(() => {
    clearTimeout(timerKode);

    if (permintaanKode) {
        permintaanKode.abort();
    }
});

// Dipanggil ketika tombol submit diklik
function submit() {
    form.post(route('books.store'));
}
</script>

<template>
    <AuthenticatedLayout>
        <div class="p-6">
            <h1 class="text-xl font-bold mb-6">Tambah Buku</h1>

            <form @submit.prevent="submit">
                <div class="flex flex-nowrap items-end gap-3 overflow-x-auto pb-3">

                    <div class="flex flex-col min-w-[130px]">
                        <label class="mb-2">Kode Buku</label>

                        <input
                            v-model="form.kode_buku"
                            type="text"
                            class="border border-gray-300 rounded px-3 py-2 w-full"
                        >

                        <!-- Pesan validasi realtime -->
                        <span v-if="statusKode === 'menunggu'"
                            class="text-gray-500 text-sm">
                            Menunggu pemeriksaan...
                        </span>

                        <span v-else-if="statusKode === 'memeriksa'"
                            class="text-blue-600 text-sm">
                            Memeriksa kode buku...
                        </span>

                        <span v-else-if="statusKode === 'tersedia'"
                            class="text-green-600 text-sm">
                            Kode buku tersedia.
                        </span>

                        <span v-else-if="statusKode === 'terpakai'"
                            class="text-red-500 text-sm">
                            Kode buku sudah digunakan.
                        </span>

                        <span v-else-if="statusKode === 'gagal'"
                            class="text-yellow-600 text-sm">
                            Pemeriksaan kode buku gagal.
                        </span>

                        <span v-if="form.errors.kode_buku"
                            class="text-red-500 text-sm">
                            {{ form.errors.kode_buku }}
                        </span>
                    </div>

                    <div class="flex flex-col min-w-[150px]">
                        <label class="mb-2">Judul</label>

                        <input
                            v-model="form.judul"
                            type="text"
                            class="border border-gray-300 rounded px-3 py-2 w-full"
                        >

                        <span v-if="form.errors.judul"
                            class="text-red-500 text-sm">
                            {{ form.errors.judul }}
                        </span>
                    </div>

                    <div class="flex flex-col min-w-[130px]">
                        <label class="mb-2">Pengarang</label>

                        <input
                            v-model="form.pengarang"
                            type="text"
                            class="border border-gray-300 rounded px-3 py-2 w-full"
                        >

                        <span v-if="form.errors.pengarang"
                            class="text-red-500 text-sm">
                            {{ form.errors.pengarang }}
                        </span>
                    </div>

                    <div class="flex flex-col min-w-[130px]">
                        <label class="mb-2">Penerbit</label>

                        <input
                            v-model="form.penerbit"
                            type="text"
                            class="border border-gray-300 rounded px-3 py-2 w-full"
                        >
                    </div>

                    <div class="flex flex-col min-w-[90px]">
                        <label class="mb-2">Tahun</label>

                        <input
                            v-model="form.tahun"
                            type="text"
                            class="border border-gray-300 rounded px-3 py-2 w-full"
                        >
                    </div>

                    <div class="flex flex-col min-w-[80px]">
                        <label class="mb-2">Stok</label>

                        <input
                            v-model="form.stok"
                            type="number"
                            class="border border-gray-300 rounded px-3 py-2 w-full"
                        >
                    </div>

                    <button
                        type="submit"
                        :disabled="form.processing ||
                            (form.kode_buku.trim() !== '' && statusKode !== 'tersedia')"
                        class="bg-blue-600 hover:bg-blue-700 disabled:bg-gray-400 text-white rounded px-4 py-2 whitespace-nowrap"
                    >
                        Simpan
                    </button>

                </div>
            </form>
        </div>
    </AuthenticatedLayout>
</template>
