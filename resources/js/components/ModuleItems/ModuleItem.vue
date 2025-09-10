<template>
    <div class=" rounded-lg p-4 bg-base-300 mb-16">
    <div class="flex justify-between items-start">
    <span class="drag-handle cursor-move mr-4">☰</span>
    <div class="flex-grow">
      
    <component
    :is="isEditing ? editComponent : viewComponent"
    :item-data="item.data"
    :module-id="moduleId"
    :item-id="item.id"
    @save="handleUpdate"
    @cancel="isEditing = false"
    />
    </div>
    <div class="flex items-center ml-4">
  
    <button @click="isEditing = !isEditing" class="btn btn-sm btn-info mr-2">
        {{ isEditing ? 'View' : 'Edit' }}</button>
    <button @click="handleDelete" class="btn btn-sm btn-error">Delete</button>
    </div>
    </div>

    </div>
</template>

<script setup lang="ts">
import { edit } from '@/routes/courses';
import {ref, defineAsyncComponent, computed} from 'vue';

const props = defineProps({
    item: Object,
    index: Number,
    moduleId: Number,
    forceEdit: {type: Boolean, default: false},
    edit: {type: Boolean, default: false}
});
const localEdit = ref(props.forceEdit || props.edit);
const emit = defineEmits(['update', 'delete']);

const isEditing = ref(localEdit.value);

const viewComponent = computed(() => defineAsyncComponent(() => import(`./View/${props.item.type.charAt(0).toUpperCase() + props.item.type.slice(1)}View.vue`)));

const editComponent = computed(() => defineAsyncComponent(() => import(`./Edit/${props.item.type.charAt(0).toUpperCase() + props.item.type.slice(1)}Edit.vue`)));

function handleUpdate(updatedItemData){
    emit('update', {...props.item, data: updatedItemData});
    isEditing.value = false;
}

function handleDelete(){
    if(confirm('Are you sure you want to delete this item?')){
        emit('delete', props.item.id);
    }
}
</script>

<style scoped>

</style>