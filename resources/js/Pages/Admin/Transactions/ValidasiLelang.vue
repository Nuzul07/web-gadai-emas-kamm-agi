<script setup>
import { ref, onMounted, watch, computed, reactive } from "vue";
import AdminLayout from "@/Layouts/AdminLayout.vue";
import TitlePage from "@/Components/TitlePage.vue";
import SearchInput from "@/Components/SearchInput.vue";
import CustTextInput from "@/Components/CustTextInput.vue";
import CustPercentInput from "@/Components/CustPercentInput.vue";
import CustDetail from "@/Components/CustDetail.vue";
import InputFormatIDR from "@/Components/InputFormatIDR.vue";
import NestedSelect from "@/Components/NestedSelect.vue";
import TextAreaInput from "@/Components/TextAreaInput.vue";
import UploadImage from "@/Components/UploadImage.vue";
import Modal from "@/Components/DialogModal.vue";
import { Head, useForm } from "@inertiajs/vue3";
import axios, { formToJSON } from "axios";
import Swal from "sweetalert2";
import dayjs from "dayjs";
import Lelang from "./Lelang.vue";

onMounted(() => {
    lelangTemp.value.forEach((item) => {
        if (!("isChecked" in item)) {
            item.isChecked = false;
        }
    });
});

defineProps({
    errors: Object,
});

const field = reactive({
    no_lelang: "",
    nama_pembeli: "",
    no_hp: "",
    alamat_pembeli: "",
});

const modal = reactive({
    lelang: false,
});

const modalLelang = () => {
    modal.lelang = true;
    lelangData();
};

const lelang = ref([]);
const lelangLoading = ref(false);
const lelangData = async () => {
    lelangLoading.value = true;
    await axios.get(route("getLelang")).then((res) => {
        const data = res.data;
        lelang.value = data;
        lelangLoading.value = false;
    });
};

const crntPage = ref(1);
const perPage = 10;

const totalPages = computed(() => Math.ceil(lelang.value.length / perPage));

const pgntLelang = computed(() => {
    const start = (crntPage.value - 1) * perPage;
    const end = Math.min(start + perPage, searchLelang.value.length);
    return searchLelang.value.slice(start, end).map((lelang, index) => ({
        ...lelang,
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

const searchILg = ref("");
const searchLelang = computed(() => {
    if (!searchILg.value) {
        return lelang.value;
    }
    const lowerCase = searchILg.value.toLowerCase();
    if (lelang.value) {
        return lelang.value.filter((lg) => {
            return (
                lg.NOMOR_LELANG.toLowerCase().includes(lowerCase)
            );
        });
    } else {
        return [];
    }
});

const selectDataLel = async (d) => {
    try {
        // Show a success message using Swal
        Swal.fire({
            title: "Berhasil",
            text: "Memilih Permohonan Lelang",
            icon: "success",
            iconColor: "#FFFFFF",
            background: "#0369a1",
            showConfirmButton: false,
            timer: 1300,
            customClass: {
                title: "text-white",
                popup: "text-white",
            },
        });

        // Send a POST request to fetch records related to the selected NOMOR_LELANG
        const response = await axios.post(route("getLelangByNomor"), {
            nomorLelang: d.NOMOR_LELANG,
        });

        if (response.data) {
            const lelangData = response.data;

            // Assign the fetched data to the form fields or use it as needed
            field.no_lelang = lelangData[0].NOMOR_LELANG; // Example of using first record
            field.nama_pembeli = lelangData[0].NAMA_PEMBELI;
            field.no_hp = lelangData[0].NO_HP;
            field.alamat_pembeli = lelangData[0].ALAMAT_PEMBELI;

            // You can handle the entire list of related records here

            selectLelang(lelangData);
        }

        // Close the modal
        modal.lelang = false;
    } catch (error) {
        console.error("Error fetching lelang records:", error);
        // Handle error if necessary
    }
};

const lelangTemp = ref([]);

const selectLelang = (lelang) => {
    const exists = lelangTemp.value
        .flat()
        .find((item) => item.Kode_barang === lelang.Kode_barang);

    if (!exists) {
        if(lelangTemp.value.length > 0) {
            lelangTemp.value.length = 0
        }
        lelangTemp.value.push(lelang);
    } else {
        Swal.fire({
            title: "Terjadi kesalahan",
            text: "Permohonan tersebut sudah dipilih",
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

const formatRupiah = (value) => {
    const numberFormat = new Intl.NumberFormat("id-ID", {
        style: "currency",
        currency: "IDR",
        minimumFractionDigits: 0, // No decimal places
        maximumFractionDigits: 0, // No decimal places
    });
    return numberFormat.format(value);
};

// Function to update INT_1 based on checkbox
const updateINT1 = (item) => {
    if (item.isChecked) {
        item.INT_1 = "1";
    } else {
        item.INT_1 = "0";
    }
};

const update = async () => {
    const header = {
        headers: {
            "Content-Type": "application/json",
        },
    };

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
        // Format the data before submission

        try {
            // Show loading state
            Swal.fire({
                title: "Memproses",
                text: "Mohon Tunggu",
                background: "#0369a1",
                allowOutsideClick: false,
                showConfirmButton: false,
                customClass: {
                    title: "text-white",
                    popup: "text-white",
                    loader: "bg-white",
                },
                didOpen: () => {
                    Swal.showLoading();
                },
            });
            // Wrap the array in an object to give it the desired name
            const lelangArray = {
                lelangTemp: lelangTemp.value.flat().map((item) => ({
                    kode_barang: item.Kode_barang,
                    int_1: item.INT_1,
                })),
            };
            // Perform the API request
            await axios.post(route("updateLelang"), lelangArray, header);

            Swal.fire({
                title: "Berhasil",
                text: "Memvalidasi permohonan lelang",
                icon: "success",
                iconColor: "#FFFFFF",
                background: "#0369a1",
                showConfirmButton: false,
                timer: 1300,
                customClass: {
                    title: "text-white",
                    popup: "text-white",
                },
            });
            resetFormLelang();
        } catch (error) {
            console.log("error", error);

            if (error.response && error.response.status === 422) {
                form.errors = error.response.data.errors || {};
            }

            Swal.fire({
                title: "Terjadi kesalahan",
                text: "Periksa kembali inputannya",
                icon: "error",
                iconColor: "#FFFFFF",
                background: "#ba1c1c",
                showConfirmButton: true,
                confirmButtonColor: "#FFFFFF",
                customClass: {
                    title: "text-white",
                    popup: "text-white",
                    confirmButton: "text-black",
                },
            });
        }
    }
};

const resetFormLelang = () => {
    field.no_lelang = "";
    field.nama_pembeli = "";
    field.no_hp = "";
    field.alamat_pembeli = "";
    lelangTemp.value = [];
};
</script>
<template>
    <Head title="Validasi Lelang" />
    <AdminLayout>
        <template #titlePage>
            <TitlePage value="Validasi Lelang" />
        </template>
        <template #content>
            <main class="p-6 bg-gray-100 flex-grow overflow-y-auto">
                <div class="mt-6">
                    <div class="bg-white p-4 rounded-lg shadow">
                        <div class="grid grid-cols-4 gap-2">
                            <div class="sm:col-span-4">
                                <CustTextInput
                                    label="No Pengajuan"
                                    id="no_lelang"
                                    type="text"
                                    name="no_lelang"
                                    v-model="field.no_lelang"
                                    :readonly="true"
                                    subClass="cursor-pointer"
                                    @click="modalLelang()"
                                />
                            </div>
                            <div class="sm:col-span-1">
                                <CustTextInput
                                    label="Nama Pembeli"
                                    id="nama_pembeli"
                                    type="text"
                                    name="nama_pembeli"
                                    v-model="field.nama_pembeli"
                                    :disabled="true"
                                />
                            </div>
                            <div class="sm:col-span-1">
                                <CustTextInput
                                    label="No HP"
                                    id="no_hp"
                                    type="text"
                                    name="no_hp"
                                    v-model="field.no_hp"
                                    :disabled="true"
                                />
                            </div>
                            <div class="sm:col-span-2">
                                <CustTextInput
                                    label="Alamat Pembeli"
                                    id="alamat_pembeli"
                                    type="text"
                                    name="alamat_pembeli"
                                    v-model="field.alamat_pembeli"
                                    :disabled="true"
                                />
                            </div>
                        </div>
                        <div class="my-1 col-span-4">
                            <hr class="border-2 border-gray-500" />
                        </div>
                        <Modal
                            :show="modal.lelang"
                            @close="[(modal.lelang = false)]"
                            maxWidth="5xl"
                            classHeader="bg-sky-700 text-white"
                        >
                            <template #title>
                                Daftar Permohonan Lelang</template
                            >
                            <template #content>
                                <div class="mt-6">
                                    <div class="pr-2-">
                                        <SearchInput
                                            label="Cari Data"
                                            v-model="searchILg"
                                            placeholder="Cari"
                                        ></SearchInput>
                                    </div>
                                    <div
                                        class="py-3 mt-7 rounded-lg bg-sky-700 shadow-lg"
                                    >
                                        <table
                                            class="min-w-full bg-sky-700 shadow-lg"
                                        >
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
                                                        nomor pengajuan
                                                    </th>
                                                    <th
                                                        scope="col"
                                                        class="text-center text-md font-sans uppercase pb-3 text-white"
                                                    >
                                                        tanggal input
                                                    </th>
                                                </tr>
                                            </thead>
                                            <tbody v-if="!lelangLoading">
                                                <tr
                                                    v-for="(
                                                        d, index
                                                    ) in pgntLelang"
                                                    :key="index"
                                                    class="odd:bg-white even:bg-amber-400"
                                                    @click="selectDataLel(d)"
                                                >
                                                    <td
                                                        class="whitespace-nowrap text-sm text-center text-black py-2 cursor-pointer"
                                                    >
                                                        {{ d.no }}
                                                    </td>
                                                    <td
                                                        class="whitespace-nowrap text-sm text-center text-black py-2 cursor-pointer"
                                                    >
                                                        {{ d.NOMOR_LELANG }}
                                                    </td>
                                                    <td
                                                        class="whitespace-nowrap text-sm text-center text-black py-2 cursor-pointer"
                                                    >
                                                        {{ d.TANGGAL_INPUT }}
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                        <div class="w-full text-center mt-2">
                                            <span
                                                class="text-center text-xl text-white"
                                                v-if="lelangLoading"
                                                >Sedang Memuat...</span
                                            >
                                        </div>
                                        <div class="w-full text-center mt-2">
                                            <span
                                                class="text-center text-xl text-white"
                                                v-if="
                                                    !lelangLoading &&
                                                    lelang.length < 1
                                                "
                                                >Tidak Ada Data</span
                                            >
                                        </div>
                                    </div>
                                    <div
                                        class="flex space-x-2 mt-2 mb-5"
                                        v-if="!lelangLoading"
                                    >
                                        <button @click="prevPage">
                                            <i
                                                ><img
                                                src="/gadaiemas/storage/icon/previous.png"
                                                title="Previous"</i
                                            >
                                        </button>
                                        <p class="p-1">
                                            Page {{ crntPage }} of
                                            {{ totalPages }}
                                        </p>
                                        <button @click="nextPage">
                                            <i
                                                ><img
                                                    src="/gadaiemas/storage/icon/next.png"
                                                    title="Next"
                                            /></i>
                                        </button>
                                    </div>
                                </div>
                            </template>
                        </Modal>
                        <div class="py-3 mt-3 rounded-lg bg-sky-700 shadow-lg">
                            <table
                                v-if="lelangTemp.length > 0"
                                class="min-w-full bg-sky-700 shadow-lg"
                            >
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
                                            no sbg
                                        </th>
                                        <th
                                            scope="col"
                                            class="text-center text-md font-sans uppercase pb-3 text-white"
                                        >
                                            nama nasabah
                                        </th>
                                        <th
                                            scope="col"
                                            class="text-center text-md font-sans uppercase pb-3 text-white"
                                        >
                                            nama barang
                                        </th>
                                        <th
                                            scope="col"
                                            class="text-center text-md font-sans uppercase pb-3 text-white"
                                        >
                                            harga awal
                                        </th>
                                        <th
                                            scope="col"
                                            class="text-center text-md font-sans uppercase pb-3 text-white"
                                        >
                                            nilai buku
                                        </th>
                                        <th
                                            scope="col"
                                            class="text-center text-md font-sans uppercase pb-3 text-white"
                                        >
                                            harga penawaran
                                        </th>
                                        <th
                                            scope="col"
                                            class="text-center text-md font-sans uppercase pb-3 text-white"
                                        >
                                            approve
                                        </th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr
                                        v-for="(item, idx) in lelangTemp.flat()"
                                        :key="idx"
                                        class="odd:bg-white even:bg-amber-400"
                                    >
                                        <td
                                            class="whitespace-nowrap text-sm text-center text-black py-2"
                                        >
                                            {{ idx + 1 }}
                                        </td>
                                        <td
                                            class="whitespace-nowrap text-sm text-center text-black py-2"
                                        >
                                            {{ item.No_sbg }}
                                        </td>
                                        <td
                                            class="whitespace-nowrap text-sm text-center text-black py-2"
                                        >
                                            {{ item.NAMA_KONS }}
                                        </td>
                                        <td
                                            class="whitespace-nowrap text-sm text-center text-black py-2"
                                        >
                                            {{ item.NAMA_BARANG }}
                                        </td>
                                        <td
                                            class="whitespace-nowrap text-sm text-center text-black py-2"
                                        >
                                            {{
                                                formatRupiah(
                                                    item.barang.Taksiran
                                                )
                                            }}
                                        </td>
                                        <td
                                            class="whitespace-nowrap text-sm text-center text-black py-2"
                                        >
                                            {{ formatRupiah(item.NILAI_BUKU) }}
                                        </td>
                                        <td
                                            class="whitespace-nowrap text-sm text-center text-black py-2"
                                        >
                                            {{
                                                formatRupiah(item.NILAI_LELANG)
                                            }}
                                        </td>
                                        <td
                                            class="whitespace-nowrap text-sm text-center text-black py-2"
                                        >
                                            <input
                                                class="cursor-pointer"
                                                type="checkbox"
                                                v-model="item.isChecked"
                                                @change="updateINT1(item)"
                                            />
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                            <div class="w-full text-center">
                                <span
                                    class="text-center text-xl text-white"
                                    v-if="lelangTemp.length < 1"
                                    >Belum Memilih Data</span
                                >
                            </div>
                        </div>
                    </div>
                    <div class="flex justify-end items-center mt-5">
                        <div class="ml-4">
                            <div class="text-black">
                                <button
                                    @click="resetFormLelang()"
                                    class="ml-2 bg-red-700 text-white py-1 px-4 rounded"
                                >
                                    Reset
                                </button>
                                <button
                                    @click="update()"
                                    class="ml-2 bg-green-700 text-white py-1 px-4 rounded"
                                >
                                    Validasi
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </main>
        </template>
    </AdminLayout>
</template>
