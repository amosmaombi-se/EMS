<template>
  <Link :href="href" class="dropdown-item">
      <span class="text-gray-400 mr-2 w-4 inline-block">
          <i :class="icon"></i>
      </span>
      <span class="truncate">{{ text }}</span>
  </Link>
</template>

<script setup>
import { Link } from '@inertiajs/vue3';

defineProps({
  href: {
      type: String,
      required: true
  },
  icon: {
      type: String,
      required: true
  },
  text: {
      type: String,
      required: true
  }
});
</script>