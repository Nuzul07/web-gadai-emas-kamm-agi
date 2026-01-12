<script setup>
import { ref, onMounted, watch, computed } from "vue";
import AdminLayout from "@/Layouts/AdminLayout.vue";
import TitlePage from "@/Components/TitlePage.vue";
import SearchInput from "@/Components/SearchInput.vue";
import { Head } from "@inertiajs/vue3";
import dayjs from "dayjs";
import relativeTime from "dayjs/plugin/relativeTime";
import "dayjs/locale/id";
import { selectedTipeGadai } from "@/src/store";

dayjs.extend(relativeTime);
dayjs.locale("id");

const props = defineProps({
    nas: Number,
    gdOnEmas: Number,
    gdOnMotor: Number,
    gdOnMobil: Number,
    gdOffEmas: Number,
    gdOffMotor: Number,
    gdOffMobil: Number,
    brgJmnEmas: Number,
    brgJmnMotor: Number,
    brgJmnMobil: Number,
    byrEmas: Array,
    byrMotor: Array,
    byrMobil: Array,
});

// Paginatioin State
const currentPage = ref(1);
const itemsPerPage = 5;

const bayarData = computed(() => 
    selectedTipeGadai.value === 'emas' 
        ? props.byrEmas 
        : selectedTipeGadai.value === 'motor' 
        ? props.byrMotor 
        : props.byrMobil
);

const searchIByr = ref("");
const searchBayar = computed(() => {
    if (!searchIByr.value) {
        return bayarData.value;
    }
    const lowerCase = searchIByr.value.toLowerCase();
    if (bayarData.value) {
        return bayarData.value.filter((byr) => {
            return byr.No_bayaran.toLowerCase().includes(lowerCase);
        });
    } else {
        return [];
    }
});

// Computed property to get paginated data
const paginatedByr = computed(() => {
    const start = (currentPage.value - 1) * itemsPerPage;
    const end = Math.min(start + itemsPerPage, searchBayar.value.length);
    return searchBayar.value.slice(start, end);
});

// Total Pages
const totalPages = computed(() =>
    Math.ceil(searchBayar.value.length / itemsPerPage)
);

// Watch for search Input changes and Reset Page
watch(searchIByr, () => {
    currentPage.value = 1;
});

// Functions to handle next and previous
const nextPage = () => {
    if (currentPage.value < totalPages.value) currentPage.value++;
};

const prevPage = () => {
    if (currentPage.value > 1) currentPage.value--;
};

const humandiff = (timestamp) => {
    return dayjs(timestamp).fromNow();
};

const formatRupiah = (value) => {
    const numberFormat = new Intl.NumberFormat("id-ID", {
        style: "currency",
        currency: "IDR",
        minimumFractionDigits: 0, // No decimal places
        maximumFractionDigits: 0, // No decimal places
    });
    return numberFormat.format(value);
};
</script>

<template>
    <Head title="Dashboard"></Head>
    <AdminLayout>
        <template #titlePage>
            <TitlePage value="Dashboard" />
        </template>
        <template #content>
            <main class="p-6 bg-gray-100 flex-grow overflow-y-auto">
                <!-- Conditional Content Based on selectedTipeGadai -->
                <div
                    v-if="selectedTipeGadai === 'motor'"
                    class="bg-blue-500 p-4 rounded-lg shadow mb-4"
                >
                    <h2 class="text-xl text-white font-bold mb-2">
                        Informasi Khusus untuk Gadai Motor
                    </h2>
                    <p class="text-white">
                        Konten dan form yang ditampilkan hanya untuk produk gadai <strong>Motor</strong>.
                    </p>
                </div>

                <div
                    v-else-if="selectedTipeGadai === 'emas'"
                    class="bg-yellow-500 p-4 rounded-lg shadow mb-4"
                >
                    <h2 class="text-xl text-white font-bold mb-2">
                        Informasi Gadai Emas
                    </h2>
                    <p class="text-white">
                        Konten dan form yang ditampilkan hanya untuk produk gadai
                        <strong>Emas</strong>.
                    </p>
                </div>

                <div
                    v-else-if="selectedTipeGadai === 'mobil'"
                    class="bg-green-500 p-4 rounded-lg shadow mb-4"
                >
                    <h2 class="text-xl text-white font-bold mb-2">
                        Informasi Gadai Mobil
                    </h2>
                    <p class="text-white">
                        Konten dan form yang ditampilkan hanya untuk produk gadai <strong>Mobil</strong>.
                    </p>
                </div>

                <div v-else class="bg-gray-400 p-4 rounded-lg shadow mb-4">
                    <h2 class="text-xl text-white font-bold mb-2">
                        Silakan Pilih Tipe Gadai
                    </h2>
                    <p class="text-white">Belum ada tipe gadai yang dipilih.</p>
                </div>

                <!-- Your existing content here -->
                <div class="grid grid-cols-4 gap-4">
                    <!-- Team Payments Card -->
                    <div v-if="selectedTipeGadai === 'emas' || selectedTipeGadai === 'motor' || selectedTipeGadai === 'mobil'"
                        class="bg-cyan-500 p-4 rounded-lg shadow">
                        <h2 class="text-xl text-white font-bold mb-4">
                            Nasabah
                        </h2>
                        <div class="text-center mt-4">
                            <div class="text-2xl text-white font-bold">
                                {{ nas }}
                            </div>
                            <div class="text-white">Orang</div>
                        </div>
                    </div>

                    <!-- Savings Card -->
                    <div v-if="selectedTipeGadai === 'emas'" class="bg-rose-500 p-4 rounded-lg shadow">
                        <h2 class="text-xl text-white font-bold mb-4">
                            Gadai Emas Aktif
                        </h2>
                        <div class="text-center">
                            <div class="text-2xl text-white font-bold">
                                {{ gdOnEmas }}
                            </div>
                            <div class="text-white">Transaksi</div>
                        </div>
                    </div>

                    <div v-if="selectedTipeGadai === 'motor'" class="bg-rose-500 p-4 rounded-lg shadow">
                        <h2 class="text-xl text-white font-bold mb-4">
                            Gadai Motor Aktif
                        </h2>
                        <div class="text-center">
                            <div class="text-2xl text-white font-bold">
                                {{ gdOnMotor }}
                            </div>
                            <div class="text-white">Transaksi</div>
                        </div>
                    </div>

                    <div v-if="selectedTipeGadai === 'mobil'" class="bg-rose-500 p-4 rounded-lg shadow">
                        <h2 class="text-xl text-white font-bold mb-4">
                            Gadai Mobil Aktif
                        </h2>
                        <div class="text-center">
                            <div class="text-2xl text-white font-bold">
                                {{ gdOnMobil }}
                            </div>
                            <div class="text-white">Transaksi</div>
                        </div>
                    </div>

                    <div v-if="selectedTipeGadai === 'emas'" class="bg-green-500 p-4 rounded-lg shadow">
                        <h2 class="text-xl text-white font-bold mb-4">
                            Emas Disimpan
                        </h2>
                        <div class="text-center">
                            <div class="text-2xl text-white font-bold">
                                {{ brgJmnEmas }}
                            </div>
                            <div class="text-white">Item</div>
                        </div>
                    </div>

                    <div v-if="selectedTipeGadai === 'motor'" class="bg-green-500 p-4 rounded-lg shadow">
                        <h2 class="text-xl text-white font-bold mb-4">
                            Motor Disimpan
                        </h2>
                        <div class="text-center">
                            <div class="text-2xl text-white font-bold">
                                {{ brgJmnMotor }}
                            </div>
                            <div class="text-white">Item</div>
                        </div>
                    </div>

                    <div v-if="selectedTipeGadai === 'mobil'" class="bg-green-500 p-4 rounded-lg shadow">
                        <h2 class="text-xl text-white font-bold mb-4">
                            Mobil Disimpan
                        </h2>
                        <div class="text-center">
                            <div class="text-2xl text-white font-bold">
                                {{ brgJmnMobil }}
                            </div>
                            <div class="text-white">Item</div>
                        </div>
                    </div>

                    <div v-if="selectedTipeGadai === 'emas'" 
                        class="bg-orange-500 p-4 rounded-lg shadow">
                        <h2 class="text-xl text-white font-bold mb-4">
                            Sudah Lunas
                        </h2>
                        <div class="text-center">
                            <div class="text-2xl text-white font-bold">
                                {{ gdOffEmas }}
                            </div>
                            <div class="text-white">Transaksi</div>
                        </div>
                    </div>

                    <div v-if="selectedTipeGadai === 'motor'" 
                        class="bg-orange-500 p-4 rounded-lg shadow">
                        <h2 class="text-xl text-white font-bold mb-4">
                            Sudah Lunas
                        </h2>
                        <div class="text-center">
                            <div class="text-2xl text-white font-bold">
                                {{ gdOffMotor }}
                            </div>
                            <div class="text-white">Transaksi</div>
                        </div>
                    </div>

                    <div v-if="selectedTipeGadai === 'mobil'" 
                        class="bg-orange-500 p-4 rounded-lg shadow">
                        <h2 class="text-xl text-white font-bold mb-4">
                            Sudah Lunas
                        </h2>
                        <div class="text-center">
                            <div class="text-2xl text-white font-bold">
                                {{ gdOffMobil }}
                            </div>
                            <div class="text-white">Transaksi</div>
                        </div>
                    </div>

                    <!-- Income Statistics Card
                    <div class="bg-white p-4 rounded-lg shadow">
                        <h2 class="text-xl font-bold mb-4">
                            Income Statistics
                        </h2>
                        <div class="flex justify-between items-center">
                            <div>
                                <div class="text-2xl font-bold">8%</div>
                                <div class="text-gray-500">Increase</div>
                            </div>
                        </div>
                    </div> -->

                    <!-- Subscription Card -->
                    <!-- <div class="bg-white p-4 rounded-lg shadow">
                        <h2 class="text-xl font-bold mb-4">Subscription</h2>
                        <div class="text-center">
                            <div class="text-2xl font-bold">$95.9</div>
                            <div class="text-gray-500">Per Month</div>
                            <button
                                class="mt-4 bg-blue-500 text-white py-2 px-4 rounded"
                            >
                                Upgrade
                            </button>
                        </div>
                    </div> -->
                </div>

                <div class="mt-3">
                    <div class="flex justify-between items-center mb-4">
                        <h2 class="text-xl font-bold">
                            Baru Saja Melakukan Pembayaran
                        </h2>
                        <div class="pr-2-">
                            <SearchInput
                                v-model="searchIByr"
                                placeholder="Cari"
                            ></SearchInput>
                        </div>
                    </div>
                    <div class="bg-emerald-500 p-2 rounded-lg shadow">
                        <!-- Case 1: Database is Empty -->
                        <div
                            v-if="bayarData.length === 0"
                            class="flex justify-center items-center h-full"
                        >
                            <div class="text-white text-xl">Belum ada data</div>
                        </div>

                        <!-- Case 2: Search Yields No Results -->
                        <div
                            v-else-if="searchBayar.length === 0"
                            class="flex justify-center items-center h-full"
                        >
                            <div class="text-white text-xl">
                                Data tidak ditemukan
                            </div>
                        </div>
                        <div v-else>
                            <div
                                v-for="(d, index) in paginatedByr"
                                :key="index"
                            >
                                <div class="flex justify-between items-center">
                                    <div class="flex items-center">
                                        <div class="ml-2">
                                            <div class="text-white">
                                                No Bayar : {{ d.No_bayaran }}
                                            </div>
                                            <div class="text-white">
                                                Jumlah Bayar :
                                                {{
                                                    formatRupiah(
                                                        d.Total_pembayaran
                                                    )
                                                }}
                                            </div>
                                            <div class="text-white">
                                                {{ d.Jenis_transaksi }}
                                            </div>
                                        </div>
                                    </div>
                                    <span class="text-white">{{
                                        humandiff(d.Tgl_pelunasan)
                                    }}</span>
                                </div>
                                <hr
                                    v-if="index !== paginatedByr.length - 1"
                                    class="my-1"
                                />
                            </div>
                        </div>
                    </div>
                    <!-- Pagination Controls Outside of the Green Box -->
                    <div
                        v-if="searchBayar.length > 0"
                        class="flex space-x-2 mt-2"
                    >
                        <button @click="prevPage" :disabled="currentPage === 1">
                            <i>
                                <img
                                    src="/gadaiemas/storage/icon/previous.png"
                                    title="Previous"
                                />
                            </i>
                        </button>
                        <p class="p-1">
                            Bagian {{ currentPage }} dari {{ totalPages }}
                        </p>
                        <button
                            @click="nextPage"
                            :disabled="currentPage === totalPages"
                        >
                            <i>
                                <img
                                    src="/gadaiemas/storage/icon/next.png"
                                    title="Next"
                                />
                            </i>
                        </button>
                    </div>
                </div>
            </main>
        </template>
    </AdminLayout>
</template>