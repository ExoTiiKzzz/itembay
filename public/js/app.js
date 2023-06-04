document.querySelectorAll('[data-range-value]').forEach(el => {
	document.querySelector(el.dataset.target).addEventListener('input', (e) => {
		el.innerHTML = formatItemPrice(e.currentTarget.value)
	});
});

let initSelect2 = (element = document) => {
	element.querySelectorAll('.form-select').forEach(el => {
		$(el).select2({
			width: '100%',
			minimumResultsForSearch: el.dataset.searchable ? 5 : -1,
		});
	});
}

initSelect2();

let initTabsContainer = (element = document) => {
	element.querySelectorAll('.tabs-container').forEach(el => {
		let tabs = el.querySelectorAll('.nav-link');
		let contents = el.querySelectorAll('.tab');

		tabs.forEach(tab => {
			tab.addEventListener('click', (e) => {
				tabs.forEach(tab => tab.classList.remove('active'));
				contents.forEach(content => content.classList.remove('active'));

				tab.classList.add('active');
				let dataTab = tab.dataset.tab;
				el.querySelector(`.tab[data-tab="${dataTab}"]`).classList.add('active');
			});
		});
	});
}

initTabsContainer();

$('[data-tooltip]').tooltip();

const sidenav = new Sidebar();