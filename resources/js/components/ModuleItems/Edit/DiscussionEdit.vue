<template>
    <div class="space-y-4">
        <div>
            <label>Title</label>
            <input type="text" v-model="editableData.title" class="w-full border border-accent bg-base-100 p-2 rounded"/>
        </div>
        <div>
            <label>Prompt</label>
            <TipTap v-model="editableData.prompt" />
        </div>
        <div>
            <label class="flex items-center gap-2">
                <input class="border border-accent bg-base-100" type="checkbox" v-model="editableData.graded"/>
                Graded Discussion
            </label>
        </div>
        <div v-if="editableData.graded" class="space-y-2">
            <div>
                <label>Points</label>
                <input type="number" v-model="editableData.grading.points" class="w-full border-accent bg-base-100 border p-2 rounded"/>
            </div>
        </div>
        <div>
            <label>Initial Post Due Date</label>
            <input type="datetime-local" v-model="editableData.inital_due_date" class="w-full border border-accent bg-base-100 p-2 rounded"/>
        </div>
        <div>
            <label>Replies Due Date</label>
            <input type="datetime-local" v-model="editableData.replies_due_date" class="w-full border-accent bg-base-100 border p-2 rounded"/>
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

const editableData = ref({...props.itemData});

function save() {
    emit('save', editableData.value);
}
</script>