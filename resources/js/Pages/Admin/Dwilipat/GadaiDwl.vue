<script setup>
import { ref, onMounted, watch, computed, reactive } from "vue";
import AdminLayout from "@/Layouts/AdminLayout.vue";
import TitlePage from "@/Components/TitlePage.vue";
import SearchInput from "@/Components/SearchInput.vue";
import { Head, useForm } from "@inertiajs/vue3";
import axios from "axios";
import Swal from "sweetalert2";
import { selectedTipeGadai } from "@/src/store";

onMounted(() => {
    checkTipeGadai()
    gadaiData();
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

defineProps({
    errors: Object,
});

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
            return (
                gad.No_sbg.toLowerCase().includes(lowerCase) ||
                gad.nasabah.No_ktp.toLowerCase().includes(lowerCase) ||
                gad.nasabah.Nama.toLowerCase().includes(lowerCase) ||
                gad.Posko.toLowerCase().includes(lowerCase)
            );
        });
    } else {
        return [];
    }
});

const cetakUlangSBG = (d) => {
    Swal.fire({
        title: "Info",
        text: "Mohon Tunggu",
        icon: "info",
        showConfirmButton: false,
        timer: 5000,
    });
    generatePDF(d);
};
//

const generatePDF = async (d) => {
    try {
        // Show a loading message while generating the PDF
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

        // Make the API request to generate the PDF
        const response = await axios.get(route("generatepdfDwi"), {
            responseType: "blob",
            params: {
                no_sbg: d.No_sbg,
                produk_gadai: d.Produk_gadai
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

            // Close the loading message and show success message
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
        };
    } catch (error) {
        // Close the loading message and show an error message
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

</script>

<template>
    <Head title="Cetak SBG Dwilipat" />
    <AdminLayout>
        <template #titlePage>
            <TitlePage value="SBG Dwilipat Gadai" />
        </template>
        <template #content>
            <main class="p-6 bg-gray-100 flex-grow overflow-y-auto">
                <div class="mt-6" id="tableTransakiOffline">
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
                                            posko
                                        </th>
                                        <th
                                            scope="col"
                                            class="text-center text-md font-sans uppercase pb-3 text-white"
                                        >
                                            aksi
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
                                            {{ d.Posko }}
                                        </td>
                                        <td
                                            class="whitespace-nowrap text-sm text-center text-black py-2"
                                        >
                                            <a @click="cetakUlangSBG(d)"
                                                ><img
                                                    class="m-auto cursor-pointer"
                                                    src="/gadaiemas/storage/icon/printerSbg.png"
                                                    title="Cetak"
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
            </main>
        </template>
    </AdminLayout>
</template>