<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { dashboard } from '@/routes';
import { show as courseShow } from '@/routes/courses';
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
        <div class="flex h-full flex-1 flex-col gap-4 overflow-x-auto rounded-xl p-4">
            <div class="grid auto-rows-min gap-4 md:grid-cols-3" >
                <div v-for="course in page.props.courses" :key="course.id" class="relative aspect-video overflow-hidden rounded-xl border border-sidebar-border/70 dark:border-sidebar-border">
                    <h2 class="absolute top-2 left-2 z-10 rounded-sm bg-black/50 px-2 py-1 text-sm text-white">{{course.prefix}} {{course.number}} {{ course.name }}</h2>
                    <Link :href="courseShow.url(course.id)" class="absolute bottom-2 right-2 z-10 rounded-sm bg-black/50 px-2 py-1 text-sm text-white">View Course</Link>
                </div>
            </div>
        </div>
        <button type="button" class="fixed bottom-6 right-6 inline-flex items-center rounded-full bg-primary px-4 py-2 text-sm font-medium text-white shadow-lg hover:bg-primary-dark focus:outline-none focus:ring-2 focus:ring-primary focus:ring-offset-2 dark:focus:ring-offset-gray-800" @click="createCourse">
            Create New Course
        </button>
        <CreateCourseModal v-if="showCreateCourseModal" @close="showCreateCourseModal = false" @createCourse="newCourse" />
    </AppLayout>
</template>
