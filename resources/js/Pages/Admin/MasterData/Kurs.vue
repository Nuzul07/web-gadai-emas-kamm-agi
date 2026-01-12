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
    kursData();
});

defineProps({
    errors: Object,
});

const form = useForm({
    kurs: "",
    tgl_berlaku: "",
});

const formDtl = useForm({
    kurs: "",
    tgl_berlaku: "",
});

const modal = reactive({
    addKurs: false,
});

const modalAddKurs = () => {
    modal.addKurs = true;
};

const kurs = ref([]);
const isKursEmpty = computed(() => kurs.value.length === 0);
const kursLoading = ref(false);
const kursData = async () => {
    kursLoading.value = true;
    await axios.get(route("getKurs")).then((res) => {
        const data = res.data;
        kurs.value = data;
        console.log(kurs.value);
        kursLoading.value = false;
    });
};

const crntPage = ref(1);
const perPage = 10;

const totalPages = computed(() => Math.ceil(kurs.value.length / perPage));

const pgntKurs = computed(() => {
    const start = (crntPage.value - 1) * perPage;
    const end = Math.min(start + perPage, searchKurs.value.length);
    return searchKurs.value.slice(start, end).map((kurs, index) => ({
        ...kurs,
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

const searchIKurs = ref("");
const searchKurs = computed(() => {
    if (!searchIKurs.value) {
        return kurs.value;
    }
    const lowerCase = searchIKurs.value.toLowerCase();
    if (kurs.value) {
        return kurs.value.filter((krs) => {
            return (
                krs.Tgl_input.toLowerCase().includes(lowerCase) ||
                krs.Kurs.toLowerCase().includes(lowerCase) ||
                krs.Tgl_berlaku.toLowerCase().includes(lowerCase)
            );
        });
    } else {
        return [];
    }
});

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

const updateAktif = async (d) => {
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
        await axios.post(route("updateKursAktif"), { tgl_input: d.Tgl_input });

        // Close the loading alert and show success message
        Swal.fire({
            title: "Berhasil",
            text: "Kurs Aktif",
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

        // Refresh data
        kursData();
    } catch (error) {
        console.log("error", error);

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
};

const updateNonAktif = async (d) => {
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
        await axios.post(route("updateKursNonAktif"), {
            tgl_input: d.Tgl_input,
        });

        // Close the loading alert and show success message
        Swal.fire({
            title: "Berhasil",
            text: "Kurs Non-Aktif",
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

        // Refresh data
        kursData();
    } catch (error) {
        console.log("error", error);

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
};

// store kurs
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
            await axios.post(route("storeKurs"), form);

            // Close the loading alert and show success message
            Swal.fire({
                title: "Berhasil",
                text: "Kurs Ditambahkan",
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
            kursData(); // Refresh the kurs data
            form.reset(); // Reset the form
            modal.addKurs = false; // Close the modal
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
// end
</script>

<template>
    <Head title="STL" />
    <AdminLayout>
        <template #titlePage>
            <TitlePage value="Data STL" />
        </template>
        <template #content>
            <main class="p-6 bg-gray-100 flex-grow overflow-y-auto">
                <div class="bg-white p-4 rounded-lg shadow">
                    <div class="flex justify-between items-center mb-4">
                        <div class="pr-2">
                            <SearchInput
                                label="Cari Data"
                                v-model="searchIKurs"
                                placeholder="Cari"
                            ></SearchInput>
                        </div>
                        <div class="ml-4">
                            <div class="text-black">
                                <button
                                    class="mt-4 ml-2 bg-sky-700 text-white py-1 px-4 rounded"
                                    @click="modalAddKurs()"
                                >
                                    Tambah
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
                                        no
                                    </th>
                                    <th
                                        scope="col"
                                        class="text-center text-md font-sans uppercase pb-3 text-white"
                                    >
                                        Tgl Input
                                    </th>
                                    <th
                                        scope="col"
                                        class="text-center text-md font-sans uppercase pb-3 text-white"
                                    >
                                        STL
                                    </th>
                                    <th
                                        scope="col"
                                        class="text-center text-md font-sans uppercase pb-3 text-white"
                                    >
                                        Tgl Berlaku
                                    </th>
                                    <th
                                        scope="col"
                                        class="text-center text-md font-sans uppercase pb-3 text-white"
                                    >
                                        status
                                    </th>
                                </tr>
                            </thead>
                            <tbody v-if="!kursLoading">
                                <tr
                                    v-for="(d, index) in pgntKurs"
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
                                        {{ formatDateDBTbl(d.Tgl_input) }}
                                    </td>
                                    <td
                                        class="whitespace-nowrap text-sm text-center text-black py-2"
                                    >
                                        {{ formatRupiah(d.Kurs) }}
                                    </td>
                                    <td
                                        class="whitespace-nowrap text-sm text-center text-black py-2"
                                    >
                                        {{ formatDateTbl(d.Tgl_berlaku) }}
                                    </td>
                                    <td
                                        class="whitespace-nowrap text-sm text-center text-black py-2"
                                        v-if="d.active == 1"
                                    >
                                        <a @click="updateNonAktif(d)"
                                            ><img
                                                class="m-auto cursor-pointer"
                                                src="/gadaiemas/storage/icon/switch-on.png"
                                                title="Tekan tombol ini jika ingin merubah status"
                                        /></a>
                                    </td>
                                    <td
                                        class="whitespace-nowrap text-sm text-center text-black py-2"
                                        v-if="d.active == 0"
                                    >
                                        <a @click="updateAktif(d)"
                                            ><img
                                                class="m-auto cursor-pointer"
                                                src="/gadaiemas/storage/icon/switch-off.png"
                                                title="Tekan tombol ini jika ingin merubah status"
                                        /></a>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                        <div class="w-full text-center mt-2">
                            <span
                                class="text-center text-xl text-white"
                                v-if="kursLoading"
                                >Sedang Memuat...</span
                            >
                        </div>
                        <div class="w-full text-center mt-2">
                            <span 
                                class="text-center text-xl text-white"
                                v-if="!kursLoading && isKursEmpty"
                                >Tidak Ada Data</span
                            >
                        </div>
                        <div class="w-full text-center mt-2">
                            <span 
                                class="text-center text-xl text-white"
                                v-if="
                                    !kursLoading && 
                                    !isKursEmpty &&
                                    searchKurs.length < 1"
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
                        :show="modal.addKurs"
                        @close="[(modal.addKurs = false), (form.errors = {})]"
                        maxWidth="2xl"
                        classHeader="bg-sky-700 text-white"
                    >
                        <template #title> Tambah STL </template>
                        <template #content>
                            <div class="mt-6 grid grid-cols-2 gap-4">
                                <div class="sm:col-span-1 pt-5">
                                    <h1
                                        class="tracking-wide block mb-2 text-xs font-bold text-gray-900 uppercase"
                                    >
                                        STL :
                                    </h1>
                                </div>
                                <div class="sm:col-span-1">
                                    <InputFormatIDR
                                        :requiredIndicator="false"
                                        id="kurs"
                                        type="text"
                                        name="kurs"
                                        v-model="form.kurs"
                                    />
                                    <div v-if="form.errors.kurs">
                                        <span class="text-sm text-red-600"
                                            >*{{ form.errors.kurs[0] }}</span
                                        >
                                    </div>
                                </div>
                                <div class="sm:col-span-1 pt-5">
                                    <h1
                                        class="tracking-wide block mb-2 text-xs font-bold text-gray-900 uppercase"
                                    >
                                        Tgl Berlaku :
                                    </h1>
                                </div>
                                <div class="sm:col-span-1">
                                    <CustTextInput
                                        :requiredIndicator="false"
                                        id="tgl_berlaku"
                                        type="date"
                                        name="tgl_berlaku"
                                        v-model="form.tgl_berlaku"
                                    />
                                    <div v-if="form.errors.tgl_berlaku">
                                        <span class="text-sm text-red-600"
                                            >*{{ form.errors.tgl_berlaku[0] }}</span
                                        >
                                    </div>
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
                </div>
            </main>
        </template>
    </AdminLayout>
</template>
