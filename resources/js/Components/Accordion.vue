<template>
  <div>
    <button
      @click="toggleAccordion"
      :class="[bgColorClass, 'w-full text-left px-4 py-2 font-medium rounded-md focus:outline-none focus:ring-2 focus:ring-yellow-500']"
    >
      {{ title }}
      <span class="float-right">
        <img v-if="isOpen" src="/gadaiemas/storage/icon/minus.png" class="mt-1">
        <img v-else src="/gadaiemas/storage/icon/plus.png" class="mt-1">
      </span>
    </button>

    <div v-if="isOpen" :class="[bgColorClass,'mt-2 px-4 py-2 border border-gray-300 rounded-md']">
      <slot></slot>
    </div>
  </div>
</template>

<script setup>
import { ref, computed } from "vue";

const props = defineProps({
  title: String,
  index: Number, // Add index prop
});

const isOpen = ref(false);

// Compute background color based on even/odd index
const bgColorClass = computed(() => {
  return props.index % 2 === 0 ? 'bg-sky-700 text-white' : 'bg-amber-400 text-gray-900';
});

const toggleAccordion = () => {
  isOpen.value = !isOpen.value;
};
</script>

<style scoped>
/* Optional: Additional styles for the accordion */
</style>
