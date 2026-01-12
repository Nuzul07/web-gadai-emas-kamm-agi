<script setup>
import { ref, onMounted, watch, computed, reactive } from "vue";
import AdminLayout from "@/Layouts/AdminLayout.vue";
import TitlePage from "@/Components/TitlePage.vue";
import SearchInput from "@/Components/SearchInput.vue";
import CustTextInput from "@/Components/CustTextInput.vue";
import NestedSelect from "@/Components/NestedSelect.vue";
import Modal from "@/Components/DialogModal.vue";
import { Head, useForm } from "@inertiajs/vue3";
import axios from "axios";
import Swal from "sweetalert2";

onMounted(() => {
    userData();
    poskoData();
});

defineProps({
    errors: Object,
});

const dropdownStates = ref({
    poskoState: false,
});

const form = useForm({
    user_id: "",
    user_name: "",
    user_password: "",
    level_user: "",
    posko: "",
    cabang: "",
    posko_sel: "",
    cabang_sel: "",

    //posko
    kd_posko: "",
    kd_cabang: "",
    ket_posko: "",
    ket_cabang: "",
});

const formDtl = useForm({
    user_id: "",
    user_name: "",
    user_password: "",
    level_user: "",
    posko: "",
    cabang: "",
});

const modal = reactive({
    addUser: false,
    detailUser: false,
    addPosko: false,
});

const modalAddUser = () => {
    modal.addUser = true;
};

const modalDetailUser = (d) => {
    selectDataUser(d);
    modal.detailUser = true;
};

const modalAddPosko = () => {
    modal.addPosko = true;
};

const selectDataUser = (d) => {
    form.user_id = d.USER_ID;
    form.user_name = d.USER_NAME;
    form.user_password = d.USER_PASSWORD;
    form.level_user = d.LEVEL_USER;
    form.posko = d.POSKO;
    form.cabang = d.CABANG;
    //
    formDtl.user_id = d.USER_ID;
    formDtl.user_name = d.USER_NAME;
    formDtl.user_password = d.USER_PASSWORD;
    formDtl.level_user = d.LEVEL_USER;
    form.posko = d.POSKO;
    form.cabang = d.CABANG;
};

const formPoskoReset = () => {
    form.kd_cabang = "";
    form.kd_posko = "";
    form.ket_cabang = "";
    form.ket_posko = "";
};

const poskoOptions = ref([]);

const posko = ref([]);
const poskoData = async () => {
    await axios.get(route("getPosko")).then((res) => {
        const data = res.data;
        posko.value = data;
        console.log(posko.value)
        updatePoskoOptions()
    });
};

const updatePoskoOptions = () => {
    const poskoSet = new Set(posko.value.map((item) => item.kd_posko));
    poskoOptions.value = [...poskoSet];
};

// Watch for changes to `kd_posko` and update `kd_cabang` automatically
watch(() => form.posko_sel, (newPosko) => {
    const selectedItem = posko.value.find(item => item.kd_posko === newPosko);
    form.cabang_sel = selectedItem ? selectedItem.kd_cabang : null;
});

const user = ref([]);
const isUserEmpty = computed(() => user.value.length === 0);
const userLoading = ref(false);
const userData = async () => {
    userLoading.value = true;
    await axios.get(route("getUser")).then((res) => {
        const data = res.data;
        user.value = data;
        userLoading.value = false;
    });
};

const crntPage = ref(1);
const perPage = 5;

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
                usr.USER_ID.toLowerCase().includes(lowerCase) ||
                usr.USER_NAME.toLowerCase().includes(lowerCase) ||
                usr.LEVEL_USER.toLowerCase().includes(lowerCase)
            );
        });
    } else {
        return [];
    }
});

const updateAktif = async (d) => {
    form.user_id = d.USER_ID;
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
        await axios.post(route("updateAktif"), { user_id: form.user_id });

        // Close the loading alert and show success message
        Swal.fire({
            title: "Berhasil",
            text: "User berhasil diaktifkan",
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

        // Refresh user data
        userData();
    } catch (error) {
        console.log(error);

        // Show error message
        Swal.fire({
            title: "Terjadi kesalahan",
            text: "Tidak dapat mengaktifkan user",
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

const updateNonAktif = async (d) => {
    form.user_id = d.USER_ID;
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
        await axios.post(route("updateNonAktif"), {
            user_id: form.user_id,
        });

        // Close the loading alert and show success message
        Swal.fire({
            title: "Berhasil",
            text: "User berhasil dinonaktifkan",
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

        // Refresh user data
        userData();
    } catch (error) {
        console.log(error);

        // Show error message
        Swal.fire({
            title: "Terjadi kesalahan",
            text: "Tidak dapat menonaktifkan user",
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

//Level user checkBox
const selectedRole = ref("");

const updateLevelUser = () => {
    form.level_user = selectedRole.value;
};

watch(selectedRole, updateLevelUser);
//end

//store user
const store = async () => {
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
            await axios.post(route("storeUser"), form);

            // Close the loading alert and show success message
            Swal.fire({
                title: "Berhasil",
                text: "User Ditambahkan",
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
            userData(); // Refresh user data
            form.reset(); // Reset the form
            selectedRole.value = ""; // Reset the selected role
            modal.addUser = false; // Close the modal
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
        }
    }
};
//end

//button update disabled
const formMatch = ref(false);
watch(
    [() => form.user_name, () => form.user_password],
    ([newUserName, newPass]) => {
        formMatch.value =
            newUserName == formDtl.user_name && newPass == formDtl.user_password
                ? true
                : false;
    }
);
//end

//update user
const update = async () => {
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
            await axios.post(route("updateUser"), form);

            // Close the loading alert and show success message
            Swal.fire({
                title: "Berhasil",
                text: "User Diperbarui",
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
            userData(); // Refresh user data
            form.reset() // Reset the form details
            modal.detailUser = false; // Close the modal
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
        }
    }
};
//end

// store posko
const storePosko = async () => {
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
            await axios.post(route("storePosko"), form);

            // Close the loading alert and show success message
            Swal.fire({
                title: "Berhasil",
                text: "Posko Ditambahkan",
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
            poskoData(); // Refresh user data
            formPoskoReset(); // Reset the form details
            modal.addPosko = false; // Close the modal
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
        }
    }
};
//end

// delete user
const selectedDelete = ref([])

const deleteSelected = async () => {
    if (selectedDelete.value.length === 0) {
        Swal.fire('No items selected', 'Please select at least one item.', 'warning');
        return;
    }

    const result = await Swal.fire({
        title: `Apakah anda yakin untuk menghapus ${selectedDelete.value.length} data?`,
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

            const payload = {
                user_id: selectedDelete.value.map(item => item.USER_ID)
            }

             // Perform the API request
            await axios.post(route("deleteUser"), payload);

            // Close the loading alert and show success message
            Swal.fire({
                title: "Berhasil",
                text: "User berhasil dihapus",
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

            userData()
            selectedDelete.value = []

        } catch (error) {
             // Show error message
            Swal.fire({
                title: "Terjadi kesalahan",
                text: "Silahkan hubungi Divisi IT untuk perbaikan",
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
//end

</script>

<template>
    <Head title="Users" />
    <AdminLayout>
        <template #titlePage>
            <TitlePage value="Data Users" />
        </template>
        <template #content>
            <main class="p-6 bg-gray-100 flex-grow overflow-y-auto">
                <div class="bg-white p-4 rounded-lg shadow">
                    <div class="flex justify-between items-center mb-4">
                        <div class="pr-2">
                            <SearchInput
                                label="Cari Data"
                                v-model="searchIUser"
                                placeholder="Cari"
                            ></SearchInput>
                        </div>
                        <div class="ml-4">
                            <div class="text-black">
                                <button
                                    class="mt-4 ml-2 bg-sky-700 text-white py-1 px-4 rounded"
                                    @click="modalAddUser()"
                                >
                                    Tambah
                                </button>
                                <button
                                    class="mt-4 ml-2 bg-red-700 text-white py-1 px-4 rounded"
                                    v-show="selectedDelete.length > 0"
                                    @click="deleteSelected()"
                                >
                                    Hapus
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
                                        level user
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
                                        cabang
                                    </th>
                                    <th
                                        scope="col"
                                        class="text-center text-md font-sans uppercase pb-3 text-white"
                                    >
                                        status
                                    </th>
                                    <th
                                        scope="col"
                                        class="text-center text-md font-sans uppercase pb-3 text-white"
                                    >
                                        detail
                                    </th>
                                    <th
                                        scope="col"
                                        class="text-center text-md font-sans uppercase pb-3 text-white"
                                    >
                                        hapus
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
                                        {{ d.USER_ID }}
                                    </td>
                                    <td
                                        class="whitespace-nowrap text-sm text-center text-black py-2"
                                    >
                                        {{ d.USER_NAME }}
                                    </td>
                                    <td
                                        class="whitespace-nowrap text-sm text-center text-black py-2"
                                    >
                                        {{ d.LEVEL_USER }}
                                    </td>
                                    <td
                                        class="whitespace-nowrap text-sm text-center text-black py-2"
                                    >
                                        {{ d.POSKO }}
                                    </td>
                                    <td
                                        class="whitespace-nowrap text-sm text-center text-black py-2"
                                    >
                                        {{ d.CABANG }}
                                    </td>
                                    <td
                                        class="whitespace-nowrap text-sm text-center text-black py-2"
                                        v-if="d.AKTIF == 1"
                                    >
                                        <a @click="updateNonAktif(d)"
                                            ><img
                                                class="m-auto cursor-pointer"
                                                src="/gadaiemas/storage/icon/switch-on.png"
                                                title="Tekan tombol ini jika ingin merubah status"
                                        /></a>
                                    </td>
                                    <td
                                        class="whitespace-nowrap text-sm text-center text-black py-2"
                                        v-if="d.AKTIF == 0"
                                    >
                                        <a @click="updateAktif(d)"
                                            ><img
                                                class="m-auto cursor-pointer"
                                                src="/gadaiemas/storage/icon/switch-off.png"
                                                title="Tekan tombol ini jika ingin merubah status"
                                        /></a>
                                    </td>
                                    <td
                                        class="whitespace-nowrap text-sm text-center text-black py-2"
                                    >
                                        <a @click="modalDetailUser(d)"
                                            ><img
                                                class="m-auto cursor-pointer"
                                                src="/gadaiemas/storage/icon/info.png"
                                                title="Detail"
                                        /></a>
                                    </td>
                                    <td
                                        class="whitespace-nowrap text-sm text-center text-black py-2"
                                    >
                                        <input type="checkbox" class="cursor-pointer" :value="d" v-model="selectedDelete">
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
                                v-if="!userLoading && isUserEmpty"
                                >Tidak Ada Data</span
                            >
                        </div>
                        <div class="w-full text-center mt-2">
                            <span 
                                class="text-center text-xl text-white"
                                v-if="
                                    !userLoading && 
                                    !isUserEmpty &&
                                    searchUser.length < 1"
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
                        :show="modal.addUser"
                        @close="[(modal.addUser = false), (form.errors = {})]"
                        maxWidth="2xl"
                        classHeader="bg-sky-700 text-white"
                    >
                        <template #title> Tambah User </template>
                        <template #content>
                            <div class="mt-6 grid grid-cols-2 gap-4">
                                <div class="sm:col-span-1 pt-5">
                                    <h1
                                        class="tracking-wide block mb-2 text-xs font-bold text-gray-900 uppercase"
                                    >
                                        User ID :
                                    </h1>
                                </div>
                                <div class="sm:col-span-1">
                                    <CustTextInput
                                        :requiredIndicator="false"
                                        id="user_id"
                                        type="text"
                                        name="user_id"
                                        v-model="form.user_id"
                                    />
                                    <span class="text-sm text-black"
                                        >* Format : gdeg_+level_+posko,
                                        "gdeg_kasir_G09"</span
                                    >
                                    <div v-if="form.errors.user_id">
                                        <span class="text-sm text-red-600"
                                            >*{{ form.errors.user_id[0] }}</span
                                        >
                                    </div>
                                </div>
                                <div class="sm:col-span-1 pt-5">
                                    <h1
                                        class="tracking-wide block mb-2 text-xs font-bold text-gray-900 uppercase"
                                    >
                                        User Name :
                                    </h1>
                                </div>
                                <div class="sm:col-span-1">
                                    <CustTextInput
                                        :requiredIndicator="false"
                                        id="user_name"
                                        type="text"
                                        name="user_name"
                                        v-model="form.user_name"
                                    />
                                    <div v-if="form.errors.user_name">
                                        <span class="text-sm text-red-600"
                                            >*{{
                                                form.errors.user_name[0]
                                            }}</span
                                        >
                                    </div>
                                </div>
                                <div class="sm:col-span-1 pt-5">
                                    <h1
                                        class="tracking-wide block mb-2 text-xs font-bold text-gray-900 uppercase"
                                    >
                                        Password :
                                    </h1>
                                </div>
                                <div class="sm:col-span-1">
                                    <CustTextInput
                                        :requiredIndicator="false"
                                        id="password"
                                        type="text"
                                        name="password"
                                        v-model="form.user_password"
                                    />
                                    <div v-if="form.errors.user_password">
                                        <span class="text-sm text-red-600"
                                            >*{{
                                                form.errors.user_password[0]
                                            }}</span
                                        >
                                    </div>
                                </div>

                                <div class="sm:col-span-1 pt-5">
                                    <h1
                                        class="tracking-wide block mb-2 text-xs font-bold text-gray-900 uppercase"
                                    >
                                        Pilih Posko :
                                    </h1>
                                </div>
                                <div class="sm:col-span-1">
                                    <NestedSelect
                                        :options="poskoOptions"
                                        v-model="form.posko_sel"
                                        @focusin="
                                            dropdownStates.poskoState = true;
                                            form.posko_sel = '';
                                            form.cabang_sel = '';
                                        "
                                        @focusout="
                                            dropdownStates.poskoState = false
                                        "
                                        :requiredIndicator="false"
                                    />
                                    <div v-if="form.errors.posko_sel">
                                        <span class="text-sm text-red-600"
                                            >*{{
                                                form.errors.posko_sel[0]
                                            }}</span
                                        >
                                    </div>
                                </div>

                                <!-- Radio Question 2 -->
                                <div class="sm:col-span-1 pt-4">
                                    <h1
                                        class="tracking-wide block text-xs font-bold text-gray-900 uppercase"
                                    >
                                        Pilih Level User
                                    </h1>
                                </div>
                                <div class="sm:col-span-1 mt-2">
                                    <input
                                        type="checkbox"
                                        :checked="selectedRole === 'pusat'"
                                        @change="selectedRole = 'pusat'"
                                    />
                                    Pusat
                                    <input
                                        class="ml-5"
                                        type="checkbox"
                                        :checked="selectedRole === 'super'"
                                        @change="selectedRole = 'super'"
                                    />
                                    Supervisor
                                    <input
                                        class="ml-5"
                                        type="checkbox"
                                        :checked="selectedRole === 'kasir'"
                                        @change="selectedRole = 'kasir'"
                                    />
                                    Kasir
                                </div>
                                <div class="sm:col-span-1 pt-5">
                                    <h1
                                        class="tracking-wide block mb-2 text-xs font-bold text-gray-900 uppercase"
                                    >
                                        Level User :
                                    </h1>
                                </div>
                                <div class="sm:col-span-1">
                                    <CustTextInput
                                        :requiredIndicator="false"
                                        id="level_user"
                                        type="text"
                                        name="level_user"
                                        v-model="form.level_user"
                                        disabled="true"
                                    />
                                    <div v-if="form.errors.level_user">
                                        <span class="text-sm text-red-600"
                                            >*{{
                                                form.errors.level_user[0]
                                            }}</span
                                        >
                                    </div>
                                </div>
                            </div>
                            <div class="flex justify-end items-center mb-24">
                                <div class="ml-4">
                                    <div class="text-black">
                                        <button
                                            @click="modalAddPosko"
                                            class="mt-4 ml-2 bg-sky-700 text-white py-1 px-4 rounded"
                                        >
                                            Tambah Posko
                                        </button>
                                        <button
                                            @click="store()"
                                            class="mt-4 ml-2 bg-green-700 text-white py-1 px-4 rounded"
                                        >
                                            Simpan
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </template>
                    </Modal>

                    <Modal
                        :show="modal.addPosko"
                        @close="[(modal.addPosko = false), (form.errors = {})]"
                        maxWidth="2xl"
                        classHeader="bg-sky-700 text-white"
                    >
                        <template #title> Tambah Posko </template>
                        <template #content>
                            <div class="mt-6 grid grid-cols-2 gap-4">
                                <div class="sm:col-span-1 pt-5">
                                    <h1
                                        class="tracking-wide block mb-2 text-xs font-bold text-gray-900 uppercase"
                                    >
                                        Kode Posko :
                                    </h1>
                                </div>
                                <div class="sm:col-span-1">
                                    <CustTextInput
                                        :requiredIndicator="false"
                                        id="kd_posko"
                                        type="text"
                                        name="kd_posko"
                                        v-model="form.kd_posko"
                                        :capital="true"
                                    />
                                    <div v-if="form.errors.kd_posko">
                                        <span class="text-sm text-red-600"
                                            >*{{ form.errors.kd_posko[0] }}</span
                                        >
                                    </div>
                                </div>
                                <div class="sm:col-span-1 pt-5">
                                    <h1
                                        class="tracking-wide block mb-2 text-xs font-bold text-gray-900 uppercase"
                                    >
                                        Kode Cabang:
                                    </h1>
                                </div>
                                <div class="sm:col-span-1">
                                    <CustTextInput
                                        :requiredIndicator="false"
                                        id="kd_cabang"
                                        type="text"
                                        name="kd_cabang"
                                        v-model="form.kd_cabang"
                                        :capital="true"
                                    />
                                    <div v-if="form.errors.kd_cabang">
                                        <span class="text-sm text-red-600"
                                            >*{{
                                                form.errors.kd_cabang[0]
                                            }}</span
                                        >
                                    </div>
                                </div>
                                <div class="sm:col-span-1 pt-5">
                                    <h1
                                        class="tracking-wide block mb-2 text-xs font-bold text-gray-900 uppercase"
                                    >
                                        Keterangan Posko:
                                    </h1>
                                </div>
                                <div class="sm:col-span-1">
                                    <CustTextInput
                                        :requiredIndicator="false"
                                        id="ket_posko"
                                        type="text"
                                        name="ket_posko"
                                        v-model="form.ket_posko"
                                        :capital="true"
                                    />
                                    <div v-if="form.errors.ket_posko">
                                        <span class="text-sm text-red-600"
                                            >*{{
                                                form.errors.ket_posko[0]
                                            }}</span
                                        >
                                    </div>
                                </div>
                                <div class="sm:col-span-1 pt-5">
                                    <h1
                                        class="tracking-wide block mb-2 text-xs font-bold text-gray-900 uppercase"
                                    >
                                        Keterangan Cabang:
                                    </h1>
                                </div>
                                <div class="sm:col-span-1">
                                    <CustTextInput
                                        :requiredIndicator="false"
                                        id="ket_cabang"
                                        type="text"
                                        name="ket_cabang"
                                        v-model="form.ket_cabang"
                                        :capital="true"
                                    />
                                    <div v-if="form.errors.ket_cabang">
                                        <span class="text-sm text-red-600"
                                            >*{{
                                                form.errors.ket_cabang[0]
                                            }}</span
                                        >
                                    </div>
                                </div>
                            </div>
                            <div class="flex justify-end items-center mb-24">
                                <div class="ml-4">
                                    <div class="text-black">
                                        <button
                                            @click="modal.addPosko = false"
                                            class="mt-4 ml-2 bg-red-700 text-white py-1 px-4 rounded"
                                        >
                                            Batal
                                        </button>
                                        <button
                                            @click="storePosko()"
                                            class="mt-4 ml-2 bg-green-700 text-white py-1 px-4 rounded"
                                        >
                                            Simpan
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </template>
                    </Modal>

                    <Modal
                        :show="modal.detailUser"
                        @close="[(modal.detailUser = false), form.reset()]"
                        maxWidth="2xl"
                        classHeader="bg-sky-700 text-white"
                    >
                        <template #title> Detail User </template>
                        <template #content>
                            <div class="mt-6 grid grid-cols-2 gap-4">
                                <div class="sm:col-span-1 pt-5">
                                    <h1
                                        class="tracking-wide block mb-2 text-xs font-bold text-gray-900 uppercase"
                                    >
                                        User ID :
                                    </h1>
                                </div>
                                <div class="sm:col-span-1">
                                    <CustTextInput
                                        :requiredIndicator="false"
                                        id="user_id_dtl"
                                        type="text"
                                        name="user_id_dtl"
                                        v-model="form.user_id"
                                        disabled="true"
                                    />
                                </div>
                                <div class="sm:col-span-1 pt-5">
                                    <h1
                                        class="tracking-wide block mb-2 text-xs font-bold text-gray-900 uppercase"
                                    >
                                        User Name :
                                    </h1>
                                </div>
                                <div class="sm:col-span-1">
                                    <CustTextInput
                                        :requiredIndicator="false"
                                        id="user_name_dtl"
                                        type="text"
                                        name="user_name_dtl"
                                        v-model="form.user_name"
                                    />
                                </div>
                                <div class="sm:col-span-1 pt-5">
                                    <h1
                                        class="tracking-wide block mb-2 text-xs font-bold text-gray-900 uppercase"
                                    >
                                        Password :
                                    </h1>
                                </div>
                                <div class="sm:col-span-1">
                                    <CustTextInput
                                        :requiredIndicator="false"
                                        id="password_dtl"
                                        type="text"
                                        name="password_dtl"
                                        v-model="form.user_password"
                                    />
                                </div>
                                <div class="sm:col-span-1 pt-5">
                                    <h1
                                        class="tracking-wide block mb-2 text-xs font-bold text-gray-900 uppercase"
                                    >
                                        Level User :
                                    </h1>
                                </div>
                                <div class="sm:col-span-1">
                                    <CustTextInput
                                        :requiredIndicator="false"
                                        id="level_user_dtl"
                                        type="text"
                                        name="level_user_dtl"
                                        v-model="form.level_user"
                                        disabled="true"
                                    />
                                </div>
                                <div class="sm:col-span-1 pt-5">
                                    <h1
                                        class="tracking-wide block mb-2 text-xs font-bold text-gray-900 uppercase"
                                    >
                                        Posko :
                                    </h1>
                                </div>
                                <div class="sm:col-span-1">
                                    <CustTextInput
                                        :requiredIndicator="false"
                                        id="level_user_dtl"
                                        type="text"
                                        name="level_user_dtl"
                                        v-model="form.posko"
                                        disabled="true"
                                    />
                                </div>
                                <div class="sm:col-span-1 pt-5">
                                    <h1
                                        class="tracking-wide block mb-2 text-xs font-bold text-gray-900 uppercase"
                                    >
                                        Cabang :
                                    </h1>
                                </div>
                                <div class="sm:col-span-1">
                                    <CustTextInput
                                        :requiredIndicator="false"
                                        id="level_user_dtl"
                                        type="text"
                                        name="level_user_dtl"
                                        v-model="form.cabang"
                                        disabled="true"
                                    />
                                </div>
                            </div>
                            <div class="flex justify-end items-center mb-7">
                                <div class="ml-4">
                                    <div class="text-black">
                                        <button
                                            @click="update()"
                                            :disabled="formMatch"
                                            :class="{ hidden: formMatch }"
                                            :title="
                                                formMatch
                                                    ? 'You dont changed any data'
                                                    : 'Click to update data'
                                            "
                                            class="mt-7 ml-2 bg-green-700 text-white py-1 px-4 rounded"
                                        >
                                            Ubah
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