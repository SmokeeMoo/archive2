<?php defined('ALTUMCODE') || die() ?>


<div id="<?= 'biolink_block_id_' . $data->link->biolink_block_id ?>" data-biolink-block-id="<?= $data->link->biolink_block_id ?>" class="col-12 my-2">
    
   <div id="<?= 'container_'.$data->link->biolink_block_id ?>">
	<span id="<?= 'text1_'.$data->link->biolink_block_id ?>"></span>
	<span id="<?= 'text2_'.$data->link->biolink_block_id ?>"></span>
</div>

<!-- The SVG filter used to create the merging effect -->
<svg id="<?= 'filters_'.$data->link->biolink_block_id ?>" style="
	<?php 
	if ($data->link->settings->font_size == 1) {
	    $height = 'height:1.0em;';
	}; 
	if ($data->link->settings->font_size == 2) {
	    $height = 'height:3em;';
	};
	if ($data->link->settings->font_size == 3) {
	    $height = 'height:4em;';
	};
	if ($data->link->settings->font_size == 4) {
	    $height = 'height:5em;';
	};
	if ($data->link->settings->font_size == 5) {
	    $height = 'height:6em;';
	};
	if ($data->link->settings->font_size == 6) {
	    $height = 'height:7em;';
	};
	if ($data->link->settings->font_size == 7) {
	    $height = 'height:8em;';
	};
	if ($data->link->settings->font_size == 8) {
	    $height = 'height:9em;';
	};
	?>
	
	<?= $height ?>">
	<defs>
		<filter id="<?= 'threshold_'.$data->link->biolink_block_id ?>">
			<!-- Basically just a threshold effect - pixels with a high enough opacity are set to full opacity, and all other pixels are set to completely transparent. -->
			<feColorMatrix in="SourceGraphic"
					type="matrix"
					values="1 0 0 0 0
									0 1 0 0 0
									0 0 1 0 0
									0 0 0 255 -140" />
		</filter>
	</defs>
</svg>
   
</div>

<style>
/* Explanation in JS tab */

/* Cool font from Google Fonts! */
@import url('https://fonts.googleapis.com/css?family=Raleway:900&display=swap');

body {
	margin: 0px;
}

#<?= 'container_'.$data->link->biolink_block_id ?> {
	/* Center the text in the viewport. */
	margin: auto;
	top: 0;
	bottom: 0;
	<?php 
	if ($data->link->settings->text_alignment == 'left') {
	    $align = 'position:relative; text-align: left;';
	}; 
	if ($data->link->settings->text_alignment == 'right') {
	    $align = 'position:relative;text-align:right;';
	};
	if ($data->link->settings->text_alignment == 'center') {
	    $align = 'width:100%; position:relative;text-align:center;';
	};
	?>
	
	
	<?= $align ?>
	
	/* This filter is a lot of the magic, try commenting it out to see how the morphing works! */
	filter: url(#<?= 'threshold_'.$data->link->biolink_block_id ?>) blur(0.6px);
}

/* Your average text styling */
#<?= 'text1_'.$data->link->biolink_block_id ?>, #<?= 'text2_'.$data->link->biolink_block_id ?> {
	position: absolute;
	width: 100%;
	display: block;
	font-family: 'Raleway', sans-serif;
	font-size: <?= $data->link->settings->font_size ?>em;
	user-select: none;
	color:<?= $data->link->settings->text_color ?>;
}
</style>

<script>
/*
	This pen cleverly utilizes SVG filters to create a "Morphing Text" effect. Essentially, it layers 2 text elements on top of each other, and blurs them depending on which text element should be more visible. Once the blurring is applied, both texts are fed through a threshold filter together, which produces the "gooey" effect. Check the CSS - Comment the #container rule's filter out to see how the blurring works!
*/

const <?= 'elts_'.$data->link->biolink_block_id ?> = {
	<?= 'text1_'.$data->link->biolink_block_id ?>: document.getElementById("<?= 'text1_'.$data->link->biolink_block_id ?>"),
	<?= 'text2_'.$data->link->biolink_block_id ?>: document.getElementById("<?= 'text2_'.$data->link->biolink_block_id ?>")
};

// The strings to morph between. You can change these to anything you want!
let i = 0;
let texts=[];
<?php foreach($data->link->settings->items as $key => $item): ?>
texts[i] = '<?= $item->title ?>';
i++;
//const texts = ["Why", "is", "this", "so", "satisfying", "to", "watch?"];
<?php endforeach ?>

// Controls the speed of morphing.
const morphTime = <?= $data->link->settings->speed ?>;
const cooldownTime = 0.25;

let textIndex = texts.length - 1;
let time = new Date();
let morph = 0;
let cooldown = cooldownTime;

<?= 'elts_'.$data->link->biolink_block_id ?>.<?= 'text1_'.$data->link->biolink_block_id ?>.textContent = texts[textIndex % texts.length];
<?= 'elts_'.$data->link->biolink_block_id ?>.<?= 'text2_'.$data->link->biolink_block_id ?>.textContent = texts[(textIndex + 1) % texts.length];

function <?= 'doMorph_'.$data->link->biolink_block_id ?>() {
	morph -= cooldown;
	cooldown = 0;

	let fraction = morph / morphTime;

	if (fraction > 1) {
		cooldown = cooldownTime;
		fraction = 1;
	}

	<?= 'setMorph_'.$data->link->biolink_block_id ?>(fraction);
}

// A lot of the magic happens here, this is what applies the blur filter to the text.
function <?= 'setMorph_'.$data->link->biolink_block_id ?>(fraction) {
	// fraction = Math.cos(fraction * Math.PI) / -2 + .5;

	<?= 'elts_'.$data->link->biolink_block_id ?>.<?= 'text2_'.$data->link->biolink_block_id ?>.style.filter = `blur(${Math.min(8 / fraction - 8, 100)}px)`;
	<?= 'elts_'.$data->link->biolink_block_id ?>.<?= 'text2_'.$data->link->biolink_block_id ?>.style.opacity = `${Math.pow(fraction, 0.4) * 100}%`;

	fraction = 1 - fraction;
	<?= 'elts_'.$data->link->biolink_block_id ?>.<?= 'text1_'.$data->link->biolink_block_id ?>.style.filter = `blur(${Math.min(8 / fraction - 8, 100)}px)`;
	<?= 'elts_'.$data->link->biolink_block_id ?>.<?= 'text1_'.$data->link->biolink_block_id ?>.style.opacity = `${Math.pow(fraction, 0.4) * 100}%`;

	<?= 'elts_'.$data->link->biolink_block_id ?>.<?= 'text1_'.$data->link->biolink_block_id ?>.textContent = texts[textIndex % texts.length];
	<?= 'elts_'.$data->link->biolink_block_id ?>.<?= 'text2_'.$data->link->biolink_block_id ?>.textContent = texts[(textIndex + 1) % texts.length];
}

function <?= 'doCooldown_'.$data->link->biolink_block_id ?>() {
	morph = 0;

	<?= 'elts_'.$data->link->biolink_block_id ?>.<?= 'text2_'.$data->link->biolink_block_id ?>.style.filter = "";
	<?= 'elts_'.$data->link->biolink_block_id ?>.<?= 'text2_'.$data->link->biolink_block_id ?>.style.opacity = "100%";

	<?= 'elts_'.$data->link->biolink_block_id ?>.<?= 'text1_'.$data->link->biolink_block_id ?>.style.filter = "";
	<?= 'elts_'.$data->link->biolink_block_id ?>.<?= 'text1_'.$data->link->biolink_block_id ?>.style.opacity = "0%";
}

// Animation loop, which is called every frame.
function <?= 'animate_'.$data->link->biolink_block_id ?>() {
	requestAnimationFrame(<?= 'animate_'.$data->link->biolink_block_id ?>);

	let newTime = new Date();
	let shouldIncrementIndex = cooldown > 0;
	let dt = (newTime - time) / 1000;
	time = newTime;

	cooldown -= dt;

	if (cooldown <= 0) {
		if (shouldIncrementIndex) {
			textIndex++;
		}

		<?= 'doMorph_'.$data->link->biolink_block_id ?>();
	} else {
		<?= 'doCooldown_'.$data->link->biolink_block_id ?>();
	}
}

// Start the animation.
<?= 'animate_'.$data->link->biolink_block_id ?>();
</script>
