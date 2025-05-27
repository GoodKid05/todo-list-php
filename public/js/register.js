const reqisterForm = document.getElementById('reqisterForm');

reqisterForm.addEventListener('submit', async function(e) {
	e.preventDefault();
	try {
		const formData = new FormData(e.target);
		const formDataForRequest = {};
		formData.forEach((value, key) => {
			formDataForRequest[key] = value;
		})
		const response = await fetch(`api/users/register`, {
			method: 'POST',
			headers: {
				'Content-Type': 'application/json'
			},
			body: JSON.stringify(formDataForRequest)
		});

		if(!response.ok) {
			const error = await response.json();
			throw new Error(error.error);
		}

		const data = await response.json();
		console.log(data);

	} catch (err) {
		console.error(err);
		alert(err)
	}
})