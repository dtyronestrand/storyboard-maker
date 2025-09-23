<template>
<Link :href="`/courses/${props.courseId}`" class="rounded-sm bg-info px-2 py-1 text-sm text-info-content hover:bg-primary-dark">View Course</Link>
</template>

<script setup lang="ts">
import {Link} from '@inertiajs/vue3';
interface Props {
    courseId: number,
}
const props = defineProps<Props>();
</script>

<style scoped>

</style>