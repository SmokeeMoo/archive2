<?php defined('ALTUMCODE') || die() ?>


 <style>
.progress-title{
    font-size: 16px;
    font-weight: 700;
    color: <?= $data->link->settings->text_color ?>;
    margin: 0 0 5px 10px;
text-align: left;
}
.progress{
    height: 20px;
    line-height: 15px;
    border-radius: 20px;
    background: #ffffff30;
    margin-bottom: 15px;
    box-shadow: none;
    overflow: visible;
}
.progress .progress-bar{
    position: relative;
    border-radius: 20px 0 0 20px;
    animation: animate-positive 2s;
}
.progress .progress-value{
    display: block;
    font-size: 13px;
    color: #fff;
    position: absolute;
      top: 2px;
    right: 8px;
}
@-webkit-keyframes animate-positive{
    0% { width: 0%; }
}
@keyframes animate-positive{
    0% { width: 0%; }
}
</style>
 

<div id="<?= 'biolink_block_id_' . $data->link->biolink_block_id ?>" data-biolink-block-id="<?= $data->link->biolink_block_id ?>" class="col-12 my-2">

<div class="container">
    <div class="row">
        <div class="col-md-12">
            <?php foreach($data->link->settings->items as $key => $item): ?>
            <h3 class="progress-title"><?= $item->title ?></h3>
            <div class="progress">
                <div class="progress-bar" style="width: <?= $item->content ?>%; background: <?= $data->link->settings->background_color ?>;">
                    <div class="progress-value"><?= $item->content ?>%</div>
                </div>
            </div>
  <?php endforeach ?>
           
        </div>
    </div>
</div>



</div>
