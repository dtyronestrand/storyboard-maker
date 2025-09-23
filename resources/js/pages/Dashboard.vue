<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { dashboard } from '@/routes';

import { type BreadcrumbItem } from '@/types';
import { Head, usePage, Link, router } from '@inertiajs/vue3';
import { ref , h} from 'vue';
import CreateCourseModal from '@/components/CreateCourseModal.vue';
import {
    useVueTable,
    FlexRender,
    getCoreRowModel,
    getSortedRowModel,
} from '@tanstack/vue-table';
import ViewButton from '@/components/Courses/ViewButton.vue';

interface Props{
    courses:{
        id: string,
        prefix: string,
        number: number,
        name: string, 
        objectives: string[],
    }[]
}
const page = usePage();
const props = defineProps<Props>();
const sorting = ref([])
const data = ref(props.courses)
const columnsCourses = [
    {
        accessorKey: 'prefix',
        header: 'Prefix',
        cell: info => info.getValue().toUpperCase()
    },
    {
        accessorKey: 'number',
        header: 'Number',
        enableSorting: false,
    },
    {
        accessorKey: 'name',
        header: 'Name',
        enableSorting: false,
    },
    {
        accessorKey: 'actions',
        header: '',
        cell: ({row}) => h(ViewButton, {courseId: row.original.id}),
        enableSorting: false,
    },
]
const table = useVueTable({
data: data.value,
columns: columnsCourses,
getCoreRowModel: getCoreRowModel(),
getSortedRowModel: getSortedRowModel(),
state: {
    get sorting() {
        return sorting.value;
    },
},
onSortingChange: updaterOrValue => {
    sorting.value =
        typeof updaterOrValue === 'function'
            ? updaterOrValue(sorting.value)
            : updaterOrValue;
},
})

const showCreateCourseModal = ref(false);

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Dashboard',
        href: dashboard().url,
    },
];


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
         <tr v-for="headerGroup in table.getHeaderGroups()" :key="headerGroup.id">
         <th @click="header.column.getToggleSortingHandler()?.($event)" v-for="header in headerGroup.headers" :key="header.id" scope="col">
         <FlexRender :render="header.column.columnDef.header" :props="header.getContext()"/>
         </th>
         </tr>
         </thead>
    <tbody>
    <tr v-for="row in table.getRowModel().rows" :key="row.id" class="hover:bg-accent/50">
    <td v-for="cell in row.getVisibleCells()" :key="cell.id">
    <FlexRender :render="cell.column.columnDef.cell" :props="cell.getContext()"/>
    
    </td>
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