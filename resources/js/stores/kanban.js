import { defineStore } from 'pinia'

export const useKanbanStore = defineStore('kanban', {
  state: () => {
    return { 
        hoveredName: 'nothing',
        selectedTask: null,
        phases: [],
        users: [],
        creatingTask: false,
        updatingTask: false,
        creatingTaskProps: {
          name: '',
          phase_id: null,
          user_id: null,
        },
        self: null,
        appSettings: {},
        userStatistics: []
    }
  },
  actions: {
    unhoverTask() {
      this.hoveredName = 'nothing'
    },
    selectTask(task) {
        console.log("selectTask");
        console.log("task : ", task);
        this.selectedTask = task
    },
    unselectTask(task) {
        this.selectedTask = null
    },
    hasSelectedTask() {
        return this.selectedTask !== null
    },
  },
})