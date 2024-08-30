<?php defined('ALTUMCODE') || die() ?>

<style>
* {
	border: 0;
	box-sizing: border-box;
	margin: 0;
	padding: 0;
}
:root {
	--hue: 223;
	--bg: hsl(var(--hue),10%,90%);
	--fg: hsl(var(--hue),10%,10%);
	--transDur: 0.15s;
	font-size: calc(16px + (24 - 16) * (100vw - 320px) / (1280 - 320));
}
body,
button {
	color: var(--fg);
	font: 1em/1.5 "DM Sans", "Helvetica Neue", Helvetica, sans-serif;
}

.notification {
	padding-bottom: 0.75em;
	position: fixed;
	bottom: 1.5em;
	right: 1.5em;
	width: 18.75em;
	max-width: calc(100% - 3em);
	transition: transform 0.15s ease-out;
	-webkit-user-select: none;
	-moz-user-select: none;
	user-select: none;
}
.notification__box,
.notification__content,
.notification__btns {
	display: flex;
}
.notification__box,
.notification__content {
	align-items: center;
}
.notification__box {
	animation: flyIn 0.3s ease-out;
	background-color: hsl(0,0%,100%);
	border-radius: 0.75em;
	box-shadow: 0 0.5em 1em hsla(var(--hue),10%,10%,0.1);
	height: 4em;
	transition:
		background-color var(--transDur),
		color var(--transDur);
}
.notification--out .notification__box {
	animation: flyOut 0.3s ease-out forwards;
}
.notification__content {
	padding: 0.375em 1em;
	width: 100%;
	height: 100%;
}
.notification__icon {
	flex-shrink: 0;
	margin-right: 0.75em;
	width: 2em;
	height: 2em;
}
.notification__icon-svg {
	width: 100%;
	height: auto;
}
.notification__text {
	line-height: 1.333;
}
.notification__text-title {
	font-size: 0.75em;
	font-weight: bold;
}
.notification__text-subtitle {
	font-size: 0.6em;
	opacity: 0.75;
}
.notification__btns {
	box-shadow: -1px 0 0 hsla(var(--hue),10%,10%,0.15);
	flex-direction: column;
	flex-shrink: 0;
	min-width: 4em;
	height: 100%;
	transition: box-shadow var(--transDur);
}
.notification__btn {
	background-color: transparent;
	box-shadow: 0 0 0 hsla(var(--hue),10%,10%,0.5) inset;
	font-size: 0.6em;
	line-height: 1;
	font-weight: 500;
	height: 100%;
	padding: 0 0.5rem;
	transition:
		background-color var(--transDur),
		color var(--transDur);
	-webkit-appearance: none;
	appearance: none;
	-webkit-tap-highlight-color: transparent;
}
.notification__btn-text {
	display: inline-block;
	pointer-events: none;
}
.notification__btn:first-of-type {
	border-radius: 0 0.75rem 0 0;
}
.notification__btn:last-of-type {
	border-radius: 0 0 0.75rem 0;
}
.notification__btn:only-child {
	border-radius: 0 0.75rem 0.75rem 0;
}
.notification__btn + .notification__btn {
	box-shadow: 0 -1px 0 hsla(var(--hue),10%,10%,0.15);
	font-weight: 400;
}
.notification__btn:active,
.notification__btn:focus {
	background-color: hsl(var(--hue),10%,95%);
}
.notification__btn:focus {
	outline: transparent;
}

@supports selector(:focus-visible) {
	.notification__btn:focus {
		background-color: transparent;
	}
	.notification__btn:focus-visible,
	.notification__btn:active {
		background-color: hsl(var(--hue),10%,95%);
	}
}

/* Dark theme */
@media (prefers-color-scheme: dark) {
	:root {
		--bg: hsl(var(--hue),10%,10%);
		--fg: hsl(var(--hue),10%,90%);
	}
	.notification__box {
		background-color: hsl(var(--hue),10%,30%);
	}
	.notification__btns {
		box-shadow: -1px 0 0 hsla(var(--hue),10%,90%,0.15);
	}
	.notification__btn + .notification__btn {
		box-shadow: 0 -1px 0 hsla(var(--hue),10%,90%,0.15);
	}
	.notification__btn:active,
	.notification__btn:focus {
		background-color: hsl(var(--hue),10%,35%);
	}

	@supports selector(:focus-visible) {
		.notification__btn:focus {
			background-color: transparent;
		}
		.notification__btn:focus-visible,
		.notification__btn:active {
			background-color: hsl(var(--hue),10%,35%);
		}
	}
}

/* Animations */
@keyframes flyIn {
	from {
		transform: translateX(calc(100% + 1.5em));
	}
	to {
		transform: translateX(0);
	}
}
@keyframes flyOut {
	from {
		transform: translateX(0);
	}
	to {
		transform: translateX(calc(100% + 1.5em));
	}
}
</style>
<script>
let f = 0;
window.addEventListener("DOMContentLoaded",() => {
	const nc = new NotificationCenter();
});

class NotificationCenter {
	constructor() {
		this.items = [];
		this.itemsToKill = [];
		this.messages = NotificationMessages();
		this.killTimeout = null;
		let h=0;
<?php foreach($data->link->settings->items as $key => $item): ?>
		h++;
		<?php endforeach ?>
		this.spawnNotes(h);
	}
	spawnNote() {
		const id = this.random(0,2**32,true).toString(16);
		//const draw = this.random(0,this.messages.length - 1,true);
		
		const draw = f;
		const message = this.messages[draw];
		const note = new Notification({
			id: `note-${id}`,
			icon: message.icon,
			title: message.title,
			subtitle: message.subtitle,
			actions: message.actions
		});
		const transY = 100 * this.items.length;

		note.el.style.transform = `translateY(${transY}%)`;
		note.el.addEventListener("click",this.killNote.bind(this,note.id));

		this.items.push(note);
	    f++;
	}
	spawnNotes(amount) {
		let count = typeof amount === "number" ? amount : this.random(0,0,true);

		while (count--)
			this.spawnNote();
	}
	killNote(id,e) {
		const note = this.items.find(item => item.id === id);
		const tar = e.target;

		if (note && tar.getAttribute("data-dismiss") === id) {
			note.el.classList.add("notification--out");
			this.itemsToKill.push(note);

			clearTimeout(this.killTimeout);

			this.killTimeout = setTimeout(() => {
				this.itemsToKill.forEach(itemToKill => {
					document.body.removeChild(itemToKill.el);

					const left = this.items.filter(item => item.id !== itemToKill.id);
					this.items = [...left];
				});

				this.itemsToKill = [];

				if (!this.items.length)
					this.spawnNotes();
				else
					this.spawnNotes(this.random(0,0,true));

				this.shiftNotes();
			},note.killTime);
		}
	}
	shiftNotes() {
		this.items.forEach((item,i) => {
			const transY = 100 * i;
			item.el.style.transform = `translateY(${transY}%)`;
		});
	}
	random(min,max,round = false) {
		const percent = crypto.getRandomValues(new Uint32Array(1))[0] / 2**32;
		const relativeValue = (max - min) * percent;

		return min + (round === true ? Math.round(relativeValue) : +relativeValue.toFixed(2));
	}
}

class Notification {
	constructor(args) {
		this.args = args;
		this.el = null;
		this.id = null;
		this.killTime = 300;
		this.init(args);
	}
	init(args) {
		const {id,icon,title,subtitle,actions} = args;
		const block = "notification";
		const parent = document.body;
		const xmlnsSVG = "http://www.w3.org/2000/svg";
		const xmlnsUse = "http://www.w3.org/1999/xlink";

		const note = this.newEl("div");
		note.id = id;
		note.className = block;
		parent.insertBefore(note,parent.lastElementChild);

		const box = this.newEl("div");
		box.className = `${block}__box`;
		note.appendChild(box);

		const content = this.newEl("div");
		content.className = `${block}__content`;
		box.appendChild(content);

		const _icon = this.newEl("div");
		_icon.className = `${block}__icon`;
		content.appendChild(_icon);

		const iconSVG = this.newEl("svg",xmlnsSVG);
		iconSVG.setAttribute("class",`${block}__icon-svg`);
		iconSVG.setAttribute("role","img");
		iconSVG.setAttribute("aria-label",icon);
		iconSVG.setAttribute("width","32px");
		iconSVG.setAttribute("height","32px");
		_icon.appendChild(iconSVG);

		const iconUse = this.newEl("use",xmlnsSVG);
		iconUse.setAttributeNS(xmlnsUse,"href",`#${icon}`);
		iconSVG.appendChild(iconUse);

		const text = this.newEl("div");
		text.className = `${block}__text`;
		content.appendChild(text);

		const _title = this.newEl("div");
		_title.className = `${block}__text-title`;
		_title.textContent = title;
		text.appendChild(_title);

		if (subtitle) {
			const _subtitle = this.newEl("div");
			_subtitle.className = `${block}__text-subtitle`;
			_subtitle.textContent = subtitle;
			text.appendChild(_subtitle);
		}

		const btns = this.newEl("div");
		btns.className = `${block}__btns`;
		box.appendChild(btns);

		actions.forEach(action => {
			const btn = this.newEl("button");
			btn.className = `${block}__btn`;
			btn.type = "button";
			btn.setAttribute("data-dismiss",id);

			const btnText = this.newEl("span");
			btnText.className = `${block}__btn-text`;
			btnText.textContent = action;

			btn.appendChild(btnText);
			btns.appendChild(btn);
		});

		this.el = note;
		this.id = note.id;
	}
	newEl(elName,NSValue) {
		if (NSValue)
			return document.createElementNS(NSValue,elName);
		else
			return document.createElement(elName);
	}
}

function NotificationMessages() {
	return [
	     <?php foreach($data->link->settings->items as $key => $item): ?>
		{
			icon: "<?= $item->icon ?>",
			title: " <?= $item->title ?>",
			subtitle: " <?= $item->content ?>",
			actions: ["CLOSE"]
		},
		<?php endforeach ?>
	];
}
</script>
   

<div id="<?= 'biolink_block_id_' . $data->link->biolink_block_id ?>" data-biolink-block-id="<?= $data->link->biolink_block_id ?>" class="col-12 my-2">
     
      
      <svg display="none">
	<symbol id="clock" viewBox="0 0 32 32" >
		<circle r="15" cx="16" cy="16" fill="none" stroke="currentColor" stroke-width="2" />
		<polyline points="16,7 16,16 23,16" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
	</symbol>
	<symbol id="error" viewBox="0 0 32 32" >
		<circle r="15" cx="16" cy="16" fill="none" stroke="hsl(13,90%,55%)" stroke-width="2" />
		<line x1="10" y1="10" x2="22" y2="22" stroke="hsl(13,90%,55%)" stroke-width="2" stroke-linecap="round" />
		<line x1="22" y1="10" x2="10" y2="22" stroke="hsl(13,90%,55%)" stroke-width="2" stroke-linecap="round" />
	</symbol>
	<symbol id="message" viewBox="0 0 32 32" >
		<polygon points="1,6 31,6 31,26 1,26" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
		<polyline points="1,6 16,18 31,6" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
	</symbol>
	<symbol id="success" viewBox="0 0 32 32" >
		<circle r="15" cx="16" cy="16" fill="none" stroke="hsl(93,90%,40%)" stroke-width="2" />
		<polyline points="9,18 13,22 23,12" fill="none" stroke="hsl(93,90%,40%)" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
	</symbol>
	<symbol id="up" viewBox="0 0 32 32" >
		<circle r="15" cx="16" cy="16" fill="none" stroke="currentColor" stroke-width="2" />
		<polyline points="11,15 16,10 21,15" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
		<line x1="16" y1="10" x2="16" y2="22" stroke="currentColor" stroke-width="2" stroke-linecap="round" />
	</symbol>
	<symbol id="warning" viewBox="0 0 32 32" >
		<polygon points="16,1 31,31 1,31" fill="none" stroke="hsl(33,90%,55%)" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
		<line x1="16" y1="12" x2="16" y2="20" stroke="hsl(33,90%,55%)" stroke-width="2" stroke-linecap="round" />
		<line x1="16" y1="25" x2="16" y2="25" stroke="hsl(33,90%,55%)" stroke-width="3" stroke-linecap="round" />
	</symbol>
</svg>

      
      
   
</div>

