<?php defined('ALTUMCODE') || die() ?>
<script src='https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js '></script>
<script src="/source/js/wawidget.js"></script>

<style>

/* CSS Multiple Whatsapp Chat */

#whatsapp-chat {
  position: fixed;
  width: 350px;
  border-radius: 10px;
  box-shadow: 0 1px 5px rgba(32, 33, 36, 0.28);
  bottom: 70px;
  left: 20px;
  overflow: hidden;
  z-index: 99;
  animation-name: showchat;
  animation-duration: 0.5s;
  transform: scale(1);
}

a.blantershow-chat {
  /*   background: #009688; */
  background: #fff;
  position: fixed;
  display: flex;
  z-index: 98;
  bottom: 20px;
  left: 20px;
  padding: 7px 15px;
  border-radius: 30px;
  box-shadow: 0 1px 5px rgba(32, 33, 36, 0.28);
    color: #444;
  text-decoration: none;
}

a.blantershow-chat svg {
  transform: scale(1);
  margin: 0 7px 0 0;
}

.whatsapp-chat-header {
  background: <?= $data->link->settings->background_color ?>;
  color: <?= $data->link->settings->text_color ?>;
  padding: 10px;
}
.whatsapp-chat-header h3 {
    margin: 0;
  }
  
  
  .whatsapp-chat-name {
 <?php if ($data->link->settings->image) {
            ?>      
    margin-top:-25px
    <?php } ?>
}
 .whatsapp-chat-name .chat-name{
font-weight: 600;
}
.whatsapp-chat-avatar {
  margin-bottom:20px;
}
.whatsapp-chat-avatar:after {
      content: "";
    bottom: 0px;
    right: 0px;
    width: 12px;
    height: 12px;
    box-sizing: border-box;
    background-color: #4ad504;
    display: block;
    position: relative;
    z-index: 1;
    border-radius: 50%;
    border: 2px solid #095e54;
    left: 40px;
    top: 38px;
  }
  
.whatsapp-chat-avatar img {
    border-radius: 100%;
    width: 50px;
    float: left;
    margin: 0 10px 0 0;
}


.info-chat span {
  display: block;
}
#get-label,
span.chat-label {
  color: #888;
}
#get-nama,
span.chat-nama {
  color: #222;
}
#get-label,
#get-nama {
  color: #fff;
}
span.my-number {
  display: none;
}
 .blanter-msg {
  background-color: #fff;
  border-top: 1px solid #ddd;
} 
.send-msg{
  background-color: #fff;
}
textarea#chat-input {
    border: none;
    width: 100%;
    height: 40px;
    outline: none;
    resize: none;
    padding: 10px;
}

a#send-it {
  width: 40px;
  padding: 10px;
  background:#fff;
}
 a#send-it svg {
    fill:#a6a6a6;
    height: 20px;
    width: 20px;
}

.first-msg {
  background: transparent;
  padding: 30px;
  text-align: center;
}
.first-msg span {
    background: #e2e2e2;
    color: #333;
    border-radius: 10px;
    display: inline-block;
  }

.start-chat .blanter-msg {
  display: flex;
}
#get-number {
  display: none;
}
a.close-chat {
  position: absolute;
  top: 5px;
  right: 15px;
  color: #fff;
  font-size: 30px;
  text-decoration: none;
}

@keyframes ZpjSY{
  0% {
    background-color: rgb(182, 181, 186);
  }
  15% {
    background-color: rgb(17, 17, 17);
  }
  25% {
    background-color: rgb(182, 181, 186);
  }
}

@keyframes hPhMsj {
  15% {
    background-color: rgb(182, 181, 186);
  }
  25% {
    background-color: rgb(17, 17, 17);
  }
  35% {
    background-color: rgb(182, 181, 186);
  }
}

@keyframes iUMejp {
  25% {
    background-color: rgb(182, 181, 186);
  }
  35% {
    background-color: rgb(17, 17, 17);
  }
  45% {
    background-color: rgb(182, 181, 186);
  }
}


@keyframes showhide {
  from {
    transform: scale(0.5);
    opacity: 0;
  }
}
@keyframes showchat {
  from {
    transform: scale(0);
    opacity: 0;
  }
}
@media screen and (max-width: 480px) {
  #whatsapp-chat {
    width: auto;
    left: 5%;
    right: 5%;
  }
}
.hide {
  display: none;
  animation-name: showhide;
  animation-duration: 0.5s;
  transform: scale(1);
  opacity: 1;
}
.show {
  display: block;
  animation-name: showhide;
  animation-duration: 0.5s;
  transform: scale(1);
  opacity: 1;
}

.whatsapp-message-container {
  display: flex;
  z-index: 1;
}

.whatsapp-message {
  padding: 7px 14px 6px;
  background-color: rgb(255, 255, 255);
  border-radius: 0px 8px 8px;
  position: relative;
  transition: all 0.3s ease 0s;
  opacity: 0;
  transform-origin: center top 0px;
  z-index: 2;
  box-shadow: rgba(0, 0, 0, 0.13) 0px 1px 0.5px;
  margin-top: 4px;
  margin-left: -54px;
  max-width: calc(100% - 66px);
}

.whatsapp-chat-body {
  padding: 20px 20px 20px 10px;
  background-color: rgb(230, 221, 212);
  position: relative;
}
.whatsapp-chat-body:before {
    display: block;
    position: absolute;
    content: "";
    left: 0px;
    top: 0px;
    height: 100%;
    width: 100%;
    z-index: 0;
    opacity: 0.08;
    background-image: url("/source/images/whatsapp.webp");
  }
}

.dAbFpq {
  display: flex;
  z-index: 1;
}

.eJJEeC {
  background-color: rgb(255, 255, 255);
  width: 52.5px;
  height: 32px;
  border-radius: 16px;
  display: flex;
  -moz-box-pack: center;
  justify-content: center;
  -moz-box-align: center;
  align-items: center;
  margin-left: 10px;
  opacity: 0;
  transition: all 0.1s ease 0s;
  z-index: 1;
  box-shadow: rgba(0, 0, 0, 0.13) 0px 1px 0.5px;
}

.hFENyl {
    position: relative;
    display: flex;
}

.ixsrax {
    height: 5px;
    width: 5px;
    margin: 0px 2px;
    border-radius: 50%;
    display: inline-block;
    position: relative;
    animation-duration: 1.2s;
    animation-iteration-count: infinite;
    animation-timing-function: linear;
    top: 0px;
    background-color: rgb(158, 157, 162);
    animation-name: ZpjSY;
}

.dRvxoz {

    height: 5px;
    width: 5px;
    margin: 0px 2px;
    background-color: rgb(182, 181, 186);
    border-radius: 50%;
    display: inline-block;
    position: relative;
    animation-duration: 1.2s;
    animation-iteration-count: infinite;
    animation-timing-function: linear;
    top: 0px;
    animation-name: hPhMsj;
}

.kAZgZq {
    padding: 10px;
    background-color: rgb(255, 255, 255);
    border-radius: 0px 8px 8px;
    position: relative;
    transition: all 0.3s ease 0s;
    opacity: 0;
    transform-origin: center top 0px;
    z-index: 2;
    box-shadow: rgba(0, 0, 0, 0.13) 0px 1px 0.5px;
    max-width: calc(100% - 66px);
}
.kAZgZq:before {
    position: absolute;
    background-image: url("data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAABgAAAAmCAMAAADp2asXAAAABGdBTUEAALGPC/xhBQAAAAFzUkdCAK7OHOkAAACQUExURUxpccPDw9ra2m9vbwAAAAAAADExMf///wAAABoaGk9PT7q6uqurqwsLCycnJz4+PtDQ0JycnIyMjPf3915eXvz8/E9PT/39/RMTE4CAgAAAAJqamv////////r6+u/v7yUlJeXl5f///5ycnOXl5XNzc/Hx8f///xUVFf///+zs7P///+bm5gAAAM7Ozv///2fVensAAAAvdFJOUwCow1cBCCnqAhNAnY0WIDW2f2/hSeo99g1lBYT87vDXG8/6d8oL4sgM5szrkgl660OiZwAAAHRJREFUKM/ty7cSggAABNFVUQFzwizmjPz/39k4YuFWtm55bw7eHR6ny63+alnswT3/rIDzUSC7CrAziPYCJCsB+gbVkgDtVIDh+DsE9OTBpCtAbSBAZSEQNgWIygJ0RgJMDWYNAdYbAeKtAHODlkHIv997AkLqIVOXVU84AAAAAElFTkSuQmCC");
    background-position: 50% 50%;
    background-repeat: no-repeat;
    background-size: contain;
    content: "";
    top: 0px;
    left: -12px;
    width: 12px;
    height: 19px;
}

.cqCDVm {
    text-align: right;
    color: rgba(17, 17, 17, 0.5);
    display: none;
}



</style>


<div id="<?= 'biolink_block_id_' . $data->link->biolink_block_id ?>" data-biolink-block-id="<?= $data->link->biolink_block_id ?>" class="col-12 my-2">



<div id='whatsapp-chat' class='hide'>
  <div class='whatsapp-chat-header'>
      <?php if ($data->link->settings->image) {
            ?>  
      <div class='whatsapp-chat-avatar'>
        <img src="<?= $data->link->settings->image ? UPLOADS_FULL_URL . 'block_images/' . $data->link->settings->image : null ?>" alt="Logo" style="aspect-ratio: 1;" />
      </div>
      <?php   } ?>
    <div class="whatsapp-chat-name"><span class="chat-name"><?= $data->link->settings->window_title ?></span><br><small><?= $data->link->settings->description_window ?></div></small>
  </div>
  
  <div class='start-chat'>
    <div pattern="/source/images/whatsapp.webp" class="WhatsappChat__Component-sc-1wqac52-0 whatsapp-chat-body">
      <div class="WhatsappChat__MessageContainer-sc-1wqac52-1 dAbFpq">
        <div style="opacity: 1;" class="WhatsappChat__Message-sc-1wqac52-4 kAZgZq">
          <div class="WhatsappChat__Text-sc-1wqac52-2 iSpIQi"><?= $data->link->settings->greeting ?></div>
          <div class="WhatsappChat__Time-sc-1wqac52-5 cqCDVm"><small>1:40</small></div>
        </div>
      </div>
    </div>

    <div class='blanter-msg'>
      <textarea id='chat-input' placeholder='<?= $data->link->settings->message_placeholder ?>' maxlength='120' row='1'></textarea>
      <a href='javascript:void;' class='send-msg' id='send-it'><svg viewBox="0 0 448 448"><path d="M.213 32L0 181.333 320 224 0 266.667.213 416 448 224z"/></svg></a>

    </div>
  </div>
  <div id='get-number'><span class='my-number'><?= $data->link->settings->phone ?></span></div>
  <a class='close-chat' href='javascript:void'>×</a>
</div>
<a class='blantershow-chat' href='javascript:void' title='Show Chat'><svg width="20" viewBox="0 0 24 24"><defs/><path fill="#eceff1" d="M20.5 3.4A12.1 12.1 0 0012 0 12 12 0 001.7 17.8L0 24l6.3-1.7c2.8 1.5 5 1.4 5.8 1.5a12 12 0 008.4-20.3z"/><path fill="#4caf50" d="M12 21.8c-3.1 0-5.2-1.6-5.4-1.6l-3.7 1 1-3.7-.3-.4A9.9 9.9 0 012.1 12a10 10 0 0117-7 9.9 9.9 0 01-7 16.9z"/><path fill="#fafafa" d="M17.5 14.3c-.3 0-1.8-.8-2-.9-.7-.2-.5 0-1.7 1.3-.1.2-.3.2-.6.1s-1.3-.5-2.4-1.5a9 9 0 01-1.7-2c-.3-.6.4-.6 1-1.7l-.1-.5-1-2.2c-.2-.6-.4-.5-.6-.5-.6 0-1 0-1.4.3-1.6 1.8-1.2 3.6.2 5.6 2.7 3.5 4.2 4.2 6.8 5 .7.3 1.4.3 1.9.2.6 0 1.7-.7 2-1.4.3-.7.3-1.3.2-1.4-.1-.2-.3-.3-.6-.4z"/></svg> <?= $data->link->settings->text ?></a>


</div>
