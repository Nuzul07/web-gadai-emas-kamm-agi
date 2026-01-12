<template>
  <div>
    <input
      type="radio"
      :id="id"
      :name="name"
      :value="value"
      :checked="modelValue === value"
      @change="updateValue"
      class="mr-1"
    />
    <label :for="id" class="tracking-wide text-xs font-bold text-gray-900 uppercase mr-5">{{ label }}</label>
  </div>
</template>

<script setup>
import { toRefs, defineProps, defineEmits } from 'vue';

const props = defineProps({
  id: {
    type: String,
    required: true,
  },
  label: {
    type: String,
    required: true,
  },
  name: {
    type: String,
    required: true,
  },
  value: {
    type: String,
    required: true,
  },
  modelValue: {
    type: String,
    required: true,
  },
});

const emit = defineEmits(['update:modelValue']);

const { modelValue } = toRefs(props);

const updateValue = (event) => {
  emit('update:modelValue', event.target.value);
};
</script>
