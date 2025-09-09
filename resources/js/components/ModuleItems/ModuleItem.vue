<template>
    <div class="border rounded-lg p-4 bg-base-100 shadow-sm">
    <div class="flex justify-between items-start">
    <div class="flex-grow">
    <component
    :is="isEditing ? editComponent : viewComponent"
    :item-data="item.data"
    @save="handleUpdate"
    @cancel="isEditing = false"
    />
    </div>
    <div class="flex items-center ml-4">
    <span class="drag-handle cursor-move mr-4">☰</span>
    <button @click="isEditing = !isEditing" class="btn btn-sm btn-info mr-2">
        {{ isEditing ? 'View' : 'Edit' }}</button>
    <button @click="handleDelete" class="btn btn-sm btn-error">Delete</button>
    </div>
    </div>

    </div>
</template>

<script setup lang="ts">
import {ref, defineAsyncComponent, computed} from 'vue';

const props = defineProps({
    item: Object,
    index: Number,
});

const emit = defineEmits(['update', 'delete']);

const isEditing = ref(false);

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