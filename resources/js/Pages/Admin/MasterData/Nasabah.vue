<script setup>
import { ref, onMounted, watch, computed, reactive } from "vue";
import AdminLayout from "@/Layouts/AdminLayout.vue";
import TitlePage from "@/Components/TitlePage.vue";
import InputError from "@/Components/InputError.vue";
import SearchInput from "@/Components/SearchInput.vue";
import CustTextInput from "@/Components/CustTextInput.vue";
import CustPhoneInput from "@/Components/CustPhoneInput.vue";
import CustDetail from "@/Components/CustDetail.vue";
import CustImageDetail from "@/Components/CustImageDetail.vue";
import RadioGroup from "@/Components/RadioGroup.vue";
import NestedSelect from "@/Components/NestedSelect.vue";
import TextAreaInput from "@/Components/TextAreaInput.vue";
import WebcamCapture from "@/Components/WebcamCapture.vue";
import UploadImage from "@/Components/UploadImage.vue";
import Modal from "@/Components/DialogModal.vue";
import { Head, useForm } from "@inertiajs/vue3";
import axios from "axios";
import Swal from "sweetalert2";

onMounted(() => {
    nasabahData(), provinsiData();
});

defineProps({
    errors: Object,
});

const dropdownStates = ref({
    provinsiKtpState: false,
    kabupatenKtpState: false,
    kecamatanKtpState: false,
    kelurahanKtpState: false,
    provinsiDmsState: false,
    kabupatenDmsState: false,
    kecamatanDmsState: false,
    kelurahanDmsState: false,
    provinsiKerabatState: false,
    kabupatenKerabatState: false,
    kecamatanKerabatState: false,
    kelurahanKerabatState: false,
});

const showForm = reactive({
    dataNasabah: false,
    dataKerabat: false,
    dataRekening: false,
});

const form = useForm({
    k_kons: "",
    nama: "",
    alamat_dms: "",
    provinsi_dms: "",
    kabupaten_dms: "",
    kecamatan_dms: "",
    kelurahan_dms: "",
    kodepos_dms: "",
    no_ktp: "",
    tempat_lahir: "",
    tgl_lahir: "",
    jenis_kelamin: "",
    cad1: "",
    cad2: "",
    alamat_ktp: "",
    provinsi_ktp: "",
    kabupaten_ktp: "",
    kecamatan_ktp: "",
    kelurahan_ktp: "",
    kodepos_ktp: "",
    no_tlp_rm: "",
    no_tlp_hp: "",
    nama_ibu: "",
    nama_kerabat: "",
    hub_kerabat: "",
    alamat_kerabat: "",
    provinsi_kerabat: "",
    kabupaten_kerabat: "",
    kecamatan_kerabat: "",
    kelurahan_kerabat: "",
    kodepos_kerabat: "",
    trans_ke: "",
    email: "",
    no_rekening: "",
    nama_rekening: "",
    nama_bank: "",
});

const formDtl = useForm({
    k_kons: "",
    nama: "",
    alamat_dms: "",
    provinsi_dms: "",
    kabupaten_dms: "",
    kecamatan_dms: "",
    kelurahan_dms: "",
    kodepos_dms: "",
    no_ktp: "",
    tempat_lahir: "",
    tgl_lahir: "",
    jenis_kelamin: "",
    cad1: "",
    cad2: "",
    alamat_ktp: "",
    provinsi_ktp: "",
    kabupaten_ktp: "",
    kecamatan_ktp: "",
    kelurahan_ktp: "",
    kodepos_ktp: "",
    no_tlp_rm: "",
    no_tlp_hp: "",
    nama_ibu: "",
    nama_kerabat: "",
    hub_kerabat: "",
    alamat_kerabat: "",
    provinsi_kerabat: "",
    kabupaten_kerabat: "",
    kecamatan_kerabat: "",
    kelurahan_kerabat: "",
    kodepos_kerabat: "",
    trans_ke: "",
    email: "",
    no_rekening: "",
    nama_rekening: "",
    nama_bank: "",
});

const modal = reactive({
    detailNas: false,
});

const modalDetailNas = (d) => {
    selectDataNas(d);
    modal.detailNas = true;
};

//selecData Nasabah
const selectDataNas = (d) => {
    formDtl.k_kons = d.K_Kons;
    formDtl.nama = d.Nama;
    formDtl.alamat_dms = d.Alamat_Domisili;
    formDtl.provinsi_dms = d.Propinsi_Domisili;
    formDtl.kabupaten_dms = d.kabupaten_Domisili;
    formDtl.kecamatan_dms = d.Kecamatan_Domisili;
    formDtl.kelurahan_dms = d.Kelurahan_Domisili;
    formDtl.kodepos_dms = d.Kodepos_Domisili;
    formDtl.no_ktp = d.No_ktp;
    formDtl.tempat_lahir = d.Tempat_Lahir;
    formDtl.tgl_lahir = formatDate(d.Tgl_Lahir);
    formDtl.jenis_kelamin = d.Jenis_Kelamin;
    formDtl.cad1 = d.cad1;
    formDtl.cad2 = d.cad2;
    formDtl.alamat_ktp = d.Alamat_ktp;
    formDtl.provinsi_ktp = d.Propinsi_ktp;
    formDtl.kabupaten_ktp = d.kabupaten_ktp;
    formDtl.kecamatan_ktp = d.Kecamatan_ktp;
    formDtl.kelurahan_ktp = d.Kelurahan_ktp;
    formDtl.kodepos_ktp = d.Kodepos_ktp;
    formDtl.no_tlp_hp = d.No_tlp_HP;
    formDtl.no_tlp_rm = d.No_tlp_rm;
    formDtl.nama_ibu = d.Nama_Ibu;
    formDtl.nama_kerabat = d.Nama_Kerabat;
    formDtl.hub_kerabat = d.Hub_Kerabat;
    formDtl.alamat_kerabat = d.Alamat_Kerabat;
    formDtl.provinsi_kerabat = d.Propinsi_Kerabat;
    formDtl.kabupaten_kerabat = d.kabupaten_Kerabat;
    formDtl.kecamatan_kerabat = d.Kecamatan_Kerabat;
    formDtl.kelurahan_kerabat = d.Kelurahan_Kerabat;
    formDtl.kodepos_kerabat = d.Kodepos_Kerabat;
    formDtl.trans_ke = d.Trans_ke;
    formDtl.email = d.email;
    formDtl.no_rekening = d.no_rekening;
    formDtl.nama_rekening = d.nama_rekening;
    formDtl.nama_bank = d.nama_bank;
};
//end

const provinsiKtpOptions = ref([]);
const kabupatenKtpOptions = ref([]);
const kecamatanKtpOptions = ref([]);
const kelurahanKtpOptions = ref([]);
const kodePosKtpOptions = ref([]);
const provinsiDmsOptions = ref([]);
const kabupatenDmsOptions = ref([]);
const kecamatanDmsOptions = ref([]);
const kelurahanDmsOptions = ref([]);
const kodePosDmsOptions = ref([]);
const provinsiKerabatOptions = ref([]);
const kabupatenKerabatOptions = ref([]);
const kecamatanKerabatOptions = ref([]);
const kelurahanKerabatOptions = ref([]);
const kodePosKerabatOptions = ref([]);

const provinsi = ref([]);
const provinsiData = async () => {
    await axios.get(route("getProvinsi")).then((res) => {
        const data = res.data;
        provinsi.value = data;
        updateProvinsiKtpOptions(),
            updateProvinsiDmsOptions(),
            updateProvinsiKerabatOptions();
    });
};

const updateProvinsiKtpOptions = () => {
    const provinsiSet = new Set(provinsi.value.map((item) => item.PROPINSI));
    provinsiKtpOptions.value = [...provinsiSet];
};

const updateKabupatenKtpOptions = () => {
    const kabupatenSet = new Set(
        provinsi.value
            .filter((item) => item.PROPINSI === form.provinsi_ktp)
            .map((item) => item.KABUPATEN)
    );
    kabupatenKtpOptions.value = [...kabupatenSet];
};

const updateKecamatanKtpOptions = () => {
    const kecamatanSet = new Set(
        provinsi.value
            .filter((item) => item.KABUPATEN === form.kabupaten_ktp)
            .map((item) => item.KECAMATAN)
    );
    kecamatanKtpOptions.value = [...kecamatanSet];
};

const updateKelurahanKtpOptions = () => {
    const kelurahanSet = new Set(
        provinsi.value
            .filter((item) => item.KECAMATAN === form.kecamatan_ktp)
            .map((item) => item.KELURAHAN)
    );
    kelurahanKtpOptions.value = [...kelurahanSet];
};

const updateKodePosKtpOptions = () => {
    const kodePosSet = new Set(
        provinsi.value
            .filter((item) => item.KELURAHAN === form.kelurahan_ktp)
            .map((item) => item.KODE_POS)
    );
    kodePosKtpOptions.value = [...kodePosSet];
};

const updateProvinsiDmsOptions = () => {
    const provinsiSet = new Set(provinsi.value.map((item) => item.PROPINSI));
    provinsiDmsOptions.value = [...provinsiSet];
};

const updateKabupatenDmsOptions = () => {
    const kabupatenSet = new Set(
        provinsi.value
            .filter((item) => item.PROPINSI === form.provinsi_dms)
            .map((item) => item.KABUPATEN)
    );
    kabupatenDmsOptions.value = [...kabupatenSet];
};

const updateKecamatanDmsOptions = () => {
    const kecamatanSet = new Set(
        provinsi.value
            .filter((item) => item.KABUPATEN === form.kabupaten_dms)
            .map((item) => item.KECAMATAN)
    );
    kecamatanDmsOptions.value = [...kecamatanSet];
};

const updateKelurahanDmsOptions = () => {
    const kelurahanSet = new Set(
        provinsi.value
            .filter((item) => item.KECAMATAN === form.kecamatan_dms)
            .map((item) => item.KELURAHAN)
    );
    kelurahanDmsOptions.value = [...kelurahanSet];
};

const updateKodePosDmsOptions = () => {
    const kodePosSet = new Set(
        provinsi.value
            .filter((item) => item.KELURAHAN === form.kelurahan_dms)
            .map((item) => item.KODE_POS)
    );
    kodePosDmsOptions.value = [...kodePosSet];
};

const updateProvinsiKerabatOptions = () => {
    const provinsiSet = new Set(provinsi.value.map((item) => item.PROPINSI));
    provinsiKerabatOptions.value = [...provinsiSet];
};

const updateKabupatenKerabatOptions = () => {
    const kabupatenSet = new Set(
        provinsi.value
            .filter((item) => item.PROPINSI === form.provinsi_kerabat)
            .map((item) => item.KABUPATEN)
    );
    kabupatenKerabatOptions.value = [...kabupatenSet];
};

const updateKecamatanKerabatOptions = () => {
    const kecamatanSet = new Set(
        provinsi.value
            .filter((item) => item.KABUPATEN === form.kabupaten_kerabat)
            .map((item) => item.KECAMATAN)
    );
    kecamatanKerabatOptions.value = [...kecamatanSet];
};

const updateKelurahanKerabatOptions = () => {
    const kelurahanSet = new Set(
        provinsi.value
            .filter((item) => item.KECAMATAN === form.kecamatan_kerabat)
            .map((item) => item.KELURAHAN)
    );
    kelurahanKerabatOptions.value = [...kelurahanSet];
};

const updateKodePosKerabatOptions = () => {
    const kodePosSet = new Set(
        provinsi.value
            .filter((item) => item.KELURAHAN === form.kelurahan_kerabat)
            .map((item) => item.KODE_POS)
    );
    kodePosKerabatOptions.value = [...kodePosSet];
};

watch(
    () => form.provinsi_ktp,
    () => {
        updateKabupatenKtpOptions();
    }
);

watch(
    () => form.kabupaten_ktp,
    () => {
        updateKecamatanKtpOptions();
    }
);

watch(
    () => form.kecamatan_ktp,
    () => {
        updateKelurahanKtpOptions();
    }
);

watch(
    () => form.kelurahan_ktp,
    () => {
        updateKodePosKtpOptions();
    }
);

watch(
    () => form.provinsi_dms,
    () => {
        updateKabupatenDmsOptions();
    }
);

watch(
    () => form.kabupaten_dms,
    () => {
        updateKecamatanDmsOptions();
    }
);

watch(
    () => form.kecamatan_dms,
    () => {
        updateKelurahanDmsOptions();
    }
);

watch(
    () => form.kelurahan_dms,
    () => {
        updateKodePosDmsOptions();
    }
);

watch(
    () => form.provinsi_kerabat,
    () => {
        updateKabupatenKerabatOptions();
    }
);

watch(
    () => form.kabupaten_kerabat,
    () => {
        updateKecamatanKerabatOptions();
    }
);

watch(
    () => form.kecamatan_kerabat,
    () => {
        updateKelurahanKerabatOptions();
    }
);

watch(
    () => form.kelurahan_kerabat,
    () => {
        updateKodePosKerabatOptions();
    }
);

//fetch data nasabah from db
const nasabah = ref([]);
const isNasabahEmpty = computed(() => nasabah.value.length === 0);
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

const formatDate = (date) => {
    const d = new Date(date);
    let month = "" + (d.getMonth() + 1);
    let day = "" + d.getDate();
    const year = d.getFullYear();

    if (month.length < 2) month = "0" + month;
    if (day.length < 2) day = "0" + day;

    return [year, month, day].join("-");
};

//store nasabah to db
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
            await axios.post(route("storeNasabah"), form, header);

            // Close the loading alert and show success message
            Swal.fire({
                title: "Berhasil",
                text: "Nasabah ditambahkan",
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
            form.reset(); // Reset the form
            nasabahData(); // Refresh the nasabah data
            showForm.dataNasabah = false; // Close the nasabah form
            showForm.dataKerabat = false; // Close the kerabat form
            showForm.dataRekening = false; // Close the rekening form
        } catch (error) {
            console.log("error", error);

            // Handle validation errors
            if (error.response && error.response.status === 422) {
                form.errors = error.response.data.errors || {};
            }

            // Show error message
            Swal.fire({
                title: "Terjadi kesalahan",
                html: "Periksa kembali inputannya.<br>Cek dari halaman awal.",
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

const handleImageCaptured = (file) => {
    form.cad2 = file;
};

const handleFotoKtp = (selectedFile) => {
    form.cad1 = selectedFile;
};

const handleFotoNasabah = (selectedFile) => {
    form.cad2 = selectedFile;
};

const validFormNasabah = ref(true);
const validFormKerabat = ref(true);
const validFormRekening = ref(true);
const formAlamatKtp = ref(false);

watch(
    [
        () => form.provinsi_ktp,
        () => form.kabupaten_ktp,
        () => form.kecamatan_ktp,
        () => form.kelurahan_ktp,
        () => form.kodepos_ktp,
        () => form.alamat_ktp,
    ],
    ([
        provinsi_ktp,
        kabupaten_ktp,
        kecamatan_ktp,
        kelurahan_ktp,
        kodepos_ktp,
        alamat_ktp,
    ]) => {
        formAlamatKtp.value =
            provinsi_ktp == "" ||
            kabupaten_ktp == "" ||
            kecamatan_ktp == "" ||
            kelurahan_ktp == "" ||
            kodepos_ktp == "" ||
            alamat_ktp == ""
                ? false
                : true;
    }
);

watch(
    [
        () => form.nama,
        () => form.no_ktp,
        () => form.tempat_lahir,
        () => form.tgl_lahir,
        () => form.jenis_kelamin,
        () => form.email,
        () => form.no_tlp_hp,
        () => form.no_tlp_rm,
        () => form.provinsi_ktp,
        () => form.kabupaten_ktp,
        () => form.kecamatan_ktp,
        () => form.kelurahan_ktp,
        () => form.kodepos_ktp,
        () => form.alamat_ktp,
        () => form.provinsi_dms,
        () => form.kabupaten_dms,
        () => form.kecamatan_dms,
        () => form.kelurahan_dms,
        () => form.kodepos_dms,
        () => form.alamat_dms,
    ],
    ([
        nama,
        no_ktp,
        tempat_lahir,
        tgl_lahir,
        jenis_kelamin,
        email,
        no_tlp_hp,
        no_tlp_rm,
        provinsi_ktp,
        kabupaten_ktp,
        kecamatan_ktp,
        kelurahan_ktp,
        kodepos_ktp,
        alamat_ktp,
        provinsi_dms,
        kabupaten_dms,
        kecamatan_dms,
        kelurahan_dms,
        kodepos_dms,
        alamat_dms,
    ]) => {
        validFormNasabah.value =
            nama == "" ||
            no_ktp == "" ||
            tempat_lahir == "" ||
            tgl_lahir == "" ||
            jenis_kelamin == "" ||
            email == "" ||
            no_tlp_hp == "" ||
            no_tlp_rm == "" ||
            provinsi_ktp == "" ||
            kabupaten_ktp == "" ||
            kecamatan_ktp == "" ||
            kelurahan_ktp == "" ||
            kodepos_ktp == "" ||
            alamat_ktp == "" ||
            provinsi_dms == "" ||
            kabupaten_dms == "" ||
            kecamatan_dms == "" ||
            kelurahan_dms == "" ||
            kodepos_dms == "" ||
            alamat_dms == ""
                ? true
                : false;
    }
);
watch(
    [
        () => form.nama_ibu,
        () => form.nama_kerabat,
        () => form.hub_kerabat,
        () => form.provinsi_kerabat,
        () => form.kabupaten_kerabat,
        () => form.kecamatan_kerabat,
        () => form.kelurahan_kerabat,
        () => form.kodepos_kerabat,
        () => form.alamat_kerabat,
    ],
    ([
        nama_ibu,
        nama_kerabat,
        hub_kerabat,
        provinsi_kerabat,
        kabupaten_kerabat,
        kecamatan_kerabat,
        kelurahan_kerabat,
        kodepos_kerabat,
        alamat_kerabat,
    ]) => {
        validFormKerabat.value =
            nama_ibu == "" ||
            nama_kerabat == "" ||
            hub_kerabat == "" ||
            provinsi_kerabat == "" ||
            kabupaten_kerabat == "" ||
            kecamatan_kerabat == "" ||
            kelurahan_kerabat == "" ||
            kodepos_kerabat == "" ||
            alamat_kerabat == ""
                ? true
                : false;
    }
);

const errors = ref({
    no_ktp: '',
    no_tlp_hp: '',
    no_tlp_rm: ''
});

const noHpChecked = ref(false);
const checkNoHp = () => {
    if (form.no_tlp_hp.length < 10) {
        errors.value.no_tlp_hp = 'Nomor HP tidak boleh kurang dari 11 angka.';
        noHpChecked.value = false;
    } else if (form.no_tlp_hp.length > 12) {
        errors.value.no_tlp_hp = 'Nomor HP tidak boleh lebih dari 13 angka.';
        noHpChecked.value = false;
    } else {
        errors.value.no_tlp_hp = '';
        noHpChecked.value = true;
    }
};

const noTlpRmhChecked = ref(false);
const checkNoTlpRmh = () => {
    if (form.no_tlp_rm.length < 7) {
        errors.value.no_tlp_rm = 'Nomor Telp Rumah tidak boleh kurang dari 7 angka.';
        noTlpRmhChecked.value = false;
    } else if (form.no_tlp_rm.length > 8) {
        errors.value.no_tlp_rm = 'Nomor Telp Rumah tidak boleh lebih dari 8 angka.';
        noTlpRmhChecked.value = false;
    } else {
        errors.value.no_tlp_rm = '';
        noTlpRmhChecked.value = true;
    }
}

const noKtpChecked = ref(false);
const checkNoKtp = () => {
    if (form.no_ktp.length !== 16) {
        errors.value.no_ktp = 'Nomor KTP harus terdiri dari 16 angka.';
        noKtpChecked.value = false;
    } else {
        errors.value.no_ktp = '';
        noKtpChecked.value = true;
    }
};

const allChecksPassed = computed(() => {
    return noHpChecked.value && noTlpRmhChecked.value && noKtpChecked.value;
})

const noRekChecked = ref(false);
const checkNoRek = () => {
    if (form.no_rekening.length < 10) {
        Swal.fire({
            icon: "warning",
            title: "Invalid Nomor Rekening",
            text: "Nomor Rekening tidak boleh kurang dari 10 angka.",
        });
        noRekChecked.value = false;
    } else if (form.no_rekening.length > 16) {
        Swal.fire({
            icon: "warning",
            title: "Invalid Nomor Rekening",
            text: "Nomor Rekening tidak boleh lebih dari 16 angka.",
        });
        noRekChecked.value = false;
    } else {
        noRekChecked.value = true;
    }
};

watch(
    [() => form.no_rekening, () => form.nama_rekening, () => form.nama_bank],
    ([no_rekening, nama_rekening, nama_bank]) => {
        validFormRekening.value =
            no_rekening == "" || nama_rekening == "" || nama_bank == ""
                ? true
                : false;
    }
);

const selectedSame = ref("");

const sameAction = () => {
    if (selectedSame.value === "Ya") {
        if (
            form.provinsi_ktp !== "" &&
            form.kabupaten_ktp !== "" &&
            form.kecamatan_ktp !== "" &&
            form.kelurahan_ktp !== "" &&
            form.kodepos_ktp !== ""
        ) {
            form.provinsi_dms = form.provinsi_ktp;
            form.kabupaten_dms = form.kabupaten_ktp;
            form.kecamatan_dms = form.kecamatan_ktp;
            form.kelurahan_dms = form.kelurahan_ktp;
            form.kodepos_dms = form.kodepos_ktp;
            form.alamat_dms = form.alamat_ktp;
        } else {
            Swal.fire({
                icon: "warning",
                title: "Invalid",
                text: "Isi terlebih dahulu alamat KTP.",
            });
        }
    } else if (selectedSame.value === "Tidak" || selectedSame.value === "") {
        form.provinsi_dms = "";
        form.kabupaten_dms = "";
        form.kecamatan_dms = "";
        form.kelurahan_dms = "";
        form.kodepos_dms = "";
        form.alamat_dms = "";
    }
};

watch(selectedSame, sameAction);
</script>

<template>
    <Head title="Nasabah" />
    <AdminLayout>
        <template #titlePage>
            <TitlePage value="Data Nasabah" />
        </template>
        <template #content>
            <main class="p-6 bg-gray-100 flex-grow overflow-y-auto">
                <!-- {{ showForm.dataNasabah }}
                {{ showForm.dataKerabat }}
                {{ showForm.dataRekening }} -->
                <span
                    class="font-bold uppercase"
                    :class="
                        showForm.dataNasabah ||
                        showForm.dataKerabat ||
                        showForm.dataRekening
                            ? 'relative'
                            : 'hidden'
                    "
                >
                    Form Data Nasabah
                    <span
                        href=""
                        class="font-bold uppercase"
                        :class="
                            showForm.dataKerabat || showForm.dataRekening
                                ? 'relative'
                                : 'hidden'
                        "
                    >
                        > Data Kerabat
                        <span
                            href=""
                            class="font-bold uppercase"
                            :class="
                                showForm.dataRekening ? 'relative' : 'hidden'
                            "
                        >
                            > Data Rekening
                        </span>
                    </span>
                </span>
                <div
                    class="mt-6"
                    id="tableNasabah"
                    :class="
                        showForm.dataNasabah ||
                        showForm.dataKerabat ||
                        showForm.dataRekening
                            ? 'hidden'
                            : 'relative'
                    "
                >
                    <div class="bg-white p-4 rounded-lg shadow">
                        <div class="flex justify-between items-center mb-4">
                            <!-- <div class="sm:col-2">
                                <SelectSearchCountry
                                    label="Status Rekening"
                                ></SelectSearchCountry>
                            </div> -->
                            <div class="pr-2-">
                                <SearchInput
                                    label="Cari Data"
                                    v-model="searchINas"
                                    placeholder="Cari"
                                ></SearchInput>
                            </div>
                            <div class="ml-4">
                                <div class="text-black">
                                    <button
                                        class="mt-4 ml-2 bg-sky-700 text-white py-1 px-4 rounded"
                                        @click="
                                            showForm.dataNasabah =
                                                !showForm.dataNasabah
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
                                        <th
                                            scope="col"
                                            class="text-center text-md font-sans uppercase pb-3 text-white"
                                        >
                                            detail
                                        </th>
                                    </tr>
                                </thead>
                                <tbody v-if="!nasabahLoading">
                                    <tr
                                        v-for="(d, index) in pgntNasabah"
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
                                            {{ d.K_Kons }}
                                        </td>
                                        <td
                                            class="whitespace-nowrap text-sm text-center text-black py-2"
                                        >
                                            {{ d.No_ktp }}
                                        </td>
                                        <td
                                            class="whitespace-nowrap text-sm text-center text-black py-2"
                                        >
                                            {{ d.Nama }}
                                        </td>
                                        <td
                                            class="whitespace-nowrap text-sm text-center text-black py-2"
                                        >
                                            {{ d.No_tlp_HP }}
                                        </td>
                                        <td
                                            class="whitespace-nowrap text-sm text-center text-black py-2"
                                        >
                                            {{ d.email }}
                                        </td>
                                        <td
                                            class="whitespace-nowrap text-sm text-center text-black py-2"
                                        >
                                            <a @click="modalDetailNas(d)"
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
                                    v-if="nasabahLoading"
                                    >Sedang Memuat...</span
                                >
                            </div>
                            <div class="w-full text-center mt-2">
                                <span
                                    class="text-center text-xl text-white"
                                    v-if="!nasabahLoading && isNasabahEmpty"
                                    >Tidak Ada Data</span
                                >
                            </div>
                            <div class="w-full text-center mt-2">
                                <span
                                    class="text-center text-xl text-white"
                                    v-if="
                                        !nasabahLoading &&
                                        !isNasabahEmpty &&
                                        searchNasabah.length < 1
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
                            :show="modal.detailNas"
                            @close="[(modal.detailNas = false)]"
                            maxWidth="6xl"
                            classHeader="bg-sky-700 text-white"
                        >
                            <template #title> Detail Nasabah </template>
                            <template #content>
                                <div class="mt-4">
                                    <h3 class="text-xl font-bold uppercase">
                                        Data Diri
                                    </h3>
                                </div>
                                <div class="mt-6 grid grid-cols-3 gap-6">
                                    <div class="sm:col-span-1">
                                        <CustDetail
                                            id="no_kons_dtl"
                                            name="no_kons_dtl"
                                            label="Kode Nasabah"
                                            :modelValue="formDtl.k_kons"
                                        />
                                    </div>
                                    <div class="sm:col-span-1">
                                        <CustDetail
                                            id="nama_dtl"
                                            name="nama_dtl"
                                            label="Nama"
                                            :modelValue="formDtl.nama"
                                        />
                                    </div>
                                    <div class="sm:col-span-1">
                                        <CustDetail
                                            id="no_ktp_dtl"
                                            name="no_ktp_dtl"
                                            label="No Ktp"
                                            :modelValue="formDtl.no_ktp"
                                        />
                                    </div>
                                    <div class="sm:col-span-1">
                                        <CustDetail
                                            id="tempat_lahir_dtl"
                                            name="tempat_lahir_dtl"
                                            label="Tempat Lahir"
                                            :modelValue="formDtl.tempat_lahir"
                                        />
                                    </div>
                                    <div class="sm:col-span-1">
                                        <CustDetail
                                            id="tgl_lahir_dtl"
                                            name="tgl_lahir_dtl"
                                            label="Tanggal Lahir"
                                            :modelValue="formDtl.tgl_lahir"
                                        />
                                    </div>
                                    <div class="sm:col-span-1">
                                        <CustDetail
                                            id="jenis_kelamin_dtl"
                                            name="jenis_kelamin_dtl"
                                            label="Jenis Kelamin"
                                            :modelValue="formDtl.jenis_kelamin"
                                        />
                                    </div>
                                    <div class="sm:col-span-1">
                                        <CustDetail
                                            id="email_dtl"
                                            name="email_dtl"
                                            label="Email"
                                            :modelValue="formDtl.email"
                                        />
                                    </div>
                                    <div class="sm:col-span-1">
                                        <CustDetail
                                            id="nama_ibu_dtl"
                                            name="nama_ibu_dtl"
                                            label="Nama Ibu"
                                            :modelValue="formDtl.nama_ibu"
                                        />
                                    </div>
                                    <div class="sm:col-span-1">
                                        <CustDetail
                                            id="no_hp_dtl"
                                            name="no_hp_dtl"
                                            label="Nomor Handphone"
                                            :modelValue="formDtl.no_tlp_hp"
                                        />
                                    </div>
                                    <div class="sm:col-span-1">
                                        <CustDetail
                                            id="no_rm_dtl"
                                            name="no_rm_dtl"
                                            label="No Tlp Rumah"
                                            :modelValue="formDtl.no_tlp_rm"
                                        />
                                    </div>
                                    <div class="my-1 col-span-3">
                                        <hr class="border-2 border-gray-500" />
                                    </div>
                                    <div class="col-span-3">
                                        <h3 class="text-xl font-bold uppercase">
                                            Alamat Ktp
                                        </h3>
                                    </div>
                                    <div class="sm:col-span-1">
                                        <CustDetail
                                            id="provinsi_ktp_dtl"
                                            name="provinsi_ktp_dtl"
                                            label="Provinsi"
                                            :modelValue="formDtl.provinsi_ktp"
                                        />
                                    </div>
                                    <div class="sm:col-span-1">
                                        <CustDetail
                                            id="kabupaten_ktp_dtl"
                                            name="kabupaten_ktp_dtl"
                                            label="Kabupaten"
                                            :modelValue="formDtl.kabupaten_ktp"
                                        />
                                    </div>
                                    <div class="sm:col-span-1">
                                        <CustDetail
                                            id="kecamatan_ktp_dtl"
                                            name="kecamatan_ktp_dtl"
                                            label="Kecamatan"
                                            :modelValue="formDtl.kecamatan_ktp"
                                        />
                                    </div>
                                    <div class="sm:col-span-1">
                                        <CustDetail
                                            id="kelurahan_ktp_dtl"
                                            name="kelurahan_ktp_dtl"
                                            label="Kelurahan"
                                            :modelValue="formDtl.kelurahan_ktp"
                                        />
                                    </div>
                                    <div class="sm:col-span-1">
                                        <CustDetail
                                            id="kodepos_ktp_dtl"
                                            name="kodepos_ktp_dtl"
                                            label="Kodepos"
                                            :modelValue="formDtl.kodepos_ktp"
                                        />
                                    </div>
                                    <div class="sm:col-span-3">
                                        <CustDetail
                                            id="alamat_ktp_dtl"
                                            name="alamat_ktp_dtl"
                                            label="Alamat Lengkap"
                                            :modelValue="formDtl.alamat_ktp"
                                        />
                                    </div>
                                    <div class="my-1 col-span-3">
                                        <hr class="border-2 border-gray-500" />
                                    </div>
                                    <div class="col-span-3">
                                        <h3 class="text-xl font-bold uppercase">
                                            Alamat Domisili
                                        </h3>
                                    </div>
                                    <div class="sm:col-span-1">
                                        <CustDetail
                                            id="provinsi_dms_dtl"
                                            name="provinsi_dms_dtl"
                                            label="Provinsi"
                                            :modelValue="formDtl.provinsi_dms"
                                        />
                                    </div>
                                    <div class="sm:col-span-1">
                                        <CustDetail
                                            id="kabupaten_dms_dtl"
                                            name="kabupaten_dms_dtl"
                                            label="Kabupaten"
                                            :modelValue="formDtl.kabupaten_dms"
                                        />
                                    </div>
                                    <div class="sm:col-span-1">
                                        <CustDetail
                                            id="kecamatan_dms_dtl"
                                            name="kecamatan_dms_dtl"
                                            label="Kecamatan"
                                            :modelValue="formDtl.kecamatan_dms"
                                        />
                                    </div>
                                    <div class="sm:col-span-1">
                                        <CustDetail
                                            id="kelurahan_dms_dtl"
                                            name="kelurahan_dms_dtl"
                                            label="Kelurahan"
                                            :modelValue="formDtl.kelurahan_dms"
                                        />
                                    </div>
                                    <div class="sm:col-span-1">
                                        <CustDetail
                                            id="kodepos_dms_dtl"
                                            name="kodepos_dms_dtl"
                                            label="Kodepos"
                                            :modelValue="formDtl.kodepos_dms"
                                        />
                                    </div>
                                    <div class="sm:col-span-3">
                                        <CustDetail
                                            id="alamat_dms_dtl"
                                            name="alamat_dms_dtl"
                                            label="Alamat Lengkap"
                                            :modelValue="formDtl.alamat_dms"
                                        />
                                    </div>
                                    <div class="my-1 col-span-3">
                                        <hr class="border-2 border-gray-500" />
                                    </div>
                                    <div class="col-span-3">
                                        <h3 class="text-xl font-bold uppercase">
                                            Data Kerabat
                                        </h3>
                                    </div>
                                    <div class="sm:col-span-1">
                                        <CustDetail
                                            id="nama_kerabat_dtl"
                                            name="nama_kerabat_dtl"
                                            label="Nama Kerabat"
                                            :modelValue="formDtl.nama_kerabat"
                                        />
                                    </div>
                                    <div class="sm:col-span-1">
                                        <CustDetail
                                            id="hub_kerabat_dtl"
                                            name="hub_kerabat_dtl"
                                            label="Hubungan Kerabat"
                                            :modelValue="formDtl.hub_kerabat"
                                        />
                                    </div>
                                    <div class="sm:col-span-1">
                                        <CustDetail
                                            id="provinsi_kerabat_dtl"
                                            name="provinsi_kerabat_dtl"
                                            label="Provinsi"
                                            :modelValue="
                                                formDtl.provinsi_kerabat
                                            "
                                        />
                                    </div>
                                    <div class="sm:col-span-1">
                                        <CustDetail
                                            id="kabupaten_kerabat_dtl"
                                            name="kabupaten_kerabat_dtl"
                                            label="Kabupaten"
                                            :modelValue="
                                                formDtl.kabupaten_kerabat
                                            "
                                        />
                                    </div>
                                    <div class="sm:col-span-1">
                                        <CustDetail
                                            id="kecamatan_kerabat_dtl"
                                            name="kecamatan_kerabat_dtl"
                                            label="Kecamatan"
                                            :modelValue="
                                                formDtl.kecamatan_kerabat
                                            "
                                        />
                                    </div>
                                    <div class="sm:col-span-1">
                                        <CustDetail
                                            id="kelurahan_kerabat_dtl"
                                            name="kelurahan_kerabat_dtl"
                                            label="Kelurahan"
                                            :modelValue="
                                                formDtl.kelurahan_kerabat
                                            "
                                        />
                                    </div>
                                    <div class="sm:col-span-1">
                                        <CustDetail
                                            id="kodepos_kerabat_dtl"
                                            name="kodepos_kerabat_dtl"
                                            label="Kodepos"
                                            :modelValue="
                                                formDtl.kodepos_kerabat
                                            "
                                        />
                                    </div>
                                    <div class="sm:col-span-3">
                                        <CustDetail
                                            id="alamat_kerabat_dtl"
                                            name="alamat_kerabat_dtl"
                                            label="Alamat Lengkap"
                                            :modelValue="formDtl.alamat_kerabat"
                                        />
                                    </div>
                                    <div class="my-1 col-span-3">
                                        <hr class="border-2 border-gray-500" />
                                    </div>
                                    <div class="col-span-3">
                                        <h3 class="text-xl font-bold uppercase">
                                            Data Rekening
                                        </h3>
                                    </div>
                                    <div class="sm:col-span-1">
                                        <CustDetail
                                            id="no_rekening_dtl"
                                            name="no_rekening_dtl"
                                            label="No Rekening"
                                            :modelValue="formDtl.no_rekening"
                                        />
                                    </div>
                                    <div class="sm:col-span-1">
                                        <CustDetail
                                            id="nama_rekening_dtl"
                                            name="nama_rekening_dtl"
                                            label="Nama Rekening"
                                            :modelValue="formDtl.nama_rekening"
                                        />
                                    </div>
                                    <div class="sm:col-span-1">
                                        <CustDetail
                                            id="nama_bank_dtl"
                                            name="nama_bank_dtl"
                                            label="Nama Bank"
                                            :modelValue="formDtl.nama_bank"
                                        />
                                    </div>
                                    <div class="my-1 col-span-3">
                                        <hr class="border-2 border-gray-500" />
                                    </div>
                                    <div class="col-span-3">
                                        <h3 class="text-xl font-bold uppercase">
                                            Foto Ktp
                                        </h3>
                                    </div>
                                    <div class="sm:col-span-3">
                                        <CustImageDetail
                                            folder="KtpPhoto"
                                            :image="formDtl.cad1"
                                            :requiredIndicator="false"
                                        />
                                    </div>
                                    <div class="my-1 col-span-3">
                                        <hr class="border-2 border-gray-500" />
                                    </div>
                                    <div class="col-span-3">
                                        <h3 class="text-xl font-bold uppercase">
                                            Foto Diri
                                        </h3>
                                    </div>
                                    <div class="sm:col-span-3">
                                        <CustImageDetail
                                            folder="NasabahPhoto"
                                            :image="formDtl.cad2"
                                            :requiredIndicator="false"
                                        />
                                    </div>
                                </div>
                                <div
                                    class="flex justify-end items-center my-10"
                                >
                                    <div class="ml-4">
                                        <div class="text-black">
                                            <button
                                                @click="modal.detailNas = false"
                                                class="mt-4 ml-2 bg-yellow-700 text-white py-1 px-4 rounded"
                                            >
                                                Tutup
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </template>
                        </Modal>
                    </div>
                </div>
                <div
                    :class="showForm.dataNasabah ? 'relative' : 'hidden'"
                    class="bg-white p-4 mt-6 rounded-lg shadow-lg"
                    id="inputData"
                >
                    <div class="bg-white p-4 rounded-lg shadow-lg">
                        <h2 class="my-2 font-bold uppercase">Data Diri</h2>
                        <hr />
                        <div class="mt-6 grid grid-cols-2 gap-4">
                            <div class="sm:col-span-1 mr-5">
                                <CustTextInput
                                    id="nama_nasabah"
                                    type="text"
                                    name="nama_nasabah"
                                    label="Nama Nasabah"
                                    v-model="form.nama"
                                />
                                <InputError class="mt-2" />
                            </div>
                            <div class="sm:col-span-1 mr-5">
                                <CustTextInput
                                    id="no_ktp"
                                    type="text"
                                    name="no_ktp"
                                    @input="checkNoKtp()"
                                    label="No Ktp"
                                    v-model="form.no_ktp"
                                    :onlyNum="true"
                                />
                                <span v-if="errors.no_ktp" class="text-sm text-red-600">{{ errors.no_ktp }}</span>
                                <div v-if="form.errors.no_ktp">
                                    <span class="text-sm text-red-600"
                                        >*{{ form.errors.no_ktp[0] }}</span
                                    >
                                </div>
                            </div>
                            <div class="sm:col-span-1 mr-5">
                                <CustTextInput
                                    id="tempat_lahir"
                                    type="text"
                                    name="tempat_lahir"
                                    label="Tempat Lahir"
                                    v-model="form.tempat_lahir"
                                />
                                <InputError class="mt-2" />
                            </div>
                            <div class="sm:col-span-1 mr-5">
                                <CustTextInput
                                    id="tgl_lahir"
                                    type="date"
                                    name="tgl_lahir"
                                    label="Tanggal Lahir"
                                    v-model="form.tgl_lahir"
                                />
                                <InputError class="mt-2" />
                            </div>
                            <div class="sm:col-span-1 mr-5">
                                <p
                                    class="tracking-wide block mb-6 text-xs font-bold text-gray-900 uppercase"
                                >
                                    Jenis Kelamin
                                </p>
                                <div class="flex space-x-6">
                                    <RadioGroup
                                        id="jenis_kelamin"
                                        label="Laki - laki"
                                        name="jenis_kelamin"
                                        value="L"
                                        v-model="form.jenis_kelamin"
                                    />
                                    <RadioGroup
                                        id="jenis_kelamin"
                                        label="Perempuan"
                                        name="jenis_kelamin"
                                        value="P"
                                        v-model="form.jenis_kelamin"
                                    />
                                </div>
                            </div>
                            <div class="sm:col-span-1 mr-5">
                                <CustTextInput
                                    id="email"
                                    type="email"
                                    name="email"
                                    label="Email"
                                    v-model="form.email"
                                />
                                <InputError class="mt-2" />
                            </div>
                            <div class="sm:col-span-1 mr-5">
                                <CustTextInput
                                    id="nama_ibu"
                                    type="text"
                                    name="nama_ibu"
                                    label="Nama Ibu Kandung"
                                    v-model="form.nama_ibu"
                                />
                                <InputError class="mt-2" />
                            </div>
                            <div class="sm:col-span-1 mr-5">
                                <CustPhoneInput
                                    id="no_hp"
                                    type="text"
                                    name="no_hp"
                                    label="No Handphone"
                                    @input="checkNoHp()"
                                    v-model="form.no_tlp_hp"
                                />
                                <span class="text-sm text-black"
                                    >* Input tanpa angka 0 didepan</span
                                >
                                <br>
                                <span v-if="errors.no_tlp_hp" class="text-sm text-red-600">{{ errors.no_tlp_hp }}</span>
                                <div v-if="form.errors.no_tlp_hp">
                                    <span class="text-sm text-red-600"
                                        >*{{ form.errors.no_tlp_hp[0] }}</span
                                    >
                                </div>
                            </div>
                            <div class="sm:col-span-1 mr-5">
                                <CustPhoneInput
                                    id="no_tlp_rmh"
                                    type="text"
                                    name="no_tlp_rmh"
                                    label="No Telp Rumah"
                                    @input="checkNoTlpRmh()"
                                    v-model="form.no_tlp_rm"
                                />
                                <span class="text-sm text-black"
                                    >* Input tanpa angka 0 didepan</span
                                >
                                <br>
                                <span v-if="errors.no_tlp_rm" class="text-sm text-red-600">{{ errors.no_tlp_rm }}</span>
                                <div v-if="form.errors.no_tlp_rm">
                                    <span class="text-sm text-red-600"
                                        >*{{ form.errors.no_tlp_rm[0] }}</span
                                    >
                                </div>
                            </div>
                        </div>
                    </div>
                    <div
                        class="bg-white p-4 mt-6 rounded-lg shadow-lg"
                        :class="allChecksPassed ? 'relative' : 'hidden'"
                    >
                        <h2 class="my-2 font-bold uppercase">Foto</h2>
                        <div class="mt-6 grid grid-cols-2 gap-4">
                            <div class="sm:col-span-1 mr-5">
                                <UploadImage
                                    label="Foto KTP"
                                    @file-selected="handleFotoKtp"
                                />
                                <span
                                    v-if="!form.errors.cad1"
                                    class="text-sm text-black"
                                    >* Format: JPEG,JPG,PNG & MAKSIMAL 2MB</span
                                >
                                <div v-if="form.errors.cad1">
                                    <span class="text-sm text-red-600"
                                        >*{{ form.errors.cad1[0] }}</span
                                    >
                                </div>
                            </div>
                            <div class="sm:col-span-1 mr-5">
                                <UploadImage
                                    label="Foto Nasabah"
                                    @file-selected="handleFotoNasabah"
                                />
                                <span
                                    v-if="!form.errors.cad2"
                                    class="text-sm text-black"
                                    >* Format: JPEG,JPG,PNG & MAKSIMAL 2MB</span
                                >
                                <div v-if="form.errors.cad2">
                                    <span class="text-sm text-red-600"
                                        >*{{ form.errors.cad2[0] }}</span
                                    >
                                </div>
                            </div>
                            <!-- <div class="sm:col-span-1 mr-5">
                                <WebcamCapture
                                    @imageCaptured="handleImageCaptured"
                                    label="Foto Nasabah"
                                    :nameAuthor="form.nama"
                                />
                            </div> -->
                        </div>
                    </div>
                    <div
                        class="bg-white p-4 mt-6 rounded-lg shadow-lg"
                        :class="allChecksPassed ? 'relative' : 'hidden'"
                    >
                        <h2 class="my-2 font-bold uppercase">alamat ktp</h2>
                        <hr />
                        <div class="mt-6 grid grid-cols-2 gap-4">
                            <div class="sm:col-span-1 mr-5">
                                <NestedSelect
                                    label="Provinsi"
                                    :options="provinsiKtpOptions"
                                    v-model="form.provinsi_ktp"
                                    @focusin="
                                        dropdownStates.provinsiKtpState = true;
                                        form.provinsi_ktp = '';
                                        form.kabupaten_ktp = '';
                                        form.kecamatan_ktp = '';
                                        form.kelurahan_ktp = '';
                                        form.kodepos_ktp = '';
                                    "
                                    @focusout="
                                        dropdownStates.provinsiKtpState = false
                                    "
                                />
                            </div>
                            <div class="sm:col-span-1 mr-5">
                                <NestedSelect
                                    label="Kabupaten"
                                    :options="kabupatenKtpOptions"
                                    v-model="form.kabupaten_ktp"
                                    @focusin="
                                        dropdownStates.kabupatenKtpState = true;
                                        form.kabupaten_ktp = '';
                                        form.kecamatan_ktp = '';
                                        form.kelurahan_ktp = '';
                                        form.kodepos_ktp = '';
                                    "
                                    @focusout="
                                        dropdownStates.kabupatenKtpState = false
                                    "
                                    v-if="form.provinsi_ktp"
                                />
                            </div>
                            <div class="sm:col-span-1 mr-5">
                                <NestedSelect
                                    label="Kecamatan"
                                    :options="kecamatanKtpOptions"
                                    v-model="form.kecamatan_ktp"
                                    @focusin="
                                        dropdownStates.kecamatanKtpState = true;
                                        form.kecamatan_ktp = '';
                                        form.kelurahan_ktp = '';
                                        form.kodepos_ktp = '';
                                    "
                                    @focusout="
                                        dropdownStates.kecamatanKtpState = false
                                    "
                                    v-if="
                                        form.kabupaten_ktp &&
                                        !dropdownStates.provinsiKtpState
                                    "
                                />
                            </div>
                            <div class="sm:col-span-1 mr-5">
                                <NestedSelect
                                    label="Kelurahan"
                                    :options="kelurahanKtpOptions"
                                    v-model="form.kelurahan_ktp"
                                    @focusin="
                                        dropdownStates.kelurahanKtpState = true;
                                        form.kelurahan_ktp = '';
                                        form.kodepos_ktp = '';
                                    "
                                    @focusout="
                                        dropdownStates.kelurahanKtpState = false
                                    "
                                    v-if="
                                        form.kecamatan_ktp &&
                                        !dropdownStates.kabupatenKtpState
                                    "
                                />
                            </div>
                            <div class="sm:col-span-1 mr-5">
                                <TextAreaInput
                                    id="alamat_ktp"
                                    name="alamat_ktp"
                                    label="Alamat Lengkap"
                                    v-model="form.alamat_ktp"
                                    rows="5"
                                />
                                <span class="text-sm text-black"
                                    >* Maksimal 70 Karakter</span
                                >
                            </div>
                            <div class="sm:col-span-1 mr-5">
                                <NestedSelect
                                    label="Kodepos"
                                    :options="kodePosKtpOptions"
                                    v-model="form.kodepos_ktp"
                                    @focusin="form.kodepos_ktp = ''"
                                    v-if="
                                        form.kelurahan_ktp &&
                                        !dropdownStates.kelurahanKtpState
                                    "
                                />
                            </div>
                        </div>
                    </div>
                    <div
                        class="bg-white p-4 mt-6 rounded-lg shadow-lg font-semibold"
                        :class="formAlamatKtp ? 'relative' : 'hidden'"
                    >
                        Alamat Domisili Sama Dengan KTP ?
                        <div class="sm:col-span-1 mt-2">
                            <input
                                type="checkbox"
                                :checked="selectedSame === 'Ya'"
                                @change="
                                    selectedSame =
                                        selectedSame === 'Ya' ? '' : 'Ya'
                                "
                            />
                            Ya
                            <input
                                class="ml-5"
                                type="checkbox"
                                :checked="selectedSame === 'Tidak'"
                                @change="
                                    selectedSame =
                                        selectedSame === 'Tidak' ? '' : 'Tidak'
                                "
                            />
                            Tidak
                        </div>
                    </div>
                    <div
                        class="bg-white p-4 mt-6 rounded-lg shadow-lg"
                        :class="
                            noKtpChecked && noHpChecked && noTlpRmhChecked
                                ? 'relative'
                                : 'hidden'
                        "
                    >
                        <h2 class="my-2 font-bold uppercase">
                            alamat domisili
                        </h2>
                        <hr />
                        <div class="mt-6 grid grid-cols-2 gap-4">
                            <div class="sm:col-span-1 mr-5">
                                <NestedSelect
                                    label="Provinsi"
                                    :options="provinsiDmsOptions"
                                    v-model="form.provinsi_dms"
                                    @focusin="
                                        dropdownStates.provinsiDmsState = true;
                                        form.provinsi_dms = '';
                                        form.kabupaten_dms = '';
                                        form.kecamatan_dms = '';
                                        form.kelurahan_dms = '';
                                        form.kodepos_dms = '';
                                    "
                                    @focusout="
                                        dropdownStates.provinsiDmsState = false
                                    "
                                />
                            </div>
                            <div class="sm:col-span-1 mr-5">
                                <NestedSelect
                                    label="Kabupaten"
                                    :options="kabupatenDmsOptions"
                                    v-model="form.kabupaten_dms"
                                    @focusin="
                                        dropdownStates.kabupatenDmsState = true;
                                        form.kabupaten_dms = '';
                                        form.kecamatan_dms = '';
                                        form.kelurahan_dms = '';
                                        form.kodepos_dms = '';
                                    "
                                    @focusout="
                                        dropdownStates.kabupatenDmsState = false
                                    "
                                    v-if="form.provinsi_dms"
                                />
                            </div>
                            <div class="sm:col-span-1 mr-5">
                                <NestedSelect
                                    label="Kecamatan"
                                    :options="kecamatanDmsOptions"
                                    v-model="form.kecamatan_dms"
                                    @focusin="
                                        dropdownStates.kecamatanDmsState = true;
                                        form.kecamatan_dms = '';
                                        form.kelurahan_dms = '';
                                        form.kodepos_dms = '';
                                    "
                                    @focusout="
                                        dropdownStates.kecamatanDmsState = false
                                    "
                                    v-if="
                                        form.kabupaten_dms &&
                                        !dropdownStates.provinsiDmsState
                                    "
                                />
                            </div>
                            <div class="sm:col-span-1 mr-5">
                                <NestedSelect
                                    label="Kelurahan"
                                    :options="kelurahanDmsOptions"
                                    v-model="form.kelurahan_dms"
                                    @focusin="
                                        dropdownStates.kelurahanDmsState = true;
                                        form.kelurahan_dms = '';
                                        form.kodepos_dms = '';
                                    "
                                    @focusout="
                                        dropdownStates.kelurahanDmsState = false
                                    "
                                    v-if="
                                        form.kecamatan_dms &&
                                        !dropdownStates.kabupatenDmsState
                                    "
                                />
                            </div>
                            <div class="sm:col-span-1 mr-5">
                                <TextAreaInput
                                    id="alamat_dms"
                                    name="alamat_dms"
                                    label="Alamat Lengkap"
                                    v-model="form.alamat_dms"
                                    rows="5"
                                />
                                <span class="text-sm text-black"
                                    >* Maksimal 70 Karakter</span
                                >
                                <InputError class="mt-2" />
                            </div>
                            <div class="sm:col-span-1 mr-5">
                                <NestedSelect
                                    label="Kodepos"
                                    :options="kodePosDmsOptions"
                                    v-model="form.kodepos_dms"
                                    @focusin="form.kodepos_dms = ''"
                                    v-if="
                                        form.kelurahan_dms &&
                                        !dropdownStates.kelurahanDmsState
                                    "
                                />
                            </div>
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
                                            form.reset(),
                                        ]
                                    "
                                >
                                    Batal
                                </button>
                                <button
                                    :disabled="validFormNasabah"
                                    :class="{
                                        'cursor-not-allowed': validFormNasabah,
                                    }"
                                    :title="
                                        validFormNasabah
                                            ? 'Isi semua form'
                                            : 'Klik untuk menlanjutkan'
                                    "
                                    @click="
                                        [
                                            (showForm.dataKerabat =
                                                !showForm.dataKerabat),
                                            (showForm.dataNasabah =
                                                !showForm.dataNasabah),
                                        ]
                                    "
                                    class="mt-4 ml-2 bg-green-700 text-white py-1 px-4 rounded"
                                >
                                    Lanjut
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
                <div
                    :class="showForm.dataKerabat ? 'relative' : 'hidden'"
                    class="bg-white p-4 mt-6 rounded-lg shadow-lg"
                    id="inputDataKerabat"
                >
                    <div class="bg-white p-4 rounded-lg shadow-lg">
                        <h2 class="my-2 font-bold uppercase">Data Kerabat</h2>
                        <hr />
                        <div class="mt-6 grid grid-cols-2 gap-4">
                            <div class="sm:col-span-1 mr-5">
                                <CustTextInput
                                    id="hub_kerabat"
                                    type="text"
                                    name="hub_kerabat"
                                    label="Hubungan Kerabat"
                                    v-model="form.hub_kerabat"
                                />
                                <InputError class="mt-2" />
                            </div>
                            <div class="sm:col-span-1 mr-5">
                                <CustTextInput
                                    id="nama_kerabat"
                                    type="text"
                                    name="nama_kerabat"
                                    label="Nama Kerabat"
                                    v-model="form.nama_kerabat"
                                />
                                <InputError class="mt-2" />
                            </div>
                            <div class="sm:col-span-1 mr-5">
                                <NestedSelect
                                    label="Provinsi"
                                    :options="provinsiKerabatOptions"
                                    v-model="form.provinsi_kerabat"
                                    @focusin="
                                        dropdownStates.provinsiKerabatState = true;
                                        form.provinsi_kerabat = '';
                                        form.kabupaten_kerabat = '';
                                        form.kecamatan_kerabat = '';
                                        form.kelurahan_kerabat = '';
                                        form.kodepos_kerabat = '';
                                    "
                                    @focusout="
                                        dropdownStates.provinsiKerabatState = false
                                    "
                                />
                                <InputError class="mt-2" />
                            </div>
                            <div class="sm:col-span-1 mr-5">
                                <NestedSelect
                                    label="Kabupaten"
                                    :options="kabupatenKerabatOptions"
                                    v-model="form.kabupaten_kerabat"
                                    @focusin="
                                        dropdownStates.kabupatenKerabatState = true;
                                        form.kabupaten_kerabat = '';
                                        form.kecamatan_kerabat = '';
                                        form.kelurahan_kerabat = '';
                                        form.kodepos_kerabat = '';
                                    "
                                    @focusout="
                                        dropdownStates.kabupatenKerabatState = false
                                    "
                                    v-if="
                                        form.provinsi_kerabat &&
                                        !dropdownStates.provinsiKerabatState
                                    "
                                />
                                <InputError class="mt-2" />
                            </div>
                            <div class="sm:col-span-1 mr-5">
                                <NestedSelect
                                    label="Kecamatan"
                                    :options="kecamatanKerabatOptions"
                                    v-model="form.kecamatan_kerabat"
                                    @focusin="
                                        dropdownStates.kecamatanKerabatState = true;
                                        form.kecamatan_kerabat = '';
                                        form.kelurahan_kerabat = '';
                                        form.kodepos_kerabat = '';
                                    "
                                    @focusout="
                                        dropdownStates.kecamatanKerabatState = false
                                    "
                                    v-if="form.kabupaten_kerabat"
                                />
                                <InputError class="mt-2" />
                            </div>
                            <div class="sm:col-span-1 mr-5">
                                <NestedSelect
                                    label="Kelurahan"
                                    :options="kelurahanKerabatOptions"
                                    v-model="form.kelurahan_kerabat"
                                    @focusin="
                                        dropdownStates.kelurahanKerabatState = true;
                                        form.kelurahan_kerabat = '';
                                        form.kodepos_kerabat = '';
                                    "
                                    @focusout="
                                        dropdownStates.kelurahanKerabatState = false
                                    "
                                    v-if="
                                        form.kecamatan_kerabat &&
                                        !dropdownStates.kabupatenKerabatState
                                    "
                                />
                                <InputError class="mt-2" />
                            </div>
                            <div class="sm:col-span-1 mr-5">
                                <TextAreaInput
                                    id="alamat_kerabat"
                                    name="alamat_kerabat"
                                    label="Alamat Lengkap"
                                    v-model="form.alamat_kerabat"
                                    rows="5"
                                />
                                <span class="text-sm text-black"
                                    >* Maksimal 70 Karakter</span
                                >
                                <InputError class="mt-2" />
                            </div>
                            <div class="sm:col-span-1 mr-5">
                                <NestedSelect
                                    label="Kodepos"
                                    :options="kodePosKerabatOptions"
                                    v-model="form.kodepos_kerabat"
                                    @focusin="form.kodepos_kerabat = ''"
                                    v-if="
                                        form.kelurahan_kerabat &&
                                        !dropdownStates.kelurahanKerabatState
                                    "
                                />
                                <InputError class="mt-2" />
                            </div>
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
                                            (showForm.dataKerabat =
                                                !showForm.dataKerabat),
                                        ]
                                    "
                                >
                                    Kembali
                                </button>
                                <button
                                    class="mt-4 ml-2 bg-red-700 text-white py-1 px-4 rounded"
                                    @click="
                                        [
                                            (showForm.dataNasabah =
                                                !showForm.dataNasabah),
                                            (showForm.dataKerabat =
                                                !showForm.dataKerabat),
                                            form.reset(),
                                        ]
                                    "
                                >
                                    Batal
                                </button>
                                <button
                                    :disabled="validFormKerabat"
                                    :class="{
                                        'cursor-not-allowed': validFormKerabat,
                                    }"
                                    :title="
                                        validFormKerabat
                                            ? 'Isi semua form'
                                            : 'Klik untuk menlanjutkan'
                                    "
                                    @click="
                                        [
                                            (showForm.dataKerabat =
                                                !showForm.dataKerabat),
                                            (showForm.dataRekening =
                                                !showForm.dataRekening),
                                        ]
                                    "
                                    class="mt-4 ml-2 bg-green-700 text-white py-1 px-4 rounded"
                                >
                                    Lanjut
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
                <div
                    class="bg-white p-4 mt-6 rounded-lg shadow-lg"
                    :class="showForm.dataRekening ? 'relative' : 'hidden'"
                >
                    <div class="bg-white p-4 rounded-lg shadow-lg">
                        <h2 class="my-2 font-bold uppercase">Data Rekening</h2>
                        <hr />
                        <div class="mt-6 grid grid-cols-2 gap-4">
                            <div class="sm:col-span-1 mr-5">
                                <CustTextInput
                                    id="no_rekening"
                                    type="text"
                                    name="no_rekening"
                                    label="No Rekening"
                                    @focusout="checkNoRek()"
                                    v-model="form.no_rekening"
                                />
                                <div v-if="form.errors.no_rekening">
                                    <span class="text-sm text-red-600"
                                        >*{{ form.errors.no_rekening[0] }}</span
                                    >
                                </div>
                            </div>
                            <div class="sm:col-span-1 mr-5">
                                <CustTextInput
                                    id="nama_rekening"
                                    type="text"
                                    name="nama_rekening"
                                    label="Nama Rekening"
                                    v-model="form.nama_rekening"
                                />
                                <InputError class="mt-2" />
                            </div>
                            <div class="sm:col-span-1 mr-5">
                                <NestedSelect
                                    label="Nama Bank"
                                    :options="[
                                        'BCA',
                                        'MANDIRI',
                                        'BRI',
                                        'BNI',
                                        'BSI',
                                        'MAYBANK',
                                        'CIMB',
                                    ]"
                                    v-model="form.nama_bank"
                                    @focusin="form.nama_bank = ''"
                                />
                                <InputError class="mt-2" />
                            </div>
                        </div>
                    </div>
                    <div class="flex justify-end items-center mb-4">
                        <button
                            class="mt-4 bg-red-700 text-white py-1 px-4 rounded"
                            @click="
                                [
                                    (showForm.dataKerabat =
                                        !showForm.dataKerabat),
                                    (showForm.dataRekening =
                                        !showForm.dataRekening),
                                ]
                            "
                        >
                            Kembali
                        </button>
                        <button
                            class="mt-4 ml-2 bg-red-700 text-white py-1 px-4 rounded"
                            @click="
                                [
                                    (showForm.dataRekening =
                                        !showForm.dataRekening),
                                    form.reset(),
                                ]
                            "
                        >
                            Batal
                        </button>
                        <button
                            type="submit"
                            :disabled="validFormRekening"
                            :class="{
                                'cursor-not-allowed':
                                    validFormRekening && !noRekChecked,
                            }"
                            :title="
                                validFormRekening
                                    ? 'Isi semua form'
                                    : 'Klik untuk menlanjutkan'
                            "
                            @click="store()"
                            class="mt-4 ml-2 bg-green-700 text-white py-1 px-4 rounded"
                        >
                            Submit
                        </button>
                    </div>
                </div>
            </main>
        </template>
    </AdminLayout>
</template>
