<template>
    <label class="block text-sm font-medium text-black mb-4">
        {{ label }}
        <span v-if="requiredIndicator" class="text-sm text-red-600">*</span>
    </label>
    <div
        class="file-uploader"
        @dragover.prevent
        @drop.prevent="handleDrop"
        @click="selectFiles"
    >
        <input
            type="file"
            multiple
            ref="fileInput"
            @change="handleFiles"
            class="file-input"
        />
        <div class="file-uploader-drop-zone">
            <p>Drag & Drop Files Here or Click to Select</p>
        </div>
        <div class="file-preview">
            <div v-for="(file, index) in modelValue" :key="index" class="file-item">
                <img
                    v-if="isImage(file)"
                    :src="file.preview"
                    alt="Preview"
                    class="file-preview-img"
                />
                <p>{{ file.name }}</p>
                <button @click="removeFile(index)">Remove</button>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, defineProps, defineEmits, watch, defineExpose } from "vue";

const props = defineProps({
    label: String,
    requiredIndicator: {
        type: Boolean,
        default: true,
    },
    modelValue: {
        type: Array,
        default: () => [], // Default to an empty array if not provided
    },
});

const emit = defineEmits(["update:modelValue", "files-selected"]);
const fileInput = ref(null);

// Function to handle file selection
const handleFiles = (event) => {
    const selectedFiles = event.target.files;
    addFiles(selectedFiles);
};

// Function to handle drag-and-drop
const handleDrop = (event) => {
    const droppedFiles = event.dataTransfer.files;
    addFiles(droppedFiles);
};

// Function to add files and generate previews
const addFiles = (fileList) => {
    const newFiles = [];
    for (const file of fileList) {
        if (!file.type.startsWith("image/")) continue;
        const reader = new FileReader();
        reader.onload = (e) => {
            const fileData = {
                file,
                name: file.name,
                preview: e.target.result,
            };
            newFiles.push(fileData);

            // Update the modelValue with the new file
            emit("update:modelValue", [...props.modelValue, fileData]);
        };
        reader.readAsDataURL(file);
    }
    // Emit the full list of selected files for any custom handling
    emit("files-selected", newFiles);
};

// Check if the file is an image
const isImage = (file) => file.file.type.startsWith("image/");

// Open file selector
const selectFiles = () => fileInput.value.click();

// Remove file from preview list
const removeFile = (index) => {
    const updatedFiles = [...props.modelValue];
    updatedFiles.splice(index, 1);
    emit("update:modelValue", updatedFiles); // Emit updated files list
};

// Clear all files and previews
const clearFiles = () => {
    emit("update:modelValue", []);
};

// Expose the clearFiles method to the parent component
defineExpose({ clearFiles });
</script>

<style scoped>
.file-uploader {
    border: 2px dashed #ccc;
    padding: 20px;
    text-align: center;
    cursor: pointer;
}

.file-input {
    display: none;
}

.file-uploader-drop-zone {
    margin-bottom: 20px;
}

.file-uploader-drop-zone:hover {
    background-color: #f0f0f0;
}

.file-preview {
    display: flex;
    flex-wrap: wrap;
    gap: 10px;
}

.file-item {
    border: 1px solid #ddd;
    padding: 10px;
    border-radius: 4px;
    text-align: center;
}

.file-preview-img {
    width: 100px;
    height: 100px;
    object-fit: cover;
}
</style>