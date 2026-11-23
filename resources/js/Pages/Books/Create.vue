<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { useForm } from '@inertiajs/vue3';

// Membuat form untuk menyimpan data buku
const form = useForm({
    kode_buku: '',
    judul: '',
    pengarang: '',
    penerbit: '',
    tahun: '',
    stok: 0,
});

// Fungsi untuk menyimpan buku
function submit() {
    form.post(route('books.store'));
}
</script>

<template>
    <AuthenticatedLayout>
        <div class="p-4">

            <h1 class="text-xl font-bold">
                Tambah Buku
            </h1>

            <form
                @submit.prevent="submit"
                class="mt-3 space-y-3"
            >

                <div>
                    <label class="block">Kode Buku</label>

                    <input
                        v-model="form.kode_buku"
                        type="text"
                    >

                    <div
                        v-if="form.errors.kode_buku"
                        class="text-red-600"
                    >
                        {{ form.errors.kode_buku }}
                    </div>
                </div>

                <div>
                    <label class="block">Judul</label>

                    <input
                        v-model="form.judul"
                        type="text"
                    >

                    <div
                        v-if="form.errors.judul"
                        class="text-red-600"
                    >
                        {{ form.errors.judul }}
                    </div>
                </div>

                <div>
                    <label class="block">Pengarang</label>

                    <input
                        v-model="form.pengarang"
                        type="text"
                    >

                    <div
                        v-if="form.errors.pengarang"
                        class="text-red-600"
                    >
                        {{ form.errors.pengarang }}
                    </div>
                </div>

                <div>
                    <label class="block">Penerbit</label>

                    <input
                        v-model="form.penerbit"
                        type="text"
                    >

                    <div
                        v-if="form.errors.penerbit"
                        class="text-red-600"
                    >
                        {{ form.errors.penerbit }}
                    </div>
                </div>

                <div>
                    <label class="block">Tahun</label>

                    <input
                        v-model="form.tahun"
                        type="text"
                    >

                    <div
                        v-if="form.errors.tahun"
                        class="text-red-600"
                    >
                        {{ form.errors.tahun }}
                    </div>
                </div>

                <div>
                    <label class="block">Stok</label>

                    <input
                        v-model="form.stok"
                        type="number"
                    >

                    <div
                        v-if="form.errors.stok"
                        class="text-red-600"
                    >
                        {{ form.errors.stok }}
                    </div>
                </div>

                <button
                    type="submit"
                    :disabled="form.processing"
                >
                    Simpan
                </button>

            </form>
        </div>
    </AuthenticatedLayout>
</template>