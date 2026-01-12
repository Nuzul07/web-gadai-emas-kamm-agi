<script setup>
import { ref, onMounted, watch, computed } from "vue";
import AdminLayout from "@/Layouts/AdminLayout.vue";
import TitlePage from "@/Components/TitlePage.vue";
import CustPercentInput from "@/Components/CustPercentInput.vue";
import { Head, useForm } from "@inertiajs/vue3";
import axios from "axios";
import Swal from "sweetalert2";
import { selectedTipeGadai } from "@/src/store";

onMounted(() => {
    checkTipeGadai();
    golonganData();
});

defineProps({
    errors: Object,
});

watch(selectedTipeGadai, (tipeGadai) => { 
    if (tipeGadai === 'motor' || tipeGadai === 'mobil') {
        window.location.replace(route("sewaModal"))
    }
    else if (tipeGadai === 'emas') {
        window.location.replace(route('bagianLain'))
    }
})

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
            window.location.href = route("gadaiDashboard");
        });
    }
};

const form = useForm({
    golongan: "",
    denda1_7: "",
    denda8_15: "",
    denda16_30: "",
    byadmin: "",
    patok_taksiran: "",
    sewa_modal_15: "",
    sewa_modal16_30: "",
    sewa_modal31_45: "",
    sewa_modal46_60: "",
    sewa_modal61_75: "",
    sewa_modal76_90: "",
    sewa_modal91_105: "",
    sewa_modal105_atas: "",
});

const golongan = ref([]);
const golonganData = async () => {
    await axios.get(route("getGolongan")).then((res) => {
        const data = res.data;
        golongan.value = data;
        updateGolonganOptions();
    });
};

const golonganOptions = ref([]);

const updateGolonganOptions = () => {
    const GolonganSet = new Set(
        golongan.value.map((item) => item.GOLONGAN) // assuming you need 'value' from the item
    );

    // Convert Set to array and update asalBarangOptions
    golonganOptions.value = [...GolonganSet];
};

// const dropdownStates = ref({
//     golonganState: false,
// });

const formatDecimal = (value) => {
    const number = parseFloat(value);
    if (isNaN(number)) {
        return value;
    }
    return number.toFixed(1);
};

const resetCustInput = () => {
    form.denda1_7 = "";
    form.denda8_15 = "";
    form.denda16_30 = "";
    form.byadmin = "";
    form.patok_taksiran = "";
    form.sewa_modal_15 = "";
    form.sewa_modal16_30 = "";
    form.sewa_modal31_45 = "";
    form.sewa_modal46_60 = "";
    form.sewa_modal61_75 = "";
    form.sewa_modal76_90 = "";
    form.sewa_modal91_105 = "";
    form.sewa_modal105_atas = "";
};

const selectDataGolongan = (retrieve) => {
    const fieldMapping = {
        denda1_7: "PERSEN_DENDA1_7HARI",
        denda8_15: "PERSEN_DENDA8_15HARI",
        denda16_30: "PERSEN_DENDA16_30HARI",
        byadmin: "PERSEN_BYADMIN",
        patok_taksiran: "PERSEN_PATOK_TAKSIRAN",
        sewa_modal_15: "SEWA_MODAL_15HARI",
        sewa_modal16_30: "SEWA_MODAL16_30HARI",
        sewa_modal31_45: "SEWA_MODAL31_45HARI",
        sewa_modal46_60: "SEWA_MODAL46_60HARI",
        sewa_modal61_75: "SEWA_MODAL61_75HARI",
        sewa_modal76_90: "SEWA_MODAL76_90HARI",
        sewa_modal91_105: "SEWA_MODAL91_105HARI",
        sewa_modal105_atas: "SEWA_MODAL_105_ATAS_HARI",
    };

    Object.keys(fieldMapping).forEach((formField) => {
        const retrieveField = fieldMapping[formField];

        // Set the form field value, handling null/undefined values
        form[formField] =
            retrieve[retrieveField] != null
                ? formatDecimal(retrieve[retrieveField] * 100)
                : "";
    });
};

const formatBackToDecimal = () => {
    const fields = [
        "denda1_7",
        "denda8_15",
        "denda16_30",
        "byadmin",
        "patok_taksiran",
        "sewa_modal_15",
        "sewa_modal16_30",
        "sewa_modal31_45",
        "sewa_modal46_60",
        "sewa_modal61_75",
        "sewa_modal76_90",
        "sewa_modal91_105",
        "sewa_modal105_atas",
    ];

    fields.forEach((field) => {
        form[field] = formatDecimal(form[field]);
    });
};

const inputToggle = ref(true);
watch(
    () => form.golongan,
    (newValue) => {
        if (newValue !== "") {
            const retrieve = golongan.value.find(
                (g) => g.GOLONGAN === form.golongan
            );
            selectDataGolongan(retrieve);
            inputToggle.value = false;
        } else {
            resetCustInput();
            inputToggle.value = true;
        }
    }
);

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
                    popup: "text-white", // Tailwind class for content text color
                    loader: "bg-white",
                },
                didOpen: () => {
                    Swal.showLoading(); // Show loading spinner
                },
            });

            // Perform the API request
            await axios.post(route("updateBagianLain"), form);

            // Close the loading alert and show success message
            Swal.fire({
                title: "Berhasil",
                text: "Komponen Hitung Berhasil diperbaharui",
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

            // Refresh data and format the input
            golonganData(); // Refresh the data
            formatBackToDecimal(); // Format the input to decimal
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
    <Head title="Komponen Perhitungan" />
    <AdminLayout>
        <template #titlePage>
            <TitlePage value="Komponen Perhitungan" />
        </template>
        <template #content>
            <main class="p-6 bg-gray-100 flex-grow overflow-y-auto">
                <div class="mt-6">
                    <div class="bg-white p-4 rounded-lg shadow">
                        <div class="mt-6 grid grid-cols-3 gap-4">
                            <div class="sm:col-span-1 pt-5">
                                <h1
                                    class="tracking-wide block mb-2 text-xs font-bold text-gray-900 uppercase"
                                >
                                    Golongan :
                                </h1>
                            </div>
                            <div class="sm:col-span-2 flex flex-row space-x-5">
                                <div
                                    v-for="(d, index) in golongan"
                                    :key="index"
                                    :class="[
                                        'box-border h-12 w-14 p-4 border-4 border-amber-500 cursor-pointer flex items-center justify-center',
                                        {
                                            'bg-sky-700 text-white':
                                                form.golongan === d.GOLONGAN,
                                        },
                                    ]"
                                    @click="form.golongan = d.GOLONGAN"
                                    @blur="form.golongan = ''"
                                >
                                    <span>{{ d.GOLONGAN }}</span>
                                </div>
                            </div>
                            <div class="sm:col-span-1 pt-5">
                                <h1
                                    class="tracking-wide block mb-2 text-xs font-bold text-gray-900 uppercase"
                                >
                                    Denda 1-7 Hari :
                                </h1>
                            </div>
                            <div class="sm:col-span-2">
                                <CustPercentInput
                                    :required-indicator="false"
                                    id="denda1_7"
                                    type="text"
                                    name="denda1_7"
                                    v-model="form.denda1_7"
                                    :disabled="inputToggle"
                                />
                            </div>
                            <div class="sm:col-span-1 pt-5">
                                <h1
                                    class="tracking-wide block mb-2 text-xs font-bold text-gray-900 uppercase"
                                >
                                    Denda 8-15 Hari :
                                </h1>
                            </div>
                            <div class="sm:col-span-2">
                                <CustPercentInput
                                    :requiredIndicator="false"
                                    id="denda8_15"
                                    type="text"
                                    name="denda8_15"
                                    v-model="form.denda8_15"
                                    :disabled="inputToggle"
                                />
                            </div>
                            <div class="sm:col-span-1 pt-5">
                                <h1
                                    class="tracking-wide block mb-2 text-xs font-bold text-gray-900 uppercase"
                                >
                                    Denda 16-30 Hari :
                                </h1>
                            </div>
                            <div class="sm:col-span-2">
                                <CustPercentInput
                                    :requiredIndicator="false"
                                    id="denda16_30"
                                    type="text"
                                    name="denda16_30"
                                    v-model="form.denda16_30"
                                    :disabled="inputToggle"
                                />
                            </div>
                            <div class="sm:col-span-1 pt-5">
                                <h1
                                    class="tracking-wide block mb-2 text-xs font-bold text-gray-900 uppercase"
                                >
                                    Biaya Admin :
                                </h1>
                            </div>
                            <div class="sm:col-span-2">
                                <CustPercentInput
                                    :required-indicator="false"
                                    id="byadmin"
                                    type="text"
                                    name="byadmin"
                                    v-model="form.byadmin"
                                    :disabled="inputToggle"
                                />
                            </div>
                            <div class="sm:col-span-1 pt-5">
                                <h1
                                    class="tracking-wide block mb-2 text-xs font-bold text-gray-900 uppercase"
                                >
                                    Patok Taksiran :
                                </h1>
                            </div>
                            <div class="sm:col-span-2">
                                <CustPercentInput
                                    :required-indicator="false"
                                    id="patok_taskiran"
                                    type="text"
                                    name="patok_taskiran"
                                    v-model="form.patok_taksiran"
                                    :disabled="inputToggle"
                                />
                            </div>
                            <div class="sm:col-span-1 pt-5">
                                <h1
                                    class="tracking-wide block mb-2 text-xs font-bold text-gray-900 uppercase"
                                >
                                    Sewa Modal 1-15 Hari :
                                </h1>
                            </div>
                            <div class="sm:col-span-2">
                                <CustPercentInput
                                    :required-indicator="false"
                                    id="sewa_modal1_15"
                                    type="text"
                                    name="sewa_modal1_15"
                                    v-model="form.sewa_modal_15"
                                    :disabled="inputToggle"
                                />
                            </div>
                            <div class="sm:col-span-1 pt-5">
                                <h1
                                    class="tracking-wide block mb-2 text-xs font-bold text-gray-900 uppercase"
                                >
                                    Sewa Modal 16-30 Hari :
                                </h1>
                            </div>
                            <div class="sm:col-span-2">
                                <CustPercentInput
                                    :required-indicator="false"
                                    id="sewa_modal16_30"
                                    type="text"
                                    name="sewa_modal16_30"
                                    v-model="form.sewa_modal16_30"
                                    :disabled="inputToggle"
                                />
                            </div>
                            <div class="sm:col-span-1 pt-5">
                                <h1
                                    class="tracking-wide block mb-2 text-xs font-bold text-gray-900 uppercase"
                                >
                                    Sewa Modal 31-45 Hari :
                                </h1>
                            </div>
                            <div class="sm:col-span-2">
                                <CustPercentInput
                                    :required-indicator="false"
                                    id="sewa_modal31_45"
                                    type="text"
                                    name="sewa_modal31_45"
                                    v-model="form.sewa_modal31_45"
                                    :disabled="inputToggle"
                                />
                            </div>
                            <div class="sm:col-span-1 pt-5">
                                <h1
                                    class="tracking-wide block mb-2 text-xs font-bold text-gray-900 uppercase"
                                >
                                    Sewa Modal 46-60 Hari :
                                </h1>
                            </div>
                            <div class="sm:col-span-2">
                                <CustPercentInput
                                    :required-indicator="false"
                                    id="sewa_modal46_30"
                                    type="text"
                                    name="sewa_modal46_30"
                                    v-model="form.sewa_modal46_60"
                                    :disabled="inputToggle"
                                />
                            </div>
                            <div class="sm:col-span-1 pt-5">
                                <h1
                                    class="tracking-wide block mb-2 text-xs font-bold text-gray-900 uppercase"
                                >
                                    Sewa Modal 61-75 Hari :
                                </h1>
                            </div>
                            <div class="sm:col-span-2">
                                <CustPercentInput
                                    :required-indicator="false"
                                    id="sewa_modal61_75"
                                    type="text"
                                    name="sewa_modal61_75"
                                    v-model="form.sewa_modal61_75"
                                    :disabled="inputToggle"
                                />
                            </div>
                            <div class="sm:col-span-1 pt-5">
                                <h1
                                    class="tracking-wide block mb-2 text-xs font-bold text-gray-900 uppercase"
                                >
                                    Sewa Modal 76-90 Hari :
                                </h1>
                            </div>
                            <div class="sm:col-span-2">
                                <CustPercentInput
                                    :required-indicator="false"
                                    id="sewa_modal76_90"
                                    type="text"
                                    name="sewa_modal76_90"
                                    v-model="form.sewa_modal76_90"
                                    :disabled="inputToggle"
                                />
                            </div>
                            <div class="sm:col-span-1 pt-5">
                                <h1
                                    class="tracking-wide block mb-2 text-xs font-bold text-gray-900 uppercase"
                                >
                                    Sewa Modal 91-105 Hari :
                                </h1>
                            </div>
                            <div class="sm:col-span-2">
                                <CustPercentInput
                                    :required-indicator="false"
                                    id="sewa_modal91_105"
                                    type="text"
                                    name="sewa_modal91_105"
                                    v-model="form.sewa_modal91_105"
                                    :disabled="inputToggle"
                                />
                            </div>
                            <div class="sm:col-span-1 pt-5">
                                <h1
                                    class="tracking-wide block mb-2 text-xs font-bold text-gray-900 uppercase"
                                >
                                    Sewa Modal > 105 Hari :
                                </h1>
                            </div>
                            <div class="sm:col-span-2">
                                <CustPercentInput
                                    :required-indicator="false"
                                    id="sewa_modal105_atas"
                                    type="text"
                                    name="sewa_modal105_atas"
                                    v-model="form.sewa_modal105_atas"
                                    :disabled="inputToggle"
                                />
                            </div>
                        </div>
                        <div class="flex justify-end items-center">
                            <div class="ml-4">
                                <div class="text-black">
                                    <button
                                        @click="update()"
                                        :title="'Click to update data'"
                                        class="mt-7 ml-2 bg-green-700 text-white py-1 px-4 rounded"
                                    >
                                        Ubah
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </main>
        </template>
    </AdminLayout>
</template>