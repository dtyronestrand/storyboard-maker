<template>
    <div class="space-y-4">
        <div>
            <label>Title</label>
            <input type="text" v-model="editableData.title" class="w-full border p-2 rounded"/>
        </div>
        <div>
            <label>Time Limit (minutes)</label>
            <input type="number" v-model="editableData.time_limit" class="w-full border p-2 rounded"/>
        </div>
        <div>
            <label>Number of Attempts</label>
            <input type="number" v-model="editableData.attempts" class="w-full border p-2 rounded"/>
        </div>
        <div>
            <label>Total Points</label>
            <input type="number" v-model="editableData.points" class="w-full border p-2 rounded"/>
        </div>
        <div class="flex gap-2">
            <button @click="save" class="px-3 py-1 rounded btn btn-success">Save</button>
            <button @click="$emit('cancel')" class="px-3 py-1 rounded btn btn-error">Cancel</button>
        </div>
    </div>
</template>

<script setup lang="ts">
import {ref} from 'vue';

const props = defineProps({
    itemData: Object
});

const emit = defineEmits(['save', 'cancel']);

const editableData = ref({...props.itemData});

function save() {
    emit('save', editableData.value);
}
</script>