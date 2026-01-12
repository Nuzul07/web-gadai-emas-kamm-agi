<script setup>
import { ref, onMounted, watch, computed, reactive } from "vue";
import AdminLayout from "@/Layouts/AdminLayout.vue";
import TitlePage from "@/Components/TitlePage.vue";
import SearchInput from "@/Components/SearchInput.vue";
import CustTextInput from "@/Components/CustTextInput.vue";
import Modal from "@/Components/DialogModal.vue";
import { Head, useForm } from "@inertiajs/vue3";
import axios from "axios";
import Swal from "sweetalert2";

onMounted(() => {
    penaksirData();
});

const props = defineProps({
    errors: Object,
    posko: String,
    cabang: String,
});

const form = useForm({
    id: "",
    name: "",
    posko: "",
    cabang: "",
});

const formDtl = useForm({
    id: "",
    name: "",
    posko: "",
    cabang: "",
});

const modal = reactive({
    addPenaksir: false,
    detailPenaksir: false,
});

const modalAddPenaksir = () => {
    poskoField.value = props.posko;
    cabangField.value = props.cabang;
    modal.addPenaksir = true;
};

const modalDetailPenaksir = (d) => {
    selectDataPenaksir(d);
    modal.detailPenaksir = true;
};

const selectDataPenaksir = (d) => {
    form.id = d.id;
    form.name = d.name;
    form.posko = d.posko;
    form.cabang = d.cabang;
    //
    formDtl.id = d.id;
    formDtl.name = d.name;
    formDtl.posko = d.posko;
    formDtl.cabang = d.cabang;
};

const formDtlReset = () => {
    formDtl.id = form.id;
    formDtl.name = form.name;
};

const penaksir = ref([]);
const isPenaksirEmpty = computed(() => penaksir.value.length === 0);
const penaksirLoading = ref(false);
const penaksirData = async () => {
    penaksirLoading.value = true;
    await axios.get(route("getPenaksir")).then((res) => {
        const data = res.data;
        penaksir.value = data;
        console.log(penaksir.value);
        penaksirLoading.value = false;
    });
};

const crntPage = ref(1);
const perPage = 10;

const totalPages = computed(() => Math.ceil(penaksir.value.length / perPage));

const pgntPenaksir = computed(() => {
    const start = (crntPage.value - 1) * perPage;
    const end = Math.min(start + perPage, searchPenaksir.value.length);
    return searchPenaksir.value.slice(start, end).map((pnk, index) => ({
        ...pnk,
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

const searchIPenaksir = ref("");
const searchPenaksir = computed(() => {
    if (!searchIPenaksir.value) {
        return penaksir.value;
    }
    const lowerCase = searchIPenaksir.value.toLowerCase();
    if (penaksir.value) {
        return penaksir.value.filter((pnk) => {
            return (
                pnk.id.toLowerCase().includes(lowerCase) ||
                pnk.name.toLowerCase().includes(lowerCase) ||
                pnk.posko.toLowerCase().includes(lowerCase) ||
                pnk.cabang.toLowerCase().includes(lowerCase)
            );
        });
    } else {
        return [];
    }
});

//sync form field
const poskoField = ref("")
const cabangField = ref("")
const syncFormField = () => {
    form.posko = poskoField.value
    form.cabang = cabangField.value
}
//end

//store user
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
        syncFormField(); // Synchronize form fields if necessary

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
                    loader: "bg-white" // Tailwind class for content text color
                },
                didOpen: () => {
                    Swal.showLoading(); // Show loading spinner
                },
            });

            // Perform the API request
            await axios.post(route("storePenaksir"), form);

            // Close the loading alert and show success message
            Swal.fire({
                title: "Berhasil",
                text: "Penaksir Ditambahkan",
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
            penaksirData(); // Refresh the penaksir data
            form.reset(); // Reset the form
            modal.addPenaksir = false; // Close the modal
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
//end

//button update disabled
const formMatch = ref(false);
watch([() => form.name], (newName) => {
    formMatch.value = newName == formDtl.name ? true : false;
});
//end

//update user
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
                    loader: "bg-white" // Tailwind class for content text color
                },
                didOpen: () => {
                    Swal.showLoading(); // Show loading spinner
                },
            });

            // Perform the API request
            await axios.post(route("updatePenaksir"), form);

            // Close the loading alert and show success message
            Swal.fire({
                title: "Berhasil",
                text: "Penaksir Diperbarui",
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
            penaksirData(); // Refresh the penaksir data
            formDtlReset(); // Reset the form details
            modal.detailPenaksir = false; // Close the modal
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
//end
</script>

<template>
    <Head title="Penaksir" />
    <AdminLayout>
        <template #titlePage>
            <TitlePage value="Data Penaksir" />
        </template>
        <template #content>
            <main class="p-6 bg-gray-100 flex-grow overflow-y-auto">
                <div class="bg-white p-4 rounded-lg shadow">
                    <div class="flex justify-between items-center mb-4">
                        <div class="pr-2">
                            <SearchInput
                                label="Cari Data"
                                v-model="searchIPenaksir"
                                placeholder="Cari"
                            ></SearchInput>
                        </div>
                        <div class="ml-4">
                            <div class="text-black">
                                <button
                                    class="mt-4 ml-2 bg-sky-700 text-white py-1 px-4 rounded"
                                    @click="modalAddPenaksir()"
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
                                        id
                                    </th>
                                    <th
                                        scope="col"
                                        class="text-center text-md font-sans uppercase pb-3 text-white"
                                    >
                                        nama
                                    </th>
                                    <th
                                        scope="col"
                                        class="text-center text-md font-sans uppercase pb-3 text-white"
                                    >
                                        posko
                                    </th>
                                    <th
                                        scope="col"
                                        class="text-center text-md font-sans uppercase pb-3 text-white"
                                    >
                                        cabang
                                    </th>
                                    <th
                                        scope="col"
                                        class="text-center text-md font-sans uppercase pb-3 text-white"
                                    >
                                        detail
                                    </th>
                                </tr>
                            </thead>
                            <tbody v-if="!penaksirLoading">
                                <tr
                                    v-for="(d, index) in pgntPenaksir"
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
                                        {{ d.id }}
                                    </td>
                                    <td
                                        class="whitespace-nowrap text-sm text-center text-black py-2"
                                    >
                                        {{ d.name }}
                                    </td>
                                    <td
                                        class="whitespace-nowrap text-sm text-center text-black py-2"
                                    >
                                        {{ d.posko }}
                                    </td>
                                    <td
                                        class="whitespace-nowrap text-sm text-center text-black py-2"
                                    >
                                        {{ d.cabang }}
                                    </td>
                                    <td
                                        class="whitespace-nowrap text-sm text-center text-black py-2"
                                    >
                                        <a @click="modalDetailPenaksir(d)"
                                            ><img
                                                class="m-auto cursor-pointer"
                                                src="/gadaiemas/storage/icon/info.png"
                                                title="Detail"
                                        /></a>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                        <div class="w-full text-center mt-2">
                            <span
                                class="text-center text-xl text-white"
                                v-if="penaksirLoading"
                                >Sedang Memuat...</span
                            >
                        </div>
                        <div class="w-full text-center mt-2">
                            <span 
                                class="text-center text-xl text-white"
                                v-if="!penaksirLoading && isPenaksirEmpty"
                                >Tidak Ada Data</span
                            >
                        </div>
                        <div class="w-full text-center mt-2">
                            <span 
                                class="text-center text-xl text-white"
                                v-if="
                                    !penaksirLoading && 
                                    !isPenaksirEmpty &&
                                    searchPenaksir.length < 1"
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
                        :show="modal.addPenaksir"
                        @close="[(modal.addPenaksir = false), (form.errors = {})]"
                        maxWidth="2xl"
                        classHeader="bg-sky-700 text-white"
                    >
                        <template #title> Tambah Penaksir </template>
                        <template #content>
                            <div class="mt-6 grid grid-cols-2 gap-4">
                                <div class="sm:col-span-1 pt-5">
                                    <h1
                                        class="tracking-wide block mb-2 text-xs font-bold text-gray-900 uppercase"
                                    >
                                        Nama :
                                    </h1>
                                </div>
                                <div class="sm:col-span-1">
                                    <CustTextInput
                                        :requiredIndicator="false"
                                        id="name"
                                        type="text"
                                        name="name"
                                        v-model="form.name"
                                    />
                                    <div v-if="form.errors.name">
                                        <span class="text-sm text-red-600"
                                            >*{{
                                                form.errors.name[0]
                                            }}</span
                                        >
                                    </div>
                                </div>
                                <div class="sm:col-span-1 pt-5">
                                    <h1
                                        class="tracking-wide block mb-2 text-xs font-bold text-gray-900 uppercase"
                                    >
                                        Posko :
                                    </h1>
                                </div>
                                <div class="sm:col-span-1">
                                    <CustTextInput
                                        :requiredIndicator="false"
                                        id="posko"
                                        type="text"
                                        name="posko"
                                        v-model="poskoField"
                                        disabled="true"
                                    />
                                </div>
                                <div class="sm:col-span-1 pt-5">
                                    <h1
                                        class="tracking-wide block mb-2 text-xs font-bold text-gray-900 uppercase"
                                    >
                                        Cabang :
                                    </h1>
                                </div>
                                <div class="sm:col-span-1">
                                    <CustTextInput
                                        :requiredIndicator="false"
                                        id="cabang"
                                        type="text"
                                        name="cabang"
                                        v-model="cabangField"
                                        disabled="true"
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
                    <Modal
                        :show="modal.detailPenaksir"
                        @close="
                            [(modal.detailPenaksir = false), formDtlReset()]
                        "
                        maxWidth="2xl"
                        classHeader="bg-sky-700 text-white"
                    >
                        <template #title> Detail User </template>
                        <template #content>
                            <div class="mt-6 grid grid-cols-2 gap-4">
                                <div class="sm:col-span-1 pt-5">
                                    <h1
                                        class="tracking-wide block mb-2 text-xs font-bold text-gray-900 uppercase"
                                    >
                                        ID :
                                    </h1>
                                </div>
                                <div class="sm:col-span-1">
                                    <CustTextInput
                                        :requiredIndicator="false"
                                        id="id_dtl"
                                        type="text"
                                        name="id_dtl"
                                        v-model="form.id"
                                        disabled="true"
                                    />
                                </div>
                                <div class="sm:col-span-1 pt-5">
                                    <h1
                                        class="tracking-wide block mb-2 text-xs font-bold text-gray-900 uppercase"
                                    >
                                        Nama :
                                    </h1>
                                </div>
                                <div class="sm:col-span-1">
                                    <CustTextInput
                                        :requiredIndicator="false"
                                        id="name_dtl"
                                        type="text"
                                        name="name_dtl"
                                        v-model="form.name"
                                    />
                                </div>
                                <div class="sm:col-span-1 pt-5">
                                    <h1
                                        class="tracking-wide block mb-2 text-xs font-bold text-gray-900 uppercase"
                                    >
                                        Posko :
                                    </h1>
                                </div>
                                <div class="sm:col-span-1">
                                    <CustTextInput
                                        :requiredIndicator="false"
                                        id="posko_dtl"
                                        type="text"
                                        name="posko_dtl"
                                        v-model="form.posko"
                                        disabled="true"
                                    />
                                </div>
                                <div class="sm:col-span-1 pt-5">
                                    <h1
                                        class="tracking-wide block mb-2 text-xs font-bold text-gray-900 uppercase"
                                    >
                                        Cabang :
                                    </h1>
                                </div>
                                <div class="sm:col-span-1">
                                    <CustTextInput
                                        :requiredIndicator="false"
                                        id="cabang_dtl"
                                        type="text"
                                        name="cabang_dtl"
                                        v-model="form.cabang"
                                        disabled="true"
                                    />
                                </div>
                            </div>
                            <div class="flex justify-end items-center mb-7">
                                <div class="ml-4">
                                    <div class="text-black">
                                        <button
                                            @click="update()"
                                            :disabled="formMatch"
                                            :class="{ hidden: formMatch }"
                                            :title="
                                                formMatch
                                                    ? 'You dont changed any data'
                                                    : 'Click to update data'
                                            "
                                            class="mt-7 ml-2 bg-green-700 text-white py-1 px-4 rounded"
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
