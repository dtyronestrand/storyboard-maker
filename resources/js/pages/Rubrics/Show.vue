<template>
    <AppLayout :breadcrumbs="breadcrumbs">
    <div>
        <h1 class="text-2xl font-bold mb-4">{{ rubric.title }}</h1>
        <table class="table-auto w-full border-collapse border border-accent">
            <thead>
                <tr>
                    <th class="border border-accent bg-base-200 p-2">Criteria</th>
                    <th v-for="level in rubric.performance_levels" :key="level" class="border border-accent bg-base-200 p-2">{{ level }}</th>
                </tr>
            </thead>
            <tbody>
                <tr v-for="criterion in rubric.criteria" :key="criterion.name">
                    <td class="border border-accent bg-base-100 p-2">
                        <p class="font-bold">{{ criterion.name }}</p>
                        <p>{{ criterion.description }}</p>
                    </td>
                    <td v-for="level in rubric.performance_levels" :key="level" class="border border-accent bg-base-100 p-2">
                        <p>{{ criterion.level_descriptions[level] }}</p>
                        <p class="font-bold">{{ criterion.level_points[level] }} points</p>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
    </AppLayout>
</template>

<script setup lang="ts">
import {type BreadcrumbItem} from '@/types';
import {rubrics as rubricsIndex} from '@/routes';
import AppLayout from '@/layouts/AppLayout.vue'; 
const { rubric } = defineProps<{
    rubric: any
}>()


const breadcrumbs: BreadcrumbItem = [
    {title: 'Dashboard', href: '/dashboard'},
    {title: 'Rubrics', href: rubricsIndex().url},
    {title: rubric.title, href: `/rubrics/${rubric.id}`}
]

</script>

<style scoped>

</style>