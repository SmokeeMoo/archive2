<?php defined('ALTUMCODE') || die() ?>

<style>
.news{width: 140px}.news-scroll a{text-decoration: none}.dot{height: 6px;width: 6px;margin-left: 3px;margin-right: 3px;margin-top: 2px !important; margin-bottom: 1px; background-color: rgb(207,23,23);border-radius: 50%;display: inline-block}
</style>
 
 
<div id="<?= 'biolink_block_id_' . $data->link->biolink_block_id ?>" data-biolink-block-id="<?= $data->link->biolink_block_id ?>" class="col-12 my-2">

<div class="container mt-5" style="margin-top: 1rem !important;">
    <div class="row">
        <div class="col-md-12" style="padding-right: 0; padding-left: 0">
            <div class="d-flex justify-content-between align-items-center breaking-news bg-white" style="border-radius: 5px">
                <div class="d-flex flex-row flex-grow-1 flex-fill justify-content-center py-2 text-white px-1 news" style="background:<?= $data->link->settings->background_color ?>; border-radius: 5px 0 0 5px;"><span class="d-flex align-items-center" style="color:<?= $data->link->settings->text_color ?>; font-size: 90%;"> <?= $data->link->settings->block_title ?></span></div>
                <marquee class="news-scroll" behavior="scroll" direction="left" onmouseover="this.stop();" onmouseout="this.start();" style="font-size: 90%;"> 
                 <?php foreach($data->link->settings->items as $key => $item): ?>
                 <span class="<?= !empty($item->content) ? null : 'd-none' ?>"><a href="<?= $item->content ?>" style="border-bottom:1px dashed <?= $data->link->settings->background_color ?>"><?= $item->title ?></a></span>
                 <span class="<?= !empty($item->content) ? 'd-none' : null ?>" style="color:#222;"><?= $item->title ?></span>
                 <span class="dot"></span> 
                <?php endforeach ?>
                </marquee>
            </div>
        </div>
    </div>
</div>


</div>
