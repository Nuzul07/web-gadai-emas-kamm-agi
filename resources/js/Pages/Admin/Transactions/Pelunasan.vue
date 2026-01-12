<script setup>
import { ref, onMounted, watch, computed, reactive } from "vue";
import AdminLayout from "@/Layouts/AdminLayout.vue";
import TitlePage from "@/Components/TitlePage.vue";
import SearchInput from "@/Components/SearchInput.vue";
import CustTextInput from "@/Components/CustTextInput.vue";
import InputFormatIDR from "@/Components/InputFormatIDR.vue";
import NestedSelect from "@/Components/NestedSelect.vue";
import Modal from "@/Components/DialogModal.vue";
import { Head, useForm } from "@inertiajs/vue3";
import axios, { formToJSON } from "axios";
import Swal from "sweetalert2";
import dayjs from "dayjs";
import { selectedTipeGadai } from "@/src/store";

onMounted(() => {
    checkTipeGadai();
    masterCodeData();
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
            html: "Tuhkan belum dipilih produk gadainya 😠 <br> Tolong pilih dulu yaa, Terima Kasih!!!"
        }).then(() => {
            window.location.href = route('gadaiDashboard')
        })
    }
}

const dropdownStates = ref({
    caraBayarState: false,
    jenisBayarState: false,
});

const form = useForm({
    no_bayaran: "",
    no_sbg: "",
    tgl_pelunasan: "",
    tgl_jtempo: "",
    pokok_pinjaman: "",
    sewa_modal: "",
    denda_pinjaman: "",
    by_lain: "",
    total_pembayaran: "",
    cara_bayar: "",
    jenis_pembayaran: "",
    user_input: "",
    jenis_transaksi: "",
    bunga: "",
    produk_gadai:
        selectedTipeGadai.value === "emas"
            ? "EMAS"
            : selectedTipeGadai.value === "motor"
            ? "MOTOR"
            : "MOBIL",
});

const resetUnForm = () => {
    formattedByLain.value = "";
    sewaModalAmount.value = "";
    finesAmount.value = "";
};

//modal
const modal = reactive({
    pelunasan: false,
});

const modalPelunasan = (d) => {
    selectDataGadai(d);
    modal.pelunasan = true;
};

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

//formatter
const dateInput = ref("");
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

const formatRupiah = (value) => {
    const numberFormat = new Intl.NumberFormat("id-ID", {
        style: "currency",
        currency: "IDR",
        minimumFractionDigits: 0, // No decimal places
        maximumFractionDigits: 0, // No decimal places
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

const masterCode = ref([]);
const masterCodeData = async () => {
    await axios.get(route("getMasterCode")).then((res) => {
        const data = res.data;
        masterCode.value = data;
        updateCaraBayarOptions();
        updateJenisBayarOptions();
    });
};
//end

const caraBayarOptions = ref([]);
const jenisBayarOptions = ref([]);

const updateCaraBayarOptions = () => {
    const CaraBayarSet = new Set(
        masterCode.value
            .filter((item) => item.Master_code === "Carabayar")
            .map((item) => item.Keterangan) // assuming you need 'value' from the item
    );

    // Convert Set to array and update asalBarangOptions
    caraBayarOptions.value = [...CaraBayarSet];
};

const updateJenisBayarOptions = () => {
    const JenisBayarSet = new Set(
        masterCode.value
            .filter((item) => item.Master_code === "Jenis_pembayaran")
            .map((item) => item.Keterangan) // assuming you need 'value' from the item
    );

    // Convert Set to array and update asalBarangOptions
    jenisBayarOptions.value = [...JenisBayarSet];
};
//

watch(
    () => dateInput.value,
    (newValue) => {
        if (newValue !== "") {
            form.tgl_pelunasan = formatDateDB(new Date());
        }
    }
);

const sewaModalAmount = ref(0);
const calculateSewaModal = (d) => {
    const pokokPinjaman = d.Pokok_pinjaman;
    const payDate = dayjs(dateInput.value).startOf("day"); // Remove any formatting
    const transDate = dayjs(d.Tgl_transaksi).startOf("day");

    const diffFirst = payDate.diff(transDate, "day") + 1;

    // if (selectedTipeGadai.value === "emas") {
    //     const sewaModalGolongan = golongan.value.find(
    //         (g) => g.GOLONGAN === d.Golongan
    //     );

    //     if (diffFirst <= 15) {
    //         sewaModalAmount.value = formatRupiah(
    //             parseFloat(
    //                 (
    //                     pokokPinjaman * sewaModalGolongan.SEWA_MODAL_15HARI
    //                 ).toFixed(0)
    //             )
    //         );
    //     } else if (diffFirst > 15 && diffFirst <= 30) {
    //         sewaModalAmount.value = formatRupiah(
    //             parseFloat(
    //                 (
    //                     pokokPinjaman * sewaModalGolongan.SEWA_MODAL16_30HARI
    //                 ).toFixed(0)
    //             )
    //         );
    //         form.sewa_modal = formatDecimal(
    //             sewaModalGolongan.SEWA_MODAL16_30HARI * 100
    //         );
    //     } else if (diffFirst > 30 && diffFirst <= 45) {
    //         sewaModalAmount.value = formatRupiah(
    //             parseFloat(
    //                 (
    //                     pokokPinjaman * sewaModalGolongan.SEWA_MODAL31_45HARI
    //                 ).toFixed(0)
    //             )
    //         );
    //         form.sewa_modal = formatDecimal(
    //             sewaModalGolongan.SEWA_MODAL31_45HARI * 100
    //         );
    //     } else if (diffFirst > 45 && diffFirst <= 60) {
    //         sewaModalAmount.value = formatRupiah(
    //             parseFloat(
    //                 (
    //                     pokokPinjaman * sewaModalGolongan.SEWA_MODAL46_60HARI
    //                 ).toFixed(0)
    //             )
    //         );
    //         form.sewa_modal = formatDecimal(
    //             sewaModalGolongan.SEWA_MODAL46_60HARI * 100
    //         );
    //     } else if (diffFirst > 60 && diffFirst <= 75) {
    //         sewaModalAmount.value = formatRupiah(
    //             parseFloat(
    //                 (
    //                     pokokPinjaman * sewaModalGolongan.SEWA_MODAL61_75HARI
    //                 ).toFixed(0)
    //             )
    //         );
    //         form.sewa_modal = formatDecimal(
    //             sewaModalGolongan.SEWA_MODAL61_75HARI * 100
    //         );
    //     } else if (diffFirst > 75 && diffFirst <= 90) {
    //         sewaModalAmount.value = formatRupiah(
    //             parseFloat(
    //                 (
    //                     pokokPinjaman * sewaModalGolongan.SEWA_MODAL76_90HARI
    //                 ).toFixed(0)
    //             )
    //         );
    //         form.sewa_modal = formatDecimal(
    //             sewaModalGolongan.SEWA_MODAL76_90HARI * 100
    //         );
    //     } else if (diffFirst > 90 && diffFirst <= 105) {
    //         sewaModalAmount.value = formatRupiah(
    //             parseFloat(
    //                 (
    //                     pokokPinjaman * sewaModalGolongan.SEWA_MODAL91_105HARI
    //                 ).toFixed(0)
    //             )
    //         );
    //         form.sewa_modal = formatDecimal(
    //             sewaModalGolongan.SEWA_MODAL91_105HARI * 100
    //         );
    //     } else if (diffFirst > 105) {
    //         sewaModalAmount.value = formatRupiah(
    //             parseFloat(
    //                 (
    //                     pokokPinjaman *
    //                     sewaModalGolongan.SEWA_MODAL_105_ATAS_HARI
    //                 ).toFixed(0)
    //             )
    //         );
    //         form.sewa_modal = formatDecimal(
    //             sewaModalGolongan.SEWA_MODAL_105_ATAS_HARI * 100
    //         );
    //     }
    // } else {
    //     sewaModalAmount.value = formatRupiah(d.Bunga);
    // }
    sewaModalAmount.value = formatRupiah(d.Bunga);
    form.sewa_modal = formatDecimal(
        parseFloat((d.Bunga / pokokPinjaman) * 100)
    );
    console.log(parseFloat(d.Bunga / pokokPinjaman))
};

const golongan = ref([]);
const golonganData = async () => {
    await axios.get(route("getGolongan")).then((res) => {
        const data = res.data;
        golongan.value = data;
    });
};

const finesAmount = ref(0);
const calculateFines = (d) => {
    const pokokPinjaman = d.Pokok_pinjaman;
    const payDate = dayjs(dateInput.value).startOf("day");
    const borrowDate = dayjs(d.Tgl_jtempo).startOf("day");

    const diffDays = payDate.diff(borrowDate, "day");
    console.log(diffDays);

    // const diffDays = 30; // Calculate the difference in days
    let fines = 0;
    if (selectedTipeGadai.value === "emas") {
        const finesGolongan = golongan.value.find(
            (g) => g.GOLONGAN === d.Golongan
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
    } else if (selectedTipeGadai.value !== "emas") {
        if (diffDays > 0) {
            const periods = Math.floor((diffDays - 1) / 15); // Calculate how many 15-day periods have passed
            const rate = 0.025 + periods * 0.025; // Increase rate by 0.025 for each 15-day period
            console.log(periods, rate);
            fines = (pokokPinjaman * rate).toFixed(0);
        }
    }

    return fines;
};

const selectDataGadai = (d) => {
    form.no_sbg = d.No_sbg;
    form.pokok_pinjaman = formatRupiah(d.Pokok_pinjaman);
    form.sewa_modal = formatDecimal(d.Sewa_modal * 100);
    dateInput.value = formatDate(new Date());
    form.tgl_jtempo = formatDate(d.Tgl_jtempo); // Ensure dateInput is set with dayjs
    calculateSewaModal(d);

    // Calculate and set fines directly
    const fines = calculateFines(d);
    finesAmount.value = formatRupiah(fines);
    const finesString = fines.toString();
    form.denda_pinjaman =
        parseFloat(finesString.replace(/[^\d,]/g, "").replace(",", ".")) || 0;

    const pokokPinjamanValue =
        parseFloat(
            form.pokok_pinjaman.replace(/[^\d,]/g, "").replace(",", ".")
        ) || 0;
    const sewaModalValue =
        parseFloat(
            sewaModalAmount.value.replace(/[^\d,]/g, "").replace(",", ".")
        ) || 0;
    const dendaPinjamanValue = form.denda_pinjaman;

    if (selectedTipeGadai.value === 'emas') {
        form.total_pembayaran =
        pokokPinjamanValue + sewaModalValue + dendaPinjamanValue;
    } else if (selectedTipeGadai.value === 'motor' || selectedTipeGadai.value === 'mobil') {
        form.total_pembayaran =
        pokokPinjamanValue + dendaPinjamanValue;
    }

    form.total_pembayaran = formatRupiah(form.total_pembayaran);
    form.cara_bayar = d.Cara_bayar;
    form.jenis_pembayaran = d.Jenis_pembayaran;
};

const formattedByLain = ref(""); // assuming this is how you get the by_lain value

watch(
    () => formattedByLain.value,
    (newValue) => {
        const byLain =
            parseFloat(newValue.replace(/[^\d,-]/g, "").replace(".", ",")) || 0;
        form.by_lain = byLain;
        const pokokPinjaman =
            parseFloat(
                form.pokok_pinjaman.replace(/[^\d,-]/g, "").replace(".", ",")
            ) || 0;
        const sewaModal =
            parseFloat(
                sewaModalAmount.value.replace(/[^\d,-]/g, "").replace(".", ",")
            ) || 0;
        const total = pokokPinjaman + sewaModal + byLain + form.denda_pinjaman;
        form.total_pembayaran = formatRupiah(total);
    }
);

// const no_bayar = ref("");
// const getNoBayar = async () => {
//     const res = await axios.get(route("getNoBayar"));
//     no_bayar.value = res.data;
//     return no_bayar.value;
// };

// const setNoBayar = async () => {
//     const nobayar = await getNoBayar();
//     form.no_bayaran = nobayar;
// };

const generatePDF = async () => {
    try {
        // Show a loading message while generating the PDF
        Swal.fire({
            title: "Mohon Tunggu...",
            text: "Membuat Surat Pelunasan.",
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

        // Make the API request to generate the PDF
        const response = await axios.get(route("generatepdfLunas"), {
            responseType: "blob",
            params: {
                no_sbg: form.no_sbg,
                produk_gadai: form.produk_gadai,
            },
        });

        // Create a Blob from the response data
        const blob = new Blob([response.data], { type: "application/pdf" });
        const url = URL.createObjectURL(blob);

        // Create an iframe to load and print the PDF
        const iframe = document.createElement("iframe");
        iframe.style.display = "none";
        iframe.src = url;
        document.body.appendChild(iframe);

        // Print the PDF once the iframe loads
        iframe.onload = () => {
            iframe.contentWindow.print();
        };

        // Close the loading message and show success message
        Swal.fire({
            title: "Selesai!",
            text: "Surat anda siap dicetak.",
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
    } catch (error) {
        // Close the loading message and show an error message
        Swal.fire({
            title: "Error",
            text: "Terjadi kesalahan saat membuat SBG.",
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

        console.error("Error generating PDF:", error);
    }
};

const cetakUlangSBG = async (d) => {
    Swal.fire({
        title: "Info",
        text: "Mohon Tunggu",
        icon: "info",
        showConfirmButton: false,
        timer: 2000,
    });
    try {
        const response = await axios.get(route("generatepdfLunas"), {
            responseType: "blob",
            params: {
                no_sbg: d.No_sbg,
            },
        });

        // Create a Blob from the response data
        const blob = new Blob([response.data], { type: "application/pdf" });
        const url = URL.createObjectURL(blob);

        const iframe = document.createElement("iframe");
        iframe.style.display = "none";
        iframe.src = url;
        document.body.appendChild(iframe);

        iframe.onload = () => {
            iframe.contentWindow.print();
        };
    } catch (error) {
        console.error("Error generating PDF:", error);
    }
};

const removeAllFormat = () => {
    form.pokok_pinjaman =
        parseFloat(
            form.pokok_pinjaman
                .toString()
                .replace(/[^\d,-]/g, "")
                .replace(".", ",")
        ) || 0;
    form.total_pembayaran =
        parseFloat(
            form.total_pembayaran
                .toString()
                .replace(/[^\d,-]/g, "")
                .replace(".", ",")
        ) || 0;
    form.bunga =
        parseFloat(
            sewaModalAmount.value
                .toString()
                .replace(/[^\d,-]/g, "")
                .replace(".", ",")
        ) || 0;
};

const formatBackRupiah = () => {
    form.pokok_pinjaman = formatRupiah(form.pokok_pinjaman);
    form.total_pembayaran = formatRupiah(form.total_pembayaran);
};

const store = async () => {
    const header = {
        headers: {
            "Content-Type": "multipart/form-data",
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
        removeAllFormat(); // Format the data before submission
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
            await axios.post(route("storePelunasan"), form, header);

            // Close the loading alert and show success message
            Swal.fire({
                title: "Berhasil",
                text: "Mohon tunggu cetakan surat lunas",
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

            // Generate the PDF and refresh data
            generatePDF();
            gadaiData();
            form.reset(); // Reset the form
            resetUnForm(); // Additional reset functionality
            modal.pelunasan = false; // Close the modal
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

            formatBackRupiah();
        }
    }
};
</script>
<template>
    <Head title="Pelunasan" />
    <AdminLayout>
        <template #titlePage>
            <TitlePage value="Pelunasan Gadai" />
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
                                            Nomor sbg
                                        </th>
                                        <th
                                            scope="col"
                                            class="text-center text-md font-sans uppercase pb-3 text-white"
                                        >
                                            Nama nasabah
                                        </th>
                                        <th
                                            scope="col"
                                            class="text-center text-md font-sans uppercase pb-3 text-white"
                                        >
                                            Status
                                        </th>
                                        <th
                                            scope="col"
                                            class="text-center text-md font-sans uppercase pb-3 text-white"
                                        >
                                            Aksi
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
                                            v-if="d.Status == 1 || 2"
                                            class="whitespace-nowrap text-sm text-center text-black py-2"
                                        >
                                            Belum Lunas
                                        </td>
                                        <td
                                            v-else
                                            class="whitespace-nowrap text-sm text-center text-black py-2"
                                        >
                                            Lunas
                                        </td>
                                        <td
                                            v-if="d.Status == 1 || 2"
                                            class="whitespace-nowrap text-sm text-center text-black py-2"
                                        >
                                            <a @click="modalPelunasan(d)"
                                                ><img
                                                    class="m-auto cursor-pointer"
                                                    src="/gadaiemas/storage/icon/money.png"
                                                    title="Proses Pelunasan"
                                            /></a>
                                        </td>
                                        <td
                                            v-else
                                            class="whitespace-nowrap text-sm text-center text-black py-2"
                                        >
                                            <a
                                                ><img
                                                    class="m-auto"
                                                    src="/gadaiemas/storage/icon/done.png"
                                                    title="Sudah Lunas"
                                            /></a>
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
                            :show="modal.pelunasan"
                            @close="[(modal.pelunasan = false)]"
                            maxWidth="2xl"
                            classHeader="bg-sky-700 text-white"
                        >
                            <template #title> Pelunasan </template>
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
                                            v-model="form.no_sbg"
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
                                            v-model="dateInput"
                                            disabled="true"
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
                                            v-model="form.tgl_jtempo"
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
                                            v-model="form.pokok_pinjaman"
                                            disabled="true"
                                        />
                                    </div>
                                    <div class="sm:col-span-1 pt-5">
                                        <h1
                                            v-if="selectedTipeGadai === 'emas'"
                                            class="tracking-wide block mb-2 text-xs font-bold text-gray-900 uppercase"
                                        >
                                            Sewa Modal :
                                        </h1>
                                        <h1
                                            v-if="selectedTipeGadai !== 'emas'"
                                            class="tracking-wide block mb-2 text-xs font-bold text-gray-900 uppercase"
                                        >
                                            Bunga Sampai Tgl Jtempo:
                                        </h1>
                                    </div>
                                    <div class="sm:col-span-1">
                                        <CustTextInput
                                            :required-indicator="false"
                                            id="sewa_modal"
                                            type="text"
                                            name="sewa_modal"
                                            v-model="form.sewa_modal"
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
                                            Biaya Lain :
                                        </h1>
                                    </div>
                                    <div class="sm:col-span-2">
                                        <InputFormatIDR
                                            :required-indicator="false"
                                            id="by_lain"
                                            type="text"
                                            name="by_lain"
                                            v-model="formattedByLain"
                                        />
                                        <div v-if="form.errors.by_lain">
                                            <span class="text-sm text-red-600"
                                                >*{{
                                                    form.errors.by_lain[0]
                                                }}</span
                                            >
                                        </div>
                                    </div>
                                    <div class="sm:col-span-1 pt-5">
                                        <h1
                                            class="tracking-wide block mb-2 text-xs font-bold text-gray-900 uppercase"
                                        >
                                            Cara Bayar :
                                        </h1>
                                    </div>
                                    <div class="sm:col-span-2">
                                        <NestedSelect
                                            :required-indicator="false"
                                            :options="caraBayarOptions"
                                            v-model="form.cara_bayar"
                                            @focusin="
                                                dropdownStates.caraBayarState = true;
                                                form.cara_bayar = '';
                                            "
                                            @focusout="
                                                dropdownStates.caraBayarState = false
                                            "
                                        />
                                        <div v-if="form.errors.cara_bayar">
                                            <span class="text-sm text-red-600"
                                                >*{{
                                                    form.errors.cara_bayar[0]
                                                }}</span
                                            >
                                        </div>
                                    </div>
                                    <div class="sm:col-span-1 pt-5">
                                        <h1
                                            v-if="form.cara_bayar"
                                            class="tracking-wide block mb-2 text-xs font-bold text-gray-900 uppercase"
                                        >
                                            Jenis Pembayaran :
                                        </h1>
                                    </div>
                                    <div class="sm:col-span-2">
                                        <NestedSelect
                                            :required-indicator="false"
                                            :options="jenisBayarOptions"
                                            v-model="form.jenis_pembayaran"
                                            @focusin="
                                                dropdownStates.jenisBayarState = true;
                                                form.jenis_pembayaran = '';
                                            "
                                            @focusout="
                                                dropdownStates.jenisBayarState = false
                                            "
                                            v-if="
                                                form.cara_bayar &&
                                                !dropdownStates.caraBayarState
                                            "
                                        />
                                        <div
                                            v-if="form.errors.jenis_pembayaran"
                                        >
                                            <span class="text-sm text-red-600"
                                                >*{{
                                                    form.errors
                                                        .jenis_pembayaran[0]
                                                }}</span
                                            >
                                        </div>
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
                                            v-model="form.total_pembayaran"
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
                                                @click="store()"
                                                class="mb-10 ml-2 bg-green-700 text-white py-1 px-4 rounded"
                                            >
                                                Bayar
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