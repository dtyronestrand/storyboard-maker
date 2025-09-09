<template>
    <div>
        <draggable v-model="localItems" item-key="id" animation="200" handle=".drag-handle" @end="handleReorder">
         <template #item="{element: item, index}">
         <div class="mb-4">
            <ModuleItem :item="item" :index="index" @update="handleUpdateItem" @delete="handleDeleteItem"/>
            </div>
         </template>
        </draggable>
        <div class="mt-4">
        <select @change="e=>handleAddItem(e.target.value)">
        <option disabled selected>--Add New Item</option>
        <option v-for="type in itemTypes" :key="type.value" :value="type.value">{{type.label}}</option>
        </select>
        </div>
    </div>
</template>

<script setup lang="ts">
import {ref} from 'vue';
import {router} from "@inertiajs/vue3";
import { VueDraggableNext as draggable } from 'vue-draggable-next';
import ModuleItem from './ModuleItem.vue';

const props = defineProps({
    module: Object, 
})

const localItems = ref([...(props.module.items || [])]);

const itemTypes = [
    {value: 'overview', label: 'Overview'},
    {value: 'page', label: 'Page'},
    {value: 'assignment', label: 'Assignment'},
    {value: 'discussion', label: 'Discussion'},
    {value: 'quiz', label: 'Quiz'},
    {value: 'wrap-up', label: 'Wrap-Up'},
]

function createItemScaffold(type){
    const base = {type: type, data: {}};
    switch(type){
        case 'overview':
            base.data ={title: `Module `, content: '', learning_objectives: [] };
            break;
        case 'page':
            base.data ={title: `New Page`, content: '' };
            break;
        case 'assignment':
            base.data ={title: `New Assignment`, purpose: '', criteria:'', examples: [], nonexamples: [], rubric:[], points: 100, due_date: null };
            break;
        case 'discussion':
            base.data ={title: `New Discussion`, prompt: '', graded: false, grading: {points: 100, rubric: []}, inital_due_date: null , replies_due_date: null};
            break;
        case 'quiz':
            base.data ={title: `New Quiz`, questions: [], time_limit: null, attempts: 1, points: 100};
            break;
        case 'wrap-up':
            base.data ={title: `Wrap-Up`, content: ''};
            break;
    }
    return base;
}
function handleAddItem(type){
    const newItem = createItemScaffold(type);
    router.post(`/modules/${props.module.id}/items`, {
        ...newItem
    }, {
        preserveScroll: true,
        onSuccess: () => {
            // Note: The page will reload, so we don't need to update localItems manually.
        }
    });
}

function handleUpdateItem(updatedItem){
    router.put(`/items/${updatedItem.id}`, {
        ...updatedItem.data
    }, {
        preserveScroll: true
    });
}

function handleDeleteItem(itemId){
    router.delete(`/items/${itemId}`, {
        preserveScroll: true
    });
}

function handleReorder() {
    const orderedItems = localItems.value.map((item, index) => {
        return {
            id: item.id,
            order: index + 1
        };
    });

    router.post(`/modules/${props.module.id}/items/reorder`, {
        items: orderedItems
    }, {
        preserveScroll: true
    });
}

</script>

<style scoped>

</style>