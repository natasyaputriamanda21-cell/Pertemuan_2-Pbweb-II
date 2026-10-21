resources/js/Pages/Books/Create.vue 
<script setup> 
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'; 
import { useForm } from '@inertiajs/vue3'; 
// useForm membuat sebuah objek form yang otomatis mengelola nilai input dan pesan error 
const form = useForm({ 
kode_buku: '', 
judul: '', 
pengarang: '', 
penerbit: '',
 tahun: '', 
    stok: 0, 
}); 
  
// Dipanggil ketika tombol submit diklik 
function submit() { 
 // Mengirim data form ke route books.store, yaitu method store() pada BookController(Langkah 5) 

    form.post(route('books.store')); 
} 
</script> 
  
```vue
```vue
<template>
    <AuthenticatedLayout>
        <div class="p-6">
            <h1 class="text-xl font-bold mb-6">Tambah Buku</h1>

            <form @submit.prevent="submit">
                <div class="flex flex-nowrap items-end gap-3 overflow-x-auto pb-3">

                    <div class="flex flex-col min-w-[130px]">
                        <label class="mb-2">Kode Buku</label>
                        <input v-model="form.kode_buku" type="text"
                            class="border border-gray-300 rounded px-3 py-2 w-full">
                        <span v-if="form.errors.kode_buku" class="text-red-500 text-sm">
                            {{ form.errors.kode_buku }}
                        </span>
                    </div>

                    <div class="flex flex-col min-w-[150px]">
                        <label class="mb-2">Judul</label>
                        <input v-model="form.judul" type="text"
                            class="border border-gray-300 rounded px-3 py-2 w-full">
                        <span v-if="form.errors.judul" class="text-red-500 text-sm">
                            {{ form.errors.judul }}
                        </span>
                    </div>

                    <div class="flex flex-col min-w-[130px]">
                        <label class="mb-2">Pengarang</label>
                        <input v-model="form.pengarang" type="text"
                            class="border border-gray-300 rounded px-3 py-2 w-full">
                        <span v-if="form.errors.pengarang" class="text-red-500 text-sm">
                            {{ form.errors.pengarang }}
                        </span>
                    </div>

                    <div class="flex flex-col min-w-[130px]">
                        <label class="mb-2">Penerbit</label>
                        <input v-model="form.penerbit" type="text"
                            class="border border-gray-300 rounded px-3 py-2 w-full">
                    </div>

                    <div class="flex flex-col min-w-[90px]">
                        <label class="mb-2">Tahun</label>
                        <input v-model="form.tahun" type="text"
                            class="border border-gray-300 rounded px-3 py-2 w-full">
                    </div>

                    <div class="flex flex-col min-w-[80px]">
                        <label class="mb-2">Stok</label>
                        <input v-model="form.stok" type="number"
                            class="border border-gray-300 rounded px-3 py-2 w-full">
                    </div>

                    <button type="submit"
                        class="bg-blue-600 hover:bg-blue-700 text-white rounded px-4 py-2 whitespace-nowrap">
                        Simpan
                    </button>

                </div>
            </form>
        </div>
    </AuthenticatedLayout>
</template>
```