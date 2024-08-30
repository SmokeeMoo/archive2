<?php defined('ALTUMCODE') || die() ?>

<style>
.tmtimeline main {
    padding: 0 15px;
}
.tmtimeline p {
  font-size: 100%;
  //line-height: 1.75em;
  border-top: 3px solid;
  -o-border-image: linear-gradient(to right, #743ad5 0%, #d53a9d 100%);
     border-image: linear-gradient(to right, #743ad5 0%, #d53a9d 100%);
  border-image-slice: 1;
  border-width: 3px;
  margin: 0;
  padding: 30px;
  counter-increment: section;
  position: relative;
  color: <?= $data->link->settings->text_color ?>;
text-align: left;
}
.tmtimeline p:before {
  content: counter(section);
  position: absolute;
  border-radius: 50%;
  padding: 7px;
  height: 2em;
  width: 2em;
  background-color: #34435E;
  text-align: center;
  line-height: 1.25em;
  color: #ffffff;
  font-size: 1em;
}

.tmtimeline p:nth-child(odd) {
  border-right: 3px solid;
  padding-left: 0;
}
.tmtimeline p:nth-child(odd):before {
  left: 100%;
  margin-left: -15px;
}

.tmtimeline p:nth-child(even) {
  border-left: 3px solid;
  padding-right: 0;
}
.tmtimeline p:nth-child(even):before {
  right: 100%;
  margin-right: -15px;
}

.tmtimeline p:first-child {
  border-top: 0;
  border-top-right-radius: 0;
  border-top-left-radius: 0;
}

.tmtimeline p:last-child {
  border-bottom-right-radius: 0;
  border-bottom-left-radius: 0;
}
</style>

<div id="<?= 'biolink_block_id_' . $data->link->biolink_block_id ?>" data-biolink-block-id="<?= $data->link->biolink_block_id ?>" class="col-12 my-2">
<main class="tmtimeline">
    <?php foreach($data->link->settings->items as $key => $item): ?>
  <p class="tmtimeline"><?= $item->title ?></p>
<?php endforeach ?>
</main>


</div>
