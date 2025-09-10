<template>
    <div class="space-y-4">
        <div>
            <label>Title</label>
            <input type="text" v-model="editableData.title" class="w-full border border-accent bg-base-100 p-2 rounded"/>
        </div>
        <div>
            <label>Purpose</label>
            <TipTap v-model="editableData.purpose" />
        </div>
        <div>
            <label>Criteria</label>
            <TipTap v-model="editableData.criteria"  />
        </div>
        <div>
            <label>Points</label>
            <input type="number" v-model="editableData.points" class="w-full border border-accent bg-base-100 p-2 rounded"/>
        </div>
        <div>
            <label>Due Date</label>
            <input type="datetime-local" v-model="editableData.due_date" class="w-full border border-accent bg-base-100 p-2 rounded"/>
        </div>
        <div class="flex gap-2">
            <button @click="save" class="px-3 py-1 rounded btn btn-success">Save</button>
            <button @click="$emit('cancel')" class="px-3 py-1 rounded btn btn-error">Cancel</button>
        </div>
    </div>
</template>

<script setup lang="ts">
import {ref} from 'vue';
import TipTap from '@/components/TipTap.vue';
const props = defineProps({
    itemData: Object
});

const emit = defineEmits(['save', 'cancel']);

const editableData = ref({
    title: '',
    purpose: '',
    criteria: '',
    points: 0,
    due_date: '',
    ...props.itemData
});

function save() {
    emit('save', editableData.value);
}
</script>