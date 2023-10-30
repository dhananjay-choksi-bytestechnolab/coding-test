<template>
    <div :style="{ backgroundColor: taskBackgroundColor, color: textColor }" class="w-full text-gray-900 shadow-md rounded-lg p-3 pb-8 mb-4 relative"
        @mouseenter="kanban.hoveredName = task.name" 
        @mouseleave="kanban.unhoverTask()"
        @click="updatingTask(task)">
        {{ task.name }}<br>
        <div class="text-xs absolute bottom-2" :class="textColorUserName">{{ task.user.name }}</div>
        <img class="w-10 h-10 shadow-lg rounded-full absolute bottom-0 right-0 -mr2 -mb-2 border-2 border-white"
            :src="getAvatar()" :alt="task.user.name" />
    </div>
</template>

<script setup>
// get the props
import { useKanbanStore } from '../stores/kanban';
import { sha256 } from 'js-sha256';
import { ref, computed } from 'vue'; // Import computed along with ref or other necessary dependencies
const kanban = useKanbanStore()

const getAvatar = function () {
    if (props.task.user.profile_picture_url !== null) {
        return props.task.user.profile_picture_url;
    } else {
        return ("https://www.gravatar.com/avatar/" + sha256(String(props.task.user.email).trim().toLowerCase()) + "?size=40");
    }
}

const props = defineProps({
    task: {
        type: Object,
        required: true
    }
})

const updatingTask = function (task) {
    kanban.updatingTask = true;
    kanban.creatingTaskProps.id = task.id;
    kanban.creatingTaskProps.phase_id = task.phase_id;
    kanban.creatingTaskProps.name = task.name;
    kanban.creatingTaskProps.user_id = task.user_id;
    kanban.creatingTaskProps.due_date = task.due_date;
}

const taskBackgroundColor = computed(() => {
    // You can set your dynamic background color conditionally here
    if (props.task.status == "due") {
        return kanban.appSettings.task_card_background_color_status_wise.due;
    } else if(props.task.status == "completed") {
        return kanban.appSettings.task_card_background_color_status_wise.completed;
    } else {
        return kanban.appSettings.task_card_background_color_status_wise.in_progress;
    }
});

const textColor = computed(() => {
    // You can set your dynamic background color conditionally here
    if (props.task.status == "due") {
        return "#FFFFFF";
    }
});

const textColorUserName = computed(() => {
    // You can set your dynamic background color conditionally here
    if (props.task.status == "due") {
        return "text-white-500";
    } else {
        return "text-gray-500";
    }
});

</script>