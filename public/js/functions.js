/**
 * App functions
 */

function setInnerHTML(elm, html) {
	elm.innerHTML = html;

	Array.from(elm.querySelectorAll("script"))
		.forEach( oldScriptEl => {
			const newScriptEl = document.createElement("script");

			Array.from(oldScriptEl.attributes).forEach( attr => {
				newScriptEl.setAttribute(attr.name, attr.value)
			});

			const scriptText = document.createTextNode(oldScriptEl.innerHTML);
			newScriptEl.appendChild(scriptText);

			oldScriptEl.parentNode.replaceChild(newScriptEl, oldScriptEl);
		});
}

function swalError(message) {
	Swal.fire({
		icon: 'error',
		title: 'Oops...',
		text: message,
	});
}

async function apiFetch(url, body = {}, method = 'POST') {
	return await
		fetch(url, {
			method: method,
			body: JSON.stringify(body)
		})
		.then(response => {
			if (!response.ok) {
				throw new Error(response.statusText);
			}
			return response.json();
		})
		.then(data => {
			if (!data.success) {
				throw new Error(data.message);
			}
			return data;
		})
		.catch(error => swalError(error));
}


/**
 * Basket functions
 */
async function refreshBasketCount() {
	let count = await getBasketCount();
	let basketCount = document.querySelector('.basket-count');
	if (basketCount) {
		basketCount.innerHTML = count?.data?.basketCount || 0;
	}
}

async function getBasketCount() {
	return await apiFetch('/basket/count');
}

async function addBasketItem(id, isCustom = false) {
	let url;
	if (isCustom) {
		url = '/basket/custom/add/' + id;
	} else {
		url = '/basket/default/add/' + id;
	}

	await fetchAndSetBasket(url);
}

async function removeBasketItem(id, isCustom = false) {
	let url;
	if (isCustom) {
		url = '/basket/custom/remove/' + id;
	} else {
		url = '/basket/default/remove/' + id;
	}

	await fetchAndSetBasket(url);
}

async function fetchAndSetBasket(url, body = {}) {
	const data = await apiFetch(url, body);
	sidenav.showLoader();
	sidenav.setContent('');
	await sidenav.setContentFromUrl('/basket');
	initBasketListeners();
	sidenav.hideLoader();
	await refreshBasketCount();
}

function initBasketListeners() {
	document.querySelectorAll('.basket-button-plus-default-item').forEach((button) => {
		button.addEventListener('click', async (event) => {
			event.preventDefault();
			const id = button.getAttribute('data-id');
			await addBasketItem(id);
		});
	});

	document.querySelectorAll('.basket-button-minus-default-item').forEach((button) => {
		button.addEventListener('click', async (event) => {
			event.preventDefault();
			const id = button.getAttribute('data-id');
			await removeBasketItem(id);
		});
	});
}