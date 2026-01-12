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
    checkTipeGadai()
    sewaModalData()
})

defineProps({
    errors: Object,
});

watch(selectedTipeGadai, (tipeGadai) => { 
    if (tipeGadai === 'emas') {
        window.location.replace(route("bagianLain"))
    } else if (tipeGadai === 'motor' || tipeGadai === 'mobil') {
        window.location.replace(route("sewaModal"))
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
            html: "Tuhkan belum dipilih produk gadainya 😠 <br> Tolong pilih dulu yaa, Terima Kasih!!!"
        }).then(() => {
            window.location.href = route('gadaiDashboard')
        })
    } else if (selectedTipeGadai.value === 'emas') {
        window.location.href = route('bagianLain')
    }
}

const form = useForm({
    type_kendaraan: selectedTipeGadai.value === 'motor' ? 'motor' : 'mobil',
    bunga_15: "",
    bunga_30: "",
    bunga_60: "",
    bunga_90: "",
    bunga_120: "",
    admin_15: "",
    admin_30: "",
    admin_60: "",
    admin_90: "",
    admin_120: ""
});

const sewaModal = ref([])
const sewaModalData = async () => {
    await axios.get(route("getSewaModal")).then((res) => {
        const data = res.data
        sewaModal.value = data
        const retrieve = sewaModal.value.find((sm) => sm.type_kendaraan === selectedTipeGadai.value)
        selectDataSewaModal(retrieve)
    })
}

const formatDecimal = (value) => {
    const number = parseFloat(value)
    if (isNaN(number)) {
        return value
    }
    return number.toFixed(1)
}

const resetInput = () => {
    form.bunga_15 = ""
    form.bunga_30 = ""
    form.bunga_60 = ""
    form.bunga_90 = ""
    form.bunga_120 = ""
    form.admin_15 = ""
    form.admin_30 = ""
    form.admin_60 = ""
    form.admin_90 = ""
    form.admin_120 = ""
}

const selectDataSewaModal = (retrieve) => {
    const fieldMapping = {
        bunga_15: "bunga_15hari",
        bunga_30: "bunga_30hari",
        bunga_60: "bunga_60hari",
        bunga_90: "bunga_90hari",
        bunga_120: "bunga_120hari",
        admin_15: "admin_15hari",
        admin_30: "admin_30hari",
        admin_60: "admin_60hari",
        admin_90: "admin_90hari",
        admin_120: "admin_120hari",
    }

    Object.keys(fieldMapping).forEach((formField) => {
        const retrieveField = fieldMapping[formField]

        // Set the form field value, handling null/undefined values
        form[formField] = retrieve[retrieveField] != null
            ? formatDecimal(retrieve[retrieveField] * 100)
            : ""
    })
};

const formatBackToDecimal = () => {
    const fields = [
        "bunga_15",
        "bunga_30",
        "bunga_60",
        "bunga_90",
        "bunga_120",
        "admin_15",
        "admin_30",
        "admin_60",
        "admin_90",
        "admin_120",
    ]

    fields.forEach((field) => {
        form[field] = formatDecimal(form[field])
    })
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
                    loader: "bg-white"
                },
                didOpen: () => {
                    Swal.showLoading(); // Show loading spinner
                },
            });

            // Perform the API request
            await axios.post(route("updateSewaModal"), form)

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
            sewaModalData()
            formatBackToDecimal()

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
            <TitlePage value="Komponen Perhitungan Motor" v-if="selectedTipeGadai === 'motor'" />
            <TitlePage value="Komponen Perhitungan Mobil" v-if="selectedTipeGadai === 'mobil'" />
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
                                    Bunga 15 Hari :
                                </h1>
                            </div>
                            <div class="sm:col-span-2">
                                <CustPercentInput
                                    :required-indicator="false"
                                    id="bunga_15"
                                    type="text"
                                    name="bunga_15"
                                    v-model="form.bunga_15"
                                />
                            </div>
                            <div class="sm:col-span-1 pt-5">
                                <h1
                                    class="tracking-wide block mb-2 text-xs font-bold text-gray-900 uppercase"
                                >
                                    Bunga 30 Hari :
                                </h1>
                            </div>
                            <div class="sm:col-span-2">
                                <CustPercentInput
                                    :requiredIndicator="false"
                                    id="bunga_30"
                                    type="text"
                                    name="bunga_30"
                                    v-model="form.bunga_30"
                                />
                            </div>
                            <div class="sm:col-span-1 pt-5">
                                <h1
                                    class="tracking-wide block mb-2 text-xs font-bold text-gray-900 uppercase"
                                >
                                    Bunga 60 Hari :
                                </h1>
                            </div>
                            <div class="sm:col-span-2">
                                <CustPercentInput
                                    :requiredIndicator="false"
                                    id="bunga_60"
                                    type="text"
                                    name="bunga_60"
                                    v-model="form.bunga_60"
                                />
                            </div>
                            <div class="sm:col-span-1 pt-5">
                                <h1
                                    class="tracking-wide block mb-2 text-xs font-bold text-gray-900 uppercase"
                                >
                                    Bunga 90 Hari :
                                </h1>
                            </div>
                            <div class="sm:col-span-2">
                                <CustPercentInput
                                    :requiredIndicator="false"
                                    id="bunga_90"
                                    type="text"
                                    name="bunga_90"
                                    v-model="form.bunga_90"
                                />
                            </div>
                            <div class="sm:col-span-1 pt-5">
                                <h1
                                    class="tracking-wide block mb-2 text-xs font-bold text-gray-900 uppercase"
                                >
                                    Bunga 120 Hari :
                                </h1>
                            </div>
                            <div class="sm:col-span-2">
                                <CustPercentInput
                                    :requiredIndicator="false"
                                    id="bunga_120"
                                    type="text"
                                    name="bunga_120"
                                    v-model="form.bunga_120"
                                    :disabled="true"
                                />
                            </div>
                            <div class="sm:col-span-1 pt-5">
                                <h1
                                    class="tracking-wide block mb-2 text-xs font-bold text-gray-900 uppercase"
                                >
                                    Admin 15 Hari :
                                </h1>
                            </div>
                            <div class="sm:col-span-2">
                                <CustPercentInput
                                    :requiredIndicator="false"
                                    id="admin_15"
                                    type="text"
                                    name="admin_15"
                                    v-model="form.admin_15"
                                />
                            </div>
                            <div class="sm:col-span-1 pt-5">
                                <h1
                                    class="tracking-wide block mb-2 text-xs font-bold text-gray-900 uppercase"
                                >
                                    Admin 30 Hari :
                                </h1>
                            </div>
                            <div class="sm:col-span-2">
                                <CustPercentInput
                                    :requiredIndicator="false"
                                    id="admin_30"
                                    type="text"
                                    name="admin_30"
                                    v-model="form.admin_30"
                                />
                            </div>
                            <div class="sm:col-span-1 pt-5">
                                <h1
                                    class="tracking-wide block mb-2 text-xs font-bold text-gray-900 uppercase"
                                >
                                    Admin 60 Hari :
                                </h1>
                            </div>
                            <div class="sm:col-span-2">
                                <CustPercentInput
                                    :requiredIndicator="false"
                                    id="admin_60"
                                    type="text"
                                    name="admin_60"
                                    v-model="form.admin_60"
                                />
                            </div>
                            <div class="sm:col-span-1 pt-5">
                                <h1
                                    class="tracking-wide block mb-2 text-xs font-bold text-gray-900 uppercase"
                                >
                                    Admin 90 Hari :
                                </h1>
                            </div>
                            <div class="sm:col-span-2">
                                <CustPercentInput
                                    :requiredIndicator="false"
                                    id="admin_90"
                                    type="text"
                                    name="admin_90"
                                    v-model="form.admin_90"
                                />
                            </div>
                            <div class="sm:col-span-1 pt-5">
                                <h1
                                    class="tracking-wide block mb-2 text-xs font-bold text-gray-900 uppercase"
                                >
                                    Admin 120 Hari :
                                </h1>
                            </div>
                            <div class="sm:col-span-2">
                                <CustPercentInput
                                    :requiredIndicator="false"
                                    id="admin_120"
                                    type="text"
                                    name="admin_120"
                                    v-model="form.admin_120"
                                    :disabled="true"
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