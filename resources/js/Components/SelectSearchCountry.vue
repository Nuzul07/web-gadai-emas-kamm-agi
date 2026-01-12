<script setup>
import { ref, watch, computed, onMounted, onBeforeUnmount } from 'vue'
import axios from 'axios'

onMounted(() => {
    window.addEventListener('click', closeOption)
})

onBeforeUnmount(() => {
    window.removeEventListener('click', closeOption)
})    

const props = defineProps({
    source: {
        type: Array,
        required: true,
        default: () => []
    },
    modelValue: String,
    label: String
})

const emit = defineEmits(['update:modelValue'])

const search = ref('')
const searchResult = computed(() => {
    if (search.value == '') {
        return Object.values(props.source)
    }

    return Object.values(props.source).filter((option) => {
        if (option.country.toLowerCase().includes(search.value.toLowerCase())) {
            return option
        }   
    })
});

const dropDown = ref(null)
const isOpen = ref(false)

const setSelected = (option) => {
    isOpen.value = false
    search.value = option
    emit('update:modelValue', search.value)
};

const handleInput = (event) => {
    search.value = event.target.value
    emit('update:modelValue', search.value)
}

const openOption = () => {
    isOpen.value = true
};

const closeOption = (element) => {
    if(!dropDown.value.contains(element.target)) {
        isOpen.value = false
    }
};

</script>
<template>
  <div class="w-full relative">
    <label class="tracking-wide block mb-2 text-xs font-bold text-gray-900 uppercase">{{ label }}</label>
    <div ref="dropDown" @click="openOption">
        <img src="/storage/icon/down-arrow.png" class="absolute right-3 my-3.5 h-4 w-3.5"/>
        <input type="text" :value="modelValue" @input="handleInput" class=" py-2 w-full border border-black rounded-md cursor-default shadow-sm focus:ring-amber-600 focus:border-amber-600" placeholder="Choose first...">
    </div>
    <ul class="mt-1 w-full max-h-60 border border-black rounded-md bg-white absolute overflow-y-auto" v-show="searchResult.length && isOpen">
        <li class="px-4 py-3 border-b border-black text-black cursor-pointer hover:bg-gray-100 transition-colors" v-for="country in searchResult" :key="country.country" @click="setSelected(country.country)">{{ country.country }}</li>
    </ul>
  </div>
</template>