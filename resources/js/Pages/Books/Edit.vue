
<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { useForm } from '@inertiajs/vue3';

const props = defineProps({
    book: Object,
});

const form = useForm({
    kode_buku: props.book.kode_buku ?? '',
    judul: props.book.judul ?? '',
    pengarang: props.book.pengarang ?? '',
    penerbit: props.book.penerbit ?? '',
    tahun: props.book.tahun ?? '',
    stok: props.book.stok ?? 0,
});

function submit() {
    form.put(route('books.update', props.book.id));
}
</script>

<template>
    <AuthenticatedLayout>
        <div class="p-5">
            <h2 class="mb-5 text-base font-semibold text-gray-900">
                Edit Buku
            </h2>

            <form @submit.prevent="submit">
                <div class="flex items-end gap-2">
                    <div class="min-w-0 flex-1">
                        <label class="mb-2 block text-sm text-gray-900">
                            Kode Buku
                        </label>
                        <input v-model="form.kode_buku" type="text"
                            class="w-full rounded border border-gray-300 px-2 py-2">
                        <p v-if="form.errors.kode_buku" class="text-xs text-red-600">
                            {{ form.errors.kode_buku }}
                        </p>
                    </div>

                    <div class="min-w-0 flex-1">
                        <label class="mb-2 block text-sm text-gray-900">
                            Judul
                        </label>
                        <input v-model="form.judul" type="text"
                            class="w-full rounded border border-gray-300 px-2 py-2">
                    </div>

                    <div class="min-w-0 flex-1">
                        <label class="mb-2 block text-sm text-gray-900">
                            Pengarang
                        </label>
                        <input v-model="form.pengarang" type="text"
                            class="w-full rounded border border-gray-300 px-2 py-2">
                    </div>

                    <div class="min-w-0 flex-1">
                        <label class="mb-2 block text-sm text-gray-900">
                            Penerbit
                        </label>
                        <input v-model="form.penerbit" type="text"
                            class="w-full rounded border border-gray-300 px-2 py-2">
                    </div>

                    <div class="min-w-0 flex-1">
                        <label class="mb-2 block text-sm text-gray-900">
                            Tahun
                        </label>
                        <input v-model="form.tahun" type="number"
                            class="w-full rounded border border-gray-300 px-2 py-2">
                    </div>

                    <div class="min-w-0 flex-1">
                        <label class="mb-2 block text-sm text-gray-900">
                            Stok
                        </label>
                        <input v-model="form.stok" type="number"
                            class="w-full rounded border border-gray-300 px-2 py-2">
                    </div>

                    <div class="shrink-0">
                       
                    </div>
                    <button type="submit">Simpan Perubahan</button>
                </div>
            </form>
        </div>
    </AuthenticatedLayout>
</template>
