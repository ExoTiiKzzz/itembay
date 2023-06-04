/**
 * App functions
 */

function formatItemPrice(price) {
	//1 gold piece = 10 silver pieces = 100 bronze pieces
	let gold = Math.floor(price / 100);
	let silver = Math.floor((price - (gold * 100)) / 10);
	let bronze = price - (gold * 100) - (silver * 10);

	let result = '';
	if (gold > 0) {
		result += gold + ' <i class="fa-brands fa-bitcoin gold"></i> ';
	}
	if (silver > 0) {
		result += silver + ' <i class="fa-brands fa-bitcoin silver"></i> ';
	}
	if (bronze > 0) {
		result += bronze + ' <i class="fa-brands fa-bitcoin bronze"></i> ';
	}

	return result;
}

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

function swalSuccess(message) {
	Swal.fire({
		icon: 'success',
		title: 'Success',
		text: message,
	});
}

function notif(message, type = 'info') {
	notie.alert({
		type: type,
		text: message,
		time: 3
	});
}

async function apiFetch(url, body = {}, method = 'POST') {
	let init = {
		method: method,
	}
	if (method === 'POST') {
		init.body = JSON.stringify(body);
	}
	if (method === 'GET') {
		url += '?' + new URLSearchParams(body).toString();
	}
	return await
		fetch(url, init)
		.then(response => {
			return response.json();
		})
		.then(data => {
			if (!data.success) {
				throw new Error(data.message);
			}
			return data;
		})
		.catch(error => swalError(error.message));
}


/**
 * BasketService functions
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

async function addBasketItem(id, isCustom = false, quantity = 1) {
	let url;
	if (isCustom) {
		url = '/basket/custom/add/' + id;
	} else {
		url = '/basket/default/add/' + id;
	}

	await fetchAndSetBasket(url, {quantity: quantity});
}

async function removeBasketItem(id, isCustom = false, quantity = 1) {
	let url;
	if (isCustom) {
		url = '/basket/custom/remove/' + id;
	} else {
		url = '/basket/default/remove/' + id;
	}

	await fetchAndSetBasket(url, {quantity: quantity});
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

async function refreshBalance() {
	let balance = document.querySelector('.balance-container');
	if (balance) {
		let result = await apiFetch('/balance');
		balance.innerHTML = result.data.balance;
	}
}

function addLoader(element) {
	element.innerHTML = '<div class="fa fa-spinner fa-spin"></div>';
}