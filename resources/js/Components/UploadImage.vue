<template>
    <div class="upload-container">
        <label class="block text-sm font-medium text-black">
            {{ label }}
            <span v-if="requiredIndicator" class="text-sm text-red-600">*</span>
        </label>
        <div class="preview-container">
            <div class="image-placeholder" v-if="!preview">
                No Image Uploaded
            </div>
            <img
                v-if="preview"
                :src="preview"
                alt="Image Preview"
                class="image-preview"
            />
        </div>
        <div class="file-input-container">
            <input type="file" @change="onFileChange" ref="fileInput" />
            <span>{{ fileName }}</span>
        </div>
    </div>
</template>

<script setup>
import { ref, defineProps, defineEmits, watch, defineExpose } from "vue";

const props = defineProps({
    label: {
        type: String,
        required: true,
    },
    requiredIndicator: {
        type: Boolean,
        default: true,
    },
    modelValue: File, // Accepts a File as the initial value for v-model
});

const emits = defineEmits(["update:modelValue", "file-selected"]);

const file = ref(props.modelValue || null);
const preview = ref(file.value ? URL.createObjectURL(file.value) : null);
const fileInput = ref(null);

const onFileChange = (e) => {
    file.value = e.target.files[0];
    preview.value = file.value ? URL.createObjectURL(file.value) : null;

    // Emit both the v-model update and the file-selected event
    emits("update:modelValue", file.value); // Update the parent’s modelValue
    emits("file-selected", file.value); // Emit file-selected for custom handling
};

// Reset the preview and file selection
const resetPreview = () => {
    file.value = null;
    preview.value = null;
    if (fileInput.value) {
        fileInput.value.value = ""; // Clear the file input element
    }
    emits("update:modelValue", null); // Clear the parent’s modelValue
};

// Watch for external changes to modelValue and update the preview accordingly
watch(
    () => props.modelValue,
    (newFile) => {
        file.value = newFile;
        preview.value = newFile ? URL.createObjectURL(newFile) : null;
    }
);

// Expose the resetPreview function to the parent component
defineExpose({ resetPreview });
</script>

<style scoped>
.upload-container {
    display: flex;
    flex-direction: column;
    align-items: flex-start;
}
.file-input-container {
    display: flex;
    align-items: center;
}
.file-input-container input[type="file"] {
    margin-right: 10px;
    margin-top: 25px;
}
.preview-container {
    margin-top: 10px;
}
.image-placeholder,
.image-preview {
    width: 450px;
    height: 200px;
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
