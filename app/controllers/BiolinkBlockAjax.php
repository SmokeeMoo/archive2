<?php
/*
 * @copyright Copyright (c) 2023 AltumCode (https://altumcode.com/)
 *
 * This software is exclusively sold through https://altumcode.com/ by the AltumCode author.
 * Downloading this product from any other sources and running it without a proper license is illegal,
 *  except the official ones linked from https://altumcode.com/.
 */

namespace Altum\Controllers;

use Altum\Alerts;
use Altum\Date;
use Altum\Response;
use Unirest\Request;

class BiolinkBlockAjax extends Controller {
    public $biolink_blocks = null;
    public $total_biolink_blocks = 0;

    public function index() {
        \Altum\Authentication::guard();

        if(!empty($_POST) && (\Altum\Csrf::check('token') || \Altum\Csrf::check('global_token')) && isset($_POST['request_type'])) {

            switch($_POST['request_type']) {

                /* Status toggle */
                case 'is_enabled_toggle': $this->is_enabled_toggle(); break;

                /* Duplicate link */
                case 'duplicate': $this->duplicate(); break;

                /* Order links */
                case 'order': $this->order(); break;

                /* Create */
                case 'create': $this->create(); break;

                /* Update */
                case 'update': $this->update(); break;

                /* Delete */
                case 'delete': $this->delete(); break;

            }

        }

        die($_POST['request_type']);
    }

    private function is_enabled_toggle() {
        /* Team checks */
        if(\Altum\Teams::is_delegated() && !\Altum\Teams::has_access('update.biolinks_blocks')) {
            Response::json(l('global.info_message.team_no_access'), 'error');
        }

        $_POST['biolink_block_id'] = (int) $_POST['biolink_block_id'];

        /* Get the current status */
        $biolink_block = db()->where('biolink_block_id', $_POST['biolink_block_id'])->where('user_id', $this->user->user_id)->getOne('biolinks_blocks', ['biolink_block_id', 'link_id', 'is_enabled']);

        if($biolink_block) {
            $new_is_enabled = (int) !$biolink_block->is_enabled;

            db()->where('biolink_block_id', $biolink_block->biolink_block_id)->update('biolinks_blocks', ['is_enabled' => $new_is_enabled]);

            /* Clear the cache */
            \Altum\Cache::$adapter->deleteItem('link?link_id=' . $biolink_block->link_id);

            Response::json('', 'success');
        }
    }

    public function duplicate() {
        /* Team checks */
        if(\Altum\Teams::is_delegated() && !\Altum\Teams::has_access('create.biolinks_blocks')) {
            Response::json(l('global.info_message.team_no_access'), 'error');
        }

        $_POST['biolink_block_id'] = (int) $_POST['biolink_block_id'];

        //ALTUMCODE:DEMO if(DEMO) if($this->user->user_id == 1) Alerts::add_error('Please create an account on the demo to test out this function.');

        if(!\Altum\Csrf::check()) {
            Alerts::add_error(l('global.error_message.invalid_csrf_token'));
            redirect('links');
        }

        /* Get the link data */
        $biolink_block = db()->where('biolink_block_id', $_POST['biolink_block_id'])->where('user_id', $this->user->user_id)->getOne('biolinks_blocks');

        if(!$biolink_block) {
            redirect('links');
        }

        /* Make sure that the user didn't exceed the limit */
        $this->total_biolink_blocks = database()->query("SELECT COUNT(*) AS `total` FROM `biolinks_blocks` WHERE `user_id` = {$this->user->user_id} AND `link_id` = {$biolink_block->link_id}")->fetch_object()->total;
        if($this->user->plan_settings->biolink_blocks_limit != -1 && $this->total_biolink_blocks >= $this->user->plan_settings->biolink_blocks_limit) {
            Alerts::add_error(l('global.info_message.plan_feature_limit'));
        }

        if(!Alerts::has_field_errors() && !Alerts::has_errors()) {
            $biolink_block->settings = json_decode($biolink_block->settings);

            /* Duplication of resources */
            switch($biolink_block->type) {
                case 'file':
                case 'audio':
                case 'video':
                case 'pdf_document':
                    $biolink_block->settings->file = \Altum\Uploads::copy_uploaded_file($biolink_block->settings->file, \Altum\Uploads::get_path('files'), \Altum\Uploads::get_path('files'), 'json_error');
                    break;

                case 'review':
                    $biolink_block->settings->file = \Altum\Uploads::copy_uploaded_file($biolink_block->settings->image, \Altum\Uploads::get_path('block_images'), \Altum\Uploads::get_path('block_images'), 'json_error');
                    break;

                case 'avatar':
                    $biolink_block->settings->image = \Altum\Uploads::copy_uploaded_file($biolink_block->settings->image, 'avatars/', 'avatars/', 'json_error');
                    break;

                case 'vcard':
                    $biolink_block->settings->vcard_avatar = \Altum\Uploads::copy_uploaded_file($biolink_block->settings->vcard_avatar, 'avatars/', 'avatars/', 'json_error');
                    $biolink_block->settings->image = \Altum\Uploads::copy_uploaded_file($biolink_block->settings->image, 'block_thumbnail_images/', 'block_thumbnail_images/', 'json_error');
                    break;

                case 'image':
                case 'image_grid':
                    $biolink_block->settings->image = \Altum\Uploads::copy_uploaded_file($biolink_block->settings->image, 'block_images/', 'block_images/', 'json_error');
                    break;

                default:
                    $biolink_block->settings->image = \Altum\Uploads::copy_uploaded_file($biolink_block->settings->image, 'block_thumbnail_images/', 'block_thumbnail_images/', 'json_error');
                    break;
            }

            $settings = json_encode($biolink_block->settings);

            /* Database query */
            db()->insert('biolinks_blocks', [
                'user_id' => $this->user->user_id,
                'link_id' => $biolink_block->link_id,
                'type' => $biolink_block->type,
                'location_url' => $biolink_block->location_url,
                'settings' => $settings,
                'order' => $biolink_block->order + 1,
                'start_date' => $biolink_block->start_date,
                'end_date' => $biolink_block->end_date,
                'is_enabled' => $biolink_block->is_enabled,
                'datetime' => \Altum\Date::$date,
            ]);

            /* Clear the cache */
            \Altum\Cache::$adapter->deleteItem('link?link_id=' . $biolink_block->link_id);

            /* Set a nice success message */
            Alerts::add_success(l('global.success_message.create2'));

            /* Redirect */
            redirect('link/' . $biolink_block->link_id . '?tab=links');
        }

        redirect('links');
    }

    private function order() {
        /* Team checks */
        if(\Altum\Teams::is_delegated() && !\Altum\Teams::has_access('update.biolinks_blocks')) {
            Response::json(l('global.info_message.team_no_access'), 'error');
        }

        if(isset($_POST['biolink_blocks']) && is_array($_POST['biolink_blocks'])) {
            foreach($_POST['biolink_blocks'] as $link) {
                if(!isset($link['biolink_block_id']) || !isset($link['order'])) {
                    continue;
                }

                $biolink_block = db()->where('biolink_block_id', $link['biolink_block_id'])->where('user_id', $this->user->user_id)->getOne('biolinks_blocks', ['link_id']);

                if(!$biolink_block) {
                    continue;
                }

                $link['biolink_block_id'] = (int) $link['biolink_block_id'];
                $link['order'] = (int) $link['order'];

                /* Update the link order */
                db()->where('biolink_block_id', $link['biolink_block_id'])->where('user_id', $this->user->user_id)->update('biolinks_blocks', ['order' => $link['order']]);
            }

            if(isset($biolink_block)) {
                /* Clear the cache */
                \Altum\Cache::$adapter->deleteItem('link?link_id=' . $biolink_block->link_id);
            }
        }

        Response::json('', 'success');
    }

    private function create() {
        /* Team checks */
        if(\Altum\Teams::is_delegated() && !\Altum\Teams::has_access('create.biolinks_blocks')) {
            Response::json(l('global.info_message.team_no_access'), 'error');
        }

        $this->biolink_blocks = require APP_PATH . 'includes/biolink_blocks.php';

        $test = 1;

        /* Check for available biolink blocks */
        if(isset($_POST['block_type']) && array_key_exists($_POST['block_type'], $this->biolink_blocks)) {
            $_POST['block_type'] = query_clean($_POST['block_type']);
            $_POST['link_id'] = (int) $_POST['link_id'];

            /* Make sure that the user didn't exceed the limit */
            $this->total_biolink_blocks = database()->query("SELECT COUNT(*) AS `total` FROM `biolinks_blocks` WHERE `user_id` = {$this->user->user_id} AND `link_id` = {$_POST['link_id']}")->fetch_object()->total;
            if($this->user->plan_settings->biolink_blocks_limit != -1 && $this->total_biolink_blocks >= $this->user->plan_settings->biolink_blocks_limit) {
                Response::json(l('global.info_message.plan_feature_limit'), 'error');
            }

            $individual_blocks = ['link', 'heading', 'big_link', 'paragraph', 'markdown', 'avatar', 'socials', 'email_collector', 'rss_feed', 'custom_html', 'vcard', 'image', 'image_grid', 'divider', 'list', 'alert', 'faq', 'timeline', 'review', 'image_slider', 'discord', 'countdown', 'cta', 'external_item', 'share', 'youtube_feed', 'paypal', 'phone_collector', 'donation', 'product', 'service', 'map', 'menu', 'modal', 'preloader', 'slider', 'cardslider', 'yamaps', 'tmprice', 'tmtimeline', 'tmfaq', 'tmscrollindicator', 'tmscrollcards', 'tmprogress', 'tmnotification', 'tmscrolltimeline', 'tmnewsfeed', 'tmpiechart', 'tmlist', 'tmreview', 'tmticker', 'tmgradienttext', 'tmtextmorph', 'tmtextlogo', 'tmtranslator', 'tmwawidget', 'tmcatalog', 'tmmarket', 'tmonetimeoffer', 'tmrichtext'];
            $embeddable_blocks = ['anchor', 'applemusic', 'soundcloud', 'spotify', 'tidal', 'tiktok_video', 'typeform', 'tiktok_profile', 'twitch', 'twitter_tweet', 'twitter_profile', 'pinterest_profile', 'vimeo', 'youtube', 'instagram_media', 'facebook', 'reddit', 'rumble'];
            $file_blocks = ['audio', 'video', 'file', 'pdf_document'];

            if(in_array($_POST['block_type'], $individual_blocks)) {
                $this->{'create_biolink_' . $_POST['block_type']}();
            }

            else if(in_array($_POST['block_type'], $file_blocks)) {
                $this->create_biolink_file($_POST['block_type']);
            }

            else if(in_array($_POST['block_type'], $embeddable_blocks)) {
                $this->create_biolink_embeddable($_POST['block_type']);
            }

        }

        Response::json('', 'success');
    }

    private function update() {
        /* Team checks */
        if(\Altum\Teams::is_delegated() && !\Altum\Teams::has_access('update.biolinks_blocks')) {
            Response::json(l('global.info_message.team_no_access'), 'error');
        }

        $this->biolink_blocks = require APP_PATH . 'includes/biolink_blocks.php';

        if(!empty($_POST)) {
            /* Check for available biolink blocks */
            if(isset($_POST['block_type']) && array_key_exists($_POST['block_type'], $this->biolink_blocks)) {
                $_POST['block_type'] = query_clean($_POST['block_type']);

                $individual_blocks = ['link', 'heading', 'big_link', 'paragraph', 'markdown', 'avatar', 'socials', 'email_collector', 'rss_feed', 'custom_html', 'vcard', 'image', 'image_grid', 'divider', 'list', 'alert', 'faq', 'timeline', 'review', 'image_slider', 'discord', 'countdown', 'cta', 'external_item', 'share', 'youtube_feed', 'paypal', 'phone_collector', 'donation', 'product', 'service', 'map', 'menu', 'modal', 'preloader', 'slider', 'cardslider', 'yamaps', 'tmprice', 'tmtimeline', 'tmfaq', 'tmscrollindicator', 'tmscrollcards', 'tmprogress', 'tmnotification', 'tmscrolltimeline', 'tmnewsfeed', 'tmpiechart', 'tmlist', 'tmreview', 'tmticker', 'tmgradienttext', 'tmtextmorph', 'tmtextlogo', 'tmtranslator', 'tmwawidget', 'tmcatalog', 'tmmarket', 'tmonetimeoffer', 'tmrichtext'];
                $embeddable_blocks = ['anchor', 'applemusic', 'soundcloud', 'spotify', 'tidal', 'tiktok_video', 'typeform', 'tiktok_profile', 'twitch', 'twitter_tweet', 'twitter_profile', 'pinterest_profile', 'vimeo', 'youtube', 'instagram_media', 'facebook', 'reddit', 'rumble'];
                $file_blocks = ['audio', 'video', 'file', 'pdf_document'];

                if(in_array($_POST['block_type'], $individual_blocks)) {
                    $this->{'update_biolink_' . $_POST['block_type']}();
                }

                else if(in_array($_POST['block_type'], $file_blocks)) {
                    $this->update_biolink_file($_POST['block_type']);
                }

                else if(in_array($_POST['block_type'], $embeddable_blocks)) {
                    $this->update_biolink_embeddable($_POST['block_type']);
                }

            }
        }

        die();
    }

    private function create_biolink_link() {
        $_POST['link_id'] = (int) $_POST['link_id'];
        $_POST['location_url'] = get_url($_POST['location_url']);
        $_POST['name'] = mb_substr(query_clean($_POST['name']), 0, 128);

        if(!$link = db()->where('link_id', $_POST['link_id'])->where('user_id', $this->user->user_id)->getOne('links')) {
            die();
        }

        $this->check_location_url($_POST['location_url']);

        $type = 'link';
        $settings = json_encode([
            'name' => $_POST['name'],
            'open_in_new_tab' => false,
            'text_color' => 'black',
            'text_alignment' => 'center',
            'background_color' => 'white',
            'border_shadow_offset_x' => 0,
            'border_shadow_offset_y' => 0,
            'border_shadow_blur' => 20,
            'border_shadow_spread' => 0,
            'border_shadow_color' => '#00000010',
            'border_width' => 0,
            'border_style' => 'solid',
            'border_color' => 'white',
            'border_radius' => 'rounded',
            'animation' => false,
            'animation_runs' => 'repeat-1',
            'icon' => '',
            'image' => '',

            /* Display settings */
            'display_countries' => [],
            'display_devices' => [],
            'display_languages' => [],
            'display_operating_systems' => [],
        ]);

        /* Database query */
        db()->insert('biolinks_blocks', [
            'user_id' => $this->user->user_id,
            'link_id' => $_POST['link_id'],
            'type' => $type,
            'location_url' => $_POST['location_url'],
            'settings' => $settings,
            'order' => $this->total_biolink_blocks,
            'datetime' => \Altum\Date::$date,
        ]);

        /* Clear the cache */
        \Altum\Cache::$adapter->deleteItem('link?link_id=' . $_POST['link_id']);

        Response::json('', 'success', ['url' => url('link/' . $_POST['link_id'] . '?tab=links')]);
    }

    private function update_biolink_link() {
        $_POST['biolink_block_id'] = (int) $_POST['biolink_block_id'];
        $_POST['location_url'] = get_url($_POST['location_url']);
        $_POST['name'] = mb_substr(query_clean($_POST['name']), 0, 128);
        $_POST['open_in_new_tab'] = isset($_POST['open_in_new_tab']);
        $_POST['border_radius'] = in_array($_POST['border_radius'], ['straight', 'round', 'rounded']) ? query_clean($_POST['border_radius']) : 'rounded';
        $_POST['border_width'] = in_array($_POST['border_width'], [0, 1, 2, 3, 4, 5]) ? (int) $_POST['border_width'] : 0;
        $_POST['border_style'] = in_array($_POST['border_style'], ['solid', 'dashed', 'double', 'inset', 'outset']) ? query_clean($_POST['border_style']) : 'solid';
        $_POST['border_color'] = !preg_match('/#([A-Fa-f0-9]{3,4}){1,2}\b/i', $_POST['border_color']) ? '#000000' : $_POST['border_color'];
        $_POST['border_shadow_offset_x'] = in_array($_POST['border_shadow_offset_x'], range(-20, 20)) ? (int) $_POST['border_shadow_offset_x'] : 0;
        $_POST['border_shadow_offset_y'] = in_array($_POST['border_shadow_offset_y'], range(-20, 20)) ? (int) $_POST['border_shadow_offset_y'] : 0;
        $_POST['border_shadow_blur'] = in_array($_POST['border_shadow_blur'], range(0, 20)) ? (int) $_POST['border_shadow_blur'] : 0;
        $_POST['border_shadow_spread'] = in_array($_POST['border_shadow_spread'], range(0, 10)) ? (int) $_POST['border_shadow_spread'] : 0;
        $_POST['border_shadow_color'] = !preg_match('/#([A-Fa-f0-9]{3,4}){1,2}\b/i', $_POST['border_shadow_color']) ? '#000000' : $_POST['border_shadow_color'];
        $_POST['animation'] = in_array($_POST['animation'], require APP_PATH . 'includes/biolink_animations.php') || $_POST['animation'] == 'false' ? query_clean($_POST['animation']) : false;
        $_POST['animation_runs'] = isset($_POST['animation_runs']) && in_array($_POST['animation_runs'], ['repeat-1', 'repeat-2', 'repeat-3', 'infinite']) ? query_clean($_POST['animation_runs']) : false;
        $_POST['icon'] = query_clean($_POST['icon']);
        $_POST['text_color'] = !preg_match('/#([A-Fa-f0-9]{3,4}){1,2}\b/i', $_POST['text_color']) ? '#000000' : $_POST['text_color'];
        $_POST['text_alignment'] = in_array($_POST['text_alignment'], ['center', 'left', 'right', 'justify']) ? query_clean($_POST['text_alignment']) : 'center';
        $_POST['background_color'] = !preg_match('/#([A-Fa-f0-9]{3,4}){1,2}\b/i', $_POST['background_color']) ? '#ffffff' : $_POST['background_color'];

        /* Display settings */
        $this->process_display_settings();

        if(!$biolink_block = db()->where('biolink_block_id', $_POST['biolink_block_id'])->where('user_id', $this->user->user_id)->getOne('biolinks_blocks')) {
            die();
        }
        $biolink_block->settings = json_decode($biolink_block->settings);

        /* Check for any errors */
        $required_fields = ['location_url', 'name'];

        /* Check for any errors */
        foreach($required_fields as $field) {
            if(!isset($_POST[$field]) || (isset($_POST[$field]) && empty($_POST[$field]) && $_POST[$field] != '0')) {
                Response::json(l('global.error_message.empty_fields'), 'error');
                break 1;
            }
        }

        $this->check_location_url($_POST['location_url']);

        /* Image upload */
        $db_image = $this->handle_image_upload($biolink_block->settings->image, 'block_thumbnail_images/', settings()->links->thumbnail_image_size_limit);

        /* Check for the removal of the already uploaded file */
        if(isset($_POST['image_remove'])) {
            /* Offload deleting */
            if(\Altum\Plugin::is_active('offload') && settings()->offload->uploads_url) {
                $s3 = new \Aws\S3\S3Client(get_aws_s3_config());
                $s3->deleteObject([
                    'Bucket' => settings()->offload->storage_name,
                    'Key' => 'uploads/block_thumbnail_images/' . $biolink_block->settings->image,
                ]);
            }

            /* Local deleting */
            else {
                /* Delete current file */
                if(!empty($biolink_block->settings->image) && file_exists(UPLOADS_PATH . 'block_thumbnail_images/' . $biolink_block->settings->image)) {
                    unlink(UPLOADS_PATH . 'block_thumbnail_images/' . $biolink_block->settings->image);
                }
            }
            $db_image = null;
        }

        $image_url = $db_image ? \Altum\Uploads::get_full_url('block_thumbnail_images') . $db_image : null;

        $settings = json_encode([
            'name' => $_POST['name'],
            'open_in_new_tab' => $_POST['open_in_new_tab'],
            'text_color' => $_POST['text_color'],
            'text_alignment' => $_POST['text_alignment'],
            'background_color' => $_POST['background_color'],
            'border_radius' => $_POST['border_radius'],
            'border_width' => $_POST['border_width'],
            'border_style' => $_POST['border_style'],
            'border_color' => $_POST['border_color'],
            'border_shadow_offset_x' => $_POST['border_shadow_offset_x'],
            'border_shadow_offset_y' => $_POST['border_shadow_offset_y'],
            'border_shadow_blur' => $_POST['border_shadow_blur'],
            'border_shadow_spread' => $_POST['border_shadow_spread'],
            'border_shadow_color' => $_POST['border_shadow_color'],
            'animation' => $_POST['animation'],
            'animation_runs' => $_POST['animation_runs'],
            'icon' => $_POST['icon'],
            'image' => $db_image,

            /* Display settings */
            'display_countries' => $_POST['display_countries'],
            'display_devices' => $_POST['display_devices'],
            'display_languages' => $_POST['display_languages'],
            'display_operating_systems' => $_POST['display_operating_systems'],
        ]);

        /* Database query */
        db()->where('biolink_block_id', $_POST['biolink_block_id'])->update('biolinks_blocks', [
            'location_url' => $_POST['location_url'],
            'settings' => $settings,
            'start_date' => $_POST['start_date'],
            'end_date' => $_POST['end_date'],
        ]);

        /* Clear the cache */
        \Altum\Cache::$adapter->deleteItem('link?link_id=' . $biolink_block->link_id);

        Response::json(l('global.success_message.update2'), 'success', ['images' => ['image' => $image_url], 'location_url' => $_POST['location_url']]);
    }

    private function create_biolink_big_link() {
        $_POST['link_id'] = (int) $_POST['link_id'];
        $_POST['location_url'] = get_url($_POST['location_url']);
        $_POST['name'] = input_clean($_POST['name'], 128);
        $_POST['description'] = input_clean($_POST['description'], 256);

        if(!$link = db()->where('link_id', $_POST['link_id'])->where('user_id', $this->user->user_id)->getOne('links')) {
            die();
        }

        $this->check_location_url($_POST['location_url']);

        $type = 'big_link';
        $settings = json_encode([
            'name' => $_POST['name'],
            'description' => $_POST['description'],
            'open_in_new_tab' => false,
            'text_color' => 'black',
            'description_color' => 'gray',
            'text_alignment' => 'left',
            'background_color' => 'white',
            'border_shadow_offset_x' => 0,
            'border_shadow_offset_y' => 0,
            'border_shadow_blur' => 20,
            'border_shadow_spread' => 0,
            'border_shadow_color' => '#00000010',
            'border_width' => 0,
            'border_style' => 'solid',
            'border_color' => 'white',
            'border_radius' => 'rounded',
            'animation' => false,
            'animation_runs' => 'repeat-1',
            'icon' => '',
            'image' => '',

            /* Display settings */
            'display_countries' => [],
            'display_devices' => [],
            'display_languages' => [],
            'display_operating_systems' => [],
        ]);

        /* Database query */
        db()->insert('biolinks_blocks', [
            'user_id' => $this->user->user_id,
            'link_id' => $_POST['link_id'],
            'type' => $type,
            'location_url' => $_POST['location_url'],
            'settings' => $settings,
            'order' => $this->total_biolink_blocks,
            'datetime' => \Altum\Date::$date,
        ]);

        /* Clear the cache */
        \Altum\Cache::$adapter->deleteItem('link?link_id=' . $_POST['link_id']);

        Response::json('', 'success', ['url' => url('link/' . $_POST['link_id'] . '?tab=links')]);
    }

    private function update_biolink_big_link() {
        $_POST['biolink_block_id'] = (int) $_POST['biolink_block_id'];
        $_POST['location_url'] = get_url($_POST['location_url']);
        $_POST['name'] = mb_substr(query_clean($_POST['name']), 0, 128);
        $_POST['description'] = input_clean($_POST['description'], 256);
        $_POST['open_in_new_tab'] = isset($_POST['open_in_new_tab']);
        $_POST['border_radius'] = in_array($_POST['border_radius'], ['straight', 'round', 'rounded']) ? query_clean($_POST['border_radius']) : 'rounded';
        $_POST['border_width'] = in_array($_POST['border_width'], [0, 1, 2, 3, 4, 5]) ? (int) $_POST['border_width'] : 0;
        $_POST['border_style'] = in_array($_POST['border_style'], ['solid', 'dashed', 'double', 'inset', 'outset']) ? query_clean($_POST['border_style']) : 'solid';
        $_POST['border_color'] = !preg_match('/#([A-Fa-f0-9]{3,4}){1,2}\b/i', $_POST['border_color']) ? '#000000' : $_POST['border_color'];
        $_POST['border_shadow_offset_x'] = in_array($_POST['border_shadow_offset_x'], range(-20, 20)) ? (int) $_POST['border_shadow_offset_x'] : 0;
        $_POST['border_shadow_offset_y'] = in_array($_POST['border_shadow_offset_y'], range(-20, 20)) ? (int) $_POST['border_shadow_offset_y'] : 0;
        $_POST['border_shadow_blur'] = in_array($_POST['border_shadow_blur'], range(0, 20)) ? (int) $_POST['border_shadow_blur'] : 0;
        $_POST['border_shadow_spread'] = in_array($_POST['border_shadow_spread'], range(0, 10)) ? (int) $_POST['border_shadow_spread'] : 0;
        $_POST['border_shadow_color'] = !preg_match('/#([A-Fa-f0-9]{3,4}){1,2}\b/i', $_POST['border_shadow_color']) ? '#000000' : $_POST['border_shadow_color'];
        $_POST['animation'] = in_array($_POST['animation'], require APP_PATH . 'includes/biolink_animations.php') || $_POST['animation'] == 'false' ? query_clean($_POST['animation']) : false;
        $_POST['animation_runs'] = isset($_POST['animation_runs']) && in_array($_POST['animation_runs'], ['repeat-1', 'repeat-2', 'repeat-3', 'infinite']) ? query_clean($_POST['animation_runs']) : false;
        $_POST['icon'] = query_clean($_POST['icon']);
        $_POST['text_color'] = !preg_match('/#([A-Fa-f0-9]{3,4}){1,2}\b/i', $_POST['text_color']) ? '#000000' : $_POST['text_color'];
        $_POST['description_color'] = !preg_match('/#([A-Fa-f0-9]{3,4}){1,2}\b/i', $_POST['description_color']) ? '#000000' : $_POST['description_color'];
        $_POST['text_alignment'] = in_array($_POST['text_alignment'], ['center', 'left', 'right', 'justify']) ? query_clean($_POST['text_alignment']) : 'center';
        $_POST['background_color'] = !preg_match('/#([A-Fa-f0-9]{3,4}){1,2}\b/i', $_POST['background_color']) ? '#ffffff' : $_POST['background_color'];

        /* Display settings */
        $this->process_display_settings();

        if(!$biolink_block = db()->where('biolink_block_id', $_POST['biolink_block_id'])->where('user_id', $this->user->user_id)->getOne('biolinks_blocks')) {
            die();
        }
        $biolink_block->settings = json_decode($biolink_block->settings);

        /* Check for any errors */
        $required_fields = ['location_url', 'name'];

        /* Check for any errors */
        foreach($required_fields as $field) {
            if(!isset($_POST[$field]) || (isset($_POST[$field]) && empty($_POST[$field]) && $_POST[$field] != '0')) {
                Response::json(l('global.error_message.empty_fields'), 'error');
                break 1;
            }
        }

        $this->check_location_url($_POST['location_url']);

        /* Image upload */
        $db_image = $this->handle_image_upload($biolink_block->settings->image, 'block_thumbnail_images/', settings()->links->thumbnail_image_size_limit);

        /* Check for the removal of the already uploaded file */
        if(isset($_POST['image_remove'])) {
            /* Offload deleting */
            if(\Altum\Plugin::is_active('offload') && settings()->offload->uploads_url) {
                $s3 = new \Aws\S3\S3Client(get_aws_s3_config());
                $s3->deleteObject([
                    'Bucket' => settings()->offload->storage_name,
                    'Key' => 'uploads/block_thumbnail_images/' . $biolink_block->settings->image,
                ]);
            }

            /* Local deleting */
            else {
                /* Delete current file */
                if(!empty($biolink_block->settings->image) && file_exists(UPLOADS_PATH . 'block_thumbnail_images/' . $biolink_block->settings->image)) {
                    unlink(UPLOADS_PATH . 'block_thumbnail_images/' . $biolink_block->settings->image);
                }
            }
            $db_image = null;
        }

        $image_url = $db_image ? \Altum\Uploads::get_full_url('block_thumbnail_images') . $db_image : null;

        $settings = json_encode([
            'name' => $_POST['name'],
            'description' => $_POST['description'],
            'open_in_new_tab' => $_POST['open_in_new_tab'],
            'text_color' => $_POST['text_color'],
            'description_color' => $_POST['description_color'],
            'text_alignment' => $_POST['text_alignment'],
            'background_color' => $_POST['background_color'],
            'border_radius' => $_POST['border_radius'],
            'border_width' => $_POST['border_width'],
            'border_style' => $_POST['border_style'],
            'border_color' => $_POST['border_color'],
            'border_shadow_offset_x' => $_POST['border_shadow_offset_x'],
            'border_shadow_offset_y' => $_POST['border_shadow_offset_y'],
            'border_shadow_blur' => $_POST['border_shadow_blur'],
            'border_shadow_spread' => $_POST['border_shadow_spread'],
            'border_shadow_color' => $_POST['border_shadow_color'],
            'animation' => $_POST['animation'],
            'animation_runs' => $_POST['animation_runs'],
            'icon' => $_POST['icon'],
            'image' => $db_image,

            /* Display settings */
            'display_countries' => $_POST['display_countries'],
            'display_devices' => $_POST['display_devices'],
            'display_languages' => $_POST['display_languages'],
            'display_operating_systems' => $_POST['display_operating_systems'],
        ]);

        /* Database query */
        db()->where('biolink_block_id', $_POST['biolink_block_id'])->update('biolinks_blocks', [
            'location_url' => $_POST['location_url'],
            'settings' => $settings,
            'start_date' => $_POST['start_date'],
            'end_date' => $_POST['end_date'],
        ]);

        /* Clear the cache */
        \Altum\Cache::$adapter->deleteItem('link?link_id=' . $biolink_block->link_id);

        Response::json(l('global.success_message.update2'), 'success', ['images' => ['image' => $image_url], 'location_url' => $_POST['location_url']]);
    }

    private function create_biolink_heading() {
        $_POST['link_id'] = (int) $_POST['link_id'];
        $_POST['text'] = mb_substr(query_clean($_POST['text']), 0, 256);
        $_POST['heading_type'] = in_array($_POST['heading_type'], ['h1', 'h2', 'h3', 'h4', 'h5', 'h6']) ? query_clean($_POST['heading_type']) : 'h1';

        if(!$link = db()->where('link_id', $_POST['link_id'])->where('user_id', $this->user->user_id)->getOne('links')) {
            die();
        }

        $type = 'heading';
        $settings = json_encode([
            'heading_type' => $_POST['heading_type'],
            'text' => $_POST['text'],
            'text_color' => '#ffffff',
            'text_alignment' => 'center',
            'verified_location' => '',

            /* Display settings */
            'display_countries' => [],
            'display_devices' => [],
            'display_languages' => [],
            'display_operating_systems' => [],
        ]);

        /* Database query */
        db()->insert('biolinks_blocks', [
            'user_id' => $this->user->user_id,
            'link_id' => $_POST['link_id'],
            'type' => $type,
            'location_url' => null,
            'settings' => $settings,
            'order' => $this->total_biolink_blocks,
            'datetime' => \Altum\Date::$date,
        ]);

        /* Clear the cache */
        \Altum\Cache::$adapter->deleteItem('link?link_id=' . $_POST['link_id']);

        Response::json('', 'success', ['url' => url('link/' . $_POST['link_id'] . '?tab=links')]);
    }

    private function update_biolink_heading() {
        $_POST['biolink_block_id'] = (int) $_POST['biolink_block_id'];
        $_POST['heading_type'] = in_array($_POST['heading_type'], ['h1', 'h2', 'h3', 'h4', 'h5', 'h6']) ? query_clean($_POST['heading_type']) : 'h1';
        $_POST['text'] = mb_substr(query_clean($_POST['text']), 0, 256);
        $_POST['text_color'] = !preg_match('/#([A-Fa-f0-9]{3,4}){1,2}\b/i', $_POST['text_color']) ? '#ffffff' : $_POST['text_color'];
        $_POST['verified_location'] = in_array($_POST['verified_location'], ['', 'left', 'right']) ? query_clean($_POST['verified_location']) : '';

        /* Display settings */
        $this->process_display_settings();

        if(!$biolink_block = db()->where('biolink_block_id', $_POST['biolink_block_id'])->where('user_id', $this->user->user_id)->getOne('biolinks_blocks')) {
            die();
        }

        $settings = json_encode([
            'heading_type' => $_POST['heading_type'],
            'text' => $_POST['text'],
            'text_color' => $_POST['text_color'],
            'text_alignment' => $_POST['text_alignment'],
            'verified_location' => $_POST['verified_location'],

            /* Display settings */
            'display_countries' => $_POST['display_countries'],
            'display_devices' => $_POST['display_devices'],
            'display_languages' => $_POST['display_languages'],
            'display_operating_systems' => $_POST['display_operating_systems'],
        ]);

        /* Database query */
        db()->where('biolink_block_id', $_POST['biolink_block_id'])->update('biolinks_blocks', [
            'settings' => $settings,
            'start_date' => $_POST['start_date'],
            'end_date' => $_POST['end_date'],
        ]);

        /* Clear the cache */
        \Altum\Cache::$adapter->deleteItem('link?link_id=' . $biolink_block->link_id);

        Response::json(l('global.success_message.update2'), 'success');
    }

    private function create_biolink_paragraph() {
        $_POST['link_id'] = (int) $_POST['link_id'];
        $_POST['text'] = mb_substr(input_clean($_POST['text']), 0, 2048);

        if(!$link = db()->where('link_id', $_POST['link_id'])->where('user_id', $this->user->user_id)->getOne('links')) {
            die();
        }

        $type = 'paragraph';
        $settings = json_encode([
            'text' => $_POST['text'],
            'text_color' => '#ffffff',
            'background_color' => '#000000',
            'border_radius' => 'rounded',
            'border_shadow_offset_x' => 0,
            'border_shadow_offset_y' => 0,
            'border_shadow_blur' => 20,
            'border_shadow_spread' => 0,
            'border_shadow_color' => '#00000010',
            'border_width' => 0,
            'border_style' => 'solid',
            'border_color' => 'white',
            'text_alignment' => 'center',

            /* Display settings */
            'display_countries' => [],
            'display_devices' => [],
            'display_languages' => [],
            'display_operating_systems' => [],
        ]);

        /* Database query */
        db()->insert('biolinks_blocks', [
            'user_id' => $this->user->user_id,
            'link_id' => $_POST['link_id'],
            'type' => $type,
            'location_url' => null,
            'settings' => $settings,
            'order' => $this->total_biolink_blocks,
            'datetime' => \Altum\Date::$date,
        ]);

        /* Clear the cache */
        \Altum\Cache::$adapter->deleteItem('link?link_id=' . $_POST['link_id']);

        Response::json('', 'success', ['url' => url('link/' . $_POST['link_id'] . '?tab=links')]);
    }

    private function update_biolink_paragraph() {
        $_POST['biolink_block_id'] = (int) $_POST['biolink_block_id'];
        $_POST['text'] = mb_substr(input_clean($_POST['text']), 0, 2048);
        $_POST['text_color'] = !preg_match('/#([A-Fa-f0-9]{3,4}){1,2}\b/i', $_POST['text_color']) ? '#ffffff' : $_POST['text_color'];
        $_POST['border_radius'] = in_array($_POST['border_radius'], ['straight', 'round', 'rounded']) ? query_clean($_POST['border_radius']) : 'rounded';
        $_POST['border_width'] = in_array($_POST['border_width'], [0, 1, 2, 3, 4, 5]) ? (int) $_POST['border_width'] : 0;
        $_POST['border_style'] = in_array($_POST['border_style'], ['solid', 'dashed', 'double', 'inset', 'outset']) ? query_clean($_POST['border_style']) : 'solid';
        $_POST['border_color'] = !preg_match('/#([A-Fa-f0-9]{3,4}){1,2}\b/i', $_POST['border_color']) ? '#000000' : $_POST['border_color'];
        $_POST['border_shadow_offset_x'] = in_array($_POST['border_shadow_offset_x'], range(-20, 20)) ? (int) $_POST['border_shadow_offset_x'] : 0;
        $_POST['border_shadow_offset_y'] = in_array($_POST['border_shadow_offset_y'], range(-20, 20)) ? (int) $_POST['border_shadow_offset_y'] : 0;
        $_POST['border_shadow_blur'] = in_array($_POST['border_shadow_blur'], range(0, 20)) ? (int) $_POST['border_shadow_blur'] : 0;
        $_POST['border_shadow_spread'] = in_array($_POST['border_shadow_spread'], range(0, 10)) ? (int) $_POST['border_shadow_spread'] : 0;
        $_POST['border_shadow_color'] = !preg_match('/#([A-Fa-f0-9]{3,4}){1,2}\b/i', $_POST['border_shadow_color']) ? '#000000' : $_POST['border_shadow_color'];
        $_POST['background_color'] = !preg_match('/#([A-Fa-f0-9]{3,4}){1,2}\b/i', $_POST['background_color']) ? '#ffffff' : $_POST['background_color'];

        /* Display settings */
        $this->process_display_settings();

        if(!$biolink_block = db()->where('biolink_block_id', $_POST['biolink_block_id'])->where('user_id', $this->user->user_id)->getOne('biolinks_blocks')) {
            die();
        }

        $settings = json_encode([
            'text' => $_POST['text'],
            'text_alignment' => $_POST['text_alignment'],
            'text_color' => $_POST['text_color'],
            'background_color' => $_POST['background_color'],
            'border_radius' => $_POST['border_radius'],
            'border_width' => $_POST['border_width'],
            'border_style' => $_POST['border_style'],
            'border_color' => $_POST['border_color'],
            'border_shadow_offset_x' => $_POST['border_shadow_offset_x'],
            'border_shadow_offset_y' => $_POST['border_shadow_offset_y'],
            'border_shadow_blur' => $_POST['border_shadow_blur'],
            'border_shadow_spread' => $_POST['border_shadow_spread'],
            'border_shadow_color' => $_POST['border_shadow_color'],

            /* Display settings */
            'display_countries' => $_POST['display_countries'],
            'display_devices' => $_POST['display_devices'],
            'display_languages' => $_POST['display_languages'],
            'display_operating_systems' => $_POST['display_operating_systems'],
        ]);

        /* Database query */
        db()->where('biolink_block_id', $_POST['biolink_block_id'])->update('biolinks_blocks', [
            'settings' => $settings,
            'start_date' => $_POST['start_date'],
            'end_date' => $_POST['end_date'],
        ]);

        /* Clear the cache */
        \Altum\Cache::$adapter->deleteItem('link?link_id=' . $biolink_block->link_id);

        Response::json(l('global.success_message.update2'), 'success');
    }

    private function create_biolink_markdown() {
        $_POST['biolink_block_id'] = (int) $_POST['biolink_block_id'];
        $_POST['text'] = mb_substr(input_clean($_POST['text']), 0, 2048);
        $_POST['text_color'] = !preg_match('/#([A-Fa-f0-9]{3,4}){1,2}\b/i', $_POST['text_color']) ? '#ffffff' : $_POST['text_color'];
        $_POST['border_radius'] = in_array($_POST['border_radius'], ['straight', 'round', 'rounded']) ? query_clean($_POST['border_radius']) : 'rounded';
        $_POST['border_width'] = in_array($_POST['border_width'], [0, 1, 2, 3, 4, 5]) ? (int) $_POST['border_width'] : 0;
        $_POST['border_style'] = in_array($_POST['border_style'], ['solid', 'dashed', 'double', 'inset', 'outset']) ? query_clean($_POST['border_style']) : 'solid';
        $_POST['border_color'] = !preg_match('/#([A-Fa-f0-9]{3,4}){1,2}\b/i', $_POST['border_color']) ? '#000000' : $_POST['border_color'];
        $_POST['border_shadow_offset_x'] = in_array($_POST['border_shadow_offset_x'], range(-20, 20)) ? (int) $_POST['border_shadow_offset_x'] : 0;
        $_POST['border_shadow_offset_y'] = in_array($_POST['border_shadow_offset_y'], range(-20, 20)) ? (int) $_POST['border_shadow_offset_y'] : 0;
        $_POST['border_shadow_blur'] = in_array($_POST['border_shadow_blur'], range(0, 20)) ? (int) $_POST['border_shadow_blur'] : 0;
        $_POST['border_shadow_spread'] = in_array($_POST['border_shadow_spread'], range(0, 10)) ? (int) $_POST['border_shadow_spread'] : 0;
        $_POST['border_shadow_color'] = !preg_match('/#([A-Fa-f0-9]{3,4}){1,2}\b/i', $_POST['border_shadow_color']) ? '#000000' : $_POST['border_shadow_color'];
        $_POST['background_color'] = !preg_match('/#([A-Fa-f0-9]{3,4}){1,2}\b/i', $_POST['background_color']) ? '#ffffff' : $_POST['background_color'];

        if(!$link = db()->where('link_id', $_POST['link_id'])->where('user_id', $this->user->user_id)->getOne('links')) {
            die();
        }

        $type = 'markdown';
        $settings = json_encode([
            'text' => $_POST['text'],
            'text_color' => '#ffffff',
            'background_color' => '#000000',
            'border_radius' => 'rounded',
            'border_shadow_offset_x' => 0,
            'border_shadow_offset_y' => 0,
            'border_shadow_blur' => 20,
            'border_shadow_spread' => 0,
            'border_shadow_color' => '#00000010',
            'border_width' => 0,
            'border_style' => 'solid',
            'border_color' => 'white',

            /* Display settings */
            'display_countries' => [],
            'display_devices' => [],
            'display_languages' => [],
            'display_operating_systems' => [],
        ]);

        /* Database query */
        db()->insert('biolinks_blocks', [
            'user_id' => $this->user->user_id,
            'link_id' => $_POST['link_id'],
            'type' => $type,
            'location_url' => null,
            'settings' => $settings,
            'order' => $this->total_biolink_blocks,
            'datetime' => \Altum\Date::$date,
        ]);

        /* Clear the cache */
        \Altum\Cache::$adapter->deleteItem('link?link_id=' . $_POST['link_id']);

        Response::json('', 'success', ['url' => url('link/' . $_POST['link_id'] . '?tab=links')]);
    }

    private function update_biolink_markdown() {
        $_POST['biolink_block_id'] = (int) $_POST['biolink_block_id'];
        $_POST['text'] = mb_substr(input_clean($_POST['text']), 0, 2048);
        $_POST['text_color'] = !preg_match('/#([A-Fa-f0-9]{3,4}){1,2}\b/i', $_POST['text_color']) ? '#ffffff' : $_POST['text_color'];

        /* Display settings */
        $this->process_display_settings();

        if(!$biolink_block = db()->where('biolink_block_id', $_POST['biolink_block_id'])->where('user_id', $this->user->user_id)->getOne('biolinks_blocks')) {
            die();
        }

        $settings = json_encode([
            'text' => $_POST['text'],
            'text_alignment' => $_POST['text_alignment'],
            'text_color' => $_POST['text_color'],
            'background_color' => $_POST['background_color'],
            'border_radius' => $_POST['border_radius'],
            'border_width' => $_POST['border_width'],
            'border_style' => $_POST['border_style'],
            'border_color' => $_POST['border_color'],
            'border_shadow_offset_x' => $_POST['border_shadow_offset_x'],
            'border_shadow_offset_y' => $_POST['border_shadow_offset_y'],
            'border_shadow_blur' => $_POST['border_shadow_blur'],
            'border_shadow_spread' => $_POST['border_shadow_spread'],
            'border_shadow_color' => $_POST['border_shadow_color'],

            /* Display settings */
            'display_countries' => $_POST['display_countries'],
            'display_devices' => $_POST['display_devices'],
            'display_languages' => $_POST['display_languages'],
            'display_operating_systems' => $_POST['display_operating_systems'],
        ]);

        /* Database query */
        db()->where('biolink_block_id', $_POST['biolink_block_id'])->update('biolinks_blocks', [
            'settings' => $settings,
            'start_date' => $_POST['start_date'],
            'end_date' => $_POST['end_date'],
        ]);

        /* Clear the cache */
        \Altum\Cache::$adapter->deleteItem('link?link_id=' . $biolink_block->link_id);

        Response::json(l('global.success_message.update2'), 'success');
    }

    private function create_biolink_avatar() {
        $_POST['link_id'] = (int) $_POST['link_id'];
        $_POST['size'] = in_array($_POST['size'], ['75', '100', '125', '150']) ? (int) $_POST['size'] : 125;
        $_POST['border_radius'] = in_array($_POST['border_radius'], ['straight', 'round', 'rounded']) ? query_clean($_POST['border_radius']) : 'rounded';

        if(!$link = db()->where('link_id', $_POST['link_id'])->where('user_id', $this->user->user_id)->getOne('links')) {
            die();
        }

        /* Image upload */
        $db_image = $this->handle_image_upload(null, 'avatars/', settings()->links->avatar_size_limit);

        $type = 'avatar';
        $settings = json_encode([
            'image' => $db_image,
            'size' => $_POST['size'],
            'border_radius' => $_POST['border_radius'],
            'border_shadow_offset_x' => 0,
            'border_shadow_offset_y' => 0,
            'border_shadow_blur' => 20,
            'border_shadow_spread' => 0,
            'border_shadow_color' => '#00000010',
            'border_width' => 0,
            'border_style' => 'solid',
            'border_color' => 'white',
            'object_fit' => 'contain',

            /* Display settings */
            'display_countries' => [],
            'display_devices' => [],
            'display_languages' => [],
            'display_operating_systems' => [],
        ]);

        /* Database query */
        db()->insert('biolinks_blocks', [
            'user_id' => $this->user->user_id,
            'link_id' => $_POST['link_id'],
            'type' => $type,
            'settings' => $settings,
            'order' => $this->total_biolink_blocks,
            'datetime' => \Altum\Date::$date,
        ]);

        /* Clear the cache */
        \Altum\Cache::$adapter->deleteItem('link?link_id=' . $_POST['link_id']);

        Response::json('', 'success', ['url' => url('link/' . $_POST['link_id'] . '?tab=links')]);
    }

    private function update_biolink_avatar() {
        $_POST['biolink_block_id'] = (int) $_POST['biolink_block_id'];
        $_POST['size'] = in_array($_POST['size'], ['75', '100', '125', '150']) ? (int) $_POST['size'] : 125;
        $_POST['border_radius'] = in_array($_POST['border_radius'], ['straight', 'round', 'rounded']) ? query_clean($_POST['border_radius']) : 'rounded';
        $_POST['border_width'] = in_array($_POST['border_width'], [0, 1, 2, 3, 4, 5]) ? (int) $_POST['border_width'] : 0;
        $_POST['border_style'] = in_array($_POST['border_style'], ['solid', 'dashed', 'double', 'inset', 'outset']) ? query_clean($_POST['border_style']) : 'solid';
        $_POST['border_color'] = !preg_match('/#([A-Fa-f0-9]{3,4}){1,2}\b/i', $_POST['border_color']) ? '#000000' : $_POST['border_color'];
        $_POST['border_shadow_offset_x'] = in_array($_POST['border_shadow_offset_x'], range(-20, 20)) ? (int) $_POST['border_shadow_offset_x'] : 0;
        $_POST['border_shadow_offset_y'] = in_array($_POST['border_shadow_offset_y'], range(-20, 20)) ? (int) $_POST['border_shadow_offset_y'] : 0;
        $_POST['border_shadow_blur'] = in_array($_POST['border_shadow_blur'], range(0, 20)) ? (int) $_POST['border_shadow_blur'] : 0;
        $_POST['border_shadow_spread'] = in_array($_POST['border_shadow_spread'], range(0, 10)) ? (int) $_POST['border_shadow_spread'] : 0;
        $_POST['border_shadow_color'] = !preg_match('/#([A-Fa-f0-9]{3,4}){1,2}\b/i', $_POST['border_shadow_color']) ? '#000000' : $_POST['border_shadow_color'];
        $_POST['object_fit'] = in_array($_POST['object_fit'], ['contain', 'cover', 'fill']) ? query_clean($_POST['object_fit']) : 'contain';

        /* Display settings */
        $this->process_display_settings();

        if(!$biolink_block = db()->where('biolink_block_id', $_POST['biolink_block_id'])->where('user_id', $this->user->user_id)->getOne('biolinks_blocks')) {
            die();
        }
        $biolink_block->settings = json_decode($biolink_block->settings);

        /* Image upload */
        $db_image = $this->handle_image_upload($biolink_block->settings->image, 'avatars/', settings()->links->image_size_limit);

        $image_url = $db_image ? \Altum\Uploads::get_full_url('avatars') . $db_image : null;

        $settings = json_encode([
            'image' => $db_image,
            'size' => $_POST['size'],
            'object_fit' => $_POST['object_fit'],
            'border_radius' => $_POST['border_radius'],
            'border_width' => $_POST['border_width'],
            'border_style' => $_POST['border_style'],
            'border_color' => $_POST['border_color'],
            'border_shadow_offset_x' => $_POST['border_shadow_offset_x'],
            'border_shadow_offset_y' => $_POST['border_shadow_offset_y'],
            'border_shadow_blur' => $_POST['border_shadow_blur'],
            'border_shadow_spread' => $_POST['border_shadow_spread'],
            'border_shadow_color' => $_POST['border_shadow_color'],

            /* Display settings */
            'display_countries' => $_POST['display_countries'],
            'display_devices' => $_POST['display_devices'],
            'display_languages' => $_POST['display_languages'],
            'display_operating_systems' => $_POST['display_operating_systems'],
        ]);

        /* Database query */
        db()->where('biolink_block_id', $_POST['biolink_block_id'])->update('biolinks_blocks', [
            'settings' => $settings,
            'start_date' => $_POST['start_date'],
            'end_date' => $_POST['end_date'],
        ]);

        /* Clear the cache */
        \Altum\Cache::$adapter->deleteItem('link?link_id=' . $biolink_block->link_id);

        Response::json(l('global.success_message.update2'), 'success', ['images' => ['image' => $image_url]]);
    }

    private function create_biolink_socials() {
        $_POST['link_id'] = (int) $_POST['link_id'];
        $_POST['color'] = !preg_match('/#([A-Fa-f0-9]{3,4}){1,2}\b/i', $_POST['color']) ? '#ffffff' : $_POST['color'];
        $_POST['size'] = in_array($_POST['size'], ['s', 'm', 'l', 'xl']) ? $_POST['size'] : 'l';

        /* Make sure the socials sent are proper */
        $biolink_socials = require APP_PATH . 'includes/biolink_socials.php';

        foreach($_POST['socials'] as $key => $value) {
            if(!array_key_exists($key, $biolink_socials)) {
                unset($_POST['socials'][$key]);
            } else {
                $_POST['socials'][$key] = mb_substr(query_clean($_POST['socials'][$key]), 0, $biolink_socials[$key]['max_length']);
            }
        }

        if(!$link = db()->where('link_id', $_POST['link_id'])->where('user_id', $this->user->user_id)->getOne('links')) {
            die();
        }

        $type = 'socials';
        $settings = json_encode([
            'color' => $_POST['color'],
            'socials' => $_POST['socials'],
            'size' => $_POST['size'],

            /* Display settings */
            'display_countries' => [],
            'display_devices' => [],
            'display_languages' => [],
            'display_operating_systems' => [],
        ]);

        /* Database query */
        db()->insert('biolinks_blocks', [
            'user_id' => $this->user->user_id,
            'link_id' => $_POST['link_id'],
            'type' => $type,
            'location_url' => null,
            'settings' => $settings,
            'order' => $this->total_biolink_blocks,
            'datetime' => \Altum\Date::$date,
        ]);

        /* Clear the cache */
        \Altum\Cache::$adapter->deleteItem('link?link_id=' . $_POST['link_id']);

        Response::json('', 'success', ['url' => url('link/' . $_POST['link_id'] . '?tab=links')]);
    }

    private function update_biolink_socials() {
        $_POST['biolink_block_id'] = (int) $_POST['biolink_block_id'];
        $_POST['color'] = !preg_match('/#([A-Fa-f0-9]{3,4}){1,2}\b/i', $_POST['color']) ? '#ffffff' : $_POST['color'];
        $_POST['size'] = in_array($_POST['size'], ['s', 'm', 'l', 'xl']) ? $_POST['size'] : 'l';

        /* Display settings */
        $this->process_display_settings();

        if(!$biolink_block = db()->where('biolink_block_id', $_POST['biolink_block_id'])->where('user_id', $this->user->user_id)->getOne('biolinks_blocks')) {
            die();
        }
        $biolink_block->settings = json_decode($biolink_block->settings);

        /* Make sure the socials sent are proper */
        $biolink_socials = require APP_PATH . 'includes/biolink_socials.php';

        foreach($_POST['socials'] as $key => $value) {
            if(!array_key_exists($key, $biolink_socials)) {
                unset($_POST['socials'][$key]);
            } else {
                $_POST['socials'][$key] = mb_substr(query_clean($_POST['socials'][$key]), 0, $biolink_socials[$key]['max_length']);
            }
        }

        $settings = json_encode([
            'color' => $_POST['color'],
            'socials' => $_POST['socials'],
            'size' => $_POST['size'],

            /* Display settings */
            'display_countries' => $_POST['display_countries'],
            'display_devices' => $_POST['display_devices'],
            'display_languages' => $_POST['display_languages'],
            'display_operating_systems' => $_POST['display_operating_systems'],
        ]);

        /* Database query */
        db()->where('biolink_block_id', $_POST['biolink_block_id'])->update('biolinks_blocks', [
            'settings' => $settings,
            'start_date' => $_POST['start_date'],
            'end_date' => $_POST['end_date'],
        ]);

        /* Clear the cache */
        \Altum\Cache::$adapter->deleteItem('link?link_id=' . $biolink_block->link_id);

        Response::json(l('global.success_message.update2'), 'success');
    }

    private function create_biolink_email_collector() {
        $_POST['link_id'] = (int) $_POST['link_id'];
        $_POST['name'] = mb_substr(query_clean($_POST['name']), 0, 128);

        if(!$link = db()->where('link_id', $_POST['link_id'])->where('user_id', $this->user->user_id)->getOne('links')) {
            die();
        }

        $type = 'email_collector';
        $settings = json_encode([
            'name' => $_POST['name'],
            'image' => '',
            'text_color' => 'black',
            'text_alignment' => 'center',
            'background_color' => 'white',
            'border_shadow_offset_x' => 0,
            'border_shadow_offset_y' => 0,
            'border_shadow_blur' => 20,
            'border_shadow_spread' => 0,
            'border_shadow_color' => '#00000010',
            'border_width' => 0,
            'border_style' => 'solid',
            'border_color' => 'white',
            'border_radius' => 'rounded',
            'animation' => false,
            'animation_runs' => 'repeat-1',
            'icon' => '',

            'email_placeholder' => l('create_biolink_email_collector_modal.email_placeholder_default'),
            'name_placeholder' => l('create_biolink_email_collector_modal.name_placeholder_default'),
            'button_text' => l('create_biolink_email_collector_modal.button_text_default'),
            'success_text' => l('create_biolink_email_collector_modal.success_text_default'),
            'thank_you_url' => '',
            'show_agreement' => false,
            'agreement_url' => '',
            'agreement_text' => '',
            'mailchimp_api' => '',
            'mailchimp_api_list' => '',
            'email_notification' => '',
            'webhook_url' => '',

            /* Display settings */
            'display_countries' => [],
            'display_devices' => [],
            'display_languages' => [],
            'display_operating_systems' => [],
        ]);

        /* Database query */
        db()->insert('biolinks_blocks', [
            'user_id' => $this->user->user_id,
            'link_id' => $_POST['link_id'],
            'type' => $type,
            'settings' => $settings,
            'order' => $this->total_biolink_blocks,
            'datetime' => \Altum\Date::$date,
        ]);

        /* Clear the cache */
        \Altum\Cache::$adapter->deleteItem('link?link_id=' . $_POST['link_id']);

        Response::json('', 'success', ['url' => url('link/' . $_POST['link_id'] . '?tab=links')]);
    }

    private function update_biolink_email_collector() {
        $_POST['biolink_block_id'] = (int) $_POST['biolink_block_id'];
        $_POST['name'] = mb_substr(query_clean($_POST['name']), 0, 128);
        $_POST['border_radius'] = in_array($_POST['border_radius'], ['straight', 'round', 'rounded']) ? query_clean($_POST['border_radius']) : 'rounded';
        $_POST['border_width'] = in_array($_POST['border_width'], [0, 1, 2, 3, 4, 5]) ? (int) $_POST['border_width'] : 0;
        $_POST['border_style'] = in_array($_POST['border_style'], ['solid', 'dashed', 'double', 'inset', 'outset']) ? query_clean($_POST['border_style']) : 'solid';
        $_POST['border_color'] = !preg_match('/#([A-Fa-f0-9]{3,4}){1,2}\b/i', $_POST['border_color']) ? '#000000' : $_POST['border_color'];
        $_POST['border_shadow_offset_x'] = in_array($_POST['border_shadow_offset_x'], range(-20, 20)) ? (int) $_POST['border_shadow_offset_x'] : 0;
        $_POST['border_shadow_offset_y'] = in_array($_POST['border_shadow_offset_y'], range(-20, 20)) ? (int) $_POST['border_shadow_offset_y'] : 0;
        $_POST['border_shadow_blur'] = in_array($_POST['border_shadow_blur'], range(0, 20)) ? (int) $_POST['border_shadow_blur'] : 0;
        $_POST['border_shadow_spread'] = in_array($_POST['border_shadow_spread'], range(0, 10)) ? (int) $_POST['border_shadow_spread'] : 0;
        $_POST['border_shadow_color'] = !preg_match('/#([A-Fa-f0-9]{3,4}){1,2}\b/i', $_POST['border_shadow_color']) ? '#000000' : $_POST['border_shadow_color'];
        $_POST['animation'] = in_array($_POST['animation'], require APP_PATH . 'includes/biolink_animations.php') || $_POST['animation'] == 'false' ? query_clean($_POST['animation']) : false;
        $_POST['animation_runs'] = isset($_POST['animation_runs']) && in_array($_POST['animation_runs'], ['repeat-1', 'repeat-2', 'repeat-3', 'infinite']) ? query_clean($_POST['animation_runs']) : false;
        $_POST['icon'] = query_clean($_POST['icon']);
        $_POST['text_color'] = !preg_match('/#([A-Fa-f0-9]{3,4}){1,2}\b/i', $_POST['text_color']) ? '#000000' : $_POST['text_color'];
        $_POST['text_alignment'] = in_array($_POST['text_alignment'], ['center', 'left', 'right', 'justify']) ? query_clean($_POST['text_alignment']) : 'center';
        $_POST['background_color'] = !preg_match('/#([A-Fa-f0-9]{3,4}){1,2}\b/i', $_POST['background_color']) ? '#ffffff' : $_POST['background_color'];
        $_POST['email_placeholder'] = mb_substr(query_clean($_POST['email_placeholder']), 0, 64);
        $_POST['name_placeholder'] = mb_substr(query_clean($_POST['name_placeholder']), 0, 64);
        $_POST['button_text'] = mb_substr(query_clean($_POST['button_text']), 0, 64);
        $_POST['success_text'] = mb_substr(query_clean($_POST['success_text']), 0, 256);
        $_POST['show_agreement'] = (bool) isset($_POST['show_agreement']);
        $_POST['agreement_url'] = get_url($_POST['agreement_url']);
        $_POST['agreement_text'] = mb_substr(query_clean($_POST['agreement_text']), 0, 256);
        $_POST['mailchimp_api'] = mb_substr(query_clean($_POST['mailchimp_api']), 0, 64);
        $_POST['mailchimp_api_list'] = mb_substr(query_clean($_POST['mailchimp_api_list']), 0, 64);
        $_POST['email_notification'] = mb_substr(query_clean($_POST['email_notification']), 0, 320);
        $_POST['webhook_url'] = get_url($_POST['webhook_url']);
        $_POST['thank_you_url'] = get_url($_POST['thank_you_url']);

        /* Display settings */
        $this->process_display_settings();

        if(!$biolink_block = db()->where('biolink_block_id', $_POST['biolink_block_id'])->where('user_id', $this->user->user_id)->getOne('biolinks_blocks')) {
            die();
        }
        $biolink_block->settings = json_decode($biolink_block->settings);

        /* Image upload */
        $db_image = $this->handle_image_upload($biolink_block->settings->image, 'block_thumbnail_images/', settings()->links->thumbnail_image_size_limit);

        /* Check for the removal of the already uploaded file */
        if(isset($_POST['image_remove'])) {
            /* Offload deleting */
            if(\Altum\Plugin::is_active('offload') && settings()->offload->uploads_url) {
                $s3 = new \Aws\S3\S3Client(get_aws_s3_config());
                $s3->deleteObject([
                    'Bucket' => settings()->offload->storage_name,
                    'Key' => 'uploads/block_thumbnail_images/' . $biolink_block->settings->image,
                ]);
            }

            /* Local deleting */
            else {
                /* Delete current file */
                if(!empty($biolink_block->settings->image) && file_exists(UPLOADS_PATH . 'block_thumbnail_images/' . $biolink_block->settings->image)) {
                    unlink(UPLOADS_PATH . 'block_thumbnail_images/' . $biolink_block->settings->image);
                }
            }
            $db_image = null;
        }

        $image_url = $db_image ? \Altum\Uploads::get_full_url('block_thumbnail_images') . $db_image : null;

        $settings = json_encode([
            'name' => $_POST['name'],
            'image' => $db_image,
            'text_color' => $_POST['text_color'],
            'text_alignment' => $_POST['text_alignment'],
            'background_color' => $_POST['background_color'],
            'border_radius' => $_POST['border_radius'],
            'border_width' => $_POST['border_width'],
            'border_style' => $_POST['border_style'],
            'border_color' => $_POST['border_color'],
            'border_shadow_offset_x' => $_POST['border_shadow_offset_x'],
            'border_shadow_offset_y' => $_POST['border_shadow_offset_y'],
            'border_shadow_blur' => $_POST['border_shadow_blur'],
            'border_shadow_spread' => $_POST['border_shadow_spread'],
            'border_shadow_color' => $_POST['border_shadow_color'],
            'animation' => $_POST['animation'],
            'animation_runs' => $_POST['animation_runs'],
            'icon' => $_POST['icon'],
            'email_placeholder' => $_POST['email_placeholder'],
            'name_placeholder' => $_POST['name_placeholder'],
            'button_text' => $_POST['button_text'],
            'success_text' => $_POST['success_text'],
            'thank_you_url' => $_POST['thank_you_url'],
            'show_agreement' => $_POST['show_agreement'],
            'agreement_url' => $_POST['agreement_url'],
            'agreement_text' => $_POST['agreement_text'],
            'mailchimp_api' => $_POST['mailchimp_api'],
            'mailchimp_api_list' => $_POST['mailchimp_api_list'],
            'email_notification' => $_POST['email_notification'],
            'webhook_url' => $_POST['webhook_url'],

            /* Display settings */
            'display_countries' => $_POST['display_countries'],
            'display_devices' => $_POST['display_devices'],
            'display_languages' => $_POST['display_languages'],
            'display_operating_systems' => $_POST['display_operating_systems'],
        ]);

        db()->where('biolink_block_id', $_POST['biolink_block_id'])->update('biolinks_blocks', [
            'settings' => $settings,
            'start_date' => $_POST['start_date'],
            'end_date' => $_POST['end_date'],
        ]);

        /* Clear the cache */
        \Altum\Cache::$adapter->deleteItem('link?link_id=' . $biolink_block->link_id);

        Response::json(l('global.success_message.update2'), 'success', ['images' => ['image' => $image_url]]);
    }

    private function create_biolink_rss_feed() {
        $_POST['link_id'] = (int) $_POST['link_id'];
        $_POST['location_url'] = get_url($_POST['location_url']);

        if(!$link = db()->where('link_id', $_POST['link_id'])->where('user_id', $this->user->user_id)->getOne('links')) {
            die();
        }

        $this->check_location_url($_POST['location_url']);

        $type = 'rss_feed';
        $settings = json_encode([
            'amount' => 5,
            'open_in_new_tab' => false,
            'text_color' => 'black',
            'text_alignment' => 'center',
            'background_color' => 'white',
            'border_shadow_offset_x' => 0,
            'border_shadow_offset_y' => 0,
            'border_shadow_blur' => 20,
            'border_shadow_spread' => 0,
            'border_shadow_color' => '#00000010',
            'border_width' => 0,
            'border_style' => 'solid',
            'border_color' => 'white',
            'border_radius' => 'rounded',
            'animation' => false,
            'animation_runs' => 'repeat-1',

            /* Display settings */
            'display_countries' => [],
            'display_devices' => [],
            'display_languages' => [],
            'display_operating_systems' => [],
        ]);

        /* Database query */
        db()->insert('biolinks_blocks', [
            'user_id' => $this->user->user_id,
            'link_id' => $_POST['link_id'],
            'type' => $type,
            'location_url' => $_POST['location_url'],
            'settings' => $settings,
            'order' => $this->total_biolink_blocks,
            'datetime' => \Altum\Date::$date,
        ]);

        /* Clear the cache */
        \Altum\Cache::$adapter->deleteItem('link?link_id=' . $_POST['link_id']);

        Response::json('', 'success', ['url' => url('link/' . $_POST['link_id'] . '?tab=links')]);
    }

    private function update_biolink_rss_feed() {
        $_POST['biolink_block_id'] = (int) $_POST['biolink_block_id'];
        $_POST['location_url'] = get_url($_POST['location_url']);
        $_POST['amount'] = (int) query_clean($_POST['amount']);
        $_POST['open_in_new_tab'] = isset($_POST['open_in_new_tab']);
        $_POST['border_radius'] = in_array($_POST['border_radius'], ['straight', 'round', 'rounded']) ? query_clean($_POST['border_radius']) : 'rounded';
        $_POST['border_width'] = in_array($_POST['border_width'], [0, 1, 2, 3, 4, 5]) ? (int) $_POST['border_width'] : 0;
        $_POST['border_style'] = in_array($_POST['border_style'], ['solid', 'dashed', 'double', 'inset', 'outset']) ? query_clean($_POST['border_style']) : 'solid';
        $_POST['border_color'] = !preg_match('/#([A-Fa-f0-9]{3,4}){1,2}\b/i', $_POST['border_color']) ? '#000000' : $_POST['border_color'];
        $_POST['border_shadow_offset_x'] = in_array($_POST['border_shadow_offset_x'], range(-20, 20)) ? (int) $_POST['border_shadow_offset_x'] : 0;
        $_POST['border_shadow_offset_y'] = in_array($_POST['border_shadow_offset_y'], range(-20, 20)) ? (int) $_POST['border_shadow_offset_y'] : 0;
        $_POST['border_shadow_blur'] = in_array($_POST['border_shadow_blur'], range(0, 20)) ? (int) $_POST['border_shadow_blur'] : 0;
        $_POST['border_shadow_spread'] = in_array($_POST['border_shadow_spread'], range(0, 10)) ? (int) $_POST['border_shadow_spread'] : 0;
        $_POST['border_shadow_color'] = !preg_match('/#([A-Fa-f0-9]{3,4}){1,2}\b/i', $_POST['border_shadow_color']) ? '#000000' : $_POST['border_shadow_color'];
        $_POST['animation'] = in_array($_POST['animation'], require APP_PATH . 'includes/biolink_animations.php') || $_POST['animation'] == 'false' ? query_clean($_POST['animation']) : false;
        $_POST['animation_runs'] = isset($_POST['animation_runs']) && in_array($_POST['animation_runs'], ['repeat-1', 'repeat-2', 'repeat-3', 'infinite']) ? query_clean($_POST['animation_runs']) : false;
        $_POST['text_color'] = !preg_match('/#([A-Fa-f0-9]{3,4}){1,2}\b/i', $_POST['text_color']) ? '#000000' : $_POST['text_color'];
        $_POST['text_alignment'] = in_array($_POST['text_alignment'], ['center', 'left', 'right', 'justify']) ? query_clean($_POST['text_alignment']) : 'center';
        $_POST['background_color'] = !preg_match('/#([A-Fa-f0-9]{3,4}){1,2}\b/i', $_POST['background_color']) ? '#ffffff' : $_POST['background_color'];

        /* Display settings */
        $this->process_display_settings();

        if(!$biolink_block = db()->where('biolink_block_id', $_POST['biolink_block_id'])->where('user_id', $this->user->user_id)->getOne('biolinks_blocks')) {
            die();
        }

        $this->check_location_url($_POST['location_url']);

        $settings = json_encode([
            'amount' => $_POST['amount'],
            'open_in_new_tab' => $_POST['open_in_new_tab'],
            'text_color' => $_POST['text_color'],
            'text_alignment' => $_POST['text_alignment'],
            'background_color' => $_POST['background_color'],
            'border_radius' => $_POST['border_radius'],
            'border_width' => $_POST['border_width'],
            'border_style' => $_POST['border_style'],
            'border_color' => $_POST['border_color'],
            'border_shadow_offset_x' => $_POST['border_shadow_offset_x'],
            'border_shadow_offset_y' => $_POST['border_shadow_offset_y'],
            'border_shadow_blur' => $_POST['border_shadow_blur'],
            'border_shadow_spread' => $_POST['border_shadow_spread'],
            'border_shadow_color' => $_POST['border_shadow_color'],
            'animation' => $_POST['animation'],
            'animation_runs' => $_POST['animation_runs'],

            /* Display settings */
            'display_countries' => $_POST['display_countries'],
            'display_devices' => $_POST['display_devices'],
            'display_languages' => $_POST['display_languages'],
            'display_operating_systems' => $_POST['display_operating_systems'],
        ]);

        /* Database query */
        db()->where('biolink_block_id', $_POST['biolink_block_id'])->update('biolinks_blocks', [
            'location_url' => $_POST['location_url'],
            'settings' => $settings,
            'start_date' => $_POST['start_date'],
            'end_date' => $_POST['end_date'],
        ]);

        /* Clear the cache */
        \Altum\Cache::$adapter->deleteItem('link?link_id=' . $biolink_block->link_id);
        \Altum\Cache::$adapter->deleteItem('biolink_block?block_id=' . $biolink_block->biolink_block_id . '&type=rss_feed');

        Response::json(l('global.success_message.update2'), 'success');
    }

    private function create_biolink_custom_html() {
        $_POST['link_id'] = (int) $_POST['link_id'];
        $_POST['html'] = mb_substr(trim($_POST['html']), 0, $this->biolink_blocks['custom_html']['max_length']);

        if(!$link = db()->where('link_id', $_POST['link_id'])->where('user_id', $this->user->user_id)->getOne('links')) {
            die();
        }

        $type = 'custom_html';
        $settings = json_encode([
            'html' => $_POST['html'],

            /* Display settings */
            'display_countries' => [],
            'display_devices' => [],
            'display_languages' => [],
            'display_operating_systems' => [],
        ]);

        /* Database query */
        db()->insert('biolinks_blocks', [
            'user_id' => $this->user->user_id,
            'link_id' => $_POST['link_id'],
            'type' => $type,
            'location_url' => null,
            'settings' => $settings,
            'order' => $this->total_biolink_blocks,
            'datetime' => \Altum\Date::$date,
        ]);

        /* Clear the cache */
        \Altum\Cache::$adapter->deleteItem('link?link_id=' . $_POST['link_id']);

        Response::json('', 'success', ['url' => url('link/' . $_POST['link_id'] . '?tab=links')]);
    }

    private function update_biolink_custom_html() {
        $_POST['biolink_block_id'] = (int) $_POST['biolink_block_id'];
        $_POST['html'] = mb_substr(trim($_POST['html']), 0, $this->biolink_blocks['custom_html']['max_length']);

        /* Display settings */
        $this->process_display_settings();

        if(!$biolink_block = db()->where('biolink_block_id', $_POST['biolink_block_id'])->where('user_id', $this->user->user_id)->getOne('biolinks_blocks')) {
            die();
        }

        $settings = json_encode([
            'html' => $_POST['html'],

            /* Display settings */
            'display_countries' => $_POST['display_countries'],
            'display_devices' => $_POST['display_devices'],
            'display_languages' => $_POST['display_languages'],
            'display_operating_systems' => $_POST['display_operating_systems'],
        ]);

        /* Database query */
        db()->where('biolink_block_id', $_POST['biolink_block_id'])->update('biolinks_blocks', [
            'settings' => $settings,
            'start_date' => $_POST['start_date'],
            'end_date' => $_POST['end_date'],
        ]);

        /* Clear the cache */
        \Altum\Cache::$adapter->deleteItem('link?link_id=' . $biolink_block->link_id);

        Response::json(l('global.success_message.update2'), 'success');
    }

    private function create_biolink_vcard() {
        $_POST['link_id'] = (int) $_POST['link_id'];
        $_POST['name'] = mb_substr(query_clean($_POST['name']), 0, 128);

        if(!$link = db()->where('link_id', $_POST['link_id'])->where('user_id', $this->user->user_id)->getOne('links')) {
            die();
        }

        $type = 'vcard';
        $settings = [
            'name' => $_POST['name'],
            'image' => '',
            'first_name' => '',
            'last_name' => '',
            'text_color' => 'black',
            'text_alignment' => 'center',
            'background_color' => 'white',
            'border_shadow_offset_x' => 0,
            'border_shadow_offset_y' => 0,
            'border_shadow_blur' => 20,
            'border_shadow_spread' => 0,
            'border_shadow_color' => '#00000010',
            'border_width' => 0,
            'border_style' => 'solid',
            'border_color' => 'white',
            'border_radius' => 'rounded',
            'animation' => false,
            'animation_runs' => 'repeat-1',
            'icon' => '',
            'vcard_socials' => [],
            'vcard_phone_numbers' => [],

            /* Display settings */
            'display_countries' => [],
            'display_devices' => [],
            'display_languages' => [],
            'display_operating_systems' => [],
        ];
        $settings = json_encode($settings);

        /* Database query */
        db()->insert('biolinks_blocks', [
            'user_id' => $this->user->user_id,
            'link_id' => $_POST['link_id'],
            'type' => $type,
            'location_url' => null,
            'settings' => $settings,
            'order' => $this->total_biolink_blocks,
            'datetime' => \Altum\Date::$date,
        ]);

        /* Clear the cache */
        \Altum\Cache::$adapter->deleteItem('link?link_id=' . $_POST['link_id']);

        Response::json('', 'success', ['url' => url('link/' . $_POST['link_id'] . '?tab=links')]);
    }

    private function update_biolink_vcard() {
        $settings = [];

        $_POST['biolink_block_id'] = (int) $_POST['biolink_block_id'];
        $_POST['name'] = mb_substr(query_clean($_POST['name']), 0, 128);
        $_POST['border_radius'] = in_array($_POST['border_radius'], ['straight', 'round', 'rounded']) ? query_clean($_POST['border_radius']) : 'rounded';
        $_POST['border_width'] = in_array($_POST['border_width'], [0, 1, 2, 3, 4, 5]) ? (int) $_POST['border_width'] : 0;
        $_POST['border_style'] = in_array($_POST['border_style'], ['solid', 'dashed', 'double', 'inset', 'outset']) ? query_clean($_POST['border_style']) : 'solid';
        $_POST['border_color'] = !preg_match('/#([A-Fa-f0-9]{3,4}){1,2}\b/i', $_POST['border_color']) ? '#000000' : $_POST['border_color'];
        $_POST['border_shadow_offset_x'] = in_array($_POST['border_shadow_offset_x'], range(-20, 20)) ? (int) $_POST['border_shadow_offset_x'] : 0;
        $_POST['border_shadow_offset_y'] = in_array($_POST['border_shadow_offset_y'], range(-20, 20)) ? (int) $_POST['border_shadow_offset_y'] : 0;
        $_POST['border_shadow_blur'] = in_array($_POST['border_shadow_blur'], range(0, 20)) ? (int) $_POST['border_shadow_blur'] : 0;
        $_POST['border_shadow_spread'] = in_array($_POST['border_shadow_spread'], range(0, 10)) ? (int) $_POST['border_shadow_spread'] : 0;
        $_POST['border_shadow_color'] = !preg_match('/#([A-Fa-f0-9]{3,4}){1,2}\b/i', $_POST['border_shadow_color']) ? '#000000' : $_POST['border_shadow_color'];
        $_POST['animation'] = in_array($_POST['animation'], require APP_PATH . 'includes/biolink_animations.php') || $_POST['animation'] == 'false' ? query_clean($_POST['animation']) : false;
        $_POST['animation_runs'] = isset($_POST['animation_runs']) && in_array($_POST['animation_runs'], ['repeat-1', 'repeat-2', 'repeat-3', 'infinite']) ? query_clean($_POST['animation_runs']) : false;
        $_POST['text_color'] = !preg_match('/#([A-Fa-f0-9]{3,4}){1,2}\b/i', $_POST['text_color']) ? '#000000' : $_POST['text_color'];
        $_POST['text_alignment'] = in_array($_POST['text_alignment'], ['center', 'left', 'right', 'justify']) ? query_clean($_POST['text_alignment']) : 'center';
        $_POST['background_color'] = !preg_match('/#([A-Fa-f0-9]{3,4}){1,2}\b/i', $_POST['background_color']) ? '#ffffff' : $_POST['background_color'];
        $_POST['icon'] = query_clean($_POST['icon']);

        $settings['vcard_first_name'] = $_POST['vcard_first_name'] = mb_substr(query_clean($_POST['vcard_first_name']), 0, $this->biolink_blocks['vcard']['fields']['first_name']['max_length']);
        $settings['vcard_last_name'] = $_POST['vcard_last_name'] = mb_substr(query_clean($_POST['vcard_last_name']), 0, $this->biolink_blocks['vcard']['fields']['last_name']['max_length']);
        $settings['vcard_email'] = $_POST['vcard_email'] = mb_substr(query_clean($_POST['vcard_email']), 0, $this->biolink_blocks['vcard']['fields']['email']['max_length']);
        $settings['vcard_url'] = $_POST['vcard_url'] = mb_substr(query_clean($_POST['vcard_url']), 0, $this->biolink_blocks['vcard']['fields']['url']['max_length']);
        $settings['vcard_company'] = $_POST['vcard_company'] = mb_substr(query_clean($_POST['vcard_company']), 0, $this->biolink_blocks['vcard']['fields']['company']['max_length']);
        $settings['vcard_job_title'] = $_POST['vcard_job_title'] = mb_substr(query_clean($_POST['vcard_job_title']), 0, $this->biolink_blocks['vcard']['fields']['job_title']['max_length']);
        $settings['vcard_birthday'] = $_POST['vcard_birthday'] = mb_substr(query_clean($_POST['vcard_birthday']), 0, $this->biolink_blocks['vcard']['fields']['birthday']['max_length']);
        $settings['vcard_street'] = $_POST['vcard_street'] = mb_substr(query_clean($_POST['vcard_street']), 0, $this->biolink_blocks['vcard']['fields']['street']['max_length']);
        $settings['vcard_city'] = $_POST['vcard_city'] = mb_substr(query_clean($_POST['vcard_city']), 0, $this->biolink_blocks['vcard']['fields']['city']['max_length']);
        $settings['vcard_zip'] = $_POST['vcard_zip'] = mb_substr(query_clean($_POST['vcard_zip']), 0, $this->biolink_blocks['vcard']['fields']['zip']['max_length']);
        $settings['vcard_region'] = $_POST['vcard_region'] = mb_substr(query_clean($_POST['vcard_region']), 0, $this->biolink_blocks['vcard']['fields']['region']['max_length']);
        $settings['vcard_country'] = $_POST['vcard_country'] = mb_substr(query_clean($_POST['vcard_country']), 0, $this->biolink_blocks['vcard']['fields']['country']['max_length']);
        $settings['vcard_note'] = $_POST['vcard_note'] = mb_substr(query_clean($_POST['vcard_note']), 0, $this->biolink_blocks['vcard']['fields']['note']['max_length']);

        /* Phone numbers */
        if(!isset($_POST['vcard_phone_numbers'])) {
            $_POST['vcard_phone_numbers'] = [];
        }
        $vcard_phone_numbers = [];
        foreach($_POST['vcard_phone_numbers'] as $key => $value) {
            if(empty(trim($value))) continue;
            if($key >= 20) continue;

            $vcard_phone_numbers[] = mb_substr(input_clean($value), 0, $this->biolink_blocks['vcard']['fields']['phone_number']['max_length']);
        }
        $settings['vcard_phone_numbers'] = $vcard_phone_numbers;

        /* Socials */
        if(!isset($_POST['vcard_social_label'])) {
            $_POST['vcard_social_label'] = [];
            $_POST['vcard_social_value'] = [];
        }
        $vcard_socials = [];
        foreach($_POST['vcard_social_label'] as $key => $value) {
            if(empty(trim($value))) continue;
            if($key >= 20) continue;

            $vcard_socials[] = [
                'label' => mb_substr(query_clean($value), 0, $this->biolink_blocks['vcard']['fields']['social_value']['max_length']),
                'value' => mb_substr(input_clean($_POST['vcard_social_value'][$key]), 0, $this->biolink_blocks['vcard']['fields']['social_value']['max_length'])
            ];
        }
        $settings['vcard_socials'] = $vcard_socials;

        /* Display settings */
        $this->process_display_settings();

        if(!$biolink_block = db()->where('biolink_block_id', $_POST['biolink_block_id'])->where('user_id', $this->user->user_id)->getOne('biolinks_blocks')) {
            die();
        }
        $biolink_block->settings = json_decode($biolink_block->settings);

        /* Image upload */
        $db_image = $this->handle_image_upload($biolink_block->settings->image, 'block_thumbnail_images/', settings()->links->thumbnail_image_size_limit);

        /* Check for the removal of the already uploaded file */
        if(isset($_POST['image_remove'])) {
            /* Offload deleting */
            if(\Altum\Plugin::is_active('offload') && settings()->offload->uploads_url) {
                $s3 = new \Aws\S3\S3Client(get_aws_s3_config());
                $s3->deleteObject([
                    'Bucket' => settings()->offload->storage_name,
                    'Key' => 'uploads/block_thumbnail_images/' . $biolink_block->settings->image,
                ]);
            }

            /* Local deleting */
            else {
                /* Delete current file */
                if(!empty($biolink_block->settings->image) && file_exists(UPLOADS_PATH . 'block_thumbnail_images/' . $biolink_block->settings->image)) {
                    unlink(UPLOADS_PATH . 'block_thumbnail_images/' . $biolink_block->settings->image);
                }
            }
            $db_image = null;
        }

        $image_url = $db_image ? \Altum\Uploads::get_full_url('block_thumbnail_images') . $db_image : null;

        /* Vcard avatar */
        $settings['vcard_avatar'] = $this->handle_file_upload($biolink_block->settings->vcard_avatar, 'vcard_avatar', 'vcard_avatar_remove', \Altum\Uploads::get_whitelisted_file_extensions('vcards_avatars'), 'avatars/', 0.75);

        /* Check for the removal of the already uploaded file */
        if(isset($_POST['vcard_avatar_remove'])) {
            /* Offload deleting */
            if(\Altum\Plugin::is_active('offload') && settings()->offload->uploads_url) {
                $s3 = new \Aws\S3\S3Client(get_aws_s3_config());
                $s3->deleteObject([
                    'Bucket' => settings()->offload->storage_name,
                    'Key' => 'uploads/avatars/' . $biolink_block->settings->vcard_avatar,
                ]);
            }

            /* Local deleting */
            else {
                /* Delete current file */
                if(!empty($biolink_block->settings->vcard_avatar) && file_exists(UPLOADS_PATH . 'avatars/' . $biolink_block->settings->vcard_avatar)) {
                    unlink(UPLOADS_PATH . 'avatars/' . $biolink_block->settings->vcard_avatar);
                }
            }
            $settings['vcard_avatar'] = null;
        }

        $vcard_avatar_url = $settings['vcard_avatar'] ? \Altum\Uploads::get_full_url('avatars') . $settings['vcard_avatar'] : null;

        $settings = array_merge($settings, [
            'name' => $_POST['name'],
            'image' => $db_image,
            'text_color' => $_POST['text_color'],
            'text_alignment' => $_POST['text_alignment'],
            'background_color' => $_POST['background_color'],
            'border_radius' => $_POST['border_radius'],
            'border_width' => $_POST['border_width'],
            'border_style' => $_POST['border_style'],
            'border_color' => $_POST['border_color'],
            'border_shadow_offset_x' => $_POST['border_shadow_offset_x'],
            'border_shadow_offset_y' => $_POST['border_shadow_offset_y'],
            'border_shadow_blur' => $_POST['border_shadow_blur'],
            'border_shadow_spread' => $_POST['border_shadow_spread'],
            'border_shadow_color' => $_POST['border_shadow_color'],
            'animation' => $_POST['animation'],
            'animation_runs' => $_POST['animation_runs'],
            'icon' => $_POST['icon'],

            /* Display settings */
            'display_countries' => $_POST['display_countries'],
            'display_devices' => $_POST['display_devices'],
            'display_languages' => $_POST['display_languages'],
            'display_operating_systems' => $_POST['display_operating_systems'],
        ]);
        $settings = json_encode($settings);

        /* Database query */
        db()->where('biolink_block_id', $_POST['biolink_block_id'])->update('biolinks_blocks', [
            'settings' => $settings,
            'start_date' => $_POST['start_date'],
            'end_date' => $_POST['end_date'],
        ]);

        /* Clear the cache */
        \Altum\Cache::$adapter->deleteItem('link?link_id=' . $biolink_block->link_id);

        Response::json(l('global.success_message.update2'), 'success',
            [
                'images' => [
                    'image' => $image_url,
                    'vcard_avatar' => $vcard_avatar_url
                ]
            ]
        );
    }

    private function create_biolink_image() {
        $_POST['link_id'] = (int) $_POST['link_id'];
        $_POST['location_url'] = get_url($_POST['location_url']);
        $_POST['image_alt'] = mb_substr(query_clean($_POST['image_alt']), 0, 100);

        if(!$link = db()->where('link_id', $_POST['link_id'])->where('user_id', $this->user->user_id)->getOne('links')) {
            die();
        }

        $this->check_location_url($_POST['location_url'], true);

        /* Image upload */
        $db_image = $this->handle_image_upload(null, 'block_images/', settings()->links->image_size_limit);

        $type = 'image';
        $settings = json_encode([
            'image' => $db_image,
            'image_alt' => $_POST['image_alt'],
            'open_in_new_tab' => false,

            /* Display settings */
            'display_countries' => [],
            'display_devices' => [],
            'display_languages' => [],
            'display_operating_systems' => [],
        ]);

        /* Database query */
        db()->insert('biolinks_blocks', [
            'user_id' => $this->user->user_id,
            'link_id' => $_POST['link_id'],
            'type' => $type,
            'location_url' => $_POST['location_url'],
            'settings' => $settings,
            'order' => $this->total_biolink_blocks,
            'datetime' => \Altum\Date::$date,
        ]);

        /* Clear the cache */
        \Altum\Cache::$adapter->deleteItem('link?link_id=' . $_POST['link_id']);

        Response::json('', 'success', ['url' => url('link/' . $_POST['link_id'] . '?tab=links')]);
    }

    private function update_biolink_image() {
        $_POST['biolink_block_id'] = (int) $_POST['biolink_block_id'];
        $_POST['location_url'] = get_url($_POST['location_url']);
        $_POST['image_alt'] = mb_substr(query_clean($_POST['image_alt']), 0, 100);
        $_POST['open_in_new_tab'] = isset($_POST['open_in_new_tab']);

        /* Display settings */
        $this->process_display_settings();

        if(!$biolink_block = db()->where('biolink_block_id', $_POST['biolink_block_id'])->where('user_id', $this->user->user_id)->getOne('biolinks_blocks')) {
            die();
        }
        $biolink_block->settings = json_decode($biolink_block->settings);

        $this->check_location_url($_POST['location_url'], true);

        /* Image upload */
        $db_image = $this->handle_image_upload($biolink_block->settings->image, 'block_images/', settings()->links->image_size_limit);

        $image_url = $db_image ? \Altum\Uploads::get_full_url('block_images') . $db_image : null;

        $settings = json_encode([
            'image' => $db_image,
            'image_alt' => $_POST['image_alt'],
            'open_in_new_tab' => $_POST['open_in_new_tab'],

            /* Display settings */
            'display_countries' => $_POST['display_countries'],
            'display_devices' => $_POST['display_devices'],
            'display_languages' => $_POST['display_languages'],
            'display_operating_systems' => $_POST['display_operating_systems'],
        ]);

        /* Database query */
        db()->where('biolink_block_id', $_POST['biolink_block_id'])->update('biolinks_blocks', [
            'location_url' => $_POST['location_url'],
            'settings' => $settings,
            'start_date' => $_POST['start_date'],
            'end_date' => $_POST['end_date'],
        ]);

        /* Clear the cache */
        \Altum\Cache::$adapter->deleteItem('link?link_id=' . $biolink_block->link_id);

        Response::json(l('global.success_message.update2'), 'success', ['images' => ['image' => $image_url]]);
    }

    private function create_biolink_image_grid() {
        $_POST['link_id'] = (int) $_POST['link_id'];
        $_POST['name'] = mb_substr(query_clean($_POST['name']), 0, 128);
        $_POST['location_url'] = get_url($_POST['location_url']);
        $_POST['image_alt'] = mb_substr(query_clean($_POST['image_alt']), 0, 100);

        if(!$link = db()->where('link_id', $_POST['link_id'])->where('user_id', $this->user->user_id)->getOne('links')) {
            die();
        }

        $this->check_location_url($_POST['location_url'], true);

        $db_image = $this->handle_image_upload(null, 'block_images/', settings()->links->image_size_limit);

        $type = 'image_grid';
        $settings = json_encode([
            'name' => $_POST['name'],
            'image' => $db_image,
            'image_alt' => $_POST['image_alt'],
            'open_in_new_tab' => false,

            /* Display settings */
            'display_countries' => [],
            'display_devices' => [],
            'display_languages' => [],
            'display_operating_systems' => [],
        ]);

        /* Database query */
        db()->insert('biolinks_blocks', [
            'user_id' => $this->user->user_id,
            'link_id' => $_POST['link_id'],
            'type' => $type,
            'location_url' => $_POST['location_url'],
            'settings' => $settings,
            'order' => $this->total_biolink_blocks,
            'datetime' => \Altum\Date::$date,
        ]);

        /* Clear the cache */
        \Altum\Cache::$adapter->deleteItem('link?link_id=' . $_POST['link_id']);

        Response::json('', 'success', ['url' => url('link/' . $_POST['link_id'] . '?tab=links')]);
    }

    private function update_biolink_image_grid() {
        $_POST['biolink_block_id'] = (int) $_POST['biolink_block_id'];
        $_POST['name'] = mb_substr(query_clean($_POST['name']), 0, 128);
        $_POST['location_url'] = get_url($_POST['location_url']);
        $_POST['image_alt'] = mb_substr(query_clean($_POST['image_alt']), 0, 100);
        $_POST['open_in_new_tab'] = isset($_POST['open_in_new_tab']);

        /* Display settings */
        $this->process_display_settings();

        if(!$biolink_block = db()->where('biolink_block_id', $_POST['biolink_block_id'])->where('user_id', $this->user->user_id)->getOne('biolinks_blocks')) {
            die();
        }
        $biolink_block->settings = json_decode($biolink_block->settings);

        $this->check_location_url($_POST['location_url'], true);

        /* Image upload */
        $db_image = $this->handle_image_upload($biolink_block->settings->image, 'block_images/', settings()->links->image_size_limit);

        $image_url = $db_image ? \Altum\Uploads::get_full_url('block_images') . $db_image : null;

        $settings = json_encode([
            'name' => $_POST['name'],
            'image' => $db_image,
            'image_alt' => $_POST['image_alt'],
            'open_in_new_tab' => $_POST['open_in_new_tab'],

            /* Display settings */
            'display_countries' => $_POST['display_countries'],
            'display_devices' => $_POST['display_devices'],
            'display_languages' => $_POST['display_languages'],
            'display_operating_systems' => $_POST['display_operating_systems'],
        ]);

        /* Database query */
        db()->where('biolink_block_id', $_POST['biolink_block_id'])->update('biolinks_blocks', [
            'location_url' => $_POST['location_url'],
            'settings' => $settings,
            'start_date' => $_POST['start_date'],
            'end_date' => $_POST['end_date'],
        ]);

        /* Clear the cache */
        \Altum\Cache::$adapter->deleteItem('link?link_id=' . $biolink_block->link_id);

        Response::json(l('global.success_message.update2'), 'success', ['images' => ['image' => $image_url]]);
    }

    private function create_biolink_divider() {
        $_POST['link_id'] = (int) $_POST['link_id'];
        $_POST['margin_top'] = $_POST['margin_top'] > 7 || $_POST['margin_top'] < 0 ? 3 : (int) $_POST['margin_top'];
        $_POST['margin_bottom'] = $_POST['margin_bottom'] > 7 || $_POST['margin_bottom'] < 0 ? 3 : (int) $_POST['margin_bottom'];

        if(!$link = db()->where('link_id', $_POST['link_id'])->where('user_id', $this->user->user_id)->getOne('links')) {
            die();
        }

        $type = 'divider';
        $settings = json_encode([
            'margin_top' => $_POST['margin_top'],
            'margin_bottom' => $_POST['margin_bottom'],
            'background_color' => 'white',
            'icon' => 'fa fa-infinity',

            /* Display settings */
            'display_countries' => [],
            'display_devices' => [],
            'display_languages' => [],
            'display_operating_systems' => [],
        ]);

        /* Database query */
        db()->insert('biolinks_blocks', [
            'user_id' => $this->user->user_id,
            'link_id' => $_POST['link_id'],
            'type' => $type,
            'location_url' => null,
            'settings' => $settings,
            'order' => $this->total_biolink_blocks,
            'datetime' => \Altum\Date::$date,
        ]);

        /* Clear the cache */
        \Altum\Cache::$adapter->deleteItem('link?link_id=' . $_POST['link_id']);

        Response::json('', 'success', ['url' => url('link/' . $_POST['link_id'] . '?tab=links')]);
    }

    private function update_biolink_divider() {
        $_POST['biolink_block_id'] = (int) $_POST['biolink_block_id'];
        $_POST['margin_top'] = $_POST['margin_top'] > 7 || $_POST['margin_top'] < 0 ? 3 : (int) $_POST['margin_top'];
        $_POST['margin_bottom'] = $_POST['margin_bottom'] > 7 || $_POST['margin_bottom'] < 0 ? 3 : (int) $_POST['margin_bottom'];
        $_POST['background_color'] = !preg_match('/#([A-Fa-f0-9]{3,4}){1,2}\b/i', $_POST['background_color']) ? '#ffffff' : $_POST['background_color'];
        $_POST['icon'] = query_clean($_POST['icon']);

        /* Display settings */
        $this->process_display_settings();

        if(!$biolink_block = db()->where('biolink_block_id', $_POST['biolink_block_id'])->where('user_id', $this->user->user_id)->getOne('biolinks_blocks')) {
            die();
        }

        $settings = json_encode([
            'margin_top' => $_POST['margin_top'],
            'margin_bottom' => $_POST['margin_bottom'],
            'background_color' => $_POST['background_color'],
            'icon' => $_POST['icon'],

            /* Display settings */
            'display_countries' => $_POST['display_countries'],
            'display_devices' => $_POST['display_devices'],
            'display_languages' => $_POST['display_languages'],
            'display_operating_systems' => $_POST['display_operating_systems'],
        ]);

        /* Database query */
        db()->where('biolink_block_id', $_POST['biolink_block_id'])->update('biolinks_blocks', [
            'settings' => $settings,
            'start_date' => $_POST['start_date'],
            'end_date' => $_POST['end_date'],
        ]);

        /* Clear the cache */
        \Altum\Cache::$adapter->deleteItem('link?link_id=' . $biolink_block->link_id);

        Response::json(l('global.success_message.update2'), 'success');
    }

    private function create_biolink_list() {
        $_POST['link_id'] = (int) $_POST['link_id'];
        $_POST['text'] = mb_substr(input_clean($_POST['text']), 0, 2048);

        if(!$link = db()->where('link_id', $_POST['link_id'])->where('user_id', $this->user->user_id)->getOne('links')) {
            die();
        }

        $type = 'list';
        $settings = json_encode([
            'text' => $_POST['text'],
            'list' => 'fa fa-check-circle',
            'text_color' => 'black',
            'text_alignment' => 'center',
            'background_color' => '#FFFFFF',
            'border_shadow_offset_x' => 0,
            'border_shadow_offset_y' => 0,
            'border_shadow_blur' => 20,
            'border_shadow_spread' => 0,
            'border_shadow_color' => '#00000010',
            'border_width' => 0,
            'border_style' => 'solid',
            'border_color' => 'white',
            'border_radius' => 'rounded',
            'margin_items_y' => '1',
            'margin_items_x' => '1',

            /* Display settings */
            'display_countries' => [],
            'display_devices' => [],
            'display_languages' => [],
            'display_operating_systems' => [],
        ]);

        /* Database query */
        db()->insert('biolinks_blocks', [
            'user_id' => $this->user->user_id,
            'link_id' => $_POST['link_id'],
            'type' => $type,
            'location_url' => null,
            'settings' => $settings,
            'order' => $this->total_biolink_blocks,
            'datetime' => \Altum\Date::$date,
        ]);

        /* Clear the cache */
        \Altum\Cache::$adapter->deleteItem('link?link_id=' . $_POST['link_id']);

        Response::json('', 'success', ['url' => url('link/' . $_POST['link_id'] . '?tab=links')]);
    }

    private function update_biolink_list() {
        $_POST['biolink_block_id'] = (int) $_POST['biolink_block_id'];
        $_POST['text'] = mb_substr(input_clean($_POST['text']), 0, 2048);
        $_POST['icon'] = query_clean($_POST['icon']);
        $_POST['border_radius'] = in_array($_POST['border_radius'], ['straight', 'round', 'rounded']) ? query_clean($_POST['border_radius']) : 'rounded';
        $_POST['border_width'] = in_array($_POST['border_width'], [0, 1, 2, 3, 4, 5]) ? (int) $_POST['border_width'] : 0;
        $_POST['border_style'] = in_array($_POST['border_style'], ['solid', 'dashed', 'double', 'inset', 'outset']) ? query_clean($_POST['border_style']) : 'solid';
        $_POST['border_color'] = !preg_match('/#([A-Fa-f0-9]{3,4}){1,2}\b/i', $_POST['border_color']) ? '#000000' : $_POST['border_color'];
        $_POST['border_shadow_offset_x'] = in_array($_POST['border_shadow_offset_x'], range(-20, 20)) ? (int) $_POST['border_shadow_offset_x'] : 0;
        $_POST['border_shadow_offset_y'] = in_array($_POST['border_shadow_offset_y'], range(-20, 20)) ? (int) $_POST['border_shadow_offset_y'] : 0;
        $_POST['border_shadow_blur'] = in_array($_POST['border_shadow_blur'], range(0, 20)) ? (int) $_POST['border_shadow_blur'] : 0;
        $_POST['border_shadow_spread'] = in_array($_POST['border_shadow_spread'], range(0, 10)) ? (int) $_POST['border_shadow_spread'] : 0;
        $_POST['border_shadow_color'] = !preg_match('/#([A-Fa-f0-9]{3,4}){1,2}\b/i', $_POST['border_shadow_color']) ? '#000000' : $_POST['border_shadow_color'];
        $_POST['text_color'] = !preg_match('/#([A-Fa-f0-9]{3,4}){1,2}\b/i', $_POST['text_color']) ? '#000000' : $_POST['text_color'];
        $_POST['text_alignment'] = in_array($_POST['text_alignment'], ['center', 'left', 'right', 'justify']) ? query_clean($_POST['text_alignment']) : 'center';
        $_POST['background_color'] = !preg_match('/#([A-Fa-f0-9]{3,4}){1,2}\b/i', $_POST['background_color']) ? '#ffffff' : $_POST['background_color'];
        $_POST['margin_items_y'] = $_POST['margin_items_y'] > 5 || $_POST['margin_items_y'] < 0 ? 2 : (int) $_POST['margin_items_y'];
        $_POST['margin_items_x'] = $_POST['margin_items_x'] > 3 || $_POST['margin_items_x'] < 0 ? 1 : (int) $_POST['margin_items_x'];

        /* Display settings */
        $this->process_display_settings();

        if(!$biolink_block = db()->where('biolink_block_id', $_POST['biolink_block_id'])->where('user_id', $this->user->user_id)->getOne('biolinks_blocks')) {
            die();
        }

        $settings = json_encode([
            'text' => $_POST['text'],
            'icon' => $_POST['icon'],
            'text_color' => $_POST['text_color'],
            'text_alignment' => $_POST['text_alignment'],
            'background_color' => $_POST['background_color'],
            'border_radius' => $_POST['border_radius'],
            'border_width' => $_POST['border_width'],
            'border_style' => $_POST['border_style'],
            'border_color' => $_POST['border_color'],
            'border_shadow_offset_x' => $_POST['border_shadow_offset_x'],
            'border_shadow_offset_y' => $_POST['border_shadow_offset_y'],
            'border_shadow_blur' => $_POST['border_shadow_blur'],
            'border_shadow_spread' => $_POST['border_shadow_spread'],
            'border_shadow_color' => $_POST['border_shadow_color'],
            'margin_items_y' => $_POST['margin_items_y'],
            'margin_items_x' => $_POST['margin_items_x'],

            /* Display settings */
            'display_countries' => $_POST['display_countries'],
            'display_devices' => $_POST['display_devices'],
            'display_languages' => $_POST['display_languages'],
            'display_operating_systems' => $_POST['display_operating_systems'],
        ]);

        /* Database query */
        db()->where('biolink_block_id', $_POST['biolink_block_id'])->update('biolinks_blocks', [
            'settings' => $settings,
            'start_date' => $_POST['start_date'],
            'end_date' => $_POST['end_date'],
        ]);

        /* Clear the cache */
        \Altum\Cache::$adapter->deleteItem('link?link_id=' . $biolink_block->link_id);

        Response::json(l('global.success_message.update2'), 'success');
    }

    private function create_biolink_alert() {
        $_POST['link_id'] = (int) $_POST['link_id'];
        $_POST['text'] = mb_substr(input_clean($_POST['text']), 0, 2048);

        if(!$link = db()->where('link_id', $_POST['link_id'])->where('user_id', $this->user->user_id)->getOne('links')) {
            die();
        }

        $type = 'alert';
        $settings = json_encode([
            'text' => $_POST['text'],
            'icon' => 'fa fa-check-circle',
            'open_in_new_tab' => false,
            'text_color' => '#ffffff',
            'text_alignment' => 'left',
            'background_color' => '#FFFFFF38',
            'border_width' => 1,
            'border_style' => 'solid',
            'border_color' => '#FFFFFF8C',
            'border_radius' => 'rounded',
            'display_close_button' => true,
            'alert_pause_after_closed' => 60,

            /* Display settings */
            'display_countries' => [],
            'display_devices' => [],
            'display_languages' => [],
            'display_operating_systems' => [],
        ]);

        /* Database query */
        db()->insert('biolinks_blocks', [
            'user_id' => $this->user->user_id,
            'link_id' => $_POST['link_id'],
            'type' => $type,
            'location_url' => null,
            'settings' => $settings,
            'order' => $this->total_biolink_blocks,
            'datetime' => \Altum\Date::$date,
        ]);

        /* Clear the cache */
        \Altum\Cache::$adapter->deleteItem('link?link_id=' . $_POST['link_id']);

        Response::json('', 'success', ['url' => url('link/' . $_POST['link_id'] . '?tab=links')]);
    }

    private function update_biolink_alert() {
        $_POST['biolink_block_id'] = (int) $_POST['biolink_block_id'];
        $_POST['text'] = mb_substr(input_clean($_POST['text']), 0, 2048);
        $_POST['icon'] = query_clean($_POST['icon']);
        $_POST['border_radius'] = in_array($_POST['border_radius'], ['straight', 'round', 'rounded']) ? query_clean($_POST['border_radius']) : 'rounded';
        $_POST['border_width'] = in_array($_POST['border_width'], [0, 1, 2, 3, 4, 5]) ? (int) $_POST['border_width'] : 0;
        $_POST['border_style'] = in_array($_POST['border_style'], ['solid', 'dashed', 'double', 'inset', 'outset']) ? query_clean($_POST['border_style']) : 'solid';
        $_POST['border_color'] = !preg_match('/#([A-Fa-f0-9]{3,4}){1,2}\b/i', $_POST['border_color']) ? '#000000' : $_POST['border_color'];
        $_POST['border_shadow_offset_x'] = in_array($_POST['border_shadow_offset_x'], range(-20, 20)) ? (int) $_POST['border_shadow_offset_x'] : 0;
        $_POST['border_shadow_offset_y'] = in_array($_POST['border_shadow_offset_y'], range(-20, 20)) ? (int) $_POST['border_shadow_offset_y'] : 0;
        $_POST['border_shadow_blur'] = in_array($_POST['border_shadow_blur'], range(0, 20)) ? (int) $_POST['border_shadow_blur'] : 0;
        $_POST['border_shadow_spread'] = in_array($_POST['border_shadow_spread'], range(0, 10)) ? (int) $_POST['border_shadow_spread'] : 0;
        $_POST['border_shadow_color'] = !preg_match('/#([A-Fa-f0-9]{3,4}){1,2}\b/i', $_POST['border_shadow_color']) ? '#000000' : $_POST['border_shadow_color'];
        $_POST['text_color'] = !preg_match('/#([A-Fa-f0-9]{3,4}){1,2}\b/i', $_POST['text_color']) ? '#000000' : $_POST['text_color'];
        $_POST['text_alignment'] = in_array($_POST['text_alignment'], ['center', 'left', 'right', 'justify']) ? query_clean($_POST['text_alignment']) : 'center';
        $_POST['background_color'] = !preg_match('/#([A-Fa-f0-9]{3,4}){1,2}\b/i', $_POST['background_color']) ? '#ffffff' : $_POST['background_color'];
        $_POST['location_url'] = get_url($_POST['location_url']);
        $_POST['open_in_new_tab'] = isset($_POST['open_in_new_tab']);
        $_POST['display_close_button'] = isset($_POST['display_close_button']);
        $_POST['alert_pause_after_closed'] = (int) $_POST['alert_pause_after_closed'];

        $this->check_location_url($_POST['location_url'], true);

        /* Display settings */
        $this->process_display_settings();

        if(!$biolink_block = db()->where('biolink_block_id', $_POST['biolink_block_id'])->where('user_id', $this->user->user_id)->getOne('biolinks_blocks')) {
            die();
        }

        $settings = json_encode([
            'text' => $_POST['text'],
            'icon' => $_POST['icon'],
            'open_in_new_tab' => $_POST['open_in_new_tab'],
            'text_color' => $_POST['text_color'],
            'text_alignment' => $_POST['text_alignment'],
            'background_color' => $_POST['background_color'],
            'border_radius' => $_POST['border_radius'],
            'border_width' => $_POST['border_width'],
            'border_style' => $_POST['border_style'],
            'border_color' => $_POST['border_color'],
            'border_shadow_offset_x' => $_POST['border_shadow_offset_x'],
            'border_shadow_offset_y' => $_POST['border_shadow_offset_y'],
            'border_shadow_blur' => $_POST['border_shadow_blur'],
            'border_shadow_spread' => $_POST['border_shadow_spread'],
            'border_shadow_color' => $_POST['border_shadow_color'],
            'display_close_button' => $_POST['display_close_button'],
            'alert_pause_after_closed' => $_POST['alert_pause_after_closed'],

            /* Display settings */
            'display_countries' => $_POST['display_countries'],
            'display_devices' => $_POST['display_devices'],
            'display_languages' => $_POST['display_languages'],
            'display_operating_systems' => $_POST['display_operating_systems'],
        ]);

        /* Database query */
        db()->where('biolink_block_id', $_POST['biolink_block_id'])->update('biolinks_blocks', [
            'settings' => $settings,
            'location_url' => $_POST['location_url'],
            'start_date' => $_POST['start_date'],
            'end_date' => $_POST['end_date'],
        ]);

        /* Clear the cache */
        \Altum\Cache::$adapter->deleteItem('link?link_id=' . $biolink_block->link_id);

        Response::json(l('global.success_message.update2'), 'success');
    }

    private function create_biolink_faq() {
        $_POST['link_id'] = (int) $_POST['link_id'];

        if(!$link = db()->where('link_id', $_POST['link_id'])->where('user_id', $this->user->user_id)->getOne('links')) {
            die();
        }

        $type = 'faq';
        $settings = json_encode([
            'items' => [],
            'text_color' => 'black',
            'text_alignment' => 'center',
            'background_color' => 'white',
            'border_shadow_offset_x' => 0,
            'border_shadow_offset_y' => 0,
            'border_shadow_blur' => 20,
            'border_shadow_spread' => 0,
            'border_shadow_color' => '#00000010',
            'border_width' => 0,
            'border_style' => 'solid',
            'border_color' => 'white',
            'border_radius' => 'rounded',

            /* Display settings */
            'display_countries' => [],
            'display_devices' => [],
            'display_languages' => [],
            'display_operating_systems' => [],
        ]);

        /* Database query */
        db()->insert('biolinks_blocks', [
            'user_id' => $this->user->user_id,
            'link_id' => $_POST['link_id'],
            'type' => $type,
            'location_url' => null,
            'settings' => $settings,
            'order' => $this->total_biolink_blocks,
            'datetime' => \Altum\Date::$date,
        ]);

        /* Clear the cache */
        \Altum\Cache::$adapter->deleteItem('link?link_id=' . $_POST['link_id']);

        Response::json('', 'success', ['url' => url('link/' . $_POST['link_id'] . '?tab=links')]);
    }

    private function update_biolink_faq() {
        $_POST['biolink_block_id'] = (int) $_POST['biolink_block_id'];
        $_POST['border_radius'] = in_array($_POST['border_radius'], ['straight', 'round', 'rounded']) ? query_clean($_POST['border_radius']) : 'rounded';
        $_POST['border_width'] = in_array($_POST['border_width'], [0, 1, 2, 3, 4, 5]) ? (int) $_POST['border_width'] : 0;
        $_POST['border_style'] = in_array($_POST['border_style'], ['solid', 'dashed', 'double', 'inset', 'outset']) ? query_clean($_POST['border_style']) : 'solid';
        $_POST['border_color'] = !preg_match('/#([A-Fa-f0-9]{3,4}){1,2}\b/i', $_POST['border_color']) ? '#000000' : $_POST['border_color'];
        $_POST['border_shadow_offset_x'] = in_array($_POST['border_shadow_offset_x'], range(-20, 20)) ? (int) $_POST['border_shadow_offset_x'] : 0;
        $_POST['border_shadow_offset_y'] = in_array($_POST['border_shadow_offset_y'], range(-20, 20)) ? (int) $_POST['border_shadow_offset_y'] : 0;
        $_POST['border_shadow_blur'] = in_array($_POST['border_shadow_blur'], range(0, 20)) ? (int) $_POST['border_shadow_blur'] : 0;
        $_POST['border_shadow_spread'] = in_array($_POST['border_shadow_spread'], range(0, 10)) ? (int) $_POST['border_shadow_spread'] : 0;
        $_POST['border_shadow_color'] = !preg_match('/#([A-Fa-f0-9]{3,4}){1,2}\b/i', $_POST['border_shadow_color']) ? '#000000' : $_POST['border_shadow_color'];
        $_POST['text_color'] = !preg_match('/#([A-Fa-f0-9]{3,4}){1,2}\b/i', $_POST['text_color']) ? '#000000' : $_POST['text_color'];
        $_POST['text_alignment'] = in_array($_POST['text_alignment'], ['center', 'left', 'right', 'justify']) ? query_clean($_POST['text_alignment']) : 'center';
        $_POST['background_color'] = !preg_match('/#([A-Fa-f0-9]{3,4}){1,2}\b/i', $_POST['background_color']) ? '#ffffff' : $_POST['background_color'];

        if(!isset($_POST['item_title'])) {
            $_POST['item_title'] = [];
            $_POST['item_content'] = [];
        }

        $items = [];
        foreach($_POST['item_title'] as $key => $value) {
            if(empty(trim($value))) continue;
            if($key >= 100) continue;

            $items[] = [
                'title' => query_clean($value),
                'content' => input_clean($_POST['item_content'][$key]),
            ];
        }

        /* Display settings */
        $this->process_display_settings();

        if(!$biolink_block = db()->where('biolink_block_id', $_POST['biolink_block_id'])->where('user_id', $this->user->user_id)->getOne('biolinks_blocks')) {
            die();
        }

        $settings = json_encode([
            'items' => $items,
            'text_color' => $_POST['text_color'],
            'text_alignment' => $_POST['text_alignment'],
            'background_color' => $_POST['background_color'],
            'border_radius' => $_POST['border_radius'],
            'border_width' => $_POST['border_width'],
            'border_style' => $_POST['border_style'],
            'border_color' => $_POST['border_color'],
            'border_shadow_offset_x' => $_POST['border_shadow_offset_x'],
            'border_shadow_offset_y' => $_POST['border_shadow_offset_y'],
            'border_shadow_blur' => $_POST['border_shadow_blur'],
            'border_shadow_spread' => $_POST['border_shadow_spread'],
            'border_shadow_color' => $_POST['border_shadow_color'],

            /* Display settings */
            'display_countries' => $_POST['display_countries'],
            'display_devices' => $_POST['display_devices'],
            'display_languages' => $_POST['display_languages'],
            'display_operating_systems' => $_POST['display_operating_systems'],
        ]);

        /* Database query */
        db()->where('biolink_block_id', $_POST['biolink_block_id'])->update('biolinks_blocks', [
            'settings' => $settings,
            'start_date' => $_POST['start_date'],
            'end_date' => $_POST['end_date'],
        ]);

        /* Clear the cache */
        \Altum\Cache::$adapter->deleteItem('link?link_id=' . $biolink_block->link_id);

        Response::json(l('global.success_message.update2'), 'success');
    }

    private function create_biolink_timeline() {
        $_POST['link_id'] = (int) $_POST['link_id'];

        if(!$link = db()->where('link_id', $_POST['link_id'])->where('user_id', $this->user->user_id)->getOne('links')) {
            die();
        }

        $type = 'timeline';
        $settings = json_encode([
            'items' => [],
            'title_color' => '#ffffff',
            'date_color' => '#ffffff',
            'description_color' => '#ffffff',
            'line_color' => '#FFFFFF38',
            'text_alignment' => 'left',
            'background_color' => '#FFFFFF00',
            'border_shadow_offset_x' => 0,
            'border_shadow_offset_y' => 0,
            'border_shadow_blur' => 20,
            'border_shadow_spread' => 0,
            'border_shadow_color' => '#00000010',
            'border_width' => 0,
            'border_style' => 'solid',
            'border_color' => 'white',
            'border_radius' => 'rounded',

            /* Display settings */
            'display_countries' => [],
            'display_devices' => [],
            'display_languages' => [],
            'display_operating_systems' => [],
        ]);

        /* Database query */
        db()->insert('biolinks_blocks', [
            'user_id' => $this->user->user_id,
            'link_id' => $_POST['link_id'],
            'type' => $type,
            'location_url' => null,
            'settings' => $settings,
            'order' => $this->total_biolink_blocks,
            'datetime' => \Altum\Date::$date,
        ]);

        /* Clear the cache */
        \Altum\Cache::$adapter->deleteItem('link?link_id=' . $_POST['link_id']);

        Response::json('', 'success', ['url' => url('link/' . $_POST['link_id'] . '?tab=links')]);
    }

    private function update_biolink_timeline() {
        $_POST['biolink_block_id'] = (int) $_POST['biolink_block_id'];
        $_POST['border_radius'] = in_array($_POST['border_radius'], ['straight', 'round', 'rounded']) ? query_clean($_POST['border_radius']) : 'rounded';
        $_POST['border_width'] = in_array($_POST['border_width'], [0, 1, 2, 3, 4, 5]) ? (int) $_POST['border_width'] : 0;
        $_POST['border_style'] = in_array($_POST['border_style'], ['solid', 'dashed', 'double', 'inset', 'outset']) ? query_clean($_POST['border_style']) : 'solid';
        $_POST['border_color'] = !preg_match('/#([A-Fa-f0-9]{3,4}){1,2}\b/i', $_POST['border_color']) ? '#000000' : $_POST['border_color'];
        $_POST['border_shadow_offset_x'] = in_array($_POST['border_shadow_offset_x'], range(-20, 20)) ? (int) $_POST['border_shadow_offset_x'] : 0;
        $_POST['border_shadow_offset_y'] = in_array($_POST['border_shadow_offset_y'], range(-20, 20)) ? (int) $_POST['border_shadow_offset_y'] : 0;
        $_POST['border_shadow_blur'] = in_array($_POST['border_shadow_blur'], range(0, 20)) ? (int) $_POST['border_shadow_blur'] : 0;
        $_POST['border_shadow_spread'] = in_array($_POST['border_shadow_spread'], range(0, 10)) ? (int) $_POST['border_shadow_spread'] : 0;
        $_POST['border_shadow_color'] = !preg_match('/#([A-Fa-f0-9]{3,4}){1,2}\b/i', $_POST['border_shadow_color']) ? '#000000' : $_POST['border_shadow_color'];
        $_POST['title_color'] = !preg_match('/#([A-Fa-f0-9]{3,4}){1,2}\b/i', $_POST['title_color']) ? '#000000' : $_POST['title_color'];
        $_POST['date_color'] = !preg_match('/#([A-Fa-f0-9]{3,4}){1,2}\b/i', $_POST['date_color']) ? '#000000' : $_POST['date_color'];
        $_POST['line_color'] = !preg_match('/#([A-Fa-f0-9]{3,4}){1,2}\b/i', $_POST['line_color']) ? '#000000' : $_POST['line_color'];
        $_POST['description_color'] = !preg_match('/#([A-Fa-f0-9]{3,4}){1,2}\b/i', $_POST['description_color']) ? '#000000' : $_POST['description_color'];
        $_POST['text_alignment'] = in_array($_POST['text_alignment'], ['center', 'left', 'right', 'justify']) ? query_clean($_POST['text_alignment']) : 'center';
        $_POST['background_color'] = !preg_match('/#([A-Fa-f0-9]{3,4}){1,2}\b/i', $_POST['background_color']) ? '#ffffff' : $_POST['background_color'];

        if(!isset($_POST['item_title'])) {
            $_POST['item_title'] = [];
            $_POST['item_content'] = [];
        }

        $items = [];
        foreach($_POST['item_title'] as $key => $value) {
            if(empty(trim($value))) continue;
            if($key >= 100) continue;

            $items[] = [
                'title' => query_clean($value),
                'date' => input_clean($_POST['item_date'][$key]),
                'description' => input_clean($_POST['item_description'][$key]),
            ];
        }

        /* Display settings */
        $this->process_display_settings();

        if(!$biolink_block = db()->where('biolink_block_id', $_POST['biolink_block_id'])->where('user_id', $this->user->user_id)->getOne('biolinks_blocks')) {
            die();
        }

        $settings = json_encode([
            'items' => $items,
            'title_color' => $_POST['title_color'],
            'date_color' => $_POST['date_color'],
            'description_color' => $_POST['description_color'],
            'line_color' => $_POST['line_color'],
            'text_alignment' => $_POST['text_alignment'],
            'background_color' => $_POST['background_color'],
            'border_radius' => $_POST['border_radius'],
            'border_width' => $_POST['border_width'],
            'border_style' => $_POST['border_style'],
            'border_color' => $_POST['border_color'],
            'border_shadow_offset_x' => $_POST['border_shadow_offset_x'],
            'border_shadow_offset_y' => $_POST['border_shadow_offset_y'],
            'border_shadow_blur' => $_POST['border_shadow_blur'],
            'border_shadow_spread' => $_POST['border_shadow_spread'],
            'border_shadow_color' => $_POST['border_shadow_color'],

            /* Display settings */
            'display_countries' => $_POST['display_countries'],
            'display_devices' => $_POST['display_devices'],
            'display_languages' => $_POST['display_languages'],
            'display_operating_systems' => $_POST['display_operating_systems'],
        ]);

        /* Database query */
        db()->where('biolink_block_id', $_POST['biolink_block_id'])->update('biolinks_blocks', [
            'settings' => $settings,
            'start_date' => $_POST['start_date'],
            'end_date' => $_POST['end_date'],
        ]);

        /* Clear the cache */
        \Altum\Cache::$adapter->deleteItem('link?link_id=' . $biolink_block->link_id);

        Response::json(l('global.success_message.update2'), 'success');
    }

    private function create_biolink_review() {
        $_POST['link_id'] = (int) $_POST['link_id'];
        $_POST['title'] = mb_substr(input_clean($_POST['title']), 0, 128);
        $_POST['description'] = mb_substr(input_clean($_POST['description']), 0, 1024);
        $_POST['author_name'] = mb_substr(input_clean($_POST['author_name']), 0, 128);
        $_POST['author_description'] = mb_substr(input_clean($_POST['author_description']), 0, 128);
        $_POST['stars'] = $_POST['stars'] > 5 || $_POST['stars'] < 0 ? 5 : (int) $_POST['stars'];

        if(!$link = db()->where('link_id', $_POST['link_id'])->where('user_id', $this->user->user_id)->getOne('links')) {
            die();
        }

        /* Image upload */
        $db_image = $this->handle_image_upload(null, 'block_images/', settings()->links->image_size_limit);

        $type = 'review';
        $settings = json_encode([
            'title' => $_POST['title'],
            'description' => $_POST['description'],
            'author_name' => $_POST['author_name'],
            'author_description' => $_POST['author_description'],
            'image' => $db_image,
            'stars' => $_POST['stars'],

            'title_color' => '#ffffff',
            'description_color' => '#ffffff',
            'author_name_color' => '#ffffff',
            'author_description_color' => '#ffffff',
            'stars_color' => '#FFDF00',
            'text_alignment' => 'left',
            'background_color' => '#FFFFFF',
            'border_shadow_offset_x' => 0,
            'border_shadow_offset_y' => 0,
            'border_shadow_blur' => 20,
            'border_shadow_spread' => 0,
            'border_shadow_color' => '#00000010',
            'border_width' => 0,
            'border_style' => 'solid',
            'border_color' => 'white',
            'border_radius' => 'rounded',

            /* Display settings */
            'display_countries' => [],
            'display_devices' => [],
            'display_languages' => [],
            'display_operating_systems' => [],
        ]);

        /* Database query */
        db()->insert('biolinks_blocks', [
            'user_id' => $this->user->user_id,
            'link_id' => $_POST['link_id'],
            'type' => $type,
            'location_url' => null,
            'settings' => $settings,
            'order' => $this->total_biolink_blocks,
            'datetime' => \Altum\Date::$date,
        ]);

        /* Clear the cache */
        \Altum\Cache::$adapter->deleteItem('link?link_id=' . $_POST['link_id']);

        Response::json('', 'success', ['url' => url('link/' . $_POST['link_id'] . '?tab=links')]);
    }

    private function update_biolink_review() {
        $_POST['biolink_block_id'] = (int) $_POST['biolink_block_id'];
        $_POST['title'] = mb_substr(input_clean($_POST['title']), 0, 128);
        $_POST['description'] = mb_substr(input_clean($_POST['description']), 0, 1024);
        $_POST['author_name'] = mb_substr(input_clean($_POST['author_name']), 0, 128);
        $_POST['author_description'] = mb_substr(input_clean($_POST['author_description']), 0, 128);
        $_POST['stars'] = $_POST['stars'] > 5 || $_POST['stars'] < 0 ? 5 : (int) $_POST['stars'];
        $_POST['border_radius'] = in_array($_POST['border_radius'], ['straight', 'round', 'rounded']) ? query_clean($_POST['border_radius']) : 'rounded';
        $_POST['border_width'] = in_array($_POST['border_width'], [0, 1, 2, 3, 4, 5]) ? (int) $_POST['border_width'] : 0;
        $_POST['border_style'] = in_array($_POST['border_style'], ['solid', 'dashed', 'double', 'inset', 'outset']) ? query_clean($_POST['border_style']) : 'solid';
        $_POST['border_color'] = !preg_match('/#([A-Fa-f0-9]{3,4}){1,2}\b/i', $_POST['border_color']) ? '#000000' : $_POST['border_color'];
        $_POST['border_shadow_offset_x'] = in_array($_POST['border_shadow_offset_x'], range(-20, 20)) ? (int) $_POST['border_shadow_offset_x'] : 0;
        $_POST['border_shadow_offset_y'] = in_array($_POST['border_shadow_offset_y'], range(-20, 20)) ? (int) $_POST['border_shadow_offset_y'] : 0;
        $_POST['border_shadow_blur'] = in_array($_POST['border_shadow_blur'], range(0, 20)) ? (int) $_POST['border_shadow_blur'] : 0;
        $_POST['border_shadow_spread'] = in_array($_POST['border_shadow_spread'], range(0, 10)) ? (int) $_POST['border_shadow_spread'] : 0;
        $_POST['border_shadow_color'] = !preg_match('/#([A-Fa-f0-9]{3,4}){1,2}\b/i', $_POST['border_shadow_color']) ? '#000000' : $_POST['border_shadow_color'];
        $_POST['title_color'] = !preg_match('/#([A-Fa-f0-9]{3,4}){1,2}\b/i', $_POST['title_color']) ? '#000000' : $_POST['title_color'];
        $_POST['description_color'] = !preg_match('/#([A-Fa-f0-9]{3,4}){1,2}\b/i', $_POST['description_color']) ? '#000000' : $_POST['description_color'];
        $_POST['author_name_color'] = !preg_match('/#([A-Fa-f0-9]{3,4}){1,2}\b/i', $_POST['author_name_color']) ? '#000000' : $_POST['author_name_color'];
        $_POST['author_description_color'] = !preg_match('/#([A-Fa-f0-9]{3,4}){1,2}\b/i', $_POST['author_description_color']) ? '#000000' : $_POST['author_description_color'];
        $_POST['stars_color'] = !preg_match('/#([A-Fa-f0-9]{3,4}){1,2}\b/i', $_POST['stars_color']) ? '#000000' : $_POST['stars_color'];
        $_POST['text_alignment'] = in_array($_POST['text_alignment'], ['center', 'left', 'right', 'justify']) ? query_clean($_POST['text_alignment']) : 'center';
        $_POST['background_color'] = !preg_match('/#([A-Fa-f0-9]{3,4}){1,2}\b/i', $_POST['background_color']) ? '#ffffff' : $_POST['background_color'];

        /* Display settings */
        $this->process_display_settings();

        if(!$biolink_block = db()->where('biolink_block_id', $_POST['biolink_block_id'])->where('user_id', $this->user->user_id)->getOne('biolinks_blocks')) {
            die();
        }
        $biolink_block->settings = json_decode($biolink_block->settings);

        /* Image upload */
        $db_image = $this->handle_image_upload($biolink_block->settings->image, 'block_images/', settings()->links->image_size_limit);

        $image_url = $db_image ? \Altum\Uploads::get_full_url('block_images') . $db_image : null;

        $settings = json_encode([
            'title' => $_POST['title'],
            'description' => $_POST['description'],
            'author_name' => $_POST['author_name'],
            'author_description' => $_POST['author_description'],
            'stars' => $_POST['stars'],
            'image' => $db_image,
            'title_color' => $_POST['title_color'],
            'description_color' => $_POST['description_color'],
            'author_name_color' => $_POST['author_name_color'],
            'author_description_color' => $_POST['author_description_color'],
            'stars_color' => $_POST['stars_color'],
            'text_alignment' => $_POST['text_alignment'],
            'background_color' => $_POST['background_color'],
            'border_radius' => $_POST['border_radius'],
            'border_width' => $_POST['border_width'],
            'border_style' => $_POST['border_style'],
            'border_color' => $_POST['border_color'],
            'border_shadow_offset_x' => $_POST['border_shadow_offset_x'],
            'border_shadow_offset_y' => $_POST['border_shadow_offset_y'],
            'border_shadow_blur' => $_POST['border_shadow_blur'],
            'border_shadow_spread' => $_POST['border_shadow_spread'],
            'border_shadow_color' => $_POST['border_shadow_color'],

            /* Display settings */
            'display_countries' => $_POST['display_countries'],
            'display_devices' => $_POST['display_devices'],
            'display_languages' => $_POST['display_languages'],
            'display_operating_systems' => $_POST['display_operating_systems'],
        ]);

        /* Database query */
        db()->where('biolink_block_id', $_POST['biolink_block_id'])->update('biolinks_blocks', [
            'settings' => $settings,
            'start_date' => $_POST['start_date'],
            'end_date' => $_POST['end_date'],
        ]);

        /* Clear the cache */
        \Altum\Cache::$adapter->deleteItem('link?link_id=' . $biolink_block->link_id);

        Response::json(l('global.success_message.update2'), 'success', ['images' => ['image' => $image_url]]);
    }

    private function create_biolink_image_slider() {
        $_POST['link_id'] = (int) $_POST['link_id'];

        if(!$link = db()->where('link_id', $_POST['link_id'])->where('user_id', $this->user->user_id)->getOne('links')) {
            die();
        }

        $type = 'image_slider';
        $settings = json_encode([
            'items' => [],
            'width_height' => '20',
            'gap' => '2',
            'display_multiple' => true,
            'display_pagination' => true,
            'autoplay' => true,
            'display_arrows' => true,
            'open_in_new_tab' => false,

            /* Display settings */
            'display_countries' => [],
            'display_devices' => [],
            'display_languages' => [],
            'display_operating_systems' => [],
        ]);

        /* Database query */
        db()->insert('biolinks_blocks', [
            'user_id' => $this->user->user_id,
            'link_id' => $_POST['link_id'],
            'type' => $type,
            'location_url' => null,
            'settings' => $settings,
            'order' => $this->total_biolink_blocks,
            'datetime' => \Altum\Date::$date,
        ]);

        /* Clear the cache */
        \Altum\Cache::$adapter->deleteItem('link?link_id=' . $_POST['link_id']);

        Response::json('', 'success', ['url' => url('link/' . $_POST['link_id'] . '?tab=links')]);
    }

    private function update_biolink_image_slider() {
        $_POST['biolink_block_id'] = (int) $_POST['biolink_block_id'];
        $_POST['width_height'] = $_POST['width_height'] > 25 || $_POST['width_height'] < 10 ? 15 : (int) $_POST['width_height'];
        $_POST['gap'] = $_POST['gap'] > 5 || $_POST['gap'] < 0 ? 2 : (int) $_POST['gap'];
        $_POST['display_arrows'] = isset($_POST['display_arrows']);
        $_POST['autoplay'] = isset($_POST['autoplay']);
        $_POST['display_multiple'] = isset($_POST['display_multiple']);
        $_POST['display_pagination'] = isset($_POST['display_pagination']);
        $_POST['open_in_new_tab'] = (int) isset($_POST['open_in_new_tab']);

        if(!isset($_POST['item_image_alt'])) {
            $_POST['item_image_alt'] = [];
            $_POST['item_location_url'] = [];
        }

        /* Display settings */
        $this->process_display_settings();

        if(!$biolink_block = db()->where('biolink_block_id', $_POST['biolink_block_id'])->where('user_id', $this->user->user_id)->getOne('biolinks_blocks')) {
            die();
        }
        $biolink_block->settings = json_decode($biolink_block->settings);

        $items = [];
        $count = 1;
        foreach($_POST['item_image_alt'] as $key => $value) {
            if($count++ >= 25) continue;

            $_POST['item_location_url'][$key] = get_url($_POST['item_location_url'][$key]);
            $this->check_location_url($_POST['item_location_url'][$key], true);

            $image = $this->handle_file_upload($biolink_block->settings->items->{$key}->image ?? null, 'item_image_' . $key, 'image_remove', ['jpg', 'jpeg', 'png', 'svg', 'ico', 'gif'], 'block_images/', settings()->links->image_size_limit);

            $items[md5($image)] = [
                'image_alt' => input_clean($value),
                'location_url' => $_POST['item_location_url'][$key],
                'image' => $image,
            ];
        }

        /* Make sure to delete old images if needed */
        foreach($biolink_block->settings->items as $key => $item) {
            if((isset($items[$key]) && $items[$key]['image'] != $item->image) || !isset($items[$key])) {
                \Altum\Uploads::delete_uploaded_file($item->image, 'block_images');
            }
        }

        $settings = json_encode([
            'items' => (array) $items,
            'width_height' => $_POST['width_height'],
            'gap' => $_POST['gap'],
            'display_multiple' => $_POST['display_multiple'],
            'autoplay' => $_POST['autoplay'],
            'display_arrows' => $_POST['display_arrows'],
            'display_pagination' => $_POST['display_pagination'],
            'open_in_new_tab' => $_POST['open_in_new_tab'],

            /* Display settings */
            'display_countries' => $_POST['display_countries'],
            'display_devices' => $_POST['display_devices'],
            'display_languages' => $_POST['display_languages'],
            'display_operating_systems' => $_POST['display_operating_systems'],
        ]);

        /* Database query */
        db()->where('biolink_block_id', $_POST['biolink_block_id'])->update('biolinks_blocks', [
            'settings' => $settings,
            'start_date' => $_POST['start_date'],
            'end_date' => $_POST['end_date'],
        ]);

        /* Clear the cache */
        \Altum\Cache::$adapter->deleteItem('link?link_id=' . $biolink_block->link_id);

        Response::json(l('global.success_message.update2'), 'success');
    }

    private function create_biolink_discord() {
        $_POST['link_id'] = (int) $_POST['link_id'];
        $_POST['server_id'] = (int) $_POST['server_id'];

        if(!$link = db()->where('link_id', $_POST['link_id'])->where('user_id', $this->user->user_id)->getOne('links')) {
            die();
        }

        $type = 'discord';
        $settings = json_encode([
            'server_id' => $_POST['server_id'],

            /* Display settings */
            'display_countries' => [],
            'display_devices' => [],
            'display_languages' => [],
            'display_operating_systems' => [],
        ]);

        /* Database query */
        db()->insert('biolinks_blocks', [
            'user_id' => $this->user->user_id,
            'link_id' => $_POST['link_id'],
            'type' => $type,
            'location_url' => null,
            'settings' => $settings,
            'order' => $this->total_biolink_blocks,
            'datetime' => \Altum\Date::$date,
        ]);

        /* Clear the cache */
        \Altum\Cache::$adapter->deleteItem('link?link_id=' . $_POST['link_id']);

        Response::json('', 'success', ['url' => url('link/' . $_POST['link_id'] . '?tab=links')]);
    }

    private function update_biolink_discord() {
        $_POST['biolink_block_id'] = (int) $_POST['biolink_block_id'];
        $_POST['server_id'] = (int) $_POST['server_id'];

        /* Display settings */
        $this->process_display_settings();

        if(!$biolink_block = db()->where('biolink_block_id', $_POST['biolink_block_id'])->where('user_id', $this->user->user_id)->getOne('biolinks_blocks')) {
            die();
        }

        $settings = json_encode([
            'server_id' => $_POST['server_id'],

            /* Display settings */
            'display_countries' => $_POST['display_countries'],
            'display_devices' => $_POST['display_devices'],
            'display_languages' => $_POST['display_languages'],
            'display_operating_systems' => $_POST['display_operating_systems'],
        ]);

        /* Database query */
        db()->where('biolink_block_id', $_POST['biolink_block_id'])->update('biolinks_blocks', [
            'settings' => $settings,
            'start_date' => $_POST['start_date'],
            'end_date' => $_POST['end_date'],
        ]);

        /* Clear the cache */
        \Altum\Cache::$adapter->deleteItem('link?link_id=' . $biolink_block->link_id);

        Response::json(l('global.success_message.update2'), 'success');
    }

    private function create_biolink_countdown() {
        $_POST['link_id'] = (int) $_POST['link_id'];
        $_POST['counter_end_date'] = (new \DateTime($_POST['counter_end_date'], new \DateTimeZone($this->user->timezone)))->setTimezone(new \DateTimeZone(\Altum\Date::$default_timezone))->format('Y-m-d H:i:s');
        $_POST['theme'] = in_array($_POST['theme'], ['light', 'dark']) ? query_clean($_POST['theme']) : 'light';

        if(!$link = db()->where('link_id', $_POST['link_id'])->where('user_id', $this->user->user_id)->getOne('links')) {
            die();
        }

        $type = 'countdown';
        $settings = json_encode([
            'counter_end_date' => $_POST['counter_end_date'],
            'theme' => $_POST['theme'],

            /* Display settings */
            'display_countries' => [],
            'display_devices' => [],
            'display_languages' => [],
            'display_operating_systems' => [],
        ]);

        /* Database query */
        db()->insert('biolinks_blocks', [
            'user_id' => $this->user->user_id,
            'link_id' => $_POST['link_id'],
            'type' => $type,
            'location_url' => null,
            'settings' => $settings,
            'order' => $this->total_biolink_blocks,
            'datetime' => \Altum\Date::$date,
        ]);

        /* Clear the cache */
        \Altum\Cache::$adapter->deleteItem('link?link_id=' . $_POST['link_id']);

        Response::json('', 'success', ['url' => url('link/' . $_POST['link_id'] . '?tab=links')]);
    }

    private function update_biolink_countdown() {
        $_POST['biolink_block_id'] = (int) $_POST['biolink_block_id'];
        $_POST['counter_end_date'] = (new \DateTime($_POST['counter_end_date'], new \DateTimeZone($this->user->timezone)))->setTimezone(new \DateTimeZone(\Altum\Date::$default_timezone))->format('Y-m-d H:i:s');
        $_POST['theme'] = in_array($_POST['theme'], ['light', 'dark']) ? query_clean($_POST['theme']) : 'light';

        /* Display settings */
        $this->process_display_settings();

        if(!$biolink_block = db()->where('biolink_block_id', $_POST['biolink_block_id'])->where('user_id', $this->user->user_id)->getOne('biolinks_blocks')) {
            die();
        }

        $settings = json_encode([
            'counter_end_date' => $_POST['counter_end_date'],
            'theme' => $_POST['theme'],

            /* Display settings */
            'display_countries' => $_POST['display_countries'],
            'display_devices' => $_POST['display_devices'],
            'display_languages' => $_POST['display_languages'],
            'display_operating_systems' => $_POST['display_operating_systems'],
        ]);

        /* Database query */
        db()->where('biolink_block_id', $_POST['biolink_block_id'])->update('biolinks_blocks', [
            'settings' => $settings,
            'start_date' => $_POST['start_date'],
            'end_date' => $_POST['end_date'],
        ]);

        /* Clear the cache */
        \Altum\Cache::$adapter->deleteItem('link?link_id=' . $biolink_block->link_id);

        Response::json(l('global.success_message.update2'), 'success');
    }

    private function create_biolink_file($type) {
        $_POST['link_id'] = (int) $_POST['link_id'];
        $_POST['name'] = mb_substr(query_clean($_POST['name']), 0, 128);
        $_POST['poster_url'] = get_url($_POST['poster_url'] ?? null);

        if(!$link = db()->where('link_id', $_POST['link_id'])->where('user_id', $this->user->user_id)->getOne('links')) {
            die();
        }

        /* File upload */
        $size_limit = in_array($type, ['file', 'pdf_document']) ? settings()->links->file_size_limit : settings()->links->{$type . '_size_limit'};
        $db_file = $this->handle_file_upload(null, 'file', 'file_remove', $this->biolink_blocks[$type]['whitelisted_file_extensions'], 'files/', $size_limit);

        $settings = [
            'file' => $db_file,
            'name' => $_POST['name'],

            /* Display settings */
            'display_countries' => [],
            'display_devices' => [],
            'display_languages' => [],
            'display_operating_systems' => [],
        ];

        if($type == 'video') {
            $settings['poster_url'] = $_POST['poster_url'];
        }

        if(in_array($type, ['file', 'pdf_document'])) {
            $settings = array_merge($settings, [
                'text_color' => 'black',
                'text_alignment' => 'center',
                'background_color' => 'white',
                'border_shadow_offset_x' => 0,
                'border_shadow_offset_y' => 0,
                'border_shadow_blur' => 20,
                'border_shadow_spread' => 0,
                'border_shadow_color' => '#00000010',
                'border_width' => 0,
                'border_style' => 'solid',
                'border_color' => 'white',
                'border_radius' => 'rounded',
                'animation' => false,
                'animation_runs' => 'repeat-1',
                'icon' => '',
                'image' => '',
                'open_in_new_tab' => true,
            ]);
        }

        $settings = json_encode($settings);

        /* Database query */
        db()->insert('biolinks_blocks', [
            'user_id' => $this->user->user_id,
            'link_id' => $_POST['link_id'],
            'type' => $type,
            'settings' => $settings,
            'order' => $this->total_biolink_blocks,
            'datetime' => \Altum\Date::$date,
        ]);

        /* Clear the cache */
        \Altum\Cache::$adapter->deleteItem('link?link_id=' . $_POST['link_id']);

        Response::json('', 'success', ['url' => url('link/' . $_POST['link_id'] . '?tab=links')]);
    }

    private function update_biolink_file($type) {
        $_POST['biolink_block_id'] = (int) $_POST['biolink_block_id'];
        $_POST['name'] = mb_substr(query_clean($_POST['name']), 0, 128);
        $_POST['poster_url'] = get_url($_POST['poster_url'] ?? null);
        $_POST['border_radius'] = in_array($_POST['border_radius'], ['straight', 'round', 'rounded']) ? query_clean($_POST['border_radius']) : 'rounded';
        $_POST['border_width'] = in_array($_POST['border_width'], [0, 1, 2, 3, 4, 5]) ? (int) $_POST['border_width'] : 0;
        $_POST['border_style'] = in_array($_POST['border_style'], ['solid', 'dashed', 'double', 'inset', 'outset']) ? query_clean($_POST['border_style']) : 'solid';
        $_POST['border_color'] = !preg_match('/#([A-Fa-f0-9]{3,4}){1,2}\b/i', $_POST['border_color']) ? '#000000' : $_POST['border_color'];
        $_POST['border_shadow_offset_x'] = in_array($_POST['border_shadow_offset_x'], range(-20, 20)) ? (int) $_POST['border_shadow_offset_x'] : 0;
        $_POST['border_shadow_offset_y'] = in_array($_POST['border_shadow_offset_y'], range(-20, 20)) ? (int) $_POST['border_shadow_offset_y'] : 0;
        $_POST['border_shadow_blur'] = in_array($_POST['border_shadow_blur'], range(0, 20)) ? (int) $_POST['border_shadow_blur'] : 0;
        $_POST['border_shadow_spread'] = in_array($_POST['border_shadow_spread'], range(0, 10)) ? (int) $_POST['border_shadow_spread'] : 0;
        $_POST['border_shadow_color'] = !preg_match('/#([A-Fa-f0-9]{3,4}){1,2}\b/i', $_POST['border_shadow_color']) ? '#000000' : $_POST['border_shadow_color'];
        $_POST['animation'] = in_array($_POST['animation'], require APP_PATH . 'includes/biolink_animations.php') || $_POST['animation'] == 'false' ? query_clean($_POST['animation']) : false;
        $_POST['animation_runs'] = isset($_POST['animation_runs']) && in_array($_POST['animation_runs'], ['repeat-1', 'repeat-2', 'repeat-3', 'infinite']) ? query_clean($_POST['animation_runs']) : false;
        $_POST['icon'] = query_clean($_POST['icon']);
        $_POST['text_color'] = !preg_match('/#([A-Fa-f0-9]{3,4}){1,2}\b/i', $_POST['text_color']) ? '#000000' : $_POST['text_color'];
        $_POST['text_alignment'] = in_array($_POST['text_alignment'], ['center', 'left', 'right', 'justify']) ? query_clean($_POST['text_alignment']) : 'center';
        $_POST['background_color'] = !preg_match('/#([A-Fa-f0-9]{3,4}){1,2}\b/i', $_POST['background_color']) ? '#ffffff' : $_POST['background_color'];
        $_POST['open_in_new_tab'] = isset($_POST['open_in_new_tab']);

        /* Display settings */
        $this->process_display_settings();

        if(!$biolink_block = db()->where('biolink_block_id', $_POST['biolink_block_id'])->where('user_id', $this->user->user_id)->getOne('biolinks_blocks')) {
            die();
        }
        $biolink_block->settings = json_decode($biolink_block->settings);

        /* File upload */
        $size_limit = in_array($type, ['file', 'pdf_document']) ? settings()->links->file_size_limit : settings()->links->{$type . '_size_limit'};
        $db_file = $this->handle_file_upload($biolink_block->settings->file, 'file', 'file_remove', $this->biolink_blocks[$type]['whitelisted_file_extensions'], 'files/', $size_limit);

        $settings = [
            'file' => $db_file,
            'name' => $_POST['name'],

            /* Display settings */
            'display_countries' => $_POST['display_countries'],
            'display_devices' => $_POST['display_devices'],
            'display_languages' => $_POST['display_languages'],
            'display_operating_systems' => $_POST['display_operating_systems'],
        ];

        if(in_array($type, ['file', 'pdf_document'])) {
            /* Image upload */
            $db_image = $this->handle_image_upload($biolink_block->settings->image, 'block_thumbnail_images/', settings()->links->thumbnail_image_size_limit);

            /* Check for the removal of the already uploaded file */
            if(isset($_POST['image_remove'])) {
                /* Offload deleting */
                if(\Altum\Plugin::is_active('offload') && settings()->offload->uploads_url) {
                    $s3 = new \Aws\S3\S3Client(get_aws_s3_config());
                    $s3->deleteObject([
                        'Bucket' => settings()->offload->storage_name,
                        'Key' => 'uploads/block_thumbnail_images/' . $biolink_block->settings->image,
                    ]);
                }

                /* Local deleting */
                else {
                    /* Delete current file */
                    if(!empty($biolink_block->settings->image) && file_exists(UPLOADS_PATH . 'block_thumbnail_images/' . $biolink_block->settings->image)) {
                        unlink(UPLOADS_PATH . 'block_thumbnail_images/' . $biolink_block->settings->image);
                    }
                }
            }

            $settings = array_merge($settings, [
                'text_color' => $_POST['text_color'],
                'text_alignment' => $_POST['text_alignment'],
                'background_color' => $_POST['background_color'],
                'border_radius' => $_POST['border_radius'],
                'border_width' => $_POST['border_width'],
                'border_style' => $_POST['border_style'],
                'border_color' => $_POST['border_color'],
                'border_shadow_offset_x' => $_POST['border_shadow_offset_x'],
                'border_shadow_offset_y' => $_POST['border_shadow_offset_y'],
                'border_shadow_blur' => $_POST['border_shadow_blur'],
                'border_shadow_spread' => $_POST['border_shadow_spread'],
                'border_shadow_color' => $_POST['border_shadow_color'],
                'animation' => $_POST['animation'],
                'animation_runs' => $_POST['animation_runs'],
                'icon' => $_POST['icon'],
                'image' => $db_image,
                'open_in_new_tab' => $_POST['open_in_new_tab'],
            ]);
        }

        if($type == 'video') {
            $settings['poster_url'] = $_POST['poster_url'];
        }

        $settings = json_encode($settings);

        /* Database query */
        db()->where('biolink_block_id', $_POST['biolink_block_id'])->update('biolinks_blocks', [
            'settings' => $settings,
            'start_date' => $_POST['start_date'],
            'end_date' => $_POST['end_date'],
        ]);

        /* Clear the cache */
        \Altum\Cache::$adapter->deleteItem('link?link_id=' . $biolink_block->link_id);

        Response::json(l('global.success_message.update2'), 'success');
    }

    private function create_biolink_cta() {
        $_POST['link_id'] = (int) $_POST['link_id'];
        $_POST['type'] = in_array($_POST['type'], ['email', 'call', 'sms', 'facetime']) ? query_clean($_POST['type']) : 'email';
        $_POST['value'] = mb_substr(query_clean($_POST['value']), 0, 320);

        if(!$link = db()->where('link_id', $_POST['link_id'])->where('user_id', $this->user->user_id)->getOne('links')) {
            die();
        }

        $type = 'cta';
        $settings = json_encode([
            'type' => $_POST['type'],
            'value' => $_POST['value'],
            'name' => $_POST['name'],
            'text_color' => 'black',
            'text_alignment' => 'center',
            'background_color' => 'white',
            'border_shadow_offset_x' => 0,
            'border_shadow_offset_y' => 0,
            'border_shadow_blur' => 20,
            'border_shadow_spread' => 0,
            'border_shadow_color' => '#00000010',
            'border_width' => 0,
            'border_style' => 'solid',
            'border_color' => 'white',
            'border_radius' => 'rounded',
            'animation' => false,
            'animation_runs' => 'repeat-1',
            'icon' => '',
            'image' => '',

            /* Display settings */
            'display_countries' => [],
            'display_devices' => [],
            'display_languages' => [],
            'display_operating_systems' => [],
        ]);

        /* Database query */
        db()->insert('biolinks_blocks', [
            'user_id' => $this->user->user_id,
            'link_id' => $_POST['link_id'],
            'type' => $type,
            'location_url' => null,
            'settings' => $settings,
            'order' => $this->total_biolink_blocks,
            'datetime' => \Altum\Date::$date,
        ]);

        /* Clear the cache */
        \Altum\Cache::$adapter->deleteItem('link?link_id=' . $_POST['link_id']);

        Response::json('', 'success', ['url' => url('link/' . $_POST['link_id'] . '?tab=links')]);
    }

    private function update_biolink_cta() {
        $_POST['biolink_block_id'] = (int) $_POST['biolink_block_id'];
        $_POST['type'] = in_array($_POST['type'], ['email', 'call', 'sms', 'facetime']) ? query_clean($_POST['type']) : 'email';
        $_POST['value'] = mb_substr(query_clean($_POST['value']), 0, 320);
        $_POST['border_radius'] = in_array($_POST['border_radius'], ['straight', 'round', 'rounded']) ? query_clean($_POST['border_radius']) : 'rounded';
        $_POST['border_width'] = in_array($_POST['border_width'], [0, 1, 2, 3, 4, 5]) ? (int) $_POST['border_width'] : 0;
        $_POST['border_style'] = in_array($_POST['border_style'], ['solid', 'dashed', 'double', 'inset', 'outset']) ? query_clean($_POST['border_style']) : 'solid';
        $_POST['border_color'] = !preg_match('/#([A-Fa-f0-9]{3,4}){1,2}\b/i', $_POST['border_color']) ? '#000000' : $_POST['border_color'];
        $_POST['border_shadow_offset_x'] = in_array($_POST['border_shadow_offset_x'], range(-20, 20)) ? (int) $_POST['border_shadow_offset_x'] : 0;
        $_POST['border_shadow_offset_y'] = in_array($_POST['border_shadow_offset_y'], range(-20, 20)) ? (int) $_POST['border_shadow_offset_y'] : 0;
        $_POST['border_shadow_blur'] = in_array($_POST['border_shadow_blur'], range(0, 20)) ? (int) $_POST['border_shadow_blur'] : 0;
        $_POST['border_shadow_spread'] = in_array($_POST['border_shadow_spread'], range(0, 10)) ? (int) $_POST['border_shadow_spread'] : 0;
        $_POST['border_shadow_color'] = !preg_match('/#([A-Fa-f0-9]{3,4}){1,2}\b/i', $_POST['border_shadow_color']) ? '#000000' : $_POST['border_shadow_color'];
        $_POST['animation'] = in_array($_POST['animation'], require APP_PATH . 'includes/biolink_animations.php') || $_POST['animation'] == 'false' ? query_clean($_POST['animation']) : false;
        $_POST['animation_runs'] = isset($_POST['animation_runs']) && in_array($_POST['animation_runs'], ['repeat-1', 'repeat-2', 'repeat-3', 'infinite']) ? query_clean($_POST['animation_runs']) : false;
        $_POST['icon'] = query_clean($_POST['icon']);
        $_POST['text_color'] = !preg_match('/#([A-Fa-f0-9]{3,4}){1,2}\b/i', $_POST['text_color']) ? '#000000' : $_POST['text_color'];
        $_POST['text_alignment'] = in_array($_POST['text_alignment'], ['center', 'left', 'right', 'justify']) ? query_clean($_POST['text_alignment']) : 'center';
        $_POST['background_color'] = !preg_match('/#([A-Fa-f0-9]{3,4}){1,2}\b/i', $_POST['background_color']) ? '#ffffff' : $_POST['background_color'];

        /* Display settings */
        $this->process_display_settings();

        if(!$biolink_block = db()->where('biolink_block_id', $_POST['biolink_block_id'])->where('user_id', $this->user->user_id)->getOne('biolinks_blocks')) {
            die();
        }
        $biolink_block->settings = json_decode($biolink_block->settings);

        /* Image upload */
        $db_image = $this->handle_image_upload($biolink_block->settings->image, 'block_thumbnail_images/', settings()->links->thumbnail_image_size_limit);

        /* Check for the removal of the already uploaded file */
        if(isset($_POST['image_remove'])) {
            /* Offload deleting */
            if(\Altum\Plugin::is_active('offload') && settings()->offload->uploads_url) {
                $s3 = new \Aws\S3\S3Client(get_aws_s3_config());
                $s3->deleteObject([
                    'Bucket' => settings()->offload->storage_name,
                    'Key' => 'uploads/block_thumbnail_images/' . $biolink_block->settings->image,
                ]);
            }

            /* Local deleting */
            else {
                /* Delete current file */
                if(!empty($biolink_block->settings->image) && file_exists(UPLOADS_PATH . 'block_thumbnail_images/' . $biolink_block->settings->image)) {
                    unlink(UPLOADS_PATH . 'block_thumbnail_images/' . $biolink_block->settings->image);
                }
            }
            $db_image = null;
        }

        $image_url = $db_image ? \Altum\Uploads::get_full_url('block_thumbnail_images') . $db_image : null;

        $settings = json_encode([
            'type' => $_POST['type'],
            'value' => $_POST['value'],
            'name' => $_POST['name'],
            'text_color' => $_POST['text_color'],
            'text_alignment' => $_POST['text_alignment'],
            'background_color' => $_POST['background_color'],
            'border_radius' => $_POST['border_radius'],
            'border_width' => $_POST['border_width'],
            'border_style' => $_POST['border_style'],
            'border_color' => $_POST['border_color'],
            'border_shadow_offset_x' => $_POST['border_shadow_offset_x'],
            'border_shadow_offset_y' => $_POST['border_shadow_offset_y'],
            'border_shadow_blur' => $_POST['border_shadow_blur'],
            'border_shadow_spread' => $_POST['border_shadow_spread'],
            'border_shadow_color' => $_POST['border_shadow_color'],
            'animation' => $_POST['animation'],
            'animation_runs' => $_POST['animation_runs'],
            'icon' => $_POST['icon'],
            'image' => $db_image,

            /* Display settings */
            'display_countries' => $_POST['display_countries'],
            'display_devices' => $_POST['display_devices'],
            'display_languages' => $_POST['display_languages'],
            'display_operating_systems' => $_POST['display_operating_systems'],
        ]);

        /* Database query */
        db()->where('biolink_block_id', $_POST['biolink_block_id'])->update('biolinks_blocks', [
            'settings' => $settings,
            'start_date' => $_POST['start_date'],
            'end_date' => $_POST['end_date'],
        ]);

        /* Clear the cache */
        \Altum\Cache::$adapter->deleteItem('link?link_id=' . $biolink_block->link_id);

        Response::json(l('global.success_message.update2'), 'success', ['images' => ['image' => $image_url]]);
    }

    private function create_biolink_external_item() {
        $_POST['link_id'] = (int) $_POST['link_id'];
        $_POST['location_url'] = get_url($_POST['location_url']);
        $_POST['name'] = mb_substr(query_clean($_POST['name']), 0, 128);
        $_POST['description'] = mb_substr(query_clean($_POST['description']), 0, 256);
        $_POST['price'] = mb_substr(query_clean($_POST['price']), 0, 32);

        if(!$link = db()->where('link_id', $_POST['link_id'])->where('user_id', $this->user->user_id)->getOne('links')) {
            die();
        }

        $this->check_location_url($_POST['location_url'], true);

        $type = 'external_item';
        $settings = json_encode([
            'name' => $_POST['name'],
            'description' => $_POST['description'],
            'price' => $_POST['price'],
            'name_color' => 'black',
            'description_color' => 'black',
            'price_color' => 'black',
            'open_in_new_tab' => false,
            'background_color' => 'white',
            'border_shadow_offset_x' => 0,
            'border_shadow_offset_y' => 0,
            'border_shadow_blur' => 20,
            'border_shadow_spread' => 0,
            'border_shadow_color' => '#00000010',
            'border_width' => 0,
            'border_style' => 'solid',
            'border_color' => 'white',
            'border_radius' => 'rounded',
            'animation' => false,
            'animation_runs' => 'repeat-1',
            'image' => '',
            'text_alignment' => 'left',

            /* Display settings */
            'display_countries' => [],
            'display_devices' => [],
            'display_languages' => [],
            'display_operating_systems' => [],
        ]);

        /* Database query */
        db()->insert('biolinks_blocks', [
            'user_id' => $this->user->user_id,
            'link_id' => $_POST['link_id'],
            'type' => $type,
            'location_url' => $_POST['location_url'],
            'settings' => $settings,
            'order' => $this->total_biolink_blocks,
            'datetime' => \Altum\Date::$date,
        ]);

        /* Clear the cache */
        \Altum\Cache::$adapter->deleteItem('link?link_id=' . $_POST['link_id']);

        Response::json('', 'success', ['url' => url('link/' . $_POST['link_id'] . '?tab=links')]);
    }

    private function update_biolink_external_item() {
        $_POST['biolink_block_id'] = (int) $_POST['biolink_block_id'];
        $_POST['location_url'] = get_url($_POST['location_url']);
        $_POST['name'] = mb_substr(query_clean($_POST['name']), 0, 128);
        $_POST['description'] = mb_substr(query_clean($_POST['description']), 0, 256);
        $_POST['price'] = mb_substr(query_clean($_POST['price']), 0, 32);
        $_POST['open_in_new_tab'] = isset($_POST['open_in_new_tab']);
        $_POST['border_radius'] = in_array($_POST['border_radius'], ['straight', 'round', 'rounded']) ? query_clean($_POST['border_radius']) : 'rounded';
        $_POST['border_width'] = in_array($_POST['border_width'], [0, 1, 2, 3, 4, 5]) ? (int) $_POST['border_width'] : 0;
        $_POST['border_style'] = in_array($_POST['border_style'], ['solid', 'dashed', 'double', 'inset', 'outset']) ? query_clean($_POST['border_style']) : 'solid';
        $_POST['border_color'] = !preg_match('/#([A-Fa-f0-9]{3,4}){1,2}\b/i', $_POST['border_color']) ? '#000000' : $_POST['border_color'];
        $_POST['border_shadow_offset_x'] = in_array($_POST['border_shadow_offset_x'], range(-20, 20)) ? (int) $_POST['border_shadow_offset_x'] : 0;
        $_POST['border_shadow_offset_y'] = in_array($_POST['border_shadow_offset_y'], range(-20, 20)) ? (int) $_POST['border_shadow_offset_y'] : 0;
        $_POST['border_shadow_blur'] = in_array($_POST['border_shadow_blur'], range(0, 20)) ? (int) $_POST['border_shadow_blur'] : 0;
        $_POST['border_shadow_spread'] = in_array($_POST['border_shadow_spread'], range(0, 10)) ? (int) $_POST['border_shadow_spread'] : 0;
        $_POST['border_shadow_color'] = !preg_match('/#([A-Fa-f0-9]{3,4}){1,2}\b/i', $_POST['border_shadow_color']) ? '#000000' : $_POST['border_shadow_color'];
        $_POST['animation'] = in_array($_POST['animation'], require APP_PATH . 'includes/biolink_animations.php') || $_POST['animation'] == 'false' ? query_clean($_POST['animation']) : false;
        $_POST['animation_runs'] = isset($_POST['animation_runs']) && in_array($_POST['animation_runs'], ['repeat-1', 'repeat-2', 'repeat-3', 'infinite']) ? query_clean($_POST['animation_runs']) : false;
        $_POST['name_color'] = !preg_match('/#([A-Fa-f0-9]{3,4}){1,2}\b/i', $_POST['name_color']) ? '#000000' : $_POST['name_color'];
        $_POST['description_color'] = !preg_match('/#([A-Fa-f0-9]{3,4}){1,2}\b/i', $_POST['description_color']) ? '#000000' : $_POST['description_color'];
        $_POST['price_color'] = !preg_match('/#([A-Fa-f0-9]{3,4}){1,2}\b/i', $_POST['price_color']) ? '#000000' : $_POST['price_color'];
        $_POST['background_color'] = !preg_match('/#([A-Fa-f0-9]{3,4}){1,2}\b/i', $_POST['background_color']) ? '#ffffff' : $_POST['background_color'];
        $_POST['text_alignment'] = in_array($_POST['text_alignment'], ['center', 'left', 'right', 'justify']) ? query_clean($_POST['text_alignment']) : 'center';

        /* Display settings */
        $this->process_display_settings();

        if(!$biolink_block = db()->where('biolink_block_id', $_POST['biolink_block_id'])->where('user_id', $this->user->user_id)->getOne('biolinks_blocks')) {
            die();
        }
        $biolink_block->settings = json_decode($biolink_block->settings);

        /* Check for any errors */
        $required_fields = ['name'];

        /* Check for any errors */
        foreach($required_fields as $field) {
            if(!isset($_POST[$field]) || (isset($_POST[$field]) && empty($_POST[$field]) && $_POST[$field] != '0')) {
                Response::json(l('global.error_message.empty_fields'), 'error');
                break 1;
            }
        }

        $this->check_location_url($_POST['location_url'], true);

        /* Image upload */
        $db_image = $this->handle_image_upload($biolink_block->settings->image, 'block_thumbnail_images/', settings()->links->thumbnail_image_size_limit);

        /* Check for the removal of the already uploaded file */
        if(isset($_POST['image_remove'])) {
            /* Offload deleting */
            if(\Altum\Plugin::is_active('offload') && settings()->offload->uploads_url) {
                $s3 = new \Aws\S3\S3Client(get_aws_s3_config());
                $s3->deleteObject([
                    'Bucket' => settings()->offload->storage_name,
                    'Key' => 'uploads/block_thumbnail_images/' . $biolink_block->settings->image,
                ]);
            }

            /* Local deleting */
            else {
                /* Delete current file */
                if(!empty($biolink_block->settings->image) && file_exists(UPLOADS_PATH . 'block_thumbnail_images/' . $biolink_block->settings->image)) {
                    unlink(UPLOADS_PATH . 'block_thumbnail_images/' . $biolink_block->settings->image);
                }
            }
            $db_image = null;
        }

        $image_url = $db_image ? \Altum\Uploads::get_full_url('block_thumbnail_images') . $db_image : null;

        $settings = json_encode([
            'name' => $_POST['name'],
            'description' => $_POST['description'],
            'price' => $_POST['price'],
            'open_in_new_tab' => $_POST['open_in_new_tab'],
            'name_color' => $_POST['name_color'],
            'description_color' => $_POST['description_color'],
            'price_color' => $_POST['price_color'],
            'background_color' => $_POST['background_color'],
            'border_radius' => $_POST['border_radius'],
            'border_width' => $_POST['border_width'],
            'border_style' => $_POST['border_style'],
            'border_color' => $_POST['border_color'],
            'border_shadow_offset_x' => $_POST['border_shadow_offset_x'],
            'border_shadow_offset_y' => $_POST['border_shadow_offset_y'],
            'border_shadow_blur' => $_POST['border_shadow_blur'],
            'border_shadow_spread' => $_POST['border_shadow_spread'],
            'border_shadow_color' => $_POST['border_shadow_color'],
            'animation' => $_POST['animation'],
            'animation_runs' => $_POST['animation_runs'],
            'image' => $db_image,
            'text_alignment' => $_POST['text_alignment'],

            /* Display settings */
            'display_countries' => $_POST['display_countries'],
            'display_devices' => $_POST['display_devices'],
            'display_languages' => $_POST['display_languages'],
            'display_operating_systems' => $_POST['display_operating_systems'],
        ]);

        /* Database query */
        db()->where('biolink_block_id', $_POST['biolink_block_id'])->update('biolinks_blocks', [
            'location_url' => $_POST['location_url'],
            'settings' => $settings,
            'start_date' => $_POST['start_date'],
            'end_date' => $_POST['end_date'],
        ]);

        /* Clear the cache */
        \Altum\Cache::$adapter->deleteItem('link?link_id=' . $biolink_block->link_id);

        Response::json(l('global.success_message.update2'), 'success', ['images' => ['image' => $image_url], 'location_url' => $_POST['location_url']]);
    }

    private function create_biolink_share() {
        $_POST['link_id'] = (int) $_POST['link_id'];
        $_POST['location_url'] = get_url($_POST['location_url']);
        $_POST['name'] = mb_substr(query_clean($_POST['name']), 0, 128);

        if(!$link = db()->where('link_id', $_POST['link_id'])->where('user_id', $this->user->user_id)->getOne('links')) {
            die();
        }

        $this->check_location_url($_POST['location_url']);

        $type = 'share';
        $settings = json_encode([
            'name' => $_POST['name'],
            'text_color' => 'black',
            'text_alignment' => 'center',
            'background_color' => 'white',
            'border_shadow_offset_x' => 0,
            'border_shadow_offset_y' => 0,
            'border_shadow_blur' => 20,
            'border_shadow_spread' => 0,
            'border_shadow_color' => '#00000010',
            'border_width' => 0,
            'border_style' => 'solid',
            'border_color' => 'white',
            'border_radius' => 'rounded',
            'animation' => false,
            'animation_runs' => 'repeat-1',
            'icon' => '',
            'image' => '',

            /* Display settings */
            'display_countries' => [],
            'display_devices' => [],
            'display_languages' => [],
            'display_operating_systems' => [],
        ]);

        /* Database query */
        db()->insert('biolinks_blocks', [
            'user_id' => $this->user->user_id,
            'link_id' => $_POST['link_id'],
            'type' => $type,
            'location_url' => $_POST['location_url'],
            'settings' => $settings,
            'order' => $this->total_biolink_blocks,
            'datetime' => \Altum\Date::$date,
        ]);

        /* Clear the cache */
        \Altum\Cache::$adapter->deleteItem('link?link_id=' . $_POST['link_id']);

        Response::json('', 'success', ['url' => url('link/' . $_POST['link_id'] . '?tab=links')]);
    }

    private function update_biolink_share() {
        $_POST['biolink_block_id'] = (int) $_POST['biolink_block_id'];
        $_POST['location_url'] = get_url($_POST['location_url']);
        $_POST['name'] = mb_substr(query_clean($_POST['name']), 0, 128);
        $_POST['border_radius'] = in_array($_POST['border_radius'], ['straight', 'round', 'rounded']) ? query_clean($_POST['border_radius']) : 'rounded';
        $_POST['border_width'] = in_array($_POST['border_width'], [0, 1, 2, 3, 4, 5]) ? (int) $_POST['border_width'] : 0;
        $_POST['border_style'] = in_array($_POST['border_style'], ['solid', 'dashed', 'double', 'inset', 'outset']) ? query_clean($_POST['border_style']) : 'solid';
        $_POST['border_color'] = !preg_match('/#([A-Fa-f0-9]{3,4}){1,2}\b/i', $_POST['border_color']) ? '#000000' : $_POST['border_color'];
        $_POST['border_shadow_offset_x'] = in_array($_POST['border_shadow_offset_x'], range(-20, 20)) ? (int) $_POST['border_shadow_offset_x'] : 0;
        $_POST['border_shadow_offset_y'] = in_array($_POST['border_shadow_offset_y'], range(-20, 20)) ? (int) $_POST['border_shadow_offset_y'] : 0;
        $_POST['border_shadow_blur'] = in_array($_POST['border_shadow_blur'], range(0, 20)) ? (int) $_POST['border_shadow_blur'] : 0;
        $_POST['border_shadow_spread'] = in_array($_POST['border_shadow_spread'], range(0, 10)) ? (int) $_POST['border_shadow_spread'] : 0;
        $_POST['border_shadow_color'] = !preg_match('/#([A-Fa-f0-9]{3,4}){1,2}\b/i', $_POST['border_shadow_color']) ? '#000000' : $_POST['border_shadow_color'];
        $_POST['animation'] = in_array($_POST['animation'], require APP_PATH . 'includes/biolink_animations.php') || $_POST['animation'] == 'false' ? query_clean($_POST['animation']) : false;
        $_POST['animation_runs'] = isset($_POST['animation_runs']) && in_array($_POST['animation_runs'], ['repeat-1', 'repeat-2', 'repeat-3', 'infinite']) ? query_clean($_POST['animation_runs']) : false;
        $_POST['icon'] = query_clean($_POST['icon']);
        $_POST['text_color'] = !preg_match('/#([A-Fa-f0-9]{3,4}){1,2}\b/i', $_POST['text_color']) ? '#000000' : $_POST['text_color'];
        $_POST['text_alignment'] = in_array($_POST['text_alignment'], ['center', 'left', 'right', 'justify']) ? query_clean($_POST['text_alignment']) : 'center';
        $_POST['background_color'] = !preg_match('/#([A-Fa-f0-9]{3,4}){1,2}\b/i', $_POST['background_color']) ? '#ffffff' : $_POST['background_color'];

        /* Display settings */
        $this->process_display_settings();

        if(!$biolink_block = db()->where('biolink_block_id', $_POST['biolink_block_id'])->where('user_id', $this->user->user_id)->getOne('biolinks_blocks')) {
            die();
        }
        $biolink_block->settings = json_decode($biolink_block->settings);

        /* Check for any errors */
        $required_fields = ['location_url', 'name'];

        /* Check for any errors */
        foreach($required_fields as $field) {
            if(!isset($_POST[$field]) || (isset($_POST[$field]) && empty($_POST[$field]) && $_POST[$field] != '0')) {
                Response::json(l('global.error_message.empty_fields'), 'error');
                break 1;
            }
        }

        $this->check_location_url($_POST['location_url']);

        /* Image upload */
        $db_image = $this->handle_image_upload($biolink_block->settings->image, 'block_thumbnail_images/', settings()->links->thumbnail_image_size_limit);

        /* Check for the removal of the already uploaded file */
        if(isset($_POST['image_remove'])) {
            /* Offload deleting */
            if(\Altum\Plugin::is_active('offload') && settings()->offload->uploads_url) {
                $s3 = new \Aws\S3\S3Client(get_aws_s3_config());
                $s3->deleteObject([
                    'Bucket' => settings()->offload->storage_name,
                    'Key' => 'uploads/block_thumbnail_images/' . $biolink_block->settings->image,
                ]);
            }

            /* Local deleting */
            else {
                /* Delete current file */
                if(!empty($biolink_block->settings->image) && file_exists(UPLOADS_PATH . 'block_thumbnail_images/' . $biolink_block->settings->image)) {
                    unlink(UPLOADS_PATH . 'block_thumbnail_images/' . $biolink_block->settings->image);
                }
            }
            $db_image = null;
        }

        $image_url = $db_image ? \Altum\Uploads::get_full_url('block_thumbnail_images') . $db_image : null;

        $settings = json_encode([
            'name' => $_POST['name'],
            'text_color' => $_POST['text_color'],
            'text_alignment' => $_POST['text_alignment'],
            'background_color' => $_POST['background_color'],
            'border_radius' => $_POST['border_radius'],
            'border_width' => $_POST['border_width'],
            'border_style' => $_POST['border_style'],
            'border_color' => $_POST['border_color'],
            'border_shadow_offset_x' => $_POST['border_shadow_offset_x'],
            'border_shadow_offset_y' => $_POST['border_shadow_offset_y'],
            'border_shadow_blur' => $_POST['border_shadow_blur'],
            'border_shadow_spread' => $_POST['border_shadow_spread'],
            'border_shadow_color' => $_POST['border_shadow_color'],
            'animation' => $_POST['animation'],
            'animation_runs' => $_POST['animation_runs'],
            'icon' => $_POST['icon'],
            'image' => $db_image,

            /* Display settings */
            'display_countries' => $_POST['display_countries'],
            'display_devices' => $_POST['display_devices'],
            'display_languages' => $_POST['display_languages'],
            'display_operating_systems' => $_POST['display_operating_systems'],
        ]);

        /* Database query */
        db()->where('biolink_block_id', $_POST['biolink_block_id'])->update('biolinks_blocks', [
            'location_url' => $_POST['location_url'],
            'settings' => $settings,
            'start_date' => $_POST['start_date'],
            'end_date' => $_POST['end_date'],
        ]);

        /* Clear the cache */
        \Altum\Cache::$adapter->deleteItem('link?link_id=' . $biolink_block->link_id);

        Response::json(l('global.success_message.update2'), 'success', ['images' => ['image' => $image_url], 'location_url' => $_POST['location_url']]);
    }

    private function create_biolink_youtube_feed() {
        $_POST['link_id'] = (int) $_POST['link_id'];
        $_POST['channel_id'] = mb_substr(query_clean($_POST['channel_id']), 0, 128);

        if(!$link = db()->where('link_id', $_POST['link_id'])->where('user_id', $this->user->user_id)->getOne('links')) {
            die();
        }

        $type = 'youtube_feed';
        $settings = json_encode([
            'channel_id' => $_POST['channel_id'],
            'amount' => 5,
            'open_in_new_tab' => false,
            'text_color' => 'black',
            'text_alignment' => 'center',
            'background_color' => 'white',
            'border_shadow_offset_x' => 0,
            'border_shadow_offset_y' => 0,
            'border_shadow_blur' => 20,
            'border_shadow_spread' => 0,
            'border_shadow_color' => '#00000010',
            'border_width' => 0,
            'border_style' => 'solid',
            'border_color' => 'white',
            'border_radius' => 'rounded',
            'animation' => false,
            'animation_runs' => 'repeat-1',

            /* Display settings */
            'display_countries' => [],
            'display_devices' => [],
            'display_languages' => [],
            'display_operating_systems' => [],
        ]);

        /* Database query */
        db()->insert('biolinks_blocks', [
            'user_id' => $this->user->user_id,
            'link_id' => $_POST['link_id'],
            'type' => $type,
            'settings' => $settings,
            'order' => $this->total_biolink_blocks,
            'datetime' => \Altum\Date::$date,
        ]);

        /* Clear the cache */
        \Altum\Cache::$adapter->deleteItem('link?link_id=' . $_POST['link_id']);

        Response::json('', 'success', ['url' => url('link/' . $_POST['link_id'] . '?tab=links')]);
    }

    private function update_biolink_youtube_feed() {
        $_POST['biolink_block_id'] = (int) $_POST['biolink_block_id'];
        $_POST['channel_id'] = mb_substr(query_clean($_POST['channel_id']), 0, 128);
        $_POST['amount'] = (int) query_clean($_POST['amount']);
        $_POST['open_in_new_tab'] = isset($_POST['open_in_new_tab']);
        $_POST['border_radius'] = in_array($_POST['border_radius'], ['straight', 'round', 'rounded']) ? query_clean($_POST['border_radius']) : 'rounded';
        $_POST['border_width'] = in_array($_POST['border_width'], [0, 1, 2, 3, 4, 5]) ? (int) $_POST['border_width'] : 0;
        $_POST['border_style'] = in_array($_POST['border_style'], ['solid', 'dashed', 'double', 'inset', 'outset']) ? query_clean($_POST['border_style']) : 'solid';
        $_POST['border_color'] = !preg_match('/#([A-Fa-f0-9]{3,4}){1,2}\b/i', $_POST['border_color']) ? '#000000' : $_POST['border_color'];
        $_POST['border_shadow_offset_x'] = in_array($_POST['border_shadow_offset_x'], range(-20, 20)) ? (int) $_POST['border_shadow_offset_x'] : 0;
        $_POST['border_shadow_offset_y'] = in_array($_POST['border_shadow_offset_y'], range(-20, 20)) ? (int) $_POST['border_shadow_offset_y'] : 0;
        $_POST['border_shadow_blur'] = in_array($_POST['border_shadow_blur'], range(0, 20)) ? (int) $_POST['border_shadow_blur'] : 0;
        $_POST['border_shadow_spread'] = in_array($_POST['border_shadow_spread'], range(0, 10)) ? (int) $_POST['border_shadow_spread'] : 0;
        $_POST['border_shadow_color'] = !preg_match('/#([A-Fa-f0-9]{3,4}){1,2}\b/i', $_POST['border_shadow_color']) ? '#000000' : $_POST['border_shadow_color'];
        $_POST['animation'] = in_array($_POST['animation'], require APP_PATH . 'includes/biolink_animations.php') || $_POST['animation'] == 'false' ? query_clean($_POST['animation']) : false;
        $_POST['animation_runs'] = isset($_POST['animation_runs']) && in_array($_POST['animation_runs'], ['repeat-1', 'repeat-2', 'repeat-3', 'infinite']) ? query_clean($_POST['animation_runs']) : false;
        $_POST['text_color'] = !preg_match('/#([A-Fa-f0-9]{3,4}){1,2}\b/i', $_POST['text_color']) ? '#000000' : $_POST['text_color'];
        $_POST['text_alignment'] = in_array($_POST['text_alignment'], ['center', 'left', 'right', 'justify']) ? query_clean($_POST['text_alignment']) : 'center';
        $_POST['background_color'] = !preg_match('/#([A-Fa-f0-9]{3,4}){1,2}\b/i', $_POST['background_color']) ? '#ffffff' : $_POST['background_color'];

        /* Display settings */
        $this->process_display_settings();

        if(!$biolink_block = db()->where('biolink_block_id', $_POST['biolink_block_id'])->where('user_id', $this->user->user_id)->getOne('biolinks_blocks')) {
            die();
        }

        $settings = json_encode([
            'channel_id' => $_POST['channel_id'],
            'amount' => $_POST['amount'],
            'open_in_new_tab' => $_POST['open_in_new_tab'],
            'text_color' => $_POST['text_color'],
            'text_alignment' => $_POST['text_alignment'],
            'background_color' => $_POST['background_color'],
            'border_radius' => $_POST['border_radius'],
            'border_width' => $_POST['border_width'],
            'border_style' => $_POST['border_style'],
            'border_color' => $_POST['border_color'],
            'border_shadow_offset_x' => $_POST['border_shadow_offset_x'],
            'border_shadow_offset_y' => $_POST['border_shadow_offset_y'],
            'border_shadow_blur' => $_POST['border_shadow_blur'],
            'border_shadow_spread' => $_POST['border_shadow_spread'],
            'border_shadow_color' => $_POST['border_shadow_color'],
            'animation' => $_POST['animation'],
            'animation_runs' => $_POST['animation_runs'],

            /* Display settings */
            'display_countries' => $_POST['display_countries'],
            'display_devices' => $_POST['display_devices'],
            'display_languages' => $_POST['display_languages'],
            'display_operating_systems' => $_POST['display_operating_systems'],
        ]);

        /* Database query */
        db()->where('biolink_block_id', $_POST['biolink_block_id'])->update('biolinks_blocks', [
            'settings' => $settings,
            'start_date' => $_POST['start_date'],
            'end_date' => $_POST['end_date'],
        ]);

        /* Clear the cache */
        \Altum\Cache::$adapter->deleteItem('link?link_id=' . $biolink_block->link_id);
        \Altum\Cache::$adapter->deleteItem('biolink_block?block_id=' . $biolink_block->biolink_block_id . '&type=youtube_feed');

        Response::json(l('global.success_message.update2'), 'success');
    }

    private function create_biolink_paypal() {
        $_POST['link_id'] = (int) $_POST['link_id'];
        $_POST['type'] = in_array($_POST['type'], ['buy_now', 'add_to_cart', 'donation']) ? $_POST['type'] : 'buy_now';
        $_POST['name'] = mb_substr(query_clean($_POST['name']), 0, 128);
        $_POST['email'] = mb_substr(query_clean($_POST['email']), 0, 320);
        $_POST['title'] = mb_substr(query_clean($_POST['title']), 0, 320);
        $_POST['currency'] = mb_substr(query_clean($_POST['currency']), 0, 8);
        $_POST['price'] = (float) $_POST['price'];

        if(!$link = db()->where('link_id', $_POST['link_id'])->where('user_id', $this->user->user_id)->getOne('links')) {
            die();
        }

        $type = 'paypal';
        $settings = json_encode([
            'type' => $_POST['type'],
            'name' => $_POST['name'],
            'email' => $_POST['email'],
            'title' => $_POST['title'],
            'currency' => $_POST['currency'],
            'price' => $_POST['price'],
            'thank_you_url' => '',
            'cancel_url' => '',
            'open_in_new_tab' => false,
            'text_color' => 'black',
            'text_alignment' => 'center',
            'background_color' => 'white',
            'border_shadow_offset_x' => 0,
            'border_shadow_offset_y' => 0,
            'border_shadow_blur' => 20,
            'border_shadow_spread' => 0,
            'border_shadow_color' => '#00000010',
            'border_width' => 0,
            'border_style' => 'solid',
            'border_color' => 'white',
            'border_radius' => 'rounded',
            'animation' => false,
            'animation_runs' => 'repeat-1',
            'icon' => '',
            'image' => '',

            /* Display settings */
            'display_countries' => [],
            'display_devices' => [],
            'display_languages' => [],
            'display_operating_systems' => [],
        ]);

        /* Database query */
        db()->insert('biolinks_blocks', [
            'user_id' => $this->user->user_id,
            'link_id' => $_POST['link_id'],
            'type' => $type,
            'settings' => $settings,
            'order' => $this->total_biolink_blocks,
            'datetime' => \Altum\Date::$date,
        ]);

        /* Clear the cache */
        \Altum\Cache::$adapter->deleteItem('link?link_id=' . $_POST['link_id']);

        Response::json('', 'success', ['url' => url('link/' . $_POST['link_id'] . '?tab=links')]);
    }

    private function update_biolink_paypal() {
        $_POST['biolink_block_id'] = (int) $_POST['biolink_block_id'];
        $_POST['type'] = in_array($_POST['type'], ['buy_now', 'add_to_cart', 'donation']) ? $_POST['type'] : 'buy_now';
        $_POST['name'] = mb_substr(query_clean($_POST['name']), 0, 128);
        $_POST['email'] = mb_substr(query_clean($_POST['email']), 0, 320);
        $_POST['title'] = mb_substr(query_clean($_POST['title']), 0, 320);
        $_POST['currency'] = mb_substr(query_clean($_POST['currency']), 0, 8);
        $_POST['price'] = (float) $_POST['price'];
        $_POST['thank_you_url'] = get_url($_POST['thank_you_url']);
        $_POST['cancel_url'] = get_url($_POST['cancel_url']);
        $_POST['open_in_new_tab'] = isset($_POST['open_in_new_tab']);
        $_POST['border_radius'] = in_array($_POST['border_radius'], ['straight', 'round', 'rounded']) ? query_clean($_POST['border_radius']) : 'rounded';
        $_POST['border_width'] = in_array($_POST['border_width'], [0, 1, 2, 3, 4, 5]) ? (int) $_POST['border_width'] : 0;
        $_POST['border_style'] = in_array($_POST['border_style'], ['solid', 'dashed', 'double', 'inset', 'outset']) ? query_clean($_POST['border_style']) : 'solid';
        $_POST['border_color'] = !preg_match('/#([A-Fa-f0-9]{3,4}){1,2}\b/i', $_POST['border_color']) ? '#000000' : $_POST['border_color'];
        $_POST['border_shadow_offset_x'] = in_array($_POST['border_shadow_offset_x'], range(-20, 20)) ? (int) $_POST['border_shadow_offset_x'] : 0;
        $_POST['border_shadow_offset_y'] = in_array($_POST['border_shadow_offset_y'], range(-20, 20)) ? (int) $_POST['border_shadow_offset_y'] : 0;
        $_POST['border_shadow_blur'] = in_array($_POST['border_shadow_blur'], range(0, 20)) ? (int) $_POST['border_shadow_blur'] : 0;
        $_POST['border_shadow_spread'] = in_array($_POST['border_shadow_spread'], range(0, 10)) ? (int) $_POST['border_shadow_spread'] : 0;
        $_POST['border_shadow_color'] = !preg_match('/#([A-Fa-f0-9]{3,4}){1,2}\b/i', $_POST['border_shadow_color']) ? '#000000' : $_POST['border_shadow_color'];
        $_POST['animation'] = in_array($_POST['animation'], require APP_PATH . 'includes/biolink_animations.php') || $_POST['animation'] == 'false' ? query_clean($_POST['animation']) : false;
        $_POST['animation_runs'] = isset($_POST['animation_runs']) && in_array($_POST['animation_runs'], ['repeat-1', 'repeat-2', 'repeat-3', 'infinite']) ? query_clean($_POST['animation_runs']) : false;
        $_POST['icon'] = query_clean($_POST['icon']);
        $_POST['text_color'] = !preg_match('/#([A-Fa-f0-9]{3,4}){1,2}\b/i', $_POST['text_color']) ? '#000000' : $_POST['text_color'];
        $_POST['text_alignment'] = in_array($_POST['text_alignment'], ['center', 'left', 'right', 'justify']) ? query_clean($_POST['text_alignment']) : 'center';
        $_POST['background_color'] = !preg_match('/#([A-Fa-f0-9]{3,4}){1,2}\b/i', $_POST['background_color']) ? '#ffffff' : $_POST['background_color'];

        /* Display settings */
        $this->process_display_settings();

        if(!$biolink_block = db()->where('biolink_block_id', $_POST['biolink_block_id'])->where('user_id', $this->user->user_id)->getOne('biolinks_blocks')) {
            die();
        }
        $biolink_block->settings = json_decode($biolink_block->settings);

        /* Check for any errors */
        $required_fields = ['name'];

        /* Check for any errors */
        foreach($required_fields as $field) {
            if(!isset($_POST[$field]) || (isset($_POST[$field]) && empty($_POST[$field]) && $_POST[$field] != '0')) {
                Response::json(l('global.error_message.empty_fields'), 'error');
                break 1;
            }
        }

        /* Image upload */
        $db_image = $this->handle_image_upload($biolink_block->settings->image, 'block_thumbnail_images/', settings()->links->thumbnail_image_size_limit);

        /* Check for the removal of the already uploaded file */
        if(isset($_POST['image_remove'])) {
            /* Offload deleting */
            if(\Altum\Plugin::is_active('offload') && settings()->offload->uploads_url) {
                $s3 = new \Aws\S3\S3Client(get_aws_s3_config());
                $s3->deleteObject([
                    'Bucket' => settings()->offload->storage_name,
                    'Key' => 'uploads/block_thumbnail_images/' . $biolink_block->settings->image,
                ]);
            }

            /* Local deleting */
            else {
                /* Delete current file */
                if(!empty($biolink_block->settings->image) && file_exists(UPLOADS_PATH . 'block_thumbnail_images/' . $biolink_block->settings->image)) {
                    unlink(UPLOADS_PATH . 'block_thumbnail_images/' . $biolink_block->settings->image);
                }
            }
            $db_image = null;
        }

        $image_url = $db_image ? \Altum\Uploads::get_full_url('block_thumbnail_images') . $db_image : null;

        $settings = json_encode([
            'type' => $_POST['type'],
            'name' => $_POST['name'],
            'email' => $_POST['email'],
            'title' => $_POST['title'],
            'currency' => $_POST['currency'],
            'price' => $_POST['price'],
            'thank_you_url' => $_POST['thank_you_url'],
            'cancel_url' => $_POST['cancel_url'],
            'open_in_new_tab' => $_POST['open_in_new_tab'],
            'text_color' => $_POST['text_color'],
            'text_alignment' => $_POST['text_alignment'],
            'background_color' => $_POST['background_color'],
            'border_radius' => $_POST['border_radius'],
            'border_width' => $_POST['border_width'],
            'border_style' => $_POST['border_style'],
            'border_color' => $_POST['border_color'],
            'border_shadow_offset_x' => $_POST['border_shadow_offset_x'],
            'border_shadow_offset_y' => $_POST['border_shadow_offset_y'],
            'border_shadow_blur' => $_POST['border_shadow_blur'],
            'border_shadow_spread' => $_POST['border_shadow_spread'],
            'border_shadow_color' => $_POST['border_shadow_color'],
            'animation' => $_POST['animation'],
            'animation_runs' => $_POST['animation_runs'],
            'icon' => $_POST['icon'],
            'image' => $db_image,

            /* Display settings */
            'display_countries' => $_POST['display_countries'],
            'display_devices' => $_POST['display_devices'],
            'display_languages' => $_POST['display_languages'],
            'display_operating_systems' => $_POST['display_operating_systems'],
        ]);

        /* Database query */
        db()->where('biolink_block_id', $_POST['biolink_block_id'])->update('biolinks_blocks', [
            'settings' => $settings,
            'start_date' => $_POST['start_date'],
            'end_date' => $_POST['end_date'],
        ]);

        /* Clear the cache */
        \Altum\Cache::$adapter->deleteItem('link?link_id=' . $biolink_block->link_id);

        Response::json(l('global.success_message.update2'), 'success', ['images' => ['image' => $image_url]]);
    }

    private function create_biolink_phone_collector() {
        $_POST['link_id'] = (int) $_POST['link_id'];
        $_POST['name'] = mb_substr(query_clean($_POST['name']), 0, 128);

        if(!$link = db()->where('link_id', $_POST['link_id'])->where('user_id', $this->user->user_id)->getOne('links')) {
            die();
        }

        $type = 'phone_collector';
        $settings = json_encode([
            'name' => $_POST['name'],
            'image' => '',
            'text_color' => 'black',
            'text_alignment' => 'center',
            'background_color' => 'white',
            'border_shadow_offset_x' => 0,
            'border_shadow_offset_y' => 0,
            'border_shadow_blur' => 20,
            'border_shadow_spread' => 0,
            'border_shadow_color' => '#00000010',
            'border_width' => 0,
            'border_style' => 'solid',
            'border_color' => 'white',
            'border_radius' => 'rounded',
            'animation' => false,
            'animation_runs' => 'repeat-1',
            'icon' => '',
            'phone_placeholder' => l('create_biolink_phone_collector_modal.phone_placeholder_default'),
            'name_placeholder' => l('create_biolink_phone_collector_modal.name_placeholder_default'),
            'button_text' => l('create_biolink_phone_collector_modal.button_text_default'),
            'success_text' => l('create_biolink_phone_collector_modal.success_text_default'),
            'thank_you_url' => '',
            'show_agreement' => false,
            'agreement_url' => '',
            'agreement_text' => '',
            'email_notification' => '',
            'webhook_url' => '',

            /* Display settings */
            'display_countries' => [],
            'display_devices' => [],
            'display_languages' => [],
            'display_operating_systems' => [],
        ]);

        /* Database query */
        db()->insert('biolinks_blocks', [
            'user_id' => $this->user->user_id,
            'link_id' => $_POST['link_id'],
            'type' => $type,
            'settings' => $settings,
            'order' => $this->total_biolink_blocks,
            'datetime' => \Altum\Date::$date,
        ]);

        /* Clear the cache */
        \Altum\Cache::$adapter->deleteItem('link?link_id=' . $_POST['link_id']);

        Response::json('', 'success', ['url' => url('link/' . $_POST['link_id'] . '?tab=links')]);
    }

    private function update_biolink_phone_collector() {
        $_POST['biolink_block_id'] = (int) $_POST['biolink_block_id'];
        $_POST['name'] = mb_substr(query_clean($_POST['name']), 0, 128);
        $_POST['border_radius'] = in_array($_POST['border_radius'], ['straight', 'round', 'rounded']) ? query_clean($_POST['border_radius']) : 'rounded';
        $_POST['border_width'] = in_array($_POST['border_width'], [0, 1, 2, 3, 4, 5]) ? (int) $_POST['border_width'] : 0;
        $_POST['border_style'] = in_array($_POST['border_style'], ['solid', 'dashed', 'double', 'inset', 'outset']) ? query_clean($_POST['border_style']) : 'solid';
        $_POST['border_color'] = !preg_match('/#([A-Fa-f0-9]{3,4}){1,2}\b/i', $_POST['border_color']) ? '#000000' : $_POST['border_color'];
        $_POST['border_shadow_offset_x'] = in_array($_POST['border_shadow_offset_x'], range(-20, 20)) ? (int) $_POST['border_shadow_offset_x'] : 0;
        $_POST['border_shadow_offset_y'] = in_array($_POST['border_shadow_offset_y'], range(-20, 20)) ? (int) $_POST['border_shadow_offset_y'] : 0;
        $_POST['border_shadow_blur'] = in_array($_POST['border_shadow_blur'], range(0, 20)) ? (int) $_POST['border_shadow_blur'] : 0;
        $_POST['border_shadow_spread'] = in_array($_POST['border_shadow_spread'], range(0, 10)) ? (int) $_POST['border_shadow_spread'] : 0;
        $_POST['border_shadow_color'] = !preg_match('/#([A-Fa-f0-9]{3,4}){1,2}\b/i', $_POST['border_shadow_color']) ? '#000000' : $_POST['border_shadow_color'];
        $_POST['animation'] = in_array($_POST['animation'], require APP_PATH . 'includes/biolink_animations.php') || $_POST['animation'] == 'false' ? query_clean($_POST['animation']) : false;
        $_POST['animation_runs'] = isset($_POST['animation_runs']) && in_array($_POST['animation_runs'], ['repeat-1', 'repeat-2', 'repeat-3', 'infinite']) ? query_clean($_POST['animation_runs']) : false;
        $_POST['icon'] = query_clean($_POST['icon']);
        $_POST['text_color'] = !preg_match('/#([A-Fa-f0-9]{3,4}){1,2}\b/i', $_POST['text_color']) ? '#000000' : $_POST['text_color'];
        $_POST['text_alignment'] = in_array($_POST['text_alignment'], ['center', 'left', 'right', 'justify']) ? query_clean($_POST['text_alignment']) : 'center';
        $_POST['background_color'] = !preg_match('/#([A-Fa-f0-9]{3,4}){1,2}\b/i', $_POST['background_color']) ? '#ffffff' : $_POST['background_color'];
        $_POST['phone_placeholder'] = mb_substr(query_clean($_POST['phone_placeholder']), 0, 64);
        $_POST['name_placeholder'] = mb_substr(query_clean($_POST['name_placeholder']), 0, 64);
        $_POST['button_text'] = mb_substr(query_clean($_POST['button_text']), 0, 64);
        $_POST['success_text'] = mb_substr(query_clean($_POST['success_text']), 0, 256);
        $_POST['show_agreement'] = (bool) isset($_POST['show_agreement']);
        $_POST['agreement_url'] = get_url($_POST['agreement_url']);
        $_POST['agreement_text'] = mb_substr(query_clean($_POST['agreement_text']), 0, 256);
        $_POST['email_notification'] = mb_substr(query_clean($_POST['email_notification']), 0, 320);
        $_POST['webhook_url'] = get_url($_POST['webhook_url']);
        $_POST['thank_you_url'] = get_url($_POST['thank_you_url']);

        /* Display settings */
        $this->process_display_settings();

        if(!$biolink_block = db()->where('biolink_block_id', $_POST['biolink_block_id'])->where('user_id', $this->user->user_id)->getOne('biolinks_blocks')) {
            die();
        }
        $biolink_block->settings = json_decode($biolink_block->settings);

        /* Image upload */
        $db_image = $this->handle_image_upload($biolink_block->settings->image, 'block_thumbnail_images/', settings()->links->thumbnail_image_size_limit);

        /* Check for the removal of the already uploaded file */
        if(isset($_POST['image_remove'])) {
            /* Offload deleting */
            if(\Altum\Plugin::is_active('offload') && settings()->offload->uploads_url) {
                $s3 = new \Aws\S3\S3Client(get_aws_s3_config());
                $s3->deleteObject([
                    'Bucket' => settings()->offload->storage_name,
                    'Key' => 'uploads/block_thumbnail_images/' . $biolink_block->settings->image,
                ]);
            }

            /* Local deleting */
            else {
                /* Delete current file */
                if(!empty($biolink_block->settings->image) && file_exists(UPLOADS_PATH . 'block_thumbnail_images/' . $biolink_block->settings->image)) {
                    unlink(UPLOADS_PATH . 'block_thumbnail_images/' . $biolink_block->settings->image);
                }
            }
            $db_image = null;
        }

        $image_url = $db_image ? \Altum\Uploads::get_full_url('block_thumbnail_images') . $db_image : null;

        $settings = json_encode([
            'name' => $_POST['name'],
            'image' => $db_image,
            'text_color' => $_POST['text_color'],
            'text_alignment' => $_POST['text_alignment'],
            'background_color' => $_POST['background_color'],
            'border_radius' => $_POST['border_radius'],
            'border_width' => $_POST['border_width'],
            'border_style' => $_POST['border_style'],
            'border_color' => $_POST['border_color'],
            'border_shadow_offset_x' => $_POST['border_shadow_offset_x'],
            'border_shadow_offset_y' => $_POST['border_shadow_offset_y'],
            'border_shadow_blur' => $_POST['border_shadow_blur'],
            'border_shadow_spread' => $_POST['border_shadow_spread'],
            'border_shadow_color' => $_POST['border_shadow_color'],
            'animation' => $_POST['animation'],
            'animation_runs' => $_POST['animation_runs'],
            'icon' => $_POST['icon'],
            'phone_placeholder' => $_POST['phone_placeholder'],
            'name_placeholder' => $_POST['name_placeholder'],
            'button_text' => $_POST['button_text'],
            'success_text' => $_POST['success_text'],
            'thank_you_url' => $_POST['thank_you_url'],
            'show_agreement' => $_POST['show_agreement'],
            'agreement_url' => $_POST['agreement_url'],
            'agreement_text' => $_POST['agreement_text'],
            'email_notification' => $_POST['email_notification'],
            'webhook_url' => $_POST['webhook_url'],

            /* Display settings */
            'display_countries' => $_POST['display_countries'],
            'display_devices' => $_POST['display_devices'],
            'display_languages' => $_POST['display_languages'],
            'display_operating_systems' => $_POST['display_operating_systems'],
        ]);

        db()->where('biolink_block_id', $_POST['biolink_block_id'])->update('biolinks_blocks', [
            'settings' => $settings,
            'start_date' => $_POST['start_date'],
            'end_date' => $_POST['end_date'],
        ]);

        /* Clear the cache */
        \Altum\Cache::$adapter->deleteItem('link?link_id=' . $biolink_block->link_id);

        Response::json(l('global.success_message.update2'), 'success', ['images' => ['image' => $image_url]]);
    }

    private function create_biolink_donation() {
        $_POST['link_id'] = (int) $_POST['link_id'];
        $_POST['name'] = mb_substr(query_clean($_POST['name']), 0, 128);

        if(!$link = db()->where('link_id', $_POST['link_id'])->where('user_id', $this->user->user_id)->getOne('links')) {
            die();
        }

        $type = 'donation';
        $settings = [
            'name' => $_POST['name'],
            'image' => '',
            'text_color' => 'black',
            'text_alignment' => 'center',
            'background_color' => 'white',
            'border_shadow_offset_x' => 0,
            'border_shadow_offset_y' => 0,
            'border_shadow_blur' => 20,
            'border_shadow_spread' => 0,
            'border_shadow_color' => '#00000010',
            'border_width' => 0,
            'border_style' => 'solid',
            'border_color' => 'white',
            'border_radius' => 'rounded',
            'animation' => false,
            'animation_runs' => 'repeat-1',
            'icon' => '',

            'title' => null,
            'description' => null,
            'prefilled_amount' => 5,
            'minimum_amount' => 1,
            'currency' => null,
            'allow_custom_amount' => true,
            'allow_message' => true,
            'thank_you_title' => null,
            'thank_you_description' => null,
            'thank_you_url' => null,
            'payment_processors_ids' => [],
            'email_notification' => null,
            'webhook_url' => null,

            /* Display settings */
            'display_countries' => [],
            'display_devices' => [],
            'display_languages' => [],
            'display_operating_systems' => [],
        ];
        $settings = json_encode($settings);

        /* Database query */
        db()->insert('biolinks_blocks', [
            'user_id' => $this->user->user_id,
            'link_id' => $_POST['link_id'],
            'type' => $type,
            'location_url' => null,
            'settings' => $settings,
            'order' => $this->total_biolink_blocks,
            'datetime' => \Altum\Date::$date,
        ]);

        /* Clear the cache */
        \Altum\Cache::$adapter->deleteItem('link?link_id=' . $_POST['link_id']);

        Response::json('', 'success', ['url' => url('link/' . $_POST['link_id'] . '?tab=links')]);
    }

    private function update_biolink_donation() {
        $_POST['biolink_block_id'] = (int) $_POST['biolink_block_id'];
        $_POST['name'] = mb_substr(query_clean($_POST['name']), 0, 128);
        $_POST['border_radius'] = in_array($_POST['border_radius'], ['straight', 'round', 'rounded']) ? query_clean($_POST['border_radius']) : 'rounded';
        $_POST['border_width'] = in_array($_POST['border_width'], [0, 1, 2, 3, 4, 5]) ? (int) $_POST['border_width'] : 0;
        $_POST['border_style'] = in_array($_POST['border_style'], ['solid', 'dashed', 'double', 'inset', 'outset']) ? query_clean($_POST['border_style']) : 'solid';
        $_POST['border_color'] = !preg_match('/#([A-Fa-f0-9]{3,4}){1,2}\b/i', $_POST['border_color']) ? '#000000' : $_POST['border_color'];
        $_POST['border_shadow_offset_x'] = in_array($_POST['border_shadow_offset_x'], range(-20, 20)) ? (int) $_POST['border_shadow_offset_x'] : 0;
        $_POST['border_shadow_offset_y'] = in_array($_POST['border_shadow_offset_y'], range(-20, 20)) ? (int) $_POST['border_shadow_offset_y'] : 0;
        $_POST['border_shadow_blur'] = in_array($_POST['border_shadow_blur'], range(0, 20)) ? (int) $_POST['border_shadow_blur'] : 0;
        $_POST['border_shadow_spread'] = in_array($_POST['border_shadow_spread'], range(0, 10)) ? (int) $_POST['border_shadow_spread'] : 0;
        $_POST['border_shadow_color'] = !preg_match('/#([A-Fa-f0-9]{3,4}){1,2}\b/i', $_POST['border_shadow_color']) ? '#000000' : $_POST['border_shadow_color'];
        $_POST['animation'] = in_array($_POST['animation'], require APP_PATH . 'includes/biolink_animations.php') || $_POST['animation'] == 'false' ? query_clean($_POST['animation']) : false;
        $_POST['animation_runs'] = isset($_POST['animation_runs']) && in_array($_POST['animation_runs'], ['repeat-1', 'repeat-2', 'repeat-3', 'infinite']) ? query_clean($_POST['animation_runs']) : false;
        $_POST['icon'] = query_clean($_POST['icon']);
        $_POST['text_color'] = !preg_match('/#([A-Fa-f0-9]{3,4}){1,2}\b/i', $_POST['text_color']) ? '#000000' : $_POST['text_color'];
        $_POST['text_alignment'] = in_array($_POST['text_alignment'], ['center', 'left', 'right', 'justify']) ? query_clean($_POST['text_alignment']) : 'center';
        $_POST['background_color'] = !preg_match('/#([A-Fa-f0-9]{3,4}){1,2}\b/i', $_POST['background_color']) ? '#ffffff' : $_POST['background_color'];

        $_POST['title'] = mb_substr(query_clean($_POST['title']), 0, $this->biolink_blocks['donation']['fields']['title']['max_length']);
        $_POST['description'] = mb_substr(query_clean($_POST['description']), 0, $this->biolink_blocks['donation']['fields']['description']['max_length']);
        $_POST['prefilled_amount'] = (float) $_POST['prefilled_amount'];
        $_POST['minimum_amount'] = (float) $_POST['minimum_amount'];
        $_POST['currency'] = mb_substr(query_clean($_POST['currency']), 0, $this->biolink_blocks['donation']['fields']['currency']['max_length']);
        $_POST['allow_custom_amount'] = (bool) isset($_POST['allow_custom_amount']);
        $_POST['allow_message'] = (bool) isset($_POST['allow_message']);
        $_POST['thank_you_title'] = mb_substr(query_clean($_POST['thank_you_title']), 0, $this->biolink_blocks['donation']['fields']['thank_you_title']['max_length']);
        $_POST['thank_you_description'] = mb_substr(query_clean($_POST['thank_you_description']), 0, $this->biolink_blocks['donation']['fields']['thank_you_description']['max_length']);
        $_POST['thank_you_url'] = mb_substr(query_clean($_POST['thank_you_url']), 0, $this->biolink_blocks['donation']['fields']['thank_you_url']['max_length']);
        $_POST['email_notification'] = mb_substr(query_clean($_POST['email_notification']), 0, $this->biolink_blocks['donation']['fields']['email_notification']['max_length']);
        $_POST['webhook_url'] = mb_substr(query_clean($_POST['webhook_url']), 0, $this->biolink_blocks['donation']['fields']['webhook_url']['max_length']);

        $payment_processors = (new \Altum\Models\PaymentProcessor())->get_payment_processors_by_user_id($this->user->user_id);
        $_POST['payment_processors_ids'] = array_map(
            function($payment_processor_id) {
                return (int) $payment_processor_id;
            },
            array_filter($_POST['payment_processors_ids'] ?? [], function($payment_processor_id) use($payment_processors) {
                return array_key_exists($payment_processor_id, $payment_processors);
            })
        );

        /* Display settings */
        $this->process_display_settings();

        if(!$biolink_block = db()->where('biolink_block_id', $_POST['biolink_block_id'])->where('user_id', $this->user->user_id)->getOne('biolinks_blocks')) {
            die();
        }
        $biolink_block->settings = json_decode($biolink_block->settings);

        /* Image upload */
        $db_image = $this->handle_image_upload($biolink_block->settings->image, 'block_thumbnail_images/', settings()->links->thumbnail_image_size_limit);

        /* Check for the removal of the already uploaded file */
        if(isset($_POST['image_remove'])) {
            /* Offload deleting */
            if(\Altum\Plugin::is_active('offload') && settings()->offload->uploads_url) {
                $s3 = new \Aws\S3\S3Client(get_aws_s3_config());
                $s3->deleteObject([
                    'Bucket' => settings()->offload->storage_name,
                    'Key' => 'uploads/block_thumbnail_images/' . $biolink_block->settings->image,
                ]);
            }

            /* Local deleting */
            else {
                /* Delete current file */
                if(!empty($biolink_block->settings->image) && file_exists(UPLOADS_PATH . 'block_thumbnail_images/' . $biolink_block->settings->image)) {
                    unlink(UPLOADS_PATH . 'block_thumbnail_images/' . $biolink_block->settings->image);
                }
            }
            $db_image = null;
        }

        $image_url = $db_image ? \Altum\Uploads::get_full_url('block_thumbnail_images') . $db_image : null;

        $settings = json_encode([
            'name' => $_POST['name'],
            'image' => $db_image,
            'text_color' => $_POST['text_color'],
            'text_alignment' => $_POST['text_alignment'],
            'background_color' => $_POST['background_color'],
            'border_radius' => $_POST['border_radius'],
            'border_width' => $_POST['border_width'],
            'border_style' => $_POST['border_style'],
            'border_color' => $_POST['border_color'],
            'border_shadow_offset_x' => $_POST['border_shadow_offset_x'],
            'border_shadow_offset_y' => $_POST['border_shadow_offset_y'],
            'border_shadow_blur' => $_POST['border_shadow_blur'],
            'border_shadow_spread' => $_POST['border_shadow_spread'],
            'border_shadow_color' => $_POST['border_shadow_color'],
            'animation' => $_POST['animation'],
            'animation_runs' => $_POST['animation_runs'],
            'icon' => $_POST['icon'],

            'title' => $_POST['title'],
            'description' => $_POST['description'],
            'prefilled_amount' => $_POST['prefilled_amount'],
            'minimum_amount' => $_POST['minimum_amount'],
            'currency' => $_POST['currency'],
            'allow_custom_amount' => $_POST['allow_custom_amount'],
            'allow_message' => $_POST['allow_message'],
            'thank_you_title' => $_POST['thank_you_title'],
            'thank_you_description' => $_POST['thank_you_description'],
            'thank_you_url' => $_POST['thank_you_url'],
            'payment_processors_ids' => $_POST['payment_processors_ids'],
            'email_notification' => $_POST['email_notification'],
            'webhook_url' => $_POST['webhook_url'],

            /* Display settings */
            'display_countries' => $_POST['display_countries'],
            'display_devices' => $_POST['display_devices'],
            'display_languages' => $_POST['display_languages'],
            'display_operating_systems' => $_POST['display_operating_systems'],
        ]);

        db()->where('biolink_block_id', $_POST['biolink_block_id'])->update('biolinks_blocks', [
            'settings' => $settings,
            'start_date' => $_POST['start_date'],
            'end_date' => $_POST['end_date'],
        ]);

        /* Clear the cache */
        \Altum\Cache::$adapter->deleteItem('link?link_id=' . $biolink_block->link_id);

        Response::json(l('global.success_message.update2'), 'success', ['images' => ['image' => $image_url]]);
    }

    private function create_biolink_product() {
        $_POST['link_id'] = (int) $_POST['link_id'];
        $_POST['name'] = mb_substr(query_clean($_POST['name']), 0, 128);

        if(!$link = db()->where('link_id', $_POST['link_id'])->where('user_id', $this->user->user_id)->getOne('links')) {
            die();
        }

        $type = 'product';
        $settings = [
            'name' => $_POST['name'],
            'image' => '',
            'text_color' => 'black',
            'text_alignment' => 'center',
            'background_color' => 'white',
            'border_shadow_offset_x' => 0,
            'border_shadow_offset_y' => 0,
            'border_shadow_blur' => 20,
            'border_shadow_spread' => 0,
            'border_shadow_color' => '#00000010',
            'border_width' => 0,
            'border_style' => 'solid',
            'border_color' => 'white',
            'border_radius' => 'rounded',
            'animation' => false,
            'animation_runs' => 'repeat-1',
            'icon' => '',

            'file' => null,
            'title' => null,
            'description' => null,
            'price' => 5,
            'minimum_price' => 1,
            'currency' => null,
            'allow_custom_price' => true,
            'thank_you_title' => null,
            'thank_you_description' => null,
            'thank_you_url' => null,
            'payment_processors_ids' => [],
            'email_notification' => null,
            'webhook_url' => null,

            /* Display settings */
            'display_countries' => [],
            'display_devices' => [],
            'display_languages' => [],
            'display_operating_systems' => [],
        ];
        $settings = json_encode($settings);

        /* Database query */
        db()->insert('biolinks_blocks', [
            'user_id' => $this->user->user_id,
            'link_id' => $_POST['link_id'],
            'type' => $type,
            'location_url' => null,
            'settings' => $settings,
            'order' => $this->total_biolink_blocks,
            'datetime' => \Altum\Date::$date,
        ]);

        /* Clear the cache */
        \Altum\Cache::$adapter->deleteItem('link?link_id=' . $_POST['link_id']);

        Response::json('', 'success', ['url' => url('link/' . $_POST['link_id'] . '?tab=links')]);
    }

    private function update_biolink_product() {
        $_POST['biolink_block_id'] = (int) $_POST['biolink_block_id'];
        $_POST['name'] = mb_substr(query_clean($_POST['name']), 0, 128);
        $_POST['border_radius'] = in_array($_POST['border_radius'], ['straight', 'round', 'rounded']) ? query_clean($_POST['border_radius']) : 'rounded';
        $_POST['border_width'] = in_array($_POST['border_width'], [0, 1, 2, 3, 4, 5]) ? (int) $_POST['border_width'] : 0;
        $_POST['border_style'] = in_array($_POST['border_style'], ['solid', 'dashed', 'double', 'inset', 'outset']) ? query_clean($_POST['border_style']) : 'solid';
        $_POST['border_color'] = !preg_match('/#([A-Fa-f0-9]{3,4}){1,2}\b/i', $_POST['border_color']) ? '#000000' : $_POST['border_color'];
        $_POST['border_shadow_offset_x'] = in_array($_POST['border_shadow_offset_x'], range(-20, 20)) ? (int) $_POST['border_shadow_offset_x'] : 0;
        $_POST['border_shadow_offset_y'] = in_array($_POST['border_shadow_offset_y'], range(-20, 20)) ? (int) $_POST['border_shadow_offset_y'] : 0;
        $_POST['border_shadow_blur'] = in_array($_POST['border_shadow_blur'], range(0, 20)) ? (int) $_POST['border_shadow_blur'] : 0;
        $_POST['border_shadow_spread'] = in_array($_POST['border_shadow_spread'], range(0, 10)) ? (int) $_POST['border_shadow_spread'] : 0;
        $_POST['border_shadow_color'] = !preg_match('/#([A-Fa-f0-9]{3,4}){1,2}\b/i', $_POST['border_shadow_color']) ? '#000000' : $_POST['border_shadow_color'];
        $_POST['animation'] = in_array($_POST['animation'], require APP_PATH . 'includes/biolink_animations.php') || $_POST['animation'] == 'false' ? query_clean($_POST['animation']) : false;
        $_POST['animation_runs'] = isset($_POST['animation_runs']) && in_array($_POST['animation_runs'], ['repeat-1', 'repeat-2', 'repeat-3', 'infinite']) ? query_clean($_POST['animation_runs']) : false;
        $_POST['icon'] = query_clean($_POST['icon']);
        $_POST['text_color'] = !preg_match('/#([A-Fa-f0-9]{3,4}){1,2}\b/i', $_POST['text_color']) ? '#000000' : $_POST['text_color'];
        $_POST['text_alignment'] = in_array($_POST['text_alignment'], ['center', 'left', 'right', 'justify']) ? query_clean($_POST['text_alignment']) : 'center';
        $_POST['background_color'] = !preg_match('/#([A-Fa-f0-9]{3,4}){1,2}\b/i', $_POST['background_color']) ? '#ffffff' : $_POST['background_color'];

        $_POST['title'] = mb_substr(query_clean($_POST['title']), 0, $this->biolink_blocks['product']['fields']['title']['max_length']);
        $_POST['description'] = mb_substr(query_clean($_POST['description']), 0, $this->biolink_blocks['product']['fields']['description']['max_length']);
        $_POST['price'] = (float) $_POST['price'];
        $_POST['minimum_price'] = (float) $_POST['minimum_price'];
        $_POST['currency'] = mb_substr(query_clean($_POST['currency']), 0, $this->biolink_blocks['product']['fields']['currency']['max_length']);
        $_POST['allow_custom_price'] = (bool) isset($_POST['allow_custom_price']);
        $_POST['thank_you_title'] = mb_substr(query_clean($_POST['thank_you_title']), 0, $this->biolink_blocks['product']['fields']['thank_you_title']['max_length']);
        $_POST['thank_you_description'] = mb_substr(query_clean($_POST['thank_you_description']), 0, $this->biolink_blocks['product']['fields']['thank_you_description']['max_length']);
        $_POST['thank_you_url'] = mb_substr(query_clean($_POST['thank_you_url']), 0, $this->biolink_blocks['donation']['fields']['thank_you_url']['max_length']);
        $_POST['email_notification'] = mb_substr(query_clean($_POST['email_notification']), 0, $this->biolink_blocks['product']['fields']['email_notification']['max_length']);
        $_POST['webhook_url'] = mb_substr(query_clean($_POST['webhook_url']), 0, $this->biolink_blocks['product']['fields']['webhook_url']['max_length']);

        $payment_processors = (new \Altum\Models\PaymentProcessor())->get_payment_processors_by_user_id($this->user->user_id);
        $_POST['payment_processors_ids'] = array_map(
            function($payment_processor_id) {
                return (int) $payment_processor_id;
            },
            array_filter($_POST['payment_processors_ids'] ?? [], function($payment_processor_id) use($payment_processors) {
                return array_key_exists($payment_processor_id, $payment_processors);
            })
        );

        /* Display settings */
        $this->process_display_settings();

        if(!$biolink_block = db()->where('biolink_block_id', $_POST['biolink_block_id'])->where('user_id', $this->user->user_id)->getOne('biolinks_blocks')) {
            die();
        }
        $biolink_block->settings = json_decode($biolink_block->settings);

        /* File upload */
        $db_file = $this->handle_file_upload($biolink_block->settings->file, 'file', 'file_remove', $this->biolink_blocks['product']['whitelisted_file_extensions'], \Altum\Uploads::get_path('products_files'), settings()->links->product_file_size_limit);

        /* Image upload */
        $db_image = $this->handle_image_upload($biolink_block->settings->image, 'block_thumbnail_images/', settings()->links->thumbnail_image_size_limit);

        /* Check for the removal of the already uploaded file */
        if(isset($_POST['image_remove'])) {
            /* Offload deleting */
            if(\Altum\Plugin::is_active('offload') && settings()->offload->uploads_url) {
                $s3 = new \Aws\S3\S3Client(get_aws_s3_config());
                $s3->deleteObject([
                    'Bucket' => settings()->offload->storage_name,
                    'Key' => 'uploads/block_thumbnail_images/' . $biolink_block->settings->image,
                ]);
            }

            /* Local deleting */
            else {
                /* Delete current file */
                if(!empty($biolink_block->settings->image) && file_exists(UPLOADS_PATH . 'block_thumbnail_images/' . $biolink_block->settings->image)) {
                    unlink(UPLOADS_PATH . 'block_thumbnail_images/' . $biolink_block->settings->image);
                }
            }
            $db_image = null;
        }

        $image_url = $db_image ? \Altum\Uploads::get_full_url('block_thumbnail_images') . $db_image : null;

        $settings = json_encode([
            'name' => $_POST['name'],
            'image' => $db_image,
            'text_color' => $_POST['text_color'],
            'text_alignment' => $_POST['text_alignment'],
            'background_color' => $_POST['background_color'],
            'border_radius' => $_POST['border_radius'],
            'border_width' => $_POST['border_width'],
            'border_style' => $_POST['border_style'],
            'border_color' => $_POST['border_color'],
            'border_shadow_offset_x' => $_POST['border_shadow_offset_x'],
            'border_shadow_offset_y' => $_POST['border_shadow_offset_y'],
            'border_shadow_blur' => $_POST['border_shadow_blur'],
            'border_shadow_spread' => $_POST['border_shadow_spread'],
            'border_shadow_color' => $_POST['border_shadow_color'],
            'animation' => $_POST['animation'],
            'animation_runs' => $_POST['animation_runs'],
            'icon' => $_POST['icon'],

            'file' => $db_file,
            'title' => $_POST['title'],
            'description' => $_POST['description'],
            'price' => $_POST['price'],
            'minimum_price' => $_POST['minimum_price'],
            'currency' => $_POST['currency'],
            'allow_custom_price' => $_POST['allow_custom_price'],
            'thank_you_title' => $_POST['thank_you_title'],
            'thank_you_description' => $_POST['thank_you_description'],
            'thank_you_url' => $_POST['thank_you_url'],
            'payment_processors_ids' => $_POST['payment_processors_ids'],
            'email_notification' => $_POST['email_notification'],
            'webhook_url' => $_POST['webhook_url'],

            /* Display settings */
            'display_countries' => $_POST['display_countries'],
            'display_devices' => $_POST['display_devices'],
            'display_languages' => $_POST['display_languages'],
            'display_operating_systems' => $_POST['display_operating_systems'],
        ]);

        db()->where('biolink_block_id', $_POST['biolink_block_id'])->update('biolinks_blocks', [
            'settings' => $settings,
            'start_date' => $_POST['start_date'],
            'end_date' => $_POST['end_date'],
        ]);

        /* Clear the cache */
        \Altum\Cache::$adapter->deleteItem('link?link_id=' . $biolink_block->link_id);

        Response::json(l('global.success_message.update2'), 'success', ['images' => ['image' => $image_url]]);
    }

    private function create_biolink_service() {
        $_POST['link_id'] = (int) $_POST['link_id'];
        $_POST['name'] = mb_substr(query_clean($_POST['name']), 0, 128);

        if(!$link = db()->where('link_id', $_POST['link_id'])->where('user_id', $this->user->user_id)->getOne('links')) {
            die();
        }

        $type = 'service';
        $settings = [
            'name' => $_POST['name'],
            'image' => '',
            'text_color' => 'black',
            'text_alignment' => 'center',
            'background_color' => 'white',
            'border_shadow_offset_x' => 0,
            'border_shadow_offset_y' => 0,
            'border_shadow_blur' => 20,
            'border_shadow_spread' => 0,
            'border_shadow_color' => '#00000010',
            'border_width' => 0,
            'border_style' => 'solid',
            'border_color' => 'white',
            'border_radius' => 'rounded',
            'animation' => false,
            'animation_runs' => 'repeat-1',
            'icon' => '',

            'title' => null,
            'description' => null,
            'price' => null,
            'currency' => null,
            'thank_you_title' => null,
            'thank_you_description' => null,
            'thank_you_url' => null,
            'payment_processors_ids' => [],
            'email_notification' => null,
            'webhook_url' => null,

            /* Display settings */
            'display_countries' => [],
            'display_devices' => [],
            'display_languages' => [],
            'display_operating_systems' => [],
        ];
        $settings = json_encode($settings);

        /* Database query */
        db()->insert('biolinks_blocks', [
            'user_id' => $this->user->user_id,
            'link_id' => $_POST['link_id'],
            'type' => $type,
            'location_url' => null,
            'settings' => $settings,
            'order' => $this->total_biolink_blocks,
            'datetime' => \Altum\Date::$date,
        ]);

        /* Clear the cache */
        \Altum\Cache::$adapter->deleteItem('link?link_id=' . $_POST['link_id']);

        Response::json('', 'success', ['url' => url('link/' . $_POST['link_id'] . '?tab=links')]);
    }

    private function update_biolink_service() {
        $_POST['biolink_block_id'] = (int) $_POST['biolink_block_id'];
        $_POST['name'] = mb_substr(query_clean($_POST['name']), 0, 128);
        $_POST['border_radius'] = in_array($_POST['border_radius'], ['straight', 'round', 'rounded']) ? query_clean($_POST['border_radius']) : 'rounded';
        $_POST['border_width'] = in_array($_POST['border_width'], [0, 1, 2, 3, 4, 5]) ? (int) $_POST['border_width'] : 0;
        $_POST['border_style'] = in_array($_POST['border_style'], ['solid', 'dashed', 'double', 'inset', 'outset']) ? query_clean($_POST['border_style']) : 'solid';
        $_POST['border_color'] = !preg_match('/#([A-Fa-f0-9]{3,4}){1,2}\b/i', $_POST['border_color']) ? '#000000' : $_POST['border_color'];
        $_POST['border_shadow_offset_x'] = in_array($_POST['border_shadow_offset_x'], range(-20, 20)) ? (int) $_POST['border_shadow_offset_x'] : 0;
        $_POST['border_shadow_offset_y'] = in_array($_POST['border_shadow_offset_y'], range(-20, 20)) ? (int) $_POST['border_shadow_offset_y'] : 0;
        $_POST['border_shadow_blur'] = in_array($_POST['border_shadow_blur'], range(0, 20)) ? (int) $_POST['border_shadow_blur'] : 0;
        $_POST['border_shadow_spread'] = in_array($_POST['border_shadow_spread'], range(0, 10)) ? (int) $_POST['border_shadow_spread'] : 0;
        $_POST['border_shadow_color'] = !preg_match('/#([A-Fa-f0-9]{3,4}){1,2}\b/i', $_POST['border_shadow_color']) ? '#000000' : $_POST['border_shadow_color'];
        $_POST['animation'] = in_array($_POST['animation'], require APP_PATH . 'includes/biolink_animations.php') || $_POST['animation'] == 'false' ? query_clean($_POST['animation']) : false;
        $_POST['animation_runs'] = isset($_POST['animation_runs']) && in_array($_POST['animation_runs'], ['repeat-1', 'repeat-2', 'repeat-3', 'infinite']) ? query_clean($_POST['animation_runs']) : false;
        $_POST['icon'] = query_clean($_POST['icon']);
        $_POST['text_color'] = !preg_match('/#([A-Fa-f0-9]{3,4}){1,2}\b/i', $_POST['text_color']) ? '#000000' : $_POST['text_color'];
        $_POST['text_alignment'] = in_array($_POST['text_alignment'], ['center', 'left', 'right', 'justify']) ? query_clean($_POST['text_alignment']) : 'center';
        $_POST['background_color'] = !preg_match('/#([A-Fa-f0-9]{3,4}){1,2}\b/i', $_POST['background_color']) ? '#ffffff' : $_POST['background_color'];

        $_POST['title'] = mb_substr(query_clean($_POST['title']), 0, $this->biolink_blocks['service']['fields']['title']['max_length']);
        $_POST['description'] = mb_substr(query_clean($_POST['description']), 0, $this->biolink_blocks['service']['fields']['description']['max_length']);
        $_POST['price'] = (float) $_POST['price'];
        $_POST['currency'] = mb_substr(query_clean($_POST['currency']), 0, $this->biolink_blocks['service']['fields']['currency']['max_length']);
        $_POST['thank_you_title'] = mb_substr(query_clean($_POST['thank_you_title']), 0, $this->biolink_blocks['service']['fields']['thank_you_title']['max_length']);
        $_POST['thank_you_description'] = mb_substr(query_clean($_POST['thank_you_description']), 0, $this->biolink_blocks['service']['fields']['thank_you_description']['max_length']);
        $_POST['thank_you_url'] = mb_substr(query_clean($_POST['thank_you_url']), 0, $this->biolink_blocks['donation']['fields']['thank_you_url']['max_length']);
        $_POST['email_notification'] = mb_substr(query_clean($_POST['email_notification']), 0, $this->biolink_blocks['service']['fields']['email_notification']['max_length']);
        $_POST['webhook_url'] = mb_substr(query_clean($_POST['webhook_url']), 0, $this->biolink_blocks['service']['fields']['webhook_url']['max_length']);

        $payment_processors = (new \Altum\Models\PaymentProcessor())->get_payment_processors_by_user_id($this->user->user_id);
        $_POST['payment_processors_ids'] = array_map(
            function($payment_processor_id) {
                return (int) $payment_processor_id;
            },
            array_filter($_POST['payment_processors_ids'] ?? [], function($payment_processor_id) use($payment_processors) {
                return array_key_exists($payment_processor_id, $payment_processors);
            })
        );

        /* Display settings */
        $this->process_display_settings();

        if(!$biolink_block = db()->where('biolink_block_id', $_POST['biolink_block_id'])->where('user_id', $this->user->user_id)->getOne('biolinks_blocks')) {
            die();
        }
        $biolink_block->settings = json_decode($biolink_block->settings);

        /* Image upload */
        $db_image = $this->handle_image_upload($biolink_block->settings->image, 'block_thumbnail_images/', settings()->links->thumbnail_image_size_limit);

        /* Check for the removal of the already uploaded file */
        if(isset($_POST['image_remove'])) {
            /* Offload deleting */
            if(\Altum\Plugin::is_active('offload') && settings()->offload->uploads_url) {
                $s3 = new \Aws\S3\S3Client(get_aws_s3_config());
                $s3->deleteObject([
                    'Bucket' => settings()->offload->storage_name,
                    'Key' => 'uploads/block_thumbnail_images/' . $biolink_block->settings->image,
                ]);
            }

            /* Local deleting */
            else {
                /* Delete current file */
                if(!empty($biolink_block->settings->image) && file_exists(UPLOADS_PATH . 'block_thumbnail_images/' . $biolink_block->settings->image)) {
                    unlink(UPLOADS_PATH . 'block_thumbnail_images/' . $biolink_block->settings->image);
                }
            }
            $db_image = null;
        }

        $image_url = $db_image ? \Altum\Uploads::get_full_url('block_thumbnail_images') . $db_image : null;

        $settings = json_encode([
            'name' => $_POST['name'],
            'image' => $db_image,
            'text_color' => $_POST['text_color'],
            'text_alignment' => $_POST['text_alignment'],
            'background_color' => $_POST['background_color'],
            'border_radius' => $_POST['border_radius'],
            'border_width' => $_POST['border_width'],
            'border_style' => $_POST['border_style'],
            'border_color' => $_POST['border_color'],
            'border_shadow_offset_x' => $_POST['border_shadow_offset_x'],
            'border_shadow_offset_y' => $_POST['border_shadow_offset_y'],
            'border_shadow_blur' => $_POST['border_shadow_blur'],
            'border_shadow_spread' => $_POST['border_shadow_spread'],
            'border_shadow_color' => $_POST['border_shadow_color'],
            'animation' => $_POST['animation'],
            'animation_runs' => $_POST['animation_runs'],
            'icon' => $_POST['icon'],

            'title' => $_POST['title'],
            'description' => $_POST['description'],
            'price' => $_POST['price'],
            'currency' => $_POST['currency'],
            'thank_you_title' => $_POST['thank_you_title'],
            'thank_you_description' => $_POST['thank_you_description'],
            'thank_you_url' => $_POST['thank_you_url'],
            'payment_processors_ids' => $_POST['payment_processors_ids'],
            'email_notification' => $_POST['email_notification'],
            'webhook_url' => $_POST['webhook_url'],

            /* Display settings */
            'display_countries' => $_POST['display_countries'],
            'display_devices' => $_POST['display_devices'],
            'display_languages' => $_POST['display_languages'],
            'display_operating_systems' => $_POST['display_operating_systems'],
        ]);

        db()->where('biolink_block_id', $_POST['biolink_block_id'])->update('biolinks_blocks', [
            'settings' => $settings,
            'start_date' => $_POST['start_date'],
            'end_date' => $_POST['end_date'],
        ]);

        /* Clear the cache */
        \Altum\Cache::$adapter->deleteItem('link?link_id=' . $biolink_block->link_id);

        Response::json(l('global.success_message.update2'), 'success', ['images' => ['image' => $image_url]]);
    }

    private function create_biolink_map() {
        $_POST['link_id'] = (int) $_POST['link_id'];
        $_POST['address'] = mb_substr(query_clean($_POST['address']), 0, 64);
        $_POST['location_url'] = get_url($_POST['location_url']);

        if(!$link = db()->where('link_id', $_POST['link_id'])->where('user_id', $this->user->user_id)->getOne('links')) {
            die();
        }

        $this->check_location_url($_POST['location_url'], true);

        $type = 'map';
        $settings = json_encode([
            'address' => $_POST['address'],
            'open_in_new_tab' => false,
            'zoom' => 15,
            'type' => 'roadmap',

            /* Display settings */
            'display_countries' => [],
            'display_devices' => [],
            'display_languages' => [],
            'display_operating_systems' => [],
        ]);

        /* Database query */
        db()->insert('biolinks_blocks', [
            'user_id' => $this->user->user_id,
            'link_id' => $_POST['link_id'],
            'type' => $type,
            'settings' => $settings,
            'order' => $this->total_biolink_blocks,
            'datetime' => \Altum\Date::$date,
        ]);

        /* Clear the cache */
        \Altum\Cache::$adapter->deleteItem('link?link_id=' . $_POST['link_id']);

        Response::json('', 'success', ['url' => url('link/' . $_POST['link_id'] . '?tab=links')]);
    }

    private function update_biolink_map() {
        $_POST['biolink_block_id'] = (int) $_POST['biolink_block_id'];
        $_POST['address'] = mb_substr(query_clean($_POST['address']), 0, 64);
        $_POST['open_in_new_tab'] = isset($_POST['open_in_new_tab']);
        $_POST['location_url'] = get_url($_POST['location_url']);
        $_POST['zoom'] = in_array($_POST['zoom'], range(1, 20)) ? (int) $_POST['zoom'] : 15;
        $_POST['type'] = in_array($_POST['type'], ['roadmap', 'satellite', 'hybrid', 'terrain']) ? $_POST['type'] : 'roadmap';

        /* Display settings */
        $this->process_display_settings();

        if(!$biolink_block = db()->where('biolink_block_id', $_POST['biolink_block_id'])->where('user_id', $this->user->user_id)->getOne('biolinks_blocks')) {
            die();
        }
        $biolink_block->settings = json_decode($biolink_block->settings);

        /* Check for any errors */
        $required_fields = ['address'];

        /* Check for any errors */
        foreach($required_fields as $field) {
            if(!isset($_POST[$field]) || (isset($_POST[$field]) && empty($_POST[$field]) && $_POST[$field] != '0')) {
                Response::json(l('global.error_message.empty_fields'), 'error');
                break 1;
            }
        }

        $this->check_location_url($_POST['location_url'], true);

        $settings = json_encode([
            'address' => $_POST['address'],
            'open_in_new_tab' => $_POST['open_in_new_tab'],
            'zoom' => $_POST['zoom'],
            'type' => $_POST['type'],

            /* Display settings */
            'display_countries' => $_POST['display_countries'],
            'display_devices' => $_POST['display_devices'],
            'display_languages' => $_POST['display_languages'],
            'display_operating_systems' => $_POST['display_operating_systems'],
        ]);

        /* Database query */
        db()->where('biolink_block_id', $_POST['biolink_block_id'])->update('biolinks_blocks', [
            'location_url' => $_POST['location_url'],
            'settings' => $settings,
            'start_date' => $_POST['start_date'],
            'end_date' => $_POST['end_date'],
        ]);

        /* Clear the cache */
        \Altum\Cache::$adapter->deleteItem('link?link_id=' . $biolink_block->link_id);

        Response::json(l('global.success_message.update2'), 'success');
    }

    private function create_biolink_embeddable($type) {
        $_POST['link_id'] = (int) $_POST['link_id'];
        $_POST['location_url'] = get_url($_POST['location_url']);
        $_POST['theme'] = isset($_POST['theme']) && in_array($_POST['theme'], ['light', 'dark']) ? query_clean($_POST['theme']) : null;

        $settings = [
            /* Display settings */
            'display_countries' => [],
            'display_devices' => [],
            'display_languages' => [],
            'display_operating_systems' => [],
        ];

        if($_POST['theme']) {
            $settings['theme'] = $_POST['theme'];
        }

        if(!$link = db()->where('link_id', $_POST['link_id'])->where('user_id', $this->user->user_id)->getOne('links')) {
            die();
        }

        /* Check for any errors */
        $required_fields = ['location_url'];

        /* Check for any errors */
        foreach($required_fields as $field) {
            if(!isset($_POST[$field]) || (isset($_POST[$field]) && empty($_POST[$field]) && $_POST[$field] != '0')) {
                Response::json(l('global.error_message.empty_fields'), 'error');
                break 1;
            }
        }

        $this->check_location_url($_POST['location_url']);

        /* Make sure the location url is valid & get needed details */
        $host = parse_url($_POST['location_url'], PHP_URL_HOST);

        if(isset($this->biolink_blocks[$type]['whitelisted_hosts']) && !in_array($host, $this->biolink_blocks[$type]['whitelisted_hosts'])) {
            Response::json(l('link.error_message.invalid_location_url_embed'), 'error');
        }

        switch($type) {
            case 'reddit':
                $response = Request::get('https://www.reddit.com/oembed?url=' . $_POST['location_url']);

                if($response->code >= 400) {
                    Response::json(l('link.error_message.invalid_location_url_embed'), 'error');
                }

                $settings['content'] = $response->body->html;
                break;
        }


        /* Database query */
        db()->insert('biolinks_blocks', [
            'user_id' => $this->user->user_id,
            'link_id' => $_POST['link_id'],
            'type' => $type,
            'location_url' => $_POST['location_url'],
            'settings' => json_encode($settings),
            'order' => $this->total_biolink_blocks,
            'datetime' => \Altum\Date::$date,
        ]);

        /* Clear the cache */
        \Altum\Cache::$adapter->deleteItem('link?link_id=' . $_POST['link_id']);

        Response::json('', 'success', ['url' => url('link/' . $_POST['link_id'] . '?tab=links')]);
    }

    private function update_biolink_embeddable($type) {
        $_POST['biolink_block_id'] = (int) $_POST['biolink_block_id'];
        $_POST['location_url'] = get_url($_POST['location_url']);
        $_POST['theme'] = isset($_POST['theme']) && in_array($_POST['theme'], ['light', 'dark']) ? query_clean($_POST['theme']) : null;

        /* Display settings */
        $this->process_display_settings();

        $settings = [
            /* Display settings */
            'display_countries' => $_POST['display_countries'],
            'display_devices' => $_POST['display_devices'],
            'display_languages' => $_POST['display_languages'],
            'display_operating_systems' => $_POST['display_operating_systems'],
        ];

        if($_POST['theme']) {
            $settings['theme'] = $_POST['theme'];
        }

        if(!$biolink_block = db()->where('biolink_block_id', $_POST['biolink_block_id'])->where('user_id', $this->user->user_id)->getOne('biolinks_blocks')) {
            die();
        }

        /* Check for any errors */
        $required_fields = ['location_url'];

        /* Check for any errors */
        foreach($required_fields as $field) {
            if(!isset($_POST[$field]) || (isset($_POST[$field]) && empty($_POST[$field]) && $_POST[$field] != '0')) {
                Response::json(l('global.error_message.empty_fields'), 'error');
                break 1;
            }
        }

        $this->check_location_url($_POST['location_url']);

        /* Make sure the location url is valid & get needed details */
        $host = parse_url($_POST['location_url'], PHP_URL_HOST);

        if(isset($this->biolink_blocks[$type]['whitelisted_hosts']) && !in_array($host, $this->biolink_blocks[$type]['whitelisted_hosts'])) {
            Response::json(l('link.error_message.invalid_location_url_embed'), 'error');
        }

        switch($type) {
            case 'reddit':
                $response = Request::get('https://www.reddit.com/oembed?url=' . $_POST['location_url']);

                if($response->code >= 400) {
                    Response::json(l('link.error_message.invalid_location_url_embed'), 'error');
                }

                $setting['content'] = $response->body->html;
                break;
        }

        /* Database query */
        db()->where('biolink_block_id', $_POST['biolink_block_id'])->update('biolinks_blocks', [
            'location_url' => $_POST['location_url'],
            'settings' => json_encode($settings),
            'start_date' => $_POST['start_date'],
            'end_date' => $_POST['end_date'],
        ]);

        /* Clear the cache */
        \Altum\Cache::$adapter->deleteItem('link?link_id=' . $biolink_block->link_id);

        Response::json(l('global.success_message.update2'), 'success');
    }
	 
        private function create_biolink_yamaps() {
        $_POST['link_id'] = (int) $_POST['link_id'];

        if(!$link = db()->where('link_id', $_POST['link_id'])->where('user_id', $this->user->user_id)->getOne('links')) {
            die();
        }

        $type = 'yamaps';
        $settings = json_encode([
            'items' => [],

            /* Display settings */
            'display_countries' => [],
            'display_devices' => [],
            'display_languages' => [],
            'display_operating_systems' => [],
        ]);

        /* Database query */
        db()->insert('biolinks_blocks', [
            'user_id' => $this->user->user_id,
            'link_id' => $_POST['link_id'],
            'type' => $type,
            'location_url' => null,
            'settings' => $settings,
            'order' => $this->total_biolink_blocks,
            'datetime' => \Altum\Date::$date,
        ]);

        /* Clear the cache */
        \Altum\Cache::$adapter->deleteItem('link?link_id=' . $_POST['link_id']);

        Response::json('', 'success', ['url' => url('link/' . $_POST['link_id'] . '?tab=links')]);
    }
	
	 private function update_biolink_yamaps() {
        $_POST['biolink_block_id'] = (int) $_POST['biolink_block_id'];
        if(!isset($_POST['item_title'])) {
            $_POST['item_title'] = [];
            $_POST['item_content'] = [];
            $_POST['item_lon'] = [];
            $_POST['item_lat'] = [];
        }
		
		/* Display settings */
        $this->process_display_settings();
        

        $items = [];
        foreach($_POST['item_title'] as $key => $value) {
            if(empty(trim($value))) continue;
            if($key >= 100) continue;
            
            $items[] = [
                'title' => trim(query_clean($value)),
                'content' => trim(filter_var($_POST['item_content'][$key], FILTER_SANITIZE_STRING)),
                'lon' => trim(filter_var($_POST['item_lon'][$key], FILTER_SANITIZE_STRING)),
                'lat' => trim(filter_var($_POST['item_lat'][$key], FILTER_SANITIZE_STRING)),
            ];
        } 


        if(!$biolink_block = db()->where('biolink_block_id', $_POST['biolink_block_id'])->where('user_id', $this->user->user_id)->getOne('biolinks_blocks')) {
            die();
        }

        $settings = json_encode([
            'items' => $items,
            /* Display settings */
            'display_countries' => $_POST['display_countries'],
            'display_devices' => $_POST['display_devices'],
            'display_languages' => $_POST['display_languages'],
            'display_operating_systems' => $_POST['display_operating_systems'],
        ]);

        /* Database query */
        db()->where('biolink_block_id', $_POST['biolink_block_id'])->update('biolinks_blocks', [
            'settings' => $settings,
            'start_date' => $_POST['start_date'],
            'end_date' => $_POST['end_date'],
        ]);

        /* Clear the cache */
        \Altum\Cache::$adapter->deleteItem('link?link_id=' . $biolink_block->link_id);

        Response::json(l('global.success_message.update2'), 'success');
    }
    
      private function create_biolink_cardslider() {
        $_POST['link_id'] = (int) $_POST['link_id'];

        if(!$link = db()->where('link_id', $_POST['link_id'])->where('user_id', $this->user->user_id)->getOne('links')) {
            die();
        }
        
        $type = 'cardslider';
        $settings = json_encode([
            'items' => [],
			'number_of_sliders_for_desktop' => 3,
			'number_of_sliders_for_tablet' => 2,
			'number_of_sliders_for_mobile' => 1,
			'autoplay' => true,
			'arrows' => true,
			'dots' => true,
			'text_color' => '#000',
            'background_color' => '#fff',
            /* Display settings */
            'display_countries' => [],
            'display_devices' => [],
            'display_languages' => [],
            'display_operating_systems' => [],
        ]);

        /* Database query */
        db()->insert('biolinks_blocks', [
            'user_id' => $this->user->user_id,
            'link_id' => $_POST['link_id'],
            'type' => $type,
            'location_url' => null,
            'settings' => $settings,
            'order' => $this->total_biolink_blocks,
            'datetime' => \Altum\Date::$date,
        ]);

        /* Clear the cache */
        \Altum\Cache::$adapter->deleteItem('link?link_id=' . $_POST['link_id']);

        Response::json('', 'success', ['url' => url('link/' . $_POST['link_id'] . '?tab=links')]);
    }
	
	private function update_biolink_cardslider() {
      $_POST['biolink_block_id'] = (int) $_POST['biolink_block_id'];
        if(!isset($_POST['item_title'])) {
            $_POST['item_title'] = [];
            $_POST['item_content'] = mb_substr(trim(query_clean($_POST['item_content'])), 0, 300);
        }
         $_POST['enable'] = true;
          $_POST['number_of_sliders_for_desktop'] = (int) $_POST['number_of_sliders_for_desktop'];
        $_POST['number_of_sliders_for_tablet'] = (int) $_POST['number_of_sliders_for_tablet']; 
        $_POST['number_of_sliders_for_mobile'] = (int) $_POST['number_of_sliders_for_mobile']; 
        $_POST['autoplay'] = isset($_POST['autoplay']);
        $_POST['arrows'] = isset($_POST['arrows']);
        $_POST['dots'] = isset($_POST['dots']);
           $_POST['text_color'] = !preg_match('/#([A-Fa-f0-9]{3,4}){1,2}\b/i', $_POST['text_color']) ? '#000' : $_POST['text_color'];
        $_POST['background_color'] = !preg_match('/#([A-Fa-f0-9]{3,4}){1,2}\b/i', $_POST['background_color']) ? '#fff' : $_POST['background_color'];
        
		/* Display settings */
        $this->process_display_settings();		
       
        $items = [];
        foreach($_POST['item_title'] as $key => $value) {
            if(empty(trim($value))) continue;
            if($key >= 100) continue;


            $items[] = [
                'title' => trim(query_clean($value)),
                'content' => trim(filter_var($_POST['item_content'][$key], FILTER_SANITIZE_STRING)),
                'enable' => isset($_POST['enable' . $key]),
            ];
            
        } 
        

        if(!$biolink_block = db()->where('biolink_block_id', $_POST['biolink_block_id'])->where('user_id', $this->user->user_id)->getOne('biolinks_blocks')) {
            die();
        }

        $settings = json_encode([
            'items' => $items,
             'number_of_sliders_for_desktop' => $_POST['number_of_sliders_for_desktop'],
            'number_of_sliders_for_tablet' => $_POST['number_of_sliders_for_tablet'],
            'number_of_sliders_for_mobile' => $_POST['number_of_sliders_for_mobile'],
            'autoplay' => $_POST['autoplay'],
			'arrows' => $_POST['arrows'],
			'dots' => $_POST['dots'],
            'text_color' => $_POST['text_color'],
            'background_color' => $_POST['background_color'],
            /* Display settings */
            'display_countries' => $_POST['display_countries'],
            'display_devices' => $_POST['display_devices'],
            'display_languages' => $_POST['display_languages'],
            'display_operating_systems' => $_POST['display_operating_systems'],
        ]);

        /* Database query */
        db()->where('biolink_block_id', $_POST['biolink_block_id'])->update('biolinks_blocks', [
            'settings' => $settings,
            'start_date' => $_POST['start_date'],
            'end_date' => $_POST['end_date'],
        ]);


          /* Clear the cache */
        \Altum\Cache::$adapter->deleteItem('link?link_id=' . $biolink_block->link_id);

        Response::json(l('global.success_message.update2'), 'success');
    }
    
    private function create_biolink_slider() {
        $_POST['link_id'] = (int) $_POST['link_id'];

        if(!$link = db()->where('link_id', $_POST['link_id'])->where('user_id', $this->user->user_id)->getOne('links')) {
            die();
        }
 
        $type = 'slider';
           
        $settings = json_encode([
            'items' => [],
            'title' => $_POST['title'],
			'number_of_sliders_for_desktop' => 3,
			'number_of_sliders_for_tablet' => 2,
			'number_of_sliders_for_mobile' => 1,
			'autoplay' => true,
			'arrows' => true,
			'dots' => true,
			'text_color' => '#000',
            'background_color' => '#fff',
			/* Display settings */
            'display_countries' => [],
            'display_devices' => [],
            'display_languages' => [],
            'display_operating_systems' => [],
        ]);
   

        /* Database query */
        db()->insert('biolinks_blocks', [
            'user_id' => $this->user->user_id,
            'link_id' => $_POST['link_id'],
            'type' => $type,
            'location_url' => null,
            'settings' => $settings,
            'order' => $this->total_biolink_blocks,
            'datetime' => \Altum\Date::$date,
        ]);

        /* Clear the cache */
        \Altum\Cache::$adapter->deleteItem('link?link_id=' . $_POST['link_id']);

        Response::json('', 'success', ['url' => url('link/' . $_POST['link_id'] . '?tab=links')]);
    }
	
	     private function update_biolink_slider() {
        $_POST['link_id'] = (int) $_POST['link_id'];
        $_POST['biolink_block_id'] = (int) $_POST['biolink_block_id'];
        if(!isset($_POST['item_title'])) {
            $_POST['item_title'] = [];
            $_POST['item_content'] = [];
            $_POST['item_link'] = [];
            $_POST['item_image_link'] = [];
            $_POST['enable'] = true;
        }
        $_POST['number_of_sliders_for_desktop'] = (int) $_POST['number_of_sliders_for_desktop'];
        $_POST['number_of_sliders_for_tablet'] = (int) $_POST['number_of_sliders_for_tablet']; 
        $_POST['number_of_sliders_for_mobile'] = (int) $_POST['number_of_sliders_for_mobile']; 
        $_POST['autoplay'] = isset($_POST['autoplay']);
        $_POST['arrows'] = isset($_POST['arrows']);
        $_POST['dots'] = isset($_POST['dots']);
        $_POST['titles'] = isset($_POST['titles']);
        $_POST['text_color'] = !preg_match('/#([A-Fa-f0-9]{3,4}){1,2}\b/i', $_POST['text_color']) ? '#000' : $_POST['text_color'];
        $_POST['background_color'] = !preg_match('/#([A-Fa-f0-9]{3,4}){1,2}\b/i', $_POST['background_color']) ? '#fff' : $_POST['background_color'];
        
         /* Display settings */
        $this->process_display_settings();
		
        if(!$biolink_block = db()->where('biolink_block_id', $_POST['biolink_block_id'])->where('user_id', $this->user->user_id)->getOne('biolinks_blocks')) {
            die();
        }
		
        $biolink_block->settings = json_decode($biolink_block->settings, true);	
        
        $items = [];
        $count = 1;
        foreach($_POST['item_title'] as $key => $value) {
            if($count++ >= 100) continue;
            
            
            $image = $this->handle_file_upload($biolink_block->settings->items->{$key}->image ?? null, 'item_image_' . $key, 'image_remove', ['jpg', 'jpeg', 'png', 'svg', 'ico', 'gif'], 'slider_images/', settings()->links->image_size_limit);
            
            if ($image == null) {
                        $image = $_POST['item_image_link'][$key];
         }


            $items[] = [
                'title' => input_clean($value),
                'content' => input_clean($_POST['item_content'][$key]),
				'link' => input_clean($_POST['item_link'][$key]),
                'image' => $image,
				'enable' => isset($_POST['enable' . $key])
            ];
        }

        /* Make sure to delete old images if needed */
        foreach($biolink_block->settings->items as $key => $item) {
            if((isset($items[$key]) && $items[$key]['image'] != $item->image) || !isset($items[$key])) {
                \Altum\Uploads::delete_uploaded_file($item->image, 'slider_images');
            }
        }

        $settings = json_encode([
            'items' => (array) $items,
            'number_of_sliders_for_desktop' => $_POST['number_of_sliders_for_desktop'],
            'number_of_sliders_for_tablet' => $_POST['number_of_sliders_for_tablet'],
            'number_of_sliders_for_mobile' => $_POST['number_of_sliders_for_mobile'],
            'autoplay' => $_POST['autoplay'],
			'arrows' => $_POST['arrows'],
			'dots' => $_POST['dots'],
			'titles' => $_POST['titles'],
			'text_color' => $_POST['text_color'],
            'background_color' => $_POST['background_color'],
            /* Display settings */
            'display_countries' => $_POST['display_countries'],
            'display_devices' => $_POST['display_devices'],
            'display_languages' => $_POST['display_languages'],
            'display_operating_systems' => $_POST['display_operating_systems'],
        ]);

        /* Database query */
        db()->where('biolink_block_id', $_POST['biolink_block_id'])->update('biolinks_blocks', [
            'settings' => $settings,
            'start_date' => $_POST['start_date'],
            'end_date' => $_POST['end_date'],
        ]);


        /* Clear the cache */
     \Altum\Cache::$adapter->deleteItem('biolinks_links_user_' . $this->user->user_id);
		\Altum\Cache::$adapter->deleteItem('link_id=' . $biolink_block->link_id);
		
		\Altum\Cache::$adapter->deleteItem('link?link_id=' . $biolink_block->link_id);
          

       Response::json("<meta http-equiv='refresh' content='1'> Saved! Reloading the page...", 'success', ['url' => url('link/' . $_POST['link_id'] . '?tab=links')]);
       
       // Response::json(l('global.success_message.update2'), 'success');
       

        

    }
    
    private function create_biolink_tmreview() {
       $_POST['link_id'] = (int) $_POST['link_id'];

        if(!$link = db()->where('link_id', $_POST['link_id'])->where('user_id', $this->user->user_id)->getOne('links')) {
            die();
        }

        $type = 'tmreview';
        
         $settings = json_encode([
            'items' => [],
            'item_title' => $_POST['item_title'],
			'text_color' => '#000',
            'background_color' => '#fff',
			/* Display settings */
            'display_countries' => [],
            'display_devices' => [],
            'display_languages' => [],
            'display_operating_systems' => [],
        ]);
        
   
        /* Database query */
        db()->insert('biolinks_blocks', [
            'user_id' => $this->user->user_id,
            'link_id' => $_POST['link_id'],
            'type' => $type,
            'location_url' => null,
            'settings' => $settings,
            'order' => $this->total_biolink_blocks,
            'datetime' => \Altum\Date::$date,
        ]);

        /* Clear the cache */
        \Altum\Cache::$adapter->deleteItem('link?link_id=' . $_POST['link_id']);

        Response::json('', 'success', ['url' => url('link/' . $_POST['link_id'] . '?tab=links')]);
    }
	
	    private function update_biolink_tmreview() {
   $_POST['link_id'] = (int) $_POST['link_id'];
        $_POST['biolink_block_id'] = (int) $_POST['biolink_block_id'];
        if(!isset($_POST['item_title'])) {
            $_POST['item_title'] = [];
            $_POST['item_content'] = [];
            $_POST['item_subcontent'] = [];
            $_POST['item_image_link'] = [];
            $_POST['enable'] = true;
        }
      
        $_POST['text_color'] = !preg_match('/#([A-Fa-f0-9]{3,4}){1,2}\b/i', $_POST['text_color']) ? '#000' : $_POST['text_color'];
        $_POST['background_color'] = !preg_match('/#([A-Fa-f0-9]{3,4}){1,2}\b/i', $_POST['background_color']) ? '#fff' : $_POST['background_color'];
        
          /* Display settings */
        $this->process_display_settings();
        
        if(!$biolink_block = db()->where('biolink_block_id', $_POST['biolink_block_id'])->where('user_id', $this->user->user_id)->getOne('biolinks_blocks')) {
            die();
        }
		
        $biolink_block->settings = json_decode($biolink_block->settings, true);	
        

        $items = [];
        $count = 1;
        foreach($_POST['item_title'] as $key => $value) {
            if($count++ >= 100) continue;
            
            
            $image = $this->handle_file_upload($biolink_block->settings->items->{$key}->image ?? null, 'image' . $key, 'image_remove', ['jpg', 'jpeg', 'png', 'svg', 'ico', 'gif'], 'tmreview_images/', settings()->links->image_size_limit);
            
            if ($image == null) {
                        $image = $_POST['item_image_link'][$key];
         }


            $items[] = [
                'title' => input_clean($value),
                'content' => input_clean($_POST['item_content'][$key]),
                'subcontent' => input_clean($_POST['item_subcontent'][$key]),
                'image' => $image,
				'enable' => isset($_POST['enable' . $key])
            ];
        }

        /* Make sure to delete old images if needed */
        foreach($biolink_block->settings->items as $key => $item) {
            if((isset($items[$key]) && $items[$key]['image'] != $item->image) || !isset($items[$key])) {
                \Altum\Uploads::delete_uploaded_file($item->image, 'tmreview_images');
            }
        }
         

        $settings = json_encode([
            'items' => (array) $items,
			'text_color' => $_POST['text_color'],
            'background_color' => $_POST['background_color'],
            /* Display settings */
            'display_countries' => $_POST['display_countries'],
            'display_devices' => $_POST['display_devices'],
            'display_languages' => $_POST['display_languages'],
            'display_operating_systems' => $_POST['display_operating_systems'],
        ]);

        /* Database query */
        db()->where('biolink_block_id', $_POST['biolink_block_id'])->update('biolinks_blocks', [
            'settings' => $settings,
            'start_date' => $_POST['start_date'],
            'end_date' => $_POST['end_date'],
        ]);
        


        /* Clear the cache */
     \Altum\Cache::$adapter->deleteItem('biolinks_links_user_' . $this->user->user_id);
		\Altum\Cache::$adapter->deleteItem('link_id=' . $biolink_block->link_id);
		
		\Altum\Cache::$adapter->deleteItem('link?link_id=' . $biolink_block->link_id);
          

        Response::json("<meta http-equiv='refresh' content='1'> Saved! Reloading the page...", 'success', ['url' => url('link/' . $_POST['link_id'] . '?tab=links')]);
       
       // Response::json(l('global.success_message.update2'), 'success');
      

        
    }
    
     private function create_biolink_preloader() {
        $_POST['link_id'] = (int) $_POST['link_id'];

        if(!$link = db()->where('link_id', $_POST['link_id'])->where('user_id', $this->user->user_id)->getOne('links')) {
            die();
        }

        $type = 'preloader';
        $settings = json_encode([
            'background_color' => '#2F4F4F',

            /* Display settings */
            'display_countries' => [],
            'display_devices' => [],
            'display_languages' => [],
            'display_operating_systems' => [],
        ]);

        /* Database query */
        db()->insert('biolinks_blocks', [
            'user_id' => $this->user->user_id,
            'link_id' => $_POST['link_id'],
            'type' => $type,
            'location_url' => null,
            'settings' => $settings,
            'order' => $this->total_biolink_blocks,
            'datetime' => \Altum\Date::$date,
        ]);

        /* Clear the cache */
        \Altum\Cache::$adapter->deleteItem('link?link_id=' . $_POST['link_id']);

        Response::json('', 'success', ['url' => url('link/' . $_POST['link_id'] . '?tab=links')]);
    }
	
	     private function update_biolink_preloader() {
        $_POST['biolink_block_id'] = (int) $_POST['biolink_block_id'];    
        $_POST['background_color'] = !preg_match('/#([A-Fa-f0-9]{3,4}){1,2}\b/i', $_POST['background_color']) ? '#fff' : $_POST['background_color'];

		/* Display settings */
        $this->process_display_settings();
		

        if(!$biolink_block = db()->where('biolink_block_id', $_POST['biolink_block_id'])->where('user_id', $this->user->user_id)->getOne('biolinks_blocks')) {
            die();
        }

        $settings = json_encode([
              'background_color' => $_POST['background_color'],
            /* Display settings */
            'display_countries' => $_POST['display_countries'],
            'display_devices' => $_POST['display_devices'],
            'display_languages' => $_POST['display_languages'],
            'display_operating_systems' => $_POST['display_operating_systems'],
        ]);

        /* Database query */
        db()->where('biolink_block_id', $_POST['biolink_block_id'])->update('biolinks_blocks', [
            'settings' => $settings,
            'start_date' => $_POST['start_date'],
            'end_date' => $_POST['end_date'],
        ]);

        /* Clear the cache */
        \Altum\Cache::$adapter->deleteItem('link?link_id=' . $biolink_block->link_id);

        Response::json(l('global.success_message.update2'), 'success');
    }
    
    private function create_biolink_tmscrollindicator() {
        $_POST['link_id'] = (int) $_POST['link_id'];

        if(!$link = db()->where('link_id', $_POST['link_id'])->where('user_id', $this->user->user_id)->getOne('links')) {
            die();
        }

        $type = 'tmscrollindicator';
        $settings = json_encode([
            'background_color' => '#2F4F4F',

            /* Display settings */
            'display_countries' => [],
            'display_devices' => [],
            'display_languages' => [],
            'display_operating_systems' => [],
        ]);

        /* Database query */
        db()->insert('biolinks_blocks', [
            'user_id' => $this->user->user_id,
            'link_id' => $_POST['link_id'],
            'type' => $type,
            'location_url' => null,
            'settings' => $settings,
            'order' => $this->total_biolink_blocks,
            'datetime' => \Altum\Date::$date,
        ]);

        /* Clear the cache */
        \Altum\Cache::$adapter->deleteItem('link?link_id=' . $_POST['link_id']);

        Response::json('', 'success', ['url' => url('link/' . $_POST['link_id'] . '?tab=links')]);
    }
	
	   private function update_biolink_tmscrollindicator() {
        $_POST['biolink_block_id'] = (int) $_POST['biolink_block_id'];
       
		/* Display settings */
        $this->process_display_settings();


        if(!$biolink_block = db()->where('biolink_block_id', $_POST['biolink_block_id'])->where('user_id', $this->user->user_id)->getOne('biolinks_blocks')) {
            die();
        }

        $settings = json_encode([

            /* Display settings */
            'display_countries' => $_POST['display_countries'],
            'display_devices' => $_POST['display_devices'],
            'display_languages' => $_POST['display_languages'],
            'display_operating_systems' => $_POST['display_operating_systems'],
        ]);

        /* Database query */
        db()->where('biolink_block_id', $_POST['biolink_block_id'])->update('biolinks_blocks', [
            'settings' => $settings,
            'start_date' => $_POST['start_date'],
            'end_date' => $_POST['end_date'],
        ]);

        /* Clear the cache */
        \Altum\Cache::$adapter->deleteItem('link?link_id=' . $biolink_block->link_id);

        Response::json(l('global.success_message.update2'), 'success');
    }
    
     private function create_biolink_modal() {
        $_POST['link_id'] = (int) $_POST['link_id'];
        $_POST['text'] = mb_substr(trim(filter_var($_POST['text'], FILTER_SANITIZE_STRING)), 0, 2048);

        if(!$link = db()->where('link_id', $_POST['link_id'])->where('user_id', $this->user->user_id)->getOne('links')) {
            die();
        }

        $type = 'modal';
        $settings = json_encode([
            'text' => $_POST['text'],
            'text_color' => 'black',
            'background_color' => 'white',

            /* Display settings */
            'display_countries' => [],
            'display_devices' => [],
            'display_languages' => [],
            'display_operating_systems' => [],
        ]);

        /* Database query */
        db()->insert('biolinks_blocks', [
            'user_id' => $this->user->user_id,
            'link_id' => $_POST['link_id'],
            'type' => $type,
            'location_url' => null,
            'settings' => $settings,
            'order' => $this->total_biolink_blocks,
            'datetime' => \Altum\Date::$date,
        ]);

        /* Clear the cache */
        \Altum\Cache::$adapter->deleteItem('link?link_id=' . $_POST['link_id']);

        Response::json('', 'success', ['url' => url('link/' . $_POST['link_id'] . '?tab=links')]);
    }
	
	   private function update_biolink_modal() {
        $_POST['biolink_block_id'] = (int) $_POST['biolink_block_id'];
        $_POST['title'] = mb_substr(trim(filter_var($_POST['title'], FILTER_SANITIZE_STRING)), 0, 2048);
        $_POST['text'] = mb_substr(trim(filter_var($_POST['text'], FILTER_SANITIZE_STRING)), 0, 2048);            
        $_POST['text_color'] = !preg_match('/#([A-Fa-f0-9]{3,4}){1,2}\b/i', $_POST['text_color']) ? '#000' : $_POST['text_color'];
        $_POST['background_color'] = !preg_match('/#([A-Fa-f0-9]{3,4}){1,2}\b/i', $_POST['background_color']) ? '#fff' : $_POST['background_color'];

		/* Display settings */
        $this->process_display_settings();

        if(!$biolink_block = db()->where('biolink_block_id', $_POST['biolink_block_id'])->where('user_id', $this->user->user_id)->getOne('biolinks_blocks')) {
            die();
        }

        $settings = json_encode([
            'title' => $_POST['title'],
            'text' => $_POST['text'],
            'text_color' => $_POST['text_color'],
            'background_color' => $_POST['background_color'],

            /* Display settings */
            'display_countries' => $_POST['display_countries'],
            'display_devices' => $_POST['display_devices'],
            'display_languages' => $_POST['display_languages'],
            'display_operating_systems' => $_POST['display_operating_systems'],
        ]);

        /* Database query */
        db()->where('biolink_block_id', $_POST['biolink_block_id'])->update('biolinks_blocks', [
            'settings' => $settings,
            'start_date' => $_POST['start_date'],
            'end_date' => $_POST['end_date'],
        ]);

        /* Clear the cache */
        \Altum\Cache::$adapter->deleteItem('link?link_id=' . $biolink_block->link_id);

        Response::json(l('global.success_message.update2'), 'success');
    }
    
    private function create_biolink_tmnotification() {
        $_POST['link_id'] = (int) $_POST['link_id'];

        if(!$link = db()->where('link_id', $_POST['link_id'])->where('user_id', $this->user->user_id)->getOne('links')) {
            die();
        }

        $type = 'tmnotification';
        $settings = json_encode([
            'items' => [],
            'text_color' => 'black',
            'background_color' => 'white',

            /* Display settings */
            'display_countries' => [],
            'display_devices' => [],
            'display_languages' => [],
            'display_operating_systems' => [],
        ]);

        /* Database query */
        db()->insert('biolinks_blocks', [
            'user_id' => $this->user->user_id,
            'link_id' => $_POST['link_id'],
            'type' => $type,
            'location_url' => null,
            'settings' => $settings,
            'order' => $this->total_biolink_blocks,
            'datetime' => \Altum\Date::$date,
        ]);

        /* Clear the cache */
        \Altum\Cache::$adapter->deleteItem('link?link_id=' . $_POST['link_id']);

        Response::json('', 'success', ['url' => url('link/' . $_POST['link_id'] . '?tab=links')]);
    }
	
	    private function update_biolink_tmnotification() {
       
         $_POST['biolink_block_id'] = (int) $_POST['biolink_block_id'];
        if(!isset($_POST['item_title'])) {
            $_POST['item_title'] = [];
            $_POST['item_content'] = mb_substr(trim(query_clean($_POST['item_content'])), 0, 2048);
            $_POST['item_icon'] = in_array($_POST['icon'], ['error', 'warning', 'success', 'message', 'clock', 'up']) ? query_clean($_POST['item_icon']) : 'message';
        }
		
		/* Display settings */
        $this->process_display_settings();		
          

        $items = [];
        foreach($_POST['item_title'] as $key => $value) {
            if(empty(trim($value))) continue;
            if($key >= 100) continue;
            
            $items[] = [
                'title' => trim(query_clean($value)),
                'content' => trim(filter_var($_POST['item_content'][$key], FILTER_SANITIZE_STRING)),
                'icon' => $_POST['item_icon'][$key]
            ];
        } 

        if(!$biolink_block = db()->where('biolink_block_id', $_POST['biolink_block_id'])->where('user_id', $this->user->user_id)->getOne('biolinks_blocks')) {
            die();
        }

        $settings = json_encode([
            'items' => $items,

            /* Display settings */
            'display_countries' => $_POST['display_countries'],
            'display_devices' => $_POST['display_devices'],
            'display_languages' => $_POST['display_languages'],
            'display_operating_systems' => $_POST['display_operating_systems'],
        ]);

        /* Database query */
        db()->where('biolink_block_id', $_POST['biolink_block_id'])->update('biolinks_blocks', [
            'settings' => $settings,
            'start_date' => $_POST['start_date'],
            'end_date' => $_POST['end_date'],
        ]);

        /* Clear the cache */
        \Altum\Cache::$adapter->deleteItem('link?link_id=' . $biolink_block->link_id);

        Response::json(l('global.success_message.update2'), 'success');
    }
    
    private function create_biolink_menu() {
        $_POST['link_id'] = (int) $_POST['link_id'];

        if(!$link = db()->where('link_id', $_POST['link_id'])->where('user_id', $this->user->user_id)->getOne('links')) {
            die();
        }

        $type = 'menu';
        $settings = json_encode([
            'items' => [],
            'text_color' => 'black',
            'background_color' => 'white',
            /* Display settings */
            'display_countries' => [],
            'display_devices' => [],
            'display_languages' => [],
            'display_operating_systems' => [],
        ]);

        /* Database query */
        db()->insert('biolinks_blocks', [
            'user_id' => $this->user->user_id,
            'link_id' => $_POST['link_id'],
            'type' => $type,
            'location_url' => null,
            'settings' => $settings,
            'order' => $this->total_biolink_blocks,
            'datetime' => \Altum\Date::$date,
        ]);

        /* Clear the cache */
        \Altum\Cache::$adapter->deleteItem('link?link_id=' . $_POST['link_id']);

        Response::json('', 'success', ['url' => url('link/' . $_POST['link_id'] . '?tab=links')]);
    }
	
	   private function update_biolink_menu() {
        $_POST['biolink_block_id'] = (int) $_POST['biolink_block_id'];
        if(!isset($_POST['item_title'])) {
            $_POST['item_title'] = [];
            $_POST['item_content'] = mb_substr(trim(query_clean($_POST['item_content'])), 0, 2048);;
        }
           $_POST['text_color'] = !preg_match('/#([A-Fa-f0-9]{3,4}){1,2}\b/i', $_POST['text_color']) ? '#000' : $_POST['text_color'];
        $_POST['background_color'] = !preg_match('/#([A-Fa-f0-9]{3,4}){1,2}\b/i', $_POST['background_color']) ? '#fff' : $_POST['background_color'];

		/* Display settings */
        $this->process_display_settings();
        
        $items = [];
        foreach($_POST['item_title'] as $key => $value) {
            if(empty(trim($value))) continue;
            if($key >= 100) continue;
            
            $items[] = [
                'title' => trim(query_clean($value)),
                'content' => trim(filter_var($_POST['item_content'][$key], FILTER_SANITIZE_STRING)),
            ];
        } 


        if(!$biolink_block = db()->where('biolink_block_id', $_POST['biolink_block_id'])->where('user_id', $this->user->user_id)->getOne('biolinks_blocks')) {
            die();
        }

        $settings = json_encode([
            'items' => $items,
            'text_color' => $_POST['text_color'],
            'background_color' => $_POST['background_color'],
            /* Display settings */
            'display_countries' => $_POST['display_countries'],
            'display_devices' => $_POST['display_devices'],
            'display_languages' => $_POST['display_languages'],
            'display_operating_systems' => $_POST['display_operating_systems'],
        ]);

        /* Database query */
        db()->where('biolink_block_id', $_POST['biolink_block_id'])->update('biolinks_blocks', [
            'settings' => $settings,
            'start_date' => $_POST['start_date'],
            'end_date' => $_POST['end_date'],
        ]);

        /* Clear the cache */
        \Altum\Cache::$adapter->deleteItem('link?link_id=' . $biolink_block->link_id);

        Response::json(l('global.success_message.update2'), 'success');
    }
    
    private function create_biolink_tmscrollcards() {
        $_POST['link_id'] = (int) $_POST['link_id'];

        if(!$link = db()->where('link_id', $_POST['link_id'])->where('user_id', $this->user->user_id)->getOne('links')) {
            die();
        }

        $type = 'tmscrollcards';
        $settings = json_encode([
            'items' => [],
            'text_color' => 'black',
            'background_color' => 'white',
            /* Display settings */
            'display_countries' => [],
            'display_devices' => [],
            'display_languages' => [],
            'display_operating_systems' => [],
        ]);

        /* Database query */
        db()->insert('biolinks_blocks', [
            'user_id' => $this->user->user_id,
            'link_id' => $_POST['link_id'],
            'type' => $type,
            'location_url' => null,
            'settings' => $settings,
            'order' => $this->total_biolink_blocks,
            'datetime' => \Altum\Date::$date,
        ]);

        /* Clear the cache */
        \Altum\Cache::$adapter->deleteItem('link?link_id=' . $_POST['link_id']);

        Response::json('', 'success', ['url' => url('link/' . $_POST['link_id'] . '?tab=links')]);
    }
	
	    private function update_biolink_tmscrollcards() {
        $_POST['biolink_block_id'] = (int) $_POST['biolink_block_id'];
        if(!isset($_POST['item_title'])) {
            $_POST['item_title'] = [];
            $_POST['item_content'] = mb_substr(trim(query_clean($_POST['item_content'])), 0, 2048);;
        }
           $_POST['text_color'] = !preg_match('/#([A-Fa-f0-9]{3,4}){1,2}\b/i', $_POST['text_color']) ? '#000' : $_POST['text_color'];
        $_POST['background_color'] = !preg_match('/#([A-Fa-f0-9]{3,4}){1,2}\b/i', $_POST['background_color']) ? '#fff' : $_POST['background_color'];

		/* Display settings */
        $this->process_display_settings();        

        $items = [];
        foreach($_POST['item_title'] as $key => $value) {
            if(empty(trim($value))) continue;
            if($key >= 100) continue;
            
            $items[] = [
                'title' => trim(query_clean($value)),
                'content' => trim(filter_var($_POST['item_content'][$key], FILTER_SANITIZE_STRING)),
            ];
        } 


        if(!$biolink_block = db()->where('biolink_block_id', $_POST['biolink_block_id'])->where('user_id', $this->user->user_id)->getOne('biolinks_blocks')) {
            die();
        }

        $settings = json_encode([
            'items' => $items,
            'text_color' => $_POST['text_color'],
            'background_color' => $_POST['background_color'],
            /* Display settings */
            'display_countries' => $_POST['display_countries'],
            'display_devices' => $_POST['display_devices'],
            'display_languages' => $_POST['display_languages'],
            'display_operating_systems' => $_POST['display_operating_systems'],
        ]);

        /* Database query */
        db()->where('biolink_block_id', $_POST['biolink_block_id'])->update('biolinks_blocks', [
            'settings' => $settings,
            'start_date' => $_POST['start_date'],
            'end_date' => $_POST['end_date'],
        ]);

        /* Clear the cache */
        \Altum\Cache::$adapter->deleteItem('link?link_id=' . $biolink_block->link_id);

        Response::json(l('global.success_message.update2'), 'success');
    }
    
    private function create_biolink_tmprogress() {
        $_POST['link_id'] = (int) $_POST['link_id'];

        if(!$link = db()->where('link_id', $_POST['link_id'])->where('user_id', $this->user->user_id)->getOne('links')) {
            die();
        }

        $type = 'tmprogress';
        $settings = json_encode([
            'items' => [],
            'text_color' => '#222',
            'background_color' => '#00A183',

            /* Display settings */
            'display_countries' => [],
            'display_devices' => [],
            'display_languages' => [],
            'display_operating_systems' => [],
        ]);

        /* Database query */
        db()->insert('biolinks_blocks', [
            'user_id' => $this->user->user_id,
            'link_id' => $_POST['link_id'],
            'type' => $type,
            'location_url' => null,
            'settings' => $settings,
            'order' => $this->total_biolink_blocks,
            'datetime' => \Altum\Date::$date,
        ]);

        /* Clear the cache */
        \Altum\Cache::$adapter->deleteItem('link?link_id=' . $_POST['link_id']);

        Response::json('', 'success', ['url' => url('link/' . $_POST['link_id'] . '?tab=links')]);
    }
	
	    private function update_biolink_tmprogress() {
        $_POST['biolink_block_id'] = (int) $_POST['biolink_block_id'];
        if(!isset($_POST['item_title'])) {
            $_POST['item_title'] = [];
            $_POST['item_content'] = mb_substr(trim(query_clean($_POST['item_content'])), 0, 2048);;
        }
           $_POST['text_color'] = !preg_match('/#([A-Fa-f0-9]{3,4}){1,2}\b/i', $_POST['text_color']) ? '#000' : $_POST['text_color'];
        $_POST['background_color'] = !preg_match('/#([A-Fa-f0-9]{3,4}){1,2}\b/i', $_POST['background_color']) ? '#fff' : $_POST['background_color'];
		
		/* Display settings */
        $this->process_display_settings();
        

        $items = [];
        foreach($_POST['item_title'] as $key => $value) {
            if(empty(trim($value))) continue;
            if($key >= 100) continue;
            
            $items[] = [
                'title' => trim(query_clean($value)),
                'content' => trim(filter_var($_POST['item_content'][$key], FILTER_SANITIZE_STRING)),
            ];
        } 

        if(!$biolink_block = db()->where('biolink_block_id', $_POST['biolink_block_id'])->where('user_id', $this->user->user_id)->getOne('biolinks_blocks')) {
            die();
        }

        $settings = json_encode([
            'items' => $items,
            'text_color' => $_POST['text_color'],
            'background_color' => $_POST['background_color'],
            /* Display settings */
            'display_countries' => $_POST['display_countries'],
            'display_devices' => $_POST['display_devices'],
            'display_languages' => $_POST['display_languages'],
            'display_operating_systems' => $_POST['display_operating_systems'],
        ]);

        /* Database query */
        db()->where('biolink_block_id', $_POST['biolink_block_id'])->update('biolinks_blocks', [
            'settings' => $settings,
            'start_date' => $_POST['start_date'],
            'end_date' => $_POST['end_date'],
        ]);

        /* Clear the cache */
        \Altum\Cache::$adapter->deleteItem('link?link_id=' . $biolink_block->link_id);

        Response::json(l('global.success_message.update2'), 'success');
    }
    
    private function create_biolink_tmticker() {
        $_POST['link_id'] = (int) $_POST['link_id'];

        if(!$link = db()->where('link_id', $_POST['link_id'])->where('user_id', $this->user->user_id)->getOne('links')) {
            die();
        }

        $type = 'tmticker';
        $settings = json_encode([
            'items' => [],
            'text_color' => '#fff',
            'background_color' => '#dc3545',

            /* Display settings */
            'display_countries' => [],
            'display_devices' => [],
            'display_languages' => [],
            'display_operating_systems' => [],
        ]);

        /* Database query */
        db()->insert('biolinks_blocks', [
            'user_id' => $this->user->user_id,
            'link_id' => $_POST['link_id'],
            'type' => $type,
            'location_url' => null,
            'settings' => $settings,
            'order' => $this->total_biolink_blocks,
            'datetime' => \Altum\Date::$date,
        ]);

        /* Clear the cache */
        \Altum\Cache::$adapter->deleteItem('link?link_id=' . $_POST['link_id']);

        Response::json('', 'success', ['url' => url('link/' . $_POST['link_id'] . '?tab=links')]);
    }
	
	    private function update_biolink_tmticker() {
        $_POST['biolink_block_id'] = (int) $_POST['biolink_block_id'];
        if(!isset($_POST['item_title'])) {
            $_POST['item_title'] = [];
            $_POST['item_content'] = mb_substr(trim(query_clean($_POST['item_content'])), 0, 2048);
        }
           $_POST['text_color'] = !preg_match('/#([A-Fa-f0-9]{3,4}){1,2}\b/i', $_POST['text_color']) ? '#000' : $_POST['text_color'];
        $_POST['background_color'] = !preg_match('/#([A-Fa-f0-9]{3,4}){1,2}\b/i', $_POST['background_color']) ? '#fff' : $_POST['background_color'];
         $_POST['block_title'] = mb_substr(trim(query_clean($_POST['block_title'])), 0, 2048);

		/* Display settings */
        $this->process_display_settings();        

        $items = [];
        foreach($_POST['item_title'] as $key => $value) {
            if(empty(trim($value))) continue;
            if($key >= 100) continue;
            
            $items[] = [
                'title' => trim(query_clean($value)),
                'content' => trim(filter_var($_POST['item_content'][$key], FILTER_SANITIZE_STRING)),
            ];
        } 


        if(!$biolink_block = db()->where('biolink_block_id', $_POST['biolink_block_id'])->where('user_id', $this->user->user_id)->getOne('biolinks_blocks')) {
            die();
        }

        $settings = json_encode([
            'items' => $items,
            'block_title' => $_POST['block_title'],
            'text_color' => $_POST['text_color'],
            'background_color' => $_POST['background_color'],
            /* Display settings */
            'display_countries' => $_POST['display_countries'],
            'display_devices' => $_POST['display_devices'],
            'display_languages' => $_POST['display_languages'],
            'display_operating_systems' => $_POST['display_operating_systems'],
        ]);

        /* Database query */
        db()->where('biolink_block_id', $_POST['biolink_block_id'])->update('biolinks_blocks', [
            'settings' => $settings,
            'start_date' => $_POST['start_date'],
            'end_date' => $_POST['end_date'],
        ]);

        /* Clear the cache */
        \Altum\Cache::$adapter->deleteItem('link?link_id=' . $biolink_block->link_id);

        Response::json(l('global.success_message.update2'), 'success');
    }
    
     private function create_biolink_tmpiechart() {
        $_POST['link_id'] = (int) $_POST['link_id'];

        if(!$link = db()->where('link_id', $_POST['link_id'])->where('user_id', $this->user->user_id)->getOne('links')) {
            die();
        }

        $type = 'tmpiechart';
        $settings = json_encode([
            'items' => [],
            'text_color' => '#222',
            'background_color' => '#fff',

            /* Display settings */
            'display_countries' => [],
            'display_devices' => [],
            'display_languages' => [],
            'display_operating_systems' => [],
        ]);

        /* Database query */
        db()->insert('biolinks_blocks', [
            'user_id' => $this->user->user_id,
            'link_id' => $_POST['link_id'],
            'type' => $type,
            'location_url' => null,
            'settings' => $settings,
            'order' => $this->total_biolink_blocks,
            'datetime' => \Altum\Date::$date,
        ]);

        /* Clear the cache */
        \Altum\Cache::$adapter->deleteItem('link?link_id=' . $_POST['link_id']);

        Response::json('', 'success', ['url' => url('link/' . $_POST['link_id'] . '?tab=links')]);
    }
	
	    private function update_biolink_tmpiechart() {
        $_POST['biolink_block_id'] = (int) $_POST['biolink_block_id'];
        if(!isset($_POST['item_title'])) {
            $_POST['item_title'] = [];
            $_POST['item_content'] = mb_substr(trim(query_clean($_POST['item_content'])), 0, 2048);
            $_POST['item_color'] = in_array($_POST['color'], ['white', 'silver', 'gray', 'black', 'red', 'maroon', 'yellow' , 'olive', 'lime', 'green', 'aqua', 'teal', 'blue', 'navy', 'fuchsia', 'purple']) ? query_clean($_POST['item_color']) : 'teal';
        }
           $_POST['text_color'] = !preg_match('/#([A-Fa-f0-9]{3,4}){1,2}\b/i', $_POST['text_color']) ? '#000' : $_POST['text_color'];
        $_POST['background_color'] = !preg_match('/#([A-Fa-f0-9]{3,4}){1,2}\b/i', $_POST['background_color']) ? '#fff' : $_POST['background_color'];
        $_POST['title_block'] = mb_substr(trim(query_clean($_POST['title_block'])), 0, 2048);

		/* Display settings */
        $this->process_display_settings();        

        $items = [];
        foreach($_POST['item_title'] as $key => $value) {
            if(empty(trim($value))) continue;
            if($key >= 100) continue;
            
            $items[] = [
                'title' => trim(query_clean($value)),
                'content' => trim(filter_var($_POST['item_content'][$key], FILTER_SANITIZE_STRING)),
                'color' => $_POST['item_color'][$key]
            ];
        } 

        if(!$biolink_block = db()->where('biolink_block_id', $_POST['biolink_block_id'])->where('user_id', $this->user->user_id)->getOne('biolinks_blocks')) {
            die();
        }

        $settings = json_encode([
            'items' => $items,
            'text_color' => $_POST['text_color'],
            'background_color' => $_POST['background_color'],
            'title_block' => $_POST['title_block'],
            /* Display settings */
            'display_countries' => $_POST['display_countries'],
            'display_devices' => $_POST['display_devices'],
            'display_languages' => $_POST['display_languages'],
            'display_operating_systems' => $_POST['display_operating_systems'],
        ]);

        /* Database query */
        db()->where('biolink_block_id', $_POST['biolink_block_id'])->update('biolinks_blocks', [
            'settings' => $settings,
            'start_date' => $_POST['start_date'],
            'end_date' => $_POST['end_date'],
        ]);

        /* Clear the cache */
        \Altum\Cache::$adapter->deleteItem('link?link_id=' . $biolink_block->link_id);

        Response::json(l('global.success_message.update2'), 'success');
    }
    
     private function create_biolink_tmtimeline() {
        $_POST['link_id'] = (int) $_POST['link_id'];

        if(!$link = db()->where('link_id', $_POST['link_id'])->where('user_id', $this->user->user_id)->getOne('links')) {
            die();
        }

        $type = 'tmtimeline';
        $settings = json_encode([
            'items' => [],
           'text_color' => '#fff',
            /* Display settings */
            'display_countries' => [],
            'display_devices' => [],
            'display_languages' => [],
            'display_operating_systems' => [],
        ]);

        /* Database query */
        db()->insert('biolinks_blocks', [
            'user_id' => $this->user->user_id,
            'link_id' => $_POST['link_id'],
            'type' => $type,
            'location_url' => null,
            'settings' => $settings,
            'order' => $this->total_biolink_blocks,
            'datetime' => \Altum\Date::$date,
        ]);

        /* Clear the cache */
        \Altum\Cache::$adapter->deleteItem('link?link_id=' . $_POST['link_id']);

        Response::json('', 'success', ['url' => url('link/' . $_POST['link_id'] . '?tab=links')]);
    }
	
	    private function update_biolink_tmtimeline() {
        $_POST['biolink_block_id'] = (int) $_POST['biolink_block_id'];
        if(!isset($_POST['item_title'])) {
            $_POST['item_title'] = [];
            $_POST['item_content'] = mb_substr(trim(query_clean($_POST['item_content'])), 0, 2048);;
        }
           $_POST['text_color'] = !preg_match('/#([A-Fa-f0-9]{3,4}){1,2}\b/i', $_POST['text_color']) ? '#000' : $_POST['text_color'];

		/* Display settings */
        $this->process_display_settings();
        
        $items = [];
        foreach($_POST['item_title'] as $key => $value) {
            if(empty(trim($value))) continue;
            if($key >= 100) continue;
            
            $items[] = [
                'title' => trim(query_clean($value)),
                'content' => trim(filter_var($_POST['item_content'][$key], FILTER_SANITIZE_STRING)),
            ];
        } 

        if(!$biolink_block = db()->where('biolink_block_id', $_POST['biolink_block_id'])->where('user_id', $this->user->user_id)->getOne('biolinks_blocks')) {
            die();
        }

        $settings = json_encode([
            'items' => $items,
            'text_color' => $_POST['text_color'],
            /* Display settings */
            'display_countries' => $_POST['display_countries'],
            'display_devices' => $_POST['display_devices'],
            'display_languages' => $_POST['display_languages'],
            'display_operating_systems' => $_POST['display_operating_systems'],
        ]);

        /* Database query */
        db()->where('biolink_block_id', $_POST['biolink_block_id'])->update('biolinks_blocks', [
            'settings' => $settings,
            'start_date' => $_POST['start_date'],
            'end_date' => $_POST['end_date'],
        ]);

        /* Clear the cache */
        \Altum\Cache::$adapter->deleteItem('link?link_id=' . $biolink_block->link_id);

        Response::json(l('global.success_message.update2'), 'success');
    }
	
	
    
    private function create_biolink_tmscrolltimeline() {
        $_POST['link_id'] = (int) $_POST['link_id'];

        if(!$link = db()->where('link_id', $_POST['link_id'])->where('user_id', $this->user->user_id)->getOne('links')) {
            die();
        }

        $type = 'tmscrolltimeline';
        $settings = json_encode([
            'items' => [],
            'text_color' => '#333',
            'background_color' => '#fff',

            /* Display settings */
            'display_countries' => [],
            'display_devices' => [],
            'display_languages' => [],
            'display_operating_systems' => [],
        ]);

        /* Database query */
        db()->insert('biolinks_blocks', [
            'user_id' => $this->user->user_id,
            'link_id' => $_POST['link_id'],
            'type' => $type,
            'location_url' => null,
            'settings' => $settings,
            'order' => $this->total_biolink_blocks,
            'datetime' => \Altum\Date::$date,
        ]);

        /* Clear the cache */
        \Altum\Cache::$adapter->deleteItem('link?link_id=' . $_POST['link_id']);

        Response::json('', 'success', ['url' => url('link/' . $_POST['link_id'] . '?tab=links')]);
    }
	
	   private function update_biolink_tmscrolltimeline() {
        $_POST['biolink_block_id'] = (int) $_POST['biolink_block_id'];
        if(!isset($_POST['item_title'])) {
            $_POST['item_title'] = [];
            $_POST['item_content'] = mb_substr(trim(query_clean($_POST['item_content'])), 0, 2048);
            $_POST['item_date'] = mb_substr(trim(query_clean($_POST['item_date'])), 0, 2048);
            $_POST['item_icon'] = in_array($_POST['icon'], ['warning', 'danger', 'primary', 'success']) ? query_clean($_POST['item_icon']) : 'success';
        }
           $_POST['text_color'] = !preg_match('/#([A-Fa-f0-9]{3,4}){1,2}\b/i', $_POST['text_color']) ? '#000' : $_POST['text_color'];
           $_POST['background_color'] = !preg_match('/#([A-Fa-f0-9]{3,4}){1,2}\b/i', $_POST['background_color']) ? '#000' : $_POST['background_color'];
           $_POST['title_block'] = mb_substr(trim(query_clean($_POST['title_block'])), 0, 2048);

		/* Display settings */
        $this->process_display_settings();
        
        $items = [];
        foreach($_POST['item_title'] as $key => $value) {
            if(empty(trim($value))) continue;
            if($key >= 100) continue;
            
            $items[] = [
                'title' => trim(query_clean($value)),
                'content' => trim(filter_var($_POST['item_content'][$key], FILTER_SANITIZE_STRING)),
                'date' => trim(filter_var($_POST['item_date'][$key], FILTER_SANITIZE_STRING)),  
                'icon' => $_POST['item_icon'][$key]
            ];
        } 


        if(!$biolink_block = db()->where('biolink_block_id', $_POST['biolink_block_id'])->where('user_id', $this->user->user_id)->getOne('biolinks_blocks')) {
            die();
        }

        $settings = json_encode([
            'items' => $items,
            'text_color' => $_POST['text_color'],
            'background_color' => $_POST['background_color'],
            'title_block' => $_POST['title_block'],
            /* Display settings */
            'display_countries' => $_POST['display_countries'],
            'display_devices' => $_POST['display_devices'],
            'display_languages' => $_POST['display_languages'],
            'display_operating_systems' => $_POST['display_operating_systems'],
        ]);

        /* Database query */
        db()->where('biolink_block_id', $_POST['biolink_block_id'])->update('biolinks_blocks', [
            'settings' => $settings,
            'start_date' => $_POST['start_date'],
            'end_date' => $_POST['end_date'],
        ]);

        /* Clear the cache */
        \Altum\Cache::$adapter->deleteItem('link?link_id=' . $biolink_block->link_id);

        Response::json(l('global.success_message.update2'), 'success');
    }
    
    private function create_biolink_tmgradienttext() {
        $_POST['link_id'] = (int) $_POST['link_id'];
        $_POST['text'] = mb_substr(trim(filter_var($_POST['text'], FILTER_SANITIZE_STRING)), 0, 2048);

        if(!$link = db()->where('link_id', $_POST['link_id'])->where('user_id', $this->user->user_id)->getOne('links')) {
            die();
        }

        $type = 'tmgradienttext';
        $settings = json_encode([
            'text' => $_POST['text'],
            'text_color' => '#84ff3d',
            'background_color' => '#2eabff',

            /* Display settings */
            'display_countries' => [],
            'display_devices' => [],
            'display_languages' => [],
            'display_operating_systems' => [],
        ]);

        /* Database query */
        db()->insert('biolinks_blocks', [
            'user_id' => $this->user->user_id,
            'link_id' => $_POST['link_id'],
            'type' => $type,
            'location_url' => null,
            'settings' => $settings,
            'order' => $this->total_biolink_blocks,
            'datetime' => \Altum\Date::$date,
        ]);

        /* Clear the cache */
        \Altum\Cache::$adapter->deleteItem('link?link_id=' . $_POST['link_id']);

        Response::json('', 'success', ['url' => url('link/' . $_POST['link_id'] . '?tab=links')]);
    }
	
	   private function update_biolink_tmgradienttext() {
        $_POST['biolink_block_id'] = (int) $_POST['biolink_block_id'];
        $_POST['text'] = mb_substr(trim(filter_var($_POST['text'], FILTER_SANITIZE_STRING)), 0, 2048);
        $_POST['text_color'] = !preg_match('/#([A-Fa-f0-9]{3,4}){1,2}\b/i', $_POST['text_color']) ? '#fff' : $_POST['text_color'];
        $_POST['background_color'] = !preg_match('/#([A-Fa-f0-9]{3,4}){1,2}\b/i', $_POST['background_color']) ? '#fff' : $_POST['background_color'];

        /* Display settings */
        $this->process_display_settings();

        if(!$biolink_block = db()->where('biolink_block_id', $_POST['biolink_block_id'])->where('user_id', $this->user->user_id)->getOne('biolinks_blocks')) {
            die();
        }

        $settings = json_encode([
            'text' => $_POST['text'],
            'text_color' => $_POST['text_color'],
            'background_color' => $_POST['background_color'],

            /* Display settings */
            'display_countries' => $_POST['display_countries'],
            'display_devices' => $_POST['display_devices'],
            'display_languages' => $_POST['display_languages'],
            'display_operating_systems' => $_POST['display_operating_systems'],
        ]);

        /* Database query */
        db()->where('biolink_block_id', $_POST['biolink_block_id'])->update('biolinks_blocks', [
            'settings' => $settings,
            'start_date' => $_POST['start_date'],
            'end_date' => $_POST['end_date'],
        ]);

        /* Clear the cache */
        \Altum\Cache::$adapter->deleteItem('link?link_id=' . $biolink_block->link_id);

        Response::json(l('global.success_message.update2'), 'success');
    }
    
    private function create_biolink_tmrichtext() {
        $_POST['link_id'] = (int) $_POST['link_id'];
        $_POST['text'] = mb_substr(trim(filter_var($_POST['text'], FILTER_SANITIZE_STRING)), 0, 2048);

        if(!$link = db()->where('link_id', $_POST['link_id'])->where('user_id', $this->user->user_id)->getOne('links')) {
            die();
        }

        $type = 'tmrichtext';
        $settings = json_encode([
            'text' => $_POST['text'],
            'text_color' => '#000',

            /* Display settings */
            'display_countries' => [],
            'display_devices' => [],
            'display_languages' => [],
            'display_operating_systems' => [],
        ]);

        /* Database query */
        db()->insert('biolinks_blocks', [
            'user_id' => $this->user->user_id,
            'link_id' => $_POST['link_id'],
            'type' => $type,
            'location_url' => null,
            'settings' => $settings,
            'order' => $this->total_biolink_blocks,
            'datetime' => \Altum\Date::$date,
        ]);

        /* Clear the cache */
        \Altum\Cache::$adapter->deleteItem('link?link_id=' . $_POST['link_id']);

        Response::json('', 'success', ['url' => url('link/' . $_POST['link_id'] . '?tab=links')]);
    }
	
	   private function update_biolink_tmrichtext() {
        $_POST['biolink_block_id'] = (int) $_POST['biolink_block_id'];
        $_POST['text'] = mb_substr(trim($_POST['text']), 0, $this->biolink_blocks['custom_html']['max_length']);
        //$_POST['text'] = mb_substr(trim(filter_var($_POST['text'], FILTER_SANITIZE_STRING)), 0, 2048);
        $_POST['text_color'] = !preg_match('/#([A-Fa-f0-9]{3,4}){1,2}\b/i', $_POST['text_color']) ? '#fff' : $_POST['text_color'];

        /* Display settings */
        $this->process_display_settings();

        if(!$biolink_block = db()->where('biolink_block_id', $_POST['biolink_block_id'])->where('user_id', $this->user->user_id)->getOne('biolinks_blocks')) {
            die();
        }

        $settings = json_encode([
            'text' => $_POST['text'],
            'text_color' => $_POST['text_color'],

            /* Display settings */
            'display_countries' => $_POST['display_countries'],
            'display_devices' => $_POST['display_devices'],
            'display_languages' => $_POST['display_languages'],
            'display_operating_systems' => $_POST['display_operating_systems'],
        ]);

        /* Database query */
        db()->where('biolink_block_id', $_POST['biolink_block_id'])->update('biolinks_blocks', [
            'settings' => $settings,
            'start_date' => $_POST['start_date'],
            'end_date' => $_POST['end_date'],
        ]);

        /* Clear the cache */
        \Altum\Cache::$adapter->deleteItem('link?link_id=' . $biolink_block->link_id);

        Response::json(l('global.success_message.update2'), 'success');
    }
    
    private function create_biolink_tmwawidget() {
        $_POST['link_id'] = (int) $_POST['link_id'];
        $_POST['text'] = mb_substr(trim(filter_var($_POST['text'], FILTER_SANITIZE_STRING)), 0, 2048);

        if(!$link = db()->where('link_id', $_POST['link_id'])->where('user_id', $this->user->user_id)->getOne('links')) {
            die();
        }

        $type = 'tmwawidget';
        $settings = json_encode([
            'text' => 'Contact us',
            'window_title' => 'Company Ltd',
            'description_window' => 'Customer Support Service',
            'greeting' => 'How can we help you?',
            'message_placeholder' => 'I want...',
            'text_color' => '#ffffff',
            'background_color' => '#298138',
            'border_color' => '#ffffff',

            /* Display settings */
            'display_countries' => [],
            'display_devices' => [],
            'display_languages' => [],
            'display_operating_systems' => [],
        ]);

        /* Database query */
        db()->insert('biolinks_blocks', [
            'user_id' => $this->user->user_id,
            'link_id' => $_POST['link_id'],
            'type' => $type,
            'location_url' => null,
            'settings' => $settings,
            'order' => $this->total_biolink_blocks,
            'datetime' => \Altum\Date::$date,
        ]);

        /* Clear the cache */
        \Altum\Cache::$adapter->deleteItem('link?link_id=' . $_POST['link_id']);

        Response::json('', 'success', ['url' => url('link/' . $_POST['link_id'] . '?tab=links')]);
    }
	
	   private function update_biolink_tmwawidget() {
        $_POST['biolink_block_id'] = (int) $_POST['biolink_block_id'];
        $_POST['text'] = mb_substr(trim(filter_var($_POST['text'], FILTER_SANITIZE_STRING)), 0, 2048);
        $_POST['window_title'] = mb_substr(trim(filter_var($_POST['window_title'], FILTER_SANITIZE_STRING)), 0, 2048);
        $_POST['description_window'] = mb_substr(trim(filter_var($_POST['description_window'], FILTER_SANITIZE_STRING)), 0, 2048);
        $_POST['greeting'] = mb_substr(trim(filter_var($_POST['greeting'], FILTER_SANITIZE_STRING)), 0, 2048);
        $_POST['message_placeholder'] = mb_substr(trim(filter_var($_POST['message_placeholder'], FILTER_SANITIZE_STRING)), 0, 2048);
       $_POST['phone'] = mb_substr(trim(filter_var($_POST['phone'], FILTER_SANITIZE_STRING)), 0, 2048);
        $_POST['text_color'] = !preg_match('/#([A-Fa-f0-9]{3,4}){1,2}\b/i', $_POST['text_color']) ? '#fff' : $_POST['text_color'];
        $_POST['background_color'] = !preg_match('/#([A-Fa-f0-9]{3,4}){1,2}\b/i', $_POST['background_color']) ? '#fff' : $_POST['background_color'];
        $_POST['border_color'] = !preg_match('/#([A-Fa-f0-9]{3,4}){1,2}\b/i', $_POST['border_color']) ? '#fff' : $_POST['border_color'];

        /* Display settings */
        $this->process_display_settings();

        if(!$biolink_block = db()->where('biolink_block_id', $_POST['biolink_block_id'])->where('user_id', $this->user->user_id)->getOne('biolinks_blocks')) {
            die();
            
        }
        
         $biolink_block->settings = json_decode($biolink_block->settings);
         
         $image = $this->handle_file_upload($biolink_block->settings->image ?? null, 'image', 'image_remove', ['jpg', 'jpeg', 'png', 'svg', 'ico', 'gif'], 'block_images/', settings()->links->image_size_limit);
         
         /* Check for the removal of the already uploaded file */
            if(isset($_POST['image_remove'])) {
            /* Offload deleting */
            if(\Altum\Plugin::is_active('offload') && settings()->offload->uploads_url) {
                $s3 = new \Aws\S3\S3Client(get_aws_s3_config());
                $s3->deleteObject([
                    'Bucket' => settings()->offload->storage_name,
                    'Key' => 'uploads/block_images/' . $biolink_block->settings->image,]);}
            /* Local deleting */
            else {
                /* Delete current file */
                if(!empty($biolink_block->settings->image) && file_exists(UPLOADS_PATH . 'block_images/' . $biolink_block->settings->image)) {
                    unlink(UPLOADS_PATH . 'block_images/' . $biolink_block->settings->image); }}
            $image = null;
            }

        $settings = json_encode([
            'image' => $image,
            'text' => $_POST['text'],
            'window_title' => $_POST['window_title'],
            'description_window' => $_POST['description_window'],
            'greeting' => $_POST['greeting'],
            'message_placeholder' => $_POST['message_placeholder'],
            'phone' => $_POST['phone'],
            'text_color' => $_POST['text_color'],
            'background_color' => $_POST['background_color'],
            'border_color' => $_POST['border_color'],

            /* Display settings */
            'display_countries' => $_POST['display_countries'],
            'display_devices' => $_POST['display_devices'],
            'display_languages' => $_POST['display_languages'],
            'display_operating_systems' => $_POST['display_operating_systems'],
        ]);

        /* Database query */
        db()->where('biolink_block_id', $_POST['biolink_block_id'])->update('biolinks_blocks', [
            'settings' => $settings,
            'start_date' => $_POST['start_date'],
            'end_date' => $_POST['end_date'],
        ]);

        /* Clear the cache */
        \Altum\Cache::$adapter->deleteItem('link?link_id=' . $biolink_block->link_id);

        Response::json(l('global.success_message.update2'), 'success');
    }
    
    
    private function create_biolink_tmonetimeoffer() {
        $_POST['link_id'] = (int) $_POST['link_id'];
        $_POST['text'] = mb_substr(trim(filter_var($_POST['text'], FILTER_SANITIZE_STRING)), 0, 2048);

        if(!$link = db()->where('link_id', $_POST['link_id'])->where('user_id', $this->user->user_id)->getOne('links')) {
            die();
        }

        $type = 'tmonetimeoffer';
        $settings = json_encode([
            'title_before' => l('create_biolink_tmonetimeoffer_modal.title_before'),
            'title_after' => l('create_biolink_tmonetimeoffer_modal.title_after'),
            'text_before' => l('create_biolink_tmonetimeoffer_modal.text_before'),
            'text_after' => l('create_biolink_tmonetimeoffer_modal.text_after'),
            'button_text_before' => l('create_biolink_tmonetimeoffer_modal.button_text_before'),
            'button_text_after' => l('create_biolink_tmonetimeoffer_modal.button_text_after'),
            'title_tagh' => 'h3',
            'text_tagh' => 'p',
            'open_in_new_tab' => false,
            'dark_theme' => false,
            'time' => '600',
            'period' => '86400',
            'location_url_before' => 'https://example.com',
            'location_url_after' => 'https://example.com',
            'animation' => false,
            'animation_runs' => 'repeat-1',
            'text_color' => '#ffffff',
            'background_color' => '#353535',
            'border_color' => '#45A783',
            'border_width' => 0,
            'border_style' => 'solid',
            'border_radius' => 'rounded',
            'youtube_autoplay' => false,
            'youtube_controls' => false,
            'youtube_info' => false,
            'youtube_related' => false,
            'youtube_loop' => false,

            /* Display settings */
            'display_countries' => [],
            'display_devices' => [],
            'display_languages' => [],
            'display_operating_systems' => [],
        ]);

        /* Database query */
        db()->insert('biolinks_blocks', [
            'user_id' => $this->user->user_id,
            'link_id' => $_POST['link_id'],
            'type' => $type,
            'location_url' => null,
            'settings' => $settings,
            'order' => $this->total_biolink_blocks,
            'datetime' => \Altum\Date::$date,
        ]);

        /* Clear the cache */
        \Altum\Cache::$adapter->deleteItem('link?link_id=' . $_POST['link_id']);

        Response::json('', 'success', ['url' => url('link/' . $_POST['link_id'] . '?tab=links')]);
    }
	
	   private function update_biolink_tmonetimeoffer() {
        $_POST['biolink_block_id'] = (int) $_POST['biolink_block_id'];
        $_POST['title_before'] = mb_substr(trim(filter_var($_POST['title_before'], FILTER_SANITIZE_STRING)), 0, 2048);
        $_POST['title_after'] = mb_substr(trim(filter_var($_POST['title_after'], FILTER_SANITIZE_STRING)), 0, 2048);
        $_POST['text_before'] = mb_substr(trim(filter_var($_POST['text_before'], FILTER_SANITIZE_STRING)), 0, 2048);
        $_POST['text_after'] = mb_substr(trim(filter_var($_POST['text_after'], FILTER_SANITIZE_STRING)), 0, 2048);
       $_POST['button_text_before'] = mb_substr(trim(filter_var($_POST['button_text_before'], FILTER_SANITIZE_STRING)), 0, 2048);
       $_POST['button_text_after'] = mb_substr(trim(filter_var($_POST['button_text_after'], FILTER_SANITIZE_STRING)), 0, 2048);
        $_POST['location_url_before'] = mb_substr(trim(filter_var($_POST['location_url_before'], FILTER_SANITIZE_STRING)), 0, 2048);
        $_POST['location_url_after'] = mb_substr(trim(filter_var($_POST['location_url_after'], FILTER_SANITIZE_STRING)), 0, 2048);
        $_POST['youtube_before'] = mb_substr(trim(filter_var($_POST['youtube_before'], FILTER_SANITIZE_STRING)), 0, 2048);
        $_POST['youtube_after'] = mb_substr(trim(filter_var($_POST['youtube_after'], FILTER_SANITIZE_STRING)), 0, 2048);
        $_POST['text_color'] = !preg_match('/#([A-Fa-f0-9]{3,4}){1,2}\b/i', $_POST['text_color']) ? '#fff' : $_POST['text_color'];
        $_POST['background_color'] = !preg_match('/#([A-Fa-f0-9]{3,4}){1,2}\b/i', $_POST['background_color']) ? '#fff' : $_POST['background_color'];
        $_POST['border_color'] = !preg_match('/#([A-Fa-f0-9]{3,4}){1,2}\b/i', $_POST['border_color']) ? '#fff' : $_POST['border_color'];
        $_POST['title_tagh'] = in_array($_POST['title_tag'], ['h1', 'h2', 'h3', 'h4', 'h5', 'h6']) ? (int) $_POST['title_tag'] : 'h1';
        $_POST['text_tagh'] = in_array($_POST['text_tag'], ['p', 'h1', 'h2', 'h3', 'h4', 'h5', 'h6']) ? (int) $_POST['text_tag'] : 'p';
        $_POST['time'] = $_POST['time'] > 9 && $_POST['time'] < 86401 ? (int) $_POST['time'] : 600;
        $_POST['period'] = $_POST['period'] > 59 && $_POST['period'] < 31536001 ? (int) $_POST['period'] : 86400;
        $_POST['countdown_enable'] = $_POST['countdown_enable'];
        $_POST['dark_theme'] = $_POST['dark_theme'];
        $_POST['youtube_enable'] = $_POST['youtube_enable'];
        $_POST['image_enable'] = $_POST['image_enable'];
        $_POST['clear_enable'] = $_POST['clear_enable'];
        $_POST['youtube_autoplay'] = $_POST['youtube_autoplay'];
        $_POST['youtube_controls'] = $_POST['youtube_controls'];
        $_POST['youtube_info'] = $_POST['youtube_info'];
        $_POST['youtube_related'] = $_POST['youtube_related'];
        $_POST['youtube_loop'] = $_POST['youtube_loop'];
        $_POST['animation'] = in_array($_POST['animation'], require APP_PATH . 'includes/biolink_animations.php') || $_POST['animation'] == 'false' ? query_clean($_POST['animation']) : false;
        $_POST['animation_runs'] = in_array($_POST['animation_runs'], ['repeat-1', 'repeat-2', 'repeat-3', 'infinite']) ? query_clean($_POST['animation_runs']) : false;
        $_POST['border_radius'] = in_array($_POST['border_radius'], ['straight', 'round', 'rounded']) ? query_clean($_POST['border_radius']) : 'rounded';
        $_POST['border_style'] = in_array($_POST['border_style'], ['solid', 'dashed', 'double', 'inset', 'outset']) ? query_clean($_POST['border_style']) : 'solid';
        $_POST['border_width'] = in_array($_POST['border_width'], [0, 1, 2, 3, 4, 5]) ? (int) $_POST['border_width'] : 0;
        $_POST['open_in_new_tab'] = isset($_POST['open_in_new_tab']);

        /* Display settings */
        $this->process_display_settings();

        if(!$biolink_block = db()->where('biolink_block_id', $_POST['biolink_block_id'])->where('user_id', $this->user->user_id)->getOne('biolinks_blocks')) {
            die();
            
        }
        
         $biolink_block->settings = json_decode($biolink_block->settings);
         
            $image[0] = $this->handle_file_upload($biolink_block->settings->image_0 ?? null, 'image_0', 'image_remove', ['jpg', 'jpeg', 'png', 'svg', 'ico', 'gif'], 'block_images/', settings()->links->image_size_limit);
            $image[1] = $this->handle_file_upload($biolink_block->settings->image_1 ?? null, 'image_1', 'image_remove', ['jpg', 'jpeg', 'png', 'svg', 'ico', 'gif'], 'block_images/', settings()->links->image_size_limit);
            $image[2] = $this->handle_file_upload($biolink_block->settings->image_2 ?? null, 'image_2', 'image_remove', ['jpg', 'jpeg', 'png', 'svg', 'ico', 'gif'], 'block_images/', settings()->links->image_size_limit);
               
               
            /* Check for the removal of the already uploaded file */
            if(isset($_POST['image_0_remove'])) {
            /* Offload deleting */
            if(\Altum\Plugin::is_active('offload') && settings()->offload->uploads_url) {
                $s3 = new \Aws\S3\S3Client(get_aws_s3_config());
                $s3->deleteObject([
                    'Bucket' => settings()->offload->storage_name,
                    'Key' => 'uploads/block_images/' . $biolink_block->settings->image_0,]);}
            /* Local deleting */
            else {
                /* Delete current file */
                if(!empty($biolink_block->settings->image_0) && file_exists(UPLOADS_PATH . 'block_images/' . $biolink_block->settings->image_0)) {
                    unlink(UPLOADS_PATH . 'block_images/' . $biolink_block->settings->image_0); }}
            $image[0] = null;
            }
            
            if(isset($_POST['image_1_remove'])) {
            /* Offload deleting */
            if(\Altum\Plugin::is_active('offload') && settings()->offload->uploads_url) {
                $s3 = new \Aws\S3\S3Client(get_aws_s3_config());
                $s3->deleteObject([
                    'Bucket' => settings()->offload->storage_name,
                    'Key' => 'uploads/block_images/' . $biolink_block->settings->image_1,]);}
            /* Local deleting */
            else {
                /* Delete current file */
                if(!empty($biolink_block->settings->image_1) && file_exists(UPLOADS_PATH . 'block_images/' . $biolink_block->settings->image_1)) {
                    unlink(UPLOADS_PATH . 'block_images/' . $biolink_block->settings->image_1); }}
            $image[1] = null;
            }
            
            if(isset($_POST['image_remove_2'])) {
            /* Offload deleting */
            if(\Altum\Plugin::is_active('offload') && settings()->offload->uploads_url) {
                $s3 = new \Aws\S3\S3Client(get_aws_s3_config());
                $s3->deleteObject([
                    'Bucket' => settings()->offload->storage_name,
                    'Key' => 'uploads/block_images/' . $biolink_block->settings->image_2,]);}
            /* Local deleting */
            else {
                /* Delete current file */
                if(!empty($biolink_block->settings->image_2) && file_exists(UPLOADS_PATH . 'block_images/' . $biolink_block->settings->image_2)) {
                    unlink(UPLOADS_PATH . 'block_images/' . $biolink_block->settings->image_2); }}
            $image[2] = null;
            }

        $settings = json_encode([
            'image' => $db_image,
            'image_0' => $image[0],
            'image_1' => $image[1],
            'title_before' => $_POST['title_before'],
            'title_after' => $_POST['title_after'],
            'title_tagh' => $_POST['title_tag'],
            'text_tagh' => $_POST['text_tag'],
            'text_before' => $_POST['text_before'],
            'text_after' => $_POST['text_after'],
            'button_text_before' => $_POST['button_text_before'],
            'button_text_after' => $_POST['button_text_after'],
            'open_in_new_tab' => $_POST['open_in_new_tab'],
            'border_radius' => $_POST['border_radius'],
            'border_style' => $_POST['border_style'],
            'border_width' => $_POST['border_width'],
            'location_url_before' => $_POST['location_url_before'],
            'location_url_after' => $_POST['location_url_after'],
            'youtube_before' => $_POST['youtube_before'],
            'youtube_after' => $_POST['youtube_after'],
            'time' => $_POST['time'],
            'period' => $_POST['period'],
            'countdown_enable' => isset($_POST['countdown_enable']),
            'dark_theme' => isset($_POST['dark_theme']),
            'youtube_enable' => isset($_POST['youtube_enable']),
            'image_enable' => isset($_POST['image_enable']),
            'clear_enable' => isset($_POST['clear_enable']),
            'youtube_autoplay' => isset($_POST['youtube_autoplay']),
            'youtube_controls' => isset($_POST['youtube_controls']),
            'youtube_info' => isset($_POST['youtube_info']),
            'youtube_related' => isset($_POST['youtube_related']),
            'youtube_loop' => isset($_POST['youtube_loop']),
            'animation' => $_POST['animation'],
            'animation_runs' => $_POST['animation_runs'],
            'text_color' => $_POST['text_color'],
            'background_color' => $_POST['background_color'],
            'border_color' => $_POST['border_color'],
            'title_tag' => $_POST['title_tag'],

            /* Display settings */
            'display_countries' => $_POST['display_countries'],
            'display_devices' => $_POST['display_devices'],
            'display_languages' => $_POST['display_languages'],
            'display_operating_systems' => $_POST['display_operating_systems'],
        ]);

        /* Database query */
        db()->where('biolink_block_id', $_POST['biolink_block_id'])->update('biolinks_blocks', [
            'settings' => $settings,
            'start_date' => $_POST['start_date'],
            'end_date' => $_POST['end_date'],
        ]);

        /* Clear the cache */
        \Altum\Cache::$adapter->deleteItem('link?link_id=' . $biolink_block->link_id);

        Response::json(l('global.success_message.update2'), 'success');
    }
    
        private function create_biolink_tmcatalog() {
        $_POST['link_id'] = (int) $_POST['link_id'];

        if(!$link = db()->where('link_id', $_POST['link_id'])->where('user_id', $this->user->user_id)->getOne('links')) {
            die();
        }
        
        /* Image upload */
        $db_image = $this->handle_image_upload(null, 'block_images/', settings()->links->image_size_limit); 

        $type = 'tmcatalog';
        $settings = json_encode([
            'image' => $db_image,
            'text_color' => '#222',
            'background_color' => '#fff',

            /* Display settings */
            'display_countries' => [],
            'display_devices' => [],
            'display_languages' => [],
            'display_operating_systems' => [],
        ]);

        /* Database query */
        db()->insert('biolinks_blocks', [
            'user_id' => $this->user->user_id,
            'link_id' => $_POST['link_id'],
            'type' => $type,
            'location_url' => null,
            'settings' => $settings,
            'order' => $this->total_biolink_blocks,
            'datetime' => \Altum\Date::$date,
        ]);

        /* Clear the cache */
        \Altum\Cache::$adapter->deleteItem('link?link_id=' . $_POST['link_id']);

        Response::json('', 'success', ['url' => url('link/' . $_POST['link_id'] . '?tab=links')]);
    }
	
	   private function update_biolink_tmcatalog() {
	         $_POST['link_id'] = (int) $_POST['link_id'];
        $_POST['biolink_block_id'] = (int) $_POST['biolink_block_id'];
        $_POST['title'] = mb_substr(trim(filter_var($_POST['title'], FILTER_SANITIZE_STRING)), 0, 2048);
        $_POST['text'] = mb_substr(trim(filter_var($_POST['text'], FILTER_SANITIZE_STRING)), 0, 2048);
        $_POST['cost'] = mb_substr(trim(filter_var($_POST['cost'], FILTER_SANITIZE_STRING)), 0, 2048);
        $_POST['title_link'] = mb_substr(trim(filter_var($_POST['title_link'], FILTER_SANITIZE_STRING)), 0, 2048);
        $_POST['url_link'] = mb_substr(trim(filter_var($_POST['url_link'], FILTER_SANITIZE_STRING)), 0, 2048);
        $_POST['text_color'] = !preg_match('/#([A-Fa-f0-9]{3,4}){1,2}\b/i', $_POST['text_color']) ? '#fff' : $_POST['text_color'];
        $_POST['background_color'] = !preg_match('/#([A-Fa-f0-9]{3,4}){1,2}\b/i', $_POST['background_color']) ? '#fff' : $_POST['background_color'];

        /* Display settings */
        $this->process_display_settings();

        if(!$biolink_block = db()->where('biolink_block_id', $_POST['biolink_block_id'])->where('user_id', $this->user->user_id)->getOne('biolinks_blocks')) {
            die();
        }
        $biolink_block->settings = json_decode($biolink_block->settings);
                  
            $image[0] = $this->handle_file_upload($biolink_block->settings->image_0 ?? null, 'image_0', 'image_remove', ['jpg', 'jpeg', 'png', 'svg', 'ico', 'gif'], 'block_images/', settings()->links->image_size_limit);
            $image[1] = $this->handle_file_upload($biolink_block->settings->image_1 ?? null, 'image_1', 'image_remove', ['jpg', 'jpeg', 'png', 'svg', 'ico', 'gif'], 'block_images/', settings()->links->image_size_limit);
            $image[2] = $this->handle_file_upload($biolink_block->settings->image_2 ?? null, 'image_2', 'image_remove', ['jpg', 'jpeg', 'png', 'svg', 'ico', 'gif'], 'block_images/', settings()->links->image_size_limit);
               
               
            /* Check for the removal of the already uploaded file */
            if(isset($_POST['image_remove_0'])) {
            /* Offload deleting */
            if(\Altum\Plugin::is_active('offload') && settings()->offload->uploads_url) {
                $s3 = new \Aws\S3\S3Client(get_aws_s3_config());
                $s3->deleteObject([
                    'Bucket' => settings()->offload->storage_name,
                    'Key' => 'uploads/block_images/' . $biolink_block->settings->image_0,]);}
            /* Local deleting */
            else {
                /* Delete current file */
                if(!empty($biolink_block->settings->image_0) && file_exists(UPLOADS_PATH . 'block_images/' . $biolink_block->settings->image_0)) {
                    unlink(UPLOADS_PATH . 'block_images/' . $biolink_block->settings->image_0); }}
            $image[0] = null;
            }
            
            if(isset($_POST['image_remove_1'])) {
            /* Offload deleting */
            if(\Altum\Plugin::is_active('offload') && settings()->offload->uploads_url) {
                $s3 = new \Aws\S3\S3Client(get_aws_s3_config());
                $s3->deleteObject([
                    'Bucket' => settings()->offload->storage_name,
                    'Key' => 'uploads/block_images/' . $biolink_block->settings->image_1,]);}
            /* Local deleting */
            else {
                /* Delete current file */
                if(!empty($biolink_block->settings->image_1) && file_exists(UPLOADS_PATH . 'block_images/' . $biolink_block->settings->image_1)) {
                    unlink(UPLOADS_PATH . 'block_images/' . $biolink_block->settings->image_1); }}
            $image[1] = null;
            }
            
            if(isset($_POST['image_remove_2'])) {
            /* Offload deleting */
            if(\Altum\Plugin::is_active('offload') && settings()->offload->uploads_url) {
                $s3 = new \Aws\S3\S3Client(get_aws_s3_config());
                $s3->deleteObject([
                    'Bucket' => settings()->offload->storage_name,
                    'Key' => 'uploads/block_images/' . $biolink_block->settings->image_2,]);}
            /* Local deleting */
            else {
                /* Delete current file */
                if(!empty($biolink_block->settings->image_2) && file_exists(UPLOADS_PATH . 'block_images/' . $biolink_block->settings->image_2)) {
                    unlink(UPLOADS_PATH . 'block_images/' . $biolink_block->settings->image_2); }}
            $image[2] = null;
            }


        $settings = json_encode([
            'image' => $db_image,
            'image_0' => $image[0],
            'image_1' => $image[1],
            'image_2' => $image[2],
            'title' => $_POST['title'],
            'text' => $_POST['text'],
            'cost' => $_POST['cost'],
            'title_link' => $_POST['title_link'],
            'url_link' => $_POST['url_link'],
            'text_color' => $_POST['text_color'],
            'background_color' => $_POST['background_color'],

            /* Display settings */
            'display_countries' => $_POST['display_countries'],
            'display_devices' => $_POST['display_devices'],
            'display_languages' => $_POST['display_languages'],
            'display_operating_systems' => $_POST['display_operating_systems'],
        ]);

        /* Database query */
        db()->where('biolink_block_id', $_POST['biolink_block_id'])->update('biolinks_blocks', [
            'settings' => $settings,
            'start_date' => $_POST['start_date'],
            'end_date' => $_POST['end_date'],
        ]);

        /* Clear the cache */
        \Altum\Cache::$adapter->deleteItem('link?link_id=' . $biolink_block->link_id);

        Response::json(l('global.success_message.update2'), 'success');
    }
    
           private function create_biolink_tmtextlogo() {
        $_POST['link_id'] = (int) $_POST['link_id'];
        $_POST['text'] = mb_substr(input_clean($_POST['text']), 0, 2048);

        if(!$link = db()->where('link_id', $_POST['link_id'])->where('user_id', $this->user->user_id)->getOne('links')) {
            die();
        }

        $type = 'tmtextlogo';
        $settings = json_encode([
            'text' => $_POST['text'],
            'text_color' => '#ffffff',
            'background_color' => '#B23333',
            'font_size' => 3,

            /* Display settings */
            'display_countries' => [],
            'display_devices' => [],
            'display_languages' => [],
            'display_operating_systems' => [],
        ]);

        /* Database query */
        db()->insert('biolinks_blocks', [
            'user_id' => $this->user->user_id,
            'link_id' => $_POST['link_id'],
            'type' => $type,
            'location_url' => null,
            'settings' => $settings,
            'order' => $this->total_biolink_blocks,
            'datetime' => \Altum\Date::$date,
        ]);

        /* Clear the cache */
        \Altum\Cache::$adapter->deleteItem('link?link_id=' . $_POST['link_id']);

        Response::json('', 'success', ['url' => url('link/' . $_POST['link_id'] . '?tab=links')]);
    }
	
	   private function update_biolink_tmtextlogo() {
        $_POST['biolink_block_id'] = (int) $_POST['biolink_block_id'];
        $_POST['text'] = mb_substr(trim(filter_var($_POST['text'], FILTER_SANITIZE_STRING)), 0, 2048);
        $_POST['text_color'] = !preg_match('/#([A-Fa-f0-9]{3,4}){1,2}\b/i', $_POST['text_color']) ? '#fff' : $_POST['text_color'];
        $_POST['background_color'] = !preg_match('/#([A-Fa-f0-9]{3,4}){1,2}\b/i', $_POST['background_color']) ? '#fff' : $_POST['background_color'];

        /* Display settings */
        $this->process_display_settings();

        if(!$biolink_block = db()->where('biolink_block_id', $_POST['biolink_block_id'])->where('user_id', $this->user->user_id)->getOne('biolinks_blocks')) {
            die();
        }

        $settings = json_encode([
            'text' => $_POST['text'],
            'text_color' => $_POST['text_color'],
            'background_color' => $_POST['background_color'],
            'font_size' => $_POST['font_size'],

            /* Display settings */
            'display_countries' => $_POST['display_countries'],
            'display_devices' => $_POST['display_devices'],
            'display_languages' => $_POST['display_languages'],
            'display_operating_systems' => $_POST['display_operating_systems'],
        ]);

        /* Database query */
        db()->where('biolink_block_id', $_POST['biolink_block_id'])->update('biolinks_blocks', [
            'settings' => $settings,
            'start_date' => $_POST['start_date'],
            'end_date' => $_POST['end_date'],
        ]);

        /* Clear the cache */
        \Altum\Cache::$adapter->deleteItem('link?link_id=' . $biolink_block->link_id);

        Response::json(l('global.success_message.update2'), 'success');
    }
    
    private function create_biolink_tmnewsfeed() {
        $_POST['link_id'] = (int) $_POST['link_id'];

        if(!$link = db()->where('link_id', $_POST['link_id'])->where('user_id', $this->user->user_id)->getOne('links')) {
            die();
        }
        
        $items = [];
        $i=0;
        
        while ($i < 4) {
            $i++;
            $items[] = [
                'title' => 'lorem ipsum dolor sit amet consectetur adipiscing elit',
                'content' => 'https://example.com',
                'date' => '10', 
                'month' => 'JUNE',  
                'topic' => 'actual',
                'enable' => 'true',
            ];
        } 

        $type = 'tmnewsfeed';
        $settings = json_encode([
            'items' => $items,
            'text_color' => '#333',
            'background_color' => '#fff',
            'icon_block' => 'fab fa-instagram',
            'title_block' => 'Our News',

            /* Display settings */
            'display_countries' => [],
            'display_devices' => [],
            'display_languages' => [],
            'display_operating_systems' => [],
        ]);

        /* Database query */
        db()->insert('biolinks_blocks', [
            'user_id' => $this->user->user_id,
            'link_id' => $_POST['link_id'],
            'type' => $type,
            'location_url' => null,
            'settings' => $settings,
            'order' => $this->total_biolink_blocks,
            'datetime' => \Altum\Date::$date,
        ]);

        /* Clear the cache */
        \Altum\Cache::$adapter->deleteItem('link?link_id=' . $_POST['link_id']);

        Response::json('', 'success', ['url' => url('link/' . $_POST['link_id'] . '?tab=links')]);
    }
	
	   private function update_biolink_tmnewsfeed() {
        $_POST['biolink_block_id'] = (int) $_POST['biolink_block_id'];
        if(!isset($_POST['item_title'])) {
            $_POST['item_title'] = [];
            $_POST['item_content'] = mb_substr(trim(query_clean($_POST['item_content'])), 0, 2048);
            $_POST['item_date'] = mb_substr(trim(query_clean($_POST['item_date'])), 0, 2048);
            $_POST['item_month'] = mb_substr(trim(query_clean($_POST['item_month'])), 0, 2048);
            $_POST['item_topic'] = mb_substr(trim(query_clean($_POST['item_topic'])), 0, 2048);
            $_POST['enable'] = true;
            
        }
           $_POST['text_color'] = !preg_match('/#([A-Fa-f0-9]{3,4}){1,2}\b/i', $_POST['text_color']) ? '#000' : $_POST['text_color'];
           $_POST['background_color'] = !preg_match('/#([A-Fa-f0-9]{3,4}){1,2}\b/i', $_POST['background_color']) ? '#000' : $_POST['background_color'];
           $_POST['title_block'] = mb_substr(trim(query_clean($_POST['title_block'])), 0, 2048);
           $_POST['icon_block'] = trim(query_clean($_POST['icon_block']));
		   
		/* Display settings */
        $this->process_display_settings();		   

        $items = [];
        foreach($_POST['item_title'] as $key => $value) {
            if(empty(trim($value))) continue;
            if($key >= 100) continue;
            
            $items[] = [
                'title' => trim(query_clean($value)),
                'content' => trim(filter_var($_POST['item_content'][$key], FILTER_SANITIZE_STRING)),
                'date' => trim(filter_var($_POST['item_date'][$key], FILTER_SANITIZE_STRING)), 
                'month' => trim(filter_var($_POST['item_month'][$key], FILTER_SANITIZE_STRING)),  
                'topic' => trim(filter_var($_POST['item_topic'][$key], FILTER_SANITIZE_STRING)),
                'enable' => isset($_POST['enable' . $key]),
            ];
        } 

        if(!$biolink_block = db()->where('biolink_block_id', $_POST['biolink_block_id'])->where('user_id', $this->user->user_id)->getOne('biolinks_blocks')) {
            die();
        }

        $settings = json_encode([
            'items' => $items,
            'text_color' => $_POST['text_color'],
            'background_color' => $_POST['background_color'],
            'title_block' => $_POST['title_block'],
            'icon_block' => $_POST['icon_block'],
            /* Display settings */
            'display_countries' => $_POST['display_countries'],
            'display_devices' => $_POST['display_devices'],
            'display_languages' => $_POST['display_languages'],
            'display_operating_systems' => $_POST['display_operating_systems'],
        ]);

        /* Database query */
        db()->where('biolink_block_id', $_POST['biolink_block_id'])->update('biolinks_blocks', [
            'settings' => $settings,
            'start_date' => $_POST['start_date'],
            'end_date' => $_POST['end_date'],
        ]);

        /* Clear the cache */
        \Altum\Cache::$adapter->deleteItem('link?link_id=' . $biolink_block->link_id);

        Response::json(l('global.success_message.update2'), 'success');
    }
    
     private function create_biolink_tmprice() {
        $_POST['link_id'] = (int) $_POST['link_id'];

        if(!$link = db()->where('link_id', $_POST['link_id'])->where('user_id', $this->user->user_id)->getOne('links')) {
            die();
        }

        $type = 'tmprice';
        $settings = json_encode([
            'items' => [],
           'text_color' => '#333',
           'background_color' => '#fff',
            /* Display settings */
            'display_countries' => [],
            'display_devices' => [],
            'display_languages' => [],
            'display_operating_systems' => [],
        ]);

        /* Database query */
        db()->insert('biolinks_blocks', [
            'user_id' => $this->user->user_id,
            'link_id' => $_POST['link_id'],
            'type' => $type,
            'location_url' => null,
            'settings' => $settings,
            'order' => $this->total_biolink_blocks,
            'datetime' => \Altum\Date::$date,
        ]);

        /* Clear the cache */
        \Altum\Cache::$adapter->deleteItem('link?link_id=' . $_POST['link_id']);

        Response::json('', 'success', ['url' => url('link/' . $_POST['link_id'] . '?tab=links')]);
    }
	
	    private function update_biolink_tmprice() {
        $_POST['biolink_block_id'] = (int) $_POST['biolink_block_id'];
        if(!isset($_POST['item_title'])) {
            $_POST['item_title'] = [];
            $_POST['item_content'] = mb_substr(trim(query_clean($_POST['item_content'])), 0, 2048);;
            $_POST['item_cost'] = mb_substr(trim(query_clean($_POST['item_cost'])), 0, 2048);;
        }
           $_POST['block_title'] = mb_substr(trim(query_clean($_POST['block_title'])), 0, 2048);;
           $_POST['block_description'] = mb_substr(trim(query_clean($_POST['block_description'])), 0, 2048);;
           $_POST['text_color'] = !preg_match('/#([A-Fa-f0-9]{3,4}){1,2}\b/i', $_POST['text_color']) ? '#000' : $_POST['text_color'];
        $_POST['background_color'] = !preg_match('/#([A-Fa-f0-9]{3,4}){1,2}\b/i', $_POST['background_color']) ? '#fff' : $_POST['background_color'];

		/* Display settings */
        $this->process_display_settings();        

        $items = [];
        foreach($_POST['item_title'] as $key => $value) {
            if(empty(trim($value))) continue;
            if($key >= 100) continue;
            
            $items[] = [
                'title' => trim(query_clean($value)),
                'content' => trim(filter_var($_POST['item_content'][$key], FILTER_SANITIZE_STRING)),
                'cost' => trim(filter_var($_POST['item_cost'][$key], FILTER_SANITIZE_STRING)),
            ];
        } 

        if(!$biolink_block = db()->where('biolink_block_id', $_POST['biolink_block_id'])->where('user_id', $this->user->user_id)->getOne('biolinks_blocks')) {
            die();
        }

        $settings = json_encode([
            'items' => $items,
            'text_color' => $_POST['text_color'],
            'background_color' => $_POST['background_color'],
            'block_title' => $_POST['block_title'],
            'block_description' => $_POST['block_description'],
            /* Display settings */
            'display_countries' => $_POST['display_countries'],
            'display_devices' => $_POST['display_devices'],
            'display_languages' => $_POST['display_languages'],
            'display_operating_systems' => $_POST['display_operating_systems'],
        ]);

        /* Database query */
        db()->where('biolink_block_id', $_POST['biolink_block_id'])->update('biolinks_blocks', [
            'settings' => $settings,
            'start_date' => $_POST['start_date'],
            'end_date' => $_POST['end_date'],
        ]);

        /* Clear the cache */
        \Altum\Cache::$adapter->deleteItem('link?link_id=' . $biolink_block->link_id);

        Response::json(l('global.success_message.update2'), 'success');
    }
    
    private function create_biolink_tmfaq() {
        $_POST['link_id'] = (int) $_POST['link_id'];

        if(!$link = db()->where('link_id', $_POST['link_id'])->where('user_id', $this->user->user_id)->getOne('links')) {
            die();
        }

        $type = 'tmfaq';
        $settings = json_encode([
            'items' => [],
            'text_color' => 'black',
            'background_color' => 'white',
            /* Display settings */
            'display_countries' => [],
            'display_devices' => [],
            'display_languages' => [],
            'display_operating_systems' => [],
        ]);

        /* Database query */
        db()->insert('biolinks_blocks', [
            'user_id' => $this->user->user_id,
            'link_id' => $_POST['link_id'],
            'type' => $type,
            'location_url' => null,
            'settings' => $settings,
            'order' => $this->total_biolink_blocks,
            'datetime' => \Altum\Date::$date,
        ]);

        /* Clear the cache */
        \Altum\Cache::$adapter->deleteItem('link?link_id=' . $_POST['link_id']);

        Response::json('', 'success', ['url' => url('link/' . $_POST['link_id'] . '?tab=links')]);
    }
	
	   private function update_biolink_tmfaq() {
        $_POST['biolink_block_id'] = (int) $_POST['biolink_block_id'];
        if(!isset($_POST['item_title'])) {
            $_POST['item_title'] = [];
            $_POST['item_content'] = mb_substr(trim(query_clean($_POST['item_content'])), 0, 2048);;
        }
           $_POST['text_color'] = !preg_match('/#([A-Fa-f0-9]{3,4}){1,2}\b/i', $_POST['text_color']) ? '#000' : $_POST['text_color'];
        $_POST['background_color'] = !preg_match('/#([A-Fa-f0-9]{3,4}){1,2}\b/i', $_POST['background_color']) ? '#fff' : $_POST['background_color'];

		/* Display settings */
        $this->process_display_settings();        

        $items = [];
        foreach($_POST['item_title'] as $key => $value) {
            if(empty(trim($value))) continue;
            if($key >= 100) continue;
            
            $items[] = [
                'title' => trim(query_clean($value)),
                'content' => trim(filter_var($_POST['item_content'][$key], FILTER_SANITIZE_STRING)),
            ];
        } 

        if(!$biolink_block = db()->where('biolink_block_id', $_POST['biolink_block_id'])->where('user_id', $this->user->user_id)->getOne('biolinks_blocks')) {
            die();
        }

        $settings = json_encode([
            'items' => $items,
            'text_color' => $_POST['text_color'],
            'background_color' => $_POST['background_color'],
            /* Display settings */
            'display_countries' => $_POST['display_countries'],
            'display_devices' => $_POST['display_devices'],
            'display_languages' => $_POST['display_languages'],
            'display_operating_systems' => $_POST['display_operating_systems'],
        ]);

        /* Database query */
        db()->where('biolink_block_id', $_POST['biolink_block_id'])->update('biolinks_blocks', [
            'settings' => $settings,
            'start_date' => $_POST['start_date'],
            'end_date' => $_POST['end_date'],
        ]);

        /* Clear the cache */
        \Altum\Cache::$adapter->deleteItem('link?link_id=' . $biolink_block->link_id);

        Response::json(l('global.success_message.update2'), 'success');
    }
    
    private function create_biolink_tmlist() {
        $_POST['link_id'] = (int) $_POST['link_id'];

        if(!$link = db()->where('link_id', $_POST['link_id'])->where('user_id', $this->user->user_id)->getOne('links')) {
            die();
        }

        $type = 'tmlist';
        $settings = json_encode([
            'items' => [],
            'text_color' => 'black',
            'background_color' => 'white',
            /* Display settings */
            'display_countries' => [],
            'display_devices' => [],
            'display_languages' => [],
            'display_operating_systems' => [],
        ]);

        /* Database query */
        db()->insert('biolinks_blocks', [
            'user_id' => $this->user->user_id,
            'link_id' => $_POST['link_id'],
            'type' => $type,
            'location_url' => null,
            'settings' => $settings,
            'order' => $this->total_biolink_blocks,
            'datetime' => \Altum\Date::$date,
        ]);

        /* Clear the cache */
        \Altum\Cache::$adapter->deleteItem('link?link_id=' . $_POST['link_id']);

        Response::json('', 'success', ['url' => url('link/' . $_POST['link_id'] . '?tab=links')]);
    }
	
	    private function update_biolink_tmlist() {
        $_POST['biolink_block_id'] = (int) $_POST['biolink_block_id'];
        if(!isset($_POST['item_title'])) {
            $_POST['item_title'] = [];
        }
           $_POST['text_color'] = !preg_match('/#([A-Fa-f0-9]{3,4}){1,2}\b/i', $_POST['text_color']) ? '#000' : $_POST['text_color'];
        $_POST['background_color'] = !preg_match('/#([A-Fa-f0-9]{3,4}){1,2}\b/i', $_POST['background_color']) ? '#fff' : $_POST['background_color'];

		/* Display settings */
        $this->process_display_settings();        

        $items = [];
        foreach($_POST['item_title'] as $key => $value) {
            if(empty(trim($value))) continue;
            if($key >= 100) continue;
            
            $items[] = [
                'title' => trim(query_clean($value)),
            ];
        } 

        if(!$biolink_block = db()->where('biolink_block_id', $_POST['biolink_block_id'])->where('user_id', $this->user->user_id)->getOne('biolinks_blocks')) {
            die();
        }

        $settings = json_encode([
            'items' => $items,
            'text_color' => $_POST['text_color'],
            'background_color' => $_POST['background_color'],
            /* Display settings */
            'display_countries' => $_POST['display_countries'],
            'display_devices' => $_POST['display_devices'],
            'display_languages' => $_POST['display_languages'],
            'display_operating_systems' => $_POST['display_operating_systems'],
        ]);

        /* Database query */
        db()->where('biolink_block_id', $_POST['biolink_block_id'])->update('biolinks_blocks', [
            'settings' => $settings,
            'start_date' => $_POST['start_date'],
            'end_date' => $_POST['end_date'],
        ]);

        /* Clear the cache */
        \Altum\Cache::$adapter->deleteItem('link?link_id=' . $biolink_block->link_id);

        Response::json(l('global.success_message.update2'), 'success');
    }
    
 private function create_biolink_tmmarket() {
        $_POST['link_id'] = (int) $_POST['link_id'];

        if(!$link = db()->where('link_id', $_POST['link_id'])->where('user_id', $this->user->user_id)->getOne('links')) {
            die();
        }

        $type = 'tmmarket';
        $settings = json_encode([
            'items' => [],
            'text_color' => 'black',
            'background_color' => 'white',
            'border_color' => '#404040',
            'phone_placeholder' => l('create_biolink_phone_collector_modal.phone_placeholder_default'),
            'name_placeholder' => l('create_biolink_mail_modal.name_placeholder_default'),
            'success_text' => l('create_biolink_tmmarket_modal.success_text_default'),
            'currency' => l('create_biolink_tmmarket_modal.currency_default'),
            'button_text' => l('create_biolink_tmmarket_modal.button_text_default'),
            'email_notification' => '',
            /* Display settings */
            'display_countries' => [],
            'display_devices' => [],
            'display_languages' => [],
            'display_operating_systems' => [],
        ]);

        /* Database query */
        db()->insert('biolinks_blocks', [
            'user_id' => $this->user->user_id,
            'link_id' => $_POST['link_id'],
            'type' => $type,
            'location_url' => null,
            'settings' => $settings,
            'order' => $this->total_biolink_blocks,
            'datetime' => \Altum\Date::$date,
        ]);

        /* Clear the cache */
        \Altum\Cache::$adapter->deleteItem('link?link_id=' . $_POST['link_id']);

        Response::json('', 'success', ['url' => url('link/' . $_POST['link_id'] . '?tab=links')]);
    }
	
	    private function update_biolink_tmmarket() {
        $_POST['biolink_block_id'] = (int) $_POST['biolink_block_id'];
        if(!isset($_POST['item_title'])) {
            $_POST['item_title'] = [];
            $_POST['item_content'] = []; 
            $_POST['item_cost'] = [];
           $_POST['item_image_link'] = [];
        }
           $_POST['text_color'] = !preg_match('/#([A-Fa-f0-9]{3,4}){1,2}\b/i', $_POST['text_color']) ? '#000' : $_POST['text_color'];
        $_POST['background_color'] = !preg_match('/#([A-Fa-f0-9]{3,4}){1,2}\b/i', $_POST['background_color']) ? '#fff' : $_POST['background_color'];
        $_POST['border_color'] = !preg_match('/#([A-Fa-f0-9]{3,4}){1,2}\b/i', $_POST['border_color']) ? '#fff' : $_POST['border_color'];
        $_POST['phone_placeholder'] = mb_substr(query_clean($_POST['phone_placeholder']), 0, 64);
        $_POST['name_placeholder'] = mb_substr(query_clean($_POST['name_placeholder']), 0, 64);
        $_POST['button_text'] = mb_substr(query_clean($_POST['button_text']), 0, 64);
        $_POST['success_text'] = mb_substr(query_clean($_POST['success_text']), 0, 64);
        $_POST['currency'] = mb_substr(query_clean($_POST['currency']), 0, 12);
         $_POST['enable'] = true;
        $_POST['email_notification'] = mb_substr(query_clean($_POST['email_notification']), 0, 320);

        /* Display settings */
        $this->process_display_settings();    
        
                if(!$biolink_block = db()->where('biolink_block_id', $_POST['biolink_block_id'])->where('user_id', $this->user->user_id)->getOne('biolinks_blocks')) {
            die();
        }
        $biolink_block->settings = json_decode($biolink_block->settings);

        $items = [];
        $count = 1;
        
        foreach($_POST['item_title'] as $key => $value) {
            if($count++ >= 100) continue;
            if(empty(trim($value))) continue;
            if($key >= 100) continue;
            
         $image = $this->handle_file_upload($biolink_block->settings->items->{$key}->image ?? null, 'item_image_' . $key, 'image_remove', ['jpg', 'jpeg', 'png', 'svg', 'ico', 'gif'], 'block_images/', settings()->links->image_size_limit);
         
         if ($image == null) {
            $image = $_POST['item_image_link'][$key];
         }
            
             $items[] = [
                 'id' => ($count - 1),
                'title' => trim(query_clean($value)),
                'description' => trim(filter_var($_POST['item_description'][$key], FILTER_SANITIZE_STRING)),
                'cost' => trim(filter_var($_POST['item_cost'][$key], FILTER_SANITIZE_STRING)),
                'currency' => trim(filter_var($_POST['item_currency'][$key], FILTER_SANITIZE_STRING)),
                'image' => $image,
                'enable' => isset($_POST['enable' . $key]),
            ];
        } 
        
        /* Make sure to delete old images if needed */
        foreach($biolink_block->settings->items as $key => $item) {
            if((isset($items[$key]) && $items[$key]['image'] != $item->image) || !isset($items[$key])) {
                \Altum\Uploads::delete_uploaded_file($item->image, 'block_images');
            }
        }


        $settings = json_encode([
            'items' => (array) $items,
            'text_color' => $_POST['text_color'],
            'background_color' => $_POST['background_color'],
            'border_color' => $_POST['border_color'],
            'phone_placeholder' => $_POST['phone_placeholder'],
            'name_placeholder' => $_POST['name_placeholder'],
            'button_text' => $_POST['button_text'],
            'success_text' => $_POST['success_text'],
            'currency' => $_POST['currency'],
            'email_notification' => $_POST['email_notification'],
            /* Display settings */
            'display_countries' => $_POST['display_countries'],
            'display_devices' => $_POST['display_devices'],
            'display_languages' => $_POST['display_languages'],
            'display_operating_systems' => $_POST['display_operating_systems'],
        ]);

        /* Database query */
        db()->where('biolink_block_id', $_POST['biolink_block_id'])->update('biolinks_blocks', [
            'settings' => $settings,
            'start_date' => $_POST['start_date'],
            'end_date' => $_POST['end_date'],
        ]);

        /* Clear the cache */
        \Altum\Cache::$adapter->deleteItem('link?link_id=' . $biolink_block->link_id);

        Response::json(l('global.success_message.update2'), 'success');
    }
    
    private function create_biolink_tmtranslator() {
        $_POST['link_id'] = (int) $_POST['link_id'];

        if(!$link = db()->where('link_id', $_POST['link_id'])->where('user_id', $this->user->user_id)->getOne('links')) {
            die();
        }


        $type = 'tmtranslator';
        $settings = json_encode([
            'items' => [],
            /* Display settings */
            'display_countries' => [],
            'display_devices' => [],
            'display_languages' => [],
            'display_operating_systems' => [],
        ]);

        /* Database query */
        db()->insert('biolinks_blocks', [
            'user_id' => $this->user->user_id,
            'link_id' => $_POST['link_id'],
            'type' => $type,
            'location_url' => null,
            'settings' => $settings,
            'order' => $this->total_biolink_blocks,
            'datetime' => \Altum\Date::$date,
        ]);

        /* Clear the cache */
        \Altum\Cache::$adapter->deleteItem('link?link_id=' . $_POST['link_id']);

        Response::json('', 'success', ['url' => url('link/' . $_POST['link_id'] . '?tab=links')]);
    }
	
	    private function update_biolink_tmtranslator() {
        $_POST['biolink_block_id'] = (int) $_POST['biolink_block_id'];
        if(!isset($_POST['item_lang'])) {
           $_POST['item_lang'] = in_array($_POST['item_lang'], ['en', 'es', 'zh', 'hi', 'ar', 'bn', 'pt', 'ja', 'ms', 'tr', 'ko', 'fr', 'de', 'it', 'uk', 'ru']) ? (int) $_POST['item_lang'] : 'en';
        }
        
        $_POST['language'] = in_array($_POST['main_lang'], ['en', 'es', 'zh', 'hi', 'ar', 'bn', 'pt', 'ja', 'ms', 'tr', 'ko', 'fr', 'de', 'it', 'uk', 'ru']) ? (int) $_POST['main_lang'] : 'en';
         
		/* Display settings */
        $this->process_display_settings();

        $items = [];
        foreach($_POST['item_lang'] as $key => $value) {
            if(empty(trim($value))) continue;
            if($key >= 16) continue;
            
            $items[] = [
                'lang' => trim(query_clean($value)),
            ];
        } 


        if(!$biolink_block = db()->where('biolink_block_id', $_POST['biolink_block_id'])->where('user_id', $this->user->user_id)->getOne('biolinks_blocks')) {
            die();
        }

        $settings = json_encode([
            'items' => $items,
            'language' => $_POST['main_lang'],
            /* Display settings */
            'display_countries' => $_POST['display_countries'],
            'display_devices' => $_POST['display_devices'],
            'display_languages' => $_POST['display_languages'],
            'display_operating_systems' => $_POST['display_operating_systems'],
        ]);

        /* Database query */
        db()->where('biolink_block_id', $_POST['biolink_block_id'])->update('biolinks_blocks', [
            'settings' => $settings,
            'start_date' => $_POST['start_date'],
            'end_date' => $_POST['end_date'],
        ]);

        /* Clear the cache */
        \Altum\Cache::$adapter->deleteItem('link?link_id=' . $biolink_block->link_id);

        Response::json(l('global.success_message.update2'), 'success');
    }
    
    private function create_biolink_tmtextmorph() {
        $_POST['link_id'] = (int) $_POST['link_id'];

        if(!$link = db()->where('link_id', $_POST['link_id'])->where('user_id', $this->user->user_id)->getOne('links')) {
            die();
        }

        $type = 'tmtextmorph';
        $settings = json_encode([
            'items' => [],
            'text_color' => 'black',
            'speed' => 2,
            'font_size' => 4,
            /* Display settings */
            'display_countries' => [],
            'display_devices' => [],
            'display_languages' => [],
            'display_operating_systems' => [],
        ]);

        /* Database query */
        db()->insert('biolinks_blocks', [
            'user_id' => $this->user->user_id,
            'link_id' => $_POST['link_id'],
            'type' => $type,
            'location_url' => null,
            'settings' => $settings,
            'order' => $this->total_biolink_blocks,
            'datetime' => \Altum\Date::$date,
        ]);

        /* Clear the cache */
        \Altum\Cache::$adapter->deleteItem('link?link_id=' . $_POST['link_id']);

        Response::json('', 'success', ['url' => url('link/' . $_POST['link_id'] . '?tab=links')]);
    }
	
	    private function update_biolink_tmtextmorph() {
        $_POST['biolink_block_id'] = (int) $_POST['biolink_block_id'];
        if(!isset($_POST['item_title'])) {
            $_POST['item_title'] = [];
        }
           $_POST['text_color'] = !preg_match('/#([A-Fa-f0-9]{3,4}){1,2}\b/i', $_POST['text_color']) ? '#000' : $_POST['text_color'];
        $_POST['speed'] = in_array($_POST['speed'], [1, 2, 3, 4, 5, 6]) ? (int) $_POST['speed'] : 2;
        $_POST['font_size'] = in_array($_POST['font_size'], [1, 2, 3, 4, 5, 6, 7, 8]) ? (int) $_POST['font_size'] : 4;

		/* Display settings */
        $this->process_display_settings();        

        $items = [];
        foreach($_POST['item_title'] as $key => $value) {
            if(empty(trim($value))) continue;
            if($key >= 100) continue;
            
            $items[] = [
                'title' => trim(query_clean($value)),
            ];
        } 


        if(!$biolink_block = db()->where('biolink_block_id', $_POST['biolink_block_id'])->where('user_id', $this->user->user_id)->getOne('biolinks_blocks')) {
            die();
        }

        $settings = json_encode([
            'items' => $items,
            'text_color' => $_POST['text_color'],
            'font_size' => $_POST['font_size'],
            'speed' => $_POST['speed'],
            'text_alignment' => $_POST['text_alignment'],
            /* Display settings */
            'display_countries' => $_POST['display_countries'],
            'display_devices' => $_POST['display_devices'],
            'display_languages' => $_POST['display_languages'],
            'display_operating_systems' => $_POST['display_operating_systems'],
        ]);

        /* Database query */
        db()->where('biolink_block_id', $_POST['biolink_block_id'])->update('biolinks_blocks', [
            'settings' => $settings,
            'start_date' => $_POST['start_date'],
            'end_date' => $_POST['end_date'],
        ]);

        /* Clear the cache */
        \Altum\Cache::$adapter->deleteItem('link?link_id=' . $biolink_block->link_id);

        Response::json(l('global.success_message.update2'), 'success');
    }
 
    private function delete() {
        /* Team checks */
        if(\Altum\Teams::is_delegated() && !\Altum\Teams::has_access('delete.biolinks_blocks')) {
            Response::json(l('global.info_message.team_no_access'), 'error');
        }

        $_POST['biolink_block_id'] = (int) $_POST['biolink_block_id'];

        /* Check for possible errors */
        if(!$biolink_block = db()->where('biolink_block_id', $_POST['biolink_block_id'])->where('user_id', $this->user->user_id)->getOne('biolinks_blocks')) {
            die();
        }

        (new \Altum\Models\BiolinkBlock())->delete($biolink_block->biolink_block_id);

        Response::json(l('global.success_message.delete2'), 'success', ['url' => url('link/' . $biolink_block->link_id . '?tab=links')]);
    }

    public function handle_file_upload($already_existing_file, $file_name, $file_name_remove, $allowed_extensions, $upload_folder, $size_limit) {
        /* File upload */
        $file = (bool) !empty($_FILES[$file_name]['name']) && !isset($_POST[$file_name_remove]);
        $db_file = $already_existing_file;

        if($file) {
            $file_extension = explode('.', $_FILES[$file_name]['name']);
            $file_extension = mb_strtolower(end($file_extension));
            $file_temp = $_FILES[$file_name]['tmp_name'];

            if($_FILES[$file_name]['error'] == UPLOAD_ERR_INI_SIZE) {
                Response::json(sprintf(l('global.error_message.file_size_limit'), $size_limit), 'error');
            }

            if($_FILES[$file_name]['error'] && $_FILES[$file_name]['error'] != UPLOAD_ERR_INI_SIZE) {
                Response::json(l('global.error_message.file_upload'), 'error');
            }

            if(!is_writable(UPLOADS_PATH . $upload_folder)) {
                Response::json(sprintf(l('global.error_message.directory_not_writable'), UPLOADS_PATH . $upload_folder), 'error');
            }

            if(!in_array($file_extension, $allowed_extensions)) {
                Response::json(l('global.error_message.invalid_file_type'), 'error');
            }

            if($_FILES[$file_name]['size'] > $size_limit * 1000000) {
                Response::json(sprintf(l('global.error_message.file_size_limit'), $size_limit), 'error');
            }

            /* Generate new name for the file */
            $file_new_name = md5(time() . rand()) . '.' . $file_extension;

            /* Try to compress the image */
            if(\Altum\Plugin::is_active('image-optimizer')) {
                \Altum\Plugin\ImageOptimizer::optimize($file_temp, $file_new_name);
            }

            /* Sanitize SVG uploads */
            if($file_extension == 'svg') {
                $svg_sanitizer = new \enshrined\svgSanitize\Sanitizer();
                $dirty_svg = file_get_contents($file_temp);
                $clean_svg = $svg_sanitizer->sanitize($dirty_svg);
                file_put_contents($file_temp, $clean_svg);
            }

            /* Offload uploading */
            if(\Altum\Plugin::is_active('offload') && settings()->offload->uploads_url) {
                try {
                    $s3 = new \Aws\S3\S3Client(get_aws_s3_config());

                    /* Delete current image */
                    if(!empty($already_existing_file)) {
                        $s3->deleteObject([
                            'Bucket' => settings()->offload->storage_name,
                            'Key' => UPLOADS_URL_PATH . $upload_folder . $already_existing_file,
                        ]);
                    }

                    /* Upload image */
                    $result = $s3->putObject([
                        'Bucket' => settings()->offload->storage_name,
                        'Key' => UPLOADS_URL_PATH . $upload_folder . $file_new_name,
                        'ContentType' => mime_content_type($file_temp),
                        'SourceFile' => $file_temp,
                        'ACL' => 'public-read'
                    ]);
                } catch (\Exception $exception) {
                    Response::json($exception->getMessage(), 'error');
                }
            }

            /* Local uploading */
            else {
                /* Delete current file */
                if(!empty($already_existing_file) && file_exists(UPLOADS_PATH . $upload_folder . $already_existing_file)) {
                    unlink(UPLOADS_PATH . $upload_folder . $already_existing_file);
                }

                /* Upload the original */
                move_uploaded_file($file_temp, UPLOADS_PATH . $upload_folder . $file_new_name);
            }

            $db_file = $file_new_name;
        }

        return $db_file;
    }

    private function handle_image_upload($uploaded_image, $upload_folder, $size_limit) {
        return $this->handle_file_upload($uploaded_image, 'image', 'image_remove', ['jpg', 'jpeg', 'png', 'svg', 'gif', 'webp'], $upload_folder, $size_limit);
    }

    /* Function to bundle together all the checks of an url */
    private function check_location_url($url, $can_be_empty = false) {

        if(empty(trim($url ?? '')) && $can_be_empty) {
            return;
        }

        if(empty(trim($url))) {
            Response::json(l('global.error_message.empty_fields'), 'error');
        }

        $url_details = parse_url($url);

        if(!isset($url_details['scheme'])) {
            Response::json(l('link.error_message.invalid_location_url'), 'error');
        }

        if(!$this->user->plan_settings->deep_links && !in_array($url_details['scheme'], ['http', 'https'])) {
            Response::json(l('link.error_message.invalid_location_url'), 'error');
        }

        /* Make sure the domain is not blacklisted */
        $domain = get_domain_from_url($url);

        if($domain && in_array($domain, explode(',', settings()->links->blacklisted_domains))) {
            Response::json(l('link.error_message.blacklisted_domain'), 'error');
        }

        /* Check the url with google safe browsing to make sure it is a safe website */
        if(settings()->links->google_safe_browsing_is_enabled) {
            if(google_safe_browsing_check($url, settings()->links->google_safe_browsing_api_key)) {
                Response::json(l('link.error_message.blacklisted_location_url'), 'error');
            }
        }
    }

    private function process_display_settings() {
        $_POST['schedule'] = (int) (bool) ($_POST['schedule'] ?? false);
        if($_POST['schedule'] && !empty($_POST['start_date']) && !empty($_POST['end_date']) && Date::validate($_POST['start_date'], 'Y-m-d H:i:s') && Date::validate($_POST['end_date'], 'Y-m-d H:i:s')) {
            $_POST['start_date'] = (new \DateTime($_POST['start_date'], new \DateTimeZone($this->user->timezone)))->setTimezone(new \DateTimeZone(\Altum\Date::$default_timezone))->format('Y-m-d H:i:s');
            $_POST['end_date'] = (new \DateTime($_POST['end_date'], new \DateTimeZone($this->user->timezone)))->setTimezone(new \DateTimeZone(\Altum\Date::$default_timezone))->format('Y-m-d H:i:s');
        } else {
            $_POST['start_date'] = $_POST['end_date'] = null;
        }

        $_POST['display_countries'] = array_filter($_POST['display_countries'] ?? [], function($country) {
            return array_key_exists($country, get_countries_array());
        });

        $_POST['display_devices'] = array_filter($_POST['display_devices'] ?? [], function($device_type) {
            return in_array($device_type, ['desktop', 'tablet', 'mobile']);
        });

        $_POST['display_languages'] = array_filter($_POST['display_languages'] ?? [], function($locale) {
            return array_key_exists($locale, get_locale_languages_array());
        });

        $_POST['display_operating_systems'] = array_filter($_POST['display_operating_systems'] ?? [], function($os_name) {
            return in_array($os_name, ['iOS', 'Android', 'Windows', 'OS X', 'Linux', 'Ubuntu', 'Chrome OS']);
        });
    }
}
