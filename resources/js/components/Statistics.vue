<template>
    <div class="max-w-7xl flex-1 mx-auto flex flex-col overflow-auto sm:px-6 lg:px-8">
        <div class="w-full mb-6 flex">
            <Teleport to="body">
                <generic-modal :show="(kanban.creatingTask || kanban.updatingTask)" @close="closeModel()" key="createTaskModal">
                    <div>
                        <div class="mt-3 sm:mt-2">
                            <DialogTitle as="h3" class="mb-6 text-base font-semibold leading-6 text-gray-900">
                            {{ kanban.creatingTask ? "Create a new task" : "Updating Task" }}
                            </DialogTitle>
                            <div>
                                <label for="name" class="block text-sm font-medium leading-6 text-gray-900">Task description</label>
                                <div class="relative mt-2">
                                    <input type="text" v-model="kanban.creatingTaskProps.name" id="name"
                                        class="peer block w-full border-0 bg-gray-50 py-1.5 text-gray-900 focus:ring-0 sm:text-sm sm:leading-6"
                                        placeholder="Make it productive, but also fun!" />
                                    <p v-if="hasError('name')" 
                                        class="mt-2 text-sm text-red-600" 
                                        id="name-error">
                                            {{ getError('name') }}
                                    </p>
                                    <div class="absolute inset-x-0 bottom-0 border-t border-gray-300 peer-focus:border-t-2 peer-focus:border-blue-600"
                                        aria-hidden="true" />
                                </div>
                            </div>

                            <Listbox as="div" v-model="kanban.creatingTaskProps.user_id" class="mt-8">
                                <ListboxLabel class="block text-sm font-medium leading-6 text-gray-900">Assigned to</ListboxLabel>
                                <div class="relative mt-2">
                                    <ListboxButton
                                        class="relative w-full cursor-default rounded-md bg-white py-1.5 pl-3 pr-10 text-left text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 focus:outline-none focus:ring-2 focus:ring-blue-500 sm:text-sm sm:leading-6">
                                        <span class="flex items-center">
                                            <img :src="getAvatar(kanban.users[kanban.creatingTaskProps.user_id || kanban.self.id])" 
                                                 alt="" 
                                                 class="h-5 w-5 flex-shrink-0 rounded-full" 
                                            />
                                            <span class="ml-3 block truncate">{{ kanban.users[kanban.creatingTaskProps.user_id || kanban.self.id].name }}</span>
                                        </span>
                                        <span
                                            class="pointer-events-none absolute inset-y-0 right-0 ml-3 flex items-center pr-2">
                                            <ChevronUpDownIcon class="h-5 w-5 text-gray-400" aria-hidden="true" />
                                        </span>
                                    </ListboxButton>

                                    <transition leave-active-class="transition ease-in duration-100"
                                        leave-from-class="opacity-100" leave-to-class="opacity-0">
                                        <ListboxOptions
                                            class="absolute z-20 mt-1 max-h-56 w-full overflow-auto rounded-md bg-white py-1 text-base shadow-lg ring-1 ring-black ring-opacity-5 focus:outline-none sm:text-sm">
                                            <ListboxOption as="template" v-for="person in kanban.users" :key="person.id"
                                                :value="person.id" v-slot="{ active, selected }">
                                                <li :class="[active ? 'bg-blue-600 text-white' : 'text-gray-900', 'relative cursor-default select-none py-2 pl-3 pr-9']">
                                                    <div class="flex items-center">
                                                        <img :src="getAvatar(person)" alt="{{ person.name }}"
                                                            class="h-5 w-5 flex-shrink-0 rounded-full" />
                                                        <span :class="[selected ? 'font-semibold' : 'font-normal', 'ml-3 block truncate']">{{ person.name }}</span>
                                                    </div>

                                                    <span v-if="selected"
                                                        :class="[active ? 'text-white' : 'text-blue-600', 'absolute inset-y-0 right-0 flex items-center pr-4']">
                                                        <CheckIcon class="h-5 w-5" aria-hidden="true" />
                                                    </span>
                                                </li>
                                            </ListboxOption>
                                        </ListboxOptions>
                                    </transition>
                                </div>
                            </Listbox>

                            <div class="mt-8">
                                <label for="taskPhase" class="block text-sm font-medium leading-6 text-gray-900">Phase</label>
                                <select v-model="kanban.creatingTaskProps.phase_id" id="taskPhase" class="mt-2 block w-full rounded-md border-0 py-1.5 pl-3 pr-10 text-gray-900 ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-blue-600 sm:text-sm sm:leading-6">
                                    <option v-for="phase in kanban.phases" :key="phase.id" :value="phase.id">{{ phase.name }}</option>
                                </select>
                            </div>

                            <div class="mt-8">
                                <label for="due_date" class="block text-sm font-medium leading-6 text-gray-900">
                                    Due Date
                                </label>
                                <div class="relative mt-2">
                                    <input type="date" v-model="kanban.creatingTaskProps.due_date" id="due_date"
                                        class="peer block w-full border-0 bg-gray-50 py-1.5 text-gray-900 focus:ring-0 sm:text-sm sm:leading-6"
                                        placeholder="Make it productive, but also fun!" />
                                    <p v-if="hasError('due_date')" 
                                        class="mt-2 text-sm text-red-600" 
                                        id="due_date-error">
                                            {{ getError('due_date') }}
                                    </p>
                                    <!-- <div class="absolute inset-x-0 bottom-0 border-t border-gray-300 peer-focus:border-t-2 peer-focus:border-blue-600"
                                        aria-hidden="true" /> -->
                                </div>
                            </div>

                        </div>

                        <div class="mt-5 sm:mt-6">
                            <button type="button"
                                class="inline-flex w-full justify-center rounded-md bg-blue-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-blue-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-blue-600"
                                @click="addCard()">{{ kanban.creatingTask ? "Add the card!" : "Update the card!" }}</button>
                        </div>
                    </div>
                </generic-modal>
            </Teleport>
        </div>

        <div id="kanban-container" class="flex-1 flex overflow-auto scrollbar-hide ">
            <div class="text-gray-900">
                <div class="h-full flex overflow-x-auto overflow-y-auto space-x-4 max-h-[680px]">
                    <div class="w-full bg-sky-950 rounded-lg shadow-lg">
                        <div class="p-4 flex flex-wrap   gap-5">
                            <div class="flex items-center justify-between w-full">
                                <h2 class="text-lg text-zinc-100 font-black mb-3">
                                Users Statistics
                                </h2>
                            </div>
                            <div class="scroll-bar max-h-[580px] flex flex-wrap overflow-x-auto overflow-y-auto gap-5">
                                <div
                                    class="w-[32%] bg-white text-gray-900 shadow-md rounded-lg p-3 pb-8 relative"
                                    v-for="(user, index) in kanban.userStatistics"
                                    :key="index"
                                    >
                                    {{ user.name }}<br>
                                    <div class="text-xs text-gray-500 bottom-2 mt-4">Due Tasks: {{ user.due_tasks.length }}</div>
                                    <div class="text-xs text-gray-500 bottom-2 mt-1">In Progress Tasks: {{ user.in_progress_tasks.length }}</div>
                                    <div class="text-xs text-gray-500 bottom-2 mt-1">Completed Tasks: {{ user.completed_tasks.length }}</div>
                                    <div class="text-xs text-gray-500 bottom-2 mt-1">Total Completed Tasks(Current Week): {{ user.total_completed_this_week }}</div>
                                    <div class="text-xs text-gray-500 bottom-2 mt-1">Total Completed Tasks(Current Month): {{ user.total_completed_this_month }}</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, onMounted, onUnmounted, nextTick } from 'vue'
import { useKanbanStore } from '../stores/kanban'
import { DialogTitle, Listbox, ListboxButton, ListboxLabel, ListboxOption, ListboxOptions } from '@headlessui/vue'
import { CheckIcon, ChevronUpDownIcon, TrashIcon } from '@heroicons/vue/20/solid'
import { sha256 } from 'js-sha256';
import commonService from '../services/common-services'


const kanban = useKanbanStore()
const selected = ref(null)
const errors = ref(null)
const isEditing = ref(false);

const getSettings = async () => {
    try {
        const response = await axios.get('/api/app-settings');
        if (response && response['data']) {
            kanban.appSettings = response['data'];    
        } 
    } catch (error) {
        console.error('There was an error for getting settings!', error);
    }
}

const getStatistics = async () => {
    try {
        const response = await axios.get('/api/users/statistics');
        if (response && response['data']) {
            kanban.userStatistics = response['data'];    
        } 
    } catch (error) {
        console.error('There was an error for getting settings!', error);
    }
}

onMounted(async () => {
    await getSettings();
    await getStatistics();
})

</script>

<style scoped>/* For Webkit-based browsers (Chrome, Safari and Opera) */
.scrollbar-hide::-webkit-scrollbar {
    display: none;
}

/* For IE, Edge and Firefox */
.scrollbar-hide {
    -ms-overflow-style: none;
    /* IE and Edge */
    scrollbar-width: none;
    /* Firefox */
}

 .scroll-bar::-webkit-scrollbar {
  width: 0px;
}
</style>