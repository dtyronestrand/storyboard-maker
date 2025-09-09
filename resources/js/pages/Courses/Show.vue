<template>
    <AppLayout>
    <div>
     <h1 class="text-5xl pt-8" >{{page.props.course.prefix}} {{ page.props.course.number }} {{ page.props.course.name }}</h1>
     <h2 class="text-2xl pt-8 mb-4">Course Objectives</h2>
        <ul>
            <li v-for="objective in page.props.course.objectives" :key="objective.id">{{ objective}}</li>
        </ul>
     <h2 class="text-2xl pt-8 mb-4">Modules</h2>
        <ul>
            <span v-if="!editModule && page.props.course.modules.length > 0">
            <div  v-for="module in page.props.course.modules" :key="module.id" class="border p-4 rounded mb-4">
                <h3 class="text-xl pt-4 pb-4">Module {{module.number}} {{ module.title }}</h3>
                <h4 class="mb-4">Learning Objectives:</h4>
                <ul v-for="objective in module.objectives" :key="objective.id">
                    <li>{{ objective }}</li>
                </ul>
          <ModuleItemsList :module="module"/>
          </div>
            </span>
            <span v-else>
            <li v-for="module in page.props.course.modules" :key="module.id">
                Module <input v-model="module.number" type="number" class="w-16"/>: <input v-model="module.title" type="text" />
           <ul>
           <li v-for="(objective, index) in module.objectives" :key="index">
                <input v-model="module.objectives[index]" type="text" required />
                <button type="button" @click="module.objectives.splice(index, 1)" class="btn btn-sm btn-error">Remove Objective</button>
            </li>
            <button type="button" class="btn btn-sm btn-info" @click="module.objectives.push('')">Add Objective</button>
           </ul>

           <div class="pt-8">
           <button type="button" @click="editModule=false" class="btn btn-md btn-error">Cancel</button>
            <button type="button" @click="updateModule" class="btn btn-md btn-success">Save Module</button>
           </div>
              </li>
</span>

        </ul>
       <button type="button" @click="addModule" class="btn btn-primary rounded-xl mt-8">Add Module</button>
    </div>
    <CreateModuleModal v-if="createModuleModal" @close="createModuleModal=false" @newModule="saveModule"/>
    </AppLayout>
</template>

<script setup lang="ts">
import {usePage, router} from "@inertiajs/vue3";
import AppLayout from '@/layouts/AppLayout.vue';
import {ref} from "vue";
import CreateModuleModal from '@/components/CreateModuleModal.vue';
import ModuleItemsList from '@/components/ModuleItems/ModuleItemsList.vue';

const page = usePage();
const createModuleModal = ref(false);
const editModule = ref(false);

const addModule = ()=>{
    createModuleModal.value = true;
}
const saveModule = (number: number, title: string, objectives: string[]) => {
    router.post('/modules', {
        course_id: page.props.course.id,
        number,
        title,
        objectives
    }, {
        onSuccess: () => {
            createModuleModal.value = false;
        },
        onFinish: () => {
            router.reload();
        }
    });
};
</script>

<style scoped>

</style>