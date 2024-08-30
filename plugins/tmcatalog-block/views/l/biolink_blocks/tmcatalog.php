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
          
    <div class="row">
    <div class="col-12 col-md-12">
        <div class="card shadow" style="background:<?= $data->link->settings->background_color ?>; color:<?= $data->link->settings->text_color ?>; border: 0px;">
            
          <?php if ($data->link->settings->image_0 || $data->link->settings->image_1 || $data->link->settings->image_2) { ?>  
            <div id="carouselExampleIndicators" class="carousel slide" data-ride="carousel">
                
                <?php if ($data->link->settings->image_0 && ($data->link->settings->image_1 || $data->link->settings->image_2) || $data->link->settings->image_1 && ($data->link->settings->image_0 || $data->link->settings->image_2) || $data->link->settings->image_2 && ($data->link->settings->image_0 || $data->link->settings->image_1)) { ?>
  <ol class="carousel-indicators">
       <?php if ($data->link->settings->image_0) { ?>
    <li data-target="#carouselExampleIndicators" data-slide-to="0" class="<?= ($data->link->settings->image_0) ? 'active' : '' ?>"></li><?php } ?>
     <?php if ($data->link->settings->image_1) { ?>
    <li data-target="#carouselExampleIndicators" data-slide-to="1" class="<?= ($data->link->settings->image_1 && !$data->link->settings->image_0) ? 'active' : '' ?>"></li>
    <?php } ?>
     <?php if ($data->link->settings->image_2) { ?>
    <li data-target="#carouselExampleIndicators" data-slide-to="2" class="<?= ($data->link->settings->image_2 && !$data->link->settings->image_0 && !$data->link->settings->image_1) ? 'active' : '' ?>"></li>
    <?php } ?>
  </ol>
  <?php } ?>
  <div class="carousel-inner">
       <?php if ($data->link->settings->image_0) { ?>
    <div class="carousel-item <?= ($data->link->settings->image_0) ? 'active' : '' ?>">
      <img class="d-block w-100" style="aspect-ratio: 1/1; border-radius: 0.25rem 0.25rem 0 0;" src="<?= $data->link->settings->image_0 ? UPLOADS_FULL_URL . 'block_images/' . $data->link->settings->image_0 : null ?>" alt="<?= $data->link->settings->title ?>">
    </div>
    <?php } ?>
    <?php if ($data->link->settings->image_1) { ?>
    <div class="carousel-item <?= ($data->link->settings->image_1 && !$data->link->settings->image_0) ? 'active' : '' ?>">
      <img class="d-block w-100" style="aspect-ratio: 1/1; border-radius: 0.25rem 0.25rem 0 0;" src="<?= $data->link->settings->image_1 ? UPLOADS_FULL_URL . 'block_images/' . $data->link->settings->image_1 : null ?>" alt="<?= $data->link->settings->title ?>">
    </div>
    <?php } ?>
    <?php if ($data->link->settings->image_2) { ?>
    <div class="carousel-item <?= ($data->link->settings->image_2 && !$data->link->settings->image_0 && !$data->link->settings->image_1) ? 'active' : '' ?>">
      <img class="d-block w-100" style="aspect-ratio: 1/1; border-radius: 0.25rem 0.25rem 0 0;" src="<?= $data->link->settings->image_2 ? UPLOADS_FULL_URL . 'block_images/' . $data->link->settings->image_2 : null ?>" alt="<?= $data->link->settings->title ?>">
    </div>
    <?php } ?>
  </div>
   <?php if ($data->link->settings->image_0 && ($data->link->settings->image_1 || $data->link->settings->image_2) || $data->link->settings->image_1 && ($data->link->settings->image_0 || $data->link->settings->image_2) || $data->link->settings->image_2 && ($data->link->settings->image_0 || $data->link->settings->image_1)) { ?>
  <a class="carousel-control-prev" href="#carouselExampleIndicators" role="button" data-slide="prev">
    <span class="carousel-control-prev-icon" aria-hidden="true"></span>
    <span class="sr-only">Previous</span>
  </a>
  <a class="carousel-control-next" href="#carouselExampleIndicators" role="button" data-slide="next">
    <span class="carousel-control-next-icon" aria-hidden="true"></span>
    <span class="sr-only">Next</span>
  </a>
  <?php } ?>
</div>
<?php } ?>

 <?php if ($data->link->settings->title || $data->link->settings->text || $data->link->settings->cost || $data->link->settings->title_link || $data->link->settings->url_link) { ?>  
            <div class="card-footer p-4" style="background:<?= $data->link->settings->background_color ?>; border-top: 0px solid <?= $data->link->settings->text_color ?>;">
                <h5><?= $data->link->settings->title ?></h5>
                <h3 class="h6 fw-light text-gray mt-2"><?= $data->link->settings->text ?></h3>
 <div class="text-center">
 <span class="h5 mb-0 text-gray"><?= $data->link->settings->cost ?></span>    
</div>  

 <?php if ($data->link->settings->url_link || $data->link->settings->title_link) { ?>
 <div class="text-center py-3">
                    <a style="padding: 0.3em 1em; border:1px solid <?= $data->link->settings->text_color ?>; border-radius:3px; background:<?= $data->link->settings->background_color ?>; color:<?= $data->link->settings->text_color ?>"  href="<?= $data->link->settings->url_link ?>"><?= $data->link->settings->title_link ?></a>
                </div>
                <?php } ?>

            </div>
            <?php } ?>
        </div> 
    </div>
</div>


</div>
