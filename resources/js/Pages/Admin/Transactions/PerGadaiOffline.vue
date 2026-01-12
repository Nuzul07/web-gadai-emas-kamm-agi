<script setup>
import { ref, onMounted, watch, computed, reactive } from "vue";
import AdminLayout from "@/Layouts/AdminLayout.vue";
import TitlePage from "@/Components/TitlePage.vue";
import SearchInput from "@/Components/SearchInput.vue";
import CustTextInput from "@/Components/CustTextInput.vue";
import CustPercentInput from "@/Components/CustPercentInput.vue";
import CustDetail from "@/Components/CustDetail.vue";
import CustImageDetail from "@/Components/CustImageDetail.vue";
import InputFormatIDR from "@/Components/InputFormatIDR.vue";
import NestedSelect from "@/Components/NestedSelect.vue";
import TextAreaInput from "@/Components/TextAreaInput.vue";
import UploadImage from "@/Components/UploadImage.vue";
import UploadMultiImage from "@/Components/UploadMultiImage.vue";
import SelectKodeBarang from "@/Components/SelectKodeBarang.vue";
import Modal from "@/Components/DialogModal.vue";
import { Head, useForm } from "@inertiajs/vue3";
import axios from "axios";
import Swal from "sweetalert2";
import dayjs from "dayjs";
import { selectedTipeGadai } from "@/src/store";

onMounted(() => {
    checkTipeGadai();
    masterCodeData();
    penaksirData();
    gadaiData();
    golonganData();
    sewaModalData()
    dateInput.value = formatDate(new Date());
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
    asalBarangState: false,
    tujuanTransaksiState: false,
    statusTransaksiState: false,
    instrumenState: false,
    jenisJaminanState: false,
    jenisBarangState: false,
    jenisBarangLainState: false,
    karataseState: false,
});

const showForm = reactive({
    dataNasabah: false,
    barangJaminan: false,
    uangJaminan: false,
});

const formTransaksi = useForm({
    no_sbg: "",
    tgl_transaksi: "",
    no_kons: "",
    asal_barang: "",
    tujuan_transaksi: "",
    fungsi_transaksi: "",
    instrumen_pembayaran: "",
    pengembalian_uang_lebih: "",
    penaksir: "",
    type_transaksi: "",
    sewa_modal: "",
    tgl_jtempo: "",
    by_admin: "",
    golongan: "",
    bunga: "",
    tenor: "",
    total_taksiran: "",
    maks_pinjaman: "",
    pokok_pinjaman: "",
    cara_pencairan: "",
    no_rek_nasabah: "",
    referensi_nosbg: "",
    foto_identitas: "",
    terima_uang: "",
    bukti_lain: [],
    produk_gadai: selectedTipeGadai.value === 'emas' ? 'EMAS' : selectedTipeGadai.value === 'motor' ? 'MOTOR' : 'MOBIL'
});

const formTransaksiDtl = useForm({
    no_sbg: "",
    tgl_transaksi: "",
    no_kons: "",
    asal_barang: "",
    tujuan_transaksi: "",
    fungsi_transaksi: "",
    instrumen_pembayaran: "",
    pengembalian_uang_lebih: "",
    penaksir: "",
    type_transaksi: "",
    sewa_modal: "",
    tgl_jtempo: "",
    by_admin: "",
    golongan: "",
    bunga: "",
    tenor: "",
    total_taksiran: "",
    maks_pinjaman: "",
    pokok_pinjaman: "",
    cara_pencairan: "",
    no_rek_nasabah: "",
    referensi_nosbg: "",
    foto_identitas: "",
    terima_uang: "",
    bukti_lain: [],
    foto_barang: "",
    foto_barang2: "",
    lain_lain: [],
    produk_gadai: ""
});

const formTransaksiSub = useForm({
    no_sbg: "",
    kode_barang: "",
    jenis: "",
    jenis_lain: "",
    kadar: "",
    berat_kotor: "",
    berat_bersih: "",
    taksiran: "",
    maks_pinjaman: "",
    pinjaman: "",
    foto_barang: "",
    foto_barang2: "",
    detail_barang: "",
    lain_lain: [],
});

const formJmnMotor = useForm({
    no_sbg: "",
    kode_barang: "",
    merk: "",
    tipe: "",
    tahun: "",
    harga: "",
    nopol: "",
    no_rangka: "",
    no_mesin: "",
    no_bpkb: "",
    taksiran: "",
    maks_pinjaman: "",
    foto_barang: "",
    foto_barang2: "",
    detail_barang: "",
    lain_lain: [],
});

const formJmnMobil = useForm({
    no_sbg: "",
    kode_barang: "",
    merk: "",
    tipe: "",
    tahun: "",
    harga: "",
    nopol: "",
    no_rangka: "",
    no_mesin: "",
    no_bpkb: "",
    taksiran: "",
    maks_pinjaman: "",
    foto_barang: "",
    foto_barang2: "",
    detail_barang: "",
    lain_lain: [],
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

const gadai = ref([]);
const isGadaiEmpty = computed(() => gadai.value.length === 0);
const gadaiLoading = ref(false);
const gadaiData = async () => {
    gadaiLoading.value = true;
    let url =
    selectedTipeGadai.value === "emas"
            ? "getGadaiEmas"
            : selectedTipeGadai.value === "motor"
            ? "getGadaiMotor"
            : selectedTipeGadai.value === "mobil"
            ? "getGadaiMobil"
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
            return (
                gad.No_sbg.toLowerCase().includes(lowerCase) ||
                gad.nasabah.No_ktp.toLowerCase().includes(lowerCase) ||
                gad.nasabah.Nama.toLowerCase().includes(lowerCase) ||
                gad.Tgl_transaksi.toLowerCase().includes(lowerCase) ||
                gad.Posko.toLowerCase().includes(lowerCase) ||
                gad.Pokok_pinjaman.toLowerCase().includes(lowerCase)
            );
        });
    } else {
        return [];
    }
});

const barangJmn = ref([]);
const detailBarang = ref([]);
const barangLoading = ref(false);
const getBarangJaminan = async () => {
    let url =
        selectedTipeGadai.value === "emas"
            ? "getBarangJaminan"
            : selectedTipeGadai.value === "motor"
            ? "getJaminanMotor"
            : "getJaminanMobil";
    barangLoading.value = true;
    try {
        const res = await axios.get(route(url), {
            params: {
                no_sbg:
                    formTransaksiSub.no_sbg ||
                    formJmnMotor.no_sbg ||
                    formJmnMobil.no_sbg,
            },
        });
        const data = res.data;
        barangJmn.value = data;
        barangLoading.value = false;
        detailBarang.value = barangJmn.value
            .map((item) => item.Detail_barang)
            .join("\n");
    } catch (error) {
        console.log(error);
    }
};

const resetFormBarang = () => {
    formTransaksiSub.jenis = "";
    formTransaksiSub.jenis_lain = "";
    formTransaksiSub.kadar = "";
    formTransaksiSub.berat_kotor = "";
    formTransaksiSub.berat_bersih = "";
    formTransaksiSub.detail_barang = "";
};

const resetFormJaminanMotor = () => {
    formJmnMotor.merk = "";
    formJmnMotor.tipe = "";
    formJmnMotor.nopol = "";
    formJmnMotor.no_rangka = "";
    formJmnMotor.no_mesin = "";
    formJmnMotor.no_bpkb = "";
    formJmnMotor.detail_barang = "";
};

const resetFormJaminanMobil = () => {
    formJmnMobil.merk = "";
    formJmnMobil.tipe = "";
    formJmnMobil.nopol = "";
    formJmnMobil.no_rangka = "";
    formJmnMobil.no_mesin = "";
    formJmnMobil.no_bpkb = "";
    formJmnMobil.detail_barang = "";
};

const storeBarangJaminan = async () => {
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

            await axios.post(route("storeBarang"), formTransaksiSub, header);

            // Close the loading alert and show success message
            Swal.fire({
                title: "Berhasil",
                text: "Barang Jaminan ditambahkan",
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
            getBarangJaminan();
            resetFormBarang();
            isBengkok.value = null;
            isPatah.value = null;
            isMataCincin.value = null;
            jumlahJaminan.value += 1;
            modal.barangjaminan = false;
        } catch (error) {
            console.log("error", error);

            // Close the loading alert and show error message
            if (error.response && error.response.status === 422) {
                formTransaksiSub.errors = error.response.data.errors || {};
            }

            if (
                error.response.data.message ==
                'Attempt to read property "PERSEN_PATOK_TAKSIRAN" on null'
            ) {
                Swal.fire({
                    title: "Terjadi kesalahan",
                    html: "Nilai taksiran barang emas ini kurang dari 500,000.<br>Informasikan terlebih dahulu ke kaops atau pimpinan yang terkait",
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
                return;
            } else {
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
    }
};

const storeJaminanMotor = async () => {
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

            await axios.post(route("storeMotor"), formJmnMotor, header);

            // Close the loading alert and show success message
            Swal.fire({
                title: "Berhasil",
                text: "Jaminan Motor ditambahkan",
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
            getBarangJaminan();
            resetFormJaminanMotor();
            jumlahJaminan.value += 1;
            modal.barangjaminan = false;
        } catch (error) {
            console.log("error", error);

            // Close the loading alert and show error message
            if (error.response && error.response.status === 422) {
                formTransaksiSub.errors = error.response.data.errors || {};
            }

            if (
                error.response.data.message ==
                'Attempt to read property "PERSEN_PATOK_TAKSIRAN" on null'
            ) {
                Swal.fire({
                    title: "Terjadi kesalahan",
                    html: "Nilai taksiran barang emas ini kurang dari 500,000.<br>Informasikan terlebih dahulu ke kaops atau pimpinan yang terkait",
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
                return;
            } else {
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
    }
};

const storeJaminanMobil = async () => {
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

            await axios.post(route("storeMobil"), formJmnMobil, header);

            // Close the loading alert and show success message
            Swal.fire({
                title: "Berhasil",
                text: "Jaminan Mobil ditambahkan",
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
            getBarangJaminan();
            resetFormJaminanMobil();
            jumlahJaminan.value += 1;
            modal.barangjaminan = false;
        } catch (error) {
            console.log("error", error);

            // Close the loading alert and show error message
            if (error.response && error.response.status === 422) {
                formTransaksiSub.errors = error.response.data.errors || {};
            }

            if (
                error.response.data.message ==
                'Attempt to read property "PERSEN_PATOK_TAKSIRAN" on null'
            ) {
                Swal.fire({
                    title: "Terjadi kesalahan",
                    html: "Nilai taksiran barang emas ini kurang dari 500,000.<br>Informasikan terlebih dahulu ke kaops atau pimpinan yang terkait",
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
                return;
            } else {
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
    }
};

const masterCode = ref([]);
const masterCodeData = async () => {
    await axios.get(route("getMasterCode")).then((res) => {
        const data = res.data;
        masterCode.value = data;
        updateAsalBarangOptions();
        updateStatusTransaksiOptions();
        updateTujuanTransaksiOptions();
        updateInstrumenPembayaranOptions();
        updateReferensiSbgOptions();
        updateJenisJaminanOptions();
        updateJenisBarangOptions();
        updateKarataseOptions();
        updateTenorOptions();
    });
};

const penaksir = ref([]);
const penaksirData = async () => {
    await axios.get(route("getPenaksirByPosko")).then((res) => {
        const data = res.data;
        penaksir.value = data;
        updatePenaksirOptions();
    });
};

const golongan = ref([]);
const golonganData = async () => {
    await axios.get(route("getGolongan")).then((res) => {
        const data = res.data;
        golongan.value = data;
    });
};

const sewaModal = ref([])
const sewaModalData = async () => {
    await axios.get(route("getSewaModal")).then((res) => {
        const data = res.data
        sewaModal.value = data
    })
}

const cabang = ref([]);
const CabangData = async () => {
    await axios.get(route("getCabang")).then((res) => {
        const data = res.data;
        cabang.value = data;
        updatePoskoOptions();
    });
};

//master code select options
const asalBarangOptions = ref([]);
const statusTransaksiOptions = ref([]);
const tujuanTransaksiOptions = ref([]);
const instrumenPembayaranOptions = ref([]);
const penaksirOptions = ref([]);
const referensiSbgOptions = ref([]);
const jenisJaminanOptions = ref([]);
const jenisBarangOptions = ref([]);
const karataseOptions = ref([]);
const TenorOptions = ref([]);

const updateAsalBarangOptions = () => {
    const AsalBarangSet = new Set(
        masterCode.value
            .filter((item) => item.Master_code === "Asalbarang")
            .map((item) => item.Keterangan) // assuming you need 'value' from the item
    );

    // Convert Set to array and update asalBarangOptions
    asalBarangOptions.value = [...AsalBarangSet];
};

const updateStatusTransaksiOptions = () => {
    const StatusTransaksiSet = new Set(
        masterCode.value
            .filter((item) => item.Master_code === "Statustransaksi")
            .map((item) => item.Keterangan) // assuming you need 'value' from the item
    );

    // Convert Set to array and update asalBarangOptions
    statusTransaksiOptions.value = [...StatusTransaksiSet];
};

const updateTujuanTransaksiOptions = () => {
    const TujuanTransaksiSet = new Set(
        masterCode.value
            .filter((item) => item.Master_code === "Tujuantransaksi")
            .map((item) => item.Keterangan) // assuming you need 'value' from the item
    );

    // Convert Set to array and update asalBarangOptions
    tujuanTransaksiOptions.value = [...TujuanTransaksiSet];
};

const updateInstrumenPembayaranOptions = () => {
    const InstrumenPembayaranSet = new Set(
        masterCode.value
            .filter((item) => item.Master_code === "Jenis_pembayaran")
            .map((item) => item.Keterangan) // assuming you need 'value' from the item
    );

    // Convert Set to array and update asalBarangOptions
    instrumenPembayaranOptions.value = [...InstrumenPembayaranSet];
};

const updatePenaksirOptions = () => {
    const PenaksirSet = new Set(
        penaksir.value.map((item) => item.name) // assuming you need 'value' from the item
    );

    // Convert Set to array and update asalBarangOptions
    penaksirOptions.value = [...PenaksirSet];
};

const updateReferensiSbgOptions = () => {
    const ReferensiSbgSet = new Set(
        masterCode.value
            .filter((item) => item.Master_code === "Type_transaksi")
            .map((item) => item.Keterangan) // assuming you need 'value' from the item
    );

    // Convert Set to array and update asalBarangOptions
    referensiSbgOptions.value = [...ReferensiSbgSet];
};

const updateJenisJaminanOptions = () => {
    const JenisJaminanSet = new Set(
        masterCode.value
            .filter((item) => item.Master_code === "Jenisjaminan")
            .map((item) => item.Keterangan) // assuming you need 'value' from the item
    );

    // Convert Set to array and update asalBarangOptions
    jenisJaminanOptions.value = [...JenisJaminanSet];
};

const updateJenisBarangOptions = () => {
    const JenisBarangSet = new Set(
        masterCode.value
            .filter((item) => item.Master_code === "JenisBarang")
            .map((item) => item.Keterangan) // assuming you need 'value' from the item
    );

    // Convert Set to array and update asalBarangOptions
    jenisBarangOptions.value = [...JenisBarangSet];
};

const updateKarataseOptions = () => {
    const KarataseSet = new Set(
        masterCode.value
            .filter((item) => item.Master_code === "Karatase")
            .map((item) => item.Code1) // assuming you need 'value' from the item
    );

    // Convert Set to array and update asalBarangOptions
    karataseOptions.value = [...KarataseSet];
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

//posko select options
const poskoOptions = ref([]);

const updatePoskoOptions = () => {
    const poskoSet = new Set(cabang.value.map((item) => item.Kode_Cabang));
    poskoOptions.value = [...poskoSet];
};

const handleFotoIdentitas = (selectedFile) => {
    formTransaksi.foto_identitas = selectedFile;
};

const handleFotoTerimaUang = (selectedFile) => {
    formTransaksi.terima_uang = selectedFile;
};

const handleFotoBuktiLainnya = (files) => {
    formTransaksi.bukti_lain = files;
};

const handleFotoJaminan = (selectedFile) => {
    formTransaksiSub.foto_barang = selectedFile;
};

const handleFotoJaminan2 = (selectedFile) => {
    formTransaksiSub.foto_barang2 = selectedFile;
};

const handleFotoJaminanMotor = (selectedFile) => {
    formJmnMotor.foto_barang = selectedFile;
};

const handleFotoJaminanMotor2 = (selectedFile) => {
    formJmnMotor.foto_barang2 = selectedFile;
};

const handleFotoJaminanMobil = (selectedFile) => {
    formJmnMobil.foto_barang = selectedFile;
};

const handleFotoJaminanMobil2 = (selectedFile) => {
    formJmnMobil.foto_barang2 = selectedFile;
};

// Handle the uploaded files from the FileUploader component
const handleFilesUploaded = (files) => {
    formTransaksiSub.lain_lain = files;
    console.log("Uploaded files:", files);
};

const uploadMultiImage = ref(null);

const clearImages = () => {
    if (uploadMultiImage.value) {
        uploadMultiImage.value.clearFiles();
    }
};

// Function to check if there’s any error for a given index in lain_lain
const hasErrorForIndex = (index) => {
    return Object.keys(formTransaksiSub.errors).some(
        (key) => key === `lain_lain.${index}`
    );
};

// Function to retrieve the first error message for a given index in lain_lain
const getFirstErrorForIndex = (index) => {
    const errorKey = `lain_lain.${index}`;
    return formTransaksiSub.errors[errorKey]
        ? formTransaksiSub.errors[errorKey][0]
        : null;
};

//modal
const modal = reactive({
    kelengkapan: false,
    barangjaminan: false,
    hsp: false,
    detailGadai: false,
    galeri: false,
    fotoTambahan: false,
    buktiLainnya: false,
});

const modalKelengkapan = () => {
    modal.kelengkapan = true;
};

const modalBarangJaminan = () => {
    modal.barangjaminan = true;
};

const modalHsp = () => {
    modal.hsp = true;
    hspData();
};

const hsp = ref([]);
const isHspEmpty = computed(() => hsp.value.length === 0);
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

const crntPageHsp = ref(1);
const perPageHsp = 10;

const totalPagesHsp = computed(() =>
    Math.ceil(searchHsp.value.length / perPageHsp)
);

const pgntHsp = computed(() => {
    const start = (crntPageHsp.value - 1) * perPageHsp;
    const end = Math.min(start + perPageHsp, searchHsp.value.length);
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
    crntPageHsp.value = 1;
});

// Functions to handle next and previous
const nextPageHsp = () => {
    if (crntPageHsp.value < totalPagesHsp.value) crntPageHsp.value++;
};

const prevPageHsp = () => {
    if (crntPageHsp.value > 1) crntPageHsp.value--;
};

const selectDataHsp = (d) => {
    Swal.fire({
        title: "Berhasil",
        text: "Memlilih Hsp",
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
    const words = d.nama_barang.split(" ");
    if (selectedTipeGadai.value === 'motor') {
        formJmnMotor.merk = words[0];
        formJmnMotor.tipe = words.slice(1).join(" ");
        formJmnMotor.tahun = d.tahun;
        formJmnMotor.harga = d.nilai;
        (formJmnMotor.detail_barang =
            "1 Unit Spd Motor, KS " +
            d.kode_stnk +
            ", KK " +
            d.kode_kendaraan +
            ", Tahun " +
            d.tahun +
            ", Kondisi: ");
    } else if (selectedTipeGadai.value === 'mobil') {
        formJmnMobil.merk = words[0];
        formJmnMobil.tipe = words.slice(1).join(" ");
        formJmnMobil.tahun = d.tahun;
        formJmnMobil.harga = d.nilai;
        (formJmnMobil.detail_barang =
            "1 Unit Mobil, KS " +
            d.kode_stnk +
            ", KK " +
            d.kode_kendaraan +
            ", Tahun " +
            d.tahun +
            ", Kondisi: ");
    }
    modal.hsp = false;
};

const modalDetailGadai = (d) => {
    selectDataGadai(d);
    barangJmn.value = [];
    modal.detailGadai = true;
};

const modalGaleri = () => {
    modal.detailGadai = false;
    modal.galeri = true;
};

const backBtnModalGaleri = () => {
    modal.galeri = false;
    modal.detailGadai = true;
};

const modalFotoTambahan = () => {
    modal.galeri = false;
    modal.fotoTambahan = true;
};

const backBtnFotoTambahan = () => {
    modal.fotoTambahan = false;
    modal.galeri = true;
};

const modalBuktiLainnya = () => {
    modal.galeri = false;
    modal.buktiLainnya = true;
};

const backBtnBuktiLainnya = () => {
    modal.buktiLainnya = false;
    modal.galeri = true;
};

const openAccordion = ref(null);
const toggleAccordion = (index) => {
    openAccordion.value = openAccordion.value === index ? null : index;
};

const cetakUlangSBG = () => {
    generatePDF();
};
//

//getNasabahByNoKtp
const loading = ref(false);

const getNasabahId = async () => {
    if (nasabahIdentitas.value === "") {
        Swal.fire({
            title: "Terjadi Kesalahan",
            text: "Masukkan No Identitas",
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
        });
    } else {
        try {
            loading.value = true;

            // Show the "Mohon Tunggu" message immediately
            Swal.fire({
                title: "Memuat",
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

            const res = await axios.get(route("getNasabahId"), {
                params: {
                    nasabahIdentitas: nasabahIdentitas.value,
                },
            });

            if (res.data[0]) {
                // Close the loading alert and show success message
                Swal.fire({
                    title: "Berhasil",
                    text: "Nasabah ditemukan",
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

                const data = res.data;
                ktpNasabah.value = data[0].No_ktp;
                namaNasabah.value = data[0].Nama;
                kodeKonsumen.value = data[0].K_Kons;
                formTransaksi.no_kons = kodeKonsumen.value;
                formTransaksi.no_rek_nasabah = data[0].no_rekening;
            } else {
                // Close the loading alert and show error message
                Swal.fire({
                    title: "Nasabah Tidak Ditemukan",
                    text: "Periksa Kembali No Identitas",
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

                ktpNasabah.value = "";
                namaNasabah.value = "";
                kodeKonsumen.value = "";
                formTransaksi.no_kons = "";
                formTransaksi.no_rek_nasabah = "";
            }
        } catch (error) {
            // Close the loading alert and show error message
            Swal.fire({
                title: "Error",
                text: "Terjadi kesalahan dalam mengambil data",
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

            console.log(error);
        } finally {
            loading.value = false;
        }
    }
};

//

const generatePDF = async () => {
    try {
        // Show "please wait" SweetAlert2 message
        Swal.fire({
            title: "Mohon Tunggu...",
            text: "Membuat SBG",
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

        const response = await axios.get(route("generatepdf"), {
            responseType: "blob",
            params: {
                no_sbg: formTransaksi.no_sbg || formTransaksiDtl.no_sbg,
                produk_gadai: formTransaksi.produk_gadai || formTransaksiDtl.produk_gadai
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
            // Close the "please wait" SweetAlert2
            Swal.close();

            // Show "Ready to print" SweetAlert2 message
            Swal.fire({
                title: "Selesai!",
                text: "SBG anda siap untuk dicetak.",
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

            // Automatically trigger the print dialog
            iframe.contentWindow.print();
        };
    } catch (error) {
        // Close the "please wait" SweetAlert2 in case of an error
        Swal.close();

        // Show an error SweetAlert2 message
        Swal.fire({
            title: "Error!",
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

let validFormNasabah = ref(true);

watch(
    [
        () => formTransaksi.no_kons,
        () => formTransaksi.asal_barang,
        () => formTransaksi.tujuan_transaksi,
        () => formTransaksi.instrumen_pembayaran,
        () => formTransaksi.fungsi_transaksi,
        () => formTransaksi.pengembalian_uang_lebih,
        () => formTransaksi.foto_identitas,
    ],
    ([
        no_kons,
        asal_barang,
        tujuan_transaksi,
        instrumen_pembayaran,
        fungsi_transaksi,
        pengembalian_uang_lebih,
        foto_identitas,
    ]) => {
        validFormNasabah.value =
            no_kons == "" ||
            asal_barang == "" ||
            tujuan_transaksi == "" ||
            instrumen_pembayaran == "" ||
            fungsi_transaksi == "" ||
            pengembalian_uang_lebih == "" ||
            foto_identitas == ""
                ? true
                : false;
    }
);

let validFormBarangJaminan = ref(true);

watch(
    [() => formTransaksi.penaksir, () => formTransaksi.type_transaksi],
    ([penaksir, type_transaksi]) => {
        validFormBarangJaminan.value =
            penaksir == "" || type_transaksi == "" ? true : false;
    }
);

let validFormUangPinjaman = ref(true);

watch(
    [
        () => formTransaksi.tgl_transaksi,
        () => formTransaksi.tgl_jtempo,
        () => formTransaksi.total_taksiran,
        () => formTransaksi.maks_pinjaman,
        () => formTransaksi.by_admin,
        () => formTransaksi.sewa_modal,
        () => formTransaksi.no_rek_nasabah,
        () => formTransaksi.cara_pencairan,
    ],
    ([
        tgl_transaksi,
        tgl_jtempo,
        total_taksiran,
        maks_pinjaman,
        by_admin,
        sewa_modal,
        norek,
        cara_pencairan,
    ]) => {
        console.log("tgl_transaksi:", tgl_transaksi);
        console.log("tgl_jtempo:", tgl_jtempo);
        console.log("total_taksiran:", total_taksiran);
        console.log("maks_pinjaman:", maks_pinjaman);
        console.log("by_admin:", by_admin);
        console.log("sewa_modal:", sewa_modal);
        console.log("no_rek_nasabah:", norek);
        console.log("cara_pencairan:", cara_pencairan);
        
        validFormUangPinjaman.value =
            tgl_transaksi == "" ||
            tgl_jtempo == "" ||
            total_taksiran == "" ||
            maks_pinjaman == "" ||
            by_admin == "" ||
            sewa_modal == "" ||
            norek == "" ||
            cara_pencairan == ""
                ? true
                : false;
    }
);

const no_sbg = ref("");

const GetNoSbg = async () => {
    const res = await axios.get(route("getNoSbg"));
    no_sbg.value = res.data;
    return no_sbg.value;
};

const setNosbg = async () => {
    const nosbg = await GetNoSbg();
    formTransaksi.no_sbg = nosbg;
    formTransaksiSub.no_sbg = nosbg;
    formJmnMotor.no_sbg = nosbg;
    formJmnMobil.no_sbg = nosbg;
};

const setTglTransaksi = () => {
    formTransaksi.tgl_transaksi = formatDateDB(new Date());
}

let formattedTaksiran = ref("");
let formattedMaksPinjaman = ref("");
const calculateTotalPinjamanEmas = () => {
    let taksiranSum = 0;
    let maksPinjamanSum = 0;

    for (let barangJ of barangJmn.value) {
        taksiranSum += parseFloat(barangJ.Taksiran);
        maksPinjamanSum += parseFloat(barangJ.Maks_pinjaman);
    }

    // Round taksiranSum up to the nearest 50,000
    if (maksPinjamanSum > 50000) {
        maksPinjamanSum = Math.ceil(maksPinjamanSum / 50000) * 50000;
    } else {
        maksPinjamanSum = 50000;
    }

    const golonganChoice = golongan.value.find(
        (g) =>
            maksPinjamanSum >= parseFloat(g.MIN_PINJAMAN) &&
            maksPinjamanSum <= parseFloat(g.MAX_PINJAMAN)
    );

    if (golonganChoice) {
        formTransaksi.golongan = golonganChoice.GOLONGAN;
        formTransaksi.sewa_modal = formatDecimal(
            golonganChoice.SEWA_MODAL_15HARI * 100
        );
    } else {
        formTransaksi.golongan = null;
    }

    formTransaksi.total_taksiran = taksiranSum;
    formTransaksi.maks_pinjaman = maksPinjamanSum;

    formattedTaksiran.value = formatRupiah(formTransaksi.total_taksiran);
    formattedMaksPinjaman.value = formatRupiah(formTransaksi.maks_pinjaman);
};

const calculateTotalPinjamanMotorMobil = () => {
    let taksiranSum = 0;
    let maksPinjamanSum = 0;

    for (let barangJ of barangJmn.value) {
        taksiranSum += parseFloat(barangJ.Taksiran);
        maksPinjamanSum += parseFloat(barangJ.Maks_pinjaman);
    }

    // Round taksiranSum up to the nearest 50,000
    if (maksPinjamanSum > 50000) {
        maksPinjamanSum = Math.ceil(maksPinjamanSum / 50000) * 50000;
    } else {
        maksPinjamanSum = 50000;
    }

    formTransaksi.total_taksiran = taksiranSum;
    formTransaksi.maks_pinjaman = maksPinjamanSum;

    formattedTaksiran.value = formatRupiah(formTransaksi.total_taksiran);
    formattedMaksPinjaman.value = formatRupiah(formTransaksi.maks_pinjaman);
}

const formattedByAdmin = ref("");
watch(
    [() => formTransaksi.pokok_pinjaman, () => formTransaksi.tenor],
    ([newValue, newTenor]) => {
        console.log(newValue)
        console.log(newTenor)
        if (newValue !== "") {
            if (selectedTipeGadai.value === 'emas') {
                const adminGolongan = golongan.value.find(
                    (g) => g.GOLONGAN === formTransaksi.golongan
                );
                let ByAdmin = newValue * adminGolongan.PERSEN_BYADMIN;
                ByAdmin = Math.round(ByAdmin / 1000) * 1000;
                formTransaksi.by_admin = ByAdmin;
                formattedByAdmin.value = formatRupiah(ByAdmin);

                if (formTransaksi.tenor == 15) {
                    formTransaksi.bunga = parseFloat(
                        formTransaksi.pokok_pinjaman *
                            adminGolongan.SEWA_MODAL_15HARI
                    );
                } else if (formTransaksi.tenor == 30) {
                    formTransaksi.bunga = parseFloat(
                        formTransaksi.pokok_pinjaman *
                            adminGolongan.SEWA_MODAL16_30HARI
                    );
                } else if (formTransaksi.tenor == 45) {
                    formTransaksi.bunga = parseFloat(
                        formTransaksi.pokok_pinjaman *
                            adminGolongan.SEWA_MODAL31_45HARI
                    );
                } else if (formTransaksi.tenor == 60) {
                    formTransaksi.bunga = parseFloat(
                        formTransaksi.pokok_pinjaman *
                            adminGolongan.SEWA_MODAL46_60HARI
                    );
                } else if (formTransaksi.tenor == 75) {
                    formTransaksi.bunga = parseFloat(
                        formTransaksi.pokok_pinjaman *
                            adminGolongan.SEWA_MODAL61_75HARI
                    );
                } else if (formTransaksi.tenor == 90) {
                    formTransaksi.bunga = parseFloat(
                        formTransaksi.pokok_pinjaman *
                            adminGolongan.SEWA_MODAL76_90HARI
                    );
                } else if (formTransaksi.tenor == 105) {
                    formTransaksi.bunga = parseFloat(
                        formTransaksi.pokok_pinjaman *
                            adminGolongan.SEWA_MODAL91_105HARI
                    );
                } else if (formTransaksi.tenor == 120) {
                    formTransaksi.bunga = parseFloat(
                        formTransaksi.pokok_pinjaman *
                            adminGolongan.SEWA_MODAL_105_ATAS_HARI
                    );
                }
            } else if (selectedTipeGadai.value === 'motor') {
                const sewaModalMotor = sewaModal.value.find(
                    (sm) => sm.type_kendaraan === 'motor'
                );
                if (newTenor == 15 && formTransaksi.pokok_pinjaman !== "") {
                    let ByAdminMtr = newValue * sewaModalMotor.admin_15hari;
                    ByAdminMtr = Math.round(ByAdminMtr / 1000) * 1000;
                    formTransaksi.by_admin = ByAdminMtr;
                    formattedByAdmin.value = formatRupiah (ByAdminMtr);
                    formTransaksi.bunga = parseFloat(
                        formTransaksi.pokok_pinjaman * sewaModalMotor.bunga_15hari
                    );
                } else if (newTenor == 30 && formTransaksi.pokok_pinjaman !== "") {
                    let ByAdminMtr = newValue * sewaModalMotor.admin_30hari;
                    ByAdminMtr = Math.round(ByAdminMtr / 1000) * 1000;
                    formTransaksi.by_admin = ByAdminMtr;
                    formattedByAdmin.value = formatRupiah (ByAdminMtr);
                    formTransaksi.bunga = parseFloat(
                        formTransaksi.pokok_pinjaman * sewaModalMotor.bunga_30hari
                    );
                } else if (newTenor == 60 && formTransaksi.pokok_pinjaman !== "") {
                    let ByAdminMtr = newValue * sewaModalMotor.admin_60hari;
                    ByAdminMtr = Math.round(ByAdminMtr / 1000) * 1000;
                    formTransaksi.by_admin = ByAdminMtr;
                    formattedByAdmin.value = formatRupiah (ByAdminMtr);
                    formTransaksi.bunga = parseFloat(
                        formTransaksi.pokok_pinjaman * sewaModalMotor.bunga_60hari
                    );
                } else if (newTenor == 90 && formTransaksi.pokok_pinjaman !== "") {
                    let ByAdminMtr = newValue * sewaModalMotor.admin_90hari;
                    ByAdminMtr = Math.round(ByAdminMtr / 1000) * 1000;
                    formTransaksi.by_admin = ByAdminMtr;
                    formattedByAdmin.value = formatRupiah (ByAdminMtr);
                    formTransaksi.bunga = parseFloat(
                        formTransaksi.pokok_pinjaman * sewaModalMotor.bunga_90hari
                    );
                }
            } else if (selectedTipeGadai.value === 'mobil') {
                const sewaModalMobil = sewaModal.value.find(
                    (sm) => sm.type_kendaraan === 'mobil'
                );
                if (newTenor == 15 && formTransaksi.pokok_pinjaman !== "") {
                    let ByAdminMbl = newValue * sewaModalMobil.admin_15hari;
                    ByAdminMbl = Math.round(ByAdminMbl / 1000) * 1000;
                    formTransaksi.by_admin = ByAdminMbl;
                    formattedByAdmin.value = formatRupiah (ByAdminMbl);
                    formTransaksi.bunga = parseFloat(
                        formTransaksi.pokok_pinjaman * sewaModalMobil.bunga_15hari
                    );
                } else if (newTenor == 30 && formTransaksi.pokok_pinjaman !== "") {
                    let ByAdminMbl = newValue * sewaModalMobil.admin_30hari;
                    ByAdminMbl = Math.round(ByAdminMbl / 1000) * 1000;
                    formTransaksi.by_admin = ByAdminMbl;
                    formattedByAdmin.value = formatRupiah (ByAdminMbl);
                    formTransaksi.bunga = parseFloat(
                        formTransaksi.pokok_pinjaman * sewaModalMobil.bunga_30hari
                    );
                } else if (newTenor == 60 && formTransaksi.pokok_pinjaman !== "") {
                    let ByAdminMbl = newValue * sewaModalMobil.admin_60hari;
                    ByAdminMbl = Math.round(ByAdminMbl / 1000) * 1000;
                    formTransaksi.by_admin = ByAdminMbl;
                    formattedByAdmin.value = formatRupiah (ByAdminMbl);
                    formTransaksi.bunga = parseFloat(
                        formTransaksi.pokok_pinjaman * sewaModalMobil.bunga_60hari
                    );
                } else if (newTenor == 90 && formTransaksi.pokok_pinjaman !== "") {
                    let ByAdminMbl = newValue * sewaModalMobil.admin_90hari;
                    ByAdminMbl = Math.round(ByAdminMbl / 1000) * 1000;
                    formTransaksi.by_admin = ByAdminMbl;
                    formattedByAdmin.value = formatRupiah (ByAdminMbl);
                    formTransaksi.bunga = parseFloat(
                        formTransaksi.pokok_pinjaman * sewaModalMobil.bunga_90hari
                    );
                }
            }
        }
    }
);

const formattedPinjaman = ref("");
const maxPinjam = ref(false);
const maskimalLoan = () => {
    formTransaksi.pokok_pinjaman = formTransaksi.maks_pinjaman;
    formattedPinjaman.value = formattedMaksPinjaman.value;
    maxPinjam.value = true;
};

watch(
    () => formattedPinjaman.value, // Watch the value directly
    (newValue) => {
        if (newValue !== "") {
            formTransaksi.pokok_pinjaman = parseInt(newValue, 10);
        }
    }
);

const sewaModalDisabled = ref(true);
const toggleDisabled = () => {
    sewaModalDisabled.value = !sewaModalDisabled.value;
};

const sewaModalState = ref(false);
const checkSewaModal = () => {
    if (formTransaksi.sewa_modal < 1) {
        Swal.fire({
            icon: "warning",
            title: "Invalid Nominal Sewa Modal",
            text: "Sewa Modal tidak boleh kurang dari 1%.",
        });
        sewaModalState.value = true;
    } else {
        sewaModalState.value = false;
    }
};

const maksPinjamanInt = ref(0);
const pinjamanInt = ref(0);
const pinjamanState = ref(false);
const checkLoanInput = () => {
    maksPinjamanInt.value = convertToInt(formattedMaksPinjaman.value);
    pinjamanInt.value = parseInt(formattedPinjaman.value, 10);
    if (pinjamanInt.value > maksPinjamanInt.value) {
        Swal.fire({
            icon: "warning",
            title: "Invalid Nominal Pinjaman",
            text: "Nominal tidak boleh lebih dari maksimal limit.",
        });
        pinjamanState.value = true;
    } else {
        pinjamanState.value = false;
    }
};

const convertToInt = (currency) => {
    if (!currency) {
        return 0;
    }
    let currencyStr = String(currency);
    // Remove the currency symbol and dots
    let cleanedString = currencyStr.replace(/[Rp\s.]/g, "");
    // Convert the cleaned string to integer
    return parseInt(cleanedString, 10);
};

//dataNasabahInput
const nasabahIdentitas = ref("");
const ktpNasabah = ref("");
const namaNasabah = ref("");
const kodeKonsumen = ref("");
//

//reset form
const uploadImageRef = ref(null);
const clearPreview = () => {
    if (uploadImageRef.value) {
        uploadImageRef.value.resetPreview();
    }
};
//

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

const maksTenor = ref(0);
// Watcher to update jatuh tempo when maksTenor changes
watch(
    () => maksTenor.value, // Watch the value directly
    (newValue) => {
        formTransaksi.tenor = newValue;
        if (newValue !== 0) {
            const resultDate = dayjs(dateInput.value, "DD/MM/YYYY")
                .add(newValue, "day")
                .subtract(1, "day") // Subtract one day to get the correct result
                .startOf("day"); // Set to start of day after addition
            formTransaksi.tgl_jtempo = formatDate(resultDate);

            if (selectedTipeGadai.value === 'motor' || selectedTipeGadai.value === 'mobil') {
                if (newValue == 30) formTransaksi.sewa_modal = 5
                if (newValue == 60) formTransaksi.sewa_modal = 10
                if (newValue == 90) formTransaksi.sewa_modal = 15
            }
        }
    }
);

//reset after store
const resetAll = () => {
    clearPreview();
    formTransaksi.reset();
    formTransaksiSub.reset();
    formJmnMotor.reset();
    formJmnMobil.reset();
    nasabahIdentitas.value = "";
    ktpNasabah.value = "";
    namaNasabah.value = "";
    kodeKonsumen.value = "";
    jumlahJaminan.value = 0;
    searchIHsp.value = "";
    maksTenor.value = "";
    formattedTaksiran.value = "";
    formattedMaksPinjaman.value = "";
    formattedPinjaman.value = "";
    formattedByAdmin.value = "";
    detailBarang.value = [];
    isBengkok.value = null;
    isPatah.value = null;
    isMataCincin.value = null;
};
//end

//store gadai to db
const store = async () => {
    const header = {
        headers: {
            "Content-Type": "multipart/form-data",
        },
    };

    // Confirmation prompt before proceeding
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
            await axios.post(route("storeGadai"), formTransaksi, header);

            // Close the loading alert and show success message
            Swal.fire({
                title: "Berhasil",
                text: "Form Gadai ditambahkan",
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

            // Generate PDF
            generatePDF();

            // Reset and update data after a delay
            setTimeout(() => {
                resetAll();
                gadaiData();
                getBarangJaminan();
                // Delay to allow the success message to be visible
            }, 1500);
            showForm.dataNasabah = false;
            showForm.barangJaminan = false;
            showForm.uangJaminan = false;
        } catch (error) {
            console.log("error", error);

            // Handle validation errors
            if (error.response && error.response.status === 422) {
                formTransaksi.errors = error.response.data.errors || {};
            }

            // Show error message
            Swal.fire({
                title: "Terjadi kesalahan",
                html: "Periksa kembali semua form, pastikan semua terisi!",
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
//

//upload foto kons terima uang
const uploadTerimaUang = async () => {
    const header = {
        headers: {
            "Content-Type": "multipart/form-data",
        },
    };

    // Confirmation prompt before proceeding
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
            await axios.post(
                route("uploadTerimaUang"),
                {
                    no_sbg: formTransaksiDtl.no_sbg,
                    terima_uang: formTransaksi.terima_uang,
                },
                header
            );

            // Close the loading alert and show success message
            Swal.fire({
                title: "Berhasil",
                text: "Foto Kons Terima Uang Terupload",
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
            await gadaiData();
            const filteredData = gadai.value.filter(
                (g) => g.No_sbg === formTransaksiDtl.no_sbg
            );
            console.log(filteredData);
            formTransaksiDtl.terima_uang =
                filteredData.length > 0 ? filteredData[0].Terima_uang : null;
            formTransaksi.terima_uang = "";
        } catch (error) {
            console.log("error", error);

            // Handle validation errors
            if (error.response && error.response.status === 422) {
                formTransaksi.errors = error.response.data.errors || {};
            }

            // Show error message
            Swal.fire({
                title: "Terjadi kesalahan",
                html: "Periksa kembali inputannya",
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
//

// Watch selectedKodeBarang and log changes
watch(
    () => formTransaksiSub.kode_barang,
    (newValue) => {
        console.log("Selected Kode Barang:", newValue);
    }
);
//upload foto kons terima uang
const uploadFotoTambahan = async () => {
    if (!formTransaksiSub.kode_barang) {
        Swal.fire(
            "Tidak ada barang yang dipilih",
            "Mohon pilih barang terlebih dahulu",
            "warning"
        );
        return;
    } else if (formTransaksiSub.lain_lain.length === 0) {
        Swal.fire(
            "Tidak ada file yang dipilih",
            "Mohon pilih terlebih dahulu",
            "warning"
        );
        return;
    }

    const header = {
        headers: {
            "Content-Type": "multipart/form-data",
        },
    };

    // Confirmation prompt before proceeding
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

            const formData = new FormData();
            formData.append("no_sbg", formTransaksiSub.no_sbg);
            formData.append("kode_barang", formTransaksiSub.kode_barang);
            formData.append("produk_gadai", formTransaksiDtl.produk_gadai)
            formTransaksiSub.lain_lain.forEach((file, index) => {
                formData.append(`lain_lain[${index}]`, file.file);
            });

            // Log FormData entries for debugging
            for (let [key, value] of formData.entries()) {
                console.log(`${key}:`, value);
            }

            // Perform the API request
            await axios.post(route("uploadTambahan"), formData, header);

            // Close the loading alert and show success message
            Swal.fire({
                title: "Berhasil",
                text: "Foto Tambahan Terupload",
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
            await getBarangJaminan();
            selectFotoGadai();
            clearImages();
            formTransaksiSub.lain_lain = [];
        } catch (error) {
            console.log("error", error);

            // Handle validation errors
            if (error.response && error.response.status === 422) {
                formTransaksiSub.errors = error.response.data.errors || {};
            }

            // Show error message
            Swal.fire({
                title: "Terjadi kesalahan",
                html: "Periksa kembali inputannya",
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
//

//upload foto bukti lainnya
const uploadBuktiLain = async () => {
    if (formTransaksi.bukti_lain.length === 0) {
        Swal.fire(
            "Tidak ada file yang dipilih",
            "Mohon pilih terlebih dahulu",
            "warning"
        );
        return;
    }

    const header = {
        headers: {
            "Content-Type": "multipart/form-data",
        },
    };

    // Confirmation prompt before proceeding
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

            const formData = new FormData();
            formData.append("no_sbg", formTransaksiDtl.no_sbg);
            formTransaksi.bukti_lain.forEach((file, index) => {
                formData.append(`bukti_lain[${index}]`, file.file);
            });

            // Log FormData entries for debugging
            for (let [key, value] of formData.entries()) {
                console.log(`${key}:`, value);
            }

            // Perform the API request
            await axios.post(route("uploadBuktiLain"), formData, header);

            // Close the loading alert and show success message
            Swal.fire({
                title: "Berhasil",
                text: "Foto Bukti Lainnya Terupload",
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
            await gadaiData();
            const filteredData = gadai.value.filter(
                (g) => g.No_sbg === formTransaksiDtl.no_sbg
            );
            formTransaksiDtl.bukti_lain =
                filteredData.length > 0 ? filteredData[0].Bukti_lain : null;
            clearImages();
            formTransaksi.bukti_lain = [];
            // await gadaiData();
            // const filteredData = gadai.value.filter((g) => g.No_sbg === formTransaksiDtl.no_sbg);
            // console.log(filteredData);
            // formTransaksiDtl.terima_uang = filteredData.length > 0 ? filteredData[0].Terima_uang : null;
            // formTransaksi.terima_uang = ""
        } catch (error) {
            console.log("error", error);

            // Handle validation errors
            if (error.response && error.response.status === 422) {
                formTransaksi.errors = error.response.data.errors || {};
            }

            // Show error message
            Swal.fire({
                title: "Terjadi kesalahan",
                html: "Periksa kembali inputannya",
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
//

//delete Barang Jaminan
const deleteBarang = () => {
    axios
        .post(route("deleteBarang"), { no_sbg: formTransaksiSub.no_sbg })
        .then(() => {
            getBarangJaminan();
            detailBarang.value = [];
            isBengkok.value = null;
            isPatah.value = null;
            isMataCincin.value = null;
            jumlahJaminan.value = 0;
            formTransaksi.reset();
            formTransaksiSub.reset();
        })
        .catch((error) => {
            // Handle any errors here
            console.error("Error deleting data:", error);
        });
};
//

// delete Jaminan Motor
const deleteJaminanMotor = () => {
    axios
        .post(route("deleteMotor"), { no_sbg: formJmnMotor.no_sbg })
        .then(() => {
            getBarangJaminan();
            detailBarang.value = [];
            jumlahJaminan.value = 0;
            formTransaksi.reset();
            formJmnMotor.reset();
            showForm
        })
        .catch((error) => {
            // Handle any errors here
            console.error("Error deleting data:", error);
        });
};
//

// delete Jaminan Mobil
const deleteJaminanMobil = () => {
    axios
        .post(route("deleteMobil"), { no_sbg: formJmnMobil.no_sbg })
        .then(() => {
            getBarangJaminan();
            detailBarang.value = [];
            jumlahJaminan.value = 0;
            formTransaksi.reset();
            formJmnMobil.reset();
            showForm
        })
        .catch((error) => {
            // Handle any errors here
            console.error("Error deleting data:", error);
        });
};
//

//
const isBengkok = ref();
const isPatah = ref();
const isMataCincin = ref();

const updateDetailBarang = () => {
    let details = [];
    if (isBengkok.value === "false") {
        details.push("Batang Cincin Tidak Bengkok");
    } else if (isBengkok.value === "true") {
        details.push("Batang Cincin Bengkok");
    }
    if (isPatah.value === "false") {
        details.push("Cincin Tidak Patah");
    } else if (isPatah.value === "true") {
        details.push("Cincin Patah");
    }
    if (isMataCincin.value === "false") {
        details.push("Mata Cincin Tidak Hilang");
    } else if (isMataCincin.value === "true") {
        details.push("Mata Cincin Hilang");
    }
    formTransaksiSub.detail_barang = details.join(", ");
};

watch([isBengkok, isPatah, isMataCincin], updateDetailBarang);
//

const formatDecimal = (value) => {
    const number = parseFloat(value);
    if (isNaN(number)) {
        return value;
    }
    return number.toFixed(1);
};

const formatScale = (value) => {
    const number = parseFloat(value);
    if (isNaN(number)) {
        return value;
    }
    return number.toFixed(2);
};

const fotoBarangJmn = ref([]);
const selectFotoGadai = () => {
    fotoBarangJmn.value = barangJmn.value.filter(
        (b) => b.No_sbg === formTransaksiDtl.no_sbg
    );
    console.log(fotoBarangJmn.value);
};

const parseLainLain = (lainLain) => {
    try {
        if (lainLain === undefined) {
            console.log("Lain_lain is undefined.");
            return [];
        } else if (lainLain === "") {
            console.log("Lain_lain is empty.");
            return [];
        } else {
            const parsedData = JSON.parse(lainLain);
            console.log("Parsed Lain_lain:", parsedData);
            return parsedData;
        }
    } catch (error) {
        console.error("Error parsing Lain_lain:", error);
        return [];
    }
};

//selecData Gadai
const selectDataGadai = async (d) => {
    formTransaksiDtl.no_sbg = d.No_sbg;
    formTransaksiSub.no_sbg = d.No_sbg;
    formTransaksiDtl.no_kons = d.No_kons;
    formTransaksiDtl.asal_barang = d.Asal_barang;
    formTransaksiDtl.tujuan_transaksi = d.Tujuan_transaksi;
    formTransaksiDtl.fungsi_transaksi = d.Fungsi_transaksi;
    formTransaksiDtl.instrumen_pembayaran = d.Instrumen_pembayaran;
    formTransaksiDtl.pengembalian_uang_lebih = d.Pengembalian_uang_lebih;
    formTransaksiDtl.penaksir = d.Penaksir;
    formTransaksiDtl.type_transaksi = d.Type_transaksi;
    formTransaksiDtl.sewa_modal = d.Sewa_modal * 100;
    formTransaksiDtl.tgl_transaksi = formatDate(d.Tgl_transaksi);
    formTransaksiDtl.tgl_jtempo = formatDate(d.Tgl_jtempo);
    formTransaksiDtl.by_admin = formatRupiah(d.By_admin);
    formTransaksiDtl.tenor = d.Tenor;
    formTransaksiDtl.total_taksiran = formatRupiah(d.Total_taksiran);
    formTransaksiDtl.maks_pinjaman = formatRupiah(d.Maks_pinjaman);
    formTransaksiDtl.pokok_pinjaman = formatRupiah(d.Pokok_pinjaman);
    formTransaksiDtl.cara_pencairan = d.Cara_pencairan;
    formTransaksiDtl.no_rek_nasabah = d.No_rek_nasabah;
    formTransaksiDtl.referensi_nosbg = d.Referensi_nosbg;
    formTransaksiDtl.foto_identitas = d.Foto_identitas;
    formTransaksiDtl.terima_uang = d.Terima_uang ?? null;
    formTransaksiDtl.bukti_lain = d.Bukti_lain ?? null;
    formTransaksiDtl.produk_gadai = d.Produk_gadai
    await getBarangJaminan();
    selectFotoGadai();
};
//end

//barang jamninan input
const jenisJaminan = computed(() => {
    return selectedTipeGadai.value === "emas"
        ? "Emas"
        : selectedTipeGadai.value === "motor"
        ? "Motor"
        : "Mobil";
});
const jumlahJaminan = ref(0);
const jumlahBarang = ref(1);

const pilihGadaiAlert = () => {
    Swal.fire({
        title: "Peringatan",
        html: "Pilih Produk Gadai Terlebih Dahulu",
        icon: "warning",
        iconColor: "#FFFFFF",
        background: "#fec400",
        showConfirmButton: true,
        confirmButtonColor: "#FFFFFF",
        customClass: {
            title: "text-white", // Tailwind class for title text color
            popup: "text-white",
            confirmButton: "text-black",
        },
    });
};

// state sementara tombol tambah
const tambahState = ref(true);

</script>

<template>
    <Head title="Transaksi Gadai" />
    <AdminLayout>
        <template #titlePage>
            <TitlePage value="Transaksi Gadai" />
        </template>
        <template #content>
            <main class="p-6 bg-gray-100 flex-grow overflow-y-auto">
                <span
                    class="font-bold uppercase"
                    :class="
                        showForm.dataNasabah ||
                        showForm.barangJaminan ||
                        showForm.uangJaminan ||
                        showForm.keseluruhan
                            ? 'relative'
                            : 'hidden'
                    "
                >
                    Data Nasabah
                    <span
                        class="font-bold uppercase"
                        :class="
                            showForm.barangJaminan ||
                            showForm.uangJaminan ||
                            showForm.keseluruhan
                                ? 'relative'
                                : 'hidden'
                        "
                    >
                        > Barang Jaminan
                        <span
                            class="font-bold uppercase"
                            :class="
                                showForm.uangJaminan || showForm.keseluruhan
                                    ? 'relative'
                                    : 'hidden'
                            "
                        >
                            > Uang Jaminan
                            <span
                                class="font-bold uppercase"
                                :class="
                                    showForm.keseluruhan ? 'relative' : 'hidden'
                                "
                            >
                            </span>
                        </span>
                    </span>
                </span>
                <div
                    class="bg-red-500 p-4 rounded-lg shadow mb-4"
                >
                    <h2 class="text-xl text-white font-bold mb-2">
                        Informasi Penting
                    </h2>
                    <p class="text-white">
                        Sementara Segala Bentuk Pencairan Di Hold Sampai Batas Waktu Yang Belum Ditentukan
                    </p>
                </div>
                <div
                    class="mt-6"
                    id="tableTransakiOffline"
                    :class="
                        showForm.dataNasabah ||
                        showForm.barangJaminan ||
                        showForm.uangJaminan
                            ? 'hidden'
                            : 'relative'
                    "
                >
                    <div class="bg-white p-4 rounded-lg shadow">
                        <div class="flex justify-between items-center mb-4">
                            <div class="pr-2">
                                <SearchInput
                                    label="Cari Data"
                                    v-model="searchIGad"
                                    placeholder="Cari"
                                />
                            </div>
                            <div class="ml-4">
                                <div class="text-black">
                                    <button
                                        class="mt-4 ml-2 bg-sky-700 text-white py-1 px-4 rounded"
                                        :disabled="tambahState"
                                        :class="{
                                            'cursor-not-allowed':
                                                tambahState,
                                        }"
                                        @click="
                                            [
                                                (showForm.dataNasabah =
                                                    !showForm.dataNasabah),
                                                setNosbg(), setTglTransaksi(),
                                            ]
                                        "
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
                                            Nomor sbg
                                        </th>
                                        <th
                                            scope="col"
                                            class="text-center text-md font-sans uppercase pb-3 text-white"
                                        >
                                            No Ktp
                                        </th>
                                        <th
                                            scope="col"
                                            class="text-center text-md font-sans uppercase pb-3 text-white"
                                        >
                                            Nasabah
                                        </th>
                                        <th
                                            scope="col"
                                            class="text-center text-md font-sans uppercase pb-3 text-white"
                                        >
                                            tanggal transaksi
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
                                            uang pinjaman
                                        </th>
                                        <th
                                            scope="col"
                                            class="text-center text-md font-sans uppercase pb-3 text-white"
                                        >
                                            detail
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
                                            {{ d.nasabah.No_ktp }}
                                        </td>
                                        <td
                                            class="whitespace-nowrap text-sm text-center text-black py-2"
                                        >
                                            {{ d.nasabah.Nama }}
                                        </td>
                                        <td
                                            class="whitespace-nowrap text-sm text-center text-black py-2"
                                        >
                                            {{ formatDateDB(d.Tgl_transaksi) }}
                                        </td>
                                        <td
                                            class="whitespace-nowrap text-sm text-center text-black py-2"
                                        >
                                            {{ d.Posko }}
                                        </td>
                                        <td
                                            class="whitespace-nowrap text-sm text-center text-black py-2"
                                        >
                                            {{ formatRupiah(d.Pokok_pinjaman) }}
                                        </td>
                                        <td
                                            class="whitespace-nowrap text-sm text-center text-black py-2"
                                        >
                                            <a @click="modalDetailGadai(d)"
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
                    </div>
                </div>
                <div
                    :class="showForm.dataNasabah ? 'relative' : 'hidden'"
                    class="bg-white p-4 mt-6 rounded-lg shadow-lg"
                >
                    <div class="bg-white p-4 rounded-lg shadow-lg">
                        <h2 class="my-2 font-bold uppercase">Data Nasabah</h2>
                        <hr />
                        <div class="mt-6 grid grid-cols-2 gap-4">
                            <div class="sm:col-span-1 mr-5">
                                <CustTextInput
                                    id="no_identitas"
                                    type="text"
                                    name="no_identitas"
                                    label="Cari No Identitas/KTP"
                                    v-model="nasabahIdentitas"
                                />
                            </div>
                            <div class="sm:col-span-1">
                                <button
                                    @click="getNasabahId()"
                                    class="mt-6 bg-sky-700 text-white py-1.5 px-4 rounded"
                                >
                                    Cek Data Nasabah
                                </button>
                            </div>
                            <div class="sm:col-span-1 mr-5">
                                <CustTextInput
                                    id="no_ktp"
                                    type="text"
                                    name="no_ktp"
                                    label="No Ktp Nasabah"
                                    v-model="ktpNasabah"
                                    disabled="true"
                                />
                            </div>
                            <div class="sm:col-span-1 mr-5">
                                <CustTextInput
                                    id="nama_nasabah"
                                    type="text"
                                    name="nama_nasabah"
                                    label="Nama Nasabah"
                                    v-model="namaNasabah"
                                    disabled="true"
                                />
                            </div>
                            <div class="sm:col-span-1 mr-5">
                                <CustTextInput
                                    id="K_Kons"
                                    type="text"
                                    name="K_Kons"
                                    label="Kode Nasabah"
                                    v-model="kodeKonsumen"
                                    disabled="true"
                                />
                            </div>
                            <div class="sm:col-span-1">
                                <button
                                    class="mt-6 bg-sky-700 text-white py-1.5 px-4 rounded"
                                    @click="modalKelengkapan()"
                                >
                                    Cek Kelengkapan Data Nasabah
                                </button>
                            </div>
                            <div class="sm:col-span-1 mr-5">
                                <UploadImage
                                    ref="uploadImageRef"
                                    label="Foto Identitas"
                                    @file-selected="handleFotoIdentitas"
                                />
                                <span
                                    v-if="!formTransaksi.errors.foto_identitas"
                                    class="text-sm text-black"
                                    >* Format: JPEG,JPG,PNG & MAKSIMAL 2MB</span
                                >
                                <div v-if="formTransaksi.errors.foto_identitas">
                                    <span class="text-sm text-red-600"
                                        >*{{
                                            formTransaksi.errors
                                                .foto_identitas[0]
                                        }}</span
                                    >
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="flex justify-end items-center mb-4">
                        <div class="ml-4">
                            <div class="text-black">
                                <button
                                    class="mt-4 ml-2 bg-red-700 text-white py-1 px-4 rounded"
                                    @click="
                                        showForm.dataNasabah = !showForm.dataNasabah;
                                        selectedTipeGadai === 'emas' ? deleteBarang() : 
                                        selectedTipeGadai === 'motor' ? deleteJaminanMotor() :
                                        selectedTipeGadai === 'mobil' ? deleteJaminanMobil() : '';
                                        resetAll();
                                    "
                                >
                                    Batal
                                </button>
                                <button
                                    @click="selectedTipeGadai !== '' ? (showForm.dataNasabah = !showForm.dataNasabah, showForm.barangJaminan = !showForm.barangJaminan) : pilihGadaiAlert()"
                                    class="mt-4 ml-2 bg-green-700 text-white py-1 px-4 rounded"
                                >
                                    Lanjut
                                </button>
                            </div>
                        </div>
                    </div>
                    <!-- Modal Detail -->
                    <Modal
                        :show="modal.detailGadai"
                        @close="[(modal.detailGadai = false)]"
                        maxWidth="6xl"
                        classHeader="bg-sky-700 text-white"
                    >
                        <template #title> Detail Gadai </template>
                        <template #content>
                            <div class="mt-4">
                                <h3 v-if="selectedTipeGadai === 'emas'" class="text-xl font-bold uppercase">
                                    Data Gadai Emas
                                </h3>
                                <h3 v-if="selectedTipeGadai === 'motor'" class="text-xl font-bold uppercase">
                                    Data Gadai Motor
                                </h3>
                                <h3 v-if="selectedTipeGadai === 'mobil'" class="text-xl font-bold uppercase">
                                    Data Gadai Mobil
                                </h3>
                            </div>
                            <div class="mt-6 grid grid-cols-3 gap-6">
                                <div class="sm:col-span-1">
                                    <CustDetail
                                        label="No SBG"
                                        :modelValue="formTransaksiDtl.no_sbg"
                                    />
                                </div>
                                <div class="sm:col-span-1">
                                    <CustDetail
                                        id="no_kons_dtl"
                                        name="no_kons_dtl"
                                        label="Kode Nasabah"
                                        :modelValue="formTransaksiDtl.no_kons"
                                    />
                                </div>
                                <div class="sm:col-span-1">
                                    <CustDetail
                                        id="tgl_transaksi"
                                        name="tgl_transaksi"
                                        label="Tgl Transaksi"
                                        :modelValue="
                                            formTransaksiDtl.tgl_transaksi
                                        "
                                    />
                                </div>
                                <div class="sm:col-span-1">
                                    <CustDetail
                                        id="type_transaksi_dtl"
                                        name="type_transaksi_dtl"
                                        label="Tipe Transaksi"
                                        :modelValue="
                                            formTransaksiDtl.type_transaksi
                                        "
                                    />
                                </div>
                                <div class="sm:col-span-1">
                                    <CustDetail
                                        id="sewa_modal_dtl"
                                        name="sewa_modal_dtl"
                                        label="Sewa Modal"
                                        :modelValue="
                                            formatDecimal(
                                                formTransaksiDtl.sewa_modal
                                            )
                                        "
                                        ket="%"
                                    />
                                </div>
                                <div class="sm:col-span-1">
                                    <CustDetail
                                        id="tgl_jtempo_dtl"
                                        name="tgl_jtempo_dtl"
                                        label="Penaksir"
                                        :modelValue="formTransaksiDtl.penaksir"
                                    />
                                </div>
                                <div class="sm:col-span-1">
                                    <CustDetail
                                        id="by_admin_dtl"
                                        name="by_admin_dtl"
                                        label="Biaya Admin"
                                        :modelValue="formTransaksiDtl.by_admin"
                                    />
                                </div>
                                <div class="sm:col-span-1">
                                    <CustDetail
                                        id="tenor_dtl"
                                        name="tenor_dtl"
                                        label="Tenor"
                                        :modelValue="formTransaksiDtl.tenor"
                                        ket="Hari"
                                    />
                                </div>
                                <div class="sm:col-span-1">
                                    <CustDetail
                                        id="tgl_jtempo_dtl"
                                        name="tgl_jtempo_dtl"
                                        label="Tgl Jtempo"
                                        :modelValue="
                                            formTransaksiDtl.tgl_jtempo
                                        "
                                    />
                                </div>
                                <div class="sm:col-span-1">
                                    <CustDetail
                                        id="total_taksiran_dtl"
                                        name="total_taksiran_dtl"
                                        label="Total Taksiran"
                                        :modelValue="
                                            formTransaksiDtl.total_taksiran
                                        "
                                    />
                                </div>
                                <div class="sm:col-span-1">
                                    <CustDetail
                                        id="maks_pinjaman_dtl"
                                        name="maks_pinjaman_dtl"
                                        label="Maks Pinjaman"
                                        :modelValue="
                                            formTransaksiDtl.maks_pinjaman
                                        "
                                    />
                                </div>
                                <div class="sm:col-span-1">
                                    <CustDetail
                                        id="pokok_pinjaman_dtl"
                                        name="pokok_pinjaman_dtl"
                                        label="Pokok Pinjaman"
                                        :modelValue="
                                            formTransaksiDtl.pokok_pinjaman
                                        "
                                    />
                                </div>
                                <div class="sm:col-span-1">
                                    <CustDetail
                                        id="cara_pencairan_dtl"
                                        name="cara_pencairan_dtl"
                                        label="Cara Pencairan"
                                        :modelValue="
                                            formTransaksiDtl.cara_pencairan
                                        "
                                    />
                                </div>
                                <div class="sm:col-span-1">
                                    <CustDetail
                                        id="no_rek_nasabah_dtl"
                                        name="no_rek_nasabah_dtl"
                                        label="No Rekening"
                                        :modelValue="
                                            formTransaksiDtl.no_rek_nasabah
                                        "
                                    />
                                </div>
                                <div class="sm:col-span-1">
                                    <CustDetail
                                        id="referensi_sbg"
                                        name="referensi_sbg"
                                        label="Referensi SBG"
                                        :modelValue="
                                            formTransaksiDtl.referensi_nosbg
                                        "
                                    />
                                </div>
                                <div class="sm:col-span-1">
                                    <CustDetail
                                        id="asal_barang_dtl"
                                        name="asal_barang_dtl"
                                        label="Asal Barang"
                                        :modelValue="
                                            formTransaksiDtl.asal_barang
                                        "
                                    />
                                </div>
                                <div class="sm:col-span-1">
                                    <CustDetail
                                        id="tujuan_transaksi_dtl"
                                        name="tujuan_transaksi_dtl"
                                        label="Tujuan Transaksi"
                                        :modelValue="
                                            formTransaksiDtl.tujuan_transaksi
                                        "
                                    />
                                </div>
                                <div class="sm:col-span-1">
                                    <CustDetail
                                        id="instrumen_dtl"
                                        name="instrumen_dtl"
                                        label="Instrumen Pembayaran"
                                        :modelValue="
                                            formTransaksiDtl.instrumen_pembayaran
                                        "
                                    />
                                </div>
                                <div class="sm:col-span-1">
                                    <CustDetail
                                        id="pengembalian_uang_dtl"
                                        name="pengembalian_uang_dtl"
                                        label="Pengembalian Uang Lebih"
                                        :modelValue="
                                            formTransaksiDtl.pengembalian_uang_lebih
                                        "
                                    />
                                </div>
                                <div class="sm:col-span-1">
                                    <CustDetail
                                        id="fungsi_transaksi_dtl"
                                        name="fungsi_transaksi_dtl"
                                        label="Fungsi Transaksi"
                                        :modelValue="
                                            formTransaksiDtl.fungsi_transaksi
                                        "
                                    />
                                </div>
                            </div>
                            <div class="my-3">
                                <hr class="border-2 border-gray-500" />
                            </div>
                            <div class="mt-4">
                                <h3 class="text-xl font-bold uppercase">
                                    Barang Jaminan
                                </h3>
                            </div>
                            <div
                                class="py-3 my-3 rounded-lg bg-sky-700 shadow-lg"
                            >
                                <table class="min-w-full bg-sky-700 shadow-lg">
                                    <thead v-if="selectedTipeGadai === 'emas'">
                                        <tr>
                                            <th
                                                scope="col"
                                                class="text-center text-md font-sans uppercase pb-3 text-white"
                                            >
                                                item
                                            </th>
                                            <th
                                                scope="col"
                                                class="text-center text-md font-sans uppercase pb-3 text-white"
                                            >
                                                jumlah
                                            </th>
                                            <th
                                                scope="col"
                                                class="text-center text-md font-sans uppercase pb-3 text-white"
                                            >
                                                kadar<br />(karat)
                                            </th>
                                            <th
                                                scope="col"
                                                class="text-center text-md font-sans uppercase pb-3 text-white"
                                            >
                                                Berat Kotor<br />(gr)
                                            </th>
                                            <th
                                                scope="col"
                                                class="text-center text-md font-sans uppercase pb-3 text-white"
                                            >
                                                Berat Bersih<br />(gr)
                                            </th>
                                            <th
                                                scope="col"
                                                class="text-center text-md font-sans uppercase pb-3 text-white"
                                            >
                                                taksiran
                                            </th>
                                            <th
                                                scope="col"
                                                class="text-center text-md font-sans uppercase pb-3 text-white"
                                            >
                                                maks uang <br />
                                                pinjaman
                                            </th>
                                        </tr>
                                    </thead>
                                    <thead v-if="selectedTipeGadai === 'motor' || selectedTipeGadai === 'mobil'">
                                        <tr>
                                            <th
                                                scope="col"
                                                class="text-center text-md font-sans uppercase pb-3 text-white"
                                            >
                                                item
                                            </th>
                                            <th
                                                scope="col"
                                                class="text-center text-md font-sans uppercase pb-3 text-white"
                                            >
                                                jumlah
                                            </th>
                                            <th
                                                scope="col"
                                                class="text-center text-md font-sans uppercase pb-3 text-white"
                                            >
                                                merk
                                            </th>
                                            <th
                                                scope="col"
                                                class="text-center text-md font-sans uppercase pb-3 text-white"
                                            >
                                                tipe
                                            </th>
                                            <th
                                                scope="col"
                                                class="text-center text-md font-sans uppercase pb-3 text-white"
                                            >
                                                tahun
                                            </th>
                                            <th
                                                scope="col"
                                                class="text-center text-md font-sans uppercase pb-3 text-white"
                                            >
                                                taksiran
                                            </th>
                                            <th
                                                scope="col"
                                                class="text-center text-md font-sans uppercase pb-3 text-white"
                                            >
                                                maks uang <br />
                                                pinjaman
                                            </th>
                                        </tr>
                                    </thead>
                                    <tbody v-if="!barangLoading">
                                        <tr
                                            v-for="(d, index) in barangJmn"
                                            :key="index"
                                            class="odd:bg-white even:bg-amber-400"
                                        >
                                            <td
                                                class="whitespace-nowrap text-sm text-center text-black py-2"
                                            >
                                                {{ index + 1 }}
                                            </td>
                                            <td
                                                class="whitespace-nowrap text-sm text-center text-black py-2"
                                            >
                                                1
                                            </td>
                                            <td v-if="selectedTipeGadai === 'emas'"
                                                class="whitespace-nowrap text-sm text-center text-black py-2"
                                            >
                                                {{ d.Kadar }}
                                            </td>
                                            <td v-if="selectedTipeGadai === 'motor' || selectedTipeGadai === 'mobil'"
                                                class="whitespace-nowrap text-sm text-center text-black py-2"
                                            >
                                                {{ d.Merk }}
                                            </td>
                                            <td v-if="selectedTipeGadai === 'emas'"
                                                class="whitespace-nowrap text-sm text-center text-black py-2"
                                            >
                                                {{ formatDecimal(d.Berat_kotor) }}
                                            </td>
                                            <td v-if="selectedTipeGadai === 'motor' || selectedTipeGadai === 'mobil'"
                                                class="whitespace-nowrap text-sm text-center text-black py-2"
                                            >
                                                {{ d.Tipe }}
                                            </td>
                                            <td v-if="selectedTipeGadai === 'emas'"
                                                class="whitespace-nowrap text-sm text-center text-black py-2"
                                            >
                                                {{ formatDecimal(d.Berat_bersih) }}
                                            </td>
                                            <td v-if="selectedTipeGadai === 'motor' || selectedTipeGadai === 'mobil'"
                                                class="whitespace-nowrap text-sm text-center text-black py-2"
                                            >
                                                {{ d.Tahun }}
                                            </td>
                                            <td
                                                class="whitespace-nowrap text-sm text-center text-black py-2"
                                            >
                                                {{ formatRupiah(d.Taksiran) }}
                                            </td>
                                            <td
                                                class="whitespace-nowrap text-sm text-center text-black py-2"
                                            >
                                                {{
                                                    formatRupiah(
                                                        d.Maks_pinjaman
                                                    )
                                                }}
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                                <div class="w-full text-center">
                                    <span
                                        class="text-center text-xl text-white"
                                        v-if="barangLoading"
                                        >Sedang Memuat...</span
                                    >
                                </div>
                            </div>
                            <div class="flex justify-end items-center my-10">
                                <div class="ml-4">
                                    <div class="text-black">
                                        <button
                                            @click="modalGaleri()"
                                            class="mt-4 ml-2 bg-yellow-700 text-white py-1 px-4 rounded"
                                        >
                                            Galeri
                                        </button>
                                        <button
                                            @click="cetakUlangSBG()"
                                            class="mt-4 ml-2 bg-yellow-700 text-white py-1 px-4 rounded"
                                        >
                                            Cetak Ulang SBG
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </template>
                    </Modal>

                    <!-- Modal Galeri -->
                    <Modal
                        :show="modal.galeri"
                        @close="[(modal.galeri = false)]"
                        maxWidth="2xl"
                        classHeader="bg-sky-700 text-white"
                    >
                        <template #title> Galeri </template>
                        <template #content>
                            <div class="mt-4">
                                <h3
                                    class="text-xl font-bold uppercase"
                                    v-if="formTransaksiDtl.no_sbg.includes('P')"
                                >
                                    Berkas Foto Gadai Emas (Perpanjangan)
                                </h3>
                                <h3 class="text-xl font-bold uppercase" v-else>
                                    Berkas Foto Gadai Emas
                                </h3>
                            </div>
                            <div class="mt-6 grid grid-cols-3 gap-6">
                                <div class="my-1 col-span-3">
                                    <hr class="border-2 border-gray-500" />
                                </div>
                                <div class="col-span-3">
                                    <div
                                        @click="toggleAccordion(0)"
                                        class="cursor-pointer border-b-2 border-gray-500 py-2 flex justify-between items-center"
                                    >
                                        <h3 class="text-xl font-bold uppercase">
                                            Foto Identitas
                                        </h3>
                                        <span>{{
                                            openAccordion === 0 ? "▲" : "▼"
                                        }}</span>
                                    </div>
                                    <div
                                        v-if="openAccordion === 0"
                                        class="sm:col-span-3 pt-4"
                                    >
                                        <CustImageDetail
                                            folder="TransaksiGadai/KtpPhoto"
                                            :image="
                                                formTransaksiDtl.foto_identitas
                                            "
                                            :requiredIndicator="false"
                                        />
                                    </div>
                                </div>
                                <div class="my-1 col-span-3">
                                    <hr class="border-2 border-gray-500" />
                                </div>
                                <div class="col-span-3">
                                    <div
                                        @click="toggleAccordion(1)"
                                        class="cursor-pointer border-b-2 border-gray-500 py-2 flex justify-between items-center"
                                    >
                                        <h3 class="text-xl font-bold uppercase">
                                            Foto Konsumen Terima Uang
                                        </h3>
                                        <span>{{
                                            openAccordion === 1 ? "▲" : "▼"
                                        }}</span>
                                    </div>
                                    <div
                                        v-if="openAccordion === 1"
                                        class="sm:col-span-3 pt-4"
                                    >
                                        <CustImageDetail
                                            v-if="formTransaksiDtl.terima_uang"
                                            folder="TransaksiGadai/TerimaUangPhoto"
                                            :image="
                                                formTransaksiDtl.terima_uang
                                            "
                                            :requiredIndicator="false"
                                        />
                                        <div
                                            v-if="!formTransaksiDtl.terima_uang"
                                        >
                                            <UploadImage
                                                label="Foto Kons Terima Uang"
                                                @file-selected="
                                                    handleFotoTerimaUang
                                                "
                                            />
                                            <span
                                                v-if="
                                                    !formTransaksi.errors
                                                        .terima_uang
                                                "
                                                class="text-sm text-black"
                                                >* Format: JPEG,JPG,PNG &
                                                MAKSIMAL 2MB</span
                                            >
                                            <div
                                                v-if="
                                                    formTransaksi.errors
                                                        .terima_uang
                                                "
                                            >
                                                <span
                                                    class="text-sm text-red-600"
                                                    >*{{
                                                        formTransaksi.errors
                                                            .terima_uang[0]
                                                    }}</span
                                                >
                                            </div>
                                            <button
                                                @click="uploadTerimaUang()"
                                                class="mt-4 ml-2 bg-green-700 text-white py-1 px-4 rounded"
                                            >
                                                Upload
                                            </button>
                                        </div>
                                    </div>
                                </div>
                                <div
                                    class="my-1 col-span-3"
                                    v-if="formTransaksiDtl.no_sbg.includes('P')"
                                >
                                    <hr class="border-2 border-gray-500" />
                                </div>
                                <div
                                    class="col-span-3"
                                    v-if="formTransaksiDtl.no_sbg.includes('P')"
                                >
                                    <div
                                        @click="toggleAccordion(2)"
                                        class="cursor-pointer border-b-2 border-gray-500 py-2 flex justify-between items-center"
                                    >
                                        <h3 class="text-xl font-bold uppercase">
                                            Foto Bukti Lainnya
                                        </h3>
                                        <span>{{
                                            openAccordion === 2 ? "▲" : "▼"
                                        }}</span>
                                    </div>
                                    <div
                                        v-if="openAccordion === 2"
                                        class="sm:col-span-3 pt-4"
                                    >
                                        <!-- Loop through each image if Lain_lain exists and has images -->
                                        <div
                                            v-if="
                                                parseLainLain(
                                                    formTransaksiDtl.bukti_lain
                                                ) != null
                                            "
                                        >
                                            <div
                                                v-for="(
                                                    image, index
                                                ) in parseLainLain(
                                                    formTransaksiDtl.bukti_lain
                                                )"
                                                :key="index"
                                                class="my-2"
                                            >
                                                <CustImageDetail
                                                    v-if="image"
                                                    :folder="`TransaksiGadai/BuktiLainnya/${formTransaksiDtl.no_sbg}`"
                                                    :image="image"
                                                    :requiredIndicator="false"
                                                />
                                            </div>
                                        </div>
                                        <h4 v-else class="text-md font-bold">
                                            KOSONG...
                                        </h4>
                                    </div>
                                </div>
                                <div class="my-1 col-span-3">
                                    <hr class="border-2 border-gray-500" />
                                </div>
                                <div class="col-span-3">
                                    <div
                                        @click="toggleAccordion(3)"
                                        class="cursor-pointer border-b-2 border-gray-500 py-2 flex justify-between items-center"
                                    >
                                        <h3 class="text-xl font-bold uppercase">
                                            Foto Barang Jaminan Sisi 1
                                        </h3>
                                        <span>{{
                                            openAccordion === 3 ? "▲" : "▼"
                                        }}</span>
                                    </div>
                                    <div
                                        v-if="openAccordion === 3"
                                        class="sm:col-span-3 pt-4"
                                    >
                                        <div
                                            v-for="(d, index) in fotoBarangJmn"
                                            :key="index"
                                            class="my-2"
                                        >
                                            <h4 class="text-md font-bold">
                                                Foto Barang Ke {{ index + 1 }}
                                            </h4>
                                            <CustImageDetail
                                                :folder="`${formTransaksiDtl.produk_gadai === 'EMAS'
                                                        ? 'BarangJaminan/'
                                                        : ''
                                                    }${
                                                    formTransaksiDtl.no_sbg.includes(
                                                        'P'
                                                    )
                                                        ? 'Perpanjang/'
                                                        : formTransaksiDtl.no_sbg.includes(
                                                        'P'
                                                    ) && formTransaksiDtl.produk_gadai === 'MOTOR'
                                                        ? 'Perpanjang/JaminanMotor/'
                                                        : formTransaksiDtl.no_sbg.includes(
                                                        'P'
                                                    ) && formTransaksiDtl.produk_gadai === 'MOBIL'
                                                        ? 'Perpanjang/JaminanMobil/'
                                                        : formTransaksiDtl.produk_gadai === 'MOTOR'
                                                        ? 'JaminanMotor/'
                                                        : formTransaksiDtl.produk_gadai === 'MOBIL'
                                                        ? 'JaminanMobil/'
                                                        : ''
                                                }${formTransaksiDtl.no_sbg}`"
                                                :image="d.Foto_barang"
                                                :requiredIndicator="false"
                                            />
                                        </div>
                                    </div>
                                </div>
                                <div class="my-1 col-span-3">
                                    <hr class="border-2 border-gray-500" />
                                </div>
                                <div class="col-span-3">
                                    <div
                                        @click="toggleAccordion(4)"
                                        class="cursor-pointer border-b-2 border-gray-500 py-2 flex justify-between items-center"
                                    >
                                        <h3 class="text-xl font-bold uppercase">
                                            Foto Barang Jaminan Sisi 2
                                        </h3>
                                        <span>{{
                                            openAccordion === 4 ? "▲" : "▼"
                                        }}</span>
                                    </div>
                                    <div
                                        v-if="openAccordion === 4"
                                        class="sm:col-span-3 pt-4"
                                    >
                                        <div
                                            v-for="(d, index) in fotoBarangJmn"
                                            :key="index"
                                            class="my-2"
                                        >
                                            <h4 class="text-md font-bold">
                                                Foto Barang Ke {{ index + 1 }}
                                            </h4>
                                            <CustImageDetail
                                                :folder="`${formTransaksiDtl.produk_gadai === 'EMAS'
                                                        ? 'BarangJaminan/'
                                                        : ''
                                                    }${
                                                    formTransaksiDtl.no_sbg.includes(
                                                        'P'
                                                    )
                                                        ? 'Perpanjang/'
                                                        : formTransaksiDtl.no_sbg.includes(
                                                        'P'
                                                    ) && formTransaksiDtl.produk_gadai == 'MOBIL'
                                                        ? 'Perpanjang/JaminanMotor/'
                                                        : formTransaksiDtl.no_sbg.includes(
                                                        'P'
                                                    ) && formTransaksiDtl.produk_gadai == 'MOBIL'
                                                        ? 'Perpanjang/JaminanMobil/'
                                                        : formTransaksiDtl.produk_gadai == 'MOTOR'
                                                        ? 'JaminanMotor/'
                                                        : formTransaksiDtl.produk_gadai == 'MOBIL'
                                                        ? 'JaminanMobil/'
                                                        : ''
                                                }${formTransaksiDtl.no_sbg}`"
                                                :image="d.Foto_barang2"
                                                :requiredIndicator="false"
                                            />
                                        </div>
                                    </div>
                                </div>
                                <div class="my-1 col-span-3">
                                    <hr class="border-2 border-gray-500" />
                                </div>
                                <div class="col-span-3">
                                    <div
                                        @click="toggleAccordion(5)"
                                        class="cursor-pointer border-b-2 border-gray-500 py-2 flex justify-between items-center"
                                    >
                                        <h3 class="text-xl font-bold uppercase">
                                            Foto Tambahan
                                        </h3>
                                        <span>{{
                                            openAccordion === 5 ? "▲" : "▼"
                                        }}</span>
                                    </div>
                                    <div
                                        v-if="openAccordion === 5"
                                        class="sm:col-span-3 pt-4"
                                    >
                                        <div
                                            v-for="(
                                                item, itemIndex
                                            ) in fotoBarangJmn"
                                            :key="itemIndex"
                                        >
                                            <h3 class="text-lg font-bold">
                                                Barang {{ itemIndex + 1 }}
                                            </h3>
                                            <div
                                                v-if="
                                                    item.Lain_lain &&
                                                    parseLainLain(
                                                        item.Lain_lain
                                                    ).length > 0
                                                "
                                            >
                                                <!-- Loop through each image if Lain_lain exists and has images -->
                                                <div
                                                    v-for="(
                                                        image, index
                                                    ) in parseLainLain(
                                                        item.Lain_lain
                                                    )"
                                                    :key="index"
                                                    class="my-2"
                                                >
                                                    <h4
                                                        class="text-md font-bold"
                                                    >
                                                        Foto Tambahan Ke
                                                        {{ index + 1 }}
                                                    </h4>
                                                    <CustImageDetail
                                                        :folder="`${formTransaksiDtl.produk_gadai === 'EMAS'
                                                                ? 'BarangJaminan/'
                                                                : formTransaksiDtl.produk_gadai === 'MOTOR'
                                                                ? 'JaminanMotor/'
                                                                : formTransaksiDtl.produk_gadai === 'MOBIL'
                                                                ? 'JaminanMobil/'
                                                                : ''}${
                                                            formTransaksiDtl.no_sbg
                                                        }/FotoTambahan/${
                                                            item.Kode_barang
                                                        }`"
                                                        :image="image"
                                                        :requiredIndicator="
                                                            false
                                                        "
                                                    />
                                                </div>
                                            </div>
                                            <div v-else>
                                                <!-- Display this if Lain_lain is missing or empty -->
                                                <h4 class="text-md font-bold">
                                                    KOSONG...
                                                </h4>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="flex justify-end items-center my-10">
                                <div class="ml-4">
                                    <div class="text-black">
                                        <button
                                            v-if="
                                                formTransaksiDtl.no_sbg.includes(
                                                    'P'
                                                )
                                            "
                                            @click="modalBuktiLainnya()"
                                            class="mt-4 ml-2 bg-sky-700 text-white py-1 px-4 rounded"
                                        >
                                            Upload Bukti Lainnya
                                        </button>
                                        <button
                                            v-else
                                            @click="modalFotoTambahan()"
                                            class="mt-4 ml-2 bg-sky-700 text-white py-1 px-4 rounded"
                                        >
                                            Upload Foto Tambahan
                                        </button>
                                        <button
                                            @click="backBtnModalGaleri()"
                                            class="mt-4 ml-2 bg-yellow-700 text-white py-1 px-4 rounded"
                                        >
                                            Kembali
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </template>
                    </Modal>

                    <!-- Modal Foto Tambahan -->
                    <Modal
                        :show="modal.buktiLainnya"
                        @close="backBtnBuktiLainnya"
                        maxWidth="2xl"
                        classHeader="bg-sky-700 text-white"
                    >
                        <template #title> Upload Foto Bukti Lainnya </template>
                        <template #content>
                            <div class="mt-6 grid grid-cols-3 gap-6">
                                <div class="col-span-3">
                                    <div class="sm:col-span-3 pt-4">
                                        <UploadMultiImage
                                            ref="uploadMultiImage"
                                            @files-uploaded="
                                                handleFotoBuktiLainnya
                                            "
                                            v-model="formTransaksi.bukti_lain"
                                            :requiredIndicator="false"
                                        />
                                        <!-- Instructional text displayed once for each item in bukti_lain -->
                                        <span
                                            v-if="
                                                !formTransaksi.errors.bukti_lain
                                            "
                                            class="text-sm text-black"
                                        >
                                            * Format: JPEG,JPG,PNG & MAKSIMAL
                                            2MB
                                        </span>
                                        <div
                                            v-for="(
                                                file, index
                                            ) in formTransaksi.bukti_lain"
                                            :key="index"
                                            class="sm:col-span-2"
                                        >
                                            <!-- Error message for the specific index -->
                                            <div
                                                v-if="
                                                    formTransaksi.errors[
                                                        'bukti_lain.' + index
                                                    ]
                                                "
                                            >
                                                <span
                                                    class="text-sm text-red-600"
                                                >
                                                    *{{
                                                        formTransaksi.errors[
                                                            "bukti_lain." +
                                                                index
                                                        ][0]
                                                    }}
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="flex justify-end items-center my-10">
                                <div class="ml-4">
                                    <div class="text-black">
                                        <button
                                            @click="backBtnBuktiLainnya()"
                                            class="mt-4 ml-2 bg-yellow-700 text-white py-1 px-4 rounded"
                                        >
                                            Kembali
                                        </button>
                                        <button
                                            @click="uploadBuktiLain"
                                            class="mt-4 ml-2 bg-green-700 text-white py-1 px-4 rounded"
                                        >
                                            Upload
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </template>
                    </Modal>

                    <!-- Modal Foto Tambahan -->
                    <Modal
                        :show="modal.fotoTambahan"
                        @close="backBtnFotoTambahan"
                        maxWidth="2xl"
                        classHeader="bg-sky-700 text-white"
                    >
                        <template #title> Upload Foto Tambahan </template>
                        <template #content>
                            <div class="mt-6 grid grid-cols-3 gap-6">
                                <div class="col-span-3">
                                    <SelectKodeBarang
                                        :source="barangJmn"
                                        v-model:modelValue="
                                            formTransaksiSub.kode_barang
                                        "
                                        label="Pilih Barang"
                                    />
                                    <div class="sm:col-span-3 pt-4">
                                        <UploadMultiImage
                                            ref="uploadMultiImage"
                                            @files-uploaded="
                                                handleFilesUploaded
                                            "
                                            v-model="formTransaksiSub.lain_lain"
                                            :requiredIndicator="false"
                                        />
                                        <!-- Instructional text displayed once for each item in bukti_lain -->
                                        <span
                                            v-if="
                                                !formTransaksiSub.errors
                                                    .lain_lain
                                            "
                                            class="text-sm text-black"
                                        >
                                            * Format: JPEG,JPG,PNG & MAKSIMAL
                                            2MB
                                        </span>
                                        <div
                                            v-for="(
                                                file, index
                                            ) in formTransaksiSub.lain_lain"
                                            :key="index"
                                            class="sm:col-span-2"
                                        >
                                            <!-- Error message for the specific index -->
                                            <div
                                                v-if="
                                                    formTransaksiSub.errors[
                                                        'lain_lain.' + index
                                                    ]
                                                "
                                            >
                                                <span
                                                    class="text-sm text-red-600"
                                                >
                                                    *{{
                                                        formTransaksiSub.errors[
                                                            "lain_lain." + index
                                                        ][0]
                                                    }}
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="flex justify-end items-center my-10">
                                <div class="ml-4">
                                    <div class="text-black">
                                        <button
                                            @click="backBtnFotoTambahan()"
                                            class="mt-4 ml-2 bg-yellow-700 text-white py-1 px-4 rounded"
                                        >
                                            Kembali
                                        </button>
                                        <button
                                            @click="uploadFotoTambahan"
                                            class="mt-4 ml-2 bg-green-700 text-white py-1 px-4 rounded"
                                        >
                                            Upload
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </template>
                    </Modal>

                    <!-- Modal Kelengkapan -->
                    <Modal
                        :show="modal.kelengkapan"
                        @close="[(modal.kelengkapan = false)]"
                        maxWidth="2xl"
                        classHeader="bg-sky-700 text-white"
                    >
                        <template #title> Kelengkapan Data Nasabah </template>
                        <template #content>
                            <div class="mt-6 grid grid-cols-2 gap-4">
                                <div class="sm:col-span-1 mr-5">
                                    <NestedSelect
                                        label="Asal Barang"
                                        :options="asalBarangOptions"
                                        v-model="formTransaksi.asal_barang"
                                        @focusin="
                                            dropdownStates.asalBarangState = true;
                                            formTransaksi.asal_barang = '';
                                        "
                                        @focusout="
                                            dropdownStates.asalBarangState = false
                                        "
                                    />
                                </div>
                                <div class="sm:col-span-1 mr-5">
                                    <NestedSelect
                                        label="Status Transaksi"
                                        :options="statusTransaksiOptions"
                                        v-model="formTransaksi.fungsi_transaksi"
                                        @focusin="
                                            dropdownStates.statusTransaksiState = true;
                                            formTransaksi.fungsi_transaksi = '';
                                        "
                                        @focusout="
                                            dropdownStates.statusTransaksiState = false
                                        "
                                    />
                                </div>
                                <div class="sm:col-span-1 mr-5">
                                    <NestedSelect
                                        label="Tujuan Transaksi"
                                        :options="tujuanTransaksiOptions"
                                        v-model="formTransaksi.tujuan_transaksi"
                                        @focusin="
                                            dropdownStates.tujuanTransaksiState = true;
                                            formTransaksi.tujuan_transaksi = '';
                                        "
                                        @focusout="
                                            dropdownStates.tujuanTransaksiState = false
                                        "
                                        v-if="
                                            formTransaksi.asal_barang &&
                                            !dropdownStates.asalBarangState
                                        "
                                    />
                                </div>
                                <div class="sm:col-span-1 mr-5">
                                    <NestedSelect
                                        label="Instrumen Pembayaran"
                                        :options="instrumenPembayaranOptions"
                                        v-model="
                                            formTransaksi.instrumen_pembayaran
                                        "
                                        @focusin="
                                            dropdownStates.instrumenState = true;
                                            formTransaksi.instrumen_pembayaran =
                                                '';
                                        "
                                        @focusout="
                                            dropdownStates.instrumenState = false
                                        "
                                        v-if="
                                            formTransaksi.fungsi_transaksi &&
                                            !dropdownStates.statusTransaksiState
                                        "
                                    />
                                </div>
                                <div class="sm:col-span-1 mr-5">
                                    <NestedSelect
                                        label="Pengembalian Uang Kelebihan"
                                        :options="[
                                            'Datang Sendiri',
                                            'Diwakilkan',
                                        ]"
                                        v-model="
                                            formTransaksi.pengembalian_uang_lebih
                                        "
                                        @focusin="
                                            formTransaksi.pengembalian_uang_lebih =
                                                ''
                                        "
                                        v-if="
                                            ((formTransaksi.instrumen_pembayaran &&
                                                !dropdownStates.instrumenState) ||
                                                (formTransaksi.tujuan_transaksi &&
                                                    formTransaksi.instrumen_pembayaran &&
                                                    !dropdownStates.tujuanTransaksiState)) &&
                                            !dropdownStates.tujuanTransaksiState
                                        "
                                    />
                                </div>
                            </div>
                            <div
                                class="flex justify-end items-center my-10 pr-4"
                            >
                                <div class="ml-4">
                                    <div class="text-black">
                                        <button
                                            @click="modal.kelengkapan = false"
                                            class="mt-16 ml-2 bg-green-700 text-white py-1 px-4 rounded"
                                        >
                                            Simpan
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </template>
                    </Modal>
                </div>
                <div
                    :class="showForm.barangJaminan ? 'relative' : 'hidden'"
                    class="bg-white p-4 mt-6 rounded-lg shadow-lg"
                >
                    <div
                        class="bg-white p-4 rounded-lg shadow-lg"
                        v-if="selectedTipeGadai === 'emas'"
                    >
                        <h2 class="my-2 font-bold uppercase">Barang Jaminan</h2>
                        <hr />
                        <div class="mt-6 mb-8 grid grid-cols-3 gap-4">
                            <div class="sm:col-span-1 mr-2">
                                <NestedSelect
                                    label="Posko"
                                    :options="[$page.props.auth.user.POSKO]"
                                    v-model="$page.props.auth.user.POSKO"
                                    disabled="true"
                                />
                            </div>
                            <div class="sm:col-span-1 mr-2">
                                <NestedSelect
                                    label="Penaksir"
                                    :options="penaksirOptions"
                                    v-model="formTransaksi.penaksir"
                                    @focusin="formTransaksi.penaksir = ''"
                                />
                            </div>
                            <div class="sm:col-span-1 mr-2">
                                <NestedSelect
                                    label="Tipe Transaksi"
                                    :options="referensiSbgOptions"
                                    v-model="formTransaksi.type_transaksi"
                                    @focusin="formTransaksi.type_transaksi = ''"
                                />
                            </div>
                        </div>
                        <hr />
                        <div class="flex justify-between items-center mb-4">
                            <div class="text-black">
                                <button
                                    class="mt-4 bg-sky-700 text-white py-1 px-4 rounded"
                                    @click="modalBarangJaminan()"
                                >
                                    Tambah
                                </button>
                            </div>
                        </div>
                        <div class="flex justify-start items-center">
                            <div class="text-black mr-10">
                                <h1
                                    class="tracking-wide block mb-2 text-xs font-bold text-gray-900 uppercase"
                                >
                                    Barang Jaminan : {{ jenisJaminan }}
                                </h1>
                            </div>
                            <div class="text-black">
                                <h1
                                    class="tracking-wide block mb-2 text-xs font-bold text-gray-900 uppercase"
                                >
                                    Jumlah : {{ jumlahJaminan }}
                                </h1>
                            </div>
                        </div>
                        <div class="py-3 my-3 rounded-lg bg-sky-700 shadow-lg">
                            <table class="min-w-full bg-sky-700 shadow-lg">
                                <thead>
                                    <tr>
                                        <th
                                            scope="col"
                                            class="text-center text-md font-sans uppercase pb-3 text-white"
                                        >
                                            item
                                        </th>
                                        <th
                                            scope="col"
                                            class="text-center text-md font-sans uppercase pb-3 text-white"
                                        >
                                            jumlah
                                        </th>
                                        <th
                                            scope="col"
                                            class="text-center text-md font-sans uppercase pb-3 text-white"
                                        >
                                            kadar<br />(karat)
                                        </th>
                                        <th
                                            scope="col"
                                            class="text-center text-md font-sans uppercase pb-3 text-white"
                                        >
                                            Berat Kotor<br />(gr)
                                        </th>
                                        <th
                                            scope="col"
                                            class="text-center text-md font-sans uppercase pb-3 text-white"
                                        >
                                            Berat Bersih<br />(gr)
                                        </th>
                                        <th
                                            scope="col"
                                            class="text-center text-md font-sans uppercase pb-3 text-white"
                                        >
                                            taksiran
                                        </th>
                                        <th
                                            scope="col"
                                            class="text-center text-md font-sans uppercase pb-3 text-white"
                                        >
                                            maks uang <br />
                                            pinjaman
                                        </th>
                                    </tr>
                                </thead>
                                <tbody v-if="!barangLoading">
                                    <tr
                                        v-for="(d, index) in barangJmn"
                                        :key="index"
                                        class="odd:bg-white even:bg-amber-400"
                                    >
                                        <td
                                            class="whitespace-nowrap text-sm text-center text-black py-2"
                                        >
                                            {{ index + 1 }}
                                        </td>
                                        <td
                                            class="whitespace-nowrap text-sm text-center text-black py-2"
                                        >
                                            1
                                        </td>
                                        <td
                                            class="whitespace-nowrap text-sm text-center text-black py-2"
                                        >
                                            {{ d.Kadar }}
                                        </td>
                                        <td
                                            class="whitespace-nowrap text-sm text-center text-black py-2"
                                        >
                                            {{ formatScale(d.Berat_kotor) }}
                                        </td>
                                        <td
                                            class="whitespace-nowrap text-sm text-center text-black py-2"
                                        >
                                            {{ formatScale(d.Berat_bersih) }}
                                        </td>
                                        <td
                                            class="whitespace-nowrap text-sm text-center text-black py-2"
                                        >
                                            {{ formatRupiah(d.Taksiran) }}
                                        </td>
                                        <td
                                            class="whitespace-nowrap text-sm text-center text-black py-2"
                                        >
                                            {{ formatRupiah(d.Maks_pinjaman) }}
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                            <div class="w-full text-center">
                                <span
                                    class="text-center text-xl text-white"
                                    v-if="barangLoading"
                                    >Sedang Memuat...</span
                                >
                            </div>
                        </div>
                        <div class="mt-8">
                            <TextAreaInput
                                id="alamat_kerabat"
                                name="alamat_kerabat"
                                label="Kondisi Fisik"
                                rows="5"
                                disabled="true"
                                v-model="detailBarang"
                                :required-indicator="false"
                            />
                        </div>
                    </div>
                    <div
                        class="bg-white p-4 rounded-lg shadow-lg"
                        v-if="selectedTipeGadai === 'motor' || selectedTipeGadai === 'mobil'"
                    >
                        <h2 class="my-2 font-bold uppercase">Barang Jaminan</h2>
                        <hr />
                        <div class="mt-6 mb-8 grid grid-cols-3 gap-4">
                            <div class="sm:col-span-1 mr-2">
                                <NestedSelect
                                    label="Posko"
                                    :options="[$page.props.auth.user.POSKO]"
                                    v-model="$page.props.auth.user.POSKO"
                                    disabled="true"
                                />
                            </div>
                            <div class="sm:col-span-1 mr-2">
                                <NestedSelect
                                    label="Penaksir"
                                    :options="penaksirOptions"
                                    v-model="formTransaksi.penaksir"
                                    @focusin="formTransaksi.penaksir = ''"
                                />
                            </div>
                            <div class="sm:col-span-1 mr-2">
                                <NestedSelect
                                    label="Tipe Transaksi"
                                    :options="referensiSbgOptions"
                                    v-model="formTransaksi.type_transaksi"
                                    @focusin="formTransaksi.type_transaksi = ''"
                                />
                            </div>
                        </div>
                        <hr />
                        <div class="flex justify-between items-center mb-4">
                            <div class="text-black">
                                <button
                                    class="mt-4 bg-sky-700 text-white py-1 px-4 rounded"
                                    @click="modalBarangJaminan()"
                                >
                                    Tambah
                                </button>
                            </div>
                        </div>
                        <div class="flex justify-start items-center">
                            <div class="text-black mr-10">
                                <h1
                                    class="tracking-wide block mb-2 text-xs font-bold text-gray-900 uppercase"
                                >
                                    Barang Jaminan : {{ jenisJaminan }}
                                </h1>
                            </div>
                            <div class="text-black">
                                <h1
                                    class="tracking-wide block mb-2 text-xs font-bold text-gray-900 uppercase"
                                >
                                    Jumlah : {{ jumlahJaminan }}
                                </h1>
                            </div>
                        </div>
                        <div class="py-3 my-3 rounded-lg bg-sky-700 shadow-lg">
                            <table class="min-w-full bg-sky-700 shadow-lg">
                                <thead>
                                    <tr>
                                        <th
                                            scope="col"
                                            class="text-center text-md font-sans uppercase pb-3 text-white"
                                        >
                                            item
                                        </th>
                                        <th
                                            scope="col"
                                            class="text-center text-md font-sans uppercase pb-3 text-white"
                                        >
                                            Kode Barang
                                        </th>
                                        <th
                                            scope="col"
                                            class="text-center text-md font-sans uppercase pb-3 text-white"
                                        >
                                            Merk
                                        </th>
                                        <th
                                            scope="col"
                                            class="text-center text-md font-sans uppercase pb-3 text-white"
                                        >
                                            Tipe
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
                                            taksiran
                                        </th>
                                        <th
                                            scope="col"
                                            class="text-center text-md font-sans uppercase pb-3 text-white"
                                        >
                                            maks uang <br />
                                            pinjaman
                                        </th>
                                    </tr>
                                </thead>
                                <tbody v-if="!barangLoading">
                                    <tr
                                        v-for="(d, index) in barangJmn"
                                        :key="index"
                                        class="odd:bg-white even:bg-amber-400"
                                    >
                                        <td
                                            class="whitespace-nowrap text-sm text-center text-black py-2"
                                        >
                                            {{ index + 1 }}
                                        </td>
                                        <td
                                            class="whitespace-nowrap text-sm text-center text-black py-2"
                                        >
                                            {{ d.Kode_barang }}
                                        </td>
                                        <td
                                            class="whitespace-nowrap text-sm text-center text-black py-2"
                                        >
                                            {{ d.Merk }}
                                        </td>
                                        <td
                                            class="whitespace-nowrap text-sm text-center text-black py-2"
                                        >
                                            {{ d.Tipe }}
                                        </td>
                                        <td
                                            class="whitespace-nowrap text-sm text-center text-black py-2"
                                        >
                                            {{ d.Tahun }}
                                        </td>
                                        <td
                                            class="whitespace-nowrap text-sm text-center text-black py-2"
                                        >
                                            {{ formatRupiah(d.Taksiran) }}
                                        </td>
                                        <td
                                            class="whitespace-nowrap text-sm text-center text-black py-2"
                                        >
                                            {{ formatRupiah(d.Maks_pinjaman) }}
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                            <div class="w-full text-center">
                                <span
                                    class="text-center text-xl text-white"
                                    v-if="barangLoading"
                                    >Sedang Memuat...</span
                                >
                            </div>
                        </div>
                        <div class="mt-8">
                            <TextAreaInput
                                id="alamat_kerabat"
                                name="alamat_kerabat"
                                label="Kondisi Fisik"
                                rows="5"
                                disabled="true"
                                v-model="detailBarang"
                                :required-indicator="false"
                            />
                        </div>
                    </div>
                    <div class="flex justify-end items-center mb-4">
                        <div class="ml-4">
                            <div class="text-black">
                                <button
                                    class="mt-4 bg-red-700 text-white py-1 px-4 rounded"
                                    @click="
                                        [
                                            (showForm.dataNasabah =
                                                !showForm.dataNasabah),
                                            (showForm.barangJaminan =
                                                !showForm.barangJaminan),
                                        ]
                                    "
                                >
                                    Kembali
                                </button>
                                <button
                                    class="mt-4 ml-2 bg-red-700 text-white py-1 px-4 rounded"
                                    @click="
                                        showForm.barangJaminan = !showForm.barangJaminan;
                                        selectedTipeGadai === 'emas' ? deleteBarang() : 
                                        selectedTipeGadai === 'motor' ? deleteJaminanMotor() : 
                                        selectedTipeGadai === 'mobil' ? deleteJaminanMobil() : '';
                                        resetAll();
                                    "
                                >
                                    Batal
                                </button>
                                <button
                                    :disabled="validFormBarangJaminan"
                                    :class="{
                                        'cursor-not-allowed':
                                            validFormBarangJaminan,
                                    }"
                                    :title="
                                        validFormBarangJaminan
                                            ? 'Isi semua form'
                                            : 'Klik untuk menlanjutkan'
                                    "
                                    @click="
                                        showForm.barangJaminan = !showForm.barangJaminan;
                                        showForm.uangJaminan = !showForm.uangJaminan;
                                        selectedTipeGadai === 'emas' ? calculateTotalPinjamanEmas() :
                                        selectedTipeGadai === 'motor' || selectedTipeGadai === 'mobil' ? calculateTotalPinjamanMotorMobil() : '';
                                    "
                                    class="mt-4 ml-2 bg-green-700 text-white py-1 px-4 rounded"
                                >
                                    Lanjut
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Modal Barang Jaminan -->
                    <Modal
                        :show="modal.barangjaminan"
                        @close="[(modal.barangjaminan = false)]"
                        maxWidth="2xl"
                        classHeader="bg-sky-700 text-white"
                    >
                        <template #title> Barang Jaminan </template>
                        <template #content v-if="selectedTipeGadai === 'emas'">
                            <div class="mt-6 grid grid-cols-2 gap-4">
                                <div class="sm:col-span-1 pt-5">
                                    <h1
                                        class="tracking-wide block mb-2 text-xs font-bold text-gray-900 uppercase"
                                    >
                                        Jenis Barang Jaminan :
                                    </h1>
                                </div>
                                <div class="sm:col-span-1">
                                    <CustTextInput
                                        :required-indicator="false"
                                        id="jangka_waktu"
                                        type="text"
                                        name="jangka_waktu"
                                        v-model="jenisJaminan"
                                        disabled="true"
                                    />
                                </div>
                                <div class="sm:col-span-1 pt-5">
                                    <h1
                                        class="tracking-wide block mb-2 text-xs font-bold text-gray-900 uppercase"
                                    >
                                        Jumlah :
                                    </h1>
                                </div>
                                <div class="sm:col-span-1">
                                    <CustTextInput
                                        :requiredIndicator="false"
                                        id="jangka_waktu"
                                        type="text"
                                        name="jangka_waktu"
                                        v-model="jumlahBarang"
                                        disabled="true"
                                    />
                                </div>
                                <div class="sm:col-span-1 pt-5">
                                    <h1
                                        class="tracking-wide block mb-2 text-xs font-bold text-gray-900 uppercase"
                                    >
                                        Jenis :
                                    </h1>
                                </div>
                                <div class="sm:col-span-1">
                                    <NestedSelect
                                        :required-indicator="false"
                                        :options="jenisBarangOptions"
                                        v-model="formTransaksiSub.jenis"
                                        @focusin="
                                            dropdownStates.jenisBarangState = true;
                                            formTransaksiSub.jenis = '';
                                        "
                                        @focusout="
                                            dropdownStates.jenisBarangState = false
                                        "
                                        v-if="!dropdownStates.jenisJaminanState"
                                    />
                                </div>
                                <div
                                    class="sm:col-span-1 pt-5"
                                    v-if="
                                        formTransaksiSub.jenis === 'Lain lain'
                                    "
                                >
                                    <h1
                                        class="tracking-wide block mb-2 text-xs font-bold text-gray-900 uppercase"
                                    >
                                        Jenis Lainnya:
                                    </h1>
                                </div>
                                <div
                                    class="sm:col-span-1"
                                    v-if="
                                        formTransaksiSub.jenis === 'Lain lain'
                                    "
                                >
                                    <NestedSelect
                                        :required-indicator="false"
                                        :options="[
                                            'Kalung Bebek',
                                            'Cincin Perak',
                                        ]"
                                        v-model="formTransaksiSub.jenis_lain"
                                        @focusin="
                                            dropdownStates.jenisBarangLainState = true;
                                            formTransaksiSub.jenis_lain = '';
                                        "
                                        @focusout="
                                            dropdownStates.jenisBarangLainState = false
                                        "
                                        v-if="
                                            formTransaksiSub.jenis ===
                                                'Lain lain' &&
                                            !dropdownStates.jenisJaminanState
                                        "
                                    />
                                </div>
                                <div class="sm:col-span-1 pt-5">
                                    <h1
                                        v-if="
                                            formTransaksiSub.jenis ||
                                            formTransaksiSub.jenis_lain
                                        "
                                        class="tracking-wide block mb-2 text-xs font-bold text-gray-900 uppercase"
                                    >
                                        Kadar (karat) :
                                    </h1>
                                </div>
                                <div class="sm:col-span-1">
                                    <NestedSelect
                                        :required-indicator="false"
                                        :options="karataseOptions"
                                        v-model="formTransaksiSub.kadar"
                                        @focusin="
                                            dropdownStates.karataseState = true;
                                            formTransaksiSub.kadar = '';
                                        "
                                        @focusout="
                                            dropdownStates.karataseState = false
                                        "
                                        v-if="
                                            (formTransaksiSub.jenis ||
                                                formTransaksiSub.jenis_lain) &&
                                            !dropdownStates.jenisBarangLainState
                                        "
                                    />
                                </div>

                                <div class="sm:col-span-1 pt-5">
                                    <h1
                                        class="tracking-wide block mb-2 text-xs font-bold text-gray-900 uppercase"
                                    >
                                        Berat Kotor (gram) :
                                    </h1>
                                </div>
                                <div class="sm:col-span-1">
                                    <CustTextInput
                                        :required-indicator="false"
                                        id="berat_kotor"
                                        type="text"
                                        name="berat_kotor"
                                        v-model="formTransaksiSub.berat_kotor"
                                    />
                                    <span
                                        v-if="
                                            !formTransaksiSub.errors.berat_kotor
                                        "
                                        class="text-sm text-black"
                                        >* Input menggunakan titik (.) untuk
                                        desimal</span
                                    >
                                    <div
                                        v-if="
                                            formTransaksiSub.errors.berat_kotor
                                        "
                                    >
                                        <span class="text-sm text-red-600"
                                            >*{{
                                                formTransaksiSub.errors
                                                    .berat_kotor[0]
                                            }}</span
                                        >
                                    </div>
                                </div>
                                <div class="sm:col-span-1 pt-5">
                                    <h1
                                        class="tracking-wide block mb-2 text-xs font-bold text-gray-900 uppercase"
                                    >
                                        Berat Bersih (gram) :
                                    </h1>
                                </div>
                                <div class="sm:col-span-1 mb-3">
                                    <CustTextInput
                                        :required-indicator="false"
                                        id="berat_bersih"
                                        type="text"
                                        name="berat_bersih"
                                        v-model="formTransaksiSub.berat_bersih"
                                    />
                                    <span
                                        v-if="
                                            !formTransaksiSub.errors
                                                .berat_bersih
                                        "
                                        class="text-sm text-black"
                                        >* Input menggunakan titik (.) untuk
                                        desimal</span
                                    >
                                    <div
                                        v-if="
                                            formTransaksiSub.errors.berat_bersih
                                        "
                                    >
                                        <span class="text-sm text-red-600"
                                            >*{{
                                                formTransaksiSub.errors
                                                    .berat_bersih[0]
                                            }}</span
                                        >
                                    </div>
                                </div>
                                <div class="sm:col-span-2 my-3">
                                    <hr class="border-2 border-gray-500" />
                                </div>

                                <!-- Radio Question 2 -->
                                <div class="sm:col-span-1 pt-2">
                                    <h1
                                        class="tracking-wide block mb-2 text-xs font-bold text-gray-900 uppercase"
                                    >
                                        Apakah Barang Jaminan Bengkok?
                                    </h1>
                                </div>
                                <div class="sm:col-span-1">
                                    <input
                                        type="radio"
                                        value="true"
                                        v-model="isBengkok"
                                        @change="updateDetailBarang()"
                                    />
                                    Ya
                                    <input
                                        class="ml-5"
                                        type="radio"
                                        value="false"
                                        v-model="isBengkok"
                                        @change="updateDetailBarang()"
                                    />
                                    Tidak
                                </div>

                                <!-- Radio Question 3 -->
                                <div class="sm:col-span-1 pt-2">
                                    <h1
                                        class="tracking-wide block mb-2 text-xs font-bold text-gray-900 uppercase"
                                    >
                                        Apakah Barang Jaminan Patah?
                                    </h1>
                                </div>
                                <div class="sm:col-span-1">
                                    <input
                                        type="radio"
                                        value="true"
                                        v-model="isPatah"
                                        @change="updateDetailBarang()"
                                    />
                                    Ya
                                    <input
                                        class="ml-5"
                                        type="radio"
                                        value="false"
                                        v-model="isPatah"
                                        @change="updateDetailBarang()"
                                    />
                                    Tidak
                                </div>

                                <!-- Radio Question 4 -->
                                <div class="sm:col-span-1 pt-2">
                                    <h1
                                        class="tracking-wide block mb-2 text-xs font-bold text-gray-900 uppercase"
                                    >
                                        Apakah Mata Barang Jaminan Hilang?
                                    </h1>
                                </div>
                                <div class="sm:col-span-1">
                                    <input
                                        type="radio"
                                        value="true"
                                        v-model="isMataCincin"
                                        @change="updateDetailBarang()"
                                    />
                                    Ya
                                    <input
                                        class="ml-5"
                                        type="radio"
                                        value="false"
                                        v-model="isMataCincin"
                                        @change="updateDetailBarang()"
                                    />
                                    Tidak
                                </div>
                                <div class="sm:col-span-2">
                                    <TextAreaInput
                                        id="detail_barang_form"
                                        name="detail_barang_form"
                                        label="Kondisi Fisik"
                                        rows="5"
                                        :required-indicator="false"
                                        v-model="formTransaksiSub.detail_barang"
                                    />
                                    <span class="text-sm text-black"
                                        >* Jika ingin menambahkan detail lebih,
                                        boleh diketikan langsung</span
                                    >
                                </div>
                                <div class="sm:col-span-2 my-3">
                                    <hr class="border-2 border-gray-500" />
                                </div>
                                <div class="sm:col-span-2">
                                    <UploadImage
                                        label="Foto Barang Jaminan Sisi Pertama"
                                        @file-selected="handleFotoJaminan"
                                    />
                                    <span
                                        v-if="
                                            !formTransaksiSub.errors.foto_barang
                                        "
                                        class="text-sm text-black"
                                        >* Format: JPEG,JPG,PNG & MAKSIMAL
                                        2MB</span
                                    >
                                    <div
                                        v-if="
                                            formTransaksiSub.errors.foto_barang
                                        "
                                    >
                                        <span class="text-sm text-red-600"
                                            >*{{
                                                formTransaksiSub.errors
                                                    .foto_barang[0]
                                            }}</span
                                        >
                                    </div>
                                </div>
                                <div class="sm:col-span-2">
                                    <UploadImage
                                        label="Foto Barang Jaminan Sisi Kedua"
                                        @file-selected="handleFotoJaminan2"
                                    />
                                    <span
                                        v-if="
                                            !formTransaksiSub.errors
                                                .foto_barang2
                                        "
                                        class="text-sm text-black"
                                        >* Format: JPEG,JPG,PNG & MAKSIMAL
                                        2MB</span
                                    >
                                    <div
                                        v-if="
                                            formTransaksiSub.errors.foto_barang2
                                        "
                                    >
                                        <span class="text-sm text-red-600"
                                            >*{{
                                                formTransaksiSub.errors
                                                    .foto_barang2[0]
                                            }}</span
                                        >
                                    </div>
                                </div>
                            </div>
                            <div class="flex justify-end items-center my-10">
                                <div class="ml-4">
                                    <div class="text-black">
                                        <button
                                            @click="storeBarangJaminan()"
                                            class="mt-4 ml-2 bg-green-700 text-white py-1 px-4 rounded"
                                        >
                                            Simpan
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </template>
                        <template #content v-if="selectedTipeGadai === 'motor'">
                            <div class="mt-6 grid grid-cols-2 gap-4">
                                <div class="sm:col-span-1 pt-5">
                                    <h1
                                        class="tracking-wide block mb-2 text-xs font-bold text-gray-900 uppercase"
                                    >
                                        Jenis Barang Jaminan :
                                    </h1>
                                </div>
                                <div class="sm:col-span-1">
                                    <CustTextInput
                                        :required-indicator="false"
                                        id="jangka_waktu"
                                        type="text"
                                        name="jangka_waktu"
                                        v-model="jenisJaminan"
                                        :disabled="true"
                                    />
                                </div>
                                <div class="sm:col-span-1 pt-5">
                                    <h1
                                        class="tracking-wide block mb-2 text-xs font-bold text-gray-900 uppercase"
                                    >
                                        Jumlah :
                                    </h1>
                                </div>
                                <div class="sm:col-span-1">
                                    <CustTextInput
                                        :requiredIndicator="false"
                                        id="jangka_waktu"
                                        type="text"
                                        name="jangka_waktu"
                                        v-model="jumlahBarang"
                                        :disabled="true"
                                    />
                                </div>

                                <div class="sm:col-span-2">
                                    <button
                                        class="bg-sky-700 w-full text-white py-1 px-4 rounded"
                                        @click="modalHsp()"
                                    >
                                        Pilih Hsp Motor
                                    </button>
                                </div>

                                <div class="sm:col-span-1 pt-5">
                                    <h1
                                        class="tracking-wide block mb-2 text-xs font-bold text-gray-900 uppercase"
                                    >
                                        Merk :
                                    </h1>
                                </div>
                                <div class="sm:col-span-1">
                                    <CustTextInput
                                        :requiredIndicator="false"
                                        id="merk"
                                        type="text"
                                        name="merk"
                                        v-model="formJmnMotor.merk"
                                        :disabled="true"
                                    />
                                </div>

                                <div class="sm:col-span-1 pt-5">
                                    <h1
                                        class="tracking-wide block mb-2 text-xs font-bold text-gray-900 uppercase"
                                    >
                                        Tipe :
                                    </h1>
                                </div>
                                <div class="sm:col-span-1">
                                    <CustTextInput
                                        :requiredIndicator="false"
                                        id="tipe"
                                        type="text"
                                        name="tipe"
                                        v-model="formJmnMotor.tipe"
                                        :disabled="true"
                                    />
                                </div>
                                <div class="sm:col-span-1 pt-5">
                                    <h1
                                        class="tracking-wide block mb-2 text-xs font-bold text-gray-900 uppercase"
                                    >
                                        No Polisi :
                                    </h1>
                                </div>
                                <div class="sm:col-span-1">
                                    <CustTextInput
                                        :requiredIndicator="false"
                                        id="nopol"
                                        type="text"
                                        name="nopol"
                                        v-model="formJmnMotor.nopol"
                                        :capital="true"
                                    />
                                </div>
                                <div class="sm:col-span-1 pt-5">
                                    <h1
                                        class="tracking-wide block mb-2 text-xs font-bold text-gray-900 uppercase"
                                    >
                                        No Rangka :
                                    </h1>
                                </div>
                                <div class="sm:col-span-1">
                                    <CustTextInput
                                        :requiredIndicator="false"
                                        id="no_rangka"
                                        type="text"
                                        name="no_rangka"
                                        v-model="formJmnMotor.no_rangka"
                                        :capital="true"
                                    />
                                </div>
                                <div class="sm:col-span-1 pt-5">
                                    <h1
                                        class="tracking-wide block mb-2 text-xs font-bold text-gray-900 uppercase"
                                    >
                                        No Mesin :
                                    </h1>
                                </div>
                                <div class="sm:col-span-1">
                                    <CustTextInput
                                        :requiredIndicator="false"
                                        id="no_mesin"
                                        type="text"
                                        name="no_mesin"
                                        v-model="formJmnMotor.no_mesin"
                                        :capital="true"
                                    />
                                </div>
                                <div class="sm:col-span-1 pt-5">
                                    <h1
                                        class="tracking-wide block mb-2 text-xs font-bold text-gray-900 uppercase"
                                    >
                                        No Bpkb :
                                    </h1>
                                </div>
                                <div class="sm:col-span-1 mb-3">
                                    <CustTextInput
                                        :requiredIndicator="false"
                                        id="no_bpkb"
                                        type="text"
                                        name="no_bpkb"
                                        v-model="formJmnMotor.no_bpkb"
                                        :capital="true"
                                    />
                                </div>
                                <div class="sm:col-span-2">
                                    <TextAreaInput
                                        id="detail_barang_motor"
                                        name="detail_barang_motor"
                                        label="Kondisi Fisik"
                                        rows="5"
                                        :required-indicator="false"
                                        v-model="formJmnMotor.detail_barang"
                                    />
                                    <span class="text-sm text-black"
                                        >* Jika ingin menambahkan detail lebih,
                                        boleh diketikan langsung</span
                                    >
                                </div>
                                <div class="sm:col-span-2 my-3">
                                    <hr class="border-2 border-gray-500" />
                                </div>
                                <div class="sm:col-span-2">
                                    <UploadImage
                                        label="Foto Barang Jaminan Sisi Pertama"
                                        @file-selected="handleFotoJaminanMotor"
                                    />
                                    <span
                                        v-if="!formJmnMotor.errors.foto_barang"
                                        class="text-sm text-black"
                                        >* Format: JPEG,JPG,PNG & MAKSIMAL
                                        2MB</span
                                    >
                                    <div v-if="formJmnMotor.errors.foto_barang">
                                        <span class="text-sm text-red-600"
                                            >*{{
                                                formJmnMotor.errors
                                                    .foto_barang[0]
                                            }}</span
                                        >
                                    </div>
                                </div>
                                <div class="sm:col-span-2">
                                    <UploadImage
                                        label="Foto Barang Jaminan Sisi Kedua"
                                        @file-selected="handleFotoJaminanMotor2"
                                    />
                                    <span
                                        v-if="!formJmnMotor.errors.foto_barang2"
                                        class="text-sm text-black"
                                        >* Format: JPEG,JPG,PNG & MAKSIMAL
                                        2MB</span
                                    >
                                    <div
                                        v-if="formJmnMotor.errors.foto_barang2"
                                    >
                                        <span class="text-sm text-red-600"
                                            >*{{
                                                formJmnMotor.errors
                                                    .foto_barang2[0]
                                            }}</span
                                        >
                                    </div>
                                </div>
                            </div>
                            <div class="flex justify-end items-center my-10">
                                <div class="ml-4">
                                    <div class="text-black">
                                        <button
                                            @click="storeJaminanMotor()"
                                            class="mt-4 ml-2 bg-green-700 text-white py-1 px-4 rounded"
                                        >
                                            Simpan
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </template>
                        <template #content v-if="selectedTipeGadai === 'mobil'">
                            <div class="mt-6 grid grid-cols-2 gap-4">
                                <div class="sm:col-span-1 pt-5">
                                    <h1
                                        class="tracking-wide block mb-2 text-xs font-bold text-gray-900 uppercase"
                                    >
                                        Jenis Barang Jaminan :
                                    </h1>
                                </div>
                                <div class="sm:col-span-1">
                                    <CustTextInput
                                        :required-indicator="false"
                                        id="jangka_waktu"
                                        type="text"
                                        name="jangka_waktu"
                                        v-model="jenisJaminan"
                                        :disabled="true"
                                    />
                                </div>
                                <div class="sm:col-span-1 pt-5">
                                    <h1
                                        class="tracking-wide block mb-2 text-xs font-bold text-gray-900 uppercase"
                                    >
                                        Jumlah :
                                    </h1>
                                </div>
                                <div class="sm:col-span-1">
                                    <CustTextInput
                                        :requiredIndicator="false"
                                        id="jangka_waktu"
                                        type="text"
                                        name="jangka_waktu"
                                        v-model="jumlahBarang"
                                        :disabled="true"
                                    />
                                </div>

                                <div class="sm:col-span-2">
                                    <button
                                        class="bg-sky-700 w-full text-white py-1 px-4 rounded"
                                        @click="modalHsp()"
                                    >
                                        Pilih Hsp Mobil
                                    </button>
                                </div>

                                <div class="sm:col-span-1 pt-5">
                                    <h1
                                        class="tracking-wide block mb-2 text-xs font-bold text-gray-900 uppercase"
                                    >
                                        Merk :
                                    </h1>
                                </div>
                                <div class="sm:col-span-1">
                                    <CustTextInput
                                        :requiredIndicator="false"
                                        id="merk"
                                        type="text"
                                        name="merk"
                                        v-model="formJmnMobil.merk"
                                        :disabled="true"
                                    />
                                </div>

                                <div class="sm:col-span-1 pt-5">
                                    <h1
                                        class="tracking-wide block mb-2 text-xs font-bold text-gray-900 uppercase"
                                    >
                                        Tipe :
                                    </h1>
                                </div>
                                <div class="sm:col-span-1">
                                    <CustTextInput
                                        :requiredIndicator="false"
                                        id="tipe"
                                        type="text"
                                        name="tipe"
                                        v-model="formJmnMobil.tipe"
                                        :disabled="true"
                                    />
                                </div>
                                <div class="sm:col-span-1 pt-5">
                                    <h1
                                        class="tracking-wide block mb-2 text-xs font-bold text-gray-900 uppercase"
                                    >
                                        No Polisi :
                                    </h1>
                                </div>
                                <div class="sm:col-span-1">
                                    <CustTextInput
                                        :requiredIndicator="false"
                                        id="nopol"
                                        type="text"
                                        name="nopol"
                                        v-model="formJmnMobil.nopol"
                                        :capital="true"
                                    />
                                </div>
                                <div class="sm:col-span-1 pt-5">
                                    <h1
                                        class="tracking-wide block mb-2 text-xs font-bold text-gray-900 uppercase"
                                    >
                                        No Rangka :
                                    </h1>
                                </div>
                                <div class="sm:col-span-1">
                                    <CustTextInput
                                        :requiredIndicator="false"
                                        id="no_rangka"
                                        type="text"
                                        name="no_rangka"
                                        v-model="formJmnMobil.no_rangka"
                                        :capital="true"
                                    />
                                </div>
                                <div class="sm:col-span-1 pt-5">
                                    <h1
                                        class="tracking-wide block mb-2 text-xs font-bold text-gray-900 uppercase"
                                    >
                                        No Mesin :
                                    </h1>
                                </div>
                                <div class="sm:col-span-1">
                                    <CustTextInput
                                        :requiredIndicator="false"
                                        id="no_mesin"
                                        type="text"
                                        name="no_mesin"
                                        v-model="formJmnMobil.no_mesin"
                                        :capital="true"
                                    />
                                </div>
                                <div class="sm:col-span-1 pt-5">
                                    <h1
                                        class="tracking-wide block mb-2 text-xs font-bold text-gray-900 uppercase"
                                    >
                                        No Bpkb :
                                    </h1>
                                </div>
                                <div class="sm:col-span-1 mb-3">
                                    <CustTextInput
                                        :requiredIndicator="false"
                                        id="no_bpkb"
                                        type="text"
                                        name="no_bpkb"
                                        v-model="formJmnMobil.no_bpkb"
                                        :capital="true"
                                    />
                                </div>
                                <div class="sm:col-span-2">
                                    <TextAreaInput
                                        id="detail_barang_mobil"
                                        name="detail_barang_mobil"
                                        label="Kondisi Fisik"
                                        rows="5"
                                        :required-indicator="false"
                                        v-model="formJmnMobil.detail_barang"
                                    />
                                    <span class="text-sm text-black"
                                        >* Jika ingin menambahkan detail lebih,
                                        boleh diketikan langsung</span
                                    >
                                </div>
                                <div class="sm:col-span-2 my-3">
                                    <hr class="border-2 border-gray-500" />
                                </div>
                                <div class="sm:col-span-2">
                                    <UploadImage
                                        label="Foto Barang Jaminan Sisi Pertama"
                                        @file-selected="handleFotoJaminanMobil"
                                    />
                                    <span
                                        v-if="!formJmnMobil.errors.foto_barang"
                                        class="text-sm text-black"
                                        >* Format: JPEG,JPG,PNG & MAKSIMAL
                                        2MB</span
                                    >
                                    <div v-if="formJmnMobil.errors.foto_barang">
                                        <span class="text-sm text-red-600"
                                            >*{{
                                                formJmnMobil.errors
                                                    .foto_barang[0]
                                            }}</span
                                        >
                                    </div>
                                </div>
                                <div class="sm:col-span-2">
                                    <UploadImage
                                        label="Foto Barang Jaminan Sisi Kedua"
                                        @file-selected="handleFotoJaminanMobil2"
                                    />
                                    <span
                                        v-if="!formJmnMobil.errors.foto_barang2"
                                        class="text-sm text-black"
                                        >* Format: JPEG,JPG,PNG & MAKSIMAL
                                        2MB</span
                                    >
                                    <div
                                        v-if="formJmnMobil.errors.foto_barang2"
                                    >
                                        <span class="text-sm text-red-600"
                                            >*{{
                                                formJmnMobil.errors
                                                    .foto_barang2[0]
                                            }}</span
                                        >
                                    </div>
                                </div>
                            </div>
                            <div class="flex justify-end items-center my-10">
                                <div class="ml-4">
                                    <div class="text-black">
                                        <button
                                            @click="storeJaminanMobil()"
                                            class="mt-4 ml-2 bg-green-700 text-white py-1 px-4 rounded"
                                        >
                                            Simpan
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </template>
                    </Modal>

                    <!-- Modal Hsp Motor / Mobil -->
                    <Modal
                        :show="modal.hsp"
                        @close="[(modal.hsp = false)]"
                        maxWidth="5xl"
                        classHeader="bg-sky-700 text-white"
                    >
                        <template #title> Daftar Hsp</template>
                        <template #content>
                            <div class="my-6">
                                <div class="pr-2">
                                    <SearchInput
                                        label="Cari Data"
                                        v-model="searchIHsp"
                                        placeholder="Cari"
                                    ></SearchInput>
                                    <span
                                        class="text-xs font-bold text-gray-900"
                                    >
                                        *pencarian hanya berdasarkan Kode Stnk,
                                        Kode Kendaraan, Tahun, Nama Barang
                                    </span>
                                </div>
                                <div
                                    class="py-3 mt-14 rounded-lg bg-sky-700 shadow-lg"
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
                                            </tr>
                                        </thead>
                                        <tbody v-if="!hspLoading">
                                            <tr
                                                v-for="(d, index) in pgntHsp"
                                                :key="index"
                                                class="odd:bg-white even:bg-amber-400 cursor-pointer"
                                                @click="selectDataHsp(d)"
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
                                            v-if="
                                                !hspLoading &&
                                                !isHspEmpty &&
                                                searchHsp.length < 1
                                            "
                                            >Data Tidak Ditemukan</span
                                        >
                                    </div>
                                </div>
                                <div
                                    class="flex space-x-2 mt-2"
                                    v-if="!hspLoading"
                                >
                                    <button
                                        @click="prevPageHsp"
                                        v-show="crntPageHsp > 1"
                                    >
                                        <i
                                            ><img
                                            src="/gadaiemas/storage/icon/previous.png"
                                            title="Previous"</i
                                        >
                                    </button>
                                    <p class="p-1">
                                        Bagian {{ crntPageHsp }} dari
                                        {{ totalPagesHsp }}
                                    </p>
                                    <button
                                        @click="nextPageHsp"
                                        v-show="crntPageHsp < totalPagesHsp"
                                    >
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
                </div>
                <div
                    :class="showForm.uangJaminan ? 'relative' : 'hidden'"
                    class="bg-white p-4 mt-6 rounded-lg shadow-lg"
                >
                <div class="bg-white p-4 rounded-lg shadow-lg">
                        <h2 class="my-2 font-bold uppercase">
                            Informasi Transaksi
                        </h2>
                        <hr />
                        <div class="mt-6 grid grid-cols-2 gap-4">
                            <div class="sm:col-span-1 mr-5">
                                <CustTextInput
                                    id="tgl_transaksi"
                                    type="date"
                                    name="tgl_transaksi"
                                    label="Tanggal Transaksi Pinjaman"
                                    disabled="true"
                                    v-model="dateInput"
                                />
                            </div>
                            <div class="sm:col-span-1 mr-5">
                                <CustTextInput
                                    id="tgl_jtempo"
                                    type="date"
                                    name="tgl_jtempo"
                                    label="Jatuh Tempo Maksimal"
                                    disabled="true"
                                    v-model="formTransaksi.tgl_jtempo"
                                />
                            </div>
                            <div v-if="selectedTipeGadai === 'emas'" class="sm:col-span-1 mr-5">
                                <NestedSelect
                                    label="Jangka Waktu Maksimal (Hari)"
                                    :options="TenorOptions"
                                    v-model="maksTenor"
                                    @focusin="maksTenor = ''"
                                />
                            </div>
                            <div v-if="selectedTipeGadai === 'motor' || selectedTipeGadai === 'mobil'" class="sm:col-span-1 mr-5">
                                <NestedSelect
                                    label="Jangka Waktu Maksimal (Hari)"
                                    :options="['30', '60', '90']"
                                    v-model="maksTenor"
                                    @focusin="maksTenor = ''"
                                />
                            </div>
                            <div v-if="selectedTipeGadai === 'emas'" class="sm:col-span-1 mr-5">
                                <CustTextInput
                                    id="golongan"
                                    type="text"
                                    name="golongan"
                                    label="Golongan"
                                    disabled="true"
                                    v-model="formTransaksi.golongan"
                                />
                            </div>
                        </div>
                    </div>
                    <div class="bg-white p-4 mt-6 rounded-lg shadow-lg">
                        <h2 class="my-2 font-bold uppercase">Uang Pinjaman</h2>
                        <hr />
                        <div class="mt-6 grid grid-cols-2 gap-4">
                            <div class="sm:col-span-1 mr-5">
                                <CustTextInput
                                    id="total_taksiran"
                                    type="text"
                                    name="total_taksiran"
                                    label="Total Taksiran"
                                    disabled="true"
                                    v-model="formattedTaksiran"
                                />
                            </div>
                            <div class="sm:col-span-1 mr-5">
                                <CustTextInput
                                    id="by_admin"
                                    type="text"
                                    name="by_admin"
                                    label="Biaya Admin"
                                    disabled="true"
                                    v-model="formattedByAdmin"
                                />
                            </div>
                            <div class="sm:col-span-1 mr-5">
                                <CustTextInput
                                    id="uang_pinjaman_max"
                                    type="text"
                                    name="uang_pinjaman_max"
                                    label="Uang Pinjaman Maksimal"
                                    disabled="true"
                                    v-model="formattedMaksPinjaman"
                                />
                            </div>
                            <div v-if="selectedTipeGadai === 'emas'"
                                class="sm:col-span-1 flex items-center space-x-6"
                            >
                                <CustPercentInput
                                    id="sewa_modal"
                                    type="text"
                                    name="sewa_modal"
                                    label="Sewa Modal"
                                    :disabled="sewaModalDisabled"
                                    v-model="formTransaksi.sewa_modal"
                                    @focusout="checkSewaModal()"
                                />
                                <button
                                    v-if="sewaModalDisabled"
                                    @click="toggleDisabled()"
                                    class="mt-6 bg-sky-700 text-white py-1.5 px-4 rounded"
                                >
                                    Ubah
                                </button>
                                <button
                                    v-if="!sewaModalDisabled"
                                    @click="toggleDisabled()"
                                    class="mt-6 bg-sky-700 text-white py-1.5 px-4 rounded"
                                >
                                    Oke
                                </button>
                            </div>
                            <div class="sm:col-span-1">
                                <div class="flex items-center space-x-4">
                                    <div class="flex-1">
                                        <InputFormatIDR
                                            id="uang_pinjaman"
                                            type="text"
                                            name="uang_pinjaman"
                                            label="Uang Pinjaman"
                                            v-model="formattedPinjaman"
                                            @focusout="checkLoanInput()"
                                        />
                                        </div>
                                        <button
                                            @click="maskimalLoan()"
                                            class="mt-6 bg-sky-700 text-white py-1.5 px-4 rounded"
                                        >
                                            Maksimal
                                    </button>
                                </div>
                                <span class="text-sm text-black">
                                    * Tekan tombol maksimal jika ingin meminjam semua limit yang tersedia
                                </span>
                                <div v-if="formTransaksi.errors.pokok_pinjaman">
                                    <span class="text-sm text-red-600">
                                    *{{ formTransaksi.errors.pokok_pinjaman[0] }}
                                    </span>
                                </div>
                            </div>
                            <div class="sm:col-span-1 mr-5">
                                <NestedSelect
                                    label="Cara Pencairan"
                                    :options="['Transfer', 'Tunai']"
                                    v-model="formTransaksi.cara_pencairan"
                                    @focusin="formTransaksi.cara_pencairan = '';"
                                />
                            </div>
                            <div class="sm:col-span-1 mr-5">
                                <CustTextInput
                                    id="norek_nasabah"
                                    type="text"
                                    name="norek_nasabah"
                                    label="No Rekening"
                                    disabled="true"
                                    v-model="formTransaksi.no_rek_nasabah"
                                />
                                <span class="text-sm text-black"
                                    >* No rekening ini sesuai nasabah
                                    terkait</span
                                >
                            </div>
                        </div>
                    </div>
                    <div class="flex justify-end items-center mb-4">
                        <div class="ml-4">
                            {{ validFormUangPinjaman }} {{ sewaModalState }}
                            {{ pinjamanState }}
                            <div class="text-black">
                                <button
                                    class="mt-4 bg-red-700 text-white py-1 px-4 rounded"
                                    @click="
                                        [
                                            (showForm.barangJaminan =
                                                !showForm.barangJaminan),
                                            (showForm.uangJaminan =
                                                !showForm.uangJaminan),
                                        ]
                                    "
                                >
                                    Kembali
                                </button>
                                <button
                                    class="mt-4 ml-2 bg-red-700 text-white py-1 px-4 rounded"
                                    @click="
                                        showForm.uangJaminan = !showForm.uangJaminan;
                                        selectedTipeGadai === 'emas' ? deleteBarang() : 
                                        selectedTipeGadai === 'motor' ? deleteJaminanMotor() : 
                                        selectedTipeGadai === 'mobil' ? deleteJaminanMobil() : '';
                                        resetAll();
                                    "
                                >
                                    Batal
                                </button>
                                <button
                                    :disabled="
                                        validFormUangPinjaman ||
                                        sewaModalState ||
                                        pinjamanState
                                    "
                                    :class="{
                                        'cursor-not-allowed':
                                            validFormUangPinjaman ||
                                            sewaModalState ||
                                            pinjamanState,
                                    }"
                                    :title="
                                        validFormUangPinjaman ||
                                        sewaModalState ||
                                        pinjamanState
                                            ? 'Isi semua form'
                                            : 'Klik untuk menlanjutkan'
                                    "
                                    @click="store()"
                                    class="mt-4 ml-2 bg-green-700 text-white py-1 px-4 rounded"
                                >
                                    Lanjut
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </main>
        </template>
    </AdminLayout>
</template>