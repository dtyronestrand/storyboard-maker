<template>
    <section v-if="editor" class="buttons flex bg-base-100 items-center flex-wrap gap-2 border border-accent p-4">
<button
          @click="editor.chain().focus().toggleBold().run()"
          :disabled="!editor.can().chain().focus().toggleBold().run()"
          :class="{ 'is-active': editor.isActive('bold') }"
        >
          Bold
        </button>
        <button
          @click="editor.chain().focus().toggleItalic().run()"
          :disabled="!editor.can().chain().focus().toggleItalic().run()"
          :class="{ 'is-active': editor.isActive('italic') }"
        >
          Italic
        </button>
        <button
          @click="editor.chain().focus().toggleBulletList().run()"
          :class="{ 'is-active': editor.isActive('bulletList') }"
        >
          Bullet list
        </button>
        <button
          @click="editor.chain().focus().toggleOrderedList().run()"
          :class="{ 'is-active': editor.isActive('orderedList') }"
        >
          Ordered list
        </button>
            <button @click="editor.chain().focus().undo().run()" :disabled="!editor.can().undo()">
          Undo
        </button>
        <button @click="editor.chain().focus().redo().run()" :disabled="!editor.can().redo()">
          Redo
        </button>
    </section>
<editor-content :editor="editor"/>

</template>

<script setup lang="ts">
import {useEditor, EditorContent} from '@tiptap/vue-3';
import StarterKit from '@tiptap/starter-kit';

const props = defineProps({
   modelValue: String, 
});
const emit = defineEmits(['update:modelValue']);

const editor = useEditor({
    extensions: [StarterKit],
    editorProps: {
        attributes: {
            class: 'bg-base-300 border border-accent rounded p-2 min-h-[150px] prose prose-sm sm:prose lg:prose-lg xl:prose-2xl mx-auto focus:outline-none',
        },
    },
  content: props.modelValue,
  onUpdate: ({editor})=>{
      emit('update:modelValue', editor.getHTML());
  },
})
</script>

<style scoped>

</style>