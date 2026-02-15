<template>
    <select
        class="block mt-1 w-full rounded-md form-input focus:border-indigo-600"
        :value="modelValue"
        @change="$emit('update:modelValue', $event.target.value)"
        ref="input"
    >
        <option v-for="option in options" :key="option.value" :value="option.value">
            {{ option.label }}
        </option>
    </select>
</template>

<script setup>
import { onMounted, ref } from 'vue';

defineProps({
    modelValue: String,
    options: {
        type: Array,
        required: true, // Ensure options are provided
    }
});

defineEmits(['update:modelValue']);

const input = ref(null);

onMounted(() => {
    if (input.value?.hasAttribute('autofocus')) {
        input.value.focus();
    }
});
</script>
