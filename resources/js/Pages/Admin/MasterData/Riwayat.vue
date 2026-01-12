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
import Map from "@/Components/MapComponent.vue";
import Modal from "@/Components/DialogModal.vue";
import { Head, useForm } from "@inertiajs/vue3";
import axios from "axios";
import dayjs from "dayjs";
import relativeTime from "dayjs/plugin/relativeTime";
import "dayjs/locale/id";
import { add } from "lodash";

dayjs.extend(relativeTime);
dayjs.locale("id");

onMounted(() => {
    userData();
});

defineProps({
    errors: Object,
});

const humandiff = (timestamp) => {
    return dayjs(timestamp).fromNow();
};

const modal = reactive({
    showMap: false,
});

const location = ref("");
const modalShowMap = (d) => {
    location.value = d.location;
    getAddress(d.location);
    modal.showMap = true;
};

const backBtnModalPeta = () => {
    modal.showMap = false;
};

const user = ref([]);
const userLoading = ref(false);
const userData = async () => {
    userLoading.value = true;
    await axios.get(route("getHistory")).then((res) => {
        const data = res.data;
        user.value = data;
        userLoading.value = false;
        console.log(user.value);
    });
};

const address = ref([]);

const getAddress = (location) => {
    axios
        .get(route("getAddress"), {
            params: {
                location,
            },
        })
        .then((response) => {
            address.value = response.data;
        })
        .catch((error) => {
            console.log("Error fetching address:", error);
        });
};

const crntPage = ref(1);
const perPage = 10;

const totalPages = computed(() => Math.ceil(user.value.length / perPage));

const pgntUser = computed(() => {
    const start = (crntPage.value - 1) * perPage;
    const end = Math.min(start + perPage, searchUser.value.length);
    return searchUser.value.slice(start, end).map((user, index) => ({
        ...user,
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

const searchIUser = ref("");
const searchUser = computed(() => {
    if (!searchIUser.value) {
        return user.value;
    }
    const lowerCase = searchIUser.value.toLowerCase();
    if (user.value) {
        return user.value.filter((usr) => {
            return (
                usr.user_id.toLowerCase().includes(lowerCase) ||
                usr.user_name.toLowerCase().includes(lowerCase)
            );
        });
    } else {
        return [];
    }
});

const formatDateDB = (input) => {
    const date = new Date(input); // Ensure input is a Date object
    const year = date.getFullYear();
    const month = String(date.getMonth() + 1).padStart(2, "0");
    const day = String(date.getDate()).padStart(2, "0");
    const hours = String(date.getHours()).padStart(2, "0");
    const minutes = String(date.getMinutes()).padStart(2, "0");
    const seconds = String(date.getSeconds()).padStart(2, "0");

    return `${day}-${month}-${year} ${hours}:${minutes}:${seconds}`;
};
</script>
<template>
    <Head title="Riwayat Login/Logout" />
    <AdminLayout>
        <template #titlePage>
            <TitlePage value="Riwayat Aktivitas Users" />
        </template>
        <template #content>
            <main class="p-6 bg-gray-100 flex-grow">
                <div class="mt-6">
                    <div class="py-3 mt-2 rounded-lg bg-sky-700 shadow-lg">
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
                                        user id
                                    </th>
                                    <th
                                        scope="col"
                                        class="text-center text-md font-sans uppercase pb-3 text-white"
                                    >
                                        user name
                                    </th>
                                    <th
                                        scope="col"
                                        class="text-center text-md font-sans uppercase pb-3 text-white"
                                    >
                                        waktu login
                                    </th>
                                    <th
                                        scope="col"
                                        class="text-center text-md font-sans uppercase pb-3 text-white"
                                    >
                                        waktu logout
                                    </th>
                                    <th
                                        scope="col"
                                        class="text-center text-md font-sans uppercase pb-3 text-white"
                                    >
                                        ip address
                                    </th>
                                    <th
                                        scope="col"
                                        class="text-center text-md font-sans uppercase pb-3 text-white"
                                    >
                                        lokasi
                                    </th>
                                    <th
                                        scope="col"
                                        class="text-center text-md font-sans uppercase pb-3 text-white"
                                    >
                                        peta
                                    </th>
                                </tr>
                            </thead>
                            <tbody v-if="!userLoading">
                                <tr
                                    v-for="(d, index) in pgntUser"
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
                                        {{ d.user_id }}
                                    </td>
                                    <td
                                        class="whitespace-nowrap text-sm text-center text-black py-2"
                                    >
                                        {{ d.user_name }}
                                    </td>
                                    <td
                                        class="whitespace-nowrap text-sm text-center text-black py-2"
                                    >
                                        {{ formatDateDB(d.login_at) }} <br />
                                        ({{ humandiff(d.login_at) }})
                                    </td>
                                    <td
                                        class="whitespace-nowrap text-sm text-center text-black py-2"
                                    >
                                        <span v-if="d.logout_at">
                                            {{ formatDateDB(d.logout_at) }}
                                            <br />
                                            ({{ humandiff(d.logout_at) }})
                                        </span>
                                        <span v-else> Belum Logout </span>
                                    </td>
                                    <td
                                        class="whitespace-nowrap text-sm text-center text-black py-2"
                                    >
                                        {{ d.ip_address }}
                                    </td>
                                    <td
                                        class="whitespace-nowrap text-sm text-center text-black py-2"
                                    >
                                        {{ d.location ? d.location : "Kosong" }}
                                    </td>
                                    <td
                                        v-if="d.location != null"
                                        class="whitespace-nowrap text-sm text-center text-black py-2"
                                    >
                                        <a @click="modalShowMap(d)"
                                            ><img
                                                class="m-auto cursor-pointer"
                                                src="/gadaiemas/storage/icon/map.png"
                                                title="Tampilkan Peta"
                                        /></a>
                                    </td>
                                    <td
                                        v-else
                                        class="whitespace-nowrap text-sm text-center text-black py-2"
                                    >
                                        <a><img
                                                class="m-auto"
                                                src="/gadaiemas/storage/icon/nothing.png"
                                                title="Tidak Ada Lokasi"
                                        /></a>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                        <div class="w-full text-center mt-2">
                            <span
                                class="text-center text-xl text-white"
                                v-if="userLoading"
                                >Sedang Memuat...</span
                            >
                        </div>
                        <div class="w-full text-center mt-2">
                            <span
                                class="text-center text-xl text-white"
                                v-if="!userLoading && user.length < 1"
                                >Tidak Ada Data</span
                            >
                        </div>
                    </div>
                    <div class="flex space-x-2 mt-2" v-if="!userLoading">
                        <button @click="prevPage">
                            <i
                                ><img src="/gadaiemas/storage/icon/previous.png"
                                title="Previous"</i
                            >
                        </button>
                        <p class="p-1">
                            Page {{ crntPage }} of {{ totalPages }}
                        </p>
                        <button @click="nextPage">
                            <i
                                ><img src="/gadaiemas/storage/icon/next.png" title="Next"
                            /></i>
                        </button>
                    </div>
                    <Modal
                        :show="modal.showMap"
                        @close="[(modal.showMap = false)]"
                        maxWidth="2xl"
                        classHeader="bg-sky-700 text-white"
                    >
                        <template #title>Lokasi Di Peta</template>
                        <template #content>
                            <div
                                class="flex justify-center items-center w-full h-96 p-2"
                            >
                                <!-- Center the container and set a fixed height -->
                                <Map
                                    :location="location"
                                    :showMap="modal.showMap"
                                    class="w-full h-full"
                                />
                            </div>
                            <div class="ml-2 mt-10 mb-2">
                                <h3 class="text-lg font-semibold uppercase">
                                    Detail Lokasi
                                </h3>
                                <div class="mt-3 grid grid-cols-2 gap-5">
                                    <h3 class="col-span-1">
                                        Provinsi : {{ address.state }}
                                    </h3>
                                    <h3 class="col-span-1 ml-3">
                                        Kabupaten/Kota : {{ address.city }}
                                    </h3>
                                    <h3 class="col-span-1">
                                        Kelurahan/Desa : {{ address.village }}
                                    </h3>
                                    <h3 class="col-span-1 ml-3">
                                        Area Sekitar :
                                        {{ address.neighbourhood }}
                                    </h3>
                                    <h3 class="col-span-1">
                                        Kodepos : {{ address.postcode }}
                                    </h3>
                                    <h3 class="col-span-1 ml-3">
                                        Negara : {{ address.country }}
                                    </h3>
                                    <h3 class="col-span-1">
                                        Nama Jalan : {{ address.road }}
                                    </h3>
                                    <h3 class="col-span-1 ml-3">
                                        Tingkat Akurat : 97.9%
                                    </h3>
                                </div>
                            </div>
                            <div class="flex justify-end items-center mb-4">
                                <div class="ml-4">
                                    <div class="text-black">
                                        <button
                                            @click="backBtnModalPeta()"
                                            class="mt-4 ml-2 bg-red-700 text-white py-1 px-4 rounded"
                                        >
                                            Kembali
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
