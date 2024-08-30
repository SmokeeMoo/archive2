<?php defined('ALTUMCODE') || die() ?>

<style>
.tmgradienttext {
  background: linear-gradient(
    to right, 
    <?= $data->link->settings->text_color ?>, 
    <?= $data->link->settings->background_color ?>
  );
  -webkit-background-clip: text;
  -webkit-text-fill-color: transparent;
  text-align: center;
}

</style>
   

<div id="<?= 'biolink_block_id_' . $data->link->biolink_block_id ?>" data-biolink-block-id="<?= $data->link->biolink_block_id ?>" class="col-12 my-2">
           
    <span class="tmgradienttext">
    <?= $data->link->settings->text ?>
    </span>


</div>
