<?php defined('ALTUMCODE') || die() ?>

<link rel="stylesheet" href="/source/percent-preloader.min.css">
<script src="/source/percent-preloader.min.js"></script>


<div id="<?= 'biolink_block_id_' . $data->link->biolink_block_id ?>" data-biolink-block-id="<?= $data->link->biolink_block_id ?>" class="col-12 my-2">
    <div class="preloader" style="background:<?= $data->link->settings->background_color ?>;">
	<div class="inner">
		<span class="percentage"><span id= "percentage">15</span>%</span>
	</div>
	<div class="loader-progress" id ="loader-progress"> </div>
</div>
<div class="transition-overlay"></div>
</div>