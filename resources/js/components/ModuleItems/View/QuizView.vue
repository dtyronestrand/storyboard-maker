<template>
    <div class="space-y-4">
        <h3 class="text-lg font-semibold">{{ itemData.title }}</h3>
        <div class="flex gap-4 text-sm">
            <span><strong>Points:</strong> {{ itemData.points }}</span>
            <span v-if="itemData.time_limit"><strong>Time Limit:</strong> {{ itemData.time_limit }} minutes</span>
            <span><strong>Attempts:</strong> {{ itemData.attempts }}</span>
        </div>
        <div v-if="itemData.questions && itemData.questions.length">
            <p class="text-sm text-gray-600">{{ itemData.questions.length }} question(s)</p>
        </div>
    </div>
</template>

<script setup lang="ts">
const props = defineProps({
    itemData: Object
});
</script>