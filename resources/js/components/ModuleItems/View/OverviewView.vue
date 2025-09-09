<template>
    <div class="space-y-4">
        <h3 class="text-lg font-semibold">{{ itemData.title }}</h3>
        <div v-html="itemData.content" class="prose"></div>
        <div v-if="itemData.learning_objectives && itemData.learning_objectives.length">
            <h4 class="font-medium">Learning Objectives:</h4>
            <ul class="list-disc list-inside">
                <li v-for="objective in itemData.learning_objectives" :key="objective">
                    {{ objective }}
                </li>
            </ul>
        </div>
    </div>
</template>

<script setup lang="ts">
const props = defineProps({
    itemData: Object
});
</script>

<style scoped>
</style>