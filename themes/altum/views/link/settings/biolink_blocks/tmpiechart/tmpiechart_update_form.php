<?php defined('ALTUMCODE') || die() ?>

<form name="update_biolink_" method="post" role="form">
    <input type="hidden" name="token" value="<?= \Altum\Csrf::get() ?>" required="required" />
    <input type="hidden" name="request_type" value="update" />
    <input type="hidden" name="block_type" value="tmpiechart" />
    <input type="hidden" name="biolink_block_id" value="<?= $row->biolink_block_id ?>" />

    <div class="notification-container"></div>

    <div id="<?= 'tmpiechart_items_' . $row->biolink_block_id ?>" data-biolink-block-id="<?= $row->biolink_block_id ?>">
        <?php foreach($row->settings->items as $key => $item): ?>
            <div class="mb-4">
                <div class="form-group">
                    <label for="<?= 'item_title_' . $key . '_' . $row->biolink_block_id ?>"><i class="fas fa-fw fa-signature fa-sm text-muted mr-1"></i> <?= l('create_biolink_tmpiechart_modal.title') ?></label>
                    <input id="<?= 'item_title_' . $key . '_' . $row->biolink_block_id ?>" type="text" name="item_title[<?= $key ?>]" class="form-control" value="<?= $item->title ?>" required="required" />
                </div>

                <div class="form-group">
                    <label for="<?= 'item_content_' . $key . '_' . $row->biolink_block_id ?>"><i class="fas fa-fw fa-pen fa-sm text-muted mr-1"></i> <?= l('create_biolink_tmpiechart_modal.content') ?></label>
                    <input id="<?= 'item_content_' . $key . '_' . $row->biolink_block_id ?>" name="item_content[<?= $key ?>]" class="form-control" maxlength="2048" required="required" value="<?= $item->content ?>" />
                </div>
                
                            
                    <div class="form-group">
                    <label for="<?= 'item_color_' . $key . '_' . $row->biolink_block_id ?>"><i class="fas fa-fw fa-expand fa-sm text-muted mr-1"></i> <?= l('create_biolink_tmpiechart_modal.color') ?></label>
                    <select id="<?= 'item_color_' . $key . '_' . $row->biolink_block_id ?>" name="item_color[<?= $key ?>]" class="form-control" >
            <option value="white" <?= $item->color == 'white' ? 'selected="selected"' : null ?>><?= l('create_biolink_tmpiechart_modal.white') ?></option>
            <option value="#c7ccd1" <?= $item->color == '#c7ccd1' ? 'selected="selected"' : null ?>><?= l('create_biolink_tmpiechart_modal.silver') ?></option>
            <option value="#9db1cc" <?= $item->color == '#9db1cc' ? 'selected="selected"' : null ?>><?= l('create_biolink_tmpiechart_modal.gray') ?></option>
            <option value="#282828" <?= $item->color == '#282828' ? 'selected="selected"' : null ?>><?= l('create_biolink_tmpiechart_modal.black') ?></option>
            <option value="#dd6864" <?= $item->color == '#dd6864' ? 'selected="selected"' : null ?>><?= l('create_biolink_tmpiechart_modal.red') ?></option>
            <option value="#8b0000" <?= $item->color == '#8b0000' ? 'selected="selected"' : null ?>><?= l('create_biolink_tmpiechart_modal.maroon') ?></option>
            <option value="#fab657" <?= $item->color == '#fab657' ? 'selected="selected"' : null ?>><?= l('create_biolink_tmpiechart_modal.yellow') ?></option>
            <option value="#00856f" <?= $item->color == '#00856f' ? 'selected="selected"' : null ?>><?= l('create_biolink_tmpiechart_modal.olive') ?></option>
            <option value="#1e5945" <?= $item->color == '#1e5945' ? 'selected="selected"' : null ?>><?= l('create_biolink_tmpiechart_modal.lime') ?></option>
            <option value="#7fc77f" <?= $item->color == '#7fc77f' ? 'selected="selected"' : null ?>><?= l('create_biolink_tmpiechart_modal.green') ?></option>
            <option value="#1ca9c9" <?= $item->color == '#1ca9c9' ? 'selected="selected"' : null ?>><?= l('create_biolink_tmpiechart_modal.aqua') ?></option>
            <option value="#105f70" <?= $item->color == '#105f70' ? 'selected="selected"' : null ?>><?= l('create_biolink_tmpiechart_modal.teal') ?></option>
            <option value="#5093ce" <?= $item->color == '#5093ce' ? 'selected="selected"' : null ?>><?= l('create_biolink_tmpiechart_modal.blue') ?></option>
            <option value="#003841" <?= $item->color == '#003841' ? 'selected="selected"' : null ?>><?= l('create_biolink_tmpiechart_modal.navy') ?></option>
            <option value="#eaaede" <?= $item->color == '#eaaede' ? 'selected="selected"' : null ?>><?= l('create_biolink_tmpiechart_modal.fuchsia') ?></option>
            <option value="#423c63" <?= $item->color == '#423c63' ? 'selected="selected"' : null ?>><?= l('create_biolink_tmpiechart_modal.purple') ?></option>
                    
                    </select>
                </div>
                

                <button type="button" data-remove="item" class="btn btn-block btn-outline-danger"><i class="fas fa-fw fa-times"></i> <?= l('global.delete') ?></button>
            </div>
        <?php endforeach ?>
    </div>

    <div class="mb-3">
        
                    <div class="form-group">
                    <label for="<?= 'tmpiechart_title_block_' . $row->biolink_block_id ?>"><i class="fas fa-fw fa-signature fa-sm text-muted mr-1"></i> <?= l('create_biolink_tmpiechart_modal.title_block') ?></label>
                    <input id="<?= 'tmpiechart_title_block_' . $row->biolink_block_id ?>" type="text" name="title_block" class="form-control" maxlength="2048"  value="<?= $row->settings->title_block ?>" placeholder="<?= l('create_biolink_tmpiechart_modal.title_block_placeholder') ?>" />
                </div>
        
                    <div class="form-group">
                <label for="<?= 'tmpiechart_text_color_' . $row->biolink_block_id ?>"><i class="fas fa-fw fa-paint-brush fa-sm text-muted mr-1"></i> <?= l('create_biolink_tmpiechart_modal.input.text_color') ?></label>
                <input id="<?= 'tmpiechart_text_color_' . $row->biolink_block_id ?>" type="hidden" name="text_color" class="form-control" value="<?= $row->settings->text_color ?>" required="required" />
                <div class="text_color_pickr"></div>
            </div>
        
                    <div class="form-group">
                <label for="<?= 'tmpiechart_background_color_' . $row->biolink_block_id ?>"><i class="fas fa-fw fa-fill fa-sm text-muted mr-1"></i> <?= l('create_biolink_tmpiechart_modal.input.background_color') ?></label>
                <input id="<?= 'tmpiechart_background_color_' . $row->biolink_block_id ?>" type="hidden" name="background_color" class="form-control" value="<?= $row->settings->background_color ?>" required="required" />
                <div class="background_color_pickr"></div>
            </div>
            
            
        <button data-add="tmpiechart_item" data-biolink-block-id="<?= $row->biolink_block_id ?>" type="button" class="btn btn-sm btn-outline-success"><i class="fas fa-fw fa-plus-circle"></i> <?= l('global.create') ?></button>
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
        <button type="submit" name="submit" class="btn btn-block btn-primary"><?= l('global.update') ?></button>
    </div>
</form>
