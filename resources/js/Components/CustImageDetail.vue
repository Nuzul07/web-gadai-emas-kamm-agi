<script setup>
import { ref, defineEmits, defineProps } from "vue";

const props = defineProps({
    label: {
        type: String,
        required: true,
    },
    requiredIndicator: {
        type: Boolean,
        default: true,
    },
    folder: {
        type: String,
        required: true,
    },
    image: {
        type: String,
        required: true,
    },
});
</script>

<template>
    <div class="upload-container">
        <label :for="id" class="block text-sm font-medium text-black">
            {{ label }}
            <span v-if="requiredIndicator" class="text-sm text-red-600">*</span>
        </label>
        <div class="preview-container">
            <div class="image-placeholder" v-if="!image">
                No Image Available
            </div>
            <img
                v-else
                :src="`/gadaiemas/storage/picture/${folder}/${image}`"
                alt="Image Preview"
                class="image-preview"
            />
        </div>
    </div>
</template>

<style scoped>
.upload-container {
    display: flex;
    flex-direction: column;
    align-items: flex-start;
}
.preview-container {
    margin-top: 10px;
}
.image-placeholder,
.image-preview {
    width: 450px;
    height: 250px;
    border: 1px solid #ccc;
    display: flex;
    justify-content: center;
    align-items: center;
    text-align: center;
}
.image-preview {
    object-fit: contain;
}
</style>
