<?php defined('ALTUMCODE') || die() ?>

<div id="scrollbar-indicator" class="scrollbar-indicator"></div>
<style>
.scrollbar-indicator {
  position: fixed;
  top: 0;
  right: 0;
  bottom: 0;
  width: 0.8vw;
  // border-radius: 8px;
  background-image: linear-gradient(to top, #fcb045, #fd1f1d, #833ab4);
  transition: 0.1s;
  will-change: height;
  pointer-events: none;
}
</style>
<script>
const scrollbarIndicator = document.querySelector('#scrollbar-indicator');

function updateScrollbarIndicatorPosition (evt) {
  const scrollPos = pageYOffset;
  const clientHeight = document.documentElement.clientHeight;
  const documentHeight = document.documentElement.scrollHeight;
  const scrollPercent = (scrollPos / (documentHeight - clientHeight)) * 100;
  
  scrollbarIndicator.style.height = `${scrollPercent}%`;
}

updateScrollbarIndicatorPosition();

window.addEventListener('scroll', function () {
  updateScrollbarIndicatorPosition();
});

window.addEventListener('resize', function () {
  updateScrollbarIndicatorPosition();
});

</script>


<div id="<?= 'biolink_block_id_' . $data->link->biolink_block_id ?>" data-biolink-block-id="<?= $data->link->biolink_block_id ?>" class="col-12 my-2">



</div>
