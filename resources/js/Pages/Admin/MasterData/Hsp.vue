<script setup>
import { ref, onMounted, watch, computed, reactive } from "vue";
import AdminLayout from "@/Layouts/AdminLayout.vue";
import TitlePage from "@/Components/TitlePage.vue";
import SearchInput from "@/Components/SearchInput.vue";
import CustTextInput from "@/Components/CustTextInput.vue";
import InputFormatIDR from "@/Components/InputFormatIDR.vue";
import Modal from "@/Components/DialogModal.vue";
import { Head, useForm } from "@inertiajs/vue3";
import axios from "axios";
import Swal from "sweetalert2";

onMounted(() => {
    hspData();
});

defineProps({
    errors: Object,
});

const form = useForm({
    kode_stnk: "",
    kode_kendaraan: "",
    tahun: "",
    nilai: "",
    nama_barang: "",
});

const modal = reactive({
    addHsp: false,
    editHsp: false
});

const modalAddHsp = () => {
    modal.addHsp = true;
};

const modalEditHsp = (d) => {
    selectDataHsp(d)
    modal.editHsp = true;
}

const selectDataHsp = (d) => {
    form.kode_stnk = d.kode_stnk
    form.kode_kendaraan = d.kode_kendaraan
    form.tahun = d.tahun
    form.nilai = formatRupiah(d.nilai)
    form.nama_barang = d.nama_barang
}

const hsp = ref([]);
const isHspEmpty = computed(() => hsp.value.length === 0)
const hspLoading = ref(false);
const hspData = async () => {
    hspLoading.value = true;
    await axios.get(route("getHsp")).then((res) => {
        const data = res.data;
        hsp.value = data;
        console.log(hsp.value);
        hspLoading.value = false;
    });
};

const crntPage = ref(1);
const perPage = 10;

const totalPages = computed(() => Math.ceil(searchHsp.value.length / perPage));

const pgntHsp = computed(() => {
    const start = (crntPage.value - 1) * perPage;
    const end = Math.min(start + perPage, searchHsp.value.length);
    return searchHsp.value.slice(start, end);
});

const searchIHsp = ref("");
const searchHsp = computed(() => {
    if (!searchIHsp.value) {
        return hsp.value;
    }
    const lowerCase = searchIHsp.value.toLowerCase();
    if (hsp.value) {
        return hsp.value.filter((h) => {
            return (
                h.kode_stnk.toLowerCase().includes(lowerCase) ||
                h.kode_kendaraan.toLowerCase().includes(lowerCase) ||
                h.nama_barang.toLowerCase().includes(lowerCase) ||
                h.tahun.toLowerCase().includes(lowerCase)
            );
        });
    } else {
        return [];
    }
});

watch(searchIHsp, () => {
    crntPage.value = 1;
});

// Functions to handle next and previous
const nextPage = () => {
    if (crntPage.value < totalPages.value) crntPage.value++;
};

const prevPage = () => {
    if (crntPage.value > 1) crntPage.value--;
};

//formatter
const formatRupiah = (value) => {
    const numberFormat = new Intl.NumberFormat("id-ID", {
        style: "currency",
        currency: "IDR",
        minimumFractionDigits: 0, // No decimal places
        maximumFractionDigits: 0, // No decimal places
    });
    return numberFormat.format(value);
};

const formatDate = (date) => {
    const d = new Date(date);
    let month = "" + (d.getMonth() + 1);
    let day = "" + d.getDate();
    const year = d.getFullYear();

    if (month.length < 2) month = "0" + month;
    if (day.length < 2) day = "0" + day;

    return [year, month, day].join("-");
};

const formatDateTbl = (date) => {
    const d = new Date(date);
    let month = "" + (d.getMonth() + 1);
    let day = "" + d.getDate();
    const year = d.getFullYear();

    if (month.length < 2) month = "0" + month;
    if (day.length < 2) day = "0" + day;

    return [day, month, year].join("-");
};

const formatDateDBTbl = (input) => {
    const date = new Date(input); // Ensure input is a Date object
    const year = date.getFullYear();
    const month = String(date.getMonth() + 1).padStart(2, "0");
    const day = String(date.getDate()).padStart(2, "0");
    const hours = String(date.getHours()).padStart(2, "0");
    const minutes = String(date.getMinutes()).padStart(2, "0");
    const seconds = String(date.getSeconds()).padStart(2, "0");

    return `${day}-${month}-${year} ${hours}:${minutes}:${seconds}`;
};

const formatDateDB = (input) => {
    const date = new Date(input); // Ensure input is a Date object
    const year = date.getFullYear();
    const month = String(date.getMonth() + 1).padStart(2, "0");
    const day = String(date.getDate()).padStart(2, "0");
    const hours = String(date.getHours()).padStart(2, "0");
    const minutes = String(date.getMinutes()).padStart(2, "0");
    const seconds = String(date.getSeconds()).padStart(2, "0");

    return `${year}-${month}-${day} ${hours}:${minutes}:${seconds}`;
};
//end

const selectedDelete = ref([])

// Function to delete selected delete
const deleteSelected = async () => {
    if (selectedDelete.value.length === 0) {
        Swal.fire('No items selected', 'Please select at least one item.', 'warning');
        return;
    }

    const result = await Swal.fire({
        title: `Apakah anda yakin untuk menghapus ${selectedDelete.value.length} data?`,
        icon: "warning",
        showDenyButton: true,
        confirmButtonText: "Submit",
        confirmButtonColor: "#036aa1",
        denyButtonText: "Cancel",
        denyButtonColor: "#aaa",
        reverseButtons: true,
    });

    if (result.isConfirmed) {
        try {
             // Show loading state
            Swal.fire({
                title: "Memproses",
                text: "Mohon Tunggu",
                background: "#0369a1",
                allowOutsideClick: false,
                showConfirmButton: false,
                customClass: {
                    title: "text-white", // Tailwind class for title text color
                    popup: "text-white",
                    loader: "bg-white", // Tailwind class for content text color
                },
                didOpen: () => {
                    Swal.showLoading(); // Show loading spinner
                },
            });

            const payload = {
                kode_stnk: selectedDelete.value.map(item => item.kode_stnk),
                kode_kendaraan: selectedDelete.value.map(item => item.kode_kendaraan),
                tahun: selectedDelete.value.map(item => item.tahun),
            }

             // Perform the API request
            await axios.post(route("deleteHsp"), payload);

            // Close the loading alert and show success message
            Swal.fire({
                title: "Berhasil",
                text: "Data HPS berhasil dihapus",
                icon: "success",
                iconColor: "#FFFFFF",
                background: "#0369a1",
                showConfirmButton: false,
                timer: 1300,
                customClass: {
                    title: "text-white", // Tailwind class for title text color
                    popup: "text-white", // Tailwind class for content text color
                },
            });

            hspData();
            selectedDelete.value = []

        } catch (error) {
             // Show error message
            Swal.fire({
                title: "Terjadi kesalahan",
                text: "Silahkan hubungi Divisi IT untuk perbaikan",
                icon: "error",
                iconColor: "#FFFFFF",
                background: "#ba1c1c",
                showConfirmButton: true,
                confirmButtonColor: "#FFFFFF",
                customClass: {
                    title: "text-white", // Tailwind class for title text color
                    popup: "text-white",
                    confirmButton: "text-black",
                },
            });
        }
    }
}

const store = async () => {
    const result = await Swal.fire({
        title: "Apakah anda yakin data sudah benar?",
        icon: "warning",
        showDenyButton: true,
        confirmButtonText: "Submit",
        confirmButtonColor: "#036aa1",
        denyButtonText: "Cancel",
        denyButtonColor: "#aaa",
        reverseButtons: true,
    });

    if (result.isConfirmed) {
        try {
            // Show loading state
            Swal.fire({
                title: "Memproses",
                text: "Mohon Tunggu",
                background: "#0369a1",
                allowOutsideClick: false,
                showConfirmButton: false,
                customClass: {
                    title: "text-white", // Tailwind class for title text color
                    popup: "text-white",
                    loader: "bg-white", // Tailwind class for content text color
                },
                didOpen: () => {
                    Swal.showLoading(); // Show loading spinner
                },
            });

            // Perform the API request
            const res = await axios.post(route("storeHsp"), form);

            if (res.data.status == false) {
                // Show error message
                Swal.fire({
                    title: "Peringatan",
                    text: "Data tersebut sudah ada",
                    icon: "warning",
                    iconColor: "#FFFFFF",
                    background: "#ba1c1c",
                    showConfirmButton: true,
                    confirmButtonColor: "#FFFFFF",
                    customClass: {
                        title: "text-white", // Tailwind class for title text color
                        popup: "text-white",
                        confirmButton: "text-black",
                    },
                })
                return
            }

            // Close the loading alert and show success message
            Swal.fire({
                title: "Berhasil",
                text: "HPS Ditambahkan",
                icon: "success",
                iconColor: "#FFFFFF",
                background: "#0369a1",
                showConfirmButton: false,
                timer: 1300,
                customClass: {
                    title: "text-white", // Tailwind class for title text color
                    popup: "text-white", // Tailwind class for content text color
                },
            });

            hspData();
            form.reset();
            modal.addHsp = false;
        } catch (error) {
            console.log("error", error);

            // Handle validation errors
            if (error.response && error.response.status === 422) {
                form.errors = error.response.data.errors || {};
            }

            // Show error message
            Swal.fire({
                title: "Terjadi kesalahan",
                text: "Periksa kembali inputannya",
                icon: "error",
                iconColor: "#FFFFFF",
                background: "#ba1c1c",
                showConfirmButton: true,
                confirmButtonColor: "#FFFFFF",
                customClass: {
                    title: "text-white", // Tailwind class for title text color
                    popup: "text-white",
                    confirmButton: "text-black",
                },
            });
        }
    }
};

const removeFormat = () => {
    form.nilai =
        parseFloat(
            form.nilai.toString().replace(/[^\d,-]/g, "").replace(".", ",")
        ) || 0;
}

const update = async () => {
    const result = await Swal.fire({
        title: "Apakah anda yakin data sudah benar?",
        icon: "warning",
        showDenyButton: true,
        confirmButtonText: "Submit",
        confirmButtonColor: "#036aa1",
        denyButtonText: "Cancel",
        denyButtonColor: "#aaa",
        reverseButtons: true,
    });

    if (result.isConfirmed) {
        removeFormat()
        try {
            // Show loading state
            Swal.fire({
                title: "Memproses",
                text: "Mohon Tunggu",
                background: "#0369a1",
                allowOutsideClick: false,
                showConfirmButton: false,
                customClass: {
                    title: "text-white", // Tailwind class for title text color
                    popup: "text-white",
                    loader: "bg-white", // Tailwind class for content text color
                },
                didOpen: () => {
                    Swal.showLoading(); // Show loading spinner
                },
            });

            // Perform the API request
            await axios.post(route("updateHsp"), form);

            // Close the loading alert and show success message
            Swal.fire({
                title: "Berhasil",
                text: "Hsp Diperbarui",
                icon: "success",
                iconColor: "#FFFFFF",
                background: "#0369a1",
                showConfirmButton: false,
                timer: 1300,
                customClass: {
                    title: "text-white", // Tailwind class for title text color
                    popup: "text-white", // Tailwind class for content text color
                },
            });

            // Refresh data and reset form
            hspData(); // Refresh user data
            form.reset();
            modal.editHsp = false; // Close the modal
        } catch (error) {
            console.log("error", error);

            // Handle validation errors
            if (error.response && error.response.status === 422) {
                form.errors = error.response.data.errors || {};
            }

            // Show error message
            Swal.fire({
                title: "Terjadi kesalahan",
                text: "Periksa kembali inputannya",
                icon: "error",
                iconColor: "#FFFFFF",
                background: "#ba1c1c",
                showConfirmButton: true,
                confirmButtonColor: "#FFFFFF",
                customClass: {
                    title: "text-white", // Tailwind class for title text color
                    popup: "text-white",
                    confirmButton: "text-black",
                },
            });
        }
    }
};
</script>

<template>
    <Head title="HPS" />
    <AdminLayout>
        <template #titlePage>
            <TitlePage value="Data HPS" />
        </template>
        <template #content>
            <main class="p-6 bg-gray-100 flex-grow overflow-y-auto">
                <div class="bg-white p-4 rounded-lg shadow">
                    <div class="flex justify-between items-center mb-4">
                        <div class="pr-2">
                            <SearchInput
                                label="Cari Data"
                                v-model="searchIHsp"
                                placeholder="Cari"
                            ></SearchInput>
                            <span class="text-xs font-bold text-gray-900">
                                *pencarian hanya berdasarkan Kode Stnk, Kode Kendaraan, Tahun, Nama Barang
                            </span>
                        </div>
                        <div class="ml-4">
                            <div class="text-black">
                                <button
                                    class="mt-4 ml-2 bg-sky-700 text-white py-1 px-4 rounded"
                                    @click="modalAddHsp()"
                                >
                                    Tambah
                                </button>
                                <button
                                    class="mt-4 ml-2 bg-red-700 text-white py-1 px-4 rounded"
                                    v-show="selectedDelete.length > 0"
                                    @click="deleteSelected()"
                                >
                                    Hapus
                                </button>
                            </div>
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
                                        Kode STNK
                                    </th>
                                    <th
                                        scope="col"
                                        class="text-center text-md font-sans uppercase pb-3 text-white"
                                    >
                                        Kode Kendaraan
                                    </th>
                                    <th
                                        scope="col"
                                        class="text-center text-md font-sans uppercase pb-3 text-white"
                                    >
                                        Tahun
                                    </th>
                                    <th
                                        scope="col"
                                        class="text-center text-md font-sans uppercase pb-3 text-white"
                                    >
                                        Nilai
                                    </th>
                                    <th
                                        scope="col"
                                        class="text-center text-md font-sans uppercase pb-3 text-white"
                                    >
                                        Nama Barang
                                    </th>
                                    <th
                                        scope="col"
                                        class="text-center text-md font-sans uppercase pb-3 text-white"
                                    >
                                        Edit
                                    </th>
                                    <th
                                        scope="col"
                                        class="text-center text-md font-sans uppercase pb-3 text-white"
                                    >
                                        Hapus
                                    </th>
                                </tr>
                            </thead>
                            <tbody v-if="!hspLoading">
                                <tr
                                    v-for="(d, index) in pgntHsp"
                                    :key="index"
                                    class="odd:bg-white even:bg-amber-400"
                                >
                                    <td
                                        class="whitespace-nowrap text-sm text-center text-black py-2"
                                    >
                                        {{ d.kode_stnk }}
                                    </td>
                                    <td
                                        class="whitespace-nowrap text-sm text-center text-black py-2"
                                    >
                                        {{ d.kode_kendaraan }}
                                    </td>
                                    <td
                                        class="whitespace-nowrap text-sm text-center text-black py-2"
                                    >
                                        {{ d.tahun }}
                                    </td>
                                    <td
                                        class="whitespace-nowrap text-sm text-center text-black py-2"
                                    >
                                        {{ formatRupiah(d.nilai) }}
                                    </td>
                                    <td
                                        class="whitespace-nowrap text-sm text-center text-black py-2"
                                    >
                                        {{ d.nama_barang }}
                                    </td>
                                    <td
                                        class="whitespace-nowrap text-sm text-center text-black py-2"
                                    >
                                        <a @click="modalEditHsp(d)"
                                            ><img
                                                class="m-auto cursor-pointer"
                                                src="/gadaiemas/storage/icon/edit.png"
                                                title="Edit"
                                        /></a>
                                    </td>
                                    <td
                                        class="whitespace-nowrap text-sm text-center text-black py-2"
                                    >
                                        <input type="checkbox" class="cursor-pointer" :value="d" v-model="selectedDelete">
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                        <div class="w-full text-center mt-2">
                            <span
                                class="text-center text-xl text-white"
                                v-if="hspLoading"
                                >Sedang Memuat...</span
                            >
                    </div>
                        <div class="w-full text-center mt-2">
                            <span
                                class="text-center text-xl text-white"
                                v-if="!hspLoading && isHspEmpty"
                                >Tidak Ada Data</span
                            >
                        </div>
                        <div class="w-full text-center mt-2">
                            <span
                                class="text-center text-xl text-white"
                                v-if="!hspLoading && !isHspEmpty && searchHsp.length < 1"
                                >Data Tidak Ditemukan</span
                            >
                        </div>
                    </div>
                    <div class="flex space-x-2 mt-2" v-if="!hspLoading">
                        <button @click="prevPage" v-show="crntPage > 1">
                            <i
                                ><img src="/gadaiemas/storage/icon/previous.png"
                                title="Previous"</i
                            >
                        </button>
                        <p class="p-1">
                            Bagian {{ crntPage }} dari {{ totalPages }}
                        </p>
                        <button @click="nextPage" v-show="crntPage < totalPages">
                            <i
                                ><img
                                    src="/gadaiemas/storage/icon/next.png"
                                    title="Next"
                            /></i>
                        </button>
                    </div>
                    <!-- Modal Add Hsp -->
                    <Modal
                        :show="modal.addHsp"
                        @close="[(modal.addHsp = false), (form.errors = {})]"
                        maxWidth="2xl"
                        classHeader="bg-sky-700 text-white"
                    >
                        <template #title> Tambah HPS </template>
                        <template #content>
                            <div class="mt-6 grid grid-cols-2 gap-4">
                                <div class="sm:col-span-1 pt-5">
                                    <h1
                                        class="tracking-wide block mb-2 text-xs font-bold text-gray-900 uppercase"
                                    >
                                        Kode Stnk :
                                        <span class="text-sm text-red-600">*</span>
                                    </h1>
                                </div>
                                <div class="sm:col-span-1">
                                    <CustTextInput
                                        :required-indicator="false"
                                        id="kode_stnk"
                                        type="text"
                                        name="kode_stnk"
                                        v-model="form.kode_stnk"
                                        :capital="true"
                                    />
                                </div>
                                <div class="sm:col-span-1 pt-5">
                                    <h1
                                        class="tracking-wide block mb-2 text-xs font-bold text-gray-900 uppercase"
                                    >
                                        Kode Kendaraan :
                                        <span class="text-sm text-red-600">*</span>
                                    </h1>
                                </div>
                                <div class="sm:col-span-1">
                                    <CustTextInput
                                        :required-indicator="false"
                                        id="kode_kendaraan"
                                        type="text"
                                        name="kode_kendaraan"
                                        v-model="form.kode_kendaraan"
                                        :capital="true"
                                    />
                                </div>
                                <div class="sm:col-span-1 pt-5">
                                    <h1
                                        class="tracking-wide block mb-2 text-xs font-bold text-gray-900 uppercase"
                                    >
                                        Tahun :
                                        <span class="text-sm text-red-600">*</span>
                                    </h1>
                                </div>
                                <div class="sm:col-span-1">
                                    <CustTextInput
                                        :required-indicator="false"
                                        id="tahun"
                                        type="text"
                                        name="tahun"
                                        v-model="form.tahun"
                                        :onlyNum="true"
                                    />
                                </div>
                                <div class="sm:col-span-1 pt-5">
                                    <h1
                                        class="tracking-wide block mb-2 text-xs font-bold text-gray-900 uppercase"
                                    >
                                        Nilai :
                                        <span class="text-sm text-red-600">*</span>
                                    </h1>
                                </div>
                                <div class="sm:col-span-1">
                                    <InputFormatIDR
                                        :required-indicator="false"
                                        id="nilai"
                                        type="text"
                                        name="nilai"
                                        v-model="form.nilai"
                                    />
                                    <div v-if="form.errors.nilai">
                                        <span class="text-sm text-red-600"
                                            >*{{ form.errors.nilai }}</span
                                        >
                                    </div>
                                </div>
                                <div class="sm:col-span-1 pt-5">
                                    <h1
                                        class="tracking-wide block mb-2 text-xs font-bold text-gray-900 uppercase"
                                    >
                                        Nama Barang :
                                        <span class="text-sm text-red-600">*</span>
                                    </h1>
                                </div>
                                <div class="sm:col-span-1">
                                    <CustTextInput
                                        :required-indicator="false"
                                        id="nama_barang"
                                        type="text"
                                        name="nama_barang"
                                        v-model="form.nama_barang"
                                        :capital="true"
                                    />
                                </div>
                            </div>
                            <div class="flex justify-end items-center mb-5">
                                <div class="ml-4">
                                    <div class="text-black">
                                        <button
                                            @click="store()"
                                            class="mt-4 ml-2 bg-green-700 text-white py-1 px-4 rounded"
                                        >
                                            Simpan
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </template>
                    </Modal>
                    <!-- Modal Edit Hsp -->
                    <Modal
                        :show="modal.editHsp"
                        @close="
                            modal.editHsp = false;
                        "
                        maxWidth="2xl"
                        classHeader="bg-sky-700 text-white"
                    >
                    <template #title> Edit HPS </template>
                    <template #content>
                            <div class="mt-6 grid grid-cols-2 gap-4">
                                <div class="sm:col-span-1 pt-5">
                                    <h1
                                        class="tracking-wide block mb-2 text-xs font-bold text-gray-900 uppercase"
                                    >
                                        Kode Stnk :
                                    </h1>
                                </div>
                                <div class="sm:col-span-1">
                                    <CustTextInput
                                        :required-indicator="false"
                                        id="kode_stnk"
                                        type="text"
                                        name="kode_stnk"
                                        v-model="form.kode_stnk"
                                        :disabled="true"
                                        :capital="true"
                                    />
                                </div>
                                <div class="sm:col-span-1 pt-5">
                                    <h1
                                        class="tracking-wide block mb-2 text-xs font-bold text-gray-900 uppercase"
                                    >
                                        Kode Kendaraan :
                                    </h1>
                                </div>
                                <div class="sm:col-span-1">
                                    <CustTextInput
                                        :required-indicator="false"
                                        id="kode_kendaraan"
                                        type="text"
                                        name="kode_kendaraan"
                                        v-model="form.kode_kendaraan"
                                        :disabled="true"
                                        :capital="true"
                                    />
                                </div>
                                <div class="sm:col-span-1 pt-5">
                                    <h1
                                        class="tracking-wide block mb-2 text-xs font-bold text-gray-900 uppercase"
                                    >
                                        Tahun :
                                    </h1>
                                </div>
                                <div class="sm:col-span-1">
                                    <CustTextInput
                                        :required-indicator="false"
                                        id="tahun"
                                        type="text"
                                        name="tahun"
                                        v-model="form.tahun"
                                        :disabled="true"
                                        :onlyNum="true"
                                    />
                                </div>
                                <div class="sm:col-span-1 pt-5">
                                    <h1
                                        class="tracking-wide block mb-2 text-xs font-bold text-gray-900 uppercase"
                                    >
                                        Nilai :
                                    </h1>
                                </div>
                                <div class="sm:col-span-1">
                                    <InputFormatIDR
                                        :required-indicator="false"
                                        id="nilai"
                                        type="text"
                                        name="nilai"
                                        v-model="form.nilai"
                                    />
                                    <div v-if="form.errors.nilai">
                                        <span class="text-sm text-red-600"
                                            >*{{ form.errors.nilai }}</span
                                        >
                                    </div>
                                </div>
                                <div class="sm:col-span-1 pt-5">
                                    <h1
                                        class="tracking-wide block mb-2 text-xs font-bold text-gray-900 uppercase"
                                    >
                                        Nama Barang :
                                    </h1>
                                </div>
                                <div class="sm:col-span-1">
                                    <CustTextInput
                                        :required-indicator="false"
                                        id="nama_barang"
                                        type="text"
                                        name="nama_barang"
                                        v-model="form.nama_barang"
                                        :capital="true"
                                    />
                                </div>
                            </div>
                            <div class="flex justify-end items-center mb-5">
                                <div class="ml-4">
                                    <div class="text-black">
                                        <button
                                            @click="update()"
                                            class="mt-4 ml-2 bg-green-700 text-white py-1 px-4 rounded"
                                        >
                                            Ubah
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </template>
                    </Modal>
                </div>
            </main>
        </template>
    </AdminLayout>
</template>