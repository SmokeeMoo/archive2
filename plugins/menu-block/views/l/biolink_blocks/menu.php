<?php defined('ALTUMCODE') || die() ?>

<style>
:root {
    --theme-color: <?= $data->link->settings->text_color ?>;
    --bg-color: <?= $data->link->settings->background_color ?>;
}


@keyframes checked-anim {
    0% {
        width: 10%;
        height: 10%;
    }

    100% {
        width: 100%;
        height: 100%;
        border-radius: 0;
    }

}


@keyframes not-checked-anim {

    0% {
        width: 100%;
        height: 100%;
    }
}


#menu li, #menu a {
    color: #fff;
    font-weight: 700;
    line-height: 1.1;
    text-decoration: none;
    text-transform: none;
    list-style: none;
    display: none;
    outline: 0;
}

#menu li {
    margin: 5px 0;
    max-width: 320px;
    width: 320px;
}

#menu a {
    display: block;
    padding: 10px 15px;
    border-radius: 3px;
    background-color: var(--theme-color);
    border: 3px solid var(--theme-color);
}


#menu a:hover,
#menu a:focus,
#menu a:active

{
    display: block;
    color: var(--theme-color);
    background-color: transparent;
    transition: all .3s;
}

#trigger, #burger, #burger:before, #burger:after {
    position: fixed; 
    top: 20px; 
    right: 20px;
    background: var(--theme-color);
    width: 30px;
    height: 5px;
    cursor: pointer;
    z-index: 999;

}


#trigger {
    height: 25px;
    background: none;
}


#burger:before {
    content: " ";
    top: 30px;
    right: 20px;
}

#burger:after {
    content: " ";
    top: 40px;
    right: 20px;
}


#menu-toggle:checked + #trigger + #burger {
    top: 35px;
    transform: rotate(-180deg);
    transition: transform .2s ease;

}


#menu-toggle:checked + #trigger + #burger:before {
    width: 20px;
    top: 3px;
    right: 14px;
    transform: rotate(-145deg) translateX(-5px);
    transition: transform .2s ease;
}

#menu-toggle:checked + #trigger + #burger:after {
    width: 20px;
    top: -3px;
    right: 14px;
    transform: rotate(145deg) translateX(-5px);
    transition: transform .2s ease;
}

#menu {
    position: fixed; 
    top: 0; 
    right: 0;
    z-index: 998;
    margin: 0; padding: 0;
    width: 90px;
    height: 90px;
    background-color: var(--bg-color);
    border-bottom-left-radius: 100%;
    box-shadow: 0 2px 5px rgba(0,0,0,0.26);
    animation: not-checked-anim .2s both;
    transition: .2s;
    display: flex;
    flex-flow: column;
    align-items: center;
    justify-content: center;
}


#menu-toggle:checked + #trigger + #burger + #menu {
    animation: checked-anim .6s ease both;
}


#menu-toggle:checked + #trigger ~ #menu > li, #menu a {
    display: block;

}

[type="checkbox"]:not(:checked), [type="checkbox"]:checked {

    display: none;
}

</style>

<div id="<?= 'biolink_block_id_' . $data->link->biolink_block_id ?>" data-biolink-block-id="<?= $data->link->biolink_block_id ?>" class="col-12 my-2">
<!-- begin burger -->

<input type="checkbox" id="menu-toggle"/>

<label id="trigger" for="menu-toggle"></label>

<label id="burger" for="menu-toggle"></label>

<!-- end burger -->


<!-- begin menu -->

<ul id="menu">
    
    <?php foreach($data->link->settings->items as $key => $item): ?>

<li><a href="<?= $item->content ?>"><?= $item->title ?></a></li>

<?php endforeach ?>


</ul>

<!-- end menu -->


</div>
