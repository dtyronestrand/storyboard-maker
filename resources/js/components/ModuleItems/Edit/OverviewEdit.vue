<template>
    <div class="space-y-4 p-8 bg-base-200">
    <div>
    <label>Title</label>
    <input type="text" v-model="editableData.title" class="w-full border-2 border-primary bg-base-300 p-2 rounded"/>
    </div>
    <div>
    <label>Content</label>
    <div class="border border-accent rounded">
<TipTap v-model="editableData.content"/>
    </div>
    </div>

    
  <div class="flex gap-2">
            <button @click="save" :disabled="saving" class="px-3 py-1 rounded btn btn-success">
                {{ saving ? 'Saving...' : 'Save' }}
            </button>
            <button @click="$emit('cancel')" class="px-3 py-1 rounded btn btn-error">Cancel</button>
        </div>
    </div>

</template>

<script setup lang="ts">
import {ref, onMounted, onBeforeUnmount} from 'vue';
import {router} from '@inertiajs/vue3';

import TipTap from '@/components/TipTap.vue';
const props = defineProps({
    itemData: Object,
    itemId: String,
    moduleId: Number,
    edit: {type: Boolean, default: false}
});

const emit = defineEmits(['save', 'cancel']);

const editableData = ref({...props.itemData});
const saving = ref(false);

function save() {
    if (props.moduleId && props.itemId) {
        saving.value = true;
        router.put(`/modules/${props.moduleId}/items/${props.itemId}`, {
            data: editableData.value
        }, {
            preserveScroll: true,
            onSuccess: () => {
                saving.value = false;
                emit('save', editableData.value);
            },
            onError: () => {
                saving.value = false;
            }
        });
    } else {
        emit('save', editableData.value);
    }
}
</script>

<style scoped>
</style>

