<template>
    <AppLayout>
    <div>
        <h1 class="text-2xl font-bold mb-4">Create Rubric</h1>
        <form @submit.prevent="createRubric" class="space-y-6">
            <div>
                <label for="title" class="label font-bold">Rubric Title</label>
                <input id="title" v-model="form.title" type="text" class="input input-bordered w-full" placeholder="e.g., Project Presentation Rubric"/>
            </div>

            <hr class="my-4 border-accent"/>

            <div>
                <h2 class="text-xl font-bold mb-2">Performance Levels</h2>
                <p class="mb-4">Define the columns for your rubric, from lowest to highest (e.g., "Beginning", "Developing", "Accomplished", "Exemplary").</p>
                <div v-for="(level, index) in form.performance_levels" :key="index" class="flex items-center mb-2">
                    <input v-model="form.performance_levels[index]" type="text" class="input input-bordered w-full mr-2" placeholder="Level Name"/>
                    <button @click.prevent="removePerformanceLevel(index)" class="btn btn-sm btn-error">Remove</button>
                </div>
                <button @click.prevent="addPerformanceLevel" class="btn btn-sm btn-secondary">Add Performance Level</button>
            </div>

            <hr class="my-4 border-accent"/>

            <div>
                <h2 class="text-xl font-bold mb-2">Criteria</h2>
                <p class="mb-4">Define the rows for your rubric. Each criterion will have a description and points for each performance level.</p>
                <div v-for="(criterion, critIndex) in form.criteria" :key="critIndex" class="mb-4 p-4 border border-accent rounded-lg bg-base-200">
                    <div class="flex justify-between items-start">
                        <div class="w-full mr-4">
                            <label class="label font-bold">Criterion Name</label>
                            <input v-model="criterion.name" type="text" class="input input-bordered w-full mb-2" placeholder="e.g., Content Knowledge"/>
                            <label class="label font-bold">Description</label>
                            <textarea v-model="criterion.description" class="textarea textarea-bordered w-full" placeholder="Describe the core skill or expectation for this criterion."></textarea>
                        </div>
                        <button @click.prevent="removeCriterion(critIndex)" class="btn btn-sm btn-error mt-8">Remove</button>
                    </div>

                    <div class="mt-4" v-if="form.performance_levels.length > 0">
                        <h3 class="font-bold mb-2">Level Details:</h3>
                        <div v-for="level in form.performance_levels" :key="level" class="mb-3">
                            <label class="label font-semibold text-primary">{{ level }}</label>
                            <textarea v-model="criterion.level_descriptions[level]" class="textarea textarea-bordered w-full mb-1" :placeholder="`Description for ${level}`"></textarea>
                            <input v-model.number="criterion.level_points[level]" type="number" class="input input-sm input-bordered w-1/4" placeholder="Points"/>
                        </div>
                    </div>
                </div>
                <button @click.prevent="addCriterion" class="btn btn-sm btn-secondary">Add Criterion</button>
            </div>
            
            <hr class="my-4 border-accent"/>

            <button type="submit" class="btn btn-primary">Save</button>
        </form>
    </div>
    </AppLayout>
</template>

<script setup lang="ts">
import { ref } from 'vue';
import { router } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';


// The main form object that holds all the rubric data.
const form = ref({
    title: '',
    performance_levels: ['Beginning', 'Developing', 'Proficient', 'Exemplary'], // Pre-populated for convenience
    criteria: [],
});

// Adds a new, empty performance level to the array.
function addPerformanceLevel() {
    form.value.performance_levels.push('');
}

// Removes a performance level by its index.
function removePerformanceLevel(index: number) {
    form.value.performance_levels.splice(index, 1);
}

// Adds a new criterion object to the criteria array.
// It initializes the level_descriptions and level_points objects
// to ensure they are reactive.
function addCriterion() {
    form.value.criteria.push({
        name: '',
        description: '',
        level_descriptions: {},
        level_points: {},
    });
}

// Removes a criterion from the array by its index.
function removeCriterion(index: number) {
    form.value.criteria.splice(index, 1);
}

// Submits the form data to the back-end to create the new rubric.
function createRubric() {
    router.post('/rubrics', form.value, {
        onSuccess: () => {
            // You can add a redirect or a success message here.
            router.visit('/rubrics');
        }
    });
}
</script>

<style scoped>
/* Scoped styles can be added here if needed */
</style>