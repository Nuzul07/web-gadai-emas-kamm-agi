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
        default: false
    },
    disabled: {
        type: Boolean,
        default: false
    },
    modelValue: {
        type: String,
        required: true,
    },
    ket: {
        type: String,
        required: false,
    },
    requiredIndicator: {
        type: Boolean,
        default: true,
    },
});

const emits = defineEmits(["update:modelValue"]);

const updateValue = (event) => {
    emits("update:modelValue", event.target.value);
};
</script>

<template>
    <div>
        <label :for="id" class="tracking-wide block mb-2 text-sm text-gray-900 uppercase">
            {{ label }} &nbsp;: {{ modelValue }} {{ ket }}
        </label>
    </div>
</template>
