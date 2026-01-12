<script setup>
import { ref, onMounted, watch, computed, reactive } from "vue";
import AdminLayout from "@/Layouts/AdminLayout.vue";
import TitlePage from "@/Components/TitlePage.vue";
import SearchInput from "@/Components/SearchInput.vue";
import CustTextInput from "@/Components/CustTextInput.vue";
import NestedSelect from "@/Components/NestedSelect.vue";
import UploadImage from "@/Components/UploadImage.vue";
import Modal from "@/Components/DialogModal.vue";
import { Head, useForm } from "@inertiajs/vue3";
import axios from "axios";
import Swal from "sweetalert2";
import dayjs from "dayjs";
import UploadMultiImage from "@/Components/UploadMultiImage.vue";
import { selectedTipeGadai } from "@/src/store";

onMounted(() => {
    checkTipeGadai();
    gadaiData();
    masterCodeData();
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

const dropdownStates = ref({
    tenorState: false,
    caraBayarState: false,
    jenisBayarState: false,
});

const form = useForm({
    //tb transaksi
    no_sbg: "",
    denda_pinjaman: "",
    by_lain: "",
    no_kons: "",
    penaksir: "",
    type_transaksi: "",
    sewa_modal: "",
    tgl_transaksi: "",
    old_tgl_jtempo: "",
    new_tgl_jtempo: "",
    by_admin: "",
    tenor: "",
    total_taksiran: "",
    pokok_pinjaman: "",
    maks_pinjaman: "",
    cara_pencairan: "",
    no_rek_nasabah: "",
    nama_bank: "",
    referensi_nosbg: "",
    asal_barang: "",
    tujuan_transaksi: "",
    instrumen_pembayaran: "",
    pengembalian_uang_lebih: "",
    fungsi_transaksi: "",
    foto_identitas: "",
    golongan: "",
    terima_uang: "",
    produk_gadai: selectedTipeGadai.value === 'emas' ? 'EMAS' : selectedTipeGadai.value === 'motor' ? 'MOTOR' : 'MOBIL',

    //tb bayar
    total_pembayaran: "",
    cara_bayar: "",
    jenis_pembayaran: "",
    jenis_transaksi: "",
    bunga: "",

    //tb barang
    foto_barang: [],
    foto_barang2: [],
    lain_lain: [],
    barangJmn: [],
});

const modal = reactive({
    perpanjangan: false,
    submitPanjang: false,
});

const modalPerpanjangan = (d) => {
    selectDataGadai(d);
    modal.perpanjangan = true;
};

const modalSubmitPanjang = () => {
    modal.submitPanjang = true;
};

// const gadai = ref([]);
// const gadaiLoading = ref(false);
// const gadaiData = async () => {
//     gadaiLoading.value = true;
//     await axios.get(route("getGadaiActive")).then((res) => {
//         const data = res.data;
//         gadai.value = data;
//         gadaiLoading.value = false;
//     });
// };

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

const barangJmn = ref([]);
const getBarangJaminan = async () => {
    let url =
        selectedTipeGadai.value === "emas"
            ? "getBarangJaminan"
            : selectedTipeGadai.value === "motor"
            ? "getJaminanMotor"
            : "getJaminanMobil";
    try {
        const res = await axios.get(route(url), {
            params: {
                no_sbg: form.referensi_nosbg,
            },
        });
        const data = res.data;
        barangJmn.value = data;
        console.log(barangJmn.value);
    } catch (error) {
        console.log(error);
    }
};

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

const searchIPer = ref("");
const searchGadai = computed(() => {
    if (!searchIPer.value) {
        return gadai.value;
    }
    const lowerCase = searchIPer.value.toLowerCase();
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
//end formatter

const masterCode = ref([]);
const masterCodeData = async () => {
    await axios.get(route("getMasterCode")).then((res) => {
        const data = res.data;
        masterCode.value = data;
        updateCaraBayarOptions();
        updateJenisBayarOptions();
        updateTenorOptions();
    });
};

const caraBayarOptions = ref([]);
const jenisBayarOptions = ref([]);
const TenorOptions = ref([]);

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

const updateTenorOptions = () => {
    const TenorSet = new Set(
        masterCode.value
            .filter((item) => item.Master_code === "Tenor")
            .map((item) => item.Code1) // assuming you need 'value' from the item
    );

    // Convert Set to array and update asalBarangOptions
    TenorOptions.value = [...TenorSet];
};
//

const sewaModalAmount = ref(0);
const diffFirst = ref(0);
const calculateSewaModal = (d) => {
    const pokokPinjaman = d.Pokok_pinjaman;
    const extendDate = dateInput.value; // Remove any formatting
    const transDate = formatDate(d.Tgl_transaksi);

    diffFirst.value = dayjs(extendDate).diff(transDate, "day");

    console.log(diffFirst)

    // if (selectedTipeGadai.value === 'emas') {
    //     if (diffFirst.value <= 15) {
    //         sewaModalAmount.value = formatRupiah(
    //             parseFloat((pokokPinjaman * d.Sewa_modal * 1).toFixed(0))
    //         );
    //     } else if (diffFirst.value > 15) {
    //         sewaModalAmount.value = formatRupiah(
    //             parseFloat(
    //                 (pokokPinjaman * d.Sewa_modal * (diffFirst.value / 15)).toFixed(0)
    //             )
    //         );
    //     }
    // } else {
    //     sewaModalAmount.value = formatRupiah(d.Bunga)
    // }
    sewaModalAmount.value = formatRupiah(d.Bunga)
    
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
    if (selectedTipeGadai.value === 'emas') {
        const finesGolongan = golongan.value.find((g) => g.GOLONGAN === d.Golongan);
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
    } else if (selectedTipeGadai.value !== 'emas'){
        if (diffDays > 0) {
            const periods = Math.floor((diffDays - 1) / 15); // Calculate how many 15-day periods have passed
            const rate = 0.025 + periods * 0.025; // Increase rate by 0.025 for each 15-day period
            console.log(periods, rate)
            fines = (pokokPinjaman * rate).toFixed(0);
        }
    }

    return fines;
};

const maksTenor = ref(0);
// Watcher to update jatuh tempo when maksTenor changes
watch(
    () => maksTenor.value, // Watch the value directly
    (newValue) => {
        form.tenor = newValue;
        if (newValue != 0) {
            const resultDate = dayjs(dateInput.value, "DD/MM/YYYY")
                .add(newValue, "day")
                .startOf("day"); // Set to start of day after addition
            form.new_tgl_jtempo = formatDate(resultDate);
        } else {
            form.new_tgl_jtempo = "";
        }
    }
);

const selectDataGadai = async (d) => {
    form.no_sbg = d.No_sbg;
    form.no_kons = d.No_kons;
    form.penaksir = d.Penaksir;
    form.type_transaksi = "Perpanjangan";
    form.sewa_modal = formatDecimal(d.Sewa_modal * 100);
    form.tgl_transaksi = formatDate(d.Tgl_transaksi);
    dateInput.value = formatDate(new Date());
    calculateSewaModal(d);
    const sewaModalValue =
        parseFloat(
            sewaModalAmount.value.replace(/[^\d,]/g, "").replace(",", ".")
        ) || 0;
    const fines = calculateFines(d);
    finesAmount.value = formatRupiah(fines);
    const finesString = fines.toString();
    form.denda_pinjaman =
        parseFloat(finesString.replace(/[^\d,]/g, "").replace(",", ".")) || 0;
    form.old_tgl_jtempo = formatDate(d.Tgl_jtempo);
    form.by_admin = formatRupiah(d.By_admin);
    form.total_taksiran = formatRupiah(d.Total_taksiran);
    form.pokok_pinjaman = formatRupiah(d.Pokok_pinjaman);
    form.maks_pinjaman = formatRupiah(d.Maks_pinjaman);
    form.cara_pencairan = d.Cara_pencairan;
    form.no_rek_nasabah = d.No_rek_nasabah;
    form.nama_bank = d.nasabah.nama_bank;
    form.referensi_nosbg = d.Referensi_nosbg;
    form.asal_barang = d.Asal_barang;
    form.tujuan_transaksi = d.Tujuan_transaksi;
    form.instrumen_pembayaran = d.Instrumen_pembayaran;
    form.pengembalian_uang_lebih = d.Pengembalian_uang_lebih;
    form.fungsi_transaksi = d.Fungsi_transaksi;
    form.foto_identitas = d.Foto_identitas;
    d.Terima_uang != null ? form.terima_uang = d.Terima_uang : form.terima_uang = "";
    selectedTipeGadai.value === 'emas' ? form.golongan = d.Golongan : form.golongan = "";
    form.total_pembayaran = formatRupiah(
        sewaModalValue + parseFloat(d.By_admin) + form.denda_pinjaman
    );
    await getBarangJaminan();

    if (selectedTipeGadai.value === 'emas') {
        form.barangJmn = barangJmn.value.map((item, index) => ({
            no_sbg: item.No_sbg,
            kode_barang: item.Kode_barang,
            jenis: item.Jenis,
            kadar: item.Kadar,
            berat_kotor: item.Berat_kotor,
            berat_bersih: item.Berat_bersih,
            taksiran: item.Taksiran,
            maks_pinjaman: item.Maks_pinjaman,
            detail_barang: item.Detail_barang,
            foto_barang: form.foto_barang[index],
            foto_barang2: form.foto_barang2[index],
            lain_lain: form.lain_lain[index],
        }));
    } else if (selectedTipeGadai.value !== 'emas') {
        form.barangJmn = barangJmn.value.map((item, index) => ({
            no_sbg: item.No_sbg,
            kode_barang: item.Kode_barang,
            merk: item.Merk,
            tipe: item.Tipe,
            tahun: item.Tahun,
            nopol: item.Nopol,
            no_rangka: item.No_rangka,
            no_mesin: item.No_mesin,
            no_bpkb: item.No_bpkb,
            taksiran: item.Taksiran,
            maks_pinjaman: item.Maks_pinjaman,
            detail_barang: item.Detail_barang,
            foto_barang: form.foto_barang[index],
            foto_barang2: form.foto_barang2[index],
            lain_lain: form.lain_lain[index],
        }));
    }
};

const formatBackOri = () => {
    form.by_admin =
        parseFloat(form.by_admin.replace(/[^\d,-]/g, "").replace(".", ",")) ||
        0;
    form.total_taksiran =
        parseFloat(
            form.total_taksiran.replace(/[^\d,-]/g, "").replace(".", ",")
        ) || 0;
    form.pokok_pinjaman =
        parseFloat(
            form.pokok_pinjaman.replace(/[^\d,-]/g, "").replace(".", ",")
        ) || 0;
    form.maks_pinjaman =
        parseFloat(
            form.maks_pinjaman.replace(/[^\d,-]/g, "").replace(".", ",")
        ) || 0;
    form.total_pembayaran =
        parseFloat(
            form.total_pembayaran.replace(/[^\d,-]/g, "").replace(".", ",")
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
    form.by_admin = formatRupiah(form.by_admin);
    form.total_taksiran = formatRupiah(form.total_taksiran);
    form.pokok_pinjaman = formatRupiah(form.pokok_pinjaman);
    form.maks_pinjaman = formatRupiah(form.maks_pinjaman);
    form.total_pembayaran = formatRupiah(form.total_pembayaran);
};

const handleFotoJaminan = (selectedFile, index) => {
    form.foto_barang[index] = selectedFile;
};

const handleFotoJaminan2 = (selectedFile, index) => {
    form.foto_barang2[index] = selectedFile;
};

const handleFilesUploaded = (files, index) => {
    form.lain_lain[index] = files;
};

// Function to check if there’s any error for a given index
const hasErrorForIndex = (index) => {
    return Object.keys(form.errors).some((key) =>
        key.startsWith(`lain_lain.${index}`)
    );
};

// Function to retrieve the first error message for a given index
const getFirstErrorForIndex = (index) => {
    const errorKey = Object.keys(form.errors).find((key) =>
        key.startsWith(`lain_lain.${index}`)
    );
    return errorKey ? form.errors[errorKey][0] : null;
};

const generatePDF = async () => {
    try {
        // Show a loading message while generating the PDF
        Swal.fire({
            title: "Mohon Tunggu...",
            text: "Membuat Surat Pelunasan Perpanjangan.",
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
        const response = await axios.get(route("generatepdfPerpanjang"), {
            responseType: "blob",
            params: {
                no_sbg: form.no_sbg,
                referensi_nosbg: form.referensi_nosbg,
                produk_gadai: form.produk_gadai
            },
        });

        // Create a Blob from the response data
        const blob = new Blob([response.data], { type: "application/pdf" });
        const url = URL.createObjectURL(blob);

        // Create an iframe to load and printDB_ABSENSI the PDF
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

const store = async () => {
    let url =
        selectedTipeGadai.value === "emas"
            ? "storeExtGadai"
            : selectedTipeGadai.value === "motor"
            ? "storeExtGadaiMotor"
            : "";

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
        try {
            // Format data before sending
            formatBackOri();

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

            // Create FormData object
            const formData = new FormData();

            // Add all other fields from `form` except `foto_barang`, `foto_barang2`, and `lain_lain`
            Object.keys(form).forEach((key) => {
                if (
                    key !== "lain_lain" &&
                    key !== "foto_barang" &&
                    key !== "foto_barang2" &&
                    key !== "barangJmn"
                ) {
                    formData.append(key, form[key]);
                }
            });

            // Append `barangJmn` fields as an array with index notation
            form.barangJmn.forEach((item, index) => {
                Object.keys(item).forEach((subKey) => {
                    formData.append(
                        `barangJmn[${index}][${subKey}]`,
                        item[subKey]
                    );
                });
            });

            // Append `foto_barang` files individually to FormData (if there are multiple)
            form.foto_barang.forEach((file, index) => {
                formData.append(`foto_barang[${index}]`, file);
            });

            // Append `foto_barang2` files individually to FormData (if there are multiple)
            form.foto_barang2.forEach((file, index) => {
                formData.append(`foto_barang2[${index}]`, file);
            });

            // Append `lain_lain` files individually to FormData
            form.lain_lain.forEach((filesArray, index) => {
                if (Array.isArray(filesArray)) {
                    filesArray.forEach((file, fileIndex) => {
                        formData.append(
                            `lain_lain[${index}][${fileIndex}]`,
                            file.file
                        );
                    });
                }
            });

            // Perform the API request
            await axios.post(route(url), formData, header);

            // Close the loading alert and show success message
            Swal.fire({
                title: "Berhasil",
                text: "Pinjaman Berhasil di Perpanjang",
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
            generatePDF();
            gadaiData(); // Refresh data
            form.reset(); // Reset the form
            maksTenor.value = null;
            modal.submitPanjang = false;
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
                    title: "text-white",
                    popup: "text-white",
                    confirmButton: "text-black",
                },
            });

            // Reformat data after error
            formatBackRupiah();
        }
    }
};

const openAccordion = ref(null); // Keeps track of the open accordion index

const toggleAccordion = (index) => {
    openAccordion.value = openAccordion.value === index ? null : index;
};
</script>
<template>
    <Head title="Perpanjangan" />
    <AdminLayout>
        <template #titlePage>
            <TitlePage value="Perpanjangan Gadai" />
        </template>
        <template #content>
            <main class="p-6 bg-gray-100 flex-grow overflow-y-auto">
                <div class="mt-6">
                    <div class="bg-white p-4 rounded-lg shadow">
                        <div class="flex justify-between items-center mb-4">
                            <div class="pr-2">
                                <SearchInput
                                    label="Cari Data"
                                    v-model="searchIPer"
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
                                            Aksi
                                        </th>
                                    </tr>
                                </thead>
                                <tbody v-if="!gadaiLoading">
                                    <tr
                                        v-if="gadai.length > 0"
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
                                            <a @click="modalPerpanjangan(d)"
                                                ><img
                                                    class="m-auto cursor-pointer"
                                                    src="/gadaiemas/storage/icon/extended.png"
                                                    title="Perpanjang"
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
                            :show="modal.perpanjangan"
                            @close="[(modal.perpanjangan = false)]"
                            maxWidth="7xl"
                            classHeader="bg-sky-700 text-white"
                        >
                            <template #title> Detail Perpanjangan </template>
                            <template #content>
                                <div class="mt-6 grid grid-cols-6 gap-4">
                                    <div class="sm:col-span-1 pt-5">
                                        <h1
                                            class="tracking-wide block mb-2 text-xs font-bold text-gray-900 uppercase"
                                        >
                                            Tanggal Transaksi :
                                        </h1>
                                    </div>
                                    <div class="sm:col-span-1">
                                        <CustTextInput
                                            :required-indicator="false"
                                            id="tgl_transaksi"
                                            type="date"
                                            name="tgl_transaksi"
                                            v-model="form.tgl_transaksi"
                                            disabled="true"
                                        />
                                    </div>
                                    <div class="sm:col-span-1 pt-5">
                                        <h1
                                            class="tracking-wide block mb-2 text-xs font-bold text-gray-900 uppercase"
                                        >
                                            Penaksir :
                                        </h1>
                                    </div>
                                    <div class="sm:col-span-1">
                                        <CustTextInput
                                            :required-indicator="false"
                                            id="penaksir"
                                            type="text"
                                            name="penaksir"
                                            v-model="form.penaksir"
                                            disabled="true"
                                        />
                                    </div>
                                    <div class="sm:col-span-1 pt-5">
                                        <h1
                                            class="tracking-wide block mb-2 text-xs font-bold text-gray-900 uppercase"
                                        >
                                            Tipe Transaksi :
                                        </h1>
                                    </div>
                                    <div class="sm:col-span-1">
                                        <CustTextInput
                                            :required-indicator="false"
                                            id="type_transaksi"
                                            type="text"
                                            name="type_transaksi"
                                            v-model="form.type_transaksi"
                                            disabled="true"
                                        />
                                    </div>
                                    <div class="sm:col-span-1 pt-5">
                                        <h1
                                            class="tracking-wide block mb-2 text-xs font-bold text-gray-900 uppercase"
                                        >
                                            Total Taksiran :
                                        </h1>
                                    </div>
                                    <div class="sm:col-span-1">
                                        <CustTextInput
                                            :required-indicator="false"
                                            id="total_taksiran"
                                            type="text"
                                            name="total_taksiran"
                                            v-model="form.total_taksiran"
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
                                    <div class="sm:col-span-1">
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
                                            class="tracking-wide block mb-2 text-xs font-bold text-gray-900 uppercase"
                                        >
                                            Maks Pinjaman :
                                        </h1>
                                    </div>
                                    <div class="sm:col-span-1">
                                        <CustTextInput
                                            :required-indicator="false"
                                            id="maks_pinjaman"
                                            type="text"
                                            name="maks_pinjaman"
                                            v-model="form.maks_pinjaman"
                                            disabled="true"
                                        />
                                    </div>
                                    <div class="sm:col-span-1 pt-5">
                                        <h1
                                            class="tracking-wide block mb-2 text-xs font-bold text-gray-900 uppercase"
                                        >
                                            Cara Pencairan :
                                        </h1>
                                    </div>
                                    <div class="sm:col-span-1">
                                        <CustTextInput
                                            :required-indicator="false"
                                            id="cara_pencairan"
                                            type="text"
                                            name="cara_pencairan"
                                            v-model="form.cara_pencairan"
                                            disabled="true"
                                        />
                                    </div>
                                    <div class="sm:col-span-1 pt-5">
                                        <h1
                                            class="tracking-wide block mb-2 text-xs font-bold text-gray-900 uppercase"
                                        >
                                            Nama Bank :
                                        </h1>
                                    </div>
                                    <div class="sm:col-span-1">
                                        <CustTextInput
                                            :required-indicator="false"
                                            id="nama_bank"
                                            type="text"
                                            name="nama_bank"
                                            v-model="form.nama_bank"
                                            disabled="true"
                                        />
                                    </div>
                                    <div class="sm:col-span-1 pt-5">
                                        <h1
                                            class="tracking-wide block mb-2 text-xs font-bold text-gray-900 uppercase"
                                        >
                                            No Rekening :
                                        </h1>
                                    </div>
                                    <div class="sm:col-span-1">
                                        <CustTextInput
                                            :required-indicator="false"
                                            id="no_rek_nasabah"
                                            type="text"
                                            name="no_rek_nasabah"
                                            v-model="form.no_rek_nasabah"
                                            disabled="true"
                                        />
                                    </div>
                                    <div class="sm:col-span-1 pt-5">
                                        <h1
                                            class="tracking-wide block mb-2 text-xs font-bold text-gray-900 uppercase"
                                        >
                                            Referensi No SBG :
                                        </h1>
                                    </div>
                                    <div class="sm:col-span-1">
                                        <CustTextInput
                                            :required-indicator="false"
                                            id="referensi_nosbg"
                                            type="text"
                                            name="referensi_nosbg"
                                            v-model="form.referensi_nosbg"
                                            disabled="true"
                                        />
                                    </div>
                                    <div class="sm:col-span-1 pt-5">
                                        <h1
                                            class="tracking-wide block mb-2 text-xs font-bold text-gray-900 uppercase"
                                        >
                                            Asal Barang :
                                        </h1>
                                    </div>
                                    <div class="sm:col-span-1">
                                        <CustTextInput
                                            :required-indicator="false"
                                            id="asal_barang"
                                            type="text"
                                            name="asal_barang"
                                            v-model="form.asal_barang"
                                            disabled="true"
                                        />
                                    </div>
                                    <div class="sm:col-span-1 pt-5">
                                        <h1
                                            class="tracking-wide block mb-2 text-xs font-bold text-gray-900 uppercase"
                                        >
                                            Tujuan Transaksi :
                                        </h1>
                                    </div>
                                    <div class="sm:col-span-1">
                                        <CustTextInput
                                            :required-indicator="false"
                                            id="tujuan_transaksi"
                                            type="text"
                                            name="tujuan_transaksi"
                                            v-model="form.tujuan_transaksi"
                                            disabled="true"
                                        />
                                    </div>
                                    <div class="sm:col-span-1 pt-5">
                                        <h1
                                            class="tracking-wide block mb-2 text-xs font-bold text-gray-900 uppercase"
                                        >
                                            Instrumen Pembayaran :
                                        </h1>
                                    </div>
                                    <div class="sm:col-span-1">
                                        <CustTextInput
                                            :required-indicator="false"
                                            id="instrumen_pembayaran"
                                            type="text"
                                            name="instrumen_pembayaran"
                                            v-model="form.instrumen_pembayaran"
                                            disabled="true"
                                        />
                                    </div>
                                    <div class="sm:col-span-1 pt-5">
                                        <h1
                                            class="tracking-wide block mb-2 text-xs font-bold text-gray-900 uppercase"
                                        >
                                            Pengembalian Uang Lebih :
                                        </h1>
                                    </div>
                                    <div class="sm:col-span-1">
                                        <CustTextInput
                                            :required-indicator="false"
                                            id="pengembalian_uang_lebih"
                                            type="text"
                                            name="pengembalian_uang_lebih"
                                            v-model="
                                                form.pengembalian_uang_lebih
                                            "
                                            disabled="true"
                                        />
                                    </div>
                                    <div class="sm:col-span-1 pt-5">
                                        <h1
                                            class="tracking-wide block mb-2 text-xs font-bold text-gray-900 uppercase"
                                        >
                                            Fungsi Transaksi :
                                        </h1>
                                    </div>
                                    <div class="sm:col-span-1">
                                        <CustTextInput
                                            :required-indicator="false"
                                            id="fungsi_transaksi"
                                            type="text"
                                            name="fungsi_transaksi"
                                            v-model="form.fungsi_transaksi"
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
                                                @click="
                                                    (modal.perpanjangan = false),
                                                        modalSubmitPanjang()
                                                "
                                                class="mb-10 ml-2 bg-green-700 text-white py-1 px-4 rounded"
                                            >
                                                Lanjut
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </template>
                        </Modal>
                        <Modal
                            :show="modal.submitPanjang"
                            @close="[(modal.submitPanjang = false)]"
                            maxWidth="2xl"
                            classHeader="bg-sky-700 text-white"
                        >
                            <template #title> Proses Perpanjangan </template>
                            <template #content>
                                <div class="mt-6 grid grid-cols-2 gap-4">
                                    <div class="sm:col-span-1 pt-5">
                                        <h1
                                            class="tracking-wide block mb-2 text-xs font-bold text-gray-900 uppercase"
                                        >
                                            Tanggal Perpanjangan :
                                        </h1>
                                    </div>
                                    <div class="sm:col-span-1">
                                        <CustTextInput
                                            :requiredIndicator="false"
                                            id="tgl_perpanjangan"
                                            type="date"
                                            name="tgl_perpanjangan"
                                            v-model="dateInput"
                                            disabled="true"
                                        />
                                    </div>
                                    <div class="sm:col-span-1 pt-5">
                                        <h1
                                            class="tracking-wide block mb-2 text-xs font-bold text-gray-900 uppercase"
                                        >
                                            Tgl Jatuh Tempo Lama:
                                        </h1>
                                    </div>
                                    <div class="sm:col-span-1">
                                        <CustTextInput
                                            :required-indicator="false"
                                            id="old_tgl_jtempo"
                                            type="date"
                                            name="old_tgl_jtempo"
                                            v-model="form.old_tgl_jtempo"
                                            disabled="true"
                                        />
                                    </div>
                                    <div class="sm:col-span-1 pt-5">
                                        <h1
                                            class="tracking-wide block mb-2 text-xs font-bold text-gray-900 uppercase"
                                        >
                                            Tgl Jatuh Tempo Baru:
                                        </h1>
                                    </div>
                                    <div class="sm:col-span-1">
                                        <CustTextInput
                                            :required-indicator="false"
                                            id="new_tgl_jtempo"
                                            type="date"
                                            name="new_tgl_jtempo"
                                            v-model="form.new_tgl_jtempo"
                                            disabled="true"
                                        />
                                    </div>
                                    <div class="sm:col-span-1 pt-5">
                                        <h1
                                            class="tracking-wide block mb-2 text-xs font-bold text-gray-900 uppercase"
                                        >
                                            Sewa Modal :
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
                                    <div class="sm:col-span-1 pt-5">
                                        <h1
                                            class="tracking-wide block mb-2 text-xs font-bold text-gray-900 uppercase"
                                        >
                                            Biaya Admin :
                                        </h1>
                                    </div>
                                    <div class="sm:col-span-1">
                                        <CustTextInput
                                            :required-indicator="false"
                                            id="by_admin"
                                            type="text"
                                            name="by_admin"
                                            v-model="form.by_admin"
                                            disabled="true"
                                        />
                                    </div>
                                    <div class="sm:col-span-1 pt-5">
                                        <h1
                                            class="tracking-wide block mb-2 text-xs font-bold text-gray-900 uppercase"
                                        >
                                            Bunga Sampai Tgl Jtempo :
                                        </h1>
                                    </div>
                                    <div class="sm:col-span-1">
                                        <CustTextInput
                                            :required-indicator="false"
                                            id="by_admin"
                                            type="text"
                                            name="by_admin"
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
                                    <div class="sm:col-span-1">
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
                                            Tenor (Hari) :
                                        </h1>
                                    </div>
                                    <div v-if="selectedTipeGadai === 'emas'" class="sm:col-span-1">
                                        <NestedSelect
                                            :options="TenorOptions"
                                            v-model="maksTenor"
                                            :required-indicator="false"
                                            @focusin="
                                                maksTenor = '';
                                                dropdownStates.tenorState = true;
                                            "
                                            @focusout="
                                                dropdownStates.tenorState = false
                                            "
                                        />
                                        <div v-if="form.errors.tenor">
                                            <span class="text-sm text-red-600"
                                                >*{{
                                                    form.errors.tenor[0]
                                                }}</span
                                            >
                                        </div>
                                    </div>
                                    <div v-if="selectedTipeGadai === 'motor' || selectedTipeGadai === 'mobil'" class="sm:col-span-1">
                                        <NestedSelect
                                            :options="['15', '30', '60', '90']"
                                            v-model="maksTenor"
                                            :required-indicator="false"
                                            @focusin="
                                                maksTenor = '';
                                                dropdownStates.tenorState = true;
                                            "
                                            @focusout="
                                                dropdownStates.tenorState = false
                                            "
                                        />
                                        <div v-if="form.errors.tenor">
                                            <span class="text-sm text-red-600"
                                                >*{{
                                                    form.errors.tenor[0]
                                                }}</span
                                            >
                                        </div>
                                    </div>
                                    <div class="sm:col-span-1 pt-5">
                                        <h1
                                            v-if="maksTenor"
                                            class="tracking-wide block mb-2 text-xs font-bold text-gray-900 uppercase"
                                        >
                                            Cara Bayar :
                                        </h1>
                                    </div>
                                    <div class="sm:col-span-1">
                                        <NestedSelect
                                            :required-indicator="false"
                                            :options="caraBayarOptions"
                                            v-model="form.cara_bayar"
                                            @focusin="
                                                form.cara_bayar = '';
                                                dropdownStates.caraBayarState = true;
                                            "
                                            @focusout="
                                                dropdownStates.caraBayarState = false
                                            "
                                            v-if="
                                                maksTenor &&
                                                !dropdownStates.tenorState
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
                                            v-if="form.cara_bayar && maksTenor"
                                            class="tracking-wide block mb-2 text-xs font-bold text-gray-900 uppercase"
                                        >
                                            Jenis Pembayaran :
                                        </h1>
                                    </div>
                                    <div class="sm:col-span-1">
                                        <NestedSelect
                                            :required-indicator="false"
                                            :options="jenisBayarOptions"
                                            v-model="form.jenis_pembayaran"
                                            @focusin="
                                                form.jenis_pembayaran = '';
                                                dropdownStates.jenisBayarState = true;
                                            "
                                            @focusout="
                                                dropdownStates.jenisBayarState = false
                                            "
                                            v-if="
                                                form.cara_bayar &&
                                                maksTenor &&
                                                !dropdownStates.caraBayarState &&
                                                !dropdownStates.tenorState
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
                                    <div class="sm:col-span-1">
                                        <CustTextInput
                                            :required-indicator="false"
                                            id="pokok_pinjaman"
                                            type="text"
                                            name="pokok_pinjaman"
                                            v-model="form.total_pembayaran"
                                            disabled="true"
                                        />
                                    </div>
                                    <div
                                        v-for="(
                                            item, index
                                        ) in barangJmn.length"
                                        :key="index"
                                        class="sm:col-span-2"
                                    >
                                        <!-- Accordion Toggle Button -->
                                        <div
                                            @click="toggleAccordion(index)"
                                            class="cursor-pointer border-b-2 border-gray-700 py-2 flex justify-between items-center"
                                        >
                                            <h3 class="text-md font-semibold">
                                                Barang Jaminan #{{ index + 1 }}
                                            </h3>
                                            <!-- Up/Down Icon -->
                                            <span class="text-md">
                                                <!-- If open, show 'up' icon, otherwise show 'down' icon -->
                                                {{
                                                    openAccordion === index
                                                        ? "▲"
                                                        : "▼"
                                                }}
                                            </span>
                                        </div>

                                        <!-- Accordion Content -->
                                        <div
                                            v-if="openAccordion === index"
                                            class="sm:col-span-2"
                                        >
                                            <div class="sm:col-span-2 pt-5">
                                                <UploadImage
                                                    label="Foto Barang Jaminan Sisi Pertama"
                                                    v-model="
                                                        form.foto_barang[index]
                                                    "
                                                    @file-selected="
                                                        (file) =>
                                                            handleFotoJaminan(
                                                                file,
                                                                index
                                                            )
                                                    "
                                                />
                                                <span
                                                    v-if="
                                                        !form.errors.foto_barang
                                                    "
                                                    class="text-sm text-black"
                                                    >* Format: JPEG,JPG,PNG &
                                                    MAKSIMAL 2MB</span
                                                >
                                                <div
                                                    v-if="
                                                        form.errors.foto_barang
                                                    "
                                                >
                                                    <span
                                                        class="text-sm text-red-600"
                                                        >*{{
                                                            form.errors
                                                                .foto_barang[0]
                                                        }}</span
                                                    >
                                                </div>
                                            </div>
                                            <div class="sm:col-span-2 pt-5">
                                                <UploadImage
                                                    label="Foto Barang Jaminan Sisi Kedua"
                                                    v-model="
                                                        form.foto_barang2[index]
                                                    "
                                                    @file-selected="
                                                        (file) =>
                                                            handleFotoJaminan2(
                                                                file,
                                                                index
                                                            )
                                                    "
                                                />
                                                <span
                                                    v-if="
                                                        !form.errors
                                                            .foto_barang2
                                                    "
                                                    class="text-sm text-black"
                                                    >* Format: JPEG,JPG,PNG &
                                                    MAKSIMAL 2MB</span
                                                >
                                                <div
                                                    v-if="
                                                        form.errors.foto_barang2
                                                    "
                                                >
                                                    <span
                                                        class="text-sm text-red-600"
                                                        >*{{
                                                            form.errors
                                                                .foto_barang2[0]
                                                        }}</span
                                                    >
                                                </div>
                                            </div>
                                            <div class="sm:col-span-2 pt-5">
                                                <UploadMultiImage
                                                    label="Foto Tambahan"
                                                    v-model="
                                                        form.lain_lain[index]
                                                    "
                                                    @files-uploaded="
                                                        (file) =>
                                                            handleFilesUploaded(
                                                                file,
                                                                index
                                                            )
                                                    "
                                                />
                                                <!-- Show format message and general error for this index -->
                                                <span
                                                    v-if="
                                                        !hasErrorForIndex(index)
                                                    "
                                                    class="text-sm text-black"
                                                >
                                                    * Format: JPEG,JPG,PNG &
                                                    MAKSIMAL 2MB
                                                </span>
                                                <div
                                                    v-if="
                                                        hasErrorForIndex(index)
                                                    "
                                                >
                                                    <span
                                                        class="text-sm text-red-600"
                                                    >
                                                        *{{
                                                            getFirstErrorForIndex(
                                                                index
                                                            )
                                                        }}
                                                    </span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div
                                    class="flex justify-end items-center mt-14"
                                >
                                    <div class="ml-4">
                                        <div class="text-black">
                                            <button
                                                @click="
                                                    (modal.submitPanjang = false),
                                                        (modal.perpanjangan = true)
                                                "
                                                class="mb-10 ml-2 bg-red-700 text-white py-1 px-4 rounded"
                                            >
                                                Kembali
                                            </button>
                                            <button
                                                @click="store()"
                                                class="mb-10 ml-2 bg-green-700 text-white py-1 px-4 rounded"
                                            >
                                                Submit
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