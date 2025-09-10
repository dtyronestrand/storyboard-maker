<template>
    <div class="space-y-4">
        <h3 class="text-lg font-semibold">{{ itemData.title }}</h3>
        <div v-if="itemData.purpose">
            <h4 class="font-medium">Purpose:</h4>
            <p>{{ itemData.purpose }}</p>
        </div>
        <div v-if="itemData.criteria">
            <h4 class="font-medium">Criteria:</h4>
            <p>{{ itemData.criteria }}</p>
        </div>
        <div class="flex gap-4">
            <span><strong>Points:</strong> {{ itemData.points }}</span>
            <span v-if="itemData.due_date"><strong>Due:</strong> {{ new Date(itemData.due_date).toLocaleString() }}</span>
        </div>
    </div>
</template>

<script setup lang="ts">
const props = defineProps({
    itemData: Object
});
</script>