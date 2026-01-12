<script setup>
import {
    ref,
    defineProps,
    defineEmits,
    watch,
    onMounted,
    onBeforeUnmount,
    computed,
} from "vue";

const props = defineProps({
    label: {
        type: String,
        required: true,
    },
    options: {
        type: Array,
        required: true,
    },
    modelValue: {
        type: String,
        default: null,
    },
    disabled: {
        type: Boolean,
        default: false
    },
    requiredIndicator: {
        type: Boolean,
        default: true,
    }
});
const emits = defineEmits(["update:modelValue"]);

const search = ref(props.modelValue || "");
const isOpen = ref(false);
const dropDown = ref(null);

const searchResult = computed(() => {
    if (search.value === "") {
        return props.options;
    }
    return props.options.filter((option) =>
        option.toLowerCase().includes(search.value.toLowerCase())
    );
});

const setSelected = (option) => {
    isOpen.value = false;
    search.value = option;
    emits("update:modelValue", search.value);
};

const handleInput = (event) => {
    search.value = event.target.value;
    emits("update:modelValue", search.value);
};

const openOption = () => {
    isOpen.value = true;
};

const closeOption = (event) => {
    if (!dropDown.value.contains(event.target)) {
        isOpen.value = false;
    }
};

onMounted(() => {
    window.addEventListener("click", closeOption);
});

onBeforeUnmount(() => {
    window.removeEventListener("click", closeOption);
});

watch(
    () => props.modelValue,
    (newValue) => {
        search.value = newValue;
    }
);
</script>

<template>
    <div class="w-full relative">
        <label
            class="tracking-wide block mb-2 text-xs font-bold text-gray-900 uppercase"
            >{{ label }}
            <span v-if="requiredIndicator" class="text-sm text-red-600"
                >*</span
            ></label
        >
        <div ref="dropDown" @click="openOption" class="relative">
            <img
                src="/gadaiemas/storage/icon/down-arrow.png"
                class="absolute right-3 top-4 h-4 w-3.5"
            />
            <input
                type="text"
                :value="search"
                :disabled="disabled"
                :required="requiredIndicator"
                @input="handleInput"
                class="py-2 w-full border border-black rounded-md cursor-default"
                placeholder="Pilih terlebih dahulu..."
            />
        </div>
        <ul
            class="mt-1 w-full max-h-60 border border-black rounded-md bg-white absolute overflow-y-auto"
            v-show="searchResult.length && isOpen"
        >
            <li
                class="px-4 py-3 border-b border-black text-black cursor-pointer hover:bg-gray-100 transition-colors"
                v-for="option in searchResult"
                :key="option"
                @click="setSelected(option)"
            >
                {{ option }}
            </li>
        </ul>
    </div>
</template>
