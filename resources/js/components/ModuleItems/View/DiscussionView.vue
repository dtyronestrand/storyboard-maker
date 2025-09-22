<template>
  <div class="bg-base-200 p-8 space-y-4">
        <h3 class="text-lg font-semibold">{{ itemData.title }}</h3>
        <div v-if="itemData.prompt">
            <h4 class="font-medium">Discussion Prompt:</h4>
            <p>{{ itemData.prompt }}</p>
        </div>
        <div class="flex gap-4 text-sm">
            <span v-if="itemData.graded" class="bg-green-100 px-2 py-1 rounded">
                Graded ({{ itemData.grading?.points || 0 }} points)
            </span>
            <span v-else class="bg-gray-100 px-2 py-1 rounded">Ungraded</span>
        </div>
        <div v-if="itemData.inital_due_date || itemData.replies_due_date" class="text-sm">
            <div v-if="itemData.inital_due_date">
                <strong>Initial Post Due:</strong> {{ new Date(itemData.inital_due_date).toLocaleString() }}
            </div>
            <div v-if="itemData.replies_due_date">
                <strong>Replies Due:</strong> {{ new Date(itemData.replies_due_date).toLocaleString() }}
            </div>
        </div>
    </div>
</template>

<script setup lang="ts">
const props = defineProps({
    itemData: Object
});
</script>