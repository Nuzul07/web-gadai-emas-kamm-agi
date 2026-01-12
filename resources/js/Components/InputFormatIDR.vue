<script setup>
import { defineProps, defineEmits, computed, ref, watch } from "vue";

const props = defineProps({
    id: {
        type: String,
        required: true,
    },
    type: {
        type: String,
        required: true,
    },
    name: {
        type: String,
        required: true,
    },
    label: {
        type: String,
        required: true,
    },
    readonly: {
        type: Boolean,
        default: false,
    },
    disabled: {
        type: Boolean,
        default: false,
    },
    modelValue: {
        type: String,
        required: true,
    },
    requiredIndicator: {
        type: Boolean,
        default: true,
    },
});

const emits = defineEmits(["update:modelValue"]);

const inputClass = computed(() => ({
    "mt-1 block w-full rounded-md border-black shadow-sm focus:ring-amber-600 focus:border-amber-600 sm:text-sm": true,
    "mt-1 block w-full rounded-md border-black shadow-sm focus:ring-amber-600 focus:border-amber-600 sm:text-sm bg-gray-300":
        props.disabled,
}));

const displayValue = ref(props.modelValue);

const formatToIDR = (value) => {
    if (!value) return "";
    const isNegative = value.startsWith('-'); // Check if the value is negative
    value = value.replace(/[^\d]/g, ""); // Remove non-numeric characters except the negative sign
    let formattedValue = parseInt(value, 10).toLocaleString("id-ID", {
        style: "currency",
        currency: "IDR",
        minimumFractionDigits: 0,
        maximumFractionDigits: 0,
    });

    return isNegative ? `- ${formattedValue}` : formattedValue;
};

const updateValue = (event) => {
    const rawValue = event.target.value.replace(/[^0-9-]/g, ""); // Allow negative sign
    const formattedValue = formatToIDR(rawValue);
    displayValue.value = formattedValue;
    emits("update:modelValue", rawValue);
};

// Watch the modelValue prop and update the display value
watch(
    () => props.modelValue,
    (newValue) => {
        displayValue.value = formatToIDR(newValue);
    }
);

// Watch the displayValue to update the modelValue if necessary
watch(displayValue, (newDisplayValue) => {
    const rawValue = newDisplayValue.replace(/[^0-9-]/g, ""); // Keep the negative sign if present
    if (props.modelValue !== rawValue) {
        emits("update:modelValue", rawValue);
    }
});
</script>

<template>
    <div>
        <label
            :for="id"
            class="tracking-wide block mb-2 text-xs font-bold text-gray-900 uppercase"
        >
            {{ label }}
            <span v-if="requiredIndicator" class="text-sm text-red-600">*</span>
        </label>
        <input
            :type="type"
            :id="id"
            :name="name"
            v-model="displayValue"
            :readonly="readonly"
            :disabled="disabled"
            :required="requiredIndicator"
            @input="updateValue"
            :class="inputClass"
        />
    </div>
</template>
