<?php defined('ALTUMCODE') || die() ?>
<style type="text/css">
            * {
box-sizing: border-box;
}

#overlay_modalblock {
    position: fixed;
    top: 0;
    left: 0;
    display: none;
    width: 100%;
    height: 100%;
    background: rgba(0, 0, 0, 0.75);
    z-index: 999;
    -webkit-animation: fade .6s;
    -moz-animation: fade .6s;
    animation: fade .6s;
    overflow: auto;
}

.popup_modalblock {
    top: 25%;
    left: 0;
    right: 0;       
    font-family: inherit;
    font-size: 16px;
    margin: auto;
    width: 85%;
    min-width: 320px;
    max-width: 600px;
    position: absolute;
    padding: 15px 20px;
    border: 1px solid #383838;
    background: <?= $data->link->settings->background_color ?>;
    z-index: 1000;
    -webkit-border-radius: 4px;
    -moz-border-radius: 4px;
    -ms-border-radius: 4px;
    border-radius: 4px;
    font: 14px/18px 'Tahoma', Arial, sans-serif;
    -webkit-box-shadow: 0 15px 20px rgba(0,0,0,.22),0 19px 60px rgba(0,0,0,.3);
    -moz-box-shadow: 0 15px 20px rgba(0,0,0,.22),0 19px 60px rgba(0,0,0,.3);
    -ms-box-shadow: 0 15px 20px rgba(0,0,0,.22),0 19px 60px rgba(0,0,0,.3);
    box-shadow: 0 15px 20px rgba(0,0,0,.22),0 19px 60px rgba(0,0,0,.3);
    -webkit-animation: fade .6s;
    -moz-animation: fade .6s;
    animation: fade .6s;
}

.popup_modalblock h4, .popup_modalblock h4 {
    margin: 0 0 1rem 0;
    font-weight: 300;
    line-height: 1;
    color: <?= $data->link->settings->text_color ?>;
}

.close_modalblock {
    top: 10px;
    right: 10px;
    width: 24px;
    height: 24px;
    position: absolute;
    border: none;
    -webkit-border-radius: 50%;
    -moz-border-radius: 50%;
    -ms-border-radius: 50%;
    -o-border-radius: 50%;
    border-radius: 50%;
    background-color: rgb(133 0 0);
    -webkit-box-shadow: 0 2px 5px 0 rgba(0, 0, 0, 0.16), 0 2px 10px 0 rgba(0, 0, 0, 0.12);
    -moz-box-shadow: 0 2px 5px 0 rgba(0, 0, 0, 0.16), 0 2px 10px 0 rgba(0, 0, 0, 0.12);
    box-shadow: 0 2px 5px 0 rgba(0, 0, 0, 0.16), 0 2px 10px 0 rgba(0, 0, 0, 0.12);
    cursor: pointer;
    outline: none;

}
.close_modalblock:before {
    color: rgba(255, 255, 255, 0.9);
    content: "X";
    font-family:  Arial, Helvetica, sans-serif;
    font-size: 16px;
    font-weight: normal;
    text-decoration: none;
    text-shadow: 0 -1px rgba(0, 0, 0, 0.9);
    -webkit-transition: all 0.5s;
    -moz-transition: all 0.5s;
    transition: all 0.5s;
}

.close_modalblock:hover {
    background-color: rgba(252, 20, 0, 0.8);
}


@-moz-keyframes fade {
    from { opacity: 0; }
    to { opacity: 1 }
}
@-webkit-keyframes fade {
    from { opacity: 0; }
    to { opacity: 1 } 
}
@keyframes fade {
    from { opacity: 0; }
    to { opacity: 1 }
}
</style>

<div id="<?= 'biolink_block_id_' . $data->link->biolink_block_id ?>" data-biolink-block-id="<?= $data->link->biolink_block_id ?>" class="col-12 my-2">
     
        <div id="overlay_modalblock">
            <div class="popup_modalblock">
                <h4><?= nl2br($data->link->settings->title) ?></h4>
                <p style="color:<?= $data->link->settings->text_color ?>; font-size: 16px; text-align:left"><?= nl2br($data->link->settings->text) ?></p>
                <button class="close_modalblock" title="Close" onclick="document.getElementById('overlay_modalblock').style.display='none';"></button>
            </div>
        </div>
<script type="text/javascript">
	var delay_popup = 1000;	setTimeout("document.getElementById('overlay_modalblock').style.display='block'", delay_popup);
</script>
   
</div>

