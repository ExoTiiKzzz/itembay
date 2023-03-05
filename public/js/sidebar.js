class Sidebar {
	constructor() {
		this.sidebar = document.querySelector('.sidebar');
		this.mask = document.querySelector('.sidebar-mask');
		this.title = this.sidebar.querySelector('.title');
		this.content = this.sidebar.querySelector('.content');
		this.loader = this.sidebar.querySelector('.loader');

		this.mask.addEventListener('click', (e) => {
			if (e.target.classList.contains('sidebar-mask')) {
				this.close();
			}
		});
	}

	open() {
		this.sidebar.classList.add('active');
		this.mask.classList.add('active');
		this.loader.classList.add('active');
		this.content.innerHTML = '';

		return this;
	}

	close() {
		this.sidebar.classList.remove('active');
		this.mask.classList.remove('active');

		return this;
	}

	setTitle(title) {
		this.title.innerHTML = title;

		return this;
	}

	setContent(content) {
		setInnerHTML(this.content, content)

		return this;
	}

	// fetch content from url and set it as sidebar content
	// if error, set error message as content
	async setContentFromUrl(url) {
		await fetch(url)
			.then(response => response.json())
			.then(content => {
				this.setContent(content.data.html)
			})
			.catch(error => this.setContent('Error: ' + error));

		return this;
	}

	hideLoader() {
		this.loader.classList.remove('active');
	}


	showLoader() {
		this.loader.classList.add('active');
	}
}