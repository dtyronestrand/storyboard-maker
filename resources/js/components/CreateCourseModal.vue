<template>
    <div class="fixed inset-0 z-50 flex items-center justify-center bg-surface-950/90">
    <div class="bg-surface-500 p-6 rounded-lg shadow-lg w-full max-w-md">
        <h1 class="text-center text-3xl mb-12">Create New Course</h1>
        <form class="flex flex-row flex-wrap gap-8 justify-around" @submit.prevent="createCourse">
            <label for="prefix">Course Prefix:</label>
            <input id="prefix" v-model="prefix" type="text" required />
            <label for="number">Course Number:</label>
            <input id="number" v-model="number" type="number" required />
            <label for="name">Course Name:</label>
            <input id="name" v-model="name" type="text" required />
            <p>Objectives</p>
            <div v-for="(objective, index) in objectives" :key="index">
                <input v-model="objectives[index]" type="text" required />
                <button type="button" @click="objectives.splice(index, 1)">Remove Objective</button>
            </div>
            <button type="button" @click="objectives.push('')">Add Objective</button> 
            <button type="submit">Create Course</button>
            <button type="button" @click="$emit('close')">Cancel</button>
        </form>
        </div>
    </div>
</template>

<script setup lang="ts">
import {ref, defineEmits} from 'vue';
const prefix = ref('');
const number = ref(null);
const objectives = ref([]);
const name = ref('');
const emits = defineEmits(['close', 'createCourse']);
const createCourse = () => {
    emits('createCourse', prefix.value, number.value, name.value, objectives.value);
    emits('close');
};
</script>

<style scoped>
@reference ../../css/app.css;

input{
    @apply bg-[var(--color-tertiary-200)] border border-[var(--color-primary-500)];
}
</style>