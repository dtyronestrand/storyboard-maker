<template>
    <div class="space-y-4 p-8 bg-base-200">
        <div>
            <label>Title</label>
            <input type="text" v-model="editableData.title" class="w-full border-2 border-accent bg-base-300 p-2 rounded">
        </div>
        <div>
            <label>Content</label>
            <div class="border rounded">
                <TipTap v-model="editableData.content"/>
            </div>
        </div>
        <div class="flex gap-2">
            <button @click="save" class="px-3 py-1 rounded btn btn-success">Save</button>
            <button @click="$emit('cancel')" class="px-3 py-1 rounded btn btn-error">Cancel</button>
        </div>
    </div>
</template>

<script setup lang="ts">
import {ref} from 'vue';
import {useEditor, EditorContent} from '@tiptap/vue-3';
import StarterKit from '@tiptap/starter-kit';
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