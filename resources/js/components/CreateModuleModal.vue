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
         <div v-for="(objective, index) in objectives" :key="index">
                <input v-model="objectives[index]" type="text" required />
                <button type="button" @click="objectives.splice(index, 1)">Remove Objective</button>
            </div>
            <button type="button" class="btn btn-info" @click="objectives.push('')">Add Objective</button> 
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

const newModule = () => {
    emits('newModule', number.value, title.value, objectives.value);
    emits('close');
};
</script>

<style scoped>
@reference "../../css/app.css";

input {
    @apply border-2 bg-base-100 text-base-content border-primary rounded-md p-2 w-full;
}
</style>