<?php defined('ALTUMCODE') || die() ?>
<link rel="stylesheet" href="/source/css/yatranslate.css">

<script>

const yatranslate = {
    lang: "<?= $data->link->settings->language ?>",
    langFirstVisit: "<?= $data->link->settings->language ?>",
};

document.addEventListener('DOMContentLoaded', function () {
    // Start
    yaTranslateInit();
})

function yaTranslateInit() {
    if (yatranslate.langFirstVisit && !localStorage.getItem('yt-widget')) {
        yaTranslateSetLang(yatranslate.langFirstVisit);
    }

    let script = document.createElement('script');
    script.src = `https://translate.yandex.net/website-widget/v1/widget.js?widgetId=ytWidget&pageLang=${yatranslate.lang}&widgetTheme=light&autoMode=false`;
    document.getElementsByTagName('head')[0].appendChild(script);
    let code = yaTranslateGetCode();
    yaTranslateHtmlHandler(code);
    yaTranslateEventHandler('click', '[data-ya-lang]', function (el) {
        yaTranslateSetLang(el.getAttribute('data-ya-lang'));
        window.location.reload();
    })
}

function yaTranslateSetLang(lang) {
    localStorage.setItem('yt-widget', JSON.stringify({
        "lang": lang,
        "active": true
    }));
}

function yaTranslateGetCode() {
    return (localStorage["yt-widget"] != undefined && JSON.parse(localStorage["yt-widget"]).lang != undefined) ? JSON.parse(localStorage["yt-widget"]).lang : yatranslate.lang;
}

function yaTranslateHtmlHandler(code) {
    document.querySelector('[data-lang-active]').innerHTML = `<img class="lang__img lang__img_select" src="/source/images/lang/lang__${code}.png" alt="${code}">`;
    document.querySelector(`[data-ya-lang="${code}"]`).remove();
}

function yaTranslateEventHandler(event, selector, handler) {
    document.addEventListener(event, function (e) {
        let el = e.target.closest(selector);
        if (el) handler(el);
    });
}
    
</script>


<div id="<?= 'biolink_block_id_' . $data->link->biolink_block_id ?>" data-biolink-block-id="<?= $data->link->biolink_block_id ?>" class="col-12 my-2">
    
    <div class="lang lang_fixed">
   <div id="ytWidget" style="display: none;"></div>
   <div class="lang__link lang__link_select" data-lang-active="">
       
   </div>
   <div class="lang__list" data-lang-list="">
       <a class="lang__link lang__link_sub" data-ya-lang="ru">
           <img class="lang__img" src="./images/lang/lang__ru.png" alt="ru">
       </a>
       <a class="lang__link lang__link_sub" data-ya-lang="en">
           <img class="lang__img" src="./images/lang/lang__en.png" alt="en">
       </a>
       <a class="lang__link lang__link_sub" data-ya-lang="de">
           <img class="lang__img" src="./images/lang/lang__de.png" alt="de">
       </a>
       <a class="lang__link lang__link_sub" data-ya-lang="zh">
           <img class="lang__img" src="./images/lang/lang__zh.png" alt="zh">
       </a>
       <a class="lang__link lang__link_sub" data-ya-lang="fr">
           <img class="lang__img" src="./images/lang/lang__fr.png" alt="fr">
       </a>
   </div>
</div>
    
<div class="lang lang_fixed">
   <div id="ytWidget" style="display: none;"></div>
   <div class="lang__link lang__link_select" data-lang-active="">
       <img class="lang__img lang__img_select">
   </div>
   <div class="lang__list" data-lang-list="">
       <?php foreach($data->link->settings->items as $key => $item): ?>
    <?php 
    if($item->lang == 'en') {
        $lang_display = "<a class=\"lang__link lang__link_sub\" data-ya-lang=\"en\"><img class=\"lang__img\" src=\"/source/images/lang/lang__en.png\" alt=\"en\"></a>";}
    if($item->lang == 'es') {
        $lang_display = "<a class=\"lang__link lang__link_sub\" data-ya-lang=\"es\"><img class=\"lang__img\" src=\"/source/images/lang/lang__es.png\" alt=\"es\"></a>";}
    if($item->lang == 'zh') {
        $lang_display = "<a class=\"lang__link lang__link_sub\" data-ya-lang=\"zh\"><img class=\"lang__img\" src=\"/source/images/lang/lang__zh.png\" alt=\"zh\"></a>";}
    if($item->lang == 'hi') {
        $lang_display = "<a class=\"lang__link lang__link_sub\" data-ya-lang=\"hi\"><img class=\"lang__img\" src=\"/source/images/lang/lang__hi.png\" alt=\"hi\"></a>";}
    if($item->lang == 'ar') {
        $lang_display = "<a class=\"lang__link lang__link_sub\" data-ya-lang=\"ar\"><img class=\"lang__img\" src=\"/source/images/lang/lang__ar.png\" alt=\"ar\"></a>";}
    if($item->lang == 'bn') {
        $lang_display = "<a class=\"lang__link lang__link_sub\" data-ya-lang=\"bn\"><img class=\"lang__img\" src=\"/source/images/lang/lang__bn.png\" alt=\"bn\"></a>";}
    if($item->lang == 'pt') {
        $lang_display = "<a class=\"lang__link lang__link_sub\" data-ya-lang=\"pt\"><img class=\"lang__img\" src=\"/source/images/lang/lang__pt.png\" alt=\"pt\"></a>";}
    if($item->lang == 'ja') {
        $lang_display = "<a class=\"lang__link lang__link_sub\" data-ya-lang=\"ja\"><img class=\"lang__img\" src=\"/source/images/lang/lang__ja.png\" alt=\"ja\"></a>";}
    if($item->lang == 'ms') {
        $lang_display = "<a class=\"lang__link lang__link_sub\" data-ya-lang=\"ms\"><img class=\"lang__img\" src=\"/source/images/lang/lang__ms.png\" alt=\"ms\"></a>";}
    if($item->lang == 'tr') {
        $lang_display = "<a class=\"lang__link lang__link_sub\" data-ya-lang=\"tr\"><img class=\"lang__img\" src=\"/source/images/lang/lang__tr.png\" alt=\"tr\"></a>";}
    if($item->lang == 'ko') {
        $lang_display = "<a class=\"lang__link lang__link_sub\" data-ya-lang=\"ko\"><img class=\"lang__img\" src=\"/source/images/lang/lang__ko.png\" alt=\"ko\"></a>";}
    if($item->lang == 'fr') {
        $lang_display = "<a class=\"lang__link lang__link_sub\" data-ya-lang=\"fr\"><img class=\"lang__img\" src=\"/source/images/lang/lang__fr.png\" alt=\"fr\"></a>";}
    if($item->lang == 'de') {
        $lang_display = "<a class=\"lang__link lang__link_sub\" data-ya-lang=\"de\"><img class=\"lang__img\" src=\"/source/images/lang/lang__de.png\" alt=\"de\"></a>";}
    if($item->lang == 'it') {
        $lang_display = "<a class=\"lang__link lang__link_sub\" data-ya-lang=\"it\"><img class=\"lang__img\" src=\"/source/images/lang/lang__it.png\" alt=\"it\"></a>";}
    if($item->lang == 'uk') {
        $lang_display = "<a class=\"lang__link lang__link_sub\" data-ya-lang=\"uk\"><img class=\"lang__img\" src=\"/source/images/lang/lang__uk.png\" alt=\"uk\"></a>";}
    if($item->lang == 'ru') {
        $lang_display = "<a class=\"lang__link lang__link_sub\" data-ya-lang=\"ru\"><img class=\"lang__img\" src=\"/source/images/lang/lang__ru.png\" alt=\"ru\"></a>";}
      
     ?>
    <?= $lang_display ?>
    <?php endforeach ?>
   </div>
</div>

    
 


</div>
