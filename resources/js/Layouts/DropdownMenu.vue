<template>
  <div>
      <div @click="toggleMenu" 
          class="dropdown-menu-header cursor-pointer" 
          :class="{ 'bg-gray-50 text-gray-900': isOpen }">
          <span class="dropdown-menu-icon">
              <i :class="icon"></i>
          </span>
          <span class="flex-1 truncate">{{ title }}</span>
          <svg class="h-5 w-5 text-gray-400 transform transition-transform duration-200" 
              :class="{ 'rotate-180': isOpen }" 
              xmlns="http://www.w3.org/2000/svg" 
              viewBox="0 0 20 20" 
              fill="currentColor">
              <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
          </svg>
      </div>
      
      <div v-show="isOpen" class="dropdown-menu-items">
          <slot></slot>
      </div>
  </div>
</template>

<script setup>
import { ref } from 'vue';

const props = defineProps({
  title: {
      type: String,
      required: true
  },
  icon: {
      type: String,
      required: true
  }
});

const isOpen = ref(false);

const toggleMenu = () => {
  isOpen.value = !isOpen.value;
};
</script>