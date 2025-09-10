<template>
    <div class="space-y-4">
        <div>
            <label>Title</label>
            <input type="text" v-model="editableData.title" class="w-full border p-2 rounded"/>
        </div>
        <div>
            <label>Content</label>
            <div class="border rounded">
                <editor-content :editor="editor"/>
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

const props = defineProps({
    itemData: Object
});

const emit = defineEmits(['save', 'cancel']);

const editableData = ref({...props.itemData});

const editor = useEditor({
    content: editableData.value.content,
    extensions: [StarterKit],
    onUpdate({ editor }) {
        editableData.value.content = editor.getHTML();
    }
})

function save() {
    emit('save', editableData.value);
}
</script>