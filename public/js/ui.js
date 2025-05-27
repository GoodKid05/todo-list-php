const overlay = document.getElementById('overlay');
const modalWindowCreateTask = document.getElementById('create-task-modal');

export function showModalWindowCreateTask() {
	overlay.classList.remove('hidden');
	modalWindowCreateTask.classList.remove('hidden');
}

export function hideModalWindowCreateTask() {
	overlay.classList.add('hidden');
	modalWindowCreateTask.classList.add('hidden');
}