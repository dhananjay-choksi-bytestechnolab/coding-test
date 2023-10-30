<template>
<div class="w-[300px] bg-sky-950 rounded-lg shadow-lg max-h-[680px]">
    <div class="p-4">
        <div class="flex items-center justify-between sticky">
            <h2 class="text-lg text-zinc-100 font-black mb-3">
            {{ kanban.phases[props.phase_id].name }} {{ kanban.phases[props.phase_id].total_task_count ? '(' + kanban.phases[props.phase_id].total_task_count + ')' : ""}}
            </h2>
           <div class="flex items-cente gap-3">

            <TrashIcon
                @click="deleteColumn(props.phase_id)" 
                class="mb-3 h-6 w-6 text-white hover:cursor-pointer" 
                aria-hidden="true" />
             <PlusIcon 
                @click="createTask()" 
                class="mb-3 h-6 w-6 text-white hover:cursor-pointer" 
                aria-hidden="true" />
                </div>
        </div>

        <div class="max-h-[530px] overflow-x-auto overflow-y-auto scroll-bar mb-2">
        <task-card v-if="kanban.phases[props.phase_id].tasks.length > 0" v-for="task in kanban.phases[props.phase_id].tasks" :task="task" />
        </div>
        <!-- A card to create a new task -->
        <div class="w-full flex items-center justify-between bg-white text-gray-900 hover:cursor-pointer shadow-md rounded-lg p-3 relative"
            @click="createTask()">
            <span>Create a new task</span>
            <PlusIcon class="h-6 w-6" aria-hidden="true" />
        </div>

    </div>
</div>
</template>

<script setup>
// get the props
import { useKanbanStore } from '../stores/kanban'
import { PlusIcon, TrashIcon } from '@heroicons/vue/20/solid'
import Swal from 'sweetalert2';
import commonService from '../services/common-services'

const kanban = useKanbanStore()

const props = defineProps({
    phase_id: {
        type: Number,
        required: true
    },
})

const createTask = function () {
    kanban.creatingTask = true;
    kanban.creatingTaskProps.phase_id = props.phase_id;
}

const deleteColumn = async (phase_id) => {

    const result = await Swal.fire({
        title: 'Are you sure?',
        text: 'You will not be able to recover this column!',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Yes, delete it!',
        cancelButtonText: 'No, keep it',
    });

    if (result.isConfirmed) {
        // Show a loading SweetAlert
        const loadingAlert = Swal.fire({
            title: 'Deleting Column',
            html: 'Please wait while the column is being deleted...',
            allowOutsideClick: false,
            showConfirmButton: false,
            didOpen: () => {
                Swal.showLoading();
            },
        });

        try {
            // Make the API request to delete the column
            await deletePhase(phase_id);

            // Close the loading SweetAlert once the API request is successful
            loadingAlert.close();

            // Perform other logic once the API request is successful
            // Refresh tasks or update the state accordingly
            await commonService.refreshTasks(kanban);
        } catch (error) {
            console.error('There was an error deleting the column!', error);

            // Close the loading SweetAlert on error
            loadingAlert.close();
            // Handle the error
        }
    } else if (result.dismiss === Swal.DismissReason.cancel) {
        console.log('Cancelled');
    }
};

const deletePhase = async (phase_id) => {
    try {
        const response = await axios.delete('/api/phases/' + phase_id);
        // You can handle the response here if needed
    } catch (error) {
        console.error('There was an error deleting the task!', error);
        // Handle the error
    }
}

</script>

<style> 
 .scroll-bar::-webkit-scrollbar {
  width: 0px;
}
</style>