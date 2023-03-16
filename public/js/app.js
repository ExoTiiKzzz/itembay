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

document.querySelectorAll('[data-range-value]').forEach(el => {
	document.querySelector(el.dataset.target).addEventListener('input', (e) => {
		el.innerHTML = formatItemPrice(e.currentTarget.value)
	});
});

document.querySelectorAll('.form-select').forEach(el => {
	$(el).select2({
		width: '100%',
		minimumResultsForSearch: el.dataset.searchable ? 5 : -1,
	});
});

const sidenav = new Sidebar();