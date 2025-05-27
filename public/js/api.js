export async function fetchSaveTask(taskData) {
	try {
		const {taskId, ...newTaskData} = taskData;
		const response = await fetch(`/api/tasks/${taskId}`, {
			method: 'PATCH',
			headers: {
				'Content-Type': 'application/json'
			},
			body: JSON.stringify(newTaskData)
		})
		if(!response.ok) {
			const error = await response.json();
			throw new Error(error.error)
		}

		const {task} = await response.json();
		return task
	} catch(err) {
		console.error(err)
	}

}

export async function fetchTableHeaders() {
	try {
		const response = await fetch('/api/tasks/headers');
		if(!response.ok) throw new Error('Ошибка получения заголовков таблицы');

		const headers = await response.json();
		return headers
	} catch(err) {
		console.error(err);
	}
}

export async function fetchTaskData(filters = {}) {
	try {
		const response = await fetch('/api/tasks/list');
		if(!response.ok) {
			const error = response.json();
			throw new Error(error.error)
		};
		const {tasks, ...data} = await response.json();
		return tasks
	} catch (err) {
		console.error(`Не удалось загрузить задачи: ${err}`);
		alert(`Не удалось загрузить задачи: ${err}`);
	}
}


export async function fetchCreateTask(formData) {
	try {
		const response = await fetch(`/api/tasks/`, {
			method: 'POST',
			body: formData
		});

		if(!response.ok) {
			const error = await response.json()
			throw new Error(error.error);
		};

		const {task} = await response.json();
		return task
	} catch(err) {
		throw err;
	};
}

export async function fetchDeleteTask(taskId) {
	try {
		const response = await fetch(`/api/tasks/${taskId}`, {
			method: 'DELETE'
		});

		if(!response.ok) {
			const error = await response.json();
			throw new Error(error.error);
		}

		const data = await response.json();
		if(data) return true
	} catch (err) {
		console.error(err)
	}
}

export async function fetchFindTask(fieledValue, inputValue) {
	try {
		const response = await fetch(`/api/tasks/list?${fieledValue}=${inputValue}`);
		
		if(!response.ok) {
			const error = await response.json()
			throw new Error(error.message || 'Неизвестная ошибка')
		};

		const {tasks} = await response.json();

		return tasks
	} catch (err) {
		console.error(err)
		alert(`Ошибка запроса на /api/tasks/list?${fieledValue}=${inputValue}`)
	}
}