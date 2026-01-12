import { ref, watch } from 'vue';

// Retrieve stored value or set default to an empty string
const storedValue = localStorage.getItem('selectedTipeGadai') || ''
export const selectedTipeGadai = ref(storedValue)

// Watch for changes and update localStorage
watch(selectedTipeGadai, (newValue) => {
    localStorage.setItem('selectedTipeGadai', newValue)
})