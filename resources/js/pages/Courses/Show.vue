<template>
    <AppLayout>
    <div>
     <h1 class="text-5xl pt-8" >{{page.props.course.prefix}} {{ page.props.course.number }} 
     <br/>
     {{ page.props.course.name }}</h1>
     <h2 class="text-2xl pt-8 mb-4">Course Objectives</h2>
        <ul>
            <li v-for="objective in page.props.course.objectives" :key="objective.id">{{ objective}}</li>
        </ul>
     <h2 class="text-2xl pt-8 mb-4">Modules</h2>
        <ul>
            <span v-if="!editModule && page.props.course.modules.length > 0">
            <div  v-for="module in page.props.course.modules" :key="module.id" class=" rounded mb-4">
                <h3 class="text-xl pt-4 pb-4  pl-4 border-2 border-accent bg-primary text-primary-content">Module {{module.number}} {{ module.title }}</h3>
                <div class="p-4 border-2 border-accent bg-secondary text-secondary-content">
                <h4 class="text-lg mb-4">Module Objectives:</h4>
               <div v-if="module.objectives && module.objectives.length > 0">
                   <div v-for="(objective, index) in module.objectives" :key="index" class=" p-4 rounded mb-4">
                    <ol>
                    <li>{{ typeof objective === 'string' ? objective : objective.objective }} <span v-if="objective.aligned_CLOs" v-for="(clo, cloIndex) in objective.aligned_CLOs" :key="cloIndex">CLO: {{ cloIndex + 1 }}</span></li>
                    </ol>
                </div>
               </div>
               <div v-else>
                   <p class="text-gray-500">No objectives defined</p>
                </div>
               </div>
            
          <ModuleItemsList :module="module"/>
          <div class="button-group">
             <button type="button" @click="editModule=true; convertObjectivesToObjects()" class="btn border border-accent btn-md btn-info">Edit Module</button>
             <button type="button" @click="deleteModule(module)" class="btn border border-accent btn-md btn-error">Delete Module</button>
          </div>
          </div>
       
            </span>
            <span v-else>
            <li v-for="module in page.props.course.modules" class="text-lg" :key="module.id">
                Module <input v-model="module.number" type="number" class="border pl-2 border-accent w-16"/>: <input v-model="module.title" type="text" />
             <h4 class="mb-4 mt-8" >Module Objectives:</h4>
               <div v-for="(objective, index) in (module.objectives || [])" :key="index" class=" flex flex-col p-4 rounded mb-4">
                <label>Objective:</label>
                <input class="border-b" v-model="module.objectives[index].objective" type="text" required />
                <label class="mt-2">Aligned CLOs: </label>
                <p class="text-sm pt-4"><em>Hold Ctrl and click to select multiple.</em></p>
                <select v-model="module.objectives[index].aligned_CLOs" multiple class="border px-4 pt-2 max-w-[100dvw] [max-content] min-h-[100px]">
                    <option v-if="!page.props.course?.objectives?.length" disabled>No course objectives available</option>
                    <option v-for="(clo, cloIndex) in page.props.course?.objectives || []" :key="cloIndex" :value="clo">{{ clo }}</option>
                </select>
                <button type="button" class="max-w-[max-content] btn btn-error mt-2" @click="module.objectives.splice(index, 1)">Remove Objective</button>
            </div>
            <div class="mb-8">
            <button type="button" class="btn btn-info" @click="(module.objectives = module.objectives || []).push({objective: '', aligned_CLOs: []})">Add Objective</button>
            </div> 
 <ModuleItemsList  :edit="true" :module="module"/>
           <div class="pt-8 space-x-4">
           <button type="button" @click="updateModule()" class="btn btn-md btn-success">Save Module</button>
           <button type="button" @click="editModule=false" class="btn btn-md btn-error">Cancel</button>
           </div>
              </li>
</span>

        </ul>
       <button type="button" @click="addModule" :course="page.props.course" class="btn btn-primary rounded-xl mt-8">Add Module</button>
    </div>
    <div>
    <form @submit.prevent="exportToGoogleDocs">
    <button class="btn btn-info mt-12" type="submit">Export to Google Docs</button>
</form>
    </div>
    <CreateModuleModal v-if="createModuleModal" :course="page.props.course" @close="createModuleModal=false" @newModule="saveModule"/>
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

const exportToGoogleDocs = () => {
    router.post(`/storyboard/export/${page.props.course.id}`, {
        course_id: page.props.course.id
    }, {
        onSuccess: () => {
            alert('Export initiated. Please check your Google Drive shortly.');
        },
        onError: (errors) => {
            alert('Error exporting to Google Docs: ' + JSON.stringify(errors));
        }
    });
};
const addModule = ()=>{
    createModuleModal.value = true;
}
const updateModule = ()=>{
    router.put(`/courses/${page.props.course.id}`, {
        modules: page.props.course.modules
    }, {
        onSuccess: () => {
            editModule.value = false;
        },
        onFinish: () => {
            router.reload();
        }
    });
}
const deleteModule = (module) => {
    if(confirm('Are you sure you want to delete this module? This will also delete all associated items.')){
        router.delete(`/modules/${module.id}`, {
            onFinish: () => {
                router.reload();
            }
        });
    }
};
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

const convertObjectivesToObjects = () => {
    page.props.course.modules.forEach(module => {
        if (module.objectives) {
            module.objectives = module.objectives.map(obj => 
                typeof obj === 'string' ? { objective: obj, aligned_CLOs: [] } : obj
            );
        }
    });
};

const convertObjectivesToStrings = () => {
    page.props.course.modules.forEach(module => {
        if (module.objectives) {
            module.objectives = module.objectives.map(obj => 
                typeof obj === 'object' ? obj.objective : obj
            );
        }
    });
};
</script>

<style scoped>
.button-group {
    width: min-content;
    display: flex;
    gap: 0.5rem;
}
</style>