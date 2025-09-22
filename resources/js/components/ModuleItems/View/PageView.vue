<template>
   <div class="bg-base-200 p-8 space-y-4">
        <h3 class="text-lg font-semibold">{{ itemData.title }}</h3>
        <div v-html="itemData.content" class="prose"></div>
    </div>
</template>

<script setup lang="ts">
const props = defineProps({
    itemData: Object
});
</script>