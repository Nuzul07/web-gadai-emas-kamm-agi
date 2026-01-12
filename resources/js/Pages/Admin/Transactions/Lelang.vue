<script setup>
import { ref, onMounted, watch, computed, reactive } from "vue";
import AdminLayout from "@/Layouts/AdminLayout.vue";
import TitlePage from "@/Components/TitlePage.vue";
import SearchInput from "@/Components/SearchInput.vue";
import CustTextInput from "@/Components/CustTextInput.vue";
import InputFormatIDR from "@/Components/InputFormatIDR.vue";
import Modal from "@/Components/DialogModal.vue";
import { Head, useForm } from "@inertiajs/vue3";
import axios, { formToJSON } from "axios";
import Swal from "sweetalert2";
import dayjs from "dayjs";
import duration from "dayjs/plugin/duration";
import { selectedTipeGadai } from "@/src/store";

dayjs.extend(duration);

onMounted(() => {
    getNoLelang();
    golonganData();
});

defineProps({
    errors: Object,
});

const form = useForm({
    k_kons: "",
    k_pembeli: "",
    nama_kons: "",
    nama_pembeli: "",
    alamat_pembeli: "",
    kecamatan: "",
    kelurahan: "",
    kodepos: "",
    no_telp: "",
    no_hp: "",
    kode_barang: "",
    nilai_lelang: "",
    nilai_buku: "",
    keterangan: "",
    untung_rugi: "",
});

const selectedItem = reactive({
    nilai_lelang: "",
    keterangan: "",
    untung_rugi: "",
});

const modal = reactive({
    nasabah: false,
    barang: false,
    lelang: false,
});

const modalNasabah = () => {
    modal.nasabah = true;
    nasabahData();
};

//fetch data nasabah from db
const nasabah = ref([]);
const nasabahLoading = ref(false);
const nasabahData = async () => {
    nasabahLoading.value = true;
    await axios.get(route("getAllNasabah")).then((res) => {
        const data = res.data;
        nasabah.value = data;
        nasabahLoading.value = false;
    });
};
//

const crntPage = ref(1);
const perPage = 10;

const totalPages = computed(() => Math.ceil(nasabah.value.length / perPage));

const pgntNasabah = computed(() => {
    const start = (crntPage.value - 1) * perPage;
    const end = Math.min(start + perPage, searchNasabah.value.length);
    return searchNasabah.value.slice(start, end).map((nasabah, index) => ({
        ...nasabah,
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

const searchINas = ref("");
const searchNasabah = computed(() => {
    if (!searchINas.value) {
        return nasabah.value;
    }
    const lowerCase = searchINas.value.toLowerCase();
    if (nasabah.value) {
        return nasabah.value.filter((nas) => {
            return (
                nas.K_Kons.toLowerCase().includes(lowerCase) ||
                nas.No_ktp.toLowerCase().includes(lowerCase) ||
                nas.Nama.toLowerCase().includes(lowerCase) ||
                nas.No_tlp_HP.toLowerCase().includes(lowerCase) ||
                nas.email.toLowerCase().includes(lowerCase)
            );
        });
    } else {
        return [];
    }
});

const selectDataNas = (d) => {
    Swal.fire({
        title: "Berhasil",
        text: "Memlilih Nasabah",
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
    form.k_pembeli = d.K_Kons;
    form.nama_pembeli = d.Nama;
    form.alamat_pembeli = d.Alamat_Domisili;
    form.kecamatan = d.Kecamatan_Domisili;
    form.kelurahan = d.Kelurahan_Domisili;
    form.kodepos = d.Kodepos_Domisili;
    form.no_telp = d.No_tlp_rm;
    form.no_hp = d.No_tlp_HP;
    modal.nasabah = false;
};

const noLelang = ref("");
const getNoLelang = async () => {
    await axios.get(route("getNoLelang")).then((res) => {
        const data = res.data;
        noLelang.value = data;
        console.log(noLelang.value);
    });
};

const barangLoading = ref(false);
const filteredBarang = ref([]);
const barangData = async () => {
    barangLoading.value = true;
    let url =
        selectedTipeGadai.value === "emas"
            ? "getGadaiEmasActive"
            : selectedTipeGadai.value === "motor"
            ? "getGadaiMotorActive"
            : "getGadaiMobilActive";

    await axios.get(route(url)).then((res) => {
        const data = res.data;

        const currentDate = dayjs();

        const filteredData = data.filter((item) => {
            const tgl_jtempo = dayjs(item.Tgl_jtempo);
            const dayDiff = currentDate.diff(tgl_jtempo, "day");
            console.log(`No_kons: ${item.No_kons} | Jtempo: ${item.Tgl_jtempo} | Diff: ${dayDiff}`);
            return dayDiff >= 14;
        });

        console.log('Filtered Overdue Data:', filteredData);

        filteredBarang.value = filteredData.flatMap((item) => {
            if (!item.barang_emas || !Array.isArray(item.barang_emas)) return []; // 👈 guard clause

            return item.barang_emas
                .filter((barang) => barang.Status_barang == 1)
                .map((barang) => {
                    const fines = calculateFines(item);
                    const tgl_jtempo = dayjs(item.Tgl_jtempo);
                    const dayDiff1 = currentDate.diff(tgl_jtempo, "day");
                    const nilai_buku =
                        parseFloat(barang.Taksiran) + parseFloat(fines);
                    return {
                        nomor_lelang: noLelang.value,
                        nama_pembeli: form.nama_pembeli,
                        alamat_pembeli: form.alamat_pembeli,
                        kecamatan: form.kecamatan,
                        kelurahan: form.kelurahan,
                        kodepos: form.kodepos,
                        no_telp: form.no_telp,
                        no_hp: form.no_hp,
                        k_kons: item.No_kons,
                        nama_kons: item.nasabah ? item.nasabah.Nama : "",
                        pokok_pinjaman: parseFloat(item.Pokok_pinjaman),
                        tgl_pinjam: item.Tgl_transaksi,
                        tenor: parseInt(item.Tenor),
                        terlambat: parseInt(dayDiff1),
                        asal_barang: item.Asal_barang,
                        fines: parseFloat(fines),
                        nilai_buku: parseFloat(nilai_buku),
                        ...barang,
                    };
                });
        });

        console.log(filteredBarang.value);
        barangLoading.value = false;
    });
};

const pilihBarang = () => {
    if (form.nama_pembeli !== "") {
        modal.barang = true;
        barangData();
    } else {
        Swal.fire({
            title: "Terjadi kesalahan",
            text: "Pilih Nasabah Terlebih Dahulu",
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

const modalLelang = (item) => {
    // Populate the form with the selected item's data
    Object.assign(selectedItem, item);
    form.kode_barang = item.Kode_barang;
    form.nilai_lelang = item.nilai_lelang || "";
    form.keterangan = item.keterangan || "";
    form.untung_rugi = item.untung_rugi || "";
    modal.lelang = true;
    selectLelang(item);
};

const crntPageBarang = ref(1);
const perPageBarang = 10;

const totalPagesBarang = computed(() =>
    Math.ceil(filteredBarang.value.length / perPageBarang)
);

const pgntBarang = computed(() => {
    const start = (crntPageBarang.value - 1) * perPageBarang;
    const end = Math.min(start + perPageBarang, searchBarang.value.length);
    return searchBarang.value.slice(start, end).map((barang, index) => ({
        ...barang,
        no: start + index + 1,
    }));
});

const prevPageBarang = () => {
    if (crntPageBarang.value > 1) {
        crntPageBarang.value--;
    }
};

const nextPageBarang = () => {
    if (crntPageBarang.value < totalPagesBarang.value) {
        crntPageBarang.value++;
    }
};

const searchIBrg = ref("");
const searchBarang = computed(() => {
    if (!searchIBrg.value) {
        return filteredBarang.value;
    }
    const lowerCase = searchIBrg.value.toLowerCase();
    if (filteredBarang.value) {
        return filteredBarang.value.filter((brg) => {
            return (
                brg.Kode_barang.toLowerCase().includes(lowerCase) ||
                brg.No_sbg.toLowerCase().includes(lowerCase) ||
                brg.Jenis.toLowerCase().includes(lowerCase) ||
                brg.Berat_bersih.toLowerCase().includes(lowerCase) ||
                brg.Kadar.toLowerCase().includes(lowerCase)
            );
        });
    } else {
        return [];
    }
});

const formatRupiah = (value) => {
    const numberFormat = new Intl.NumberFormat("id-ID", {
        style: "currency",
        currency: "IDR",
        minimumFractionDigits: 0, // No decimal places
        maximumFractionDigits: 0, // No decimal places
    });
    return numberFormat.format(value);
};

const lelangTemp = ref([]);

const selectBarang = (barang) => {
    // Check if the item is already in the array (to avoid duplicates)
    const exists = lelangTemp.value.find(
        (item) => item.Kode_barang === barang.Kode_barang
    );

    if (!exists) {
        Swal.fire({
            title: "Berhasil",
            text: "Memlilih Barang Lelang",
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
        lelangTemp.value.push(barang); // Add the selected item to the array
        modal.barang = false;
    } else {
        Swal.fire({
            title: "Terjadi kesalahan",
            text: "Barang tersebut sudah dipilih",
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

const selectLelang = (item) => {
    form.kode_barang = item.Kode_barang;
    form.nilai_buku = item.nilai_buku;
    if (item.nilai_lelang && item.keterangan) {
        form.nilai_lelang = formatRupiah(item.nilai_lelang);
        form.keterangan = item.keterangan;
        form.untung_rugi = formatRupiah(item.untung_rugi);
    }
};

const addToLelang = async () => {
    selectedItem.nilai_lelang = form.nilai_lelang;
    selectedItem.keterangan = form.keterangan;
    selectedItem.untung_rugi = form.untung_rugi;

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
        const newBarang = {
            nilai_lelang: parseFloat(form.nilai_lelang),
            keterangan: form.keterangan,
            untung_rugi: parseFloat(form.untung_rugi),
        };

        const index = lelangTemp.value.findIndex(
            (item) => item.Kode_barang === form.kode_barang
        );

        if (index !== -1) {
            // Merge the new fields with the existing object using the spread operator
            lelangTemp.value[index] = {
                ...lelangTemp.value[index], // Keep the existing fields
                ...newBarang, // Update with the new fields
            };
        }
        Swal.fire({
            title: "Berhasil",
            text: "Nominal Lelang Sudah Terinput",
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
        console.log(lelangTemp.value);
        modal.lelang = false;
    }
};

const resetFormLelang = () => {
    form.reset();
    lelangTemp.value = [];
};

watch(
    () => form.nilai_lelang,
    (lelang) => {
        if (lelang !== "") {
            let ur =
                parseFloat(
                    form.nilai_lelang.replace(/[^\d,]/g, "").replace(",", ".")
                ) - parseFloat(form.nilai_buku);
            form.untung_rugi = formatRupiah(ur);
            console.log(form.untung_rugi);
        } else {
            form.untung_rugi = "";
        }
    }
);

const generatePDF = async () => {
    try {
        // Show a loading message while generating the PDF
        Swal.fire({
            title: "Mohon Tunggu...",
            text: "Membuat Surat Permohonan Pembelian Barang Lelang.",
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
        const response = await axios.get(route("generatepdfMohonLelang"), {
            responseType: "blob",
            params: {
                nomor_lelang: noLelang.value,
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
            text: "Terjadi kesalahan saat membuat surat.",
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
                lelangTemp: lelangTemp.value, // This will send the array as 'lelangTemp'
            };
            console.log(lelangTemp.value);
            // Perform the API request
            await axios.post(route("storeLelang"), lelangArray, header);

            Swal.fire({
                title: "Berhasil",
                text: "Mohon tunggu cetakan surat lunas",
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

const golongan = ref([]);
const golonganData = async () => {
    await axios.get(route("getGolongan")).then((res) => {
        const data = res.data;
        golongan.value = data;
    });
};

const calculateFines = (d) => {
    const pokokPinjaman = d.Pokok_pinjaman;
    const payDate = dayjs().startOf("day");
    const borrowDate = dayjs(d.Tgl_jtempo).startOf("day");

    const diffDays = payDate.diff(borrowDate, "day");
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
    } else if (selectedTipeGadai.value !== 'emas') {
        if (diffDays > 0) {
            const periods = Math.floor((diffDays - 1) / 15); // Calculate how many 15-day periods have passed
            const rate = 0.025 + periods * 0.025; // Increase rate by 0.025 for each 15-day period
            console.log(periods, rate)
            fines = (pokokPinjaman * rate).toFixed(0);
        }
    }

    return fines;
};
</script>
<template>
    <Head title="Pengajuan Lelang" />
    <AdminLayout>
        <template #titlePage>
            <TitlePage value="Pengajuan Lelang" />
        </template>
        <template #content>
            <main class="p-6 bg-gray-100 flex-grow overflow-y-auto">
                <div class="mt-6">
                    <div class="bg-white p-4 rounded-lg shadow">
                        <h2 class="my-2 font-bold uppercase">Form Pengajuan</h2>
                        <div class="grid grid-cols-4 gap-2">
                            <div class="sm:col-span-2">
                                <CustTextInput
                                    label="No Pengajuan"
                                    id="no_lelang"
                                    type="text"
                                    name="no_lelang"
                                    v-model="noLelang"
                                    :disabled="true"
                                />
                            </div>
                            <div class="sm:col-span-2">
                                <CustTextInput
                                    label="Kode Nasabah"
                                    id="k_kons"
                                    type="text"
                                    name="k_kons"
                                    v-model="form.k_pembeli"
                                    :readonly="true"
                                    subClass="cursor-pointer"
                                    @click="modalNasabah()"
                                />
                            </div>
                            <div class="my-1 col-span-4">
                                <hr class="border-2 border-gray-500" />
                            </div>
                            <div class="sm:col-span-2">
                                <CustTextInput
                                    label="Nama Pembeli"
                                    id="nama_pembeli"
                                    type="text"
                                    name="nama_pembeli"
                                    v-model="form.nama_pembeli"
                                    :disabled="true"
                                />
                            </div>
                            <div class="sm:col-span-2">
                                <CustTextInput
                                    label="Alamat"
                                    id="alamat"
                                    type="text"
                                    name="alamat"
                                    v-model="form.alamat_pembeli"
                                    :disabled="true"
                                />
                            </div>
                            <div class="sm:col-span-2">
                                <CustTextInput
                                    label="Kecamatan"
                                    id="kecamatan"
                                    type="text"
                                    name="kecamatan"
                                    v-model="form.kecamatan"
                                    :disabled="true"
                                />
                            </div>
                            <div class="sm:col-span-2">
                                <CustTextInput
                                    label="Kelurahan"
                                    id="kelurahan"
                                    type="text"
                                    name="kelurahan"
                                    v-model="form.kelurahan"
                                    :disabled="true"
                                />
                            </div>
                            <div class="sm:col-span-2">
                                <CustTextInput
                                    label="Kode Pos"
                                    id="kode_pos"
                                    type="text"
                                    name="kode_pos"
                                    v-model="form.kodepos"
                                    :disabled="true"
                                />
                            </div>
                            <div class="sm:col-span-1">
                                <CustTextInput
                                    label="No Telp"
                                    id="no_telp"
                                    type="text"
                                    name="no_telp"
                                    v-model="form.no_telp"
                                    :disabled="true"
                                />
                            </div>
                            <div class="sm:col-span-1">
                                <CustTextInput
                                    label="No HP"
                                    id="no_hp"
                                    type="text"
                                    name="no_hp"
                                    v-model="form.no_hp"
                                    :disabled="true"
                                />
                            </div>
                            <div class="my-1 col-span-4">
                                <hr class="border-2 border-gray-500" />
                            </div>
                        </div>
                        <!-- Modal Nasabah -->
                        <Modal
                            :show="modal.nasabah"
                            @close="[(modal.nasabah = false)]"
                            maxWidth="5xl"
                            classHeader="bg-sky-700 text-white"
                        >
                            <template #title> Daftar Nasabah</template>
                            <template #content>
                                <div class="my-6">
                                    <div class="pr-2-">
                                        <SearchInput
                                            label="Cari Data"
                                            v-model="searchINas"
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
                                                        kode konsumen
                                                    </th>
                                                    <th
                                                        scope="col"
                                                        class="text-center text-md font-sans uppercase pb-3 text-white"
                                                    >
                                                        nomor identitas
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
                                                        nomor telepon
                                                    </th>
                                                    <th
                                                        scope="col"
                                                        class="text-center text-md font-sans uppercase pb-3 text-white"
                                                    >
                                                        email
                                                    </th>
                                                </tr>
                                            </thead>
                                            <tbody v-if="!nasabahLoading">
                                                <tr
                                                    v-for="(
                                                        d, index
                                                    ) in pgntNasabah"
                                                    :key="index"
                                                    class="odd:bg-white even:bg-amber-400 cursor-pointer"
                                                    @click="selectDataNas(d)"
                                                >
                                                    <td
                                                        class="whitespace-nowrap text-sm text-center text-black py-2 cursor-pointer"
                                                    >
                                                        {{ d.no }}
                                                    </td>
                                                    <td
                                                        class="whitespace-nowrap text-sm text-center text-black py-2 cursor-pointer"
                                                    >
                                                        {{ d.K_Kons }}
                                                    </td>
                                                    <td
                                                        class="whitespace-nowrap text-sm text-center text-black py-2 cursor-pointer"
                                                    >
                                                        {{ d.No_ktp }}
                                                    </td>
                                                    <td
                                                        class="whitespace-nowrap text-sm text-center text-black py-2 cursor-pointer"
                                                    >
                                                        {{ d.Nama }}
                                                    </td>
                                                    <td
                                                        class="whitespace-nowrap text-sm text-center text-black py-2 cursor-pointer"
                                                    >
                                                        {{ d.No_tlp_HP }}
                                                    </td>
                                                    <td
                                                        class="whitespace-nowrap text-sm text-center text-black py-2 cursor-pointer"
                                                    >
                                                        {{ d.email }}
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                        <div class="w-full text-center mt-2">
                                            <span
                                                class="text-center text-xl text-white"
                                                v-if="nasabahLoading"
                                                >Sedang Memuat...</span
                                            >
                                        </div>
                                        <div class="w-full text-center mt-2">
                                            <span
                                                class="text-center text-xl text-white"
                                                v-if="
                                                    !nasabahLoading &&
                                                    nasabah.length < 1
                                                "
                                                >Tidak Ada Data</span
                                            >
                                        </div>
                                        <div class="w-full text-center mt-2">
                                            <span 
                                                class="text-center text-xl text-white"
                                                v-if="!nasabahLoading && searchNasabah.length < 1"
                                                >Data Tidak Ditemukan</span
                                            >
                                        </div>
                                    </div>
                                    <div
                                        class="flex space-x-2 mt-2"
                                        v-if="!nasabahLoading"
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
                        <div class="ml-2">
                            <div class="text-black">
                                <button
                                    class="mt-4 bg-green-700 text-white py-1 px-4 rounded"
                                    @click="pilihBarang()"
                                >
                                    Pilih Barang
                                </button>
                            </div>
                        </div>
                        <Modal
                            :show="modal.barang"
                            @close="[(modal.barang = false)]"
                            maxWidth="5xl"
                            classHeader="bg-sky-700 text-white"
                        >
                            <template #title> Daftar Barang Lelang</template>
                            <template #content>
                                <div class="mt-6">
                                    <div class="pr-2-">
                                        <SearchInput
                                            label="Cari Data"
                                            v-model="searchIBrg"
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
                                                        kode barang
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
                                                        jenis
                                                    </th>
                                                    <th
                                                        scope="col"
                                                        class="text-center text-md font-sans uppercase pb-3 text-white"
                                                    >
                                                        berat bersih
                                                    </th>
                                                    <th
                                                        scope="col"
                                                        class="text-center text-md font-sans uppercase pb-3 text-white"
                                                    >
                                                        kadar
                                                    </th>
                                                    <th
                                                        scope="col"
                                                        class="text-center text-md font-sans uppercase pb-3 text-white"
                                                    >
                                                        Nilai Pinjaman
                                                    </th>
                                                    <th
                                                        scope="col"
                                                        class="text-center text-md font-sans uppercase pb-3 text-white"
                                                    >
                                                        Denda
                                                    </th>
                                                    <th
                                                        scope="col"
                                                        class="text-center text-md font-sans uppercase pb-3 text-white"
                                                    >
                                                        Nilai Buku
                                                    </th>
                                                </tr>
                                            </thead>
                                            <tbody v-if="!barangLoading">
                                                <tr
                                                    v-for="(
                                                        d, index
                                                    ) in pgntBarang"
                                                    :key="index"
                                                    class="odd:bg-white even:bg-amber-400"
                                                    @click="selectBarang(d)"
                                                >
                                                    <td
                                                        class="whitespace-nowrap text-sm text-center text-black py-2 cursor-pointer"
                                                    >
                                                        {{ d.no }}
                                                    </td>
                                                    <td
                                                        class="whitespace-nowrap text-sm text-center text-black py-2 cursor-pointer"
                                                    >
                                                        {{ d.Kode_barang }}
                                                    </td>
                                                    <td
                                                        class="whitespace-nowrap text-sm text-center text-black py-2 cursor-pointer"
                                                    >
                                                        {{ d.No_sbg }}
                                                    </td>
                                                    <td
                                                        class="whitespace-nowrap text-sm text-center text-black py-2 cursor-pointer"
                                                    >
                                                        {{ d.Jenis }}
                                                    </td>
                                                    <td
                                                        class="whitespace-nowrap text-sm text-center text-black py-2 cursor-pointer"
                                                    >
                                                        {{ d.Berat_bersih }}
                                                    </td>
                                                    <td
                                                        class="whitespace-nowrap text-sm text-center text-black py-2 cursor-pointer"
                                                    >
                                                        {{ d.Kadar }}
                                                    </td>
                                                    <td
                                                        class="whitespace-nowrap text-sm text-center text-black py-2 cursor-pointer"
                                                    >
                                                        {{
                                                            formatRupiah(
                                                                d.pokok_pinjaman
                                                            )
                                                        }}
                                                    </td>
                                                    <td
                                                        class="whitespace-nowrap text-sm text-center text-black py-2 cursor-pointer"
                                                    >
                                                        {{
                                                            formatRupiah(
                                                                d.fines
                                                            )
                                                        }}
                                                    </td>
                                                    <td
                                                        class="whitespace-nowrap text-sm text-center text-black py-2 cursor-pointer"
                                                    >
                                                        {{
                                                            formatRupiah(
                                                                d.nilai_buku
                                                            )
                                                        }}
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                        <div class="w-full text-center mt-2">
                                            <span
                                                class="text-center text-xl text-white"
                                                v-if="barangLoading"
                                                >Sedang Memuat...</span
                                            >
                                        </div>
                                        <div class="w-full text-center mt-2">
                                            <span
                                                class="text-center text-xl text-white"
                                                v-if="
                                                    !barangLoading &&
                                                    filteredBarang.length < 1
                                                "
                                                >Tidak Ada Data</span
                                            >
                                        </div>
                                    </div>
                                    <div
                                        class="flex space-x-2 mt-2 mb-5"
                                        v-if="!barangLoading"
                                    >
                                        <button @click="prevPageBarang">
                                            <i
                                                ><img
                                                src="/gadaiemas/storage/icon/previous.png"
                                                title="Previous"</i
                                            >
                                        </button>
                                        <p class="p-1">
                                            Page {{ crntPageBarang }} of
                                            {{ totalPagesBarang }}
                                        </p>
                                        <button @click="nextPageBarang">
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
                        <h3 class="text-lg font-bold mt-8">
                            Barang Lelang yang Dipilih :
                        </h3>
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
                                            kode barang
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
                                            jenis
                                        </th>
                                        <th
                                            scope="col"
                                            class="text-center text-md font-sans uppercase pb-3 text-white"
                                        >
                                            berat bersih
                                        </th>
                                        <th
                                            scope="col"
                                            class="text-center text-md font-sans uppercase pb-3 text-white"
                                        >
                                            kadar
                                        </th>
                                        <th
                                            scope="col"
                                            class="text-center text-md font-sans uppercase pb-3 text-white"
                                        >
                                            Nilai Pinjaman
                                        </th>
                                        <th
                                            scope="col"
                                            class="text-center text-md font-sans uppercase pb-3 text-white"
                                        >
                                            Denda
                                        </th>
                                        <th
                                            scope="col"
                                            class="text-center text-md font-sans uppercase pb-3 text-white"
                                        >
                                            Nilai Buku
                                        </th>
                                        <th
                                            scope="col"
                                            class="text-center text-md font-sans uppercase pb-3 text-white"
                                        >
                                            Lelang
                                        </th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr
                                        v-for="(item, idx) in lelangTemp"
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
                                            {{ item.Kode_barang }}
                                        </td>
                                        <td
                                            class="whitespace-nowrap text-sm text-center text-black py-2"
                                        >
                                            {{ item.No_sbg }}
                                        </td>
                                        <td
                                            class="whitespace-nowrap text-sm text-center text-black py-2"
                                        >
                                            {{ item.Jenis }}
                                        </td>
                                        <td
                                            class="whitespace-nowrap text-sm text-center text-black py-2"
                                        >
                                            {{ item.Berat_bersih }}
                                        </td>
                                        <td
                                            class="whitespace-nowrap text-sm text-center text-black py-2"
                                        >
                                            {{ item.Kadar }}
                                        </td>
                                        <td
                                            class="whitespace-nowrap text-sm text-center text-black py-2"
                                        >
                                            {{
                                                formatRupiah(
                                                    item.pokok_pinjaman
                                                )
                                            }}
                                        </td>
                                        <td
                                            class="whitespace-nowrap text-sm text-center text-black py-2"
                                        >
                                            {{ formatRupiah(item.fines) }}
                                        </td>
                                        <td
                                            class="whitespace-nowrap text-sm text-center text-black py-2 cursor-pointer"
                                        >
                                            {{ formatRupiah(item.nilai_buku) }}
                                        </td>
                                        <td
                                            class="whitespace-nowrap text-sm text-center text-black py-2"
                                        >
                                            <a @click="modalLelang(item)"
                                                ><img
                                                    class="m-auto cursor-pointer"
                                                    src="/gadaiemas/storage/icon/shopping.png"
                                                    title="Proses Lelang"
                                            /></a>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                            <div class="w-full text-center">
                                <span
                                    class="text-center text-xl text-white"
                                    v-if="lelangTemp.length < 1"
                                    >Tidak Ada Data</span
                                >
                            </div>
                            <Modal
                                :show="modal.lelang"
                                @close="[(modal.lelang = false)]"
                                maxWidth="2xl"
                                classHeader="bg-sky-700 text-white"
                            >
                                <template #title>
                                    Input Nominal Lelang
                                </template>
                                <template #content>
                                    <div class="mt-6 grid grid-cols-3 gap-4">
                                        <div class="sm:col-span-1 pt-5">
                                            <h1
                                                class="tracking-wide block mb-2 text-xs font-bold text-gray-900 uppercase"
                                            >
                                                Kode Barang :
                                            </h1>
                                        </div>
                                        <div class="sm:col-span-2">
                                            <CustTextInput
                                                :required-indicator="false"
                                                id="kode_barang"
                                                type="text"
                                                name="kode_barang"
                                                v-model="form.kode_barang"
                                                :disabled="true"
                                            />
                                        </div>
                                        <div class="sm:col-span-1 pt-5">
                                            <h1
                                                class="tracking-wide block mb-2 text-xs font-bold text-gray-900 uppercase"
                                            >
                                                Nominal Lelang :
                                            </h1>
                                        </div>
                                        <div class="sm:col-span-2">
                                            <InputFormatIDR
                                                :requiredIndicator="false"
                                                id="nominal_lelang"
                                                type="text"
                                                name="nominal_lelang"
                                                v-model="form.nilai_lelang"
                                            />
                                        </div>
                                        <div class="sm:col-span-1 pt-5">
                                            <h1
                                                class="tracking-wide block mb-2 text-xs font-bold text-gray-900 uppercase"
                                            >
                                                Keterangan :
                                            </h1>
                                        </div>
                                        <div class="sm:col-span-2">
                                            <CustTextInput
                                                :requiredIndicator="false"
                                                id="keterangan"
                                                type="text"
                                                name="keterangan"
                                                v-model="form.keterangan"
                                            />
                                        </div>
                                        <div class="sm:col-span-1 pt-5">
                                            <h1
                                                class="tracking-wide block mb-2 text-xs font-bold text-gray-900 uppercase"
                                            >
                                                Untung Rugi :
                                            </h1>
                                        </div>
                                        <div class="sm:col-span-2">
                                            <InputFormatIDR
                                                :requiredIndicator="false"
                                                id="untung_rugi"
                                                type="text"
                                                name="untung_rugi"
                                                v-model="form.untung_rugi"
                                            />
                                        </div>
                                    </div>
                                    <div
                                        class="flex justify-end items-center mt-10"
                                    >
                                        <div class="ml-4">
                                            <div class="text-black">
                                                <button
                                                    @click="addToLelang()"
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
                                    @click="store()"
                                    class="ml-2 bg-green-700 text-white py-1 px-4 rounded"
                                >
                                    Simpan & Cetak
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </main>
        </template>
    </AdminLayout>
</template>