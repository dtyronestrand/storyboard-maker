<template>
    <div class="flex flex-col flex-wrap gap-8 p-4 ">
        <h1 class="text-2xl text-center">New Module</h1>
        <form class="flex flex-col gap-4" @submit.prevent="newModule">
            <label for="number">Module Number:</label>
            <input type="number" v-model="number" id="number" name="number" required />
            <br />
            <label for="title">Module Title:</label>
            <input type="text" id="title" v-model="title" name="title" required />
            <br />
        <div>Module Objectives</div>
         <div v-for="(objective, index) in objectives" :key="index" class="border p-4 rounded mb-4">
                <label>Objective Text:</label>
                <input v-model="objectives[index].objective" type="text" required />
                <label class="mt-2">Aligned CLOs:</label>
                <select v-model="objectives[index].aligned_CLOs" multiple class="border p-2 w-full min-h-[100px]">
                    <option v-if="!props.course?.objectives?.length" disabled>No course objectives available</option>
                    <option v-for="(clo, cloIndex) in props.course?.objectives || []" :key="cloIndex" :value="clo">{{ clo }}</option>
                </select>
                <button type="button" class="btn btn-error mt-2" @click="objectives.splice(index, 1)">Remove Objective</button>
            </div>
            <button type="button" class="btn btn-info" @click="objectives.push({objective: '', aligned_CLOs: []})">Add Objective</button> 
            <button type="submit" class="btn btn-success">Create Module</button>
            <button type="button" class="btn btn-error" @click="$emit('close')">Cancel</button>
        </form>
    </div>
</template>

<script setup lang="ts">
import {ref} from 'vue';
const objectives = ref([]);
const title = ref('');
const number = ref(null);
const emits = defineEmits(['newModule', 'close'])
const props = defineProps({
    course: Object
})

const newModule = () => {
    emits('newModule', number.value, title.value, objectives.value);
    emits('close');
};
</script>

<style scoped>
@reference "../../css/app.css";

input, select {
    @apply border-2 bg-base-100 text-base-content border-primary rounded-md p-2 w-full;
}
</style>