<template>
    <inertia-link :href="'/'">
		<img class="object-contain h-48 w-full" :src="'/images/logo-1.jpg'">
    </inertia-link>
</template>

<script>
import { Link } from '@inertiajs/vue3';
export default {
  components: {
    'inertia-link': Link
  }
}
</script>