<?php defined('ALTUMCODE') || die() ?>

<style>
.link-btn-arrow-wrapper-setting2 {
    overflow: hidden;
    position: relative;
    float: right;
    right: 20px;

}

.link-btn-arrow-wrapper-setting3 {
    overflow: hidden;
    position: relative;
    float: right;
    right: 20px;

}

a[aria-expanded=true] .fa-chevron-right {
   display: none;
}
a[aria-expanded=true] .fa-chevron-up {
   display: none;
}
a[aria-expanded=false] .fa-chevron-down {
   display: none;
}
</style>

<form name="update_biolink_" method="post" role="form">
    <input type="hidden" name="token" value="<?= \Altum\Csrf::get() ?>" required="required" />
    <input type="hidden" name="request_type" value="update" />
    <input type="hidden" name="block_type" value="tmmarket" />
    <input type="hidden" name="biolink_block_id" value="<?= $row->biolink_block_id ?>" />

    <div class="notification-container"></div>
    
              <!-- Start Settings -->
    
        <button class="btn btn-block btn-gray-300 my-4" type="button" data-toggle="collapse" data-target="#<?= 'tmmarket_settings_container_' . $row->biolink_block_id ?>" aria-expanded="false" aria-controls="<?= 'tmmarket_settings_container_' . $row->biolink_block_id ?>">
        <?= l('create_biolink_tmmarket_modal.phone_collector_header') ?>
    </button>
    
                <div class="collapse" id="<?= 'tmmarket_settings_container_' . $row->biolink_block_id ?>">
        <div class="form-group">
            <label for="<?= 'tmmarket_phone_placeholder_' . $row->biolink_block_id ?>"><?= l('create_biolink_tmmarket_modal.phone_placeholder') ?></label>
            <input id="<?= 'tmmarket_phone_placeholder_' . $row->biolink_block_id ?>" type="text" name="phone_placeholder" class="form-control" value="<?= $row->settings->phone_placeholder ?>" maxlength="64" required="required" />
        </div>

        <div class="form-group">
            <label for="<?= 'tmmarket_name_placeholder_' . $row->biolink_block_id ?>"><?= l('create_biolink_tmmarket_modal.name_placeholder') ?></label>
            <input id="<?= 'tmmarket_name_placeholder_' . $row->biolink_block_id ?>" type="text" name="name_placeholder" class="form-control" value="<?= $row->settings->name_placeholder ?>" maxlength="64" required="required" />
        </div>

        <div class="form-group">
            <label for="<?= 'tmmarket_button_text_' . $row->biolink_block_id ?>"><?= l('create_biolink_tmmarket_modal.button_text') ?></label>
            <input id="<?= 'tmmarket_button_text_' . $row->biolink_block_id ?>" type="text" name="button_text" class="form-control" value="<?= $row->settings->button_text ?>" maxlength="64" required="required" />
        </div>

        <div class="form-group">
            <label for="<?= 'tmmarket_success_text_' . $row->biolink_block_id ?>"><?= l('create_biolink_tmmarket_modal.success_text') ?></label>
            <input id="<?= 'tmmarket_success_text_' . $row->biolink_block_id ?>" type="text" name="success_text" class="form-control" value="<?= $row->settings->success_text ?>" maxlength="256" required="required" />
        </div>
        
        <div class="form-group">
            <label for="<?= 'tmmarket_currency_' . $row->biolink_block_id ?>"><?= l('create_biolink_tmmarket_modal.currency') ?></label>
            <input id="<?= 'tmmarket_currency_' . $row->biolink_block_id ?>" type="text" name="currency" class="form-control" placeholder="$" value="<?= $row->settings->currency ?>" maxlength="12" required="required" />
        </div>
        
                            <div class="form-group">
                <label for="<?= 'tmmarket_text_color_' . $row->biolink_block_id ?>"><i class="fas fa-fw fa-paint-brush fa-sm text-muted mr-1"></i> <?= l('create_biolink_tmmarket_modal.input.text_color') ?></label>
                <input id="<?= 'tmmarket_text_color_' . $row->biolink_block_id ?>" type="hidden" name="text_color" class="form-control" value="<?= $row->settings->text_color ?>" required="required" />
                <div class="text_color_pickr"></div>
            </div>
        
                    <div class="form-group">
                <label for="<?= 'tmmarket_background_color_' . $row->biolink_block_id ?>"><i class="fas fa-fw fa-fill fa-sm text-muted mr-1"></i> <?= l('create_biolink_tmmarket_modal.input.background_color') ?></label>
                <input id="<?= 'tmmarket_background_color_' . $row->biolink_block_id ?>" type="hidden" name="background_color" class="form-control" value="<?= $row->settings->background_color ?>" required="required" />
                <div class="background_color_pickr"></div>
            </div>
            
                    <div class="form-group">
                <label for="<?= 'tmmarket_button_color_' . $row->biolink_block_id ?>"><i class="fas fa-fw fa-fill fa-sm text-muted mr-1"></i> <?= l('create_biolink_tmmarket_modal.input.button_color') ?></label>
                <input id="<?= 'tmmarket_button_color_' . $row->biolink_block_id ?>" type="hidden" name="border_color" class="form-control" value="<?= $row->settings->border_color ?>" required="required" />
                <div class="border_color_pickr"></div>
            </div>

            
        </div>
         
         <!-- End Settings -->
         
    <button class="btn btn-block btn-gray-300 my-4" type="button" data-toggle="collapse" data-target="#<?= 'tmmarket_data_container_' . $row->biolink_block_id ?>" aria-expanded="false" aria-controls="<?= 'tmmarket_data_container_' . $row->biolink_block_id ?>">
        <?= l('create_biolink_block_modal.data_header') ?>
    </button>

    <div class="collapse" id="<?= 'tmmarket_data_container_' . $row->biolink_block_id ?>">
        <div class="alert alert-info">
            <i class="fas fa-fw fa-sm fa-info-circle mr-1"></i> <?= l('create_biolink_block_modal.data_help') ?>
        </div>

        <div class="form-group">
            <label for="<?= 'tmmarket_email_notification_' . $row->biolink_block_id ?>"><?= l('create_biolink_block_modal.email_notification') ?></label>
            <input type="text" id="<?= 'tmmarket_email_notification_' . $row->biolink_block_id ?>" name="email_notification" class="form-control" value="<?= $row->settings->email_notification ?? null ?>" maxlength="<?= $data->biolink_blocks['service']['fields']['email_notification']['max_length'] ?>" />
            <small class="form-text text-muted"><?= l('create_biolink_block_modal.email_notification_help') ?></small>
        </div>

        <div class="form-group">
            <label for="<?= 'tmmarket_webhook_url_' . $row->biolink_block_id ?>"><?= l('create_biolink_block_modal.webhook_url') ?></label>
            <input id="<?= 'tmmarket_webhook_url_' . $row->biolink_block_id ?>" type="text" name="webhook_url" class="form-control" value="<?= $row->settings->webhook_url ?>" maxlength="2048" />
            <small class="form-text text-muted"><?= l('create_biolink_block_modal.webhook_url_help') ?></small>
        </div>
    </div>
        
        <!-- Start Items -->
        <button class="btn btn-block btn-gray-300 my-4" type="button" data-toggle="collapse" data-target="#<?= 'tmmarket_items_container_' . $row->biolink_block_id ?>" aria-expanded="false" aria-controls="<?= 'tmmarket_items_container_' . $row->biolink_block_id ?>">
        <?= l('create_biolink_tmmarket_modal.items_header') ?>
    </button>
    
                <div class="collapse" id="<?= 'tmmarket_items_container_' . $row->biolink_block_id ?>">

    <div id="<?= 'tmmarket_items_' . $row->biolink_block_id ?>" data-biolink-block-id="<?= $row->biolink_block_id ?>">
        <?php foreach($row->settings->items as $key => $item): ?>
            <div class="mb-4">
                
                		<!-- Start block -->
				<?php if($item->enable): ?>
					<div class="d-flex align-items-center "style="border-radius: 6px; padding: 10px; border: 2px solid #d3d3d35e;">	
						<div class="col-2 mr-2 p-0"> 
							<img src="<?= $item->image ? UPLOADS_FULL_URL . 'block_images/' . $item->image : null ?>" class="img-fluid rounded <?= !empty($item->image) ? null : 'd-none' ?>" loading="lazy" />   
						</div>
						<div class="col-5 col-md-5"> 
							<div class="d-flex flex-column">
								<a href="#" data-toggle="collapse" data-target="#<?= 'Product' . ( $key + 1 ) . $row->biolink_block_id ?>" aria-expanded="false" aria-controls="<?= 'Product' . ( $key + 1 )  ?>">
									<strong><?= !empty($item->name) ? $item->name : l('create_biolink_market_modal.market_title.market.block') . ' '. ( $key + 1 ). ' | ID ' . $item->id  ?></strong>
								</a>

								<span class="d-flex align-items-center">
																			</span>

							</div>
						</div>	
						<div class="col-2 col-md-1"> 
								<div class="custom-control custom-switch">
									<input
											id="<?= 'item_enable_' . $key . '_' . $row->biolink_block_id ?>"
											name="<?= 'enable' . $key ?>" type="checkbox"
											class="custom-control-input"
											<?= $item->enable ? 'checked="checked"' : null ?>
									>
									<label class="custom-control-label" for="<?= 'item_enable_' . $key . '_' . $row->biolink_block_id ?>"></label>
								</div>
						</div>	
						<div class="col-3 col-md-2 d-flex justify-content-end">
							<a href="#" data-toggle="collapse" data-target="#<?= 'Product' . ( $key + 1 ). $row->biolink_block_id  ?>" aria-expanded="false" aria-controls="<?= 'Product' . ( $key + 1 ) . $row->biolink_block_id ?>" >
								<div class="link-btn-arrow-wrapper-setting2" >		
									<i class="fas fa-chevron-right "></i>
									<i class="fas fa-chevron-down "></i>
								</div> 
							</a>                                       
						</div>
						<div class="col-3 col-md-2 d-flex justify-content-end">
						    <button type="button" data-remove="item" class="btn btn-block btn-outline-danger" style="display:inline-block; border: none;"><i class="fas fa-fw fa-times"></i></button>
						    </div>
					</div>	
				<?php else: ?>
					<div class="d-flex align-items-center "style="background: #5f5f5f26;   border-radius: 6px; padding: 10px;">	
						<div class="col-2 mr-2 p-0"> 
							<img src="<?= $item->image ? UPLOADS_FULL_URL . 'block_images/' . $item->image : null ?>" class="img-fluid rounded <?= !empty($item->image) ? null : 'd-none' ?>" loading="lazy" />   
						</div>
						<div class="col-5 col-md-5"> 
							<div class="d-flex flex-column">
								<a href="#" data-toggle="collapse" data-target="#<?= 'Product' . ( $key + 1 ) . $row->biolink_block_id  ?>" aria-expanded="false" aria-controls="<?= 'Product' . ( $key + 1 )  ?>">
									<strong><?= !empty($item->name) ? $item->name : l('create_biolink_market_modal.market_title.market.block') . ' '. ( $key + 1 ) . ' | ID ' . $item->id ?></strong>
								</a>

								<span class="d-flex align-items-center">
																			</span>

							</div>
						</div>	
						<div class="col-2 col-md-1"> 
								<div class="custom-control custom-switch">
									<input
											id="<?= 'item_enable_' . $key . '_' . $row->biolink_block_id ?>"
											name="<?= 'enable' . $key ?>" type="checkbox"
											class="custom-control-input"
											<?= $item->enable ? 'checked="checked"' : null ?>
									>
									<label class="custom-control-label" for="<?= 'item_enable_' . $key . '_' . $row->biolink_block_id ?>"></label>
								</div>
						</div>	 
						<div class="col-3 col-md-2 d-flex justify-content-end">
							<a href="#" data-toggle="collapse" data-target="#<?= 'Product' . ( $key + 1 ) . $row->biolink_block_id  ?>" aria-expanded="false" aria-controls="<?= 'Product' . ( $key + 1 ) . $row->biolink_block_id  ?>" >									
								<div class="link-btn-arrow-wrapper-setting2" >		
									<i class="fas fa-chevron-right "></i>
									<i class="fas fa-chevron-down "></i>
								</div> 
							</a>                                       
						</div>	
						<div class="col-3 col-md-2 d-flex justify-content-end">
						    <button type="button" data-remove="item" class="btn btn-block btn-outline-danger" style="display:inline-block; border: none;"><i class="fas fa-fw fa-times"></i></button>
						    </div>
					</div>				
				<?php endif ?>	

			
		<!-- End block -->
		
				<div class="collapse" id="<?= 'Product' . ( $key + 1 ) . $row->biolink_block_id  ?>">	
            <div class="mb-2">
                
                    <div class="form-group">
                        <label for="<?= 'item_image_' . $key . '_' . $row->biolink_block_id ?>"><i class="fas fa-fw fa-image fa-sm text-muted mr-1"></i> <?= l('create_biolink_image_modal.image') ?></label>
                        <div class="<?= !empty($item->image) ? null : 'd-none' ?>">
                            <div class="row">
                                <div class="m-1 col-6 col-xl-3">
                                    <img src="<?= $item->image ? UPLOADS_FULL_URL . 'block_images/' . $item->image : null ?>" class="img-fluid rounded <?= !empty($item->image) ? null : 'd-none' ?>" loading="lazy" />
                                </div>
                            </div>
                        </div>
                        <input id="<?= 'item_image_' . $key . '_' . $row->biolink_block_id ?>" type="file" name="item_image_<?= $key ?>" accept=".gif, .png, .jpg, .jpeg, .svg" class="form-control-file" />
                        <input type="hidden" name="item_image_link[<?= $key ?>]" value="<?= $item->image ?>" />

                    </div>
                    
                    
                <div class="form-group">
                    <label for="<?= 'item_title_' . $key . '_' . $row->biolink_block_id ?>"><i class="fas fa-fw fa-signature fa-sm text-muted mr-1"></i> <?= l('create_biolink_tmmarket_modal.title') ?></label>
                    <input id="<?= 'item_title_' . $key . '_' . $row->biolink_block_id ?>" type="text" name="item_title[<?= $key ?>]" class="form-control" value="<?= $item->title ?>" required="required" />
                </div>
                
                <div class="form-group">
                    <label for="<?= 'item_description_' . $key . '_' . $row->biolink_block_id ?>"><i class="fas fa-fw fa-paragraph fa-sm text-muted mr-1"></i> <?= l('create_biolink_tmmarket_modal.description') ?></label>
                    <input id="<?= 'item_description' . $key . '_' . $row->biolink_block_id ?>" type="text" name="item_description[<?= $key ?>]" class="form-control" value="<?= $item->description ?>" required="required" />
                </div>
                
                
                <div class="form-group">
                    <label for="<?= 'item_cost_' . $key . '_' . $row->biolink_block_id ?>"><i class="fas fa-fw fa-credit-card fa-sm text-muted mr-1"></i> <?= l('create_biolink_tmmarket_modal.cost') ?></label>
                    <input id="<?= 'item_cost' . $key . '_' . $row->biolink_block_id ?>" type="text" name="item_cost[<?= $key ?>]" class="form-control" value="<?= $item->cost ?>" required="required" />
                </div>
                

                <button type="button" data-remove="item" class="btn btn-block btn-outline-danger"><i class="fas fa-fw fa-times"></i> <?= l('global.delete') ?></button>
            </div>
            </div></div>
        <?php endforeach ?>
        </div>
        <button data-add="tmmarket_item" data-biolink-block-id="<?= $row->biolink_block_id ?>" type="button" class="btn btn-sm btn-outline-success"><i class="fas fa-fw fa-plus-circle"></i> <?= l('global.create') ?></button>
        </div>



    <div class="mb-3">
        
    
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
