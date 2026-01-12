<script setup>
import { ref, computed } from "vue";

const props = defineProps([
    "modelValue",
    "label",
    "id",
    "type",
    "placeholder",
    "disabled",
    "subClass",
    "btnClass",
    "label",
]);
defineEmits(["update:modelValue"]);

// Computed class for button based on btnClass condition
const computedBtnClass = computed(() => {
    return [
        props.btnClass === "w-full"
            ? "absolute right-2 top-10 transform -translate-y-1/2"
            : "absolute start-44",
        "p-1.5 text-sm font-medium text-white rounded-e-lg",
    ];
});
</script>

<template>
    <div class="relative">
        <label
            class="tracking-wide block mb-2 text-xs font-bold text-gray-900 uppercase"
            >{{ label }}</label
        >
        <div
            class="absolute inset-y-0 start-0 flex items-center ps-3 pointer-events-none"
        ></div>
        <input
            type="search"
            :id="id"
            :value="modelValue"
            :class="subClass"
            :placeholder="placeholder"
            @input="$emit('update:modelValue', $event.target.value)"
            class="p-2 ps-7 pe-8 text-sm text-center rounded-md text-black border-black shadow-sm focus:ring-amber-600 focus:border-amber-600 sm:text-sm"
            placeholder="Search"
            required
        />
        <button type="submit" disabled :class="computedBtnClass">
            <i><img src="/gadaiemas/storage/icon/search-bar.png" /></i>
        </button>
    </div>
</template>
