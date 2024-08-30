<?php defined('ALTUMCODE') || die() ?>

<style>

.wrapper {
  max-width: 100%;
  margin: 0 auto;
}

.scroll-cards {
  counter-reset: card;
  position: relative;
  display: block;
  padding-bottom: 15vh;
}

.scroll-cards > .scroll-cards__item + .scroll-cards__item {
  margin-top: 1vh;
}

.scroll-cards h1 {
  position: sticky;
  //top: 2rem;
  font-size: 2em;
  margin: 0 0 0.5em;
}

.scroll-cards__item {
  --offset: 0.5em;
  color: <?= $data->link->settings->text_color ?>;
  position: sticky;
  top: max(1vh, 1em);
  padding: 1.5em 1.5em;
  min-height: 15em;
  background: <?= $data->link->settings->background_color ?>;
  box-shadow: 0 2px 40px rgba(0, 0, 0, 0.1);
  width: calc(100% - 5 * var(--offset));
border-radius: 5px;
}

h2.tmscrollcards {
  font-size: 120%;
  text-transform: uppercase;
  margin: 0;
padding-bottom: 10px;
color: <?= $data->link->settings->text_color ?>;
}

p.tmscrollcards {
  font-size: 100%;
  line-height: 1.3;
  color: <?= $data->link->settings->text_color ?>;
}


.scroll-cards__item:nth-of-type(0) {
  transform: translate(calc((0 - 1) * var(--offset)), calc((0 - 1) * var(--offset)));
}
.scroll-cards__item:nth-of-type(1) {
  transform: translate(calc((1 - 1) * var(--offset)), calc((1 - 1) * var(--offset)));
}
.scroll-cards__item:nth-of-type(2) {
  transform: translate(calc((2 - 1) * var(--offset)), calc((2 - 1) * var(--offset)));
}
.scroll-cards__item:nth-of-type(3) {
  transform: translate(calc((3 - 1) * var(--offset)), calc((3 - 1) * var(--offset)));
}
.scroll-cards__item:nth-of-type(4) {
  transform: translate(calc((4 - 1) * var(--offset)), calc((4 - 1) * var(--offset)));
}
.scroll-cards__item:nth-of-type(5) {
  transform: translate(calc((5 - 1) * var(--offset)), calc((5 - 1) * var(--offset)));
}

.scroll-cards__item:nth-of-type(6) {
  transform: translate(calc((4 - 1) * var(--offset)), calc((6 - 1) * var(--offset)));
}

.scroll-cards__item:nth-of-type(7) {
  transform: translate(calc((3 - 1) * var(--offset)), calc((7 - 1) * var(--offset)));
}
.scroll-cards__item:nth-of-type(8) {
  transform: translate(calc((2 - 1) * var(--offset)), calc((8 - 1) * var(--offset)));
}

.scroll-cards__item:nth-of-type(9) {
  transform: translate(calc((1 - 1) * var(--offset)), calc((9 - 1) * var(--offset)));
}

.scroll-cards__item:nth-of-type(10) {
  transform: translate(calc((2 - 1) * var(--offset)), calc((10 - 1) * var(--offset)));
}

.scroll-cards__item:nth-of-type(11) {
  transform: translate(calc((3 - 1) * var(--offset)), calc((11 - 1) * var(--offset)));
}

.scroll-cards__item:nth-of-type(12) {
  transform: translate(calc((4 - 1) * var(--offset)), calc((12 - 1) * var(--offset)));
}

.scroll-cards__item:nth-of-type(13) {
  transform: translate(calc((5 - 1) * var(--offset)), calc((13 - 1) * var(--offset)));
}

.scroll-cards__item:nth-of-type(14) {
  transform: translate(calc((4 - 1) * var(--offset)), calc((14 - 1) * var(--offset)));
}

.scroll-cards__item:nth-of-type(15) {
  transform: translate(calc((3 - 1) * var(--offset)), calc((15 - 1) * var(--offset)));
}

.scroll-cards__item:nth-of-type(16) {
  transform: translate(calc((2 - 1) * var(--offset)), calc((16 - 1) * var(--offset)));
}

.scroll-cards__item:nth-of-type(17) {
  transform: translate(calc((1 - 1) * var(--offset)), calc((17 - 1) * var(--offset)));
}
@media screen and (min-width: 37em) {
 
  .scroll-cards__item {
    --offset: 1em;
    padding-left: 5em;
  }
  .scroll-cards__item:before {
    counter-increment: card;
    content: "0" counter(card);
    display: flex;
    align-items: center;
    justify-content: center;
    width: 2.75em;
    height: 2.75em;
    background: #f09;
    color: <?= $data->link->settings->background_color ?>;
    text-align: center;
    border-radius: 50%;
    position: absolute;
    left: 1.25em;
    top: 1.25em;
    font-weight: bold;
  }
}

@media screen and (min-width: 62em) {

  .scroll-cards__item {
    --offset: 0.75em;
    max-width: 42em;
  }
}
</style>


<div id="<?= 'biolink_block_id_' . $data->link->biolink_block_id ?>" data-biolink-block-id="<?= $data->link->biolink_block_id ?>" class="col-12 my-2">



<div class="wrapper">
  <div class="scroll-cards">
      <?php foreach($data->link->settings->items as $key => $item): ?>
    <article class="scroll-cards__item" aria-label="Wie - 1">
      <h2 class="tmscrollcards"><?= $item->title ?></h2>
      <p class="tmscrollcards"><?= $item->content ?></p>
    </article>
    <?php endforeach ?>
   
  </div>
</div>


</div>
