<template>
  <div class="flex flex-col h-full">
    <section v-if="editor" class="buttons flex bg-base-100 justify-around items-center flex-wrap gap-2 border border-accent p-4">
<button
          @click="editor.chain().focus().toggleBold().run()"
          :disabled="!editor.can().chain().focus().toggleBold().run()"
          :class="{ 'is-active': editor.isActive('bold') }"
        >
         <Bold/>
        </button>
        <button
          @click="editor.chain().focus().toggleItalic().run()"
          :disabled="!editor.can().chain().focus().toggleItalic().run()"
          :class="{ 'is-active': editor.isActive('italic') }"
        >
         <Italic/>
        </button>
        <button
          @click="editor.chain().focus().toggleBulletList().run()"
          :class="{ 'is-active': editor.isActive('bulletList') }"
        >
          <List/>
        </button>
        <button
          @click="editor.chain().focus().toggleOrderedList().run()"
          :class="{ 'is-active': editor.isActive('orderedList') }"
        >
          <ListOrdered />
        </button>
            <button @click="editor.chain().focus().undo().run()" :disabled="!editor.can().undo()">
      <Undo/>
        </button>
        <button @click="editor.chain().focus().redo().run()" :disabled="!editor.can().redo()">
    <Redo/>
        </button>
    </section>
<div class="flex flex-1 flex-row prose max-w-none">
<editor-content :editor="editor" class="flex-1" />
</div>
</div>
  


</template>

<script setup lang="ts">
import {useEditor, EditorContent} from '@tiptap/vue-3';
import StarterKit from '@tiptap/starter-kit';

import { Bold, Italic, List, ListOrdered, Undo, Redo} from 'lucide-vue-next';
const props = defineProps({
   modelValue: String, 
});
const emit = defineEmits(['update:modelValue']);

const editor = useEditor({
    extensions: [StarterKit],
    editorProps: {
        attributes: {
            class: 'bg-base-300 h-full border border-accent rounded min-h-[150px] w-full focus:outline-none',
        },
    },
  content: props.modelValue,
  onUpdate: ({editor})=>{
      emit('update:modelValue', editor.getHTML());
  },
})
</script>

<style scoped>
button {
  border: 1px solid var(--color-accent);
  padding: 0.5rem;
}
li {
  margin: 0;
}
</style>