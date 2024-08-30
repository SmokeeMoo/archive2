<?php defined('ALTUMCODE') || die() ?>
<!-- Add the slick-theme.css if you want default styling -->


<style>
    /* Slider start */
.cardddd {


  position: relative;


transition: 0.4s ease-out;
 margin: 0px 0px 0px 2px;}

.cardd {


  position: relative;


transition: 0.4s ease-out;
 margin: 30px 10px 30px 10px;}

  .cardd:hover {
    transform: translateY(10px); }
    .cardd:hover:before {
      opacity: 1; }
    .cardd:hover .info {
      opacity: 1;
      transform: translateY(0px); }

.carddd {
	display: flex;
    -webkit-box-align: center;
    align-items: center;
  width: 80px;
  height: 80px;
white-space: nowrap;
  position: relative;


transition: 0.4s ease-out;
}
.slick-slider
{
    position: relative;

    display: block;
    box-sizing: border-box;

    -webkit-user-select: none;
       -moz-user-select: none;
        -ms-user-select: none;
            user-select: none;

    -webkit-touch-callout: none;
    -khtml-user-select: none;
    -ms-touch-action: pan-y;
        touch-action: pan-y;
    -webkit-tap-highlight-color: transparent;
}

.slick-list
{
    position: relative;

    display: block;

overflow: hidden;


    margin: 0;
    padding: 0;
}
.slick-list:focus
{
    outline: none;
}
.slick-list.dragging
{
    cursor: pointer;
    cursor: hand;
}

.slick-slider .slick-track,
.slick-slider .slick-list
{
    -webkit-transform: translate3d(0, 0, 0);
       -moz-transform: translate3d(0, 0, 0);
        -ms-transform: translate3d(0, 0, 0);
         -o-transform: translate3d(0, 0, 0);
            transform: translate3d(0, 0, 0);
}

.slick-track
{
    position: relative;
    top: 0;
    left: 0;

    display: block;
    margin-left: auto;
    margin-right: auto;
}
.slick-track:before,
.slick-track:after
{
    display: table;

    content: '';
}
.slick-track:after
{
    clear: both;
}
.slick-loading .slick-track
{
    visibility: hidden;
}

.slick-slide
{
    display: none;
    float: left;

    height: auto;
    min-height: 1px;
}
[dir='rtl'] .slick-slide
{
    float: right;
}


.slickimage{
display: block;
    width: 80%;
    height: auto;
    border-radius: 11px;
    box-shadow: 0px 2px 4px rgb(0 0 0 / 50%);
    object-fit: cover;
	object-position: center center;
}

.avatorslide
{
display: block;
width: 10%;

    border-radius: 50px !important;

    object-fit: cover;
	object-position: center center;
}
.slick-slide.slick-loading img
{
    display: none;
}
.slick-slide.dragging img
{
    pointer-events: none;
}
.slick-initialized .slick-slide
{
    display: block;
}

.slick-loading .slick-slide
{
    visibility: hidden;
}
.slick-vertical .slick-slide
{
    display: block;

    height: auto;

    border: 1px solid transparent;
}
.slick-arrow.slick-hidden {
    display: none;
}

.slick-slideimage {
    border: 5px solid #fff;
    display: block;
    width: 100%;
	margin: auto;
}


@media (min-width: 600px) {
	.slick-slideimage {

  width: 90%;

	}
}

@media (min-width: 750px) {
	.slick-slideimage {

  width: 90%;

	}
}


@media (min-width: 980px) {
	.slick-slideimage {

  width: 80%;

	}
}



@media (min-width: 1200px) {
	.slick-slideimage {

  width: 80%;

	}
}


.link-btn-arrow-wrapper-setting{
    overflow: hidden;
    position: relative;
    float: right;
    right: 20px;
    top: 32%;
}

button[aria-expanded=true] .fa-chevron-right {
   display: none;
}
button[aria-expanded=true] .fa-chevron-up {
   display: none;
}
button[aria-expanded=false] .fa-chevron-down {
   display: none;
}
/* Dots */
.slick-dotted.slick-slider
{
    margin-bottom: 30px;
}

.slick-dots
{
    position: absolute;
    bottom: -25px;

    display: block;

    width: 100%;
    padding: 0;
    margin: 0;

    list-style: none;

    text-align: center;
}
.slick-dots li
{
    position: relative;

    display: inline-block;

    width: 20px;
    height: 20px;
    margin: 0 5px;
    padding: 0;

    cursor: pointer;
}


 .slick-dots li:only-child{
	 display: none;
}
.slick-dots li button
{
    font-size: 0;
    line-height: 0;

    display: block;

    width: 20px;
    height: 20px;
    padding: 5px;

    cursor: pointer;

    color: transparent;
    border: 0;
    outline: none;
    background: transparent;
}
.slick-dots li button:hover,
.slick-dots li button:focus
{
    outline: none;
}
.slick-dots li button:hover:before,
.slick-dots li button:focus:before
{
    opacity: 1;
}
.slick-dots li button:before
{
    font-family: 'slick';
    font-size: 35px;
    line-height: 20px;

    position: absolute;
    top: 0;
    left: 0;

    width: 20px;
    height: 20px;

    content: '•';
    text-align: center;

    opacity: .25;
    color: black;

    -webkit-font-smoothing: antialiased;
    -moz-osx-font-smoothing: grayscale;
}
.slick-dots li.slick-active button:before
{
    opacity: .75;
    color: black;
}

/* Arrows */
.slick-prev,
.slick-next
{
    font-size: 0;
    line-height: 0;

    position: absolute;
    top: 50%;

    display: block;

    width: 20px;
    height: 20px;
    padding: 0;
    -webkit-transform: translate(0, -50%);
    -ms-transform: translate(0, -50%);
    transform: translate(0, -50%);

    cursor: pointer;

    color: transparent;
    border: none;
    outline: none;
    background: transparent;
}
.slick-prev:hover,
.slick-prev:focus,
.slick-next:hover,
.slick-next:focus
{

    background: #000;
    opacity: .75;
    z-index: 9;
}
.slick-prev:hover:before,
.slick-prev:focus:before,
.slick-next:hover:before,
.slick-next:focus:before
{
    opacity: 1;
}
.slick-prev.slick-disabled:before,
.slick-next.slick-disabled:before
{
    opacity: .25;
}

.slick-prev:before,
.slick-next:before
{
    font-family: 'slick';
    font-size: 20px;
    line-height: 1;

    opacity: .75;
    color: white;

    -webkit-font-smoothing: antialiased;
    -moz-osx-font-smoothing: grayscale;
}

.slick-prev
{
    left: 0px;

    background: #000;
    opacity: .45;

    border-radius: 50%;
    z-index: 9;
        width: 35px;
    height: 35px;
}
[dir='rtl'] .slick-prev
{
    right: -25px;
    left: auto;
}
.slick-prev:before
{
    content: '←';
}
[dir='rtl'] .slick-prev:before
{
    content: '→';
}

.slick-next
{
    right: 0px;

    background: #000;
    opacity: .45;

    border-radius: 50%;
    z-index: 9; 
        width: 35px;
    height: 35px;   
}
[dir='rtl'] .slick-next
{
    right: auto;
    left: -25px;
}
.slick-next:before
{
    content: '→';
}
[dir='rtl'] .slick-next:before
{
    content: '←';
}
a[aria-expanded=true] .fa-chevron-right {
   display: none;
}
a[aria-expanded=true] .fa-chevron-up {
   display: none;
}
a[aria-expanded=false] .fa-chevron-down {
   display: none;
}
.link-btn-arrow-wrapper-setting2 {
    overflow: hidden;
    position: relative;
    float: right;
    right: 20px;

}

.tmslider_img {
    border-radius: 10px;
}

.tmslider_titles {
display:block;
width: 100%;
border-radius: 0px 0px 10px 10px; 
line-height: 1.1; padding: 10px 5px 10px; 
font-size: 14px; margin: 0 auto; 
box-shadow: rgb(0 0 0 / 50%) 0px 2px 4px;    
}
/* Slider end */
</style>

<div id="<?= 'biolink_block_id_' . $data->link->biolink_block_id ?>" data-biolink-block-id="<?= $data->link->biolink_block_id ?>" class="col-12 my-2">

<div class="tmslider_<?= $data->link->biolink_block_id ?>">
    <?php foreach($data->link->settings->items as $key => $item): ?>
    <?php if($item->enable): ?>
    
  <div class="cardd" style="box-shadow: 0px 2px 4px rgb(0 0 0 / 50%);
    border-radius: 10px;">
       <?php if($item->link): ?>
    <a href="<?= $item->link . $data->link->utm_query ?>" data-biolink-block-id="<?= $data->link->biolink_block_id ?>" target="_blank">
    <?php endif ?>
      <img class="tmslider_img" style="width: 100%; aspect-ratio: 1; object-fit: cover;" src="/uploads/slider_images/<?= $item->image ?>" alt="<?= $item->content ?>">
      <?php if($data->link->settings->titles): ?>
      <style>.tmslider_img {border-radius: 10px 10px 0 0;}</style>
      <div class="tmslider_titles" style="background: <?= $data->link->settings->background_color ?>; color: <?= $data->link->settings->text_color ?>;"><?= $item->title ?></div>
      <?php endif ?>
       <?php if($item->link): ?>
    </a>
	<?php endif ?>	
      </div>
	
      <?php endif ?>
 <?php endforeach ?>
</div>

<script type="text/javascript" src="https://code.jquery.com/jquery-1.11.0.min.js"></script>
<script type="text/javascript" src="https://code.jquery.com/jquery-migrate-1.2.1.min.js"></script>
<script type="text/javascript" src="https://cdn.jsdelivr.net/npm/slick-carousel@1.8.1/slick/slick.min.js"></script>

<script>
$('.tmslider_<?= $data->link->biolink_block_id ?>').slick({
  centerMode: true,
  autoplay: <?php if($data->link->settings->autoplay): ?>true<?php else: ?>false<?php endif ?>,
  dots: <?php if($data->link->settings->dots): ?>true<?php else: ?>false<?php endif ?>,
  arrows: <?php if($data->link->settings->arrows): ?>true<?php else: ?>false<?php endif ?>,
  
  centerPadding: '60px',
  slidesToShow: <?= $data->link->settings->number_of_sliders_for_desktop ?>,
  responsive: [
    {
      breakpoint: 768,
      settings: {
        centerMode: true,
        centerPadding: '40px',
        slidesToShow: <?= $data->link->settings->number_of_sliders_for_tablet ?>
      }
    },
    {
      breakpoint: 480,
      settings: {
        centerMode: true,
        centerPadding: '40px',
        slidesToShow: <?= $data->link->settings->number_of_sliders_for_mobile ?>
      }
    }
  ]
});
</script>

</div>
