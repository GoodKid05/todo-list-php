import  { fetchTaskData, fetchTableHeaders } from './api.js';
import { updateTaskList, updateTableHeaders } from './tasks.js';
import './events.js';

document.addEventListener('DOMContentLoaded', async () => {
	try {
		const tasks = await fetchTaskData();
		updateTaskList(tasks);
	
		const headers = await fetchTableHeaders()
		updateTableHeaders(headers);
	} catch(err) {
		console.error('Ошибка при инициализации приложения: ', err)
	}
})
