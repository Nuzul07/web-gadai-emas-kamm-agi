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
});

const emits = defineEmits(["update:modelValue"]);

const inputClass = computed(() => ({
    "mt-1 block w-full rounded-md border-black shadow-sm focus:ring-amber-600 focus:border-amber-600 sm:text-sm": true,
    "mt-1 block w-full rounded-md border-black shadow-sm focus:ring-amber-600 focus:border-amber-600 sm:text-sm bg-gray-300":
        props.disabled,
}));

const updateValue = (event) => {
    emits("update:modelValue", event.target.value);
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
        <div class="relative mt-1">
            <span
                class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-500"
            >
                %
            </span>
            <input
                :type="type"
                :id="id"
                :name="name"
                :value="modelValue"
                :readonly="readonly"
                :disabled="disabled"
                :required="requiredIndicator"
                @input="updateValue"
                :class="[inputClass, 'pl-3 pr-48']"
            />
        </div>
    </div>
</template>

<style scoped>
/* Add some padding to the input field to make space for the prefix */
.pl-12 {
    padding-left: 3rem;
}
</style>
