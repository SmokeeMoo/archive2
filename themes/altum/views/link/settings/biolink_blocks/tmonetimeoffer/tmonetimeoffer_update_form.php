<?php defined('ALTUMCODE') || die() ?>

<form name="update_biolink_" method="post" role="form">
    <input type="hidden" name="token" value="<?= \Altum\Csrf::get() ?>" required="required" />
    <input type="hidden" name="request_type" value="update" />
    <input type="hidden" name="block_type" value="tmonetimeoffer" />
    <input type="hidden" name="biolink_block_id" value="<?= $row->biolink_block_id ?>" />

    <div class="notification-container"></div>
    
        <button class="btn btn-block btn-gray-300 my-4" type="button" data-toggle="collapse" data-target="#setting_title" aria-expanded="false" aria-controls="title">
		<?= l('create_biolink_tmonetimeoffer_modal.settings_title') ?>
		<div class="link-btn-arrow-wrapper-setting" >		
		</div>
	</button>

	<div class="collapse" id="setting_title">

    <div class="form-group">
        <label for="<?= 'tmonetimeoffer_title_before_' . $row->biolink_block_id ?>"><i class="fas fa-fw fa-align-center fa-sm text-muted mr-1"></i> <?= l('create_biolink_tmonetimeoffer_modal.title_before') ?></label>
        <input id="<?= 'tmonetimeoffer_title_before_' . $row->biolink_block_id ?>" class="form-control" name="title_before" placeholder="<?= l('create_biolink_tmonetimeoffer_modal.title_before.placeholder') ?>" required="required" maxlength="2048" value="<?= $row->settings->title_before ?>" />
    </div>
    
    <div class="form-group">
        <label for="<?= 'tmonetimeoffer_title_after_' . $row->biolink_block_id ?>"><i class="fas fa-fw fa-align-center fa-sm text-muted mr-1"></i> <?= l('create_biolink_tmonetimeoffer_modal.title_after') ?></label>
        <input id="<?= 'tmonetimeoffer_title_after_' . $row->biolink_block_id ?>" class="form-control" name="title_after" placeholder="<?= l('create_biolink_tmonetimeoffer_modal.title_after.placeholder') ?>"  maxlength="2048" value="<?= $row->settings->title_after ?>" />
    </div>
    
    <div class="form-group">
         <label for="<?= 'tmonetimeoffer_title_tag_' . $row->biolink_block_id ?>"><i class="fas fa-fw fa-align-center fa-sm text-muted mr-1"></i> <?= l('create_biolink_tmonetimeoffer_modal.title_tag') ?></label>
            <select id="title_tag" name="title_tag" class="form-control" >
            <option value="h1" <?= $row->settings->title_tag == 'h1' ? 'selected="selected"' : null ?>><?= l('create_biolink_tmonetimeoffer_modal.h1') ?></option>
            <option value="h2" <?= $row->settings->title_tag == 'h2' ? 'selected="selected"' : null ?>><?= l('create_biolink_tmonetimeoffer_modal.h2') ?></option>
            <option value="h3" <?= $row->settings->title_tag == 'h3' ? 'selected="selected"' : null ?>><?= l('create_biolink_tmonetimeoffer_modal.h3') ?></option>
            <option value="h4" <?= $row->settings->title_tag == 'h4' ? 'selected="selected"' : null ?>><?= l('create_biolink_tmonetimeoffer_modal.h4') ?></option>
            <option value="h5" <?= $row->settings->title_tag == 'h5' ? 'selected="selected"' : null ?>><?= l('create_biolink_tmonetimeoffer_modal.h5') ?></option>
            <option value="h6" <?= $row->settings->title_tag == 'h6' ? 'selected="selected"' : null ?>><?= l('create_biolink_tmonetimeoffer_modal.h6') ?></option>
                    </select>
                </div>
                </div>
                
    <button class="btn btn-block btn-gray-300 my-4" type="button" data-toggle="collapse" data-target="#setting_link" aria-expanded="false" aria-controls="link">
		<?= l('create_biolink_tmonetimeoffer_modal.settings_link') ?>
		<div class="link-btn-arrow-wrapper-setting" >		
		</div>
	</button>

	<div class="collapse" id="setting_link">
                
    
    <div class="form-group">
        <label for="<?= 'tmonetimeoffer_location_url_before_' . $row->biolink_block_id ?>"><i class="fas fa-fw fas fa-link fa-sm text-muted mr-1"></i> <?= l('create_biolink_tmonetimeoffer_modal.location_url_before') ?></label>
        <input id="<?= 'tmonetimeoffer_location_url_before_' . $row->biolink_block_id ?>" class="form-control" name="location_url_before" placeholder="<?= l('create_biolink_tmonetimeoffer_modal.location_url_before.placeholder') ?>" maxlength="2048" required="required" value="<?= $row->settings->location_url_before ?>" />
    </div>
    
    <div class="form-group">
        <label for="<?= 'tmonetimeoffer_button_text_before_' . $row->biolink_block_id ?>"><i class="fas fa-fw fas fa-link fa-sm text-muted mr-1"></i> <?= l('create_biolink_tmonetimeoffer_modal.button_text_before') ?></label>
        <input id="<?= 'tmonetimeoffer_button_text_before_' . $row->biolink_block_id ?>" class="form-control" name="button_text_before" placeholder="<?= l('create_biolink_tmonetimeoffer_modal.button_text_before.placeholder') ?>" maxlength="2048" required="required" value="<?= $row->settings->button_text_before ?>" />
    </div>
    
    <div class="form-group">
        <label for="<?= 'tmonetimeoffer_location_url_after_' . $row->biolink_block_id ?>"><i class="fas fa-fw fas fa-link fa-sm text-muted mr-1"></i> <?= l('create_biolink_tmonetimeoffer_modal.location_url_after') ?></label>
        <input id="<?= 'tmonetimeoffer_location_url_after_' . $row->biolink_block_id ?>" class="form-control" name="location_url_after" placeholder="<?= l('create_biolink_tmonetimeoffer_modal.location_url_after.placeholder') ?>" maxlength="2048" value="<?= $row->settings->location_url_after ?>" />
         <small class="form-text text-muted"><?= l('create_biolink_tmonetimeoffer_modal.location_url_after.description') ?></small>
    </div>
    
    <div class="form-group">
        <label for="<?= 'tmonetimeoffer_button_text_after_' . $row->biolink_block_id ?>"><i class="fas fa-fw fas fa-link fa-sm text-muted mr-1"></i> <?= l('create_biolink_tmonetimeoffer_modal.button_text_after') ?></label>
        <input id="<?= 'tmonetimeoffer_button_text_after_' . $row->biolink_block_id ?>" class="form-control" name="button_text_after" placeholder="<?= l('create_biolink_tmonetimeoffer_modal.button_text_after.placeholder') ?>" maxlength="2048" value="<?= $row->settings->button_text_after ?>" />
         <small class="form-text text-muted"><?= l('create_biolink_tmonetimeoffer_modal.button_text_after.description') ?></small>
    </div>
    
    
        <div class="form-group custom-control custom-switch">
        <input
                id="<?= 'link_open_in_new_tab_' . $row->biolink_block_id ?>"
                name="open_in_new_tab" type="checkbox"
                class="custom-control-input"
            <?= $row->settings->open_in_new_tab ? 'checked="checked"' : null ?>
        >
        <label class="custom-control-label" for="<?= 'link_open_in_new_tab_' . $row->biolink_block_id ?>"><?= l('create_biolink_link_modal.input.open_in_new_tab') ?></label>
    </div>
    
            <div class="form-group">
                <label for="<?= 'link_animation_' . $row->biolink_block_id ?>"><i class="fas fa-fw fa-film fa-sm text-muted mr-1"></i> <?= l('create_biolink_link_modal.input.animation') ?></label>
                <select id="<?= 'link_animation_' . $row->biolink_block_id ?>" name="animation" class="form-control">
                    <option value="false" <?= !$row->settings->animation ? 'selected="selected"' : null ?>>-</option>
                    <?php foreach(require APP_PATH . 'includes/biolink_animations.php' as $animation): ?>
                        <option value="<?= $animation ?>" <?= $row->settings->animation == $animation ? 'selected="selected"' : null ?>><?= $animation ?></option>
                    <?php endforeach ?>
                </select>
            </div>

            <div class="form-group">
                <label for="<?= 'link_animation_runs_' . $row->biolink_block_id ?>"><i class="fas fa-fw fa-play-circle fa-sm text-muted mr-1"></i> <?= l('create_biolink_link_modal.input.animation_runs') ?></label>
                <select id="<?= 'link_animation_runs_' . $row->biolink_block_id ?>" name="animation_runs" class="form-control">
                    <option value="repeat-1" <?= $row->settings->animation_runs == 'repeat-1' ? 'selected="selected"' : null ?>>1</option>
                    <option value="repeat-2" <?= $row->settings->animation_runs == 'repeat-2' ? 'selected="selected"' : null ?>>2</option>
                    <option value="repeat-3" <?= $row->settings->animation_runs == 'repeat-3' ? 'selected="selected"' : null ?>>3</option>
                    <option value="infinite" <?= $row->settings->animation_runs == 'repeat-infinite' ? 'selected="selected"' : null ?>><?= l('create_biolink_link_modal.input.animation_runs_infinite') ?></option>
                </select>
            </div>
            
            <div class="form-group">
                <label for="<?= 'link_border_width_' . $row->biolink_block_id ?>"><i class="fas fa-fw fa-border-style fa-sm text-muted mr-1"></i> <?= l('create_biolink_link_modal.input.border_width') ?></label>
                <input id="<?= 'link_border_width_' . $row->biolink_block_id ?>" type="range" min="0" max="5" class="form-control-range" name="border_width" value="<?= $row->settings->border_width ?>" required="required" />
            </div>
            
            <div class="form-group">
                <label for="<?= 'block_border_radius_' . $row->biolink_block_id ?>"><i class="fas fa-fw fa-border-all fa-sm text-muted mr-1"></i> <?= l('create_biolink_link_modal.input.border_radius') ?></label>
                <div class="row btn-group-toggle" data-toggle="buttons">
                    <div class="col-4">
                        <label class="btn btn-light btn-block <?= ($row->settings->border_radius  ?? null) == 'straight' ? 'active"' : null?>">
                            <input type="radio" name="border_radius" value="straight" class="custom-control-input" <?= ($row->settings->border_radius  ?? null) == 'straight' ? 'checked="checked"' : null?> />
                            <i class="fas fa-fw fa-square-full fa-sm mr-1"></i> <?= l('create_biolink_link_modal.input.border_radius_straight') ?>
                        </label>
                    </div>
                    <div class="col-4">
                        <label class="btn btn-light btn-block <?= ($row->settings->border_radius  ?? null) == 'round' ? 'active' : null?>">
                            <input type="radio" name="border_radius" value="round" class="custom-control-input" <?= ($row->settings->border_radius  ?? null) == 'round' ? 'checked="checked"' : null?> />
                            <i class="fas fa-fw fa-circle fa-sm mr-1"></i> <?= l('create_biolink_link_modal.input.border_radius_round') ?>
                        </label>
                    </div>
                    <div class="col-4">
                        <label class="btn btn-light btn-block <?= ($row->settings->border_radius  ?? null) == 'rounded' ? 'active' : null?>">
                            <input type="radio" name="border_radius" value="rounded" class="custom-control-input" <?= ($row->settings->border_radius  ?? null) == 'rounded' ? 'checked="checked"' : null?> />
                            <i class="fas fa-fw fa-square fa-sm mr-1"></i> <?= l('create_biolink_link_modal.input.border_radius_rounded') ?>
                        </label>
                    </div>
                </div>
            </div>

            <div class="form-group">
                <label for="<?= 'block_border_style_' . $row->biolink_block_id ?>"><i class="fas fa-fw fa-border-none fa-sm text-muted mr-1"></i> <?= l('create_biolink_link_modal.input.border_style') ?></label>
                <div class="row btn-group-toggle" data-toggle="buttons">
                    <?php foreach(['solid', 'dashed', 'double', 'outset', 'inset'] as $border_style): ?>
                        <div class="col-4">
                            <label class="btn btn-light btn-block <?= ($row->settings->border_style  ?? null) == $border_style ? 'active"' : null?>">
                                <input type="radio" name="border_style" value="<?= $border_style ?>" class="custom-control-input" <?= ($row->settings->border_style  ?? null) == $border_style ? 'checked="checked"' : null?> />
                                <?= l('create_biolink_link_modal.input.border_style_' . $border_style) ?>
                            </label>
                        </div>
                    <?php endforeach ?>
                </div>
            </div>
            
            </div>
    
    <button class="btn btn-block btn-gray-300 my-4" type="button" data-toggle="collapse" data-target="#setting_text" aria-expanded="false" aria-controls="text">
		<?= l('create_biolink_tmonetimeoffer_modal.settings_text') ?>
		<div class="link-btn-arrow-wrapper-setting" >		
		</div>
	</button>

	<div class="collapse" id="setting_text">
                
    <div class="form-group">
         <label for="<?= 'tmonetimeoffer_text_tag_' . $row->biolink_block_id ?>"><i class="fas fa-fw fa-align-center fa-sm text-muted mr-1"></i> <?= l('create_biolink_tmonetimeoffer_modal.text_tag') ?></label>
            <select id="text_tag" name="text_tag" class="form-control" >
            <option value="p" <?= $row->settings->text_tagh == 'p' ? 'selected="selected"' : null ?>><?= l('create_biolink_tmonetimeoffer_modal.p') ?></option>
            <option value="h1" <?= $row->settings->text_tagh == 'h1' ? 'selected="selected"' : null ?>><?= l('create_biolink_tmonetimeoffer_modal.h1') ?></option>
            <option value="h2" <?= $row->settings->text_tagh == 'h2' ? 'selected="selected"' : null ?>><?= l('create_biolink_tmonetimeoffer_modal.h2') ?></option>
            <option value="h3" <?= $row->settings->text_tagh == 'h3' ? 'selected="selected"' : null ?>><?= l('create_biolink_tmonetimeoffer_modal.h3') ?></option>
            <option value="h4" <?= $row->settings->text_tagh == 'h4' ? 'selected="selected"' : null ?>><?= l('create_biolink_tmonetimeoffer_modal.h4') ?></option>
            <option value="h5" <?= $row->settings->text_tagh == 'h5' ? 'selected="selected"' : null ?>><?= l('create_biolink_tmonetimeoffer_modal.h5') ?></option>
            <option value="h6" <?= $row->settings->text_tagh == 'h6' ? 'selected="selected"' : null ?>><?= l('create_biolink_tmonetimeoffer_modal.h6') ?></option>
                    </select>
                </div>
    
    <div class="form-group">
        <label for="<?= 'tmonetimeoffer_text_before_' . $row->biolink_block_id ?>"><i class="fas fa-fw fa-align-center fa-sm text-muted mr-1"></i> <?= l('create_biolink_tmonetimeoffer_modal.text_before') ?></label>
        <input id="<?= 'tmonetimeoffer_text_before_' . $row->biolink_block_id ?>" class="form-control" name="text_before" placeholder="<?= l('create_biolink_tmonetimeoffer_modal.text_before.placeholder') ?>" maxlength="2048" required="required" value="<?= $row->settings->text_before ?>" />
    </div>
    
    <div class="form-group">
        <label for="<?= 'tmonetimeoffer_text_after_' . $row->biolink_block_id ?>"><i class="fas fa-fw fa-paragraph fa-sm text-muted mr-1"></i> <?= l('create_biolink_tmonetimeoffer_modal.text_after') ?></label>
        <input id="<?= 'tmonetimeoffer_text_after_' . $row->biolink_block_id ?>" class="form-control" name="text_after" placeholder="<?= l('create_biolink_tmonetimeoffer_modal.text_after.placeholder') ?>" maxlength="2048" required="required" value="<?= $row->settings->text_after ?>" />
             <small class="form-text text-muted"><?= l('create_biolink_tmonetimeoffer_modal.text_after.description') ?></small>
    </div>
    </div>
    
    <button class="btn btn-block btn-gray-300 my-4" type="button" data-toggle="collapse" data-target="#setting_youtube" aria-expanded="false" aria-controls="youtube">
		<?= l('create_biolink_tmonetimeoffer_modal.settings_youtube') ?>
		<div class="link-btn-arrow-wrapper-setting" >		
		</div>
	</button>

	<div class="collapse" id="setting_youtube">
    
    <div class="form-group">
	<div class="custom-control custom-switch">
	<input id="<?= 'tmonetimeoffer_youtube_enable_' . $row->biolink_block_id ?>"name="youtube_enable" type="checkbox" class="custom-control-input"<?= $row->settings->youtube_enable ? 'checked="checked"' : null ?> />	<label class="custom-control-label" for="<?= 'tmonetimeoffer_youtube_enable_' . $row->biolink_block_id ?>"><?= l('create_biolink_tmonetimeoffer_modal.youtube_enable') ?></label></div></div>
	
    <div class="form-group">
	<div class="custom-control custom-switch">
	<input id="<?= 'tmonetimeoffer_youtube_autoplay_' . $row->biolink_block_id ?>"name="youtube_autoplay" type="checkbox" class="custom-control-input"<?= $row->settings->youtube_autoplay ? 'checked="checked"' : null ?> />	<label class="custom-control-label" for="<?= 'tmonetimeoffer_youtube_autoplay_' . $row->biolink_block_id ?>"><?= l('create_biolink_tmonetimeoffer_modal.youtube_autoplay') ?></label></div></div>
	
    <div class="form-group">
	<div class="custom-control custom-switch">
	<input id="<?= 'tmonetimeoffer_youtube_controls_' . $row->biolink_block_id ?>"name="youtube_controls" type="checkbox" class="custom-control-input"<?= $row->settings->youtube_controls ? 'checked="checked"' : null ?> />	<label class="custom-control-label" for="<?= 'tmonetimeoffer_youtube_controls_' . $row->biolink_block_id ?>"><?= l('create_biolink_tmonetimeoffer_modal.youtube_controls') ?></label></div></div>
	
    <div class="form-group">
	<div class="custom-control custom-switch">
	<input id="<?= 'tmonetimeoffer_youtube_info_' . $row->biolink_block_id ?>"name="youtube_info" type="checkbox" class="custom-control-input"<?= $row->settings->youtube_info ? 'checked="checked"' : null ?> />	<label class="custom-control-label" for="<?= 'tmonetimeoffer_youtube_info_' . $row->biolink_block_id ?>"><?= l('create_biolink_tmonetimeoffer_modal.youtube_info') ?></label></div></div>
	
    <div class="form-group">
	<div class="custom-control custom-switch">
	<input id="<?= 'tmonetimeoffer_youtube_related_' . $row->biolink_block_id ?>"name="youtube_related" type="checkbox" class="custom-control-input"<?= $row->settings->youtube_related ? 'checked="checked"' : null ?> />	<label class="custom-control-label" for="<?= 'tmonetimeoffer_youtube_related_' . $row->biolink_block_id ?>"><?= l('create_biolink_tmonetimeoffer_modal.youtube_related') ?></label></div></div>
	
    <div class="form-group">
	<div class="custom-control custom-switch">
	<input id="<?= 'tmonetimeoffer_youtube_loop_' . $row->biolink_block_id ?>"name="youtube_loop" type="checkbox" class="custom-control-input"<?= $row->settings->youtube_loop ? 'checked="checked"' : null ?> />	<label class="custom-control-label" for="<?= 'tmonetimeoffer_youtube_loop_' . $row->biolink_block_id ?>"><?= l('create_biolink_tmonetimeoffer_modal.youtube_loop') ?></label></div></div>
    
    <div class="form-group">
        <label for="<?= 'tmonetimeoffer_youtube_before_' . $row->biolink_block_id ?>"><i class="fas fa-fw fab fa-youtube fa-sm text-muted mr-1"></i> <?= l('create_biolink_tmonetimeoffer_modal.youtube_before') ?></label>
        <input id="<?= 'tmonetimeoffer_youtube_before_' . $row->biolink_block_id ?>" class="form-control" name="youtube_before" placeholder="<?= l('create_biolink_tmonetimeoffer_modal.youtube_before.placeholder') ?>" maxlength="2048" value="<?= $row->settings->youtube_before ?>" />
    </div>
    
    <div class="form-group">
        <label for="<?= 'tmonetimeoffer_youtube_after_' . $row->biolink_block_id ?>"><i class="fas fa-fw fab fa-youtube fa-sm text-muted mr-1"></i> <?= l('create_biolink_tmonetimeoffer_modal.youtube_after') ?></label>
        <input id="<?= 'tmonetimeoffer_youtube_after_' . $row->biolink_block_id ?>" class="form-control" name="youtube_after" placeholder="<?= l('create_biolink_tmonetimeoffer_modal.youtube_after.placeholder') ?>" maxlength="2048" value="<?= $row->settings->youtube_after ?>" />
                 <small class="form-text text-muted"><?= l('create_biolink_tmonetimeoffer_modal.youtube_after.description') ?></small>
    </div>
    </div>

    <button class="btn btn-block btn-gray-300 my-4" type="button" data-toggle="collapse" data-target="#setting_countdown" aria-expanded="false" aria-controls="countdown">
		<?= l('create_biolink_tmonetimeoffer_modal.settings_countdown') ?>
		<div class="link-btn-arrow-wrapper-setting" >		
		</div>
	</button>

	<div class="collapse" id="setting_countdown">	    
    <div class="form-group">
	<div class="custom-control custom-switch">
	<input id="<?= 'tmonetimeoffer_countdown_enable_' . $row->biolink_block_id ?>"name="countdown_enable" type="checkbox" class="custom-control-input"<?= $row->settings->countdown_enable ? 'checked="checked"' : null ?> />	<label class="custom-control-label" for="<?= 'tmonetimeoffer_countdown_enable_' . $row->biolink_block_id ?>"><?= l('create_biolink_tmonetimeoffer_modal.countdown_enable') ?></label></div></div>
	
	
   <div class="form-group">
	<div class="custom-control custom-switch">
	<input id="<?= 'tmonetimeoffer_dark_theme_' . $row->biolink_block_id ?>"name="dark_theme" type="checkbox" class="custom-control-input"<?= $row->settings->dark_theme ? 'checked="checked"' : null ?> />	<label class="custom-control-label" for="<?= 'tmonetimeoffer_dark_theme_' . $row->biolink_block_id ?>"><?= l('create_biolink_tmonetimeoffer_modal.dark_theme') ?></label></div></div>
	
    <div class="form-group">
	<div class="custom-control custom-switch">
	<input id="<?= 'tmonetimeoffer_clear_enable_' . $row->biolink_block_id ?>"name="clear_enable" type="checkbox" class="custom-control-input"<?= $row->settings->clear_enable ? 'checked="checked"' : null ?> />	<label class="custom-control-label" for="<?= 'tmonetimeoffer_clear_enable_' . $row->biolink_block_id ?>"><?= l('create_biolink_tmonetimeoffer_modal.clear_enable') ?></label></div>
	<small class="form-text text-muted"><?= l('create_biolink_tmonetimeoffer_modal.clear.description') ?></small>
	</div>
    
    <div class="form-group">
        <label for="<?= 'tmonetimeoffer_time_' . $row->biolink_block_id ?>"><i class="fas fa-fw far fa-clock fa-sm text-muted mr-1"></i> <?= l('create_biolink_tmonetimeoffer_modal.time') ?></label>
        <input id="<?= 'tmonetimeoffer_time_' . $row->biolink_block_id ?>" class="form-control" type="number" min="10" max="86400" name="time" placeholder="<?= l('create_biolink_tmonetimeoffer_modal.time.placeholder') ?>" maxlength="5" value="<?= $row->settings->time ?>" />
        <small class="form-text text-muted"><?= l('create_biolink_tmonetimeoffer_modal.time.description') ?></small>
    </div>
    
    <div class="form-group">
        <label for="<?= 'tmonetimeoffer_period_' . $row->biolink_block_id ?>"><i class="fas fa-fw far fa-clock fa-sm text-muted mr-1"></i> <?= l('create_biolink_tmonetimeoffer_modal.period') ?></label>
        <input id="<?= 'tmonetimeoffer_period_' . $row->biolink_block_id ?>" class="form-control" type="number" min="60" max="31536000" name="period" placeholder="<?= l('create_biolink_tmonetimeoffer_modal.period.placeholder') ?>" maxlength="8" value="<?= $row->settings->period ?>" />
        <small class="form-text text-muted"><?= l('create_biolink_tmonetimeoffer_modal.period.description') ?></small>
    </div>
    </div>
    
    <button class="btn btn-block btn-gray-300 my-4" type="button" data-toggle="collapse" data-target="#setting_image" aria-expanded="false" aria-controls="countdown">
		<?= l('create_biolink_tmonetimeoffer_modal.settings_image') ?>
		<div class="link-btn-arrow-wrapper-setting" >		
		</div>
	</button>

	<div class="collapse" id="setting_image">	
    
    <div class="form-group">
	<div class="custom-control custom-switch">
	<input id="<?= 'tmonetimeoffer_image_enable_' . $row->biolink_block_id ?>"name="image_enable" type="checkbox" class="custom-control-input"<?= $row->settings->image_enable ? 'checked="checked"' : null ?> />	<label class="custom-control-label" for="<?= 'tmonetimeoffer_image_enable_' . $row->biolink_block_id ?>"><?= l('create_biolink_tmonetimeoffer_modal.image_enable') ?></label></div></div>
    
    	<div class="form-group">
			<label for="<?= 'image_0_' . $row->biolink_block_id ?>"><i class="fas fa-fw fa-image fa-sm mr-1"></i> <?= l('create_biolink_tmonetimeoffer_modal.image_0') ?></label>
			<div data-image-container class="<?= !empty($row->settings->image_0) ? null : 'd-none' ?>">
			<div class="row">
				<div class="m-1 col-6 col-xl-3">
				<img src="<?= $row->settings->image_0 ? UPLOADS_FULL_URL . 'block_images/' . $row->settings->image_0 : null ?>" class="img-fluid rounded <?= !empty($row->settings->image_0) ? null : 'd-none' ?>" loading="lazy" />
								</div>
							</div>
						</div>
			<input id="<?= 'image_0_' . $row->biolink_block_id ?>" type="file" name="image_0" accept=".gif, .png, .jpg, .jpeg, .svg" class="form-control-file" />
					
					
					 <div class="custom-control custom-checkbox my-2">
                <input id="<?= $row->biolink_block_id . '_image_0_remove' ?>" name="image_0_remove" type="checkbox" class="custom-control-input" onchange="this.checked ? document.querySelector('#<?= 'link_image_' . $row->biolink_block_id ?>').classList.add('d-none') : document.querySelector('#<?= 'link_image_' . $row->biolink_block_id ?>').classList.remove('d-none')">
                <label class="custom-control-label" for="<?= $row->biolink_block_id . '_image_0_remove' ?>">
                    <span class="text-muted"><?= l('global.delete_file') ?></span>
                </label>
            </div>
            </div>
            
    	<div class="form-group">
			<label for="<?= 'image_1_' . $row->biolink_block_id ?>"><i class="fas fa-fw fa-image fa-sm mr-1"></i> <?= l('create_biolink_tmonetimeoffer_modal.image_1') ?></label>
			<div data-image-container class="<?= !empty($row->settings->image_1) ? null : 'd-none' ?>">
			<div class="row">
				<div class="m-1 col-6 col-xl-3">
				<img src="<?= $row->settings->image_1 ? UPLOADS_FULL_URL . 'block_images/' . $row->settings->image_1 : null ?>" class="img-fluid rounded <?= !empty($row->settings->image_1) ? null : 'd-none' ?>" loading="lazy" />
								</div>
							</div>
						</div>
			<input id="<?= 'image_1_' . $row->biolink_block_id ?>" type="file" name="image_1" accept=".gif, .png, .jpg, .jpeg, .svg" class="form-control-file" />
		<small class="form-text text-muted"><?= l('create_biolink_tmonetimeoffer_modal.image_1.description') ?></small>
					
					
					 <div class="custom-control custom-checkbox my-2">
                <input id="<?= $row->biolink_block_id . '_image_1_remove' ?>" name="image_1_remove" type="checkbox" class="custom-control-input" onchange="this.checked ? document.querySelector('#<?= 'link_image_' . $row->biolink_block_id ?>').classList.add('d-none') : document.querySelector('#<?= 'link_image_' . $row->biolink_block_id ?>').classList.remove('d-none')">
                <label class="custom-control-label" for="<?= $row->biolink_block_id . '_image_1_remove' ?>">
                    <span class="text-muted"><?= l('global.delete_file') ?></span>
                </label>
            </div>
            </div>
            </div>
            
    <button class="btn btn-block btn-gray-300 my-4" type="button" data-toggle="collapse" data-target="#setting_color" aria-expanded="false" aria-controls="color">
		<?= l('create_biolink_tmonetimeoffer_modal.settings_color') ?>
		<div class="link-btn-arrow-wrapper-setting" >		
		</div>
	</button>

	<div class="collapse" id="setting_color">

            <div class="form-group">
                <label><i class="fas fa-fw fa-paint-brush fa-sm text-muted mr-1"></i> <?= l('create_biolink_tmonetimeoffer_modal.text_color') ?></label>
                <input type="hidden" name="text_color" class="form-control" value="<?= $row->settings->text_color ?>" required="required" />
                <div class="text_color_pickr"></div>
            </div>
            
            <div class="form-group">
                <label><i class="fas fa-fw fa-fill fa-sm text-muted mr-1"></i> <?= l('create_biolink_tmonetimeoffer_modal.background_color') ?></label>
                <input type="hidden" name="background_color" class="form-control" value="<?= $row->settings->background_color ?>" required="required" />
                <div class="background_color_pickr"></div>
            </div>
            
                <div class="form-group">
                    <label><i class="fas fa-fw fa-fill fa-sm text-muted mr-1"></i> <?= l('create_biolink_tmonetimeoffer_modal.background_color_button') ?></label>
                    <input type="hidden" name="border_color" class="form-control" value="<?= $row->settings->border_color ?>" required="required" />
                    <div class="border_color_pickr"></div>
                </div>
            </div>
                

    
    <button class="btn btn-block btn-gray-300 my-4" type="button" data-toggle="collapse" data-target="#<?= 'display_settings_container_' . $row->biolink_block_id ?>" aria-expanded="false" aria-controls="<?= 'display_settings_container_' . $row->biolink_block_id ?>">
        <?= l('create_biolink_link_modal.display_settings_header') ?>
    </button>

    <div class="collapse" id="<?= 'display_settings_container_' . $row->biolink_block_id ?>">
        <div <?= $this->user->plan_settings->temporary_url_is_enabled ? null : 'data-toggle="tooltip" title="' . l('global.info_message.plan_feature_no_access') . '"' ?>>
            <div class="<?= $this->user->plan_settings->temporary_url_is_enabled ? null : 'container-disabled' ?>">
                <div class="custom-control custom-switch mb-3">
                    <input
                            id="<?= 'link_schedule_' . $row->biolink_block_id ?>"
                            name="schedule" type="checkbox"
                            class="custom-control-input"
                        <?= !empty($row->start_date) && !empty($row->end_date) ? 'checked="checked"' : null ?>
                        <?= $this->user->plan_settings->temporary_url_is_enabled ? null : 'disabled="disabled"' ?>
                    >
                    <label class="custom-control-label" for="<?= 'link_schedule_' . $row->biolink_block_id ?>"><?= l('link.settings.schedule') ?></label>
                    <small class="form-text text-muted"><?= l('link.settings.schedule_help') ?></small>
                </div>
            </div>
        </div>

        <div class="mt-3 schedule_container" style="display: none;">
            <div <?= $this->user->plan_settings->temporary_url_is_enabled ? null : 'data-toggle="tooltip" title="' . l('global.info_message.plan_feature_no_access') . '"' ?>>
                <div class="<?= $this->user->plan_settings->temporary_url_is_enabled ? null : 'container-disabled' ?>">
                    <div class="row">
                        <div class="col">
                            <div class="form-group">
                                <label for="<?= 'link_start_date_' . $row->biolink_block_id ?>"><i class="fas fa-fw fa-clock fa-sm text-muted mr-1"></i> <?= l('link.settings.start_date') ?></label>
                                <input
                                        id="<?= 'link_start_date_' . $row->biolink_block_id ?>"
                                        type="text"
                                        class="form-control"
                                        name="start_date"
                                        value="<?= \Altum\Date::get($row->start_date, 1) ?>"
                                        placeholder="<?= l('link.settings.start_date') ?>"
                                        autocomplete="off"
                                        data-daterangepicker
                                >
                            </div>
                        </div>

                        <div class="col">
                            <div class="form-group">
                                <label for="<?= 'link_end_date_' . $row->biolink_block_id ?>"><i class="fas fa-fw fa-clock fa-sm text-muted mr-1"></i> <?= l('link.settings.end_date') ?></label>
                                <input
                                        id="<?= 'link_end_date_' . $row->biolink_block_id ?>"
                                        type="text"
                                        class="form-control"
                                        name="end_date"
                                        value="<?= \Altum\Date::get($row->end_date, 1) ?>"
                                        placeholder="<?= l('link.settings.end_date') ?>"
                                        autocomplete="off"
                                        data-daterangepicker
                                >
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="form-group">
            <label for="<?= 'link_display_countries_' . $row->biolink_block_id ?>"><i class="fas fa-fw fa-globe fa-sm text-muted mr-1"></i> <?= l('global.countries') ?></label>
            <select id="<?= 'link_display_countries_' . $row->biolink_block_id ?>" name="display_countries[]" class="form-control" multiple="multiple">
                <?php foreach(get_countries_array() as $country => $country_name): ?>
                    <option value="<?= $country ?>" <?= in_array($country, $row->settings->display_countries ?? []) ? 'selected="selected"' : null ?>><?= $country_name ?></option>
                <?php endforeach ?>
            </select>
            <small class="form-text text-muted"><?= l('create_biolink_link_modal.input.display_countries_help') ?></small>
        </div>

        <div class="form-group">
            <label for="<?= 'link_display_devices_' . $row->biolink_block_id ?>"><i class="fas fa-fw fa-laptop fa-sm text-muted mr-1"></i> <?= l('create_biolink_link_modal.input.display_devices') ?></label>
            <select id="<?= 'link_display_devices_' . $row->biolink_block_id ?>" name="display_devices[]" class="form-control" multiple="multiple">
                <?php foreach(['desktop', 'tablet', 'mobile'] as $device_type): ?>
                    <option value="<?= $device_type ?>" <?= in_array($device_type, $row->settings->display_devices ?? []) ? 'selected="selected"' : null ?>><?= l('global.device.' . $device_type) ?></option>
                <?php endforeach ?>
            </select>
            <small class="form-text text-muted"><?= l('create_biolink_link_modal.input.display_devices_help') ?></small>
        </div>

        <div class="form-group">
            <label for="<?= 'link_display_languages_' . $row->biolink_block_id ?>"><i class="fas fa-fw fa-language fa-sm text-muted mr-1"></i> <?= l('create_biolink_link_modal.input.display_languages') ?></label>
            <select id="<?= 'link_display_languages_' . $row->biolink_block_id ?>" name="display_languages[]" class="form-control" multiple="multiple">
                <?php foreach(get_locale_languages_array() as $locale => $language): ?>
                    <option value="<?= $locale ?>" <?= in_array($locale, $row->settings->display_languages ?? []) ? 'selected="selected"' : null ?>><?= $language ?></option>
                <?php endforeach ?>
            </select>
            <small class="form-text text-muted"><?= l('create_biolink_link_modal.input.display_languages_help') ?></small>
        </div>

        <div class="form-group">
            <label for="<?= 'link_display_operating_systems_' . $row->biolink_block_id ?>"><i class="fas fa-fw fa-window-restore fa-sm text-muted mr-1"></i> <?= l('create_biolink_link_modal.input.display_operating_systems') ?></label>
            <select id="<?= 'link_display_operating_systems_' . $row->biolink_block_id ?>" name="display_operating_systems[]" class="form-control" multiple="multiple">
                <?php foreach(['iOS', 'Android', 'Windows', 'OS X', 'Linux', 'Ubuntu', 'Chrome OS'] as $os_name): ?>
                    <option value="<?= $os_name ?>" <?= in_array($os_name, $row->settings->display_operating_systems ?? []) ? 'selected="selected"' : null ?>><?= $os_name ?></option>
                <?php endforeach ?>
            </select>
            <small class="form-text text-muted"><?= l('create_biolink_link_modal.input.display_operating_systems_help') ?></small>
        </div>
    </div>


    <div class="mt-4">
        <button type="submit" name="submit" class="btn btn-block btn-primary" data-is-ajax><?= l('global.update') ?></button>
    </div>
</form>
