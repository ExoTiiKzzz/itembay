class Sidebar {
	constructor() {
		this.sidebar = document.querySelector('.sidebar');
		this.mask = document.querySelector('.sidebar-mask');
		this.title = this.sidebar.querySelector('.title');
		this.content = this.sidebar.querySelector('.content');
		this.button = this.sidebar.querySelector('.button');
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
		this.content.innerHTML = content;

		return this;
	}

	// fetch content from url and set it as sidebar content
	// if error, set error message as content
	async setContentFromUrl(url) {
		await fetch(url)
			.then(response => response.text())
			.then(data => this.setContent(data))
			.catch(error => this.setContent('Error: ' + error));

		return this;
	}

	setButton(text, callback) {
		this.button.innerHTML = text;
		this.button.onclick = callback;

		return this;
	}

	hideLoader() {
		this.loader.classList.remove('active');
	}


}