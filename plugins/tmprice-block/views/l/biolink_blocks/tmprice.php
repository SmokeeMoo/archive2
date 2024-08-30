<?php defined('ALTUMCODE') || die() ?>

<style>
.module {
    position: relative;
    background: <?= $data->link->settings->background_color ?>;
    padding: 30px 0;
    border-radius: 15px;
}

  .intro-title,
  .module-title,
  .callout-text,
  .iconbox-title {
    font-size: 200%;
    margin: 0 0 5px;
  }
  .module-title, .menu-title, .menu-price {
    color: <?= $data->link->settings->text_color ?>;  
  }
  .module-subtitle, .menu-detail {
      opacity: 0.7;
    color: <?= $data->link->settings->text_color ?>;    
  }
  
.module-subtitle {
  font-size: 100%;
  text-transform: uppercase;
  letter-spacing: 1px;
  margin: 0;
}
.menu-title,
.menu-price {
font-size: 90%;
    margin: 0;
    font-weight: bold;
}
.menu-detail {
    font-size: 70%;
}
.menu-price-detail {
    position: relative;
    text-align: right;
}
.menu {
    border-bottom: 1px dotted #ddd;
    padding: 0 0 10px;
    margin: 0 0 20px;
    text-align: left;
}
.module-header {
    text-align: center;
    margin: 0 0 30px;
}
@media screen and (max-width: 768px) {
.menu-price-detail {
    position: relative;
    text-align: right;
    margin-top: 10px;
}}
</style>

<div id="<?= 'biolink_block_id_' . $data->link->biolink_block_id ?>" data-biolink-block-id="<?= $data->link->biolink_block_id ?>" class="col-12 my-2">


<section id="popular" class="module">
  <div class="container">

    <div class="row">
      <div class="col-sm-12 col-sm-offset-3">
        <div class="module-header wow fadeInUp animated" style="visibility: visible; animation-name: fadeInUp;">
          <h2 class="module-title"><?= $data->link->settings->block_title ?></h2>
          <h3 class="module-subtitle"><?= $data->link->settings->block_description ?></h3>
        </div>
      </div>
    </div><!-- .row -->
    <div class="row">
      <div class="col-sm-12">
           <?php foreach($data->link->settings->items as $key => $item): ?>
           <div class="menu">
          <div class="row">
            <div class="col-sm-9">
              <h4 class="menu-title"><?= $item->title ?></h4>
              <div class="menu-detail"><?= $item->content ?></div>
            </div>
            <div class="col-sm-3 menu-price-detail">
              <h4 class="menu-price"><?= $item->cost ?></h4>
            </div>
          </div>
        </div>
        <?php endforeach ?>
        
      
      </div>
      </div>
      </div>
      </section>


</div>
