<template>
    <div>
        <draggable class="bg-base-300" v-model="localItems" item-key="id" animation="200" handle=".drag-handle">
         <div v-for ="(item, index) in localItems" :key="item.id" class="mb-4">
            <ModuleItem :item="item" :index="index" :edit="props.edit" :module-id="module.id" :force-edit="item._justAdded" @update="handleUpdateItem" @delete="handleDeleteItem"/>
            </div>
        </draggable>
        <div class="mt-4" v-if="edit">
        <select class="border p-2 bg-base-200 border-accent" v-model="selectedType" @change="handleAddItem">
        <option value="" disabled>--Add New Item--</option>
        <option v-for="type in itemTypes" :key="type.value" :value="type.value">{{type.label}}</option>
        </select>
        <button class="btn btn-md btn-success ml-4" @click="saveChanges" :disabled="localItems.length === (props.module.items || []).length && localItems.every((item, idx) => item.id === (props.module.items || [])[idx]?.id)">Save Changes</button>
        </div>
    
    </div>
</template>

<script setup lang="ts">
import {ref} from 'vue';
import {router} from "@inertiajs/vue3";
import {v4 as uuidv4} from 'uuid';
import { VueDraggableNext as draggable } from 'vue-draggable-next';
import ModuleItem from './ModuleItem.vue';

const props = defineProps({
    module: Object, 
    edit: {type: Boolean, default: false}
})

// Handle both new ModuleItem structure and legacy JSON structure
const getItemsArray = () => {
    if (props.module.module_items && props.module.module_items.length > 0) {
        // New structure: convert ModuleItem objects to legacy format for compatibility
        return props.module.module_items.map(item => ({
            id: item.id,
            type: item.type,
            data: {
                title: item.title,
                ...item.data,
                // Add quiz questions back to data for compatibility
                ...(item.quiz_questions ? { questions: item.quiz_questions.map(q => ({
                    question: q.question_text,
                    type: q.question_type,
                    options: q.options,
                    correct_answer: q.correct_answer,
                    points: q.points
                })) } : {})
            }
        }));
    }
    return props.module.items || [];
};

const localItems = ref([...getItemsArray()]);
const selectedType = ref('');

const itemTypes = [
    {value: 'overview', label: 'Overview'},
    {value: 'page', label: 'Page'},
    {value: 'assignment', label: 'Assignment'},
    {value: 'discussion', label: 'Discussion'},
    {value: 'quiz', label: 'Quiz'},
    {value: 'wrap-up', label: 'Wrap-Up'},
]

function createItemScaffold(type){
    const base = {id: uuidv4(), type: type, data: {}};
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
function handleAddItem(){
    if (!selectedType.value) return;
    
    const newItem = createItemScaffold(selectedType.value);
    newItem._justAdded = true; // Flag to force edit mode only for just-added items
    localItems.value.push(newItem);
    selectedType.value = ''; // Reset dropdown
}
function handleUpdateItem(updatedItem){
    const index = localItems.value.findIndex(item => item.id === updatedItem.id);
    if (index !== -1){
        // Remove the _justAdded flag when item is updated
        delete updatedItem._justAdded;
        localItems.value[index] = updatedItem;
    }
}

function handleDeleteItem(itemId){
    router.delete(`/modules/${props.module.id}/items/${itemId}`, {
        preserveScroll: true,
        onSuccess: () => {
            localItems.value = localItems.value.filter(item => item.id !== itemId);
        }
    });
}

function saveChanges(){
    router.put(`/modules/${props.module.id}/items`, {
        module: props.module.id,
        items: localItems.value
    }, {
        preserveScroll: true
    });
}

</script>

<style scoped>

</style>