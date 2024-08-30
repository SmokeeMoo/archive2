<?php defined('ALTUMCODE') || die() ?>
<style type="text/css">
    .ymaps-map, ymaps-i-ua_js_yes {
        border-radius: 15px;
    }
</style>

    <script src="https://api-maps.yandex.ru/2.0/?load=package.standard&amp;lang=en_US&amp;apikey=<?= settings()->links->yamaps_api_key ?>" type="text/javascript"></script>
    <script src="https://yandex.st/jquery/1.6.4/jquery.min.js" type="text/javascript"></script>

<script>

ymaps.ready(init);
var poi=[[]];

        <?php foreach($data->link->settings->items as $key => $item): ?>
poi.push([<?= $item->lat ?>, <?= $item->lon ?>]);
        <?php endforeach ?>
        
        var lat = 40.71;
        var lot = -73.99;


function init () {
    var myMap = new ymaps.Map("map", {
            center: [40.71, -73.99],
            zoom: 11
            }, {
            maxZoom: 17
        })
         myMap.controls
       
        .add('zoomControl', { left: 5, top: 5 })
        <?php foreach($data->link->settings->items as $key => $item): ?>
         myPlacemark = new ymaps.Placemark([<?= $item->lat ?>, <?= $item->lon ?>], {
            
            balloonContentHeader: "<?= $item->title ?>",
            balloonContentBody: "<small><?= $item->content ?></small>",
            hintContent: "<?= $item->title ?>"
        });
        
 
  myMap.geoObjects.add(myPlacemark);
 <?php endforeach ?>
        myMap.setBounds(myMap.geoObjects.getBounds());
        
}





</script>


<div id="<?= 'biolink_block_id_' . $data->link->biolink_block_id ?>" data-biolink-block-id="<?= $data->link->biolink_block_id ?>" class="col-12 my-2">
  
 <div id="map" style="width:100%; height:270px;"></div>

</div>
