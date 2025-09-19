<template>
    <div>
        <h1 class="text-2xl font-bold mb-4">Edit Rubric</h1>
        <form @submit.prevent="updateRubric" class="space-y-6">
            <div>
                <label for="title" class="label font-bold">Rubric Title</label>
                <input id="title" v-model="form.title" type="text" class="input input-bordered w-full"/>
            </div>

            <hr class="my-4 border-accent"/>

            <div>
                <h2 class="text-xl font-bold mb-2">Performance Levels</h2>
                <div v-for="(level, index) in form.performance_levels" :key="index" class="flex items-center mb-2">
                    <input v-model="form.performance_levels[index]" type="text" class="input input-bordered w-full mr-2"/>
                    <button @click.prevent="removePerformanceLevel(index)" class="btn btn-sm btn-error">Remove</button>
                </div>
                <button @click.prevent="addPerformanceLevel" class="btn btn-sm btn-secondary">Add Performance Level</button>
            </div>

            <hr class="my-4 border-accent"/>

            <div>
                <h2 class="text-xl font-bold mb-2">Criteria</h2>
                <div v-for="(criterion, critIndex) in form.criteria" :key="critIndex" class="mb-4 p-4 border border-accent rounded-lg bg-base-200">
                     <div class="flex justify-between items-start">
                        <div class="w-full mr-4">
                            <label class="label font-bold">Criterion Name</label>
                            <input v-model="criterion.name" type="text" class="input input-bordered w-full mb-2"/>
                            <label class="label font-bold">Description</label>
                            <textarea v-model="criterion.description" class="textarea textarea-bordered w-full"></textarea>
                        </div>
                        <button @click.prevent="removeCriterion(critIndex)" class="btn btn-sm btn-error mt-8">Remove</button>
                    </div>

                    <div class="mt-4" v-if="form.performance_levels.length > 0">
                        <h3 class="font-bold mb-2">Level Details:</h3>
                        <div v-for="level in form.performance_levels" :key="level" class="mb-3">
                            <label class="label font-semibold text-primary">{{ level }}</label>
                            <textarea v-model="criterion.level_descriptions[level]" class="textarea textarea-bordered w-full mb-1"></textarea>
                            <input v-model.number="criterion.level_points[level]" type="number" class="input input-sm input-bordered w-1/4" placeholder="Points"/>
                        </div>
                    </div>
                </div>
                <button @click.prevent="addCriterion" class="btn btn-sm btn-secondary">Add Criterion</button>
            </div>

            <hr class="my-4 border-accent"/>

            <button type="submit" class="btn btn-primary">Save Changes</button>
        </form>
    </div>
</template>

<script setup lang="ts">
import { ref } from 'vue';
import { router } from '@inertiajs/vue3';

const props = defineProps<{
    rubric: any;
}>();

// Use ref to create a reactive copy of the rubric prop for editing.
const form = ref({
    title: props.rubric.title,
    performance_levels: [...props.rubric.performance_levels],
    criteria: JSON.parse(JSON.stringify(props.rubric.criteria)), // Deep copy
});

function addPerformanceLevel() {
    form.value.performance_levels.push('');
}

function removePerformanceLevel(index: number) {
    form.value.performance_levels.splice(index, 1);
}

function addCriterion() {
    form.value.criteria.push({
        name: '',
        description: '',
        level_descriptions: {},
        level_points: {},
    });
}

function removeCriterion(index: number) {
    form.value.criteria.splice(index, 1);
}

// Submits the form data to the back-end to update the rubric.
function updateRubric() {
    router.put(`/rubrics/${props.rubric.id}`, form.value, {
         onSuccess: () => {
            router.visit('/rubrics');
        }
    });
}
</script>

<style scoped>
/* Scoped styles can be added here if needed */
</style>