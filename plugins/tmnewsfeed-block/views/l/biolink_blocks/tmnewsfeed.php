<?php defined('ALTUMCODE') || die() ?>

<script src="//maxcdn.bootstrapcdn.com/bootstrap/4.1.1/js/bootstrap.min.js"></script>
<script src="//cdnjs.cloudflare.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>

<style>
.sidebar-page-container .sidebar .sidebar-post .post-inner .post{
	position: relative;
	padding: 0px 0px 0px 75px;
	padding-bottom: 10px;
	margin-bottom: 6px;
	border-bottom: 1px solid #e5e5e5;
}

.sidebar-page-container .sidebar .sidebar-post .post-inner .post:last-child{
	border-bottom: none;
}

.sidebar-page-container .sidebar .sidebar-post .post-inner .post .post-date{
    position: absolute;
    left: 0px;
    top: 4px;
    width: 54px;
    height: 54px;
    text-align: center;
    border-radius: 5px;

}
.sidebar-page-container .sidebar .sidebar-post .post-inner .post .post-date{
background: <?= $data->link->settings->text_color ?>;
}

.sidebar-page-container .sidebar .sidebar-post .post-inner .post .post-date p{
    display: block;
    font-size: 110%;
    font-weight: 500;
    color: <?= $data->link->settings->background_color ?>;
    text-align: center;
    margin:0px;
    padding-top: 5px;
}

.sidebar-page-container .sidebar .sidebar-post .post-inner .post .post-date span{
    position: relative;
    display: block;
    font-size: 60%;
    line-height: 1.3;
    text-transform: uppercase;
    color: <?= $data->link->settings->background_color ?>;
    margin:0px;
    padding:0px;
}

.sidebar-page-container .sidebar .sidebar-post .post-inner .post .file-box{
	position: relative;
	margin-bottom: 2px;
text-align: left;
}

.sidebar-page-container .sidebar .sidebar-post .post-inner .post .file-box i{
    position: relative;
    display: inline-block;
    font-size: 14px;
    color: #666666 !important;
    margin-right: 10px;
}

h3 {
color: <?= $data->link->settings->text_color ?>;
font-size: 150%;
font-weight: bold;
}

.sidebar-page-container .sidebar .sidebar-post .post-inner .post .file-box p{
    position: relative;
    display: inline-block;
    margin-bottom:0px;
    font-size: 90%;
    margin-left: 5px;
    color: <?= $data->link->settings->text_color ?>;
}

.sidebar-page-container .sidebar .sidebar-post .post-inner .post h5{
	position: relative;
	display: block;
	font-size: 90%;
	line-height: 1.2;
	font-weight: 600;
	margin-bottom: 0px;
	color: <?= $data->link->settings->text_color ?>;
	margin:0px;
text-align: left;
}

.file-box {
//display: none;
}

.sidebar-page-container .sidebar .sidebar-post .post-inner .post h5 a{
	display: inline-block;
	color: <?= $data->link->settings->text_color ?>;
}

.post h5 a {
    text-decoration: underline;
    
}
.sidebar-page-container .sidebar .sidebar-post .post-inner .post h5 a:hover{
	color: #e61819;	
}
.carousel-inner-data{
  margin:0px auto;
  height: fit-content;
  overflow:hidden;
}
.carousel-inner-data ul{
  list-style:none;
  position:relative;
padding-inline-start: 0px;
}
.carousel-inner-data li{
  height:auto;
 overflow: hidden;
  text-overflow: ellipsis;
  display: -webkit-box;
  -webkit-box-orient: vertical;
  -webkit-line-clamp: 3;
  max-height: 80px;
  height: 80px;
}
</style>
<script>
$(function(){
      var tickerLength = $('.carousel-inner-data ul li').length;
      var tickerHeight = $('.carousel-inner-data ul li').outerHeight(true);
      $('.carousel-inner-data ul li:last-child').prependTo('.carousel-inner-data ul');
      $('.carousel-inner-data ul').css('marginTop',-80);

      function moveTop(){
        $('.carousel-inner-data ul').animate({
          top : -tickerHeight
      },600, function(){
       $('.carousel-inner-data ul li:first-child').appendTo('.carousel-inner-data ul');
       $('.carousel-inner-data ul').css('top','');
   });

    }
    setInterval( function(){
        moveTop();
    }, 3000);
});
</script>
  

<div id="<?= 'biolink_block_id_' . $data->link->biolink_block_id ?>" data-biolink-block-id="<?= $data->link->biolink_block_id ?>" class="col-12 my-2">
    


<div class="container" style="background: <?= $data->link->settings->background_color ?>;padding: 25px 20px;border-radius: 15px;"
>
	<div class="row">
		<div class="col-lg-12 col-md-12 col-sm-12 sidebar-page-container">
                <div class="sidebar">
                    <div class="sidebar-widget sidebar-post">
                        <div class="widget-title">
                            <h3 style="margin-bottom: 15px;"><?= $data->link->settings->title_block ?></h3>
                        </div>
                        <div class="post-inner">
                                <div class="carousel-inner-data">
                                    <ul>
                                      <?php foreach($data->link->settings->items as $key => $item): ?>  
                                      <?php if($item->enable): ?>
                                        <li>
                                        <div class="post">
                                            <div class="post-date"><p><?= $item->date ?></p><span><?= $item->month ?></span></div>
                                            <div class="file-box"><i class="<?= $data->link->settings->icon_block ?>" style="color:<?= $data->link->settings->text_color ?>; font-size: 90%;"></i><p><?= $item->topic ?></p></div>

 <h5 class="<?= !empty($item->content) ? null : 'd-none' ?>"><a href="<?= $item->content ?>"><?= $item->title ?></a></h5>
 <h5 class="<?= !empty($item->content) ? 'd-none' : null ?>"><?= $item->title ?></h5>
                                        </div>
                                        </li>
                                        <?php endif ?>
                                      <?php endforeach ?>  
                                    </ul>
                                </div>

                        </div>
                    </div>
                </div>
            </div>
	</div>
</div>





</div>
