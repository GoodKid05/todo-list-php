const taskTableBody = document.getElementById('table-task-body');
const taskTableHeader = document.getElementById('task-table__head');

const translateTableHeaders = {
	'title': 'Название',
	'description': 'Описание',
	'status': 'Статус',
	'created_at': 'Создано',
	'updated_at': 'Обновлено',
	'deadline': 'Срок',
}

export const translateStatus = {
	'not started': 'Не начат',
	'in progress': 'В процессе',
	'completed': 'Завершен',
	'deferred': 'Отложен',
	'overdue': 'Просрочен'
}

export function formatFullDate(utcString) {
	const date = new Date(utcString + 'Z');
	const localTime = new Intl.DateTimeFormat('ru-RU', {
		dateStyle: 'short',
		timeStyle: 'short'
	}).format(date)
	return localTime
}

export function formatDateOnly(utcString) {
	const date = new Date(utcString + 'Z');
	const localTime = new Intl.DateTimeFormat('ru-RU', {
		dateStyle: 'short',
	}).format(date)
	return localTime
}

export function formatDateForInput(dateString) {
	const separator = dateString.includes('.') ? '.' : '-';
	const [day, month, year] = dateString.split(separator);
	return `${year}-${month}-${day}`;
}

export async function updateTaskList(tasks, {replace = true} = {}) {
	if (!Array.isArray(tasks)) tasks = [tasks];

	if(tasks.length === 0) return;

	if(replace === true) taskTableBody.innerHTML = '';

	const rows = tasks
		.map(task => {
			let row = `<tr class="table-task-row" data-task-id="${task.id }">`;

			for (const [key, value] of Object.entries(task)) {
				if(key === 'id') continue;
				if (key === 'status') {
					row += `
						<td data-column="${key}">
							<div class="row-content">${translateStatus[value]}</div>
						</td>`;
				} else if  (['created_at', 'updated_at', 'deadline'].includes(key)) {
					row += `
					<td data-column="${key}">
						<div class="row-content">${key === 'deadline' ? formatDateOnly(value)  : formatFullDate(value)}</div>
					</td>`;
				} else {
					row += `
					<td data-column="${key}">
						<div class="row-content">${value}</div>
					</td>`;
				};
			}
			
			row += ` 
				<td class="task-actions-icon" data-column="icon">
					<div class="row-content">
						<img class="edit-icon" src="/../icons/edit-icon.png" alt="иконка" width="32" height="32">
						<img class="trash-icon" src="/../icons/trash-icon.png" alt="иконка" width="32" height="32">
					</div>
				</td>
			</tr>`;
			return row;
		}
	).join('');
	taskTableBody.insertAdjacentHTML('afterbegin', rows);
}

export function getSelectedTask(){
	const checkboxes = document.querySelectorAll('input[type="checkbox"]:checked');
	const selectedTaskId = [];
	checkboxes.forEach(checkboxe => {
		const row = checkboxe.closest('tr');
		const taskId = row.dataset.taskId;
		selectedTaskId.push(taskId);
	})
	return selectedTaskId;
}

export function updateTableHeaders(headers) {
	const headerCells = headers
		.filter(header => header !== 'id')
		.map(header => `<th class="task-table__cell-head">${translateTableHeaders[header]} </th>`)
		.join('');
	const tableHeaderHTML = 
		`<tr class="table-task-row ">
			${headerCells}
			<th class="task-actions-icon"></th>
		</tr>`;
	taskTableHeader.innerHTML = tableHeaderHTML;
}

export function createEditableField(column, value) {
	if (column === 'description') {
		return  `<textarea class="editable-field editable-textarea">${value} </textarea>`;
	} else if(column === 'status') {
		return `
		<select class="editable-field editable-select">
			<option value="not started" ${value === 'Не начат' ? 'selected' : ''}>Не начат</option>
			<option value="in progress" ${value === 'В процессе' ? 'selected' : ''}>В процессе</option>
			<option value="completed" ${value === 'Завершен' ? 'selected' : ''}>Завершен</option>
			<option value="deferred" ${value === 'Отложен' ? 'selected' : ''}>Отложен</option>
			<option value="overdue" ${value === 'Просрочен' ? 'selected' : ''}>Просрочен</option>
		</select>`;
	}  else if(column === 'deadline') {
		return `<input class="editable-field editable-input" type="date" value="${formatDateForInput(value)}">`;
	} else {
		return `<input class="editable-field editable-input" type="text" value="${value}">`;
	}
}
