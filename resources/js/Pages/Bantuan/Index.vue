<script setup>
import { ref, onMounted, watch, computed } from "vue";
import AdminLayout from "@/Layouts/AdminLayout.vue";
import TitlePage from "@/Components/TitlePage.vue";
import { Head } from "@inertiajs/vue3";

const openAccordion = ref(null); // Keeps track of the open accordion index

const toggleAccordion = (section) => {
    openAccordion.value = openAccordion.value === section ? null : section;
};
</script>

<template>
    <Head title="Bantuan"></Head>
    <AdminLayout>
        <template #titlePage>
            <TitlePage value="Bantuan" />
        </template>
        <template #content>
            <main class="p-6 bg-gray-100 flex-grow overflow-y-auto">
                <div class="bg-sky-700 p-4 rounded-lg shadow">
                    <div class="sm:col-span-1 text-white text-center">
                        <h1
                            class="tracking-wide block text-md font-bold text-white uppercase"
                        >
                            Panduan lengkap untuk menggunakan web aplikasi Gadai
                            Emas
                        </h1>
                    </div>
                </div>

                <!-- Menu Master Data -->
                <div class="mt-6">
                    <div class="bg-sky-700 p-4 rounded-lg shadow">
                        <div class="sm:col-span-1 text-white">
                            <h1
                                class="tracking-wide block text-md font-bold mb-3 text-white uppercase"
                            >
                                Master Data Menu :
                            </h1>
                            <div
                                @click="toggleAccordion('nasabah')"
                                class="cursor-pointer"
                            >
                                <h1
                                    class="tracking-wide block text-md font-bold mb-1 text-white uppercase"
                                >
                                    * Nasabah <span class="text-md">
                                        <!-- If open, show 'up' icon, otherwise show 'down' icon -->
                                        {{
                                            openAccordion === 'nasabah'
                                                ? "▲"
                                                : "▼"
                                        }}
                                    </span>
                                </h1>
                            </div>
                            <div
                                class="mb-3"
                                v-if="openAccordion === 'nasabah'"
                            >
                                <p class="ml-3">
                                    Panduan menggunakan menu Nasabah, sebagai
                                    berikut
                                </p>
                                <p class="ml-6">
                                    1. Klik tombol tambah yang berada di sebelah
                                    kanan atas
                                </p>
                                <p class="ml-6">
                                    2. Isi form data diri dengan ketentuan yang
                                    telah ditentukan
                                </p>
                                <p class="ml-6">Berikut Ketentuan</p>
                                <p class="ml-9">
                                    - Isian Nomor KTP, wajib 16 digit
                                </p>
                                <p class="ml-9">
                                    - Isian Nomor HP, minimal 11 digit &
                                    maksimal 13 digit
                                </p>
                                <p class="ml-9">
                                    - Isian Nomor Tlp Rumah, minimal 7 digit &
                                    maksimal 8 digit
                                </p>
                                <p class="ml-9">
                                    - Isian Nomor Rekening, minimal 10 digit &
                                    maksimal 16 digit
                                </p>
                                <p class="ml-6">
                                    Jika ketentuan tersebut tidak terpenuhi maka
                                    pengisian form tidak bisa berlanjut
                                </p>
                                <p class="ml-6">
                                    3. Masukan/Upload foto diri dan foto ktp
                                    nasabah
                                </p>
                                <p class="ml-6">
                                    4. Masukan alamat ktp & alamat sekarang
                                    nasabah secara lengkap
                                </p>
                                <p class="ml-6">5. Lanjut ke form kerabat</p>
                                <p class="ml-6">
                                    6. Isi form kerabat secara keseluruhan
                                </p>
                                <p class="ml-6">7. Lanjut ke form rekening</p>
                                <p class="ml-6">Berikut Ketentuan</p>
                                <p class="ml-9">
                                    - Isian Nomor Rekening, minimal 10 digit &
                                    maksimal 16 digit
                                </p>
                                <p class="ml-6">
                                    Jika ketentuan tersebut tidak terpenuhi maka
                                    form tidak bisa disubmit
                                </p>
                                <p class="ml-6">8. Selesai</p>
                                <p class="ml-6">
                                    ** Jika gagal berarti ada form yg belum
                                    terisi atau tidak sesuai ketentuan
                                </p>
                            </div>

                            <div
                                @click="toggleAccordion('stl')"
                                class="cursor-pointer"
                            >
                                <h1
                                    class="tracking-wide block text-md font-bold mb-1 text-white uppercase"
                                >
                                    * STL <span class="text-md">
                                        <!-- If open, show 'up' icon, otherwise show 'down' icon -->
                                        {{
                                            openAccordion === 'stl'
                                                ? "▲"
                                                : "▼"
                                        }}
                                    </span>
                                </h1>
                            </div>
                            <div class="mb-3" v-if="openAccordion === 'stl'">
                                <p class="ml-3">
                                    Panduan menggunakan menu STL, sebagai
                                    berikut
                                </p>
                                <p class="ml-3">(*) Menambah harga STL :</p>
                                <p class="ml-6">
                                    1. Klik tombol tambah yang berada di sebelah
                                    kanan atas
                                </p>
                                <p class="ml-6">
                                    2. Isi form stl dengan harga standard
                                    taksiran logam di dunia
                                </p>
                                <p class="ml-6">
                                    3. Isi tanggal berlakunya harga yang telah
                                    diinput
                                </p>
                                <p class="ml-6">4. Selesai</p>
                                <p class="ml-6">
                                    ** Perlu diingat harga STL maksimal yang
                                    aktif adalah satu, jadi ketika menginput
                                    harga baru pastikan harga lama statusnya
                                    sudah off
                                </p>
                                <p class="ml-3">
                                    (*) Merubah status harga STL :
                                </p>
                                <p class="ml-6">
                                    Klik tombol on/off yang ada di tabel
                                </p>
                            </div>

                            <div
                                @click="toggleAccordion('penaksir')"
                                class="cursor-pointer"
                            >
                                <h1
                                    class="tracking-wide block text-md font-bold mb-1 text-white uppercase"
                                >
                                    * Penaksir <span class="text-md">
                                        <!-- If open, show 'up' icon, otherwise show 'down' icon -->
                                        {{
                                            openAccordion === 'penaksir'
                                                ? "▲"
                                                : "▼"
                                        }}
                                    </span>
                                </h1>
                            </div>
                            <div
                                class="mb-3"
                                v-if="openAccordion === 'penaksir'"
                            >
                                <p class="ml-3">
                                    Panduan menggunakan menu Penaksir, sebagai
                                    berikut
                                </p>
                                <p class="ml-3">(*) Menambah Penaksir :</p>
                                <p class="ml-6">
                                    1. Klik tombol tambah yang berada di sebelah
                                    kanan atas
                                </p>
                                <p class="ml-6">2. Isi form nama</p>
                                <p class="ml-6">
                                    3. Untuk bagian posko dan cabang, sudah auto
                                    generate, tidak bisa diubah
                                </p>
                                <p class="ml-6">4. Selesai</p>
                                <p class="ml-3">(*) Merubah Nama Penaksir :</p>
                                <p class="ml-6">
                                    1. Klik tombol info yang ada di tabel
                                </p>
                                <p class="ml-6">
                                    2. Ubah nama sebelumnya menjadi nama yang
                                    diinginkan
                                </p>
                                <p class="ml-6">3. Kemudian submit</p>
                                <p class="ml-6">4. Selesai</p>
                                <p class="ml-6">
                                    ** Jika nama sebelumnya sama dengan nama
                                    yang sekarang, tombol simpan tidak akan
                                    muncul
                                </p>
                            </div>

                            <div
                                @click="toggleAccordion('komponen_hitung')"
                                class="cursor-pointer"
                            >
                                <h1
                                    class="tracking-wide block text-md font-bold mb-1 text-white uppercase"
                                >
                                    * Komponen Hitung <span class="text-md">
                                        <!-- If open, show 'up' icon, otherwise show 'down' icon -->
                                        {{
                                            openAccordion === 'komponen_hitung'
                                                ? "▲"
                                                : "▼"
                                        }}
                                    </span>
                                </h1>
                            </div>
                            <div
                                class="mb-3"
                                v-if="openAccordion === 'komponen_hitung'"
                            >
                                <p class="ml-3">
                                    Panduan menggunakan menu Komponen Hitung,
                                    sebagai berikut
                                </p>
                                <p class="ml-6">
                                    1. Pilih golongan yang tertera mulai dari A
                                    s/d E
                                </p>
                                <p class="ml-6">
                                    2. Lalu semua form akan terisi data dari
                                    golongan yang dipilih
                                </p>
                                <p class="ml-6">
                                    3. Ubah form/isi yang dinginkan
                                </p>
                                <p class="ml-6">
                                    4. Klik tombol ubah yang berada di pojok
                                    kanan bawah
                                </p>
                                <p class="ml-6">5. Selesai</p>
                            </div>

                            <div
                                @click="toggleAccordion('users')"
                                class="cursor-pointer"
                            >
                                <h1
                                    class="tracking-wide block text-md font-bold text-white uppercase"
                                >
                                    * Users <span class="text-md">
                                        <!-- If open, show 'up' icon, otherwise show 'down' icon -->
                                        {{
                                            openAccordion === 'users'
                                                ? "▲"
                                                : "▼"
                                        }}
                                    </span>
                                </h1>
                            </div>
                            <div class="mb-3" v-if="openAccordion === 'users'">
                                <p class="ml-3">
                                    Panduan menggunakan menu Users, sebagai
                                    berikut
                                </p>
                                <p class="ml-3">(*) Menambah Users :</p>
                                <p class="ml-6">
                                    1. Klik tombol tambah yang berada di sebelah
                                    kanan atas
                                </p>
                                <p class="ml-6">
                                    2. Isi user_id sesuai dengan format yang
                                    telah ditentukan
                                </p>
                                <p class="ml-6">
                                    3. Lalu isi form yang lain dengan benar
                                </p>
                                <p class="ml-6">4. Klik tombol simpan</p>
                                <p class="ml-6">4. Selesai</p>
                                <p class="ml-3">
                                    (*) Merubah Username/Password Users :
                                </p>
                                <p class="ml-6">
                                    1. Klik tombol info yang ada di tabel
                                </p>
                                <p class="ml-6">
                                    2. Ubah username atau password sebelumnya
                                    dengan yang dinginkan
                                </p>
                                <p class="ml-6">3. Kemudian submit</p>
                                <p class="ml-6">4. Selesai</p>
                                <p class="ml-6">
                                    ** Jika username atau password sebelumnya
                                    sama dengan yang sekarang, tombol ubah tidak
                                    akan muncul
                                </p>
                                <p class="ml-3">(*) Merubah Status Users :</p>
                                <p class="ml-6">
                                    Klik tombol on/off yang ada di tabel
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Menu Transaksi -->
                <div class="mt-6">
                    <div class="bg-sky-700 p-4 rounded-lg shadow">
                        <div class="sm:col-span-1 text-white">
                            <h1
                                class="tracking-wide block text-md font-bold mb-3 text-white uppercase"
                            >
                                Transaksi Menu :
                            </h1>
                            <div
                                @click="toggleAccordion('permohonan')"
                                class="cursor-pointer"
                            >
                                <h1
                                    class="tracking-wide block text-md font-bold mb-1 text-white uppercase"
                                >
                                    * Permohonan Gadai <span class="text-md">
                                        <!-- If open, show 'up' icon, otherwise show 'down' icon -->
                                        {{
                                            openAccordion === 'permohonan'
                                                ? "▲"
                                                : "▼"
                                        }}
                                    </span>
                                </h1>
                            </div>
                            <div
                                class="mb-3"
                                v-if="openAccordion === 'permohonan'"
                            >
                                <p class="ml-3">
                                    Panduan menggunakan menu Permohonan Gadai,
                                    sebagai berikut
                                </p>
                                <p class="ml-6">
                                    1. Klik tombol tambah yang berada di sebelah
                                    kanan atas
                                </p>
                                <p class="ml-6">
                                    2. Masukkan No Identitas/KTP Nasabah, lalu
                                    klik tombol cek data nasabah
                                </p>
                                <p class="ml-6">
                                    3. Klik tombol cek kelengkapan data nasabah,
                                    lalu isi semua form di dalam modal tersebut
                                </p>
                                <p class="ml-6">
                                    4. Upload foto identitas/ktp nasabah sesuai
                                    dengan ketentuan
                                </p>
                                <p class="ml-6">5. Klik tombol lanjut</p>
                                <p class="ml-6">6. Pilih penaksir</p>
                                <p class="ml-6">
                                    7. Pilih tipe transaksi sesuai kondisi
                                </p>
                                <p class="ml-6">
                                    8. Klik tombol tambah (untuk barang jaminan)
                                </p>
                                <p class="ml-6">
                                    9. Isi semua form dengan benar dan teliti
                                </p>
                                <p class="ml-6">
                                    10. Upload foto barang jaminan dari kedua
                                    sisi (harus berbeda setiap sisi)
                                </p>
                                <p class="ml-6">
                                    11. Klik tombol simpan, lakukan hal serupa
                                    jika ada dua atau lebih barang jaminan
                                </p>
                                <p class="ml-6">
                                    ** Jika pada saat pertama kali menambah
                                    barang, ketika di simpan barang menjadi 2
                                    atau lebih, klik tombol batal terlebih
                                    dahulu, kemudian input kembali data
                                </p>
                                <p class="ml-6">12. Klik tombol lanjut</p>
                                <p class="ml-6">
                                    13. Pilih tenor yang diinginkan nasabah
                                </p>
                                <p class="ml-6">
                                    14. Masukan nominal uang pinjaman dari limit
                                    maksimal pinjaman sesuai kenginan nasabah,
                                    klik tombol maksimal jika nasabah ingin
                                    meminjam semuanya
                                </p>
                                <p class="ml-6">
                                    15. Jika ingin mengubah sewa modal, klik
                                    tombol ubah, namun dengan persetujuan mancab
                                    & minimal 1%
                                </p>
                                <p class="ml-6">
                                    16. Pilih metode pencairan uang pinjaman
                                </p>
                                <p class="ml-6">
                                    17. Kemudian klik tombol lanjut
                                </p>
                                <p class="ml-6">
                                    18. Tunggu proses memuat surat bukti gadai
                                </p>
                                <p class="ml-6">19. Selesai</p>
                                <p class="ml-6">
                                    ** Jika gagal berarti ada form yg belum
                                    terisi atau tidak sesuai ketentuan
                                </p>
                            </div>

                            <div
                                @click="toggleAccordion('perpanjangan')"
                                class="cursor-pointer"
                            >
                                <h1
                                    class="tracking-wide block text-md font-bold mb-1 text-white uppercase"
                                >
                                    * Perpanjangan <span class="text-md">
                                        <!-- If open, show 'up' icon, otherwise show 'down' icon -->
                                        {{
                                            openAccordion === 'perpanjangan'
                                                ? "▲"
                                                : "▼"
                                        }}
                                    </span>
                                </h1>
                            </div>
                            <div
                                class="mb-3"
                                v-if="openAccordion === 'perpanjangan'"
                            >
                                <p class="ml-3">
                                    Panduan menggunakan menu Perpanjangan,
                                    sebagai berikut
                                </p>
                                <p class="ml-6">
                                    1. Klik tombol extend yang ada di tabel
                                </p>
                                <p class="ml-6">
                                    2. Cek terlebih dahulu data pinjaman, bisa
                                    konfirmasikan ke nasabah
                                </p>
                                <p class="ml-6">
                                    3. Jika sudah, klik tombol lanjut
                                </p>
                                <p class="ml-6">
                                    4. Pilih tenor yang dinginkan nasabah
                                </p>
                                <p class="ml-6">5. Pilih cara bayar</p>
                                <p class="ml-6">6. Pilih jenis pembayaran</p>
                                <p class="ml-6">
                                    7. Upload foto barang jaminan dari kedua
                                    sisi, untuk memberikan rasa aman kepada
                                    nasabah dan menghindari kecurangan
                                </p>
                                <p class="ml-6">8. Klik tombol submit</p>
                                <p class="ml-6">9. Selesai</p>
                            </div>

                            <div
                                @click="toggleAccordion('pelunasan')"
                                class="cursor-pointer"
                            >
                                <h1
                                    class="tracking-wide block text-md font-bold mb-1 text-white uppercase"
                                >
                                    * Pelunasan
                                    <span class="text-md">
                                        <!-- If open, show 'up' icon, otherwise show 'down' icon -->
                                        {{
                                            openAccordion === 'pelunasan'
                                                ? "▲"
                                                : "▼"
                                        }}
                                    </span>
                                </h1>
                            </div>
                            <div
                                class="mb-3"
                                v-if="openAccordion === 'pelunasan'"
                            >
                                <p class="ml-3">
                                    Panduan menggunakan menu Penaksir, sebagai
                                    berikut
                                </p>
                                <p class="ml-6">
                                    1. Klik tombol bayar yang ada di tabel
                                </p>
                                <p class="ml-6">
                                    2. Masukan biaya lain "JIKA ADA"
                                </p>
                                <p class="ml-6">3. Pilih cara bayar</p>
                                <p class="ml-6">4. Pilih jenis pembayaran</p>
                                <p class="ml-6">
                                    5. Infokan kembali kepada nasabah, nominal
                                    total pembayarannya
                                </p>
                                <p class="ml-6">6. Selesai</p>
                            </div>

                            <div
                                @click="toggleAccordion('simul_pelunasan')"
                                class="cursor-pointer"
                            >
                                <h1
                                    class="tracking-wide block text-md font-bold mb-1 text-white uppercase"
                                >
                                    * Simulasi Pelunasan
                                    <span class="text-md">
                                        <!-- If open, show 'up' icon, otherwise show 'down' icon -->
                                        {{
                                            openAccordion === "simul_pelunasan"
                                                ? "▲"
                                                : "▼"
                                        }}
                                    </span>
                                </h1>
                            </div>
                            <div
                                class="mb-3 text-white"
                                v-if="openAccordion === 'simul_pelunasan'"
                            >
                                <p class="ml-3">
                                    Panduan menggunakan menu Simulasi Pelunasan,
                                    sebagai berikut
                                </p>
                                <p class="ml-6">
                                    1. Klik tombol simulasi yang ada di tabel
                                </p>
                                <p class="ml-6">
                                    2. Pilih tanggal pelunasan sesuai kenginan
                                    nasabah
                                </p>
                                <p class="ml-6">
                                    3. Akan muncul data simulasi dan estimasi
                                    nominal total pembayaran
                                </p>
                                <p class="ml-6">4. Selesai</p>
                            </div>

                            <div
                                @click="toggleAccordion('pengajuan-lelang')"
                                class="cursor-pointer"
                            >
                                <h1
                                    class="tracking-wide block text-md font-bold mb-1 text-white uppercase"
                                >
                                    * Pengajuan Lelang
                                    <span class="text-md">
                                        <!-- If open, show 'up' icon, otherwise show 'down' icon -->
                                        {{
                                            openAccordion === 'pengajuan-lelang'
                                                ? "▲"
                                                : "▼"
                                        }}
                                    </span>
                                </h1>
                            </div>
                            <div
                                class="mb-3"
                                v-if="openAccordion === 'pengajuan-lelang'"
                            >
                                <p class="ml-3">
                                    Panduan menggunakan menu Pengajuan Lelang, sebagai
                                    berikut
                                </p>
                                <p class="ml-6">
                                    1. PIlih Nasabah
                                </p>
                                <p class="ml-6">
                                    2. Lalu pilih barang yang ingin dilelang
                                </p>
                                <p class="ml-6">3. Klik tombol lelang pada tabel</p>
                                <p class="ml-6">4. Masukkan nominal lelang & keterangan</p>
                                <p class="ml-6">5. Klik tombol submit</p>
                                <p class="ml-6">6. Jika data sudah sesuai dan terisi semua, klik tombol simpan dan cetak</p>
                                <p class="ml-6">7. Tunggu proses pembuatan surat permohonan lelang</p>
                                <p class="ml-6">8. Selesai</p>
                            </div>

                            <div
                                @click="toggleAccordion('validasi-lelang')"
                                class="cursor-pointer"
                            >
                                <h1
                                    class="tracking-wide block text-md font-bold mb-1 text-white uppercase"
                                >
                                    * Validasi Lelang
                                    <span class="text-md">
                                        <!-- If open, show 'up' icon, otherwise show 'down' icon -->
                                        {{
                                            openAccordion === 'validasi-lelang'
                                                ? "▲"
                                                : "▼"
                                        }}
                                    </span>
                                </h1>
                            </div>
                            <div
                                class="mb-3"
                                v-if="openAccordion === 'validasi-lelang'"
                            >
                                <p class="ml-3">
                                    Panduan menggunakan menu Validasi Lelang, sebagai
                                    berikut
                                </p>
                                <p class="ml-6">
                                    1. PIlih No Pengajuan
                                </p>
                                <p class="ml-6">
                                    2. Sesuaikan & konfirmasi data dengan nasabah
                                </p>
                                <p class="ml-6">3. Check barang lelang mana yang akan disetujui</p>
                                <p class="ml-6">4. Klik tombol validasi</p>
                                <p class="ml-6">5. Selesai</p>
                            </div>

                            <div
                                @click="toggleAccordion('input-lelang')"
                                class="cursor-pointer"
                            >
                                <h1
                                    class="tracking-wide block text-md font-bold mb-1 text-white uppercase"
                                >
                                    * Input Approve Lelang
                                    <span class="text-md">
                                        <!-- If open, show 'up' icon, otherwise show 'down' icon -->
                                        {{
                                            openAccordion === 'input-lelang'
                                                ? "▲"
                                                : "▼"
                                        }}
                                    </span>
                                </h1>
                            </div>
                            <div
                                class="mb-3"
                                v-if="openAccordion === 'input-lelang'"
                            >
                                <p class="ml-3">
                                    Panduan menggunakan menu Input Approve Lelang, sebagai
                                    berikut
                                </p>
                                <p class="ml-6">
                                    1. PIlih No Pengajuan (yang telah disetujui)
                                </p>
                                <p class="ml-6">
                                    2. Sesuaikan & konfirmasi data dengan nasabah
                                </p>
                                <p class="ml-6">3. Jika sudah, klik tombol cetak nota</p>
                                <p class="ml-6">4. Tunggu proses pembuatan kwitansi pembelian barang lelang</p>
                                <p class="ml-6">5. Selesai</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Menu SBG Dwilipat -->
                <div class="mt-6">
                    <div class="bg-sky-700 p-4 rounded-lg shadow">
                        <div class="sm:col-span-1 text-white">
                            <h1
                                class="tracking-wide block text-md font-bold mb-3 text-white uppercase"
                            >
                                Cetak SBG Dwilipat Menu :
                            </h1>
                            <div
                                @click="toggleAccordion('sbgdwilipat')"
                                class="cursor-pointer"
                            >
                                <h1
                                    class="tracking-wide block text-md font-bold mb-1 text-white uppercase"
                                >
                                    * Transaksi Gadai
                                    <span class="text-md">
                                        <!-- If open, show 'up' icon, otherwise show 'down' icon -->
                                        {{
                                            openAccordion === "sbgdwilipat"
                                                ? "▲"
                                                : "▼"
                                        }}
                                    </span>
                                </h1>
                            </div>
                            <div
                                class="mb-3 text-white"
                                v-if="openAccordion === 'sbgdwilipat'"
                            >
                                <p class="ml-3">
                                    Panduan menggunakan menu Transaksi Gadai,
                                    sebagai berikut
                                </p>
                                <p class="ml-6">
                                    1. Klik tombol cetak yang ada di tabel
                                </p>
                                <p class="ml-6">
                                    2. Tunggu proses memuat surat dwilipat bukti
                                    gadai
                                </p>
                                <p class="ml-6">3. Selesai</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Menu Laporan -->
                <div class="mt-6">
                    <div class="bg-sky-700 p-4 rounded-lg shadow">
                        <div class="sm:col-span-1 text-white">
                            <h1
                                class="tracking-wide block text-md font-bold mb-3 text-white uppercase"
                            >
                                Laporan Menu :
                            </h1>
                            <p class="ml-3">
                                Panduan menggunakan menu Laporan, sebagai
                                berikut
                            </p>
                            <p class="ml-6">
                                1. Klik export data yang diinginkan
                            </p>
                            <p class="ml-6">
                                2. Pilih periode yang ingin di export
                            </p>
                            <p class="ml-6">
                                3. Jika memilih spesifik periode, maka pilih
                                tanggal awal dan akhir
                            </p>
                            <p class="ml-6">4. Klik tombol export</p>
                            <p class="ml-6">
                                5. Tunggu proses pengunduhan excel file
                            </p>
                            <p class="ml-6">6. Selesai</p>
                        </div>
                    </div>
                </div>

                <!-- Menu Bantuan -->
            </main>
        </template>
    </AdminLayout>
</template>
