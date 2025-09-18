<template>
    <div>
        <h1 class="text-2xl font-bold mb-4">Rubrics</h1>
        <table class="table-auto w-full border-collapse border border-accent">
            <thead>
                <tr>
                    <th class="border border-accent bg-base-200 p-2">Title</th>
                    <th class="border border-accent bg-base-200 p-2">Actions</th>
                </tr>
            </thead>
            <tbody>
                <tr v-for="rubric in rubrics" :key="rubric.id">
                    <td class="border border-accent bg-base-100 p-2">{{ rubric.title }}</td>
                    <td class="border border-accent bg-base-100 p-2">
                    <button @click="viewRubric(rubric.id)" class="btn btn-sm btn-info mr-2">View</button>
                        <button @click="editRubric(rubric.id)" class="btn btn-sm btn-primary mr-2">Edit</button>
                        <button @click="deleteRubric(rubric.id)" class="btn btn-sm btn-error">Delete</button>
                    </td>
                </tr>
            </tbody>
        </table>
        <button @click="createRubric" class="btn btn-primary mt-4">Create New Rubric</button>
    </div>
</template>

<script setup lang="ts">
const 
</script>

<style scoped>

</style>