<script setup>
import { ref, onMounted, watch, computed, reactive } from "vue";
import AdminLayout from "@/Layouts/AdminLayout.vue";
import TitlePage from "@/Components/TitlePage.vue";
import InputError from "@/Components/InputError.vue";
import SearchInput from "@/Components/SearchInput.vue";
import CustPercentInput from "@/Components/CustPercentInput.vue";
import CustPhoneInput from "@/Components/CustPhoneInput.vue";
import CustDetail from "@/Components/CustDetail.vue";
import CustImageDetail from "@/Components/CustImageDetail.vue";
import InputFormatIDR from "@/Components/InputFormatIDR.vue";
import RadioGroup from "@/Components/RadioGroup.vue";
import NestedSelect from "@/Components/NestedSelect.vue";
import TextAreaInput from "@/Components/TextAreaInput.vue";
import WebcamCapture from "@/Components/WebcamCapture.vue";
import UploadImage from "@/Components/UploadImage.vue";
import Modal from "@/Components/DialogModal.vue";
import { Head, useForm } from "@inertiajs/vue3";
import axios from "axios";
import Swal from "sweetalert2";
import CustTextInput from "@/Components/CustTextInput.vue";
import dayjs from "dayjs";

defineProps({
    errors: Object,
});

const modal = reactive({
    nasabah: false,
    stl: false,
    penaksir: false,
    users: false,
    trans: false,
    bayar: false,
});

const typeExp = ref(true)
const exportField = reactive({
    strDate: null,
    endDate: null,
});

const modalNasabah = () => {
    modal.nasabah = true;
};

const modalStl = () => {
    modal.stl = true;
};

const modalPenaksir = () => {
    modal.penaksir = true;
};

const modalUsers = () => {
    modal.users = true;
};

const modalTrans = () => {
    modal.trans = true;
};

const modalBayar = () => {
    modal.bayar = true;
};

const resetExportForm = () => {
    exportField.strDate = null;
    exportField.endDate = null;
    typeExp.value = true;
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

function extractDatePart(dateString) {
    return dateString.split(" ")[0]; // Extracts 'YYYY-MM-DD' from 'YYYY-MM-DD HH:MM:SS.sss'
}

// const validExpDate = ref(true);
// const checkDateExp = () => {
//     // Log the exportField date values
//     console.log('exportField.strDate:', exportField.strDate);
//     console.log('exportField.endDate:', exportField.endDate);

//     // Iterate through gadai.value and log the dates being compared
//     gadai.value.forEach(g => {
//         const transactionDate = extractDatePart(g.Tgl_transaksi);
//         console.log('Comparing:', transactionDate, 'with', exportField.strDate, 'and', exportField.endDate);
//     });

//     const ExpstrDate = gadai.value.find(
//         g => extractDatePart(g.Tgl_transaksi) === exportField.strDate
//     );
//     const ExpendDate = gadai.value.find(
//         g => extractDatePart(g.Tgl_transaksi) === exportField.endDate
//     );

//     // Log the results of find operations
//     console.log('Start Date Found:', ExpstrDate);
//     console.log('End Date Found:', ExpendDate);

//     if (ExpstrDate && ExpendDate) {
//         validExpDate.value = false;
//     } else {
//         validExpDate.value = true;
//     }

//     console.log('gadai.value:', gadai.value);
//     console.log('validExpDate.value:', validExpDate.value);
// };

// watch([() => exportField.strDate, () => exportField.endDate], () => {
//     checkDateExp();
// });
const dateNow = dayjs(new Date()).format("DD-MM-YYYY");

const exportNasabah = async () => {
    if (typeExp.value == false && exportField.strDate == null && exportField.strDate == null) {
        Swal.fire({
            title: "Error",
            text: "Tanggal awal dan akhir tidak boleh kosong.",
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
    } else {
        try {
            // Show a loading message while exporting the data
            Swal.fire({
                title: "Mohon Tunggu...",
                text: "Kami sedang mengekspor data Anda.",
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

            // Make the API request to export the data
            const response = await axios.get(route("nasabahExport"), {
                params: {
                    strDate: exportField.strDate,
                    endDate: exportField.endDate,
                },
                responseType: "blob",
            });

            // Create a blob URL for the exported file
            const blob = new Blob([response.data], {
                type: "application/vnd.openxmlformats-officedocument.spreadsheetml.sheet",
            });
            const url = window.URL.createObjectURL(blob);

            // Create a link and trigger the download
            const link = document.createElement("a");
            link.href = url;
            link.download = "Nasabah Recap " + dateNow + ".xlsx";
            link.click();

            // Clean up the blob URL
            window.URL.revokeObjectURL(url);

            // Close the loading message and show success message
            Swal.fire({
                title: "Selesai!",
                text: "Data berhasil di export.",
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

            modal.nasabah = false;
            resetExportForm();
        } catch (error) {
            console.error("Error response:", error.response); // Log the entire error response

            // Check for specific error response
            if (
                error.response.status === 404 &&
                error.response.statusText === "Not Found"
            ) {
                Swal.fire({
                    title: "Tidak Ada Data",
                    text: "Tidak ada data yang ditemukan untuk periode tersebut.",
                    icon: "error",
                    iconColor: "#FFFFFF",
                    background: "#0369a1",
                    showConfirmButton: true,
                    confirmButtonColor: "#FFFFFF",
                    customClass: {
                        title: "text-white",
                        popup: "text-white",
                        confirmButton: "text-black",
                    },
                });
            } else {
                // Generic error message
                Swal.fire({
                    title: "Error",
                    text: "Terjadi kesalahan saat mengeksport data.",
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
    }
};

const exportStl = async () => {
    if (typeExp.value == false && exportField.strDate == null && exportField.strDate == null) {
        Swal.fire({
            title: "Error",
            text: "Tanggal awal dan akhir tidak boleh kosong.",
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
    } else {
        try {
            // Show a loading message while exporting the data
            Swal.fire({
                title: "Mohon Tunggu...",
                text: "Kami sedang mengekspor data Anda.",
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

            // Make the API request to export the data
            const response = await axios.get(route("stlExport"), {
                params: {
                    strDate: exportField.strDate,
                    endDate: exportField.endDate,
                },
                responseType: "blob",
            });

            // Create a blob URL for the exported file
            const blob = new Blob([response.data], {
                type: "application/vnd.openxmlformats-officedocument.spreadsheetml.sheet",
            });
            const url = window.URL.createObjectURL(blob);

            // Create a link and trigger the download
            const link = document.createElement("a");
            link.href = url;
            link.download = "STL Recap " + dateNow + ".xlsx";
            link.click();

            // Clean up the blob URL
            window.URL.revokeObjectURL(url);

            // Close the loading message and show success message
            Swal.fire({
                title: "Selesai!",
                text: "Data berhasil di export.",
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

            modal.stl = false;
            resetExportForm();
        } catch (error) {
            console.error("Error response:", error.response); // Log the entire error response

            // Check for specific error response
            if (
                error.response.status === 404 &&
                error.response.statusText === "Not Found"
            ) {
                Swal.fire({
                    title: "Tidak Ada Data",
                    text: "Tidak ada data yang ditemukan untuk periode tersebut.",
                    icon: "error",
                    iconColor: "#FFFFFF",
                    background: "#0369a1",
                    showConfirmButton: true,
                    confirmButtonColor: "#FFFFFF",
                    customClass: {
                        title: "text-white",
                        popup: "text-white",
                        confirmButton: "text-black",
                    },
                });
            } else {
                // Generic error message
                Swal.fire({
                    title: "Error",
                    text: "Terjadi kesalahan saat mengeksport data.",
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
    }
};

const exportPenaksir = async () => {
    if (typeExp.value == false && exportField.strDate == null && exportField.strDate == null) {
        Swal.fire({
            title: "Error",
            text: "Tanggal awal dan akhir tidak boleh kosong.",
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
    } else {
        try {
            // Show a loading message while exporting the data
            Swal.fire({
                title: "Mohon Tunggu...",
                text: "Kami sedang mengekspor data Anda.",
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

            // Make the API request to export the data
            const response = await axios.get(route("penaksirExport"), {
                params: {
                    strDate: exportField.strDate,
                    endDate: exportField.endDate,
                },
                responseType: "blob",
            });

            // Create a blob URL for the exported file
            const blob = new Blob([response.data], {
                type: "application/vnd.openxmlformats-officedocument.spreadsheetml.sheet",
            });
            const url = window.URL.createObjectURL(blob);

            // Create a link and trigger the download
            const link = document.createElement("a");
            link.href = url;
            link.download = "Penaksir Recap " + dateNow + ".xlsx";
            link.click();

            // Clean up the blob URL
            window.URL.revokeObjectURL(url);

            // Close the loading message and show success message
            Swal.fire({
                title: "Selesai!",
                text: "Data berhasil di export.",
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

            modal.penaksir = false;
            resetExportForm();
        } catch (error) {
            console.error("Error response:", error.response); // Log the entire error response

            // Check for specific error response
            if (
                error.response.status === 404 &&
                error.response.statusText === "Not Found"
            ) {
                Swal.fire({
                    title: "Tidak Ada Data",
                    text: "Tidak ada data yang ditemukan untuk periode tersebut.",
                    icon: "error",
                    iconColor: "#FFFFFF",
                    background: "#0369a1",
                    showConfirmButton: true,
                    confirmButtonColor: "#FFFFFF",
                    customClass: {
                        title: "text-white",
                        popup: "text-white",
                        confirmButton: "text-black",
                    },
                });
            } else {
                // Generic error message
                Swal.fire({
                    title: "Error",
                    text: "Terjadi kesalahan saat mengeksport data.",
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
    }
};

const exportUsers = async () => {
    if (typeExp.value == false && exportField.strDate == null && exportField.strDate == null) {
        Swal.fire({
            title: "Error",
            text: "Tanggal awal dan akhir tidak boleh kosong.",
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
    } else {
        try {
            // Show a loading message while exporting the data
            Swal.fire({
                title: "Mohon Tunggu...",
                text: "Kami sedang mengekspor data Anda.",
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

            // Make the API request to export the data
            const response = await axios.get(route("usersExport"), {
                params: {
                    strDate: exportField.strDate,
                    endDate: exportField.endDate,
                },
                responseType: "blob",
            });

            // Create a blob URL for the exported file
            const blob = new Blob([response.data], {
                type: "application/vnd.openxmlformats-officedocument.spreadsheetml.sheet",
            });
            const url = window.URL.createObjectURL(blob);

            // Create a link and trigger the download
            const link = document.createElement("a");
            link.href = url;
            link.download = "Users Recap " + dateNow + ".xlsx";
            link.click();

            // Clean up the blob URL
            window.URL.revokeObjectURL(url);

            // Close the loading message and show success message
            Swal.fire({
                title: "Selesai!",
                text: "Data berhasil di export.",
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

            modal.users = false;
            resetExportForm();
        } catch (error) {
            console.error("Error response:", error.response); // Log the entire error response

            // Check for specific error response
            if (
                error.response.status === 404 &&
                error.response.statusText === "Not Found"
            ) {
                Swal.fire({
                    title: "Tidak Ada Data",
                    text: "Tidak ada data yang ditemukan untuk periode tersebut.",
                    icon: "error",
                    iconColor: "#FFFFFF",
                    background: "#0369a1",
                    showConfirmButton: true,
                    confirmButtonColor: "#FFFFFF",
                    customClass: {
                        title: "text-white",
                        popup: "text-white",
                        confirmButton: "text-black",
                    },
                });
            } else {
                // Generic error message
                Swal.fire({
                    title: "Error",
                    text: "Terjadi kesalahan saat mengeksport data.",
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
    }
};

const exportTrans = async () => {
    if (typeExp.value == false && exportField.strDate == null && exportField.strDate == null) {
        Swal.fire({
            title: "Error",
            text: "Tanggal awal dan akhir tidak boleh kosong.",
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
    } else {
        try {
            // Show a loading message while exporting the data
            Swal.fire({
                title: "Mohon Tunggu...",
                text: "Kami sedang mengekspor data Anda.",
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

            // Make the API request to export the data
            const response = await axios.get(route("transExport"), {
                params: {
                    strDate: exportField.strDate,
                    endDate: exportField.endDate,
                },
                responseType: "blob",
            });

            // Create a blob URL for the exported file
            const blob = new Blob([response.data], {
                type: "application/vnd.openxmlformats-officedocument.spreadsheetml.sheet",
            });
            const url = window.URL.createObjectURL(blob);

            // Create a link and trigger the download
            const link = document.createElement("a");
            link.href = url;
            link.download = "Transaksi Recap " + dateNow + ".xlsx";
            link.click();

            // Clean up the blob URL
            window.URL.revokeObjectURL(url);

            // Close the loading message and show success message
            Swal.fire({
                title: "Selesai!",
                text: "Data berhasil di export.",
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

            modal.trans = false;
            resetExportForm();
        } catch (error) {
            console.error("Error response:", error.response); // Log the entire error response

            // Check for specific error response
            if (
                error.response.status === 404 &&
                error.response.statusText === "Not Found"
            ) {
                Swal.fire({
                    title: "Tidak Ada Data",
                    text: "Tidak ada data yang ditemukan untuk periode tersebut.",
                    icon: "error",
                    iconColor: "#FFFFFF",
                    background: "#0369a1",
                    showConfirmButton: true,
                    confirmButtonColor: "#FFFFFF",
                    customClass: {
                        title: "text-white",
                        popup: "text-white",
                        confirmButton: "text-black",
                    },
                });
            } else {
                // Generic error message
                Swal.fire({
                    title: "Error",
                    text: "Terjadi kesalahan saat mengeksport data.",
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
    }
};

const exportBayar = async () => {
    if (typeExp.value == false && exportField.strDate == null && exportField.strDate == null) {
        Swal.fire({
            title: "Error",
            text: "Tanggal awal dan akhir tidak boleh kosong.",
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
    } else {
        try {
            // Show a loading message while exporting the data
            Swal.fire({
                title: "Mohon Tunggu...",
                text: "Kami sedang mengekspor data Anda.",
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

            // Make the API request to export the data
            const response = await axios.get(route("bayarExport"), {
                params: {
                    strDate: exportField.strDate,
                    endDate: exportField.endDate,
                },
                responseType: "blob",
            });

            // Create a blob URL for the exported file
            const blob = new Blob([response.data], {
                type: "application/vnd.openxmlformats-officedocument.spreadsheetml.sheet",
            });
            const url = window.URL.createObjectURL(blob);

            // Create a link and trigger the download
            const link = document.createElement("a");
            link.href = url;
            link.download = "Pembayaran Recap " + dateNow + ".xlsx";
            link.click();

            // Clean up the blob URL
            window.URL.revokeObjectURL(url);

            // Close the loading message and show success message
            Swal.fire({
                title: "Selesai!",
                text: "Data berhasil di export.",
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

            modal.bayar = false;
            resetExportForm();
        } catch (error) {
            console.error("Error response:", error.response); // Log the entire error response

            // Check for specific error response
            if (
                error.response.status === 404 &&
                error.response.statusText === "Not Found"
            ) {
                Swal.fire({
                    title: "Tidak Ada Data",
                    text: "Tidak ada data yang ditemukan untuk periode tersebut.",
                    icon: "error",
                    iconColor: "#FFFFFF",
                    background: "#0369a1",
                    showConfirmButton: true,
                    confirmButtonColor: "#FFFFFF",
                    customClass: {
                        title: "text-white",
                        popup: "text-white",
                        confirmButton: "text-black",
                    },
                });
            } else {
                // Generic error message
                Swal.fire({
                    title: "Error",
                    text: "Terjadi kesalahan saat mengeksport data.",
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
    }
};
</script>
<template>
    <Head title="Laporan" />
    <AdminLayout>
        <template #titlePage>
            <TitlePage value="Laporan Excel" />
        </template>
        <template #content>
            <main class="p-6 bg-gray-100 flex-grow overflow-y-auto">
                <div class="mt-6">
                    <div class="bg-white p-4 rounded-lg shadow mb-5">
                        <h2 class="mb-4 text-xl font-bold uppercase">
                            Laporan Master Data
                        </h2>
                        <div class="grid grid-cols-2 gap-4">
                            <div
                                class="bg-sky-700 p-4 rounded-lg shadow cursor-pointer"
                                @click="modalNasabah()"
                            >
                                <h2
                                    class="text-xl text-center text-white font-bold my-5"
                                >
                                    Export Nasabah
                                </h2>
                            </div>

                            <div class="bg-sky-700 p-4 rounded-lg shadow cursor-pointer"
                                @click="modalStl()">
                                <h2
                                    class="text-xl text-center text-white font-bold my-5"
                                >
                                    Export STL
                                </h2>
                            </div>

                            <div class="bg-sky-700 p-4 rounded-lg shadow cursor-pointer"
                                @click="modalPenaksir()">
                                <h2
                                    class="text-xl text-center text-white font-bold my-5"
                                >
                                    Export Penaksir
                                </h2>
                            </div>

                            <div class="bg-sky-700 p-4 rounded-lg shadow cursor-pointer"
                                @click="modalUsers()">
                                <h2
                                    class="text-xl text-center text-white font-bold my-5"
                                >
                                    Export Users
                                </h2>
                            </div>
                        </div>
                    </div>

                    <!-- Modal Nasabah -->
                    <Modal
                        :show="modal.nasabah"
                        @close="[(modal.nasabah = false), resetExportForm()]"
                        maxWidth="xl"
                        classHeader="bg-sky-700 text-white"
                    >
                        <template #title> EXPORT NASABAH </template>
                        <template #content>
                            <div class="my-6 grid grid-rows-2 gap-4">
                                <div class="sm:col-2">
                                    <p
                                        class="tracking-wide block mb-6 text-xs font-bold text-gray-900 uppercase"
                                    >
                                        Data Yang Akan Di Export
                                    </p>
                                    <div class="flex">
                                        <label
                                            for="typeExp"
                                            class="tracking-wide block mb-0 text-xs font-bold text-gray-900 uppercase mr-2"
                                            >Semua Periode</label
                                        >
                                        <input
                                            type="radio"
                                            id="typeExp"
                                            name="typeExp"
                                            :value="true"
                                            v-model="typeExp"
                                            checked
                                        />
                                        <label
                                            for="typeExp1"
                                            class="tracking-wide block mb-0 text-xs font-bold text-gray-900 uppercase ml-14 mr-2"
                                            >Spesifik Periode</label
                                        >
                                        <input
                                            type="radio"
                                            id="typeExp1"
                                            name="typeExp"
                                            :value="false"
                                            v-model="typeExp"
                                        />
                                    </div>
                                </div>
                                <div class="sm:col-2">
                                    <CustTextInput
                                        type="date"
                                        label="Pilih Tanggal Awal"
                                        :disabled="typeExp"
                                        v-model="exportField.strDate"
                                    />
                                </div>
                                <div class="sm:col-2">
                                    <CustTextInput
                                        type="date"
                                        label="Pilih Tanggal Akhir"
                                        :disabled="typeExp"
                                        v-model="exportField.endDate"
                                    />
                                </div>
                            </div>
                            <div class="flex justify-end items-center mt-14">
                                <div class="ml-4">
                                    <div class="text-black">
                                        <button
                                            @click="exportNasabah()"
                                            class="mb-10 ml-2 bg-green-700 text-white py-1 px-4 rounded"
                                            :title="'Click to export data'"
                                        >
                                            Export
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </template>
                    </Modal>

                    <!-- Modal STL -->
                     <Modal
                        :show="modal.stl"
                        @close="[(modal.stl = false), resetExportForm()]"
                        maxWidth="xl"
                        classHeader="bg-sky-700 text-white"
                    >
                        <template #title> EXPORT STL </template>
                        <template #content>
                            <div class="my-6 grid grid-rows-2 gap-4">
                                <div class="sm:col-2">
                                    <p
                                        class="tracking-wide block mb-6 text-xs font-bold text-gray-900 uppercase"
                                    >
                                        Data Yang Akan Di Export
                                    </p>
                                    <div class="flex">
                                        <label
                                            for="typeExp"
                                            class="tracking-wide block mb-0 text-xs font-bold text-gray-900 uppercase mr-2"
                                            >Semua Periode</label
                                        >
                                        <input
                                            type="radio"
                                            id="typeExp"
                                            name="typeExp"
                                            :value="true"
                                            v-model="typeExp"
                                            checked
                                        />
                                        <label
                                            for="typeExp1"
                                            class="tracking-wide block mb-0 text-xs font-bold text-gray-900 uppercase ml-14 mr-2"
                                            >Spesifik Periode</label
                                        >
                                        <input
                                            type="radio"
                                            id="typeExp1"
                                            name="typeExp"
                                            :value="false"
                                            v-model="typeExp"
                                        />
                                    </div>
                                </div>
                                <div class="sm:col-2">
                                    <CustTextInput
                                        type="date"
                                        label="Pilih Tanggal Awal"
                                        :disabled="typeExp"
                                        v-model="exportField.strDate"
                                    />
                                </div>
                                <div class="sm:col-2">
                                    <CustTextInput
                                        type="date"
                                        label="Pilih Tanggal Akhir"
                                        :disabled="typeExp"
                                        v-model="exportField.endDate"
                                    />
                                </div>
                            </div>
                            <div class="flex justify-end items-center mt-14">
                                <div class="ml-4">
                                    <div class="text-black">
                                        <button
                                            @click="exportStl()"
                                            class="mb-10 ml-2 bg-green-700 text-white py-1 px-4 rounded"
                                            :title="'Click to export data'"
                                        >
                                            Export
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </template>
                    </Modal>

                    <!-- Modal Penaksir -->
                     <Modal
                        :show="modal.penaksir"
                        @close="[(modal.penaksir = false), resetExportForm()]"
                        maxWidth="xl"
                        classHeader="bg-sky-700 text-white"
                    >
                        <template #title> EXPORT PENAKSIR </template>
                        <template #content>
                            <div class="my-6 grid grid-rows-2 gap-4">
                                <div class="sm:col-2">
                                    <p
                                        class="tracking-wide block mb-6 text-xs font-bold text-gray-900 uppercase"
                                    >
                                        Data Yang Akan Di Export
                                    </p>
                                    <div class="flex">
                                        <label
                                            for="typeExp"
                                            class="tracking-wide block mb-0 text-xs font-bold text-gray-900 uppercase mr-2"
                                            >Semua Periode</label
                                        >
                                        <input
                                            type="radio"
                                            id="typeExp"
                                            name="typeExp"
                                            :value="true"
                                            v-model="typeExp"
                                            checked
                                        />
                                        <label
                                            for="typeExp1"
                                            class="tracking-wide block mb-0 text-xs font-bold text-gray-900 uppercase ml-14 mr-2"
                                            >Spesifik Periode</label
                                        >
                                        <input
                                            type="radio"
                                            id="typeExp1"
                                            name="typeExp"
                                            :value="false"
                                            v-model="typeExp"
                                        />
                                    </div>
                                </div>
                                <div class="sm:col-2">
                                    <CustTextInput
                                        type="date"
                                        label="Pilih Tanggal Awal"
                                        :disabled="typeExp"
                                        v-model="exportField.strDate"
                                    />
                                </div>
                                <div class="sm:col-2">
                                    <CustTextInput
                                        type="date"
                                        label="Pilih Tanggal Akhir"
                                        :disabled="typeExp"
                                        v-model="exportField.endDate"
                                    />
                                </div>
                            </div>
                            <div class="flex justify-end items-center mt-14">
                                <div class="ml-4">
                                    <div class="text-black">
                                        <button
                                            @click="exportPenaksir()"
                                            class="mb-10 ml-2 bg-green-700 text-white py-1 px-4 rounded"
                                            :title="'Click to export data'"
                                        >
                                            Export
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </template>
                    </Modal>

                    <!-- Modal Users -->
                    <Modal
                        :show="modal.users"
                        @close="[(modal.users = false), resetExportForm()]"
                        maxWidth="xl"
                        classHeader="bg-sky-700 text-white"
                    >
                        <template #title> EXPORT USERS </template>
                        <template #content>
                            <div class="my-6 grid grid-rows-2 gap-4">
                                <div class="sm:col-2">
                                    <p
                                        class="tracking-wide block mb-6 text-xs font-bold text-gray-900 uppercase"
                                    >
                                        Data Yang Akan Di Export
                                    </p>
                                    <div class="flex">
                                        <label
                                            for="typeExp"
                                            class="tracking-wide block mb-0 text-xs font-bold text-gray-900 uppercase mr-2"
                                            >Semua Periode</label
                                        >
                                        <input
                                            type="radio"
                                            id="typeExp"
                                            name="typeExp"
                                            :value="true"
                                            v-model="typeExp"
                                            checked
                                        />
                                        <label
                                            for="typeExp1"
                                            class="tracking-wide block mb-0 text-xs font-bold text-gray-900 uppercase ml-14 mr-2"
                                            >Spesifik Periode</label
                                        >
                                        <input
                                            type="radio"
                                            id="typeExp1"
                                            name="typeExp"
                                            :value="false"
                                            v-model="typeExp"
                                        />
                                    </div>
                                </div>
                                <div class="sm:col-2">
                                    <CustTextInput
                                        type="date"
                                        label="Pilih Tanggal Awal"
                                        :disabled="typeExp"
                                        v-model="exportField.strDate"
                                    />
                                </div>
                                <div class="sm:col-2">
                                    <CustTextInput
                                        type="date"
                                        label="Pilih Tanggal Akhir"
                                        :disabled="typeExp"
                                        v-model="exportField.endDate"
                                    />
                                </div>
                            </div>
                            <div class="flex justify-end items-center mt-14">
                                <div class="ml-4">
                                    <div class="text-black">
                                        <button
                                            @click="exportUsers()"
                                            class="mb-10 ml-2 bg-green-700 text-white py-1 px-4 rounded"
                                            :title="'Click to export data'"
                                        >
                                            Export
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </template>
                    </Modal>


                    <div class="bg-white p-4 rounded-lg shadow">
                        <h2 class="mb-4 text-xl font-bold uppercase">
                            Laporan Transaksi
                        </h2>
                        <div class="grid grid-cols-2 gap-4">
                            <div class="bg-sky-700 p-4 rounded-lg shadow cursor-pointer"
                                @click="modalTrans()">
                                <h2
                                    class="text-xl text-center text-white font-bold my-5"
                                >
                                    Export Transaksi Emas
                                </h2>
                            </div>

                            <div class="bg-sky-700 p-4 rounded-lg shadow cursor-pointer"
                                @click="modalBayar()">
                                <h2
                                    class="text-xl text-center text-white font-bold my-5"
                                >
                                    Export Data Pembayaran
                                </h2>
                            </div>
                        </div>
                    </div>

                    <!-- Modal Transaksi -->
                     <Modal
                        :show="modal.trans"
                        @close="[(modal.trans = false), resetExportForm()]"
                        maxWidth="xl"
                        classHeader="bg-sky-700 text-white"
                    >
                        <template #title> EXPORT TRANSAKSI </template>
                        <template #content>
                            <div class="my-6 grid grid-rows-2 gap-4">
                                <div class="sm:col-2">
                                    <p
                                        class="tracking-wide block mb-6 text-xs font-bold text-gray-900 uppercase"
                                    >
                                        Data Yang Akan Di Export
                                    </p>
                                    <div class="flex">
                                        <label
                                            for="typeExp"
                                            class="tracking-wide block mb-0 text-xs font-bold text-gray-900 uppercase mr-2"
                                            >Semua Periode</label
                                        >
                                        <input
                                            type="radio"
                                            id="typeExp"
                                            name="typeExp"
                                            :value="true"
                                            v-model="typeExp"
                                            checked
                                        />
                                        <label
                                            for="typeExp1"
                                            class="tracking-wide block mb-0 text-xs font-bold text-gray-900 uppercase ml-14 mr-2"
                                            >Spesifik Periode</label
                                        >
                                        <input
                                            type="radio"
                                            id="typeExp1"
                                            name="typeExp"
                                            :value="false"
                                            v-model="typeExp"
                                        />
                                    </div>
                                </div>
                                <div class="sm:col-2">
                                    <CustTextInput
                                        type="date"
                                        label="Pilih Tanggal Awal"
                                        :disabled="typeExp"
                                        v-model="exportField.strDate"
                                    />
                                </div>
                                <div class="sm:col-2">
                                    <CustTextInput
                                        type="date"
                                        label="Pilih Tanggal Akhir"
                                        :disabled="typeExp"
                                        v-model="exportField.endDate"
                                    />
                                </div>
                            </div>
                            <div class="flex justify-end items-center mt-14">
                                <div class="ml-4">
                                    <div class="text-black">
                                        <button
                                            @click="exportTrans()"
                                            class="mb-10 ml-2 bg-green-700 text-white py-1 px-4 rounded"
                                            :title="'Click to export data'"
                                        >
                                            Export
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </template>
                    </Modal>

                    <!-- Modal Bayar -->
                     <Modal
                        :show="modal.bayar"
                        @close="[(modal.bayar = false), resetExportForm()]"
                        maxWidth="xl"
                        classHeader="bg-sky-700 text-white"
                    >
                        <template #title> EXPORT PEMBAYARAN </template>
                        <template #content>
                            <div class="my-6 grid grid-rows-2 gap-4">
                                <div class="sm:col-2">
                                    <p
                                        class="tracking-wide block mb-6 text-xs font-bold text-gray-900 uppercase"
                                    >
                                        Data Yang Akan Di Export
                                    </p>
                                    <div class="flex">
                                        <label
                                            for="typeExp"
                                            class="tracking-wide block mb-0 text-xs font-bold text-gray-900 uppercase mr-2"
                                            >Semua Periode</label
                                        >
                                        <input
                                            type="radio"
                                            id="typeExp"
                                            name="typeExp"
                                            :value="true"
                                            v-model="typeExp"
                                            checked
                                        />
                                        <label
                                            for="typeExp1"
                                            class="tracking-wide block mb-0 text-xs font-bold text-gray-900 uppercase ml-14 mr-2"
                                            >Spesifik Periode</label
                                        >
                                        <input
                                            type="radio"
                                            id="typeExp1"
                                            name="typeExp"
                                            :value="false"
                                            v-model="typeExp"
                                        />
                                    </div>
                                </div>
                                <div class="sm:col-2">
                                    <CustTextInput
                                        type="date"
                                        label="Pilih Tanggal Awal"
                                        :disabled="typeExp"
                                        v-model="exportField.strDate"
                                    />
                                </div>
                                <div class="sm:col-2">
                                    <CustTextInput
                                        type="date"
                                        label="Pilih Tanggal Akhir"
                                        :disabled="typeExp"
                                        v-model="exportField.endDate"
                                    />
                                </div>
                            </div>
                            <div class="flex justify-end items-center mt-14">
                                <div class="ml-4">
                                    <div class="text-black">
                                        <button
                                            @click="exportBayar()"
                                            class="mb-10 ml-2 bg-green-700 text-white py-1 px-4 rounded"
                                            :title="'Click to export data'"
                                        >
                                            Export
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
