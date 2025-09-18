<template>
    <div>
        <h1 class="text-2xl font-bold mb-4">Edit Rubric</h1>
        <table>
        <thead>
        <tr>
            <th class="border border-accent bg-base-200 p-2">Criterion</th>
            <th v-for="level in props.performance_levels" :key="level.id" class="border border-accent bg-base-200 p-2"><input v-model="level.name" type="text"/></th>
            <th class="border border-accent bg-base-200 p-2">Total Points</th>
        </tr>
        </thead>
        <tbody>
        <tr v-for="criterion in props.rubric_criteria" :key="criterion.id">
            <td class="border border-accent bg-base-100 p-2"><input type="text"  v-model="criterion.name"/><br/><textarea v-model="criterion.description"/></td>
            <td v-for="level in props.performance_level" :key="level.id" class="border border-accent bg-base-100 p-2">
            <textarea v-model="criterion.level_descriptions[level.id]" class="w-full border border-accent bg-base-100 p-1 rounded mb-1"/><br/><input type="number" v-model="criterion.level_points[level.id]" class="w-full border border-accent bg-base-100 p-1 rounded mb-1"/>
            </td>
            <td class="border border-accent bg-base-100 p-2"><input type="number" v-model="totalPoints"/></td>
        </tr>
        </tbody>
        </table>
    </div>
</template>

<script setup lang="ts">
import {ref, computed} from 'vue';
const props = defineProps({
    title: string,
    performance_levels: Array<string>,
    rubric_criterion: Array as () => ({
        id: number,
        name: string,
        description: string,
        level_descriptions: Record<number, string>,
        level_points: Record<number, number>
    })[],
})

localRubric = ref(props.rubric_criteria);
localPerformanceLevels = ref(props.performance_levels);
localTitle = ref(props.title);

const totalPoints = computed(() => {
    return localRubric.value.reduce((sum, criterion) => {
        return sum + Object.values(criterion.level_points).reduce((levelSum, points) => levelSum + points, 0);
    }, 0);
});

const emits = defineEmits(['updateRubric', localTitle, localPerformanceLevels, localRubric]);
</script>

<style scoped>

</style>