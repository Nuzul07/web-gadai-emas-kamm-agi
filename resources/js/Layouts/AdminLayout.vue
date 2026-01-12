<script setup>
import { ref, onMounted, watch, computed } from "vue";
import Dropdown from "@/Components/Dropdown.vue";
import DropdownLink from "@/Components/DropdownLink.vue";
import Swal from "sweetalert2";
import axios from "axios";
import { selectedTipeGadai } from "@/src/store";

const currentUrl = ref(window.location.href);
const showMenu = ref(true);
const dropdownStates = ref({
    masterdata: false,
    transaksi: false,
    transaksiGadai: false,
    lelang: false,
    sbgDwilipat: false,
});
const radioVisible = ref(localStorage.getItem("radioVisible") === "true");
const previousTipeGadai = ref(selectedTipeGadai.value); // Track the previous selection

const toggleRadioVisibility = () => {
    radioVisible.value = !radioVisible.value;
    localStorage.setItem("radioVisible", radioVisible.value);
};

const changeTipeGadai = async (event, tipe) => {
    event.preventDefault(); // Prevent immediate change of radio button
    const result = await Swal.fire({
        title: `Apakah anda yakin ingin beralih ke produk gadai ${tipe.toUpperCase()}?`,
        icon: "warning",
        showDenyButton: true,
        confirmButtonText: "Ya, pilih!",
        confirmButtonColor: "#036aa1",
        denyButtonText: "Batal",
        denyButtonColor: "#aaa",
        reverseButtons: true,
    });

    if (result.isConfirmed) {
        selectedTipeGadai.value = tipe;
        previousTipeGadai.value = tipe; // Update previous selection
        radioVisible.value = false;
        localStorage.setItem("radioVisible", radioVisible.value);
    } else {
        // Reset the radio button back to the previous selection
        setTimeout(() => {
            event.target.checked = false;
            const previousInput = document.querySelector(
                `input[value="${previousTipeGadai.value}"]`
            );
            if (previousInput) previousInput.checked = true;
        }, 0);
    }
};

onMounted(() => {
    const savedStates = localStorage.getItem("dropdownStates");
    if (savedStates) {
        dropdownStates.value = JSON.parse(savedStates);
    }
});

watch(
    dropdownStates,
    (newStates) => {
        localStorage.setItem("dropdownStates", JSON.stringify(newStates));
    },
    { deep: true }
);

const toggleDropdown = (key) => {
    dropdownStates.value[key] = !dropdownStates.value[key];
    localStorage.setItem(
        "dropdownStates",
        JSON.stringify(dropdownStates.value)
    );
};

const logout = async () => {
    try {
        const response = await axios.post(route("logout"));
        localStorage.clear();
        window.location.href = route("login");
    } catch (error) {
        console.error("Logout failed:", error);
    }
};
</script>

<template>
    <div class="flex h-screen bg-gray-100">
        <!-- Sidebar -->
        <div
            :class="showMenu ? 'relative' : 'hidden'"
            class="flex flex-col bg-clip-border absolute inset-y-0 left-0 sm:-translate-x-0 transform transition duration-200 ease-in-out bg-sky-700 text-white h-screen w-full max-w-[18rem] p-4 shadow-xl shadow-blue-gray-900/5 overflow-y-scroll scrollbar scrollbar-thumb-sky-700 scrollbar-track-white"
        >
            <div class="flex items-center mb-10 bg-amber-500 rounded-lg py-2">
                <img
                    src="/gadaiemas/storage/image/pawnXpert-logo-sidemenu.png"
                    alt="Veritas Logo"
                    class="h-10 w-10 ml-2 mr-3"
                />
                <span class="font-semibold text-xl text-white"
                    >PawnXpert AGI</span
                >
            </div>
            <div
                class="mb-3 bg-amber-500 flex items-center hover:bg-amber-400 p-2 rounded w-full text-left cursor-pointer"
                @click="toggleRadioVisibility"
            >
                <span class="font-semibold text-xl text-white">
                    Pilih Produk Gadai
                </span>
                <img
                    :src="
                        radioVisible
                            ? '/gadaiemas/storage/icon/up.png'
                            : '/gadaiemas/storage/icon/down.png'
                    "
                    class="ml-auto"
                />
            </div>

            <!-- Radio Buttons -->
            <div class="mb-10 px-4" v-show="radioVisible">
                <form>
                    <div class="flex items-center mb-4">
                        <input
                            id="emas"
                            type="radio"
                            value="emas"
                            :checked="selectedTipeGadai === 'emas'"
                            @change="(event) => changeTipeGadai(event, 'emas')"
                            name="produk"
                            class="w-4 h-4 text-amber-500 focus:ring-amber-500 border-gray-300 cursor-pointer"
                            title="Beralih Ke Gadai Emas"
                        />
                        <label
                            for="emas"
                            class="ml-2 text-sm font-medium text-white"
                        >
                            Emas
                        </label>
                    </div>

                    <!-- <div class="flex items-center mb-4">
                        <input 
                            id="motor" 
                            type="radio" 
                            value="motor" 
                            :checked="selectedTipeGadai === 'motor'"
                            @change="(event) => changeTipeGadai(event, 'motor')"
                            name="produk"
                            class="w-4 h-4 text-amber-500 focus:ring-amber-500 border-gray-300 cursor-not-allowed"
                            :disabled="false"
                            title="Belum Tersedia"
                        />
                        <label for="motor" class="ml-2 text-sm font-medium text-white">
                            Motor
                        </label>
                    </div> -->

                    <!-- <div
                        class="flex items-center mb-4"
                    >
                        <input
                            id="mobil"
                            type="radio"
                            value="mobil"
                            :checked="selectedTipeGadai === 'mobil'"
                            @change="(event) => changeTipeGadai(event, 'mobil')"
                            name="produk"
                            class="w-4 h-4 text-amber-500 focus:ring-amber-500 border-gray-300 cursor-pointer"
                            :disabled="false"
                            title="Belum Tersedia"
                        />
                        <label
                            for="mobil"
                            class="ml-2 text-sm font-medium text-white"
                        >
                            Mobil
                        </label>
                    </div> -->
                </form>
            </div>

            <!-- Logo -->
            <div class="flex-1">
                <nav class="flex flex-col space-y-4">
                    <a
                        :href="route('gadaiDashboard')"
                        :class="[
                            currentUrl.match('index')
                                ? 'bg-amber-500 text-white'
                                : 'bg-amber-500 text-white',
                        ]"
                        class="flex items-center hover:bg-amber-400 hover:text-white p-2 rounded cursor-pointer"
                    >
                        <i class="mr-2"
                            ><img src="/gadaiemas/storage/icon/home.png"
                        /></i>
                        Dashboard
                    </a>
                    <div>
                        <button
                            @click="toggleDropdown('masterdata')"
                            :class="[
                                currentUrl.match('index')
                                    ? 'bg-amber-500 text-white'
                                    : 'bg-amber-500 text-white',
                            ]"
                            class="flex items-center hover:bg-amber-400 p-2 rounded w-full text-left"
                        >
                            <i class="mr-2"
                                ><img src="/gadaiemas/storage/icon/byte.png"
                            /></i>
                            Master Data
                            <span
                                v-if="dropdownStates.masterdata"
                                class="ml-auto"
                                ><img src="/gadaiemas/storage/icon/up.png"
                            /></span>
                            <span v-else class="ml-auto"
                                ><img src="/gadaiemas/storage/icon/down.png"
                            /></span>
                        </button>
                        <div
                            v-if="dropdownStates.masterdata"
                            class="ml-6 mt-2 space-y-2"
                        >
                            <a
                                :href="route('nasabah')"
                                :class="{
                                    'bg-amber-500': currentUrl.match('nasabah'),
                                }"
                                class="flex items-center hover:bg-amber-400 hover:text-white p-2 rounded cursor-pointer"
                                v-if="
                                    $page.props.auth.user.LEVEL_USER ==
                                        'pusat' ||
                                    $page.props.auth.user.LEVEL_USER ==
                                        'super' ||
                                    $page.props.auth.user.LEVEL_USER == 'kasir'
                                "
                            >
                                <i class="mr-1"
                                    ><img
                                        src="/gadaiemas/storage/icon/asterisk.png"
                                /></i>
                                Nasabah
                            </a>
                            <a
                                :href="route('kurs')"
                                :class="{
                                    'bg-amber-500': currentUrl.match('kurs'),
                                }"
                                class="flex items-center hover:bg-amber-400 hover:text-white p-2 rounded cursor-pointer"
                                v-if="
                                    ($page.props.auth.user.LEVEL_USER ==
                                        'pusat' ||
                                        $page.props.auth.user.LEVEL_USER ==
                                            'super') &&
                                    selectedTipeGadai === 'emas'
                                "
                            >
                                <i class="mr-1"
                                    ><img
                                        src="/gadaiemas/storage/icon/asterisk.png"
                                /></i>
                                STL
                            </a>
                            <a
                                :href="route('hps')"
                                :class="{
                                    'bg-amber-500': currentUrl.match('hps'),
                                }"
                                class="flex items-center hover:bg-amber-400 hover:text-white p-2 rounded cursor-pointer"
                                v-if="
                                    ($page.props.auth.user.LEVEL_USER ==
                                        'pusat' ||
                                        $page.props.auth.user.LEVEL_USER ==
                                            'super') &&
                                    (selectedTipeGadai === 'motor' ||
                                        selectedTipeGadai === 'mobil')
                                "
                            >
                                <i class="mr-1"
                                    ><img
                                        src="/gadaiemas/storage/icon/asterisk.png"
                                /></i>
                                HPS
                            </a>
                            <a
                                :href="route('penaksir')"
                                :class="{
                                    'bg-amber-500':
                                        currentUrl.match('penaksir'),
                                }"
                                class="flex items-center hover:bg-amber-400 hover:text-white p-2 rounded cursor-pointer"
                                v-if="
                                    $page.props.auth.user.LEVEL_USER ==
                                        'pusat' ||
                                    $page.props.auth.user.LEVEL_USER == 'super'
                                "
                            >
                                <i class="mr-1"
                                    ><img
                                        src="/gadaiemas/storage/icon/asterisk.png"
                                /></i>
                                Penaksir
                            </a>
                            <a
                                :href="route('bagianLain')"
                                :class="{
                                    'bg-amber-500':
                                        currentUrl.match('bagianLain'),
                                }"
                                class="flex items-center hover:bg-amber-400 hover:text-white p-2 rounded cursor-pointer"
                                v-if="
                                    selectedTipeGadai === 'emas' && ($page.props.auth.user.LEVEL_USER == 'pusat' || $page.props.auth.user.LEVEL_USER == 'super')
                                "
                            >
                                <i class="mr-1"
                                    ><img
                                        src="/gadaiemas/storage/icon/asterisk.png"
                                /></i>
                                Komponen Hitung
                            </a>
                            <a  
                                :href="route('sewaModal')"
                                :class="{
                                    'bg-amber-500':
                                        currentUrl.match('sewaModal'),
                                }"
                                class="flex items-center hover:bg-amber-400 hover:text-white p-2 rounded cursor-pointer"
                                v-if="
                                    selectedTipeGadai !== 'emas' && 
                                    ($page.props.auth.user.LEVEL_USER ==
                                        'pusat' ||
                                    $page.props.auth.user.LEVEL_USER == 'super')
                                "
                            >
                                <i class="mr-1"
                                    ><img
                                        src="/gadaiemas/storage/icon/asterisk.png"
                                /></i>
                                Komponen Hitung
                            </a>
                            <a
                                v-if="
                                    $page.props.auth.user.LEVEL_USER == 'pusat'
                                "
                                :href="route('user')"
                                :class="{
                                    'bg-amber-500': currentUrl.match('user'),
                                }"
                                class="flex items-center hover:bg-amber-400 hover:text-white p-2 rounded cursor-pointer"
                            >
                                <i class="mr-1"
                                    ><img
                                        src="/gadaiemas/storage/icon/asterisk.png"
                                /></i>
                                Users
                            </a>
                            <!-- <a
                                to="/dashboard/analytics"
                                class="flex items-center hover:bg-amber-400 hover:text-white p-2 rounded cursor-pointer"
                                active-class="active-link"
                                exact-active-class="exact-active-link"
                            >
                                <i class="mr-1"
                                    ><img src="/gadaiemas/storage/icon/asterisk.png"  
                                /></i>
                                Data Barang
                            </a> -->
                        </div>
                    </div>
                    <div>
                        <button
                            @click="toggleDropdown('transaksi')"
                            :class="[
                                currentUrl.match('index')
                                    ? 'bg-amber-500 text-white'
                                    : 'bg-amber-500 text-white',
                            ]"
                            class="flex items-center hover:bg-amber-400 p-2 rounded w-full text-left"
                        >
                            <i class="mr-2"
                                ><img
                                    src="/gadaiemas/storage/icon/transaction.png"
                            /></i>
                            Transaksi
                            <span
                                v-if="dropdownStates.transaksi"
                                class="ml-auto"
                                ><img src="/gadaiemas/storage/icon/up.png"
                            /></span>
                            <span v-else class="ml-auto"
                                ><img src="/gadaiemas/storage/icon/down.png"
                            /></span>
                        </button>
                        <div
                            v-if="dropdownStates.transaksi"
                            class="ml-6 mt-2 space-y-2"
                        >
                            <button
                                @click="toggleDropdown('transaksiGadai')"
                                :class="[
                                    currentUrl.match('index')
                                        ? 'bg-amber-500 text-white'
                                        : 'bg-amber-500 text-white',
                                ]"
                                class="flex items-center hover:bg-amber-400 p-2 rounded w-full text-left"
                            >
                                <i class="mr-1"
                                    ><img
                                        src="/gadaiemas/storage/icon/asterisk.png"
                                /></i>
                                Transaksi Gadai
                                <span
                                    v-if="dropdownStates.transaksiGadai"
                                    class="ml-auto"
                                    ><img src="/gadaiemas/storage/icon/up.png"
                                /></span>
                                <span v-else class="ml-auto"
                                    ><img
                                        src="/gadaiemas/storage/icon/down.png"
                                /></span>
                            </button>
                            <div
                                v-if="dropdownStates.transaksiGadai"
                                class="ml-6 mt-2 space-y-2"
                            >
                                <a
                                    :href="route('perGadaiOffline')"
                                    :class="{
                                        'bg-amber-500':
                                            currentUrl.match('perGadaiOffline'),
                                    }"
                                    class="flex items-center hover:bg-amber-400 hover:text-white p-2 rounded cursor-pointer"
                                >
                                    <i class="mr-1"
                                        ><img
                                            src="/gadaiemas/storage/icon/asterisk.png"
                                    /></i>
                                    Permohonan Gadai
                                </a>
                                <a
                                    :href="route('perpanjangan')"
                                    :class="{
                                        'bg-amber-500':
                                            currentUrl.match('perpanjangan'),
                                    }"
                                    class="flex items-center hover:bg-amber-400 hover:text-white p-2 rounded cursor-pointer"
                                >
                                    <i class="mr-1"
                                        ><img
                                            src="/gadaiemas/storage/icon/asterisk.png"
                                    /></i>
                                    Perpanjangan
                                </a>
                                <a
                                    :href="route('pelunasan')"
                                    :class="{
                                        'bg-amber-500':
                                            currentUrl.match('pelunasan'),
                                    }"
                                    class="flex items-center hover:bg-amber-400 hover:text-white p-2 rounded cursor-pointer"
                                >
                                    <i class="mr-1"
                                        ><img
                                            src="/gadaiemas/storage/icon/asterisk.png"
                                    /></i>
                                    Pelunasan
                                </a>
                                <a
                                    :href="route('simulasiLunas')"
                                    :class="{
                                        'bg-amber-500':
                                            currentUrl.match('simulasi-lunas'),
                                    }"
                                    class="flex items-center hover:bg-amber-400 hover:text-white p-2 rounded cursor-pointer"
                                >
                                    <i class="mr-1"
                                        ><img
                                            src="/gadaiemas/storage/icon/asterisk.png"
                                    /></i>
                                    Simulasi Pelunasan
                                </a>
                            </div>
                        </div>
                        <div
                            v-if="dropdownStates.transaksi"
                            class="ml-6 mt-2 space-y-2"
                        >
                            <button
                                @click="toggleDropdown('lelang')"
                                :class="[
                                    currentUrl.match('index')
                                        ? 'bg-amber-500 text-white'
                                        : 'bg-amber-500 text-white',
                                ]"
                                class="flex items-center hover:bg-amber-400 p-2 rounded w-full text-left"
                            >
                                <i class="mr-1"
                                    ><img
                                        src="/gadaiemas/storage/icon/asterisk.png"
                                /></i>
                                Lelang
                                <span
                                    v-if="dropdownStates.lelang"
                                    class="ml-auto"
                                    ><img src="/gadaiemas/storage/icon/up.png"
                                /></span>
                                <span v-else class="ml-auto"
                                    ><img
                                        src="/gadaiemas/storage/icon/down.png"
                                /></span>
                            </button>
                            <div
                                v-if="dropdownStates.lelang"
                                class="ml-6 mt-2 space-y-2"
                            >
                                <a
                                    :href="route('lelang')"
                                    :class="{
                                        'bg-amber-500':
                                            currentUrl.match('lelang'),
                                    }"
                                    class="flex items-center hover:bg-amber-400 hover:text-white p-2 rounded cursor-pointer"
                                >
                                    <i class="mr-1"
                                        ><img
                                            src="/gadaiemas/storage/icon/asterisk.png"
                                    /></i>
                                    Pengajuan Lelang
                                </a>
                                <a
                                    v-if="
                                        $page.props.auth.user.LEVEL_USER ==
                                            'pusat' ||
                                        $page.props.auth.user.LEVEL_USER ==
                                            'super'
                                    "
                                    :href="route('validasiLelang')"
                                    :class="{
                                        'bg-amber-500':
                                            currentUrl.match('validasi-spv'),
                                    }"
                                    class="flex items-center hover:bg-amber-400 hover:text-white p-2 rounded cursor-pointer"
                                >
                                    <i class="mr-1"
                                        ><img
                                            src="/gadaiemas/storage/icon/asterisk.png"
                                    /></i>
                                    Validasi Lelang
                                </a>
                                <a
                                    :href="route('inputLelang')"
                                    :class="{
                                        'bg-amber-500':
                                            currentUrl.match('input-approve'),
                                    }"
                                    class="flex items-center hover:bg-amber-400 hover:text-white p-2 rounded cursor-pointer"
                                >
                                    <i class="mr-1"
                                        ><img
                                            src="/gadaiemas/storage/icon/asterisk.png"
                                    /></i>
                                    Input Approve Lelang
                                </a>
                                <!-- <a
                                    :href="route('perpanjangan')"
                                    :class="{
                                        'bg-amber-500':
                                            currentUrl.match('perpanjangan'),
                                    }"
                                    class="flex items-center hover:bg-amber-400 hover:text-white p-2 rounded cursor-pointer"
                                >
                                    <i class="mr-1"
                                        ><img src="/gadaiemas/storage/icon/asterisk.png"
                                    /></i>
                                    Perpanjangan
                                </a> -->
                                <!-- <a
                                    :href="route('pelunasan')"
                                    :class="{
                                        'bg-amber-500':
                                            currentUrl.match('pelunasan'),
                                    }"
                                    class="flex items-center hover:bg-amber-400 hover:text-white p-2 rounded cursor-pointer"
                                >
                                    <i class="mr-1"
                                        ><img src="/gadaiemas/storage/icon/asterisk.png"
                                    /></i>
                                    Pelunasan
                                </a> -->
                                <!-- <a
                                    :href="route('simulasiLunas')"
                                    :class="{
                                        'bg-amber-500':
                                            currentUrl.match('simulasi-lunas'),
                                    }"
                                    class="flex items-center hover:bg-amber-400 hover:text-white p-2 rounded cursor-pointer"
                                >
                                    <i class="mr-1"
                                        ><img src="/gadaiemas/storage/icon/asterisk.png"
                                    /></i>
                                    Simulasi Pelunasan
                                </a> -->
                            </div>
                        </div>
                    </div>
                    <div>
                        <button
                            @click="toggleDropdown('sbgDwilipat')"
                            :class="[
                                currentUrl.match('index')
                                    ? 'bg-amber-500 text-white'
                                    : 'bg-amber-500 text-white',
                            ]"
                            class="flex items-center hover:bg-amber-400 p-2 rounded w-full text-left"
                        >
                            <i class="mr-2"
                                ><img src="/gadaiemas/storage/icon/printer.png"
                            /></i>
                            Cetak SBG Dwilipat
                            <span
                                v-if="dropdownStates.sbgDwilipat"
                                class="ml-auto"
                                ><img src="/gadaiemas/storage/icon/up.png"
                            /></span>
                            <span v-else class="ml-auto"
                                ><img src="/gadaiemas/storage/icon/down.png"
                            /></span>
                        </button>
                        <div
                            v-if="dropdownStates.sbgDwilipat"
                            class="ml-6 mt-2 space-y-2"
                        >
                            <a
                                :href="route('dwilipatGadai')"
                                :class="{
                                    'bg-amber-500':
                                        currentUrl.match('dwilipatGadai'),
                                }"
                                class="flex items-center hover:bg-amber-400 hover:text-white p-2 rounded cursor-pointer"
                            >
                                <i class="mr-1"
                                    ><img
                                        src="/gadaiemas/storage/icon/asterisk.png"
                                /></i>
                                Transaksi Gadai
                            </a>
                            <!-- <a
                                :href="route('dwilipatLunas')"
                                :class="{
                                    'bg-amber-500':
                                        currentUrl.match('dwilipatLunas'),
                                }"
                                class="flex items-center hover:bg-amber-400 hover:text-white p-2 rounded cursor-pointer"
                            >
                                <i class="mr-1"
                                    ><img src="/gadaiemas/storage/icon/asterisk.png"
                                /></i>
                                Pelunasan
                            </a>
                            <a
                                :href="route('dwilipatPanjang')"
                                :class="{
                                    'bg-amber-500':
                                        currentUrl.match('dwilipatPanjang'),
                                }"
                                class="flex items-center hover:bg-amber-400 hover:text-white p-2 rounded cursor-pointer"
                            >
                                <i class="mr-1"
                                    ><img src="/gadaiemas/storage/icon/asterisk.png"
                                /></i>
                                Perpanjangan
                            </a> -->
                            <!-- <a
                                to="/dashboard/analytics"
                                class="flex items-center hover:bg-amber-400 hover:text-white p-2 rounded cursor-pointer"
                                active-class="active-link"
                                exact-active-class="exact-active-link"
                            >
                                <i class="mr-1"
                                    ><img src="/gadaiemas/storage/icon/asterisk.png"  
                                /></i>
                                Data Barang
                            </a> -->
                        </div>
                    </div>

                    <div>
                        <a
                            :href="route('kartuPiutang')"
                            :class="[
                                currentUrl.match('kartu-piutang')
                                    ? 'bg-amber-500 text-white'
                                    : 'bg-amber-500 text-white',
                            ]"
                            class="flex items-center hover:bg-amber-400 hover:text-white p-2 rounded cursor-pointer"
                        >
                            <i class="mr-2"
                                ><img src="/gadaiemas/storage/icon/piutang.png"
                            /></i>
                            Kartu Piutang
                        </a>
                    </div>

                    <div>
                        <a
                            :href="route('laporanIndex')"
                            :class="[
                                currentUrl.match('laporan-all')
                                    ? 'bg-amber-500 text-white'
                                    : 'bg-amber-500 text-white',
                            ]"
                            class="flex items-center hover:bg-amber-400 hover:text-white p-2 rounded cursor-pointer"
                        >
                            <i class="mr-2"
                                ><img
                                    src="/gadaiemas/storage/icon/seo-report.png"
                            /></i>
                            Laporan
                        </a>
                    </div>

                    <div>
                        <a
                            :href="route('helperIndex')"
                            :class="[
                                currentUrl.match('helper-index')
                                    ? 'bg-amber-500 text-white'
                                    : 'bg-amber-500 text-white',
                            ]"
                            class="flex items-center hover:bg-amber-400 hover:text-white p-2 rounded cursor-pointer"
                        >
                            <i class="mr-2"
                                ><img src="/gadaiemas/storage/icon/chatbot.png"
                            /></i>
                            Bantuan
                        </a>
                    </div>
                </nav>
            </div>
        </div>
        <!-- Content -->
        <div class="flex-1 flex flex-col">
            <!-- Header -->
            <header class="flex justify-between items-center bg-amber-500 p-4">
                <button @click="showMenu = !showMenu">
                    <svg
                        xmlns="http://www.w3.org/2000/svg"
                        fill="none"
                        viewBox="0 2 20 20"
                        stroke-width="1.5"
                        stroke="currentColor"
                        class="w-6 h-6 text-white"
                    >
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5"
                        />
                    </svg>
                </button>
                <!-- Wrap the slot and span inside a div -->
                <div class="flex items-center gap-2">
                    <slot name="titlePage" />
                    <span
                        v-if="selectedTipeGadai === 'emas'"
                        class="bg-yellow-100 text-yellow-800 text-xs font-medium px-2.5 py-0.5 rounded-sm dark:bg-gray-700 dark:text-yellow-300 border border-yellow-300"
                    >
                        Emas
                    </span>
                    <span
                        v-if="selectedTipeGadai === 'motor'"
                        class="bg-indigo-100 text-indigo-800 text-xs font-medium me-2 px-2.5 py-0.5 rounded-sm dark:bg-gray-700 dark:text-indigo-400 border border-indigo-400"
                    >
                        Motor
                    </span>
                    <span
                        v-if="selectedTipeGadai === 'mobil'"
                        class="bg-purple-100 text-purple-800 text-xs font-medium me-2 px-2.5 py-0.5 rounded-sm dark:bg-gray-700 dark:text-purple-400 border border-purple-400"
                    >
                        Mobil
                    </span>
                </div>
                <Dropdown align="right" width="48">
                    <template #trigger>
                        <span class="inline-flex rounded-md">
                            <button
                                type="button"
                                class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-white bg-sky-700 focus:outline-none transition ease-in-out duration-150"
                            >
                                {{ $page.props.auth.user.USER_NAME }}

                                <svg
                                    class="ml-2 -mr-0.5 h-4 w-4"
                                    xmlns="http://www.w3.org/2000/svg"
                                    viewBox="0 0 20 20"
                                    fill="currentColor"
                                >
                                    <path
                                        fill-rule="evenodd"
                                        d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                                        clip-rule="evenodd"
                                    />
                                </svg>
                            </button>
                        </span>
                    </template>

                    <template #content>
                        <DropdownLink @click="logout" method="get" as="button">
                            Log Out
                        </DropdownLink>
                    </template>
                </Dropdown>
            </header>
            <!-- Main -->
            <slot name="content" />
        </div>
    </div>
</template>
