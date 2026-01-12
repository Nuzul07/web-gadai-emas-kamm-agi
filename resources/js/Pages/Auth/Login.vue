<script setup>
import GuestLayout from "@/Layouts/GuestLayout.vue";
import InputError from "@/Components/InputError.vue";
import { Head, Link, useForm } from "@inertiajs/vue3";
import Swal from "sweetalert2";

defineProps({
    canResetPassword: {
        type: Boolean,
    },
    status: {
        type: String,
    },
});

const form = useForm({
    user_id: "",
    user_password: "",
    latitude: null,
    longtitude: null,
});

const getLocation = () => {
    return new Promise((resolve, reject) => {
        if (navigator.geolocation) {
            navigator.geolocation.getCurrentPosition(
                (position) => {
                    resolve({
                        latitude: position.coords.latitude,
                        longtitude: position.coords.longitude,
                    });
                },
                (error) => {
                    reject(error);
                }
            );
        } else {
            reject(new Error("Browser ini tidak mendukung lokasi"));
        }
    });
};

const submit = async () => {
    try {
        // Attempt to get the location
        const location = await getLocation();
        form.latitude = location.latitude;
        form.longtitude = location.longtitude;
    } catch (error) {
        console.warn("Location not available or permission denied:", error);
        // If location is not available, set latitude and longtitude to null
        form.latitude = null;
        form.longtitude = null;
    }

    if (form.user_id !== "" && form.user_password !== "") {
        form.post(route("login"), {
            onError: (errors) => {
                console.log(errors); // For debugging
                if (errors && errors.user_id) {
                    if (errors.user_id === "Akun sudah tidak aktif") {
                        Swal.fire({
                            icon: "error",
                            title: "Akun Tidak Aktif",
                            text: "Silakan hubungi pusat untuk mengaktifkan kembali.",
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
                    } else if (
                        errors.user_id === "Username atau Password Salah"
                    ) {
                        Swal.fire({
                            icon: "error",
                            title: "Username atau Password Salah",
                            text: "Ketikan dengan benar username dan password anda!",
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
            },
            onSuccess: () => {
                localStorage.removeItem('radioVisible')
                Swal.fire({
                    imageUrl: "/gadaiemas/storage/image/Kim You Jung.jpg",
                    imageWidth: 200,
                    imageHeight: 200,
                    imageAlt: "Custom image",
                    title: "Selamat Datang",
                    html: "Semangat jalanin harinya yaa...😊 <br> Jangan lupa pilih produk gadainya terlebih dahulu, sebelum memulai sistem",
                });
            },
        });
    } else {
        Swal.fire({
            icon: "error",
            title: "Terjadi kesalahan",
            text: " Username dan Password tidak boleh kosong!.",
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
    // Proceed with form submission even if location is not available
};
</script>

<template>
    <GuestLayout>
        <Head title="Log in" />

        <div v-if="status" class="mb-4 font-medium text-sm text-green-600">
            {{ status }}
        </div>
        <div class="flex items-center justify-center">
            <div class="bg-white w-full rounded-lg shadow-lg flex flex-row overflow-hidden">
                <!-- Image Section -->
                <div class="w-full md:w-1/2 p-16 bg-amber-400">
                    <img
                        src="/gadaiemas/storage/image/pawnXpert-logo.png"
                        alt="Placeholder Image"
                        class="w-full h-full object-contain"
                    />
                </div>
                <!-- Form Section -->
                <div class="w-full md:w-1/2 py-20 px-12">
                    <form @submit.prevent="submit" class="space-y-6">
                        <div class="text-2xl font-bold mb-4">
                            Masuk untuk melanjutkan!
                        </div>
                        <div>
                            <label for="user_id" class="block text-sm font-medium text-black">Username</label>
                            <input
                                type="text"
                                id="user_id"
                                name="user_id"
                                v-model="form.user_id"
                                class="mt-1 block w-full rounded-md border-black shadow-sm focus:ring-amber-600 focus:border-amber-600 sm:text-sm"
                            />
                        </div>
                        <div>
                            <label for="user_password" class="block text-sm font-medium text-black">Password</label>
                            <input
                                type="password"
                                id="user_password"
                                name="user_password"
                                v-model="form.user_password"
                                class="mt-1 block w-full rounded-md border-black shadow-sm focus:ring-amber-600 focus:border-amber-600 sm:text-sm"
                            />
                        </div>
                        <div>
                            <button
                                type="submit"
                                class="w-full flex justify-center py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-amber-600 hover:bg-amber-500 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-amber-500"
                            >
                                Masuk
                            </button>
                        </div>
                        <div class="text-1xl text-center mb-4">
                            Powered by AMM Company &copy; 2025
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </GuestLayout>
</template>
