import { showModalWindowCreateTask, hideModalWindowCreateTask } from './ui.js';
import { fetchCreateTask, fetchDeleteTask, fetchFindTask, fetchSaveTask } from './api.js';
import { updateTaskList, createEditableField, translateStatus, formatFullDate, formatDateOnly} from './tasks.js';

const btn_createTask = document.getElementById('create-task-button');
const btn_searchTask = document.getElementById('search-task-button');

const createTaskForm = document.getElementById('create-task-form');
const taskTableBody = document.getElementById('table-task-body');

const searchTaskInput = document.querySelector('.task-search-input');
const selectField = document.querySelector('.task-search-select');

btn_createTask.addEventListener('click', function() {
	showModalWindowCreateTask();
});

overlay.addEventListener('click', function() {
	hideModalWindowCreateTask();
});

createTaskForm.addEventListener('submit', async function(e){
	e.preventDefault();
	try {
		const formData = new FormData(createTaskForm);
		const newTask = await fetchCreateTask(formData);
		console.log(newTask)
		updateTaskList(newTask, {replace: false});
		hideModalWindowCreateTask();
	} catch (err) {
		console.error(err);
		alert(err)
	}
});

selectField.addEventListener('change', function() {
	const selectFieldValue = selectField.value
	if(['created_at', 'updated_at', 'deadline'].includes(selectFieldValue)) {
		searchTaskInput.type = 'date';
	} else {
		searchTaskInput.type = 'text';
	}
})


searchTaskInput.addEventListener('click', function(e) {
	if(e.target.classList.contains('input-error')){
		e.target.classList.remove('input-error');
	}
})

btn_searchTask.addEventListener('click', async function(e) {
	e.preventDefault();
	try {
		const searchTaskForm = e.target.closest('#task-search-form');
		const fieledValue = searchTaskForm.querySelector('.task-search-select').value;
		const inputValue = searchTaskInput.value;
		
		const tasks = await fetchFindTask(fieledValue, inputValue)
		
		if(tasks.length === 0) {
			searchTaskInput.classList.add('input-error');
			alert('Ничего не найдено');
			return
		} 
		updateTaskList(tasks);
	} catch(err) {
		console.error(err)
	}
});

taskTableBody.addEventListener('click', async function(e) {
	const row = e.target.closest('.table-task-row');
	const cells = row.querySelectorAll('td');

	if(e.target.classList.contains('edit-icon')){
		e.target.src = '/icons/check-mark-icon.png';
		e.target.classList.replace('edit-icon', 'save-icon');
		cells.forEach(cell => {
			const column = cell.dataset.column;
			const cellContent = cell.querySelector('.row-content');
			if (column === 'created_at' || column === 'updated_at' || column === 'icon') return;
			const value = cellContent.textContent;

			cellContent.innerHTML = createEditableField(column, value);
		})
		return;
	}

	if(e.target.classList.contains('save-icon')) {
		e.target.src = '/icons/edit-icon.png';
		e.target.classList.replace('save-icon', 'edit-icon');
		const taskData = {
			"taskId": row.dataset.taskId
		}
		cells.forEach(cell => {
			const cellContent = cell.querySelector('.row-content');
			const column = cell.dataset.column;
			if (column === 'created_at' || column === 'updated_at' || column === 'icon') return;

			if (column === 'description') {
				const titleValue = cellContent.querySelector('.editable-textarea').value;
				taskData[column] = titleValue;
			}

			else if (column === 'status') {
				const statusValue = cellContent.querySelector('.editable-select').value;
				taskData[column] = statusValue;

			} else if(column === 'deadline') {
				const inputValue = cellContent.querySelector('.editable-input').value;
				taskData[column] = inputValue;
			}
			else {
				const inputValue = cellContent.querySelector('.editable-input').value;
				taskData[column] = inputValue;
			}
		})

		const updatedTask = await fetchSaveTask(taskData);
		cells.forEach(cell => {
			const cellContent = cell.querySelector('.row-content');
			const column = cell.dataset.column;
			if (column === 'icon') return

			if (column === 'status') {
				cellContent.innerHTML = translateStatus[updatedTask[column]];
			} else if ((['created_at', 'updated_at', 'deadline'].includes(column))) {
				cellContent.innerHTML = column === 'deadline' 
				? formatDateOnly(updatedTask[column])  
				:  formatFullDate(updatedTask[column]);
			}
			else {
				cellContent.innerHTML = updatedTask[column];
			}			
		})
		
	}

	if (e.target.classList.contains('trash-icon')) {
		e.target.src = '/icons/cancel-icon.png';
		e.target.classList.replace('trash-icon', 'cancel-deletion-icon');
		const editIcon = e.target.previousElementSibling;
		editIcon.src = '/icons/check-mark-icon.png';
		editIcon.classList.replace('edit-icon', 'confirm-deletion-icon')
		row.classList.add('pre-delete');
		return
	}

	if (e.target.classList.contains('cancel-deletion-icon')) {
		e.target.src = '/icons/trash-icon.png';
		e.target.classList.replace('cancel-deletion-icon', 'trash-icon');
		const confirmDelete = e.target.previousElementSibling;
		confirmDelete.src = '/icons/edit-icon.png';
		confirmDelete.classList.replace('confirm-deletion-icon', 'edit-icon');
		row.classList.remove('pre-delete');
		return
	}

	if(e.target.classList.contains('confirm-deletion-icon')) {
		try {
			const taskId = row.dataset.taskId;
			if(!row.classList.contains('pre-delete')) throw new Error('Задача должна содержать класс для 	удаления');
			
			if(fetchDeleteTask(taskId)) {
				const rowContent = row.querySelectorAll('.row-content');
				rowContent.forEach(el => el.classList.add('fade-out'));
				
				setTimeout(() => {
					row.remove();
				  }, 500);
			}
			
		} catch (err) {
			console.error(err)
		}
	}
})
