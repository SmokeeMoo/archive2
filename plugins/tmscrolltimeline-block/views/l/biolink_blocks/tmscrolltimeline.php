<?php defined('ALTUMCODE') || die() ?>


<style>
body{
 background-color: transparent;
}

.mt-70{
     margin-top: 10px;
}

.mb-70{
     margin-bottom: 10px;
}

.card {
    box-shadow: 0 0.46875rem 2.1875rem rgba(4,9,20,0.03), 0 0.9375rem 1.40625rem rgba(4,9,20,0.03), 0 0.25rem 0.53125rem rgba(4,9,20,0.05), 0 0.125rem 0.1875rem rgba(4,9,20,0.03);
    border-width: 0;
    transition: all .2s;
}

.card {
    position: relative;
    display: flex;
    flex-direction: column;
    min-width: 0;
    word-wrap: break-word;
    background-clip: border-box;
    border: 0px solid rgba(26,54,126,0.125);
    background-color: transparent;
}

.card-body {
    flex: 1 1 auto;
    padding: 1.25rem;
    color: <?= $data->link->settings->text_color ?>;
    background: <?= $data->link->settings->background_color ?>;
    border-radius: 15px;
}

.scroll-area {
    overflow-x: hidden;
    height: 400px;
}


.vertical-timeline {
    width: 100%;
    position: relative;
    padding: 1.5rem 0 1rem;
}

.vertical-timeline::before {
    content: '';
    position: absolute;
    top: 0;
    left: 67px;
    height: 100%;
    width: 4px;
    background: #e9ecef;
    border-radius: .25rem;
}

.vertical-timeline-element {
    position: relative;
    margin: 0 0 1rem;
}

.vertical-timeline--animate .vertical-timeline-element-icon.bounce-in {
    visibility: visible;
    animation: cd-bounce-1 .8s;
}
.vertical-timeline-element-icon {
    position: absolute;
    top: 0;
    left: 60px;
}

.vertical-timeline-element-icon .badge-dot-xl {
    box-shadow: 0 0 0 5px #fff;
}

.badge-dot-xl {
    width: 18px;
    height: 18px;
    position: relative;
}
.badge:empty {
    display: none;
}


.badge-dot-xl::before {
    content: '';
    width: 10px;
    height: 10px;
    border-radius: .25rem;
    position: absolute;
    left: 50%;
    top: 50%;
    margin: -5px 0 0 -5px;
    background: #fff;
}

.vertical-timeline-element-content {
    position: relative;
    margin-left: 90px;
    font-size: .8rem;
}

.vertical-timeline-element-content .timeline-title {
    font-size: .8rem;
    text-transform: uppercase;
    margin: 0 0 .5rem;
    padding: 2px 0 0;
    font-weight: bold;
}

.vertical-timeline-element-content .vertical-timeline-element-date {
    display: block;
    position: absolute;
    left: -90px;
    top: 0;
    padding-right: 10px;
    text-align: right;
    color: #7e7e7e;
    font-size: 80%;
    font-weight: bold;
    white-space: nowrap;
}

.vertical-timeline-element-content:after {
    content: "";
    display: table;
    clear: both;
}

h5 {
    color: #7e7e7e;
}
</style>

<div id="<?= 'biolink_block_id_' . $data->link->biolink_block_id ?>" data-biolink-block-id="<?= $data->link->biolink_block_id ?>" class="col-12 my-2">



  <?= $item->title ?>



<div class="row d-flex justify-content-center mt-70 mb-70">

          <div class="col-md-12">

            <div class="main-card mb-3 card">
                                        <div class="card-body">
                                            <h5 class="card-title"><?= $data->link->settings->title_block ?></h5>
                                            <div class="scroll-area">
                                            <div class="vertical-timeline vertical-timeline--animate vertical-timeline--one-column">
                                               <?php foreach($data->link->settings->items as $key => $item): ?> 
                                                <div class="vertical-timeline-item vertical-timeline-element">
                                                    <div>
                                                        <span class="vertical-timeline-element-icon bounce-in">
                                                            <i class="badge badge-dot badge-dot-xl badge-<?= $item->icon ?>"> </i>
                                                        </span>
                                                        <div class="vertical-timeline-element-content bounce-in">
                                                            <h4 class="timeline-title"><?= $item->title ?></h4>
                                                            <p><?= $item->content ?></p>
                                                            <span class="vertical-timeline-element-date"><?= $item->date ?></span>
                                                        </div>
                                                    </div>
                                                </div>
                                        <?php endforeach ?>       
     
                                                
                                            </div>
                                         </div>
                                        </div>
                                    </div>        
                                    </div></div> 
      


</div>
