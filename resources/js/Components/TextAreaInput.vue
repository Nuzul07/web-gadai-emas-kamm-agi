<script setup>
import { defineProps, defineEmits } from "vue";

const props = defineProps({
    id: {
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
    rows: {
        type: Number,
        default: 5,
    },
    modelValue: {
        type: String,
        default: "",
    },
    disabled: {
        type: Boolean,
        default: false,
    },
    requiredIndicator: {
        type: Boolean,
        default: true,
    },
});

const emit = defineEmits(["update:modelValue"]);

const updateValue = (event) => {
    emit("update:modelValue", event.target.value);
};
</script>

<template>
    <div>
        <label :for="id" class="tracking-wide block mb-2 text-xs font-bold text-gray-900 uppercase">
            {{ label }}
            <span v-if="requiredIndicator" class="text-sm text-red-600">*</span>
        </label>
        <textarea
            :id="id"
            :name="name"
            :rows="rows"
            :value="modelValue"
            :required="requiredIndicator"
            :disabled="disabled"
            @input="updateValue"
            class="mt-1 block w-full rounded-md border-black shadow-sm focus:ring-amber-600 focus:border-amber-600 sm:text-sm"
        />
    </div>
</template>
