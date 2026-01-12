<script setup>
import { defineProps, defineEmits, computed } from "vue";

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
    ket: {
        type: String,
        default: "",
    },
    onlyNum: {
        type: Boolean,
        default: false,
    },
    subClass: {
        type: String,
        default: "", // Default to an empty string if no class is provided
    },
    capital: {
        type: Boolean,
        default: false,
    },
    lower: {
        type: Boolean,
        default: false,
    }
});

const emits = defineEmits(["update:modelValue"]);

const inputClass = computed(() => ({
    "mt-1 block w-full rounded-md border-black shadow-sm focus:ring-amber-600 focus:border-amber-600 sm:text-sm": true,
    "bg-gray-300": props.disabled,
    [props.subClass]: !!props.subClass,
}));

const updateValue = (event) => {
    let value = event.target.value;
    if (props.capital) {
        value = value.toUpperCase();
        event.target.value = value; // Ensure input value is also updated in UI
    } else if (props.lower) {
        value = value.toLowerCase();
        event.target.value = value;
    }
    emits("update:modelValue", value);
};

const allowOnlyNumbers = (event) => {
    const allowedKeys = ["Backspace", "ArrowLeft", "ArrowRight", "Tab"];
    if (!/^\d$/.test(event.key) && !allowedKeys.includes(event.key)) {
        event.preventDefault();
    }
};

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
            v-if="ket"
            :type="type"
            :id="id"
            :name="name"
            :value="`${modelValue} ${ket}`"
            :readonly="readonly"
            :disabled="disabled"
            :required="requiredIndicator"
            @input="updateValue"
            :class="inputClass"
            @dblclick="$emit('dblclick')"
        />
        <input
            v-else-if="onlyNum"
            :type="type"
            :id="id"
            :name="name"
            :value="modelValue"
            :readonly="readonly"
            :disabled="disabled"
            :required="requiredIndicator"
            @input="updateValue"
            @keydown="allowOnlyNumbers"
            :class="inputClass"
            @dblclick="$emit('dblclick')"
        />
        <input
            v-else
            :type="type"
            :id="id"
            :name="name"
            :value="modelValue"
            :readonly="readonly"
            :disabled="disabled"
            :required="requiredIndicator"
            @input="updateValue"
            :class="inputClass"
            @dblclick="$emit('dblclick')"
        />
    </div>
</template>