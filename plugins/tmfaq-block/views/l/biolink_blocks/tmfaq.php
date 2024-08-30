<?php defined('ALTUMCODE') || die() ?>

<style>
.container_faq {
  background-color: <?= $data->link->settings->background_color ?>;
  color: <?= $data->link->settings->text_color ?>;
  border-radius: 5px;
  //box-shadow: 0 5px 10px 0 rgb(0,0,0,0.25);
  margin: 10px 0;
}

.question {
  font-size: 100%;
  font-weight: bold;
line-height: 1.1;
  padding: 20px 60px 20px 20px;
  position: relative;
  display: flex;
  align-items: center;
  cursor: pointer;
text-align: left;
}

.question::after {
  content: "\002B";
  font-size: 2.2rem;
  position: absolute;
  right: 20px;
  transition: 0.2s;
//margin-bottom: 10px;
}

.question.active::after {
  transform: rotate(45deg);
}

.answercont {
  max-height: 0;
  overflow: hidden;
  transition: 0.3s;
}

.answer {
  padding: 0 20px 20px;
  text-align: left;
    font-size: 100%;
    line-height: 1.3;
}

.question.active + .answercont {
}

@media screen and (max-width: 790px){
  html {
    font-size: 14px;
  }
  .wrapper {
  width: 80%;
}
}
</style>


<div id="<?= 'biolink_block_id_' . $data->link->biolink_block_id ?>" data-biolink-block-id="<?= $data->link->biolink_block_id ?>" class="col-12 my-2">

 <?php foreach($data->link->settings->items as $key => $item): ?>
<div class="container_faq">
    <div class="question">
      <?= $item->title ?>
    </div>
    <div class="answercont">
      <div class="answer">
        <?= $item->content ?>
      </div>
    </div>
  </div>
  <?php endforeach ?>
    
 <script>
let question = document.querySelectorAll(".question");

question.forEach((question) => {
  question.addEventListener("click", (event) => {
    const active = document.querySelector(".question.active");
    if (active && active !== question) {
      active.classList.toggle("active");
      active.nextElementSibling.style.maxHeight = 0;
    }
    question.classList.toggle("active");
    const answer = question.nextElementSibling;
    if (question.classList.contains("active")) {
      answer.style.maxHeight = answer.scrollHeight + "px";
    } else {
      answer.style.maxHeight = 0;
    }
  });
});
</script>


</div>
