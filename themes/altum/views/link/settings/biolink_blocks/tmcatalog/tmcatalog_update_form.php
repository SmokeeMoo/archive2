<?php defined('ALTUMCODE') || die() ?>

<form name="update_biolink_" method="post" role="form">
    <input type="hidden" name="token" value="<?= \Altum\Csrf::get() ?>" required="required" />
    <input type="hidden" name="request_type" value="update" />
    <input type="hidden" name="block_type" value="tmcatalog" />
    <input type="hidden" name="biolink_block_id" value="<?= $row->biolink_block_id ?>" />

    <div class="notification-container"></div>
    
    
    	<div class="form-group">
			<label for="<?= 'image_0_' . $row->biolink_block_id ?>"><i class="fas fa-fw fa-image fa-sm mr-1"></i> <?= l('create_biolink_tmcatalog_modal.image1') ?></label>
			<div data-image-container class="<?= !empty($row->settings->image_0) ? null : 'd-none' ?>">
			<div class="row">
				<div class="m-1 col-6 col-xl-3">
				<img src="<?= $row->settings->image_0 ? UPLOADS_FULL_URL . 'block_images/' . $row->settings->image_0 : null ?>" class="img-fluid rounded <?= !empty($row->settings->image_0) ? null : 'd-none' ?>" loading="lazy" />
								</div>
							</div>
						</div>
			<input id="<?= 'image_0_' . $row->biolink_block_id ?>" type="file" name="image_0" accept=".gif, .png, .jpg, .jpeg, .svg" class="form-control-file" />
					
					
					 <div class="custom-control custom-checkbox my-2">
                <input id="<?= $row->biolink_block_id . '_image_remove_0' ?>" name="image_remove_0" type="checkbox" class="custom-control-input" onchange="this.checked ? document.querySelector('#<?= 'link_image_' . $row->biolink_block_id ?>').classList.add('d-none') : document.querySelector('#<?= 'link_image_' . $row->biolink_block_id ?>').classList.remove('d-none')">
                <label class="custom-control-label" for="<?= $row->biolink_block_id . '_image_remove_0' ?>">
                    <span class="text-muted"><?= l('global.delete_file') ?></span>
                </label>
            </div>
            </div>

					
					
		    	<div class="form-group">
			<label for="<?= 'image_1_' . $row->biolink_block_id ?>"><i class="fas fa-fw fa-image fa-sm mr-1"></i> <?= l('create_biolink_tmcatalog_modal.image2') ?></label>
			<div data-image-container class="<?= !empty($row->settings->image_1) ? null : 'd-none' ?>">
			<div class="row">
				<div class="m-1 col-6 col-xl-3">
				<img src="<?= $row->settings->image_1 ? UPLOADS_FULL_URL . 'block_images/' . $row->settings->image_1 : null ?>" class="img-fluid rounded <?= !empty($row->settings->image_1) ? null : 'd-none' ?>" loading="lazy" />
								</div>
							</div>
						</div>
			<input id="<?= 'image_1_' . $row->biolink_block_id ?>" type="file" name="image_1" accept=".gif, .png, .jpg, .jpeg, .svg" class="form-control-file" />
			
				 <div class="custom-control custom-checkbox my-2">
                <input id="<?= $row->biolink_block_id . '_image_remove_1' ?>" name="image_remove_1" type="checkbox" class="custom-control-input" onchange="this.checked ? document.querySelector('#<?= 'link_image_' . $row->biolink_block_id ?>').classList.add('d-none') : document.querySelector('#<?= 'link_image_' . $row->biolink_block_id ?>').classList.remove('d-none')">
                <label class="custom-control-label" for="<?= $row->biolink_block_id . '_image_remove_1' ?>">
                    <span class="text-muted"><?= l('global.delete_file') ?></span>
                </label>
            </div>
					</div>
					
		    	<div class="form-group">
			<label for="<?= 'image_2_' . $row->biolink_block_id ?>"><i class="fas fa-fw fa-image fa-sm mr-1"></i> <?= l('create_biolink_tmcatalog_modal.image3') ?></label>
			<div data-image-container class="<?= !empty($row->settings->image_2) ? null : 'd-none' ?>">
			<div class="row">
				<div class="m-1 col-6 col-xl-3">
				<img src="<?= $row->settings->image_2 ? UPLOADS_FULL_URL . 'block_images/' . $row->settings->image_2 : null ?>" class="img-fluid rounded <?= !empty($row->settings->image_2) ? null : 'd-none' ?>" loading="lazy" />
								</div>
							</div>
						</div>
			<input id="<?= 'image_2_' . $row->biolink_block_id ?>" type="file" name="image_2" accept=".gif, .png, .jpg, .jpeg, .svg" class="form-control-file" />
			
				 <div class="custom-control custom-checkbox my-2">
                <input id="<?= $row->biolink_block_id . '_image_remove_2' ?>" name="image_remove_2" type="checkbox" class="custom-control-input" onchange="this.checked ? document.querySelector('#<?= 'link_image_' . $row->biolink_block_id ?>').classList.add('d-none') : document.querySelector('#<?= 'link_image_' . $row->biolink_block_id ?>').classList.remove('d-none')">
                <label class="custom-control-label" for="<?= $row->biolink_block_id . '_image_remove_2' ?>">
                    <span class="text-muted"><?= l('global.delete_file') ?></span>
                </label>
            </div>
					</div>
    
    <div class="form-group">
        <label for="<?= 'tmcatalog_title_' . $row->biolink_block_id ?>"><i class="fas fa-fw fa-pen fa-sm text-muted mr-1"></i> <?= l('create_biolink_tmcatalog_modal.title') ?></label>
        <input id="<?= 'tmcatalog_title_' . $row->biolink_block_id ?>" class="form-control" name="title" placeholder="<?= l('create_biolink_tmcatalog_modal.title.placeholder') ?>" maxlength="2048" value="<?= $row->settings->title ?>"/>
    </div>

    <div class="form-group">
        <label for="<?= 'tmcatalog_text_' . $row->biolink_block_id ?>"><i class="fas fa-fw fa-paragraph fa-sm text-muted mr-1"></i> <?= l('create_biolink_tmcatalog_modal.text') ?></label>
        <textarea id="<?= 'tmcatalog_text_' . $row->biolink_block_id ?>" class="form-control" name="text" placeholder="<?= l('create_biolink_tmcatalog_modal.text.placeholder') ?>" maxlength="2048"><?= $row->settings->text ?></textarea>
    </div>
    
    <div class="form-group">
        <label for="<?= 'tmcatalog_cost_' . $row->biolink_block_id ?>"><i class="fas fa-fw fa-dollar-sign fa-sm text-muted mr-1"></i> <?= l('create_biolink_tmcatalog_modal.cost') ?></label>
        <input id="<?= 'tmcatalog_cost_' . $row->biolink_block_id ?>" class="form-control" name="cost" placeholder="<?= l('create_biolink_tmcatalog_modal.cost.placeholder') ?>" maxlength="2048" value="<?= $row->settings->cost ?>"/>
    </div>
    
    <div class="form-group">
        <label for="<?= 'tmcatalog_title_link_' . $row->biolink_block_id ?>"><i class="fas fa-fw fa-pen fa-sm text-muted mr-1"></i> <?= l('create_biolink_tmcatalog_modal.title_link') ?></label>
        <input id="<?= 'tmcatalog_title_link_' . $row->biolink_block_id ?>" class="form-control" name="title_link" placeholder="<?= l('create_biolink_tmcatalog_modal.title_link.placeholder') ?>" maxlength="2048" value="<?= $row->settings->title_link ?>"/>
    </div>
    
    <div class="form-group">
        <label for="<?= 'tmcatalog_url_link_' . $row->biolink_block_id ?>"><i class="fas fa-fw fa-link fa-sm text-muted mr-1"></i> <?= l('create_biolink_tmcatalog_modal.url_link') ?></label>
        <input id="<?= 'tmcatalog_url_link_' . $row->biolink_block_id ?>" class="form-control" name="url_link" placeholder="<?= l('create_biolink_tmcatalog_modal.url_link.placeholder') ?>" maxlength="2048" value="<?= $row->settings->url_link ?>"/>
    </div>

            <div class="form-group">
                <label><i class="fas fa-fw fa-paint-brush fa-sm text-muted mr-1"></i> <?= l('create_biolink_tmcatalog_modal.text_color') ?></label>
                <input type="hidden" name="text_color" class="form-control" value="<?= $row->settings->text_color ?>" required="required" />
                <div class="text_color_pickr"></div>
            </div>
            
            <div class="form-group">
                <label><i class="fas fa-fw fa-paint-brush fa-sm text-muted mr-1"></i> <?= l('create_biolink_tmcatalog_modal.background_color') ?></label>
                <input type="hidden" name="background_color" class="form-control" value="<?= $row->settings->background_color ?>" required="required" />
                <div class="background_color_pickr"></div>
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
