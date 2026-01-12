<script setup>
defineProps({
    value: {
        type: String
    }
});
</script>
<template>
    <div class="text-2xl font-semibold text-white">{{ value }}</div>
</template>