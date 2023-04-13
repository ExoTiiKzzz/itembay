class MovingCircleGame {
	constructor(url, level) {
		this.url = url;
		this.level = level;
		this.clickDropChance = 2;
		this.dropAdvancement = 0;
		this.initDropChance();
		this.inventoryQuantity = parseInt(document.querySelector('#inventoryQuantitySpan').textContent);

		this.rangeEl = document.querySelector('#itemLevelRange');
		this.circle = document.querySelector('.circle');
		this.dropChanceSpan = document.querySelector('#dropChanceSpan');
		this.dropChanceSpan = document.querySelector('#dropChanceSpan');
		this.inventoryQuantitySpan = document.querySelector('#inventoryQuantitySpan');
		//get the current position of the circle
		this.x = this.circle.offsetLeft;
		this.y = this.circle.offsetTop;

		this.eventListeners();
		this.updateDropChance();
	}

	random(min, max) {
		return Math.floor(Math.random() * (max - min + 1) + min);
	}

	eventListeners() {
		this.circle.addEventListener('click', async (e) => {
			await this.click(e);
		});
	}

	async click(e) {
		if (e.currentTarget === this.circle) {
			this.x = this.random(10, 90);
			this.y = this.random(10, 90);
			//set the new position of the circle
			this.circle.style.left = this.x + '%';
			this.circle.style.top = this.y + '%';
			this.updateDropChance();
			await this.isItemDropped();
		}
	}

	async isItemDropped() {
		let random = this.random(1, 100);
		if (random <= this.dropAdvancement) {
			this.dropAdvancement = 0;
			await this.generateItem();
		} else {
			this.dropAdvancement += this.clickDropChance;
		}
	}

	async generateItem() {
		this.showDrop();
		let response = await apiFetch(this.url);
		this.inventoryQuantity = response.data.quantity;
		this.updateInventoryQuantity();
	}

	showDrop() {
		let drop = document.createElement('div');
		drop.classList.add('drop');
		drop.style.position = 'absolute';
		drop.style.left = this.x + '%';
		drop.style.top = this.y + '%';
		drop.style.width = '40px';
		drop.style.height = '40px';
		drop.style.borderRadius = '50%';
		drop.style.transform = 'translate(-50%, -50%)';
		//set background image
		drop.style.backgroundImage = backgroundImageUrl;
		drop.style.backgroundSize = 'cover';
		drop.style.backgroundColor = 'rgba(159,250,164,0.5)';

		document.querySelector('.game').appendChild(drop);
		//create movement effect going up
		drop.style.animation = 'circleAnimation 0.6s ease-in-out forwards';

		setTimeout(() => {
			drop.remove();
		}, 600);
	}

	initDropChance() {
		if (this.level < 20) {
			this.clickDropChance = 50
		} else if (this.level < 40) {
			this.clickDropChance = 30
		} else if (this.level < 60) {
			this.clickDropChance = 20
		} else if (this.level < 80) {
			this.clickDropChance = 10
		} else if (this.level < 100) {
			this.clickDropChance = 5
		} else {
			this.clickDropChance = 2
		}
	}

	updateDropChance() {
		this.dropChanceSpan.textContent = this.dropAdvancement;
	}

	updateInventoryQuantity() {
		this.inventoryQuantitySpan.textContent = '' + this.inventoryQuantity;
	}
}