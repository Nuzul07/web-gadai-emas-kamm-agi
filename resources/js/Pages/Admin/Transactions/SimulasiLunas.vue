<script setup>
import { ref, onMounted, watch, computed, reactive } from "vue";
import AdminLayout from "@/Layouts/AdminLayout.vue";
import TitlePage from "@/Components/TitlePage.vue";
import SearchInput from "@/Components/SearchInput.vue";
import CustTextInput from "@/Components/CustTextInput.vue";
import Modal from "@/Components/DialogModal.vue";
import { Head, useForm } from "@inertiajs/vue3";
import axios, { formToJSON } from "axios";
import Swal from "sweetalert2";
import dayjs from "dayjs";
import { selectedTipeGadai } from "@/src/store";

onMounted(() => {
    checkTipeGadai();
    gadaiData();
    golonganData();
});

defineProps({
    errors: Object,
});

const checkTipeGadai = () => {
    if (selectedTipeGadai.value === "") {
        Swal.fire({
            imageWidth: 200,
            imageUrl: "/gadaiemas/storage/image/oops-kim yoo jung.jpg",
            imageHeight: 200,
            imageAlt: "Custom image",
            title: "Ooppss....",
            html: "Tuhkan belum dipilih produk gadainya 😠 <br> Tolong pilih dulu yaa, Terima Kasih!!!",
        }).then(() => {
            window.location.href = route('gadaiDashboard')
        })
    }
}

const simul = reactive({
    no_sbg: "",
    tgl_transaksi: "",
    tgl_pelunasan: "",
    tgl_jtempo: "",
    pokok_pinjaman: "",
    sewa_modal: "",
    denda_pinjaman: "",
    golongan: "",
    total_pembayaran: "",
});

const resetField = () => {
    simul.no_sbg = "";
    simul.tgl_pelunasan = "";
    simul.tgl_jtempo = "";
    simul.pokok_pinjaman = "";
    simul.sewa_modal = "";
    simul.denda_pinjaman = "";
    finesAmount.value = 0;
    simul.golongan = "";
    simul.total_pembayaran = "";
};

const modal = reactive({
    simulasi: false,
});

const modalSimulasi = (d) => {
    selectDataGadai(d);
    modal.simulasi = true;
};

const closeModalSimulasi = () => {
    modal.simulasi = false;
    resetField();
}

//fetch data from DB
const gadai = ref([]);
const isGadaiEmpty = computed(() => gadai.value.length === 0);
const gadaiLoading = ref(false);
const gadaiData = async () => {
    gadaiLoading.value = true;
    let url =
        selectedTipeGadai.value === "emas"
            ? "getGadaiEmasActive"
            : selectedTipeGadai.value === "motor"
            ? "getGadaiMotorActive"
            : selectedTipeGadai.value === "mobil"
            ? "getGadaiMobilActive"
            : ""
    await axios.get(route(url)).then((res) => {
        const data = res.data;  
        gadai.value = data;
        gadaiLoading.value = false;
    });
};

watch(selectedTipeGadai, () => {
    gadaiData();
});

const golongan = ref([]);
const golonganData = async () => {
    await axios.get(route("getGolongan")).then((res) => {
        const data = res.data;
        golongan.value = data;
    });
};
//end

//pagination
const crntPage = ref(1);
const perPage = 10;

const totalPages = computed(() => Math.ceil(gadai.value.length / perPage));

const pgntGadai = computed(() => {
    const start = (crntPage.value - 1) * perPage;
    const end = Math.min(start + perPage, searchGadai.value.length);
    return searchGadai.value.slice(start, end).map((gadai, index) => ({
        ...gadai,
        no: start + index + 1,
    }));
});

const prevPage = () => {
    if (crntPage.value > 1) {
        crntPage.value--;
    }
};

const nextPage = () => {
    if (crntPage.value < totalPages.value) {
        crntPage.value++;
    }
};
//end

//search data in table
const searchIGad = ref("");
const searchGadai = computed(() => {
    if (!searchIGad.value) {
        return gadai.value;
    }
    const lowerCase = searchIGad.value.toLowerCase();
    if (gadai.value) {
        return gadai.value.filter((gad) => {
            return gad.No_sbg.toLowerCase().includes(lowerCase);
        });
    } else {
        return [];
    }
});

//end

//formatter
const formatDate = (date) => {
    const d = new Date(date);
    let month = "" + (d.getMonth() + 1);
    let day = "" + d.getDate();
    let year = d.getFullYear();

    if (month.length < 2) month = "0" + month;
    if (day.length < 2) day = "0" + day;

    return [year, month, day].join("-");
};

const formatDateTB = (date) => {
    const d = new Date(date);
    let month = "" + (d.getMonth() + 1);
    let day = "" + d.getDate();
    let year = d.getFullYear();

    if (month.length < 2) month = "0" + month;
    if (day.length < 2) day = "0" + day;

    return [day, month, year].join("-");
};

const formatRupiah = (value) => {
    const numberFormat = new Intl.NumberFormat("id-ID", {
        style: "currency",
        currency: "IDR",
        minimumFractionDigits: 0,
        maximumFractionDigits: 0,
    });
    return numberFormat.format(value);
};

const formatDecimal = (value) => {
    const number = parseFloat(value);
    if (isNaN(number)) {
        return value;
    }
    return number.toFixed(1);
};
//end

//calculation
const sewaModalAmount = ref(0);
const calculateSewaModal = () => {
    const pokokPinjaman =  parseFloat(simul.pokok_pinjaman.replace(/[^\d,]/g, "").replace(",", ".")) || 0;
    const payDate = dayjs(simul.tgl_pelunasan).startOf('day'); // Remove any formatting
    const transDate = dayjs(simul.tgl_transaksi).startOf('day');

    const diffFirst = dayjs(payDate).diff(transDate, "day") + 1;
    if (selectedTipeGadai.value === 'emas') {
        const sewaModalGolongan = golongan.value.find(
            (g) => g.GOLONGAN === simul.golongan
        );

        if (diffFirst <= 15) {
            sewaModalAmount.value = formatRupiah(
                parseFloat(
                    (pokokPinjaman * sewaModalGolongan.SEWA_MODAL_15HARI).toFixed(0)
                )
            );
        } else if (diffFirst > 15 && diffFirst <= 30) {
            sewaModalAmount.value = formatRupiah(
                parseFloat(
                    (
                        pokokPinjaman *
                        (sewaModalGolongan.SEWA_MODAL16_30HARI)
                    ).toFixed(0)
                )
            );
            simul.sewa_modal = formatDecimal(sewaModalGolongan.SEWA_MODAL16_30HARI * 100);
        } else if (diffFirst > 30 && diffFirst <= 45) {
            sewaModalAmount.value = formatRupiah(
                parseFloat(
                    (
                        pokokPinjaman *
                        (sewaModalGolongan.SEWA_MODAL31_45HARI)
                    ).toFixed(0)
                )
            );
            simul.sewa_modal = formatDecimal(sewaModalGolongan.SEWA_MODAL31_45HARI * 100);
        } else if (diffFirst > 45 && diffFirst <= 60) {
            sewaModalAmount.value = formatRupiah(
                parseFloat(
                    (
                        pokokPinjaman *
                        (sewaModalGolongan.SEWA_MODAL46_60HARI)
                    ).toFixed(0)
                )
            );
            simul.sewa_modal = formatDecimal(sewaModalGolongan.SEWA_MODAL46_60HARI * 100);
        } else if (diffFirst > 60 && diffFirst <= 75) {
            sewaModalAmount.value = formatRupiah(
                parseFloat(
                    (
                        pokokPinjaman *
                        (sewaModalGolongan.SEWA_MODAL61_75HARI)
                    ).toFixed(0)
                )
            );
            simul.sewa_modal = formatDecimal(sewaModalGolongan.SEWA_MODAL61_75HARI * 100);
        } else if (diffFirst > 75 && diffFirst <= 90) {
            sewaModalAmount.value = formatRupiah(
                parseFloat(
                    (
                        pokokPinjaman *
                        (sewaModalGolongan.SEWA_MODAL76_90HARI)
                    ).toFixed(0)
                )
            );
            simul.sewa_modal = formatDecimal(sewaModalGolongan.SEWA_MODAL76_90HARI * 100);
        } else if (diffFirst > 90 && diffFirst <= 105) {
            sewaModalAmount.value = formatRupiah(
                parseFloat(
                    (
                        pokokPinjaman *
                        (sewaModalGolongan.SEWA_MODAL91_105HARI)
                    ).toFixed(0)
                ) 
            );
            simul.sewa_modal = formatDecimal(sewaModalGolongan.SEWA_MODAL91_105HARI * 100);
        } else if (diffFirst > 105) {
            sewaModalAmount.value = formatRupiah(
                parseFloat(
                    (
                        pokokPinjaman *
                        (sewaModalGolongan.SEWA_MODAL_105_ATAS_HARI)
                    ).toFixed(0)
                )
            );
            simul.sewa_modal = formatDecimal(sewaModalGolongan.SEWA_MODAL_105_ATAS_HARI * 100);
        }
    }
    
    console.log(diffFirst)
    console.log(pokokPinjaman)
    console.log(sewaModalAmount.value);
};
const finesAmount = ref(0);
const calculateFines = () => {
    const pokokPinjaman =  parseFloat(simul.pokok_pinjaman.replace(/[^\d,]/g, "").replace(",", ".")) || 0;;
    const payDate = dayjs(simul.tgl_pelunasan).startOf('day');
    const borrowDate = dayjs(simul.tgl_jtempo).startOf('day');

    const diffDays = payDate.diff(borrowDate, "day");
    
    // const diffDays = 30; // Calculate the difference in days

    let fines = 0;
    if (selectedTipeGadai.value === 'emas') {
        const finesGolongan = golongan.value.find(
            (g) => g.GOLONGAN === simul.golongan
        );
        if (diffDays > 0 && diffDays <= 7) {
            fines = (
                pokokPinjaman *
                finesGolongan.PERSEN_DENDA1_7HARI *
                diffDays
            ).toFixed(0);
        } else if (diffDays > 7 && diffDays <= 15) {
            fines = (
                pokokPinjaman *
                finesGolongan.PERSEN_DENDA8_15HARI *
                diffDays
            ).toFixed(0);
        } else if (diffDays > 15) {
            fines = (
                pokokPinjaman *
                finesGolongan.PERSEN_DENDA16_30HARI *
                diffDays
            ).toFixed(0);
        }
    } else if (selectedTipeGadai.value !== 'emas') {
        if (diffDays > 0) {
            const periods = Math.floor((diffDays - 1) / 15); // Calculate how many 15-day periods have passed
            const rate = 0.025 + periods * 0.025; // Increase rate by 0.025 for each 15-day period
            console.log({
                diffDays: diffDays,
                periode: periods, 
                rate: rate
            })
            fines = (pokokPinjaman * rate).toFixed(0);
        }
    }

    return fines;
};

const selectDataGadai = (d) => {
    simul.no_sbg = d.No_sbg;
    simul.tgl_transaksi = d.Tgl_transaksi;
    simul.pokok_pinjaman = formatRupiah(d.Pokok_pinjaman);
    simul.sewa_modal = formatDecimal(d.Sewa_modal * 100);
    simul.tgl_jtempo = formatDate(d.Tgl_jtempo);
    simul.golongan = d.Golongan;
    if (selectedTipeGadai.value !== 'emas') sewaModalAmount.value = formatRupiah(d.Bunga)
};

watch(
    () => simul.tgl_pelunasan,
    (newValue) => {
        console.log("New tgl_pelunasan:", newValue);
        if (newValue !== "" && selectedTipeGadai.value === 'emas') {
            calculateSewaModal();
            const fines = calculateFines();
            finesAmount.value = formatRupiah(fines);
            const finesString = fines.toString();
            simul.denda_pinjaman =
                parseFloat(
                    finesString.replace(/[^\d,]/g, "").replace(",", ".")
                ) || 0;

            const pokokPinjamanValue =
                parseFloat(
                    simul.pokok_pinjaman
                        .replace(/[^\d,]/g, "")
                        .replace(",", ".")
                ) || 0;
            const sewaModalString = sewaModalAmount.value.toString(); // Fix here: use sewaModalAmount.value
            const sewaModalValue =
                parseFloat(
                    sewaModalString.replace(/[^\d,]/g, "").replace(",", ".")
                ) || 0;
            const dendaPinjamanValue = simul.denda_pinjaman;
            simul.total_pembayaran = formatRupiah(
                pokokPinjamanValue + sewaModalValue + dendaPinjamanValue
            );
        } else if (newValue !== "" && selectedTipeGadai.value !== 'emas') {
            const fines = calculateFines();
            finesAmount.value = formatRupiah(fines);
            const finesString = fines.toString();
            simul.denda_pinjaman =
                parseFloat(
                    finesString.replace(/[^\d,]/g, "").replace(",", ".")
                ) || 0;

            const pokokPinjamanValue =
                parseFloat(
                    simul.pokok_pinjaman
                        .replace(/[^\d,]/g, "")
                        .replace(",", ".")
                ) || 0;
            const sewaModalString = sewaModalAmount.value.toString(); // Fix here: use sewaModalAmount.value
            const sewaModalValue =
                parseFloat(
                    sewaModalString.replace(/[^\d,]/g, "").replace(",", ".")
                ) || 0;
            const dendaPinjamanValue = simul.denda_pinjaman;
            simul.total_pembayaran = formatRupiah(
                pokokPinjamanValue + sewaModalValue + dendaPinjamanValue
            );
        }
    }
);
</script>
<template>
    <Head title="Simulasi Pelunasan" />
    <AdminLayout>
        <template #titlePage>
            <TitlePage value="Simulasi Pelunasan Gadai" />
        </template>
        <template #content>
            <main class="p-6 bg-gray-100 flex-grow overflow-y-auto">
                <div class="mt-6">
                    <div class="bg-white p-4 rounded-lg shadow">
                        <div class="flex justify-between items-center mb-4">
                            <div class="pr-2">
                                <SearchInput
                                    label="Cari Data"
                                    v-model="searchIGad"
                                    placeholder="Cari"
                                />
                            </div>
                        </div>
                        <div class="py-3 mt-14 rounded-lg bg-sky-700 shadow-lg">
                            <table class="min-w-full bg-sky-700 shadow-lg">
                                <thead>
                                    <tr>
                                        <th
                                            scope="col"
                                            class="text-center text-md font-sans uppercase pb-3 text-white"
                                        >
                                            no
                                        </th>
                                        <th
                                            scope="col"
                                            class="text-center text-md font-sans uppercase pb-3 text-white"
                                        >
                                            nomor sbg
                                        </th>
                                        <th
                                            scope="col"
                                            class="text-center text-md font-sans uppercase pb-3 text-white"
                                        >
                                            nasabah
                                        </th>
                                        <th
                                            scope="col"
                                            class="text-center text-md font-sans uppercase pb-3 text-white"
                                        >
                                            tanggal transaksi
                                        </th>
                                        <th
                                            scope="col"
                                            class="text-center text-md font-sans uppercase pb-3 text-white"
                                        >
                                            aksi
                                        </th>
                                    </tr>
                                </thead>
                                <tbody v-if="!gadaiLoading">
                                    <tr
                                        v-for="(d, index) in pgntGadai"
                                        :key="index"
                                        class="odd:bg-white even:bg-amber-400"
                                    >
                                        <td
                                            class="whitespace-nowrap text-sm text-center text-black py-2"
                                        >
                                            {{ d.no }}
                                        </td>
                                        <td
                                            class="whitespace-nowrap text-sm text-center text-black py-2"
                                        >
                                            {{ d.No_sbg }}
                                        </td>
                                        <td
                                            class="whitespace-nowrap text-sm text-center text-black py-2"
                                        >
                                            {{ d.nasabah.Nama }}
                                        </td>
                                        <td
                                            class="whitespace-nowrap text-sm text-center text-black py-2"
                                        >
                                            {{ formatDateTB(d.Tgl_transaksi) }}
                                        </td>
                                        <td
                                            class="whitespace-nowrap text-sm text-center text-black py-2"
                                        >
                                            <a @click="modalSimulasi(d)"
                                                ><img
                                                    class="m-auto cursor-pointer"
                                                    src="/gadaiemas/storage/icon/diagram.png"
                                                    title="Simulasikan"
                                                />
                                            </a>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                            <div class="w-full text-center mt-2">
                                <span
                                    class="text-center text-xl text-white"
                                    v-if="gadaiLoading"
                                    >Sedang Memuat...</span
                                >
                            </div>
                            <div class="w-full text-center mt-2">
                                <span
                                    class="text-center text-xl text-white"
                                    v-if="!gadaiLoading && isGadaiEmpty"
                                    >Tidak Ada Data</span
                                >
                            </div>
                            <div class="w-full text-center mt-2">
                                <span
                                    class="text-center text-xl text-white"
                                    v-if="
                                        !gadaiLoading &&
                                        !isGadaiEmpty &&
                                        searchGadai.length < 1
                                    "
                                    >Data Tidak Ditemukan</span
                                >
                            </div>
                        </div>
                        <div class="flex space-x-2 mt-2">
                            <button @click="prevPage" v-show="crntPage > 1">
                                <i
                                    ><img
                                        src="/gadaiemas/storage/icon/previous.png"
                                        title="Previous"
                                /></i>
                            </button>
                            <p class="p-1">
                                Bagian {{ crntPage }} dari {{ totalPages }}
                            </p>
                            <button
                                @click="nextPage"
                                v-show="crntPage < totalPages"
                            >
                                <i
                                    ><img
                                        src="/gadaiemas/storage/icon/next.png"
                                        title="Next"
                                /></i>
                            </button>
                        </div>
                        <Modal
                            :show="modal.simulasi"
                            @close="[(closeModalSimulasi())]"
                            maxWidth="2xl"
                            classHeader="bg-sky-700 text-white"
                        >
                            <template #title> Simulasi Pelunasan </template>
                            <template #content>
                                <div class="mt-6 grid grid-cols-3 gap-4">
                                    <div class="sm:col-span-1 pt-5">
                                        <h1
                                            class="tracking-wide block mb-2 text-xs font-bold text-gray-900 uppercase"
                                        >
                                            SBG :
                                        </h1>
                                    </div>
                                    <div class="sm:col-span-2">
                                        <CustTextInput
                                            :required-indicator="false"
                                            id="no_sbg"
                                            type="text"
                                            name="no_sbg"
                                            v-model="simul.no_sbg"
                                            disabled="true"
                                        />
                                    </div>
                                    <div class="sm:col-span-1 pt-5">
                                        <h1
                                            class="tracking-wide block mb-2 text-xs font-bold text-gray-900 uppercase"
                                        >
                                            Tanggal Pelunasan :
                                        </h1>
                                    </div>
                                    <div class="sm:col-span-2">
                                        <CustTextInput
                                            :requiredIndicator="false"
                                            id="tgl_pelunasan"
                                            type="date"
                                            name="tgl_pelunasan"
                                            v-model="simul.tgl_pelunasan"
                                        />
                                    </div>
                                    <div class="sm:col-span-1 pt-5">
                                        <h1
                                            class="tracking-wide block mb-2 text-xs font-bold text-gray-900 uppercase"
                                        >
                                            Tanggal Jatuh Tempo :
                                        </h1>
                                    </div>
                                    <div class="sm:col-span-2">
                                        <CustTextInput
                                            :requiredIndicator="false"
                                            id="tgl_jtempo"
                                            type="date"
                                            name="tgl_jtempo"
                                            v-model="simul.tgl_jtempo"
                                            disabled="true"
                                        />
                                    </div>
                                    <div class="sm:col-span-1 pt-5">
                                        <h1
                                            class="tracking-wide block mb-2 text-xs font-bold text-gray-900 uppercase"
                                        >
                                            Pokok Pinjaman :
                                        </h1>
                                    </div>
                                    <div class="sm:col-span-2">
                                        <CustTextInput
                                            :required-indicator="false"
                                            id="pokok_pinjaman"
                                            type="text"
                                            name="pokok_pinjaman"
                                            v-model="simul.pokok_pinjaman"
                                            disabled="true"
                                        />
                                    </div>
                                    <div v-if="selectedTipeGadai === 'emas'" class="sm:col-span-1 pt-5">
                                        <h1
                                            class="tracking-wide block mb-2 text-xs font-bold text-gray-900 uppercase"
                                        >
                                            Sewa Modal :
                                        </h1>
                                    </div>
                                    <div v-if="selectedTipeGadai !== 'emas'" class="sm:col-span-1 pt-5">
                                        <h1
                                            class="tracking-wide block mb-2 text-xs font-bold text-gray-900 uppercase"
                                        >
                                            Bunga :
                                        </h1>
                                    </div>
                                    <div class="sm:col-span-1">
                                        <CustTextInput
                                            :required-indicator="false"
                                            id="sewa_modal"
                                            type="text"
                                            name="sewa_modal"
                                            v-model="simul.sewa_modal"
                                            disabled="true"
                                            ket="%"
                                        />
                                    </div>
                                    <div class="sm:col-span-1">
                                        <CustTextInput
                                            :required-indicator="false"
                                            id="sewa_modal_amount"
                                            type="text"
                                            name="sewa_modal_amount"
                                            v-model="sewaModalAmount"
                                            disabled="true"
                                        />
                                    </div>
                                    <div class="sm:col-span-1 pt-5">
                                        <h1
                                            class="tracking-wide block mb-2 text-xs font-bold text-gray-900 uppercase"
                                        >
                                            Denda Pinjaman :
                                        </h1>
                                    </div>
                                    <div class="sm:col-span-2">
                                        <CustTextInput
                                            :required-indicator="false"
                                            id="denda_pinjaman"
                                            type="text"
                                            name="denda_pinjaman"
                                            v-model="finesAmount"
                                            disabled="true"
                                        />
                                    </div>
                                    <div class="sm:col-span-1 pt-5">
                                        <h1
                                            class="tracking-wide block mb-2 text-xs font-bold text-gray-900 uppercase"
                                        >
                                            Total Pembayaran :
                                        </h1>
                                    </div>
                                    <div class="sm:col-span-2">
                                        <CustTextInput
                                            :required-indicator="false"
                                            id="pokok_pinjaman"
                                            type="text"
                                            name="pokok_pinjaman"
                                            v-model="simul.total_pembayaran"
                                            disabled="true"
                                        />
                                    </div>
                                </div>
                                <div
                                    class="flex justify-end items-center mt-14"
                                >
                                    <div class="ml-4">
                                        <div class="text-black">
                                            <button
                                                @click="closeModalSimulasi()"
                                                class="mb-10 ml-2 bg-red-700 text-white py-1 px-4 rounded"
                                            >
                                                Selesai
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </template>
                        </Modal>
                    </div>
                </div>
            </main>
        </template>
    </AdminLayout>
</template>