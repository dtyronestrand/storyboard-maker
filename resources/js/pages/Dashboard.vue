<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { dashboard } from '@/routes';

import { type BreadcrumbItem } from '@/types';
import { Head, usePage, Link, router } from '@inertiajs/vue3';
import { ref } from 'vue';
import CreateCourseModal from '@/components/CreateCourseModal.vue';


const showCreateCourseModal = ref(false);

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Dashboard',
        href: dashboard().url,
    },
];
const page = usePage();

const createCourse = () => {
    showCreateCourseModal.value = true;
}

const newCourse = (prefix, number, name, objectives) => {
    router.post('/courses', {
        prefix,
        number,
        name,
        objectives,
    }, {
        onSuccess: () => {
            showCreateCourseModal.value = false;
        },
        onFinish: () => {
            router.reload();
        }
    });
}
</script>

<template>
    <Head title="Storyboard Maker - Dashboard" />

    <AppLayout :breadcrumbs="breadcrumbs">
    <div class="flex-1 flex flex-col min-h-[100dvh]">
       <div class=" flex-1 p-[0.5rem] overflow-y-auto bg-base-300">
         <div class="bg-base-100 w-full overflow-x-auto mb-[20px] shadow-[0 2px 4px rgba(0,0,0,0.1)]">
         <table class="min-w-[600px] w-full border-collapse">
         <thead>
         <tr>
         <th data-column="0">Prefix<i-flowbite-caret-sort-solid class="icon"></i-flowbite-caret-sort-solid></th>
         <th data-column="1">Number<i-flowbite-caret-sort-solid class="icon"></i-flowbite-caret-sort-solid></th>
         <th data-column="2">Name<i-flowbite-caret-sort-solid class="icon"></i-flowbite-caret-sort-solid></th>
            <th data-column="3"></th>
         </tr>
         </thead>
         <tbody>
         <tr v-for="course in page.props.courses" :key="course.id" class="hover:bg-accent/50">
            <td>{{ course.prefix.toUpperCase()}}</td>
            <td>{{ course.number }}</td>
            <td>{{ course.name }}</td>
            <td><Link :href="`/courses/${course.id}`" class="rounded-sm bg-info px-2 py-1 text-sm text-info-content hover:bg-primary-dark">View Course</Link></td>
         </tr>
         </tbody>
         </table>
         </div>

          
          
        </div>
        <button type="button" class="fixed bottom-6 right-20 inline-flex items-center rounded-full bg-info px-4 py-2 text-sm font-medium text-info-content shadow-lg hover:bg-primary-dark focus:outline-none focus:ring-2 focus:ring-primary focus:ring-offset-2 dark:focus:ring-offset-accent" @click="createCourse">
            Create New Course
        </button>
        <CreateCourseModal v-if="showCreateCourseModal" @close="showCreateCourseModal = false" @createCourse="newCourse" />
    </div>
    </AppLayout>
</template>
<style scoped>
@reference '../../css/app.css';

th, td {
    padding: 12px 15px;
    white-space: nowrap;
    text-align: left;
    @apply border-b border-accent;
}

th {
    @apply bg-primary text-primary-content font-[600] relative cursor-pointer top-0;
    user-select: none;

}

th .icon{
    @apply ml-[0.5rem] opacity-70;
    display: inline;
    vertical-align: middle;
}



tr:nth-child(even){
    @apply bg-base-200 text-base-content hover:bg-accent/50;
}
</style>