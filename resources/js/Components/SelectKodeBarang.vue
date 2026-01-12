<script setup>
import { ref, onMounted, onBeforeUnmount } from "vue";

const props = defineProps({
    source: {
        type: Array,
        required: true,
        default: () => [],
    },
    modelValue: String,
    label: String,
});

const emit = defineEmits(["update:modelValue"]);

const dropDown = ref(null);
const isOpen = ref(false);

const setSelected = (option) => {
    isOpen.value = false;
    emit("update:modelValue", option.Kode_barang);
};

const openOption = () => {
    isOpen.value = true;
};

const closeOption = (event) => {
    if (dropDown.value && !dropDown.value.contains(event.target)) {
        isOpen.value = false;
    }
};

onMounted(() => {
    window.addEventListener("click", closeOption);
});

onBeforeUnmount(() => {
    window.removeEventListener("click", closeOption);
});
</script>

<template>
    <div class="w-full relative">
        <label
            class="tracking-wide block mb-2 text-xs font-bold text-gray-900 uppercase"
            >{{ label }}</label
        >
        <div ref="dropDown" @click="openOption" class="relative">
            <img
                src="/gadaiemas/storage/icon/down-arrow.png"
                class="absolute right-3 my-3.5 h-4 w-3.5"
            />
            <div
                class="py-2 pl-4 w-full border border-black rounded-md cursor-pointer shadow-sm focus:ring-amber-600 focus:border-amber-600"
            >
                {{ modelValue ? modelValue : "Pilih..." }}
            </div>
        </div>
        <ul
            class="mt-1 w-full max-h-60 border border-black rounded-md bg-white absolute overflow-y-auto"
            v-show="isOpen"
        >
            <li
                class="px-4 py-3 border-b border-black text-black cursor-pointer hover:bg-gray-100 transition-colors"
                v-for="(option, index) in source"
                :key="option.Kode_barang"
                @click="setSelected(option)"
            >
                Barang ke - {{ index + 1 }}
            </li>
        </ul>
    </div>
</template>

<style scoped>
.w-full {
    width: 100%;
}

.relative {
    position: relative;
}

.mt-1 {
    margin-top: 0.25rem;
}
</style>
