<script setup>
import { ref, onMounted, watch, computed, reactive } from "vue";
import AdminLayout from "@/Layouts/AdminLayout.vue";
import TitlePage from "@/Components/TitlePage.vue";
import SearchInput from "@/Components/SearchInput.vue";
import Accordion from "@/Components/Accordion.vue";
import { Head, useForm } from "@inertiajs/vue3";
import axios, { formToJSON } from "axios";
import Swal from "sweetalert2";
import dayjs from "dayjs";
import duration from "dayjs/plugin/duration";
import { selectedTipeGadai } from "@/src/store";

dayjs.extend(duration);

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

    // Step 1: Apply pagination
    const paginatedData = searchGadai.value
        .slice(start, end)
        .map((gadai, index) => ({
            ...gadai,
            no: start + index + 1,
        }));

    // Step 2: Group paginated data by base `no_sbg`
    const groups = {};
    paginatedData.forEach((item) => {
        const baseNo = item.No_sbg.replace(/P\d+$/, ""); // Remove suffix if any

        if (!groups[baseNo]) {
            groups[baseNo] = {
                No_sbg: baseNo,
                nama: item.nasabah.Nama,
                items: [],
            };
        }
        groups[baseNo].items.push(item);
    });

    // Step 3: Convert groups object into an array for easy rendering in the template
    return Object.values(groups);
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

const generatePDF = async (nosbg, produk_gadai) => {
    try {
        // Show "please wait" SweetAlert2 message
        Swal.fire({
            title: "Mohon Tunggu...",
            text: "Membuat Kartu Piutang",
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

        const response = await axios.get(route("generatepdfPiutang"), {
            responseType: "blob",
            params: {
                no_sbg: nosbg,
                produk_gadai: produk_gadai,
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
                text: "Kartu Piutang anda siap untuk dicetak.",
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
            text: "Terjadi kesalahan saat membuat Kartu Piutang.",
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
    <Head title="Kartu Piutang" />
    <AdminLayout>
        <template #titlePage>
            <TitlePage value="Kartu Piutang" />
        </template>
        <template #content>
            <main class="p-6 bg-gray-100 flex-grow overflow-y-auto">
                <div class="mt-6">
                    <div class="bg-white p-4 rounded-lg shadow">
                        <SearchInput
                            label="Cari Data"
                            placeholder="Masukkan No Sbg"
                            subClass="w-full"
                            btnClass="w-full"
                            v-model="searchIGad"
                        ></SearchInput>
                    </div>
                    <div
                        v-if="!gadaiLoading"
                        class="mt-6"
                        v-for="(gadai, index) in pgntGadai"
                        :key="index"
                    >
                        <Accordion
                            :title="
                                gadai.No_sbg +
                                '&nbsp;&nbsp' +
                                '(' +
                                gadai.nama +
                                ')'
                            "
                            :index="index"
                        >
                            <div
                                v-for="(item, index) in gadai.items"
                                :key="item.No_sbg"
                            >
                                <div class="flex justify-between my-2">
                                    <p>{{ item.Produk_gadai }}</p>
                                    <a
                                        @click="
                                            generatePDF(
                                                item.No_sbg,
                                                item.Produk_gadai
                                            )
                                        "
                                        ><img
                                            src="/gadaiemas/storage/icon/printerSbg.png"
                                            title="Cetak Kartu"
                                            class="w-6 h-6 cursor-pointer"
                                    /></a>
                                </div>
                                <hr
                                    v-if="index < gadai.items.length - 1"
                                    class="border-2"
                                />
                            </div>
                        </Accordion>
                    </div>
                    <div class="w-full text-center mt-2">
                        <span
                            class="text-center text-xl text-black"
                            v-if="gadaiLoading"
                            >Sedang Memuat...</span
                        >
                    </div>
                    <div class="w-full text-center mt-2">
                        <span
                            class="text-center text-xl text-black"
                            v-if="!gadaiLoading && isGadaiEmpty"
                            >Tidak Ada Data</span
                        >
                    </div>
                    <div class="w-full text-center mt-2">
                        <span
                            class="text-center text-xl text-black"
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
                    <button @click="nextPage" v-show="crntPage < totalPages">
                        <i
                            ><img
                                src="/gadaiemas/storage/icon/next.png"
                                title="Next"
                        /></i>
                    </button>
                </div>
            </main>
        </template>
    </AdminLayout>
</template>