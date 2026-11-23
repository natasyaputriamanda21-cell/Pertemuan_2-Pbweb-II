<script setup>

// Mengambil fungsi Vue.
import {
    ref,
    onMounted,
    onBeforeUnmount
} from 'vue';

// Mengambil Chart.js.
import Chart from 'chart.js/auto';

// Data yang diterima dari Dashboard.vue.
const props = defineProps({

    // Label nama bulan.
    label: Array,

    // Jumlah peminjaman.
    jumlah: Array,

    // Judul data grafik.
    judul: {
        type: String,
        default: 'Jumlah'
    },

});

// Menghubungkan Vue dengan elemen canvas.
const kanvas = ref(null);

// Menyimpan objek grafik.
let grafik = null;

// Membuat grafik setelah halaman tampil.
onMounted(() => {

    grafik = new Chart(kanvas.value, {

        // Jenis grafik batang.
        type: 'bar',

        data: {

            // Data label pada sumbu X.
            labels: [...props.label],

            datasets: [

                {
                    // Nama data.
                    label: props.judul,

                    // Jumlah peminjaman.
                    data: [...props.jumlah],

                    // Warna grafik.
                    backgroundColor: '#3b82f6',
                },

            ],

        },

        options: {

            // Grafik mengikuti ukuran wadah.
            responsive: true,

            maintainAspectRatio: false,

            plugins: {

                // Menyembunyikan legenda.
                legend: {
                    display: false
                },

            },

            scales: {

                y: {

                    // Sumbu Y dimulai dari nol.
                    beginAtZero: true,

                    // Angka ditampilkan dalam bilangan bulat.
                    ticks: {
                        precision: 0
                    },

                },

            },

        },

    });

});

// Menghapus grafik ketika halaman ditinggalkan.
onBeforeUnmount(() => {

    if (grafik) {
        grafik.destroy();
    }

});

</script>

<template>

    <!-- Wadah grafik -->
    <div class="h-64">

        <!-- Tempat menggambar grafik -->
        <canvas ref="kanvas"></canvas>

    </div>

</template>