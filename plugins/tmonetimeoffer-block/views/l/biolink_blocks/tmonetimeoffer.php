<?php defined('ALTUMCODE') || die() ?>
<style>
.tmoto {
    background: <?= $data->link->settings->background_color ?>;
    color: <?= $data->link->settings->text_color ?>;
    padding: 2rem 1rem;
    margin-right: 0px;
    margin-left: 0px;
    border-radius: 0.3rem;
}  

.tmoto_button {
    background: <?= $data->link->settings->border_color ?>;
    color: <?= $data->link->settings->text_color ?>;   
}

.countdown {
  position: initial;
  width: 100%;
  text-align: center;
}
.countdown.style-1 div {
  margin-left: 1rem;
  margin-right: 1rem;
}
.countdown.style-1 div span {
  display: block;
}
.countdown div {
  font-size: 3rem;
  font-weight: 100;
  display: inline-grid;
  padding: 0 0.2rem;
}
.countdown div span {
  font-weight: 300;
  color: #777;
  font-size: 1rem;
  margin-top: -1rem;
}
    
</style>

<style>
    
    .flip-clock {
  text-align: center;
  perspective: 400px;
  margin: 20px auto;
}
.flip-clock *,
.flip-clock *:before,
.flip-clock *:after {
  box-sizing: border-box;
}
.flip-clock__piece {
  display: inline-block;
  margin: 0 5px;
}
.flip-clock__slot {
  font-size: 2vw;
}
.cards {
  display: block;
  position: relative;
  padding-bottom: 0.72em;
  line-height: 0.95;
  background-color: transparent;
  background-clip: border-box;
  border: 0px solid #f1f2f4;
  font-size: 5rem;
}

@media screen and (max-width: 768px) {
 .cards {
 font-size: 2.5rem;    
 }
}

<?php if($data->link->settings->dark_theme == true): ?>
.card__top,
.card__bottom,
.card__back::before,
.card__back::after {
  display: block;
  font-weight: 500;
  height: 0.72em;
  color: #ccc;
  background: #222;
  padding: 0.2em 0.2em;
  border-radius: 0.15em 0.15em 0 0;
  backface-visiblity: hidden;
  transform-style: preserve-3d;
  width: 1.8em;
  transform: translateZ(0);
}
.card__bottom {
  color: #FFF;
  position: absolute;
  top: 50%;
  left: 0;
  border-top: solid 1px #000;
  background: #393939;
  border-radius: 0 0 0.15em 0.15em;
  pointer-events: none;
  overflow: hidden;
}
<?php endif ?>

<?php if($data->link->settings->dark_theme == false): ?>
.card__top,
.card__bottom,
.card__back::before,
.card__back::after {
  display: block;
  font-weight: 500;
  height: 0.72em;
  color: #444;
  background: #e8e8e8;
  padding: 0.2em 0.2em;
  border-radius: 0.15em 0.15em 0 0;
  backface-visiblity: hidden;
  transform-style: preserve-3d;
  width: 1.8em;
  transform: translateZ(0);
}
.card__bottom {
  color: #222;
  position: absolute;
  top: 50%;
  left: 0;
  border-top: solid 1px #000;
  background: #dbdbdb;
  border-radius: 0 0 0.15em 0.15em;
  pointer-events: none;
  overflow: hidden;
}
<?php endif ?>
.card__bottom::after {
  display: block;
  margin-top: -0.72em;
}
.card__back::before,
.card__bottom::after {
  content: attr(data-value);
}
.card__back {
  position: absolute;
  top: 0;
  height: 100%;
  left: 0%;
  pointer-events: none;
}
.card__back::before {
  position: relative;
  z-index: -1;
  overflow: hidden;
}
.flip .card__back::before {
  -webkit-animation: flipTop 0.6s cubic-bezier(0.9, 0.5, 0.5, 0.5);
          animation: flipTop 0.6s cubic-bezier(0.9, 0.5, 0.5, 0.5);
  -webkit-animation-fill-mode: both;
          animation-fill-mode: both;
  transform-origin: center bottom;
}
.flip .card__back .card__bottom {
  transform-origin: center top;
  -webkit-animation-fill-mode: both;
          animation-fill-mode: both;
  -webkit-animation: flipBottom 0.9s cubic-bezier(0.5, 0.5, 0.5, 0.9);
          animation: flipBottom 0.9s cubic-bezier(0.5, 0.5, 0.5, 0.9);
}
@-webkit-keyframes flipTop {
  0% {
    transform: rotateX(0deg);
    z-index: 2;
  }
  0%,
  99% {
    opacity: 0.99;
  }
  100% {
    transform: rotateX(-90deg);
    opacity: 0;
  }
}
@keyframes flipTop {
  0% {
    transform: rotateX(0deg);
    z-index: 2;
  }
  0%,
  99% {
    opacity: 0.99;
  }
  100% {
    transform: rotateX(-90deg);
    opacity: 0;
  }
}
@-webkit-keyframes flipBottom {
  0%,
  50% {
    z-index: -1;
    transform: rotateX(90deg);
    opacity: 0;
  }
  51% {
    opacity: 0.99;
  }
  100% {
    opacity: 0.99;
    transform: rotateX(0deg);
    z-index: 5;
  }
}
@keyframes flipBottom {
  0%,
  50% {
    z-index: -1;
    transform: rotateX(90deg);
    opacity: 0;
  }
  51% {
    opacity: 0.99;
  }
  100% {
    opacity: 0.99;
    transform: rotateX(0deg);
    z-index: 5;
  }
}

</style>

<script src="https://cdn.jsdelivr.net/npm/jquery@3.5.1/dist/jquery.slim.min.js"
        integrity="sha384-DfXdz2htPH0lsSSs5nCTpuj/zy4C+OGpamoFVy38MVBnE+IbbVYUew+OrCXaRkfj"
        crossorigin="anonymous"></script>

<div id="<?= 'biolink_block_id_' . $data->link->biolink_block_id ?>" data-biolink-block-id="<?= $data->link->biolink_block_id ?>" class="col-12 my-2">

<div class="row bg-1 tmoto">
<!-------------------------- Title --------------------------------->
    <div class="col-md-12" id="showtitlebefore">
<<?= $data->link->settings->title_tagh ?>><?= $data->link->settings->title_before ?></<?= $data->link->settings->title_tagh ?>>
<<?= $data->link->settings->text_tagh ?>><?= $data->link->settings->text_before ?></<?= $data->link->settings->text_tagh ?>>
</div>

<?php if($data->link->settings->countdown_enable == true): ?>
<div class="col-md-12" id="showtitleafter">
<<?= $data->link->settings->title_tagh ?>><?= $data->link->settings->title_after ?></<?= $data->link->settings->title_tagh ?>>
<<?= $data->link->settings->text_tagh ?>><?= $data->link->settings->text_after ?></<?= $data->link->settings->text_tagh ?>>
</div>
<?php endif ?>

<!-------------------------- Image --------------------------------->
<?php if($data->link->settings->image_enable == true && ($data->link->settings->image_0 || $data->link->settings->image_1)): ?>
<div class="col-md-12" style="margin: 0 0 1rem;">
 <img id="showimagebefore" src="<?= $data->link->settings->image_0 ? UPLOADS_FULL_URL . 'block_images/' . $data->link->settings->image_0 : null ?>" style="max-width:100%; margin: 0 auto; border-radius: 0.3rem;"></img>
 
 <?php if($data->link->settings->countdown_enable == true): ?>
 <img id="showimageafter" src="<?= $data->link->settings->image_1 ? UPLOADS_FULL_URL . 'block_images/' . $data->link->settings->image_1 : null ?>" style="max-width:100%; margin: 0 auto; border-radius: 0.3rem;"></img>
 <?php endif ?>
</div>
<?php endif ?>

<!-------------------------- Youtube --------------------------------->
<?php if($data->link->settings->youtube_enable == true && ($data->link->settings->youtube_before || $data->link->settings->youtube_after)): ?>
<div class="col-md-12" style="margin: 1rem 0 1rem;">
<?php 
$youtube_old_before  = $data->link->settings->youtube_before;
$youtube_old_after  = $data->link->settings->youtube_after;
$youtube_user_link = "https://www.youtube.com/watch?v=";
$youtube_normal_link   = "https://www.youtube.com/embed/";
$youtube_new_before = str_replace($youtube_user_link, $youtube_normal_link, $youtube_old_before);
$youtube_new_after = str_replace($youtube_user_link, $youtube_normal_link, $youtube_old_after);
if ($data->link->settings->youtube_autoplay == true) { $youtube_autoplay = 1;
} else $youtube_autoplay = 0;
if ($data->link->settings->youtube_controls == true) { $youtube_controls = 1;
} else $youtube_controls = 0;
if ($data->link->settings->youtube_info == true) { $youtube_info = 1;
} else $youtube_info = 0;
if ($data->link->settings->youtube_related == true) { $youtube_related = 1;
} else $youtube_related = 0;
if ($data->link->settings->youtube_loop == true) { $youtube_loop = 1;
} else $youtube_loop = 0;
$youtube_new_before = $youtube_new_before ."?autoplay=". $youtube_autoplay . "&controls=". $youtube_controls ."&showinfo=". $youtube_info ."&rel=". $youtube_related ."&loop=". $youtube_loop;
$youtube_new_after = $youtube_new_after ."?autoplay=". $youtube_autoplay . "&controls=". $youtube_controls ."&showinfo=". $youtube_info ."&rel=". $youtube_related ."&loop=". $youtube_loop;
?>
 <iframe id="showyoutubebefore" width="100%" height="315" src="<?= $youtube_new_before ?>" title="YouTube video player" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share" allowfullscreen></iframe>
<?php if($data->link->settings->countdown_enable == true): ?>
 <iframe id="showyoutubeafter" width="100%" height="315" src="<?= $youtube_new_after ?>" title="YouTube video player" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share" allowfullscreen></iframe>
 <?php endif ?>
</div>
<?php endif ?>
<!-------------------------- End youtube --------------------------------->

<!-------------------------- Countdown --------------------------------->
<?php if($data->link->settings->countdown_enable == true): ?>
<div class="col-md-12" style="margin: 0 0 1rem;">
    <div class="countdown" id="showcountdown">
       <!-- <span></span>
      <div class="hours-container">
        <div class="hours" style="width:100%;"></div><span><?= l('create_biolink_tmonetimeoffer_modal.hours') ?></span>
      </div>
      <div class="minutes-container">
        <div class="minutes" style="width:100%;"></div><span><?= l('create_biolink_tmonetimeoffer_modal.minutes') ?></span>
      </div>
      <div class="seconds-container">
        <div class="seconds" style="width:100%;"></div><span><?= l('create_biolink_tmonetimeoffer_modal.seconds') ?></span>
      </div>
    </div> -->
</div>
<?php endif ?>

<!-------------------------- Button --------------------------------->
<div class="col-md-12 text-center" id="showtextbefore" style="margin-top: 1rem;">
<a class="btn btn-block btn-primary link-btn link-btn-<?= $data->link->settings->border_radius ?> link-hover-animation  animate__animated animate__<?= $data->link->settings->animation_runs ?> animate__<?= $data->link->settings->animation ?> animate__delay-2s" target="<?= $data->link->settings->open_in_new_tab ? '_blank' : '_self' ?>" rel="<?= $data->user->plan_settings->dofollow_is_enabled ? 'dofollow' : 'nofollow' ?>" style="background:<?= $data->link->settings->border_color ?>; border-color:<?= $data->link->settings->text_color ?>; border-width: <?= $data->link->settings->border_width ?>px;border-style: <?= $data->link->settings->border_style ?>;" href="<?= $data->link->settings->location_url_before ?>"><?= $data->link->settings->button_text_before ?></a>
</div>

<?php if($data->link->settings->countdown_enable == true): ?>
<div class="col-md-12 text-center" id="showtextafter" style="margin-top: 1rem;">
<a class="btn btn-block btn-primary link-btn link-btn-<?= $data->link->settings->border_radius ?> link-hover-animation  animate__animated animate__<?= $data->link->settings->animation_runs ?> animate__<?= $data->link->settings->animation ?> animate__delay-2s" target="<?= $data->link->settings->open_in_new_tab ? '_blank' : '_self' ?>" rel="<?= $data->user->plan_settings->dofollow_is_enabled ? 'dofollow' : 'nofollow' ?>" style="background:<?= $data->link->settings->border_color ?>; border-color:<?= $data->link->settings->text_color ?>; border-width: <?= $data->link->settings->border_width ?>px;border-style: <?= $data->link->settings->border_style ?>;" href="<?= $data->link->settings->location_url_after ?>"><?= $data->link->settings->button_text_after ?></a>
</div>
<?php endif ?>



</div>
</div>







<script>

//Cookies
const now = new Date();
const endOffer = Date.now() + <?= (int)$data->link->settings->time * 1000 ?>;
//Delete cookies
<?php if ($data->link->settings->clear_enable == true): ?> 
var cookies = document.cookie.split(/;/);
for (var i = 0, len = cookies.length; i < len; i++) {
	var cookie = cookies[i].split(/=/);
	document.cookie = cookie[0] + "=;max-age=-1";
}
<?php endif ?>

  
function getCookie(name) {
	var matches = document.cookie.match(new RegExp("(?:^|; )" + name.replace(/([\.$?*|{}\(\)\[\]\\\/\+^])/g, '\\$1') + "=([^;]*)"));
	return matches ? decodeURIComponent(matches[1]) : document.cookie="time="+ endOffer + "; max-age=" + <?= $data->link->settings->period ?>;
}

if (parseInt(getCookie('time')) != undefined) {
  endDateOffer = parseInt(getCookie('time'));  
} else endDateOffer = endOffer;



function CountdownTracker(label, value) {
  var countdownhtml = document.getElementById("showcountdown");
  var el = document.createElement("span");

  el.className = "flip-clock__piece";
  el.innerHTML =
    '<b class="flip-clock__card cards"><b class="card__top"></b><b class="card__bottom"></b><b class="card__back"><b class="card__bottom"></b></b></b>' +
    '<span class="flip-clock__slot">' +
    label +
    "</span>"; 

  this.el = el;
  

  var top = el.querySelector(".card__top"),
    bottom = el.querySelector(".card__bottom"),
    back = el.querySelector(".card__back"),
    backBottom = el.querySelector(".card__back .card__bottom");

  this.update = function (val) {
    val = ("0" + val).slice(-2);
    if (val !== this.currentValue) {
      if (this.currentValue >= 0) {
        back.setAttribute("data-value", this.currentValue);
        bottom.setAttribute("data-value", this.currentValue);
      }
      this.currentValue = val;
      top.innerText = this.currentValue;
      backBottom.setAttribute("data-value", this.currentValue);

      this.el.classList.remove("flip");
      void this.el.offsetWidth;
      this.el.classList.add("flip");
    }
  };

  this.update(value);
}


function getTimeRemaining(endtime) {
  var t = Date.parse(endtime) - Date.parse(new Date());
  return {
    Total: t,
    //Days: Math.floor(t / (1000 * 60 * 60 * 24)),
    <?= l('create_biolink_tmonetimeoffer_modal.hours') ?>: Math.floor((t / (1000 * 60 * 60)) % 24),
    <?= l('create_biolink_tmonetimeoffer_modal.minutes') ?>: Math.floor((t / 1000 / 60) % 60),
    <?= l('create_biolink_tmonetimeoffer_modal.seconds') ?>: Math.floor((t / 1000) % 60)
  };
}

function getTime() {
  var t = new Date();
  return {
    Total: t,
    Hours: t.getHours() % 12,
    Minutes: t.getMinutes(),
    Seconds: t.getSeconds()
  };
}

function Clock(countdown, callback) {
  countdown = countdown ? new Date(Date.parse(countdown)) : false;
  callback = callback || function () {};

  var updateFn = countdown ? getTimeRemaining : getTime;

  this.el = document.createElement("div");
  this.el.className = "flip-clock";
  this.el.id = "flipclock";

  var trackers = {},
    t = updateFn(countdown),
    key,
    timeinterval;

  for (key in t) {
    if (key === "Total") {
      continue;
    }
    trackers[key] = new CountdownTracker(key, t[key]);
    this.el.appendChild(trackers[key].el);
  }

  var i = 0;
  function updateClock() {
    timeinterval = requestAnimationFrame(updateClock);

    // throttle so it's not constantly updating the time.
    if (i++ % 10) {
      return;
    }

    var t = updateFn(countdown);
    if (t.Total < 0) {
      cancelAnimationFrame(timeinterval);
      for (key in trackers) {
        trackers[key].update(0);
      }
      callback();
      return;
    }

    for (key in trackers) {
      trackers[key].update(t[key]);
    }
  }

  setTimeout(updateClock, 500);
}
<?php if($data->link->settings->countdown_enable == true): ?>
var deadline = new Date(endDateOffer);
currentDate = Date.now();
/*var deadline = endDateOffer - currentDate;*/
console.log(endDateOffer)
console.log(currentDate)
console.log(deadline)
/*var deadline = new Date(Date.parse(new Date()) + endDateOffer);*/
var c = new Clock(deadline, function () {
          $("#showcountdown").hide();
        $("#showimagebefore").hide();
        $("#showtitlebefore").hide();
        $("#showyoutubebefore").hide();
        $("#showtextbefore").hide();
        $("#flipclock").hide();
        $("#showimageafter").show();
        $("#showtitleafter").show();
        $("#showtextafter").show();
        $("#showyoutubeafter").show();
});

if ((endDateOffer - currentDate) >= 0) { 
      $("#showcountdown").show();
      $("#showimagebefore").show();
      $("#showtitlebefore").show();
      $("#showtextbefore").show();
      $("#flipclock").show();
      $("#showyoutubebefore").show();
      $("#showimageafter").hide();
      $("#showtitleafter").hide();
      $("#showtextafter").hide();
      $("#showyoutubeafter").hide();
}
<?php endif ?>

<?php if($data->link->settings->countdown_enable == false): ?>
      $("#showimagebefore").show();
      $("#showtitlebefore").show();
      $("#showtextbefore").show();
      $("#showyoutubebefore").show();
<?php endif ?>
showcountdown.after(c.el)
//document.body.appendChild(c.el);

var clock = new Clock();
//document.body.appendChild(clock.el);
</script>