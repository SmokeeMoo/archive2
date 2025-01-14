<?php defined('ALTUMCODE') || die() ?>


<div id="<?= 'biolink_block_id_' . $data->link->biolink_block_id ?>" data-biolink-block-id="<?= $data->link->biolink_block_id ?>" class="col-12 my-2">
    
  <script src="//maxcdn.bootstrapcdn.com/bootstrap/4.1.1/js/bootstrap.min.js"></script>
<script src="//cdnjs.cloudflare.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
<!------ Include the above in your HEAD tag ---------->

<link href="https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.2.0/assets/owl.carousel.css" rel="stylesheet">
<link href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/3.7.0/animate.css" rel="stylesheet">
<section class="testimonial-section">
		<div class="large-container">

			<div class="testimonial-carousel owl-carousel owl-theme">
			    <?php foreach($data->link->settings->items as $key => $item): ?>
			     <?php if($item->enable): ?>
				<div class="testimonial-block">
					<div class="inner-box">
						<div class="text"><?= $item->content ?></div>
						<div class="info-box">
							<div class="thumb"><img src="/uploads/tmreview_images/<?= $item->image ?>" alt=""></div>
							<h4 class="name"><?= $item->title ?></h4>
							<span class="designation"><?= $item->subcontent ?></span>
						</div>
					</div>
				</div>
				<?php endif ?>
				<?php endforeach ?>

			
			</div>
		</div>
	</section>
	<script src="https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.2.0/owl.carousel.js"></script>
<style>

.fa-spin {
  -webkit-animation: fa-spin 2s infinite linear;
          animation: fa-spin 2s infinite linear; }

.fa-pulse {
  -webkit-animation: fa-spin 1s infinite steps(8);
          animation: fa-spin 1s infinite steps(8); }

@-webkit-keyframes fa-spin {
  0% {
    -webkit-transform: rotate(0deg);
            transform: rotate(0deg); }
  100% {
    -webkit-transform: rotate(360deg);
            transform: rotate(360deg); } }

@keyframes fa-spin {
  0% {
    -webkit-transform: rotate(0deg);
            transform: rotate(0deg); }
  100% {
    -webkit-transform: rotate(360deg);
            transform: rotate(360deg); } }
.large-container {
    position: static;
    max-width: 100%;
    //padding: 0px 5px;
    margin: 0 auto;
}

.testimonial-section{
	position: relative;
	padding-top: 10px;
	padding-bottom: 10px;
}

.testimonial-section:before{
display: none;
	position: absolute;
	left: -200px;
	top: 22%;
	background-image: url(http://t.commonsupport.com/adro/images/icons/ring-circle.png);
	background-repeat: no-repeat;
	background-position: center;
	width: 701px;
	height: 756px;
	content: "";
	-webkit-animation: fa-spin 25s infinite alternate;
	-moz-animation: fa-spin 25s infinite alternate;
	-ms-animation: fa-spin 25s infinite alternate;
	-o-animation: fa-spin 25s infinite alternate;
	animation: fa-spin 25s infinite alternate;
}

.testimonial-section .sec-title{
	position: relative;
	margin-bottom: 115px;
}

.testimonial-section .sec-title .title{
	margin-bottom: 20px;
}

.testimonial-section .testimonial-carousel{
	position: relative;
	max-width: 100%;
	margin: 0 0 50px;
}

.testimonial-block{
	position: relative;
	padding: 5px;
}

.testimonial-block .inner-box{
	padding: 30px 10px;
	background-color:<?= $data->link->settings->background_color ?>;
	//box-shadow: 0 0 50px rgba(226,222,232,0.75);
border-radius: 15px;
}

.testimonial-block .text{
	position: relative;
	display: block;
	line-height: 1.2;
text-align: left;
padding: 0 15px;
	color: <?= $data->link->settings->text_color ?>;
	font-weight: 400;
	margin-bottom: 50px;
}

.testimonial-block .info-box{
	position: relative;
	padding-left: 115px;
	padding-top: 10px;
}

.testimonial-block .info-box .thumb{
	position: absolute;
	left: 0;
	top: 0;
	height: 82px;
	width: 82px;
}

.testimonial-block .info-box .thumb img{
	border: 6px solid #e5e6fa;
	object-fit: cover;
    aspect-ratio: 1;
	border-radius: 50%;
	overflow: hidden;
	display: block;
	width: 100%;
	box-shadow: 0 45px 45px rgba(147,147,147,0.35);
}

.testimonial-block .info-box .name{
	position: relative;
	display: block;
	font-size: 120%;
	line-height: 1.2em;
	color: <?= $data->link->settings->text_color ?>;
	font-weight: 700;
	margin-bottom: 5px;

}

.testimonial-block .info-box .designation{
	position: relative;
	display: block;
	line-height: 24px;
	color: <?= $data->link->settings->text_color ?>;
	font-weight: 400;
}

.testimonial-carousel .owl-nav{
	position: absolute;
    right: 0px;
    bottom: -60px;
}

.testimonial-carousel .owl-next,
.testimonial-carousel .owl-prev{
	position: relative;
	display: inline-block;
	height: 50px;
	width: 50px;
	line-height: 50px;
	text-align: center;
	border-radius: 50%;
	background-color:<?= $data->link->settings->background_color ?>;
	-webkit-transition: all 300ms ease;
	-moz-transition: all 300ms ease;
	-ms-transition: all 300ms ease;
	-o-transition: all 300ms ease;
	transition: all 300ms ease;
margin-left: 10px;
}

.testimonial-carousel .owl-next:hover,
.testimonial-carousel .owl-prev:hover{
	background-color:#00df97;
	//box-shadow: 0 24px 24px rgba(187,187,187,.75);
}

.arrow-right,
.arrow-left{
	position: relative;
	display: inline-block;
	height: 9px;
	width: 43px;
	background-image: url(http://t.commonsupport.com/adro/images/icons/arrow-left-2.png);
	background-repeat: no-repeat;
	background-position: center;
}

.arrow-right{
	background-image: url(http://t.commonsupport.com/adro/images/icons/arrow-right-2.png);
}

.testimonial-section .thumb-layer{
    position: absolute;
    right: 30px;
    top: 120px;
}

.testimonial-section .thumb-layer .image{
	position: relative;
	margin-right: 0;
}

.testimonial-section .thumb-layer .image img{
	display: inline-block;
	max-width: 100%;
	height: auto;
}

.owl-carousel .owl-stage-outer {
    border-radius: 15px;
}
</style>
<script>
(function($) {
	
	"use strict";
	
	
	//Hide Loading Box (Preloader)
	function handlePreloader() {
		if($('.preloader').length){
			$('.preloader').delay(200).fadeOut(500);
		}
	}
	
	
	//Update Header Style and Scroll to Top
	function headerStyle() {
		if($('.main-header').length){
			var windowpos = $(window).scrollTop();
			var siteHeader = $('.main-header');
			var scrollLink = $('.scroll-to-top');
			if (windowpos >= 1) {
				siteHeader.addClass('fixed-header');
				scrollLink.fadeIn(300);
			} else {
				siteHeader.removeClass('fixed-header');
				scrollLink.fadeOut(300);
			}
		}
	}
	
	headerStyle();
	
	
	//Submenu Dropdown Toggle
	if($('.main-header li.dropdown ul').length){
		$('.main-header li.dropdown').append('<div class="dropdown-btn"><span class="fas fa-angle-down"></span></div>');
		
		//Dropdown Button
		$('.main-header li.dropdown .dropdown-btn').on('click', function() {
			$(this).prev('ul').slideToggle(500);
		});
		
		//Disable dropdown parent link
		$('.main-header .navigation li.dropdown > a,.hidden-bar .side-menu li.dropdown > a').on('click', function(e) {
			e.preventDefault();
		});
	}

	//Side Content Toggle
	if($('.main-header .outer-box .nav-btn').length){
		//Show Form
		$('.main-header .outer-box .nav-btn').on('click', function(e) {
			e.preventDefault();
			$('body').addClass('side-content-visible');
		});
		//Hide Form
		$('.hidden-bar .inner-box .cross-icon,.form-back-drop,.close-menu').on('click', function(e) {
			e.preventDefault();
			$('body').removeClass('side-content-visible');
		});
		//Dropdown Menu
		$('.fullscreen-menu .navigation li.dropdown > a').on('click', function() {
			$(this).next('ul').slideToggle(500);
		});
	}

		//Hidden Sidebar
	if ($('.hidden-bar').length) {
		$('.hidden-bar').mCustomScrollbar({
		    theme:"dark"
		});
	}

	// Testimonial Carousel
	if ($('.testimonial-carousel').length) {
		$('.testimonial-carousel').owlCarousel({
			animateOut: 'slideOutDown',
		    animateIn: 'zoomIn',
			loop:true,
			margin:0,
			nav:true,
			smartSpeed: 300,
			autoplay: 7000,
			navText: [ '<span class="arrow-left"></span>', '<span class="arrow-right"></span>' ],
			responsive:{
				0:{
					items:1
				},
				600:{
					items:1
				},
				800:{
					items:1
				},
				1024:{
					items:1
				}
			}
		});  		
	}


	//Fact Counter + Text Count
	if($('.count-box').length){
		$('.count-box').appear(function(){
	
			var $t = $(this),
				n = $t.find(".count-text").attr("data-stop"),
				r = parseInt($t.find(".count-text").attr("data-speed"), 10);
				
			if (!$t.hasClass("counted")) {
				$t.addClass("counted");
				$({
					countNum: $t.find(".count-text").text()
				}).animate({
					countNum: n
				}, {
					duration: r,
					easing: "linear",
					step: function() {
						$t.find(".count-text").text(Math.floor(this.countNum));
					},
					complete: function() {
						$t.find(".count-text").text(this.countNum);
					}
				});
			}
			
		},{accY: 0});
	}

	
	//Accordion Box
	if($('.accordion-box').length){
		$(".accordion-box").on('click', '.acc-btn', function() {
			
			var outerBox = $(this).parents('.accordion-box');
			var target = $(this).parents('.accordion');
			
			if($(this).hasClass('active')!==true){
				$(outerBox).find('.accordion .acc-btn').removeClass('active');
			}
			
			if ($(this).next('.acc-content').is(':visible')){
				return false;
			}else{
				$(this).addClass('active');
				$(outerBox).children('.accordion').removeClass('active-block');
				$(outerBox).find('.accordion').children('.acc-content').slideUp(300);
				target.addClass('active-block');
				$(this).next('.acc-content').slideDown(300);	
			}
		});	
	}
	
	
	//Tabs Box
	if($('.tabs-box').length){
		$('.tabs-box .tab-buttons .tab-btn').on('click', function(e) {
			e.preventDefault();
			var target = $($(this).attr('data-tab'));
			
			if ($(target).is(':visible')){
				return false;
			}else{
				target.parents('.tabs-box').find('.tab-buttons').find('.tab-btn').removeClass('active-btn');
				$(this).addClass('active-btn');
				target.parents('.tabs-box').find('.tabs-content').find('.tab').fadeOut(0);
				target.parents('.tabs-box').find('.tabs-content').find('.tab').removeClass('active-tab animated fadeIn');
				$(target).fadeIn(300);
				$(target).addClass('active-tab animated fadeIn');
			}
		});
	}
	

	//Default Masonary
	function defaultMasonry() {
		if($('.masonry-items-container').length){
	
			var winDow = $(window);
			// Needed variables
			var $container=$('.masonry-items-container');
	
			$container.isotope({
				itemSelector: '.masonry-item',
				 masonry: {
					columnWidth :2
				 },
				animationOptions:{
					duration:500,
					easing:'linear'
				}
			});
	
			winDow.on('resize', function(){

				$container.isotope({ 
					itemSelector: '.masonry-item',
					animationOptions: {
						duration: 500,
						easing	: 'linear',
						queue	: false
					}
				});
			});
		}
	}
	defaultMasonry();

	//Gallery Filters
	 if($('.filter-list').length){
	 	 $('.filter-list').mixItUp({});
	 }
	
	//LightBox / Fancybox
	if($('.lightbox-image').length) {
		$('.lightbox-image').fancybox({
			openEffect  : 'fade',
			closeEffect : 'fade',
			helpers : {
				media : {}
			}
		});
	}
	
	//Contact Form Validation
	if($('#contact-form').length){
		$('#contact-form').validate({
			rules: {
				username: {
					required: true
				},
				email: {
					required: true,
					email: true
				},
				phone: {
					required: true
				},
				message: {
					required: true
				}
			}
		});
	}
	
	
	// Scroll to a Specific Div
	if($('.scroll-to-target').length){
		$(".scroll-to-target").on('click', function() {
			var target = $(this).attr('data-target');
		   // animate
		   $('html, body').animate({
			   scrollTop: $(target).offset().top
			 }, 1500);
	
		});
	}

	if($('.paroller').length){
		$('.paroller').paroller({
			  factor: 0.05,            // multiplier for scrolling speed and offset, +- values for direction control  
			  factorLg: 0.05,          // multiplier for scrolling speed and offset if window width is less than 1200px, +- values for direction control  
			  type: 'foreground',     // background, foreground  
			  direction: 'horizontal' // vertical, horizontal  
		});
	}


	if($('.timer').length){
	   $(function(){
		    $('[data-countdown]').each(function() {
		   var $this = $(this), finalDate = $(this).data('countdown');
		   $this.countdown(finalDate, function(event) {
		     $this.html(event.strftime('%D days %H:%M:%S'));
		   });
		 });
		});

	   $('.cs-countdown').countdown('').on('update.countdown', function(event) {
		  var $this = $(this).html(event.strftime('<div class="count-col"><span>%m</span><h6>Months</h6></div> <div class="count-col"><span>%D</span><h6>days</h6></div> <div class="count-col"><span>%H</span><h6>Hours</h6></div> <div class="count-col"><span>%M</span><h6>Minutes</h6></div> <div class="count-col"><span>%S</span><h6>Seconds</h6></div>'));
		});
	}
	
	
	// Elements Animation
	if($('.wow').length){
		var wow = new WOW(
		  {
			boxClass:     'wow',      // animated element css class (default is wow)
			animateClass: 'animated', // animation css class (default is animated)
			offset:       0,          // distance to the element when triggering the animation (default is 0)
			mobile:       true,       // trigger animations on mobile devices (default is true)
			live:         true       // act on asynchronously loaded content (default is true)
		  }
		);
		wow.init();
	}


/* ==========================================================================
   When document is Scrollig, do
   ========================================================================== */
	
	$(window).on('scroll', function() {
		headerStyle();
	});
	
/* ==========================================================================
   When document is loading, do
   ========================================================================== */
	
	$(window).on('load', function() {
		handlePreloader();
		defaultMasonry();
	});	

})(window.jQuery);
</script>



</div>
