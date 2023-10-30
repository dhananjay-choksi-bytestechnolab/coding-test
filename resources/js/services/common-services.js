// commonService.js
import axios from 'axios'; // Make sure to import axios if you haven't already

export default {
  async refreshTasks(kanban) {
    try {
      const response = await axios.get('/api/tasks');
      const originalTasks = response.data;
      kanban.phases = originalTasks.reduce((acc, cur) => {
        acc[cur.id] = cur;
        return acc;
      }, {});
    } catch (error) {
      console.error('There was an error fetching the tasks!', error);
    }
  },
  
  // Define other common functions here
};
