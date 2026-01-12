<script setup>
import { ref, onMounted, defineEmits, onUnmounted } from "vue";

const video = ref(null);
const canvas = ref(null);
const captured = ref(false);
const imageSrc = ref(null);
const videoStopped = ref(true);

const props = defineProps({
    label: {
        type: String,
        required: true,
    },
    nameAuthor: {
        type: String,
        required: true,
    },
    requiredIndicator: {
        type: Boolean,
        default: true,
    }
});

const emit = defineEmits(["imageCaptured"]);

const startWebcam = () => {
    navigator.mediaDevices
        .getUserMedia({ video: true })
        .then((stream) => {
            video.value.srcObject = stream;
            videoStopped.value = false;
        })
        .catch((err) => {
            console.error("Error accessing webcam: ", err);
        });
};

const stopWebcam = () => {
    const stream = video.value.srcObject;
    const tracks = stream.getTracks();

    tracks.forEach((track) => track.stop());
    video.value.srcObject = null;
    videoStopped.value = true;
};

const captureImage = () => {
    const context = canvas.value.getContext("2d");
    canvas.value.width = video.value.videoWidth;
    canvas.value.height = video.value.videoHeight;
    context.drawImage(
        video.value,
        0,
        0,
        canvas.value.width,
        canvas.value.height
    );
    canvas.value.toBlob((blob) => {
        const filename = `Foto Nasabah -${props.nameAuthor}.png`
        const fileFotoIdentitas = new File([blob], filename, {
            type: "image/png",
        });
        imageSrc.value = URL.createObjectURL(blob);
        captured.value = true;
        emit("imageCaptured", fileFotoIdentitas);
        stopWebcam();
    }, "image/png");
};

const retakeImage = () => {
    captured.value = false;
    imageSrc.value = null;
    startWebcam();
};

onMounted(() => {
    // Initially, the webcam is stopped.
});

onUnmounted(() => {
    if (!videoStopped.value) {
        stopWebcam();
    }
});
</script>

<template>
    <div>
        <label :for="id" class="block text-sm font-medium text-black">
            {{ label }}
            <span v-if="requiredIndicator" class="text-sm text-red-600">*</span>
        </label>
        <video v-if="!captured" ref="video" autoplay></video>
        <canvas ref="canvas" style="display: none"></canvas>
        <img v-if="captured" :src="imageSrc" alt="Captured Image" />
        <div>
            <button class="mt-4 ml-2 bg-blue-700 text-white py-1 px-4 rounded" @click="captured ? retakeImage() : (videoStopped ? startWebcam() : captureImage())">{{ captured ? 'Take Ulang' : (videoStopped ? 'Mulai Webcam' : 'Capture') }}</button>
        </div>
    </div>
</template>

<style scoped>
video,
img {
    width: 100%;
    max-width: 400px;
}
</style>
