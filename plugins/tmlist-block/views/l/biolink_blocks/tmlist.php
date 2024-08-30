<?php defined('ALTUMCODE') || die() ?>

<style>
 /* List */
.tmlist ul {
  counter-reset: index;  
  padding: 0;
  max-width: 100%;
}

/* List element */
.tmlist li {
  counter-increment: index; 
  display: flex;
  align-items: center;
  padding: 12px 0;
  box-sizing: border-box;
text-align: left;
color:<?= $data->link->settings->text_color ?>;
}


/* Element counter */
.tmlist li::before {
  content: counters(index, ".", decimal-leading-zero);
  font-size: 1.5rem;
  text-align: right;
  font-weight: bold;
  min-width: 50px;
  padding-right: 12px;
  font-feature-settings: "tnum";
  font-variant-numeric: tabular-nums;
  align-self: flex-start;
 // background-image: linear-gradient(to bottom, aquamarine, orangered);
background: <?= $data->link->settings->background_color ?>;
  background-attachment: fixed;
  -webkit-background-clip: text;
  -webkit-text-fill-color: transparent;
}


/* Element separation */
.tmlist li + li {
  border-top: 1px solid rgba(255,255,255,0.2);
}
</style>


<div id="<?= 'biolink_block_id_' . $data->link->biolink_block_id ?>" data-biolink-block-id="<?= $data->link->biolink_block_id ?>" class="col-12 my-2">
    
    <ul class="tmlist">
         <?php foreach($data->link->settings->items as $key => $item): ?>
  <li class="tmlist"><?= $item->title ?></li>
<?php endforeach ?>
 </ul>

   

    
 


</div>
