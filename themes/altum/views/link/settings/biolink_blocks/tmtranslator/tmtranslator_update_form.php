<?php defined('ALTUMCODE') || die() ?>

<form name="update_biolink_" method="post" role="form">
    <input type="hidden" name="token" value="<?= \Altum\Csrf::get() ?>" required="required" />
    <input type="hidden" name="request_type" value="update" />
    <input type="hidden" name="block_type" value="tmtranslator" />
    <input type="hidden" name="biolink_block_id" value="<?= $row->biolink_block_id ?>" />

    <div class="notification-container"></div>

    <div id="<?= 'tmtranslator_items_' . $row->biolink_block_id ?>" data-biolink-block-id="<?= $row->biolink_block_id ?>">
        <?php foreach($row->settings->items as $key => $item): ?>
            <div class="mb-4">

            <label for="<?= 'item_lang_' . $key . '_' . $row->biolink_block_id ?>"><i class="fas fa-fw fa-language fa-sm text-muted mr-1"></i> <?= l('create_biolink_tmtranslator_modal.lang') ?></label>
            <div class="row">
           <div class="form-group col-9">
            <select id="<?= 'item_lang_' . $key . '_' . $row->biolink_block_id ?>" name="item_lang[<?= $key ?>]" class="form-control" >
            <option value="en" <?= $item->lang == 'en' ? 'selected="selected"' : null ?>><?= l('create_biolink_tmtranslator_modal.en') ?></option>
            <option value="es" <?= $item->lang == 'es' ? 'selected="selected"' : null ?>><?= l('create_biolink_tmtranslator_modal.es') ?></option>
            <option value="zh" <?= $item->lang == 'zh' ? 'selected="selected"' : null ?>><?= l('create_biolink_tmtranslator_modal.zh') ?></option>
            <option value="hi" <?= $item->lang == 'hi' ? 'selected="selected"' : null ?>><?= l('create_biolink_tmtranslator_modal.hi') ?></option>
            <option value="ar" <?= $item->lang == 'ar' ? 'selected="selected"' : null ?>><?= l('create_biolink_tmtranslator_modal.ar') ?></option>
            <option value="bn" <?= $item->lang == 'bn' ? 'selected="selected"' : null ?>><?= l('create_biolink_tmtranslator_modal.bn') ?></option>
            <option value="pt" <?= $item->lang == 'pt' ? 'selected="selected"' : null ?>><?= l('create_biolink_tmtranslator_modal.pt') ?></option>
            <option value="ja" <?= $item->lang == 'ja' ? 'selected="selected"' : null ?>><?= l('create_biolink_tmtranslator_modal.ja') ?></option>
            <option value="ms" <?= $item->lang == 'ms' ? 'selected="selected"' : null ?>><?= l('create_biolink_tmtranslator_modal.ms') ?></option>
            <option value="tr" <?= $item->lang == 'tr' ? 'selected="selected"' : null ?>><?= l('create_biolink_tmtranslator_modal.tr') ?></option>
            <option value="ko" <?= $item->lang == 'ko' ? 'selected="selected"' : null ?>><?= l('create_biolink_tmtranslator_modal.ko') ?></option>
            <option value="fr" <?= $item->lang == 'fr' ? 'selected="selected"' : null ?>><?= l('create_biolink_tmtranslator_modal.fr') ?></option>
            <option value="de" <?= $item->lang == 'de' ? 'selected="selected"' : null ?>><?= l('create_biolink_tmtranslator_modal.de') ?></option>
            <option value="it" <?= $item->lang == 'it' ? 'selected="selected"' : null ?>><?= l('create_biolink_tmtranslator_modal.it') ?></option>
            <option value="uk" <?= $item->lang == 'uk' ? 'selected="selected"' : null ?>><?= l('create_biolink_tmtranslator_modal.uk') ?></option>
            <option value="ru" <?= $item->lang == 'ru' ? 'selected="selected"' : null ?>><?= l('create_biolink_tmtranslator_modal.ru') ?></option>
                    
                    </select>
                </div>

                

                <button type="button" data-remove="item" class="btn btn-block btn-outline-danger col-2 ml-4" style="height:calc(1.5em + 0.75rem + 2px);"><i class="fas fa-fw fa-times"></i></button>
                </div>
            </div>
        <?php endforeach ?>
    </div>
    <div class="mb-3">
            <button data-add="tmtranslator_item" data-biolink-block-id="<?= $row->biolink_block_id ?>" type="button" class="btn btn-sm btn-outline-success"><i class="fas fa-fw fa-plus-circle"></i> <?= l('global.create') ?></button>
            </div>

    <div class="mb-3">
        
        
                 <div class="form-group">
                <label for="<?= 'main_lang_' . $row->biolink_block_id ?>"><i class="fas fa-fw fa-language fa-sm text-muted mr-1"></i> <?= l('create_biolink_tmtranslator_modal.main_lang') ?></label>
                <select id="<?= 'main_lang_' . $row->biolink_block_id ?>" name="main_lang" class="form-control" >
            <option value="en" <?= $row->settings->language == 'en' ? 'selected="selected"' : null ?>><?= l('create_biolink_tmtranslator_modal.en') ?></option>
             <option value="es" <?= $row->settings->language == 'es' ? 'selected="selected"' : null ?>><?= l('create_biolink_tmtranslator_modal.es') ?></option>
            <option value="zh" <?= $row->settings->language == 'zh' ? 'selected="selected"' : null ?>><?= l('create_biolink_tmtranslator_modal.zh') ?></option>
            <option value="hi" <?= $row->settings->language == 'hi' ? 'selected="selected"' : null ?>><?= l('create_biolink_tmtranslator_modal.hi') ?></option>
            <option value="ar" <?= $row->settings->language == 'ar' ? 'selected="selected"' : null ?>><?= l('create_biolink_tmtranslator_modal.ar') ?></option>
            <option value="bn" <?= $row->settings->language == 'bn' ? 'selected="selected"' : null ?>><?= l('create_biolink_tmtranslator_modal.bn') ?></option>
            <option value="pt" <?= $row->settings->language == 'pt' ? 'selected="selected"' : null ?>><?= l('create_biolink_tmtranslator_modal.pt') ?></option>
            <option value="ja" <?= $row->settings->language == 'ja' ? 'selected="selected"' : null ?>><?= l('create_biolink_tmtranslator_modal.ja') ?></option>
            <option value="ms" <?= $row->settings->language == 'ms' ? 'selected="selected"' : null ?>><?= l('create_biolink_tmtranslator_modal.ms') ?></option>
            <option value="tr" <?= $row->settings->language == 'tr' ? 'selected="selected"' : null ?>><?= l('create_biolink_tmtranslator_modal.tr') ?></option>
            <option value="ko" <?= $row->settings->language == 'ko' ? 'selected="selected"' : null ?>><?= l('create_biolink_tmtranslator_modal.ko') ?></option>
            <option value="fr" <?= $row->settings->language == 'fr' ? 'selected="selected"' : null ?>><?= l('create_biolink_tmtranslator_modal.fr') ?></option>
            <option value="de" <?= $row->settings->language == 'de' ? 'selected="selected"' : null ?>><?= l('create_biolink_tmtranslator_modal.de') ?></option>
            <option value="it" <?= $row->settings->language == 'it' ? 'selected="selected"' : null ?>><?= l('create_biolink_tmtranslator_modal.it') ?></option>
            <option value="uk" <?= $row->settings->language == 'uk' ? 'selected="selected"' : null ?>><?= l('create_biolink_tmtranslator_modal.uk') ?></option>
            <option value="ru" <?= $row->settings->language == 'ru' ? 'selected="selected"' : null ?>><?= l('create_biolink_tmtranslator_modal.ru') ?></option>
                    
                    </select>
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
        <button type="submit" name="submit" class="btn btn-block btn-primary"><?= l('global.update') ?></button>
    </div>
</form>
