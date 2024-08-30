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
    <input type="hidden" name="block_type" value="tmnewsfeed" />
    <input type="hidden" name="biolink_block_id" value="<?= $row->biolink_block_id ?>" />

    <div class="notification-container"></div>
    
      <!-- begin setting -->
    <button class="btn btn-block btn-gray-300 my-4" type="button" data-toggle="collapse" data-target="#setting" aria-expanded="false" aria-controls="setting">
		
		<?= l('create_biolink_tmnewsfeed_modal.tmnewsfeed_settings') ?>
		<div class="link-btn-arrow-wrapper-setting" >		
		
		</div>
	</button>

	<div class="collapse" id="setting">	
	
		
 <div class="form-group">
                    <label for="<?= 'tmnewsfeed_title_block_' . $row->biolink_block_id ?>"><i class="fas fa-fw fa-signature fa-sm text-muted mr-1"></i> <?= l('create_biolink_tmnewsfeed_modal.block_title') ?></label>
                    <input id="<?= 'tmnewsfeed_title_block_' . $row->biolink_block_id ?>" type="text" name="title_block" class="form-control" maxlength="2048"  value="<?= $row->settings->title_block ?>" placeholder="<?= l('create_biolink_tmnewsfeed_modal.block_title_placeholder') ?>" />
                </div>
                
                                        <div class="form-group">
        <label for="<?= 'tmnewsfeed_icon_block_' . $row->biolink_block_id ?>"><i class="fas fa-fw fa-globe fa-sm text-muted mr-1"></i> <?= l('create_biolink_tmnewsfeed_modal.input.icon') ?></label>
        <input id="<?= 'tmnewsfeed_icon_block_' . $row->biolink_block_id ?>" type="text" name="icon_block" class="form-control" value="<?= $row->settings->icon_block ?>" placeholder="<?= l('create_biolink_link_modal.input.icon_placeholder') ?>" />
        <small class="form-text text-muted"><?= l('create_biolink_link_modal.input.icon_help') ?></small>
    </div>
    
                    <div class="form-group">
                <label for="<?= 'tmnewsfeed_text_color_' . $row->biolink_block_id ?>"><i class="fas fa-fw fa-paint-brush fa-sm text-muted mr-1"></i> <?= l('create_biolink_tmnewsfeed_modal.input.text_color') ?></label>
                <input id="<?= 'tmnewsfeed_text_color_' . $row->biolink_block_id ?>" type="hidden" name="text_color" class="form-control" value="<?= $row->settings->text_color ?>" required="required" />
                <div class="text_color_pickr"></div>
            </div>
        
                    <div class="form-group">
                <label for="<?= 'tmnewsfeed_background_color_' . $row->biolink_block_id ?>"><i class="fas fa-fw fa-fill fa-sm text-muted mr-1"></i> <?= l('create_biolink_tmnewsfeed_modal.input.background_color') ?></label>
                <input id="<?= 'tmnewsfeed_background_color_' . $row->biolink_block_id ?>" type="hidden" name="background_color" class="form-control" value="<?= $row->settings->background_color ?>" required="required" />
                <div class="background_color_pickr"></div>
            </div>

		
	</div>
	
	<!-- end setting -->
    
    <div id="<?= 'tmnewsfeed_items_' . $row->biolink_block_id ?>" data-biolink-block-id="<?= $row->biolink_block_id ?>">
        
        <?php foreach($row->settings->items as $key => $item): ?>
         <div class="mb-4" style="padding: 0.35rem;display:block"> 
         
		<!-- Start block -->
				<?php if($item->enable): ?>
					<div class="d-flex align-items-center "style="border-radius: 6px; padding: 10px; border: 2px solid #d3d3d35e;">	

						<div class="col-7"> 
							<div class="d-flex flex-column">
								<a href="#" data-toggle="collapse" data-target="#<?= 'News' . ( $key + 1 ) . $row->biolink_block_id ?>" aria-expanded="false" aria-controls="<?= 'News' . ( $key + 1 )  ?>">
									<strong><?= !empty($item->name) ? $item->name : l('create_biolink_tmnewsfeed_modal.tmnewsfeed_title.tmnewsfeed.block') . ' '. ( $key + 1 ) ?></strong>
								</a>

								<span class="d-flex align-items-center">
																			</span>

							</div>
						</div>	
						<div class="col-2"> 
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
						<!--<div class="col-3 col-md-2 d-flex justify-content-end">
							<a href="#" data-toggle="collapse" data-target="#<?= 'News' . ( $key + 1 ). $row->biolink_block_id  ?>" aria-expanded="false" aria-controls="<?= 'News' . ( $key + 1 ) . $row->biolink_block_id ?>" >
								<div class="link-btn-arrow-wrapper-setting2" >		
									<i class="fas fa-chevron-right "></i>
									<i class="fas fa-chevron-down "></i>
								</div> 
							</a>                                       
						</div> -->
						<div class="col-3 d-flex justify-content-end">
						    <button type="button" data-remove="item" class="btn btn-block btn-outline-danger" style="display:inline-block; border: none;"><i class="fas fa-fw fa-times"></i></button>
						    </div>
					</div>	
				<?php else: ?>
					<div class="d-flex align-items-center "style="background: #5f5f5f26;   border-radius: 6px; padding: 10px;">	
					
						<div class="col-7"> 
							<div class="d-flex flex-column">
								<a href="#" data-toggle="collapse" data-target="#<?= 'News' . ( $key + 1 ) . $row->biolink_block_id  ?>" aria-expanded="false" aria-controls="<?= 'News' . ( $key + 1 )  ?>">
									<strong><?= !empty($item->name) ? $item->name : l('create_biolink_tmnewsfeed_modal.tmnewsfeed_title.tmnewsfeed.block') . ' '. ( $key + 1 ) ?></strong>
								</a>

								<span class="d-flex align-items-center">
																			</span>

							</div>
						</div>	
						<div class="col-2"> 
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
						<!--<div class="col-3 col-md-2 d-flex justify-content-end">
							<a href="#" data-toggle="collapse" data-target="#<?= 'News' . ( $key + 1 ) . $row->biolink_block_id  ?>" aria-expanded="false" aria-controls="<?= 'News' . ( $key + 1 ) . $row->biolink_block_id  ?>" >									
								<div class="link-btn-arrow-wrapper-setting2" >		
									<i class="fas fa-chevron-right "></i>
									<i class="fas fa-chevron-down "></i>
								</div> 
							</a>                                       
						</div>-->
							<div class="col-3 d-flex justify-content-end">
						    <button type="button" data-remove="item" class="btn btn-block btn-outline-danger" style="display:inline-block; border: none;"><i class="fas fa-fw fa-times"></i></button>
						    </div>
					</div>				
				<?php endif ?>	

			
		<!-- End block -->
		
		<div class="collapse" id="<?= 'News' . ( $key + 1 ) . $row->biolink_block_id  ?>">	
            <div class="mb-2">
             
             <div class="form-group">
                    <label for="<?= 'item_title_' . $key . '_' . $row->biolink_block_id ?>"><i class="fas fa-fw fa-signature fa-sm text-muted mr-1"></i> <?= l('create_biolink_tmnewsfeed_modal.title') ?></label>
                    <input id="<?= 'item_title_' . $key . '_' . $row->biolink_block_id ?>" type="text" name="item_title[<?= $key ?>]" class="form-control" value="<?= $item->title ?>" required="required" placeholder="<?= l('create_biolink_tmnewsfeed_modal.title.placeholder') ?>"/>
                </div>
    
                <div class="form-group">
                    <label for="<?= 'item_content_' . $key . '_' . $row->biolink_block_id ?>"><i class="fas fa-fw fa-pen fa-sm text-muted mr-1"></i> <?= l('create_biolink_tmnewsfeed_modal.text') ?></label>
                    <input id="<?= 'item_content_' . $key . '_' . $row->biolink_block_id ?>" name="item_content[<?= $key ?>]" class="form-control" maxlength="2048" value="<?= $item->content ?>" placeholder="<?= l('create_biolink_tmnewsfeed_modal.text.placeholder') ?>"></input>
                </div>
                
                 <div class="form-group">
                    <label for="<?= 'item_date_' . $key . '_' . $row->biolink_block_id ?>"><i class="fas fa-fw fa-clock fa-sm text-muted mr-1"></i> <?= l('create_biolink_tmnewsfeed_modal.date') ?></label>
                     <input id="<?= 'item_date_' . $key . '_' . $row->biolink_block_id ?>" type="text" name="item_date[<?= $key ?>]" class="form-control" value="<?= $item->date ?>" required="required" placeholder="<?= l('create_biolink_tmnewsfeed_modal.date.placeholder') ?>" />
                </div>
                
                                 <div class="form-group">
                    <label for="<?= 'item_month_' . $key . '_' . $row->biolink_block_id ?>"><i class="fas fa-fw fa-clock fa-sm text-muted mr-1"></i> <?= l('create_biolink_tmnewsfeed_modal.month') ?></label>
                     <input id="<?= 'item_month_' . $key . '_' . $row->biolink_block_id ?>" type="text" name="item_month[<?= $key ?>]" class="form-control" value="<?= $item->month ?>" required="required" placeholder="<?= l('create_biolink_tmnewsfeed_modal.month.placeholder') ?>" />
                </div>
                
                                   <div class="form-group">
                    <label for="<?= 'item_topic_' . $key . '_' . $row->biolink_block_id ?>"><i class="fas fa-fw fa-folder-open fa-sm text-muted mr-1"></i> <?= l('create_biolink_tmnewsfeed_modal.topic') ?></label>
                     <input id="<?= 'item_topic_' . $key . '_' . $row->biolink_block_id ?>" type="text" name="item_topic[<?= $key ?>]" class="form-control" value="<?= $item->topic ?>" required="required" placeholder="<?= l('create_biolink_tmnewsfeed_modal.topic.placeholder') ?>"/>
                </div>
                
                
                                

    
         <button type="button" data-remove="item" class="btn btn-block btn-outline-danger"><i class="fas fa-fw fa-times"></i> <?= l('global.delete') ?></button>
            </div>
            </div>
			</div>
        <?php endforeach ?>
    </div>

    <div class="mb-3">
        
     
        <button data-add="tmnewsfeed_item" data-biolink-block-id="<?= $row->biolink_block_id ?>" type="button" class="btn btn-sm btn-outline-success"><i class="fas fa-fw fa-plus-circle"></i> <?= l('global.create') ?></button>
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
