<?php
/*
 * @copyright Copyright (c) 2023 AltumCode (https://altumcode.com/)
 *
 * This software is exclusively sold through https://altumcode.com/ by the AltumCode author.
 * Downloading this product from any other sources and running it without a proper license is illegal,
 *  except the official ones linked from https://altumcode.com/.
 */

$pro_blocks = \Altum\Plugin::is_active('pro-blocks') && file_exists(\Altum\Plugin::get('pro-blocks')->path . 'pro_blocks.php') ? include \Altum\Plugin::get('pro-blocks')->path . 'pro_blocks.php' : [];
$ultimate_blocks = \Altum\Plugin::is_active('ultimate-blocks') && file_exists(\Altum\Plugin::get('ultimate-blocks')->path . 'ultimate_blocks.php') ? include \Altum\Plugin::get('ultimate-blocks')->path . 'ultimate_blocks.php' : [];
$payment_blocks = \Altum\Plugin::is_active('payment-blocks') && file_exists(\Altum\Plugin::get('payment-blocks')->path . 'payment_blocks.php') ? include \Altum\Plugin::get('payment-blocks')->path . 'payment_blocks.php' : [];
$menu_block = \Altum\Plugin::is_active('menu-block') && file_exists(\Altum\Plugin::get('menu-block')->path . 'menu_block.php') ? include \Altum\Plugin::get('menu-block')->path . 'menu_block.php' : [];
$modal_block = \Altum\Plugin::is_active('modal-block') && file_exists(\Altum\Plugin::get('modal-block')->path . 'modal_block.php') ? include \Altum\Plugin::get('modal-block')->path . 'modal_block.php' : [];
$preloader_block = \Altum\Plugin::is_active('preloader-block') && file_exists(\Altum\Plugin::get('preloader-block')->path . 'preloader_block.php') ? include \Altum\Plugin::get('preloader-block')->path . 'preloader_block.php' : [];
$slider_block = \Altum\Plugin::is_active('slider-block') && file_exists(\Altum\Plugin::get('slider-block')->path . 'slider_block.php') ? include \Altum\Plugin::get('slider-block')->path . 'slider_block.php' : [];
$cardslider_block = \Altum\Plugin::is_active('cardslider-block') && file_exists(\Altum\Plugin::get('cardslider-block')->path . 'cardslider_block.php') ? include \Altum\Plugin::get('cardslider-block')->path . 'cardslider_block.php' : [];
$yamaps_block = \Altum\Plugin::is_active('yamaps-block') && file_exists(\Altum\Plugin::get('yamaps-block')->path . 'yamaps_block.php') ? include \Altum\Plugin::get('yamaps-block')->path . 'yamaps_block.php' : [];
$tmprice_block = \Altum\Plugin::is_active('tmprice-block') && file_exists(\Altum\Plugin::get('tmprice-block')->path . 'tmprice_block.php') ? include \Altum\Plugin::get('tmprice-block')->path . 'tmprice_block.php' : [];
$tmtimeline_block = \Altum\Plugin::is_active('tmtimeline-block') && file_exists(\Altum\Plugin::get('tmtimeline-block')->path . 'tmtimeline_block.php') ? include \Altum\Plugin::get('tmtimeline-block')->path . 'tmtimeline_block.php' : [];
$tmfaq_block = \Altum\Plugin::is_active('tmfaq-block') && file_exists(\Altum\Plugin::get('tmfaq-block')->path . 'tmfaq_block.php') ? include \Altum\Plugin::get('tmfaq-block')->path . 'tmfaq_block.php' : [];
$tmscrollindicator_block = \Altum\Plugin::is_active('tmscrollindicator-block') && file_exists(\Altum\Plugin::get('tmscrollindicator-block')->path . 'tmscrollindicator_block.php') ? include \Altum\Plugin::get('tmscrollindicator-block')->path . 'tmscrollindicator_block.php' : [];
$tmscrollcards_block = \Altum\Plugin::is_active('tmscrollcards-block') && file_exists(\Altum\Plugin::get('tmscrollcards-block')->path . 'tmscrollcards_block.php') ? include \Altum\Plugin::get('tmscrollcards-block')->path . 'tmscrollcards_block.php' : [];
$tmprogress_block = \Altum\Plugin::is_active('tmprogress-block') && file_exists(\Altum\Plugin::get('tmprogress-block')->path . 'tmprogress_block.php') ? include \Altum\Plugin::get('tmprogress-block')->path . 'tmprogress_block.php' : [];
$tmnotification_block = \Altum\Plugin::is_active('tmnotification-block') && file_exists(\Altum\Plugin::get('tmnotification-block')->path . 'tmnotification_block.php') ? include \Altum\Plugin::get('tmnotification-block')->path . 'tmnotification_block.php' : [];
$tmscrolltimeline_block = \Altum\Plugin::is_active('tmscrolltimeline-block') && file_exists(\Altum\Plugin::get('tmscrolltimeline-block')->path . 'tmscrolltimeline_block.php') ? include \Altum\Plugin::get('tmscrolltimeline-block')->path . 'tmscrolltimeline_block.php' : [];
$tmnewsfeed_block = \Altum\Plugin::is_active('tmnewsfeed-block') && file_exists(\Altum\Plugin::get('tmnewsfeed-block')->path . 'tmnewsfeed_block.php') ? include \Altum\Plugin::get('tmnewsfeed-block')->path . 'tmnewsfeed_block.php' : [];
$tmpiechart_block = \Altum\Plugin::is_active('tmpiechart-block') && file_exists(\Altum\Plugin::get('tmpiechart-block')->path . 'tmpiechart_block.php') ? include \Altum\Plugin::get('tmpiechart-block')->path . 'tmpiechart_block.php' : [];
$tmlist_block = \Altum\Plugin::is_active('tmlist-block') && file_exists(\Altum\Plugin::get('tmlist-block')->path . 'tmlist_block.php') ? include \Altum\Plugin::get('tmlist-block')->path . 'tmlist_block.php' : [];
$tmreview_block = \Altum\Plugin::is_active('tmreview-block') && file_exists(\Altum\Plugin::get('tmreview-block')->path . 'tmreview_block.php') ? include \Altum\Plugin::get('tmreview-block')->path . 'tmreview_block.php' : [];
$tmticker_block = \Altum\Plugin::is_active('tmticker-block') && file_exists(\Altum\Plugin::get('tmticker-block')->path . 'tmticker_block.php') ? include \Altum\Plugin::get('tmticker-block')->path . 'tmticker_block.php' : [];
$tmgradienttext_block = \Altum\Plugin::is_active('tmgradienttext-block') && file_exists(\Altum\Plugin::get('tmgradienttext-block')->path . 'tmgradienttext_block.php') ? include \Altum\Plugin::get('tmgradienttext-block')->path . 'tmgradienttext_block.php' : [];
$tmtextmorph_block = \Altum\Plugin::is_active('tmtextmorph-block') && file_exists(\Altum\Plugin::get('tmtextmorph-block')->path . 'tmtextmorph_block.php') ? include \Altum\Plugin::get('tmtextmorph-block')->path . 'tmtextmorph_block.php' : [];
$tmtextlogo_block = \Altum\Plugin::is_active('tmtextlogo-block') && file_exists(\Altum\Plugin::get('tmtextlogo-block')->path . 'tmtextlogo_block.php') ? include \Altum\Plugin::get('tmtextlogo-block')->path . 'tmtextlogo_block.php' : [];
$tmtranslator_block = \Altum\Plugin::is_active('tmtranslator-block') && file_exists(\Altum\Plugin::get('tmtranslator-block')->path . 'tmtranslator_block.php') ? include \Altum\Plugin::get('tmtranslator-block')->path . 'tmtranslator_block.php' : [];
$tmwawidget_block = \Altum\Plugin::is_active('tmwawidget-block') && file_exists(\Altum\Plugin::get('tmwawidget-block')->path . 'tmwawidget_block.php') ? include \Altum\Plugin::get('tmwawidget-block')->path . 'tmwawidget_block.php' : [];
$tmcatalog_block = \Altum\Plugin::is_active('tmcatalog-block') && file_exists(\Altum\Plugin::get('tmcatalog-block')->path . 'tmcatalog_block.php') ? include \Altum\Plugin::get('tmcatalog-block')->path . 'tmcatalog_block.php' : [];
$tmmarket_block = \Altum\Plugin::is_active('tmmarket-block') && file_exists(\Altum\Plugin::get('tmmarket-block')->path . 'tmmarket_block.php') ? include \Altum\Plugin::get('tmmarket-block')->path . 'tmmarket_block.php' : [];
$tmonetimeoffer_block = \Altum\Plugin::is_active('tmonetimeoffer-block') && file_exists(\Altum\Plugin::get('tmonetimeoffer-block')->path . 'tmonetimeoffer_block.php') ? include \Altum\Plugin::get('tmonetimeoffer-block')->path . 'tmonetimeoffer_block.php' : [];
$tmrichtext_block = \Altum\Plugin::is_active('tmrichtext-block') && file_exists(\Altum\Plugin::get('tmrichtext-block')->path . 'tmrichtext_block.php') ? include \Altum\Plugin::get('tmrichtext-block')->path . 'tmrichtext_block.php' : [];																																																											
$default_blocks = [
    'link' => [
        'type' => 'default',
        'icon' => 'fas fa-fw fa-link',
        'color' => '#004ecc',
        'has_statistics' => true,
        'display_dynamic_name' => 'name',
        'whitelisted_thumbnail_image_extensions' => ['jpg', 'jpeg', 'png', 'svg', 'gif', 'webp'],
        'category' => 'standard',
    ],
    'heading' => [
        'type' => 'default',
        'icon' => 'fas fa-fw fa-heading',
        'color' => '#000000',
        'has_statistics' => false,
        'display_dynamic_name' => 'text',
        'category' => 'standard',
    ],
    'paragraph' => [
        'type' => 'default',
        'icon' => 'fas fa-fw fa-paragraph',
        'color' => '#494949',
        'has_statistics' => false,
        'display_dynamic_name' => 'text',
        'category' => 'standard',
    ],
    'avatar' => [
        'type' => 'default',
        'icon' => 'fas fa-fw fa-user',
        'color' => '#8b2abf',
        'has_statistics' => true,
        'display_dynamic_name' => false,
        'whitelisted_image_extensions' => ['jpg', 'jpeg', 'png', 'svg', 'gif', 'webp'],
        'category' => 'standard',
    ],
    'image' => [
        'type' => 'default',
        'icon' => 'fas fa-fw fa-image',
        'color' => '#0682FF',
        'has_statistics' => true,
        'display_dynamic_name' => 'image_alt',
        'whitelisted_image_extensions' => ['jpg', 'jpeg', 'png', 'svg', 'gif', 'webp'],
        'category' => 'standard',
    ],
    'socials' => [
        'type' => 'default',
        'icon' => 'fas fa-fw fa-users',
        'color' => '#63d2ff',
        'has_statistics' => false,
        'display_dynamic_name' => false,
        'category' => 'standard',
    ],
    'email_collector' => [
        'type' => 'default',
        'icon' => 'fas fa-envelope',
        'color' => '#c91685',
        'has_statistics' => false,
        'display_dynamic_name' => 'name',
        'whitelisted_thumbnail_image_extensions' => ['jpg', 'jpeg', 'png', 'svg', 'gif', 'webp'],
        'category' => 'advanced',
    ],
    'threads' => [
        'type' => 'default',
        'icon' => 'fab fa-threads',
        'color' => '#f54640',
        'has_statistics' => false,
        'display_dynamic_name' => false,
        'whitelisted_hosts' => ['threads.net', 'www.threads.net'],
        'category' => 'embeds',
    ],
    'soundcloud' => [
        'type' => 'default',
        'icon' => 'fab fa-soundcloud',
        'color' => '#ff8800',
        'has_statistics' => false,
        'display_dynamic_name' => false,
        'whitelisted_hosts' => ['soundcloud.com'],
        'category' => 'embeds',
    ],
    'spotify' => [
        'type' => 'default',
        'icon' => 'fab fa-spotify',
        'color' => '#1db954',
        'has_statistics' => false,
        'display_dynamic_name' => false,
        'whitelisted_hosts' => ['open.spotify.com'],
        'category' => 'embeds',
    ],
    'youtube' => [
        'type' => 'default',
        'icon' => 'fab fa-youtube',
        'color' => '#ff0000',
        'has_statistics' => false,
        'display_dynamic_name' => false,
        'whitelisted_hosts' => ['www.youtube.com', 'youtu.be'],
        'category' => 'embeds',
    ],
    'twitch' => [
        'type' => 'default',
        'icon' => 'fab fa-twitch',
        'color' => '#6441a5',
        'has_statistics' => false,
        'display_dynamic_name' => false,
        'whitelisted_hosts' => ['www.twitch.tv'],
        'category' => 'embeds',
    ],
    'vimeo' => [
        'type' => 'default',
        'icon' => 'fab fa-vimeo',
        'color' => '#1ab7ea',
        'has_statistics' => false,
        'display_dynamic_name' => false,
        'whitelisted_hosts' => ['vimeo.com'],
        'category' => 'embeds',
    ],
    'tiktok_video' => [
        'type' => 'default',
        'icon' => 'fab fa-tiktok',
        'color' => '#FD3E3E',
        'has_statistics' => false,
        'display_dynamic_name' => false,
        'whitelisted_hosts' => ['www.tiktok.com'],
        'category' => 'embeds',
    ],
    'paypal' => [
        'type' => 'default',
        'icon' => 'fab fa-fw fa-paypal',
        'color' => '#00457C',
        'has_statistics' => true,
        'display_dynamic_name' => 'name',
        'whitelisted_thumbnail_image_extensions' => ['jpg', 'jpeg', 'png', 'svg', 'gif', 'webp'],
        'category' => 'payments',
    ],
    'phone_collector' => [
        'type' => 'default',
        'icon' => 'fas fa-phone-square-alt',
        'color' => '#39c640',
        'has_statistics' => false,
        'display_dynamic_name' => 'name',
        'whitelisted_thumbnail_image_extensions' => ['jpg', 'jpeg', 'png', 'svg', 'gif', 'webp'],
        'category' => 'advanced',
    ],
    'contact_collector' => [
        'type' => 'default',
        'icon' => 'fas fa-address-book',
        'color' => '#7136c0',
        'has_statistics' => false,
        'display_dynamic_name' => 'name',
        'whitelisted_thumbnail_image_extensions' => ['jpg', 'jpeg', 'png', 'svg', 'gif', 'webp'],
        'category' => 'advanced',
    ],
];

if(settings()->links->google_static_maps_is_enabled) {
    $default_blocks['map'] = [
        'type' => 'default',
        'icon' => 'fas fa-fw fa-map',
        'color' => '#31A952',
        'has_statistics' => true,
        'display_dynamic_name' => 'address',
        'category' => 'advanced',
    ];
}

return array_merge(
    $default_blocks,
    $pro_blocks,
    $ultimate_blocks,
    $payment_blocks,
    $menu_block,
    $modal_block,
    $preloader_block,
    $slider_block,
    $cardslider_block,
    $yamaps_block,
    $tmprice_block,
    $tmtimeline_block,
    $tmfaq_block,
    $tmscrollindicator_block,
    $tmscrollcards_block,
    $tmprogress_block,
    $tmnotification_block,
    $tmscrolltimeline_block,
    $tmnewsfeed_block,
    $tmpiechart_block,
    $tmlist_block,
    $tmreview_block,
    $tmticker_block,
    $tmgradienttext_block,
    $tmtextlogo_block,
    $tmtextmorph_block,
    $tmtranslator_block,
    $tmwawidget_block,
    $tmcatalog_block,
    $tmmarket_block,
    $tmonetimeoffer_block,
    $tmrichtext_block,	
);

