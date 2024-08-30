<?php defined('ALTUMCODE') || die() ?>

<?php if(settings()->links->shortener_is_enabled): ?>
<div class="modal fade" id="create_link" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">

            <div class="modal-header">
                <h5 class="modal-title"><?= l('create_link_modal.header') ?></h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="<?= l('global.close') ?>">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <div class="modal-body">
                <form name="create_link" method="post" role="form">
                    <input type="hidden" name="token" value="<?= \Altum\Csrf::get() ?>" required="required" />
                    <input type="hidden" name="request_type" value="create" />
                    <input type="hidden" name="type" value="link" />

                    <div class="notification-container"></div>

                    <div class="form-group">
                        <label for="link_location_url"><i class="fas fa-fw fa-link fa-sm text-muted mr-1"></i> <?= l('create_link_modal.input.location_url') ?></label>
                        <input id="link_location_url" type="text" class="form-control" name="location_url" maxlength="2048" required="required" placeholder="<?= l('global.url_placeholder') ?>" />
                    </div>

                    <div class="form-group">
                        <label for="link_url"><i class="fas fa-fw fa-bolt fa-sm text-muted mr-1"></i> <?= l('create_link_modal.input.url') ?></label>
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <?php if(count($data->domains)): ?>
                                    <select name="domain_id" class="appearance-none custom-select form-control input-group-text">
                                        <?php if(settings()->links->main_domain_is_enabled || \Altum\Authentication::is_admin()): ?>
                                            <option value=""><?= remove_url_protocol_from_url(SITE_URL) ?></option>
                                        <?php endif ?>

                                        <?php foreach($data->domains as $row): ?>
                                        <option value="<?= $row->domain_id ?>"><?= remove_url_protocol_from_url($row->url) ?></option>
                                        <?php endforeach ?>
                                    </select>
                                <?php else: ?>
                                    <span class="input-group-text"><?= remove_url_protocol_from_url(SITE_URL) ?></span>
                                <?php endif ?>
                            </div>
                            <input
                                id="link_url"
                                type="text"
                                class="form-control"
                                name="url"
                                maxlength="256"
                                onchange="update_this_value(this, get_slug)"
                                onkeyup="update_this_value(this, get_slug)"
                                placeholder="<?= $this->user->plan_settings->custom_url ? l('global.url_slug_placeholder') : l('create_link_modal.input.url_placeholder') ?>"
                                <?= !$this->user->plan_settings->custom_url ? 'readonly="readonly"' : null ?>
                                <?= $this->user->plan_settings->custom_url ? null : 'data-toggle="tooltip" title="' . l('global.info_message.plan_feature_no_access') . '"' ?>
                            />
                        </div>
                        <small class="form-text text-muted"><?= l('create_link_modal.input.url_help') ?></small>
                    </div>

                    <div class="text-center mt-4">
                        <button type="submit" name="submit" class="btn btn-block btn-primary" data-is-ajax><?= l('create_link_modal.input.submit') ?></button>
                    </div>
                </form>
            </div>

        </div>
    </div>
</div>
<?php endif ?>

<?php if(settings()->links->biolinks_is_enabled): ?>
<div class="modal fade" id="create_biolink" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">

            <div class="modal-header">
                <h5 class="modal-title"><?= l('create_biolink_modal.header') ?></h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="<?= l('global.close') ?>">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <div class="modal-body">
                <form name="create_biolink" method="post" role="form">
                    <input type="hidden" name="token" value="<?= \Altum\Csrf::get() ?>" required="required" />
                    <input type="hidden" name="request_type" value="create" />
                    <input type="hidden" name="type" value="biolink" />
                    <input type="hidden" name="biolink_template_id" value="" />

                    <div class="notification-container"></div>

                    <div class="form-group">
                        <label for="biolink_url"><i class="fas fa-fw fa-bolt fa-sm text-muted mr-1"></i> <?= l('link.settings.url') ?></label>
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <?php if(count($data->domains)): ?>
                                    <select name="domain_id" class="appearance-none custom-select form-control input-group-text">
                                        <?php if(settings()->links->main_domain_is_enabled || \Altum\Authentication::is_admin()): ?>
                                            <option value=""><?= remove_url_protocol_from_url(SITE_URL) ?></option>
                                        <?php endif ?>

                                        <?php foreach($data->domains as $row): ?>
                                            <option value="<?= $row->domain_id ?>"><?= remove_url_protocol_from_url($row->url) ?></option>
                                        <?php endforeach ?>
                                    </select>
                                <?php else: ?>
                                    <span class="input-group-text"><?= remove_url_protocol_from_url(SITE_URL) ?></span>
                                <?php endif ?>
                            </div>
                            <input
                                id="biolink_url"
                                type="text"
                                class="form-control"
                                name="url"
                                maxlength="256"
                                onchange="update_this_value(this, get_slug)"
                                onkeyup="update_this_value(this, get_slug)"
                                placeholder="<?= $this->user->plan_settings->custom_url ? l('global.url_slug_placeholder') : l('link.settings.url_placeholder_random') ?>"
                                <?= !$this->user->plan_settings->custom_url ? 'readonly="readonly"' : null ?>
                                <?= $this->user->plan_settings->custom_url ? null : 'data-toggle="tooltip" title="' . l('global.info_message.plan_feature_no_access') . '"' ?>
                            />
                        </div>
                        <small class="form-text text-muted"><?= l('link.settings.url_help') ?></small>
                    </div>

					<!-- Create theme ------------------------------------------------------->

<?php
$datas = db()->where('user_id', 1)->get('links');
$count = 0;
foreach($datas as $row => $value) {
    $search[$row] = json_decode($value->settings, true);
    if ($search[$row]['theme_enable'] === true) {
    $themes[$count]['link_id'] = $value->link_id;
    $themes[$count]['theme_name'] = $search[$row]['theme_name'];
    $themes[$count]['url'] = $value->url;
    $themes[$count]['theme_image'] = $search[$row]['seo']['image'];
    $themes[$count]['theme_default'] = $search[$row]['theme_default'];
    $link_theme = $value->link_id;
    $count++;
    }
}

?>

<?php if($themes[0]['link_id']): ?>
<div class="form-group">
          <label for="theme"><i class="fas fa-fw fa-expand fa-sm text-muted mr-1"></i> <?= l('create_biolink_select_theme.name') ?></label>
                    <select id="theme" name="link_id" class="form-control" onchange="javascript:selectChanged();">
         <?php  $theme_default='';
         foreach($themes as $key) { 
         if ($key['theme_default'] === true) {
             $theme_default = $key['link_id'];
         }} ?>
            <option value="<?= $theme_default ?>">---</option>
        <?php foreach($themes as $key) { ?>             
            <option value="<?= $key['link_id'] ?>"><?= $key['theme_name'] ?></option>
            <?php } ?>
                    </select>
                </div>

<div class="block" style="visibility: hidden;"></div>
<?php if ($theme_default != '') { ?>
<div id="mydiv"><input type="hidden" name="request_type" value="duplicate_theme" /></div>
<?php } ?>

<?php if ($theme_default == '') { ?>
<div id="mydiv"><input type="hidden" name="request_type" value="create" /></div>
<?php } ?>

<style>
    .block {
  display: none;
  margin: 10px;
  padding: 10px;
  border: 2px solid #f1f2f4;
  border-radius: 0.5rem;
}
</style>

<?php foreach($themes as $key) { ?>
<div class="block text-center mt-4">
            <div data-image-container="image" class="<?= !empty($key['theme_image']) ? null : 'd-none' ?>">
            <div class="row">
                <div class="col-8 col-xl-8" style="margin: 0 auto; margin-bottom: 1.5rem;">
                    <img src="<?= $key['theme_image'] ? UPLOADS_FULL_URL . 'block_images/' . $key['theme_image'] : null ?>" class="img-fluid rounded <?= !empty($key['theme_image']) ? null : 'd-none' ?>" loading="lazy" />
                </div>
            </div>
        </div>
    <a class="btn btn-danger" href="<?= $key['url'] ?>" target="_blank" role="button"><?= l('create_biolink_select_theme.preview') ?></a></div>
<?php } ?>
 <?php endif ?> 
 
 <script>
    let select = document.getElementById('theme');
let block = document.querySelectorAll('.block');
let lastIndex = 0;

select.addEventListener('change', function() {
  block[lastIndex].style.display = "none"; 

  let index = select.selectedIndex;
  block[index].style.display = "block";

  lastIndex = index;
});
</script>

<?php if ($theme_default == '') { ?>
<script type="text/javascript">
         function selectChanged() {
            var sel = document.getElementById('theme');
            var str = sel.selectedIndex ? '<input type="hidden" name="request_type" value="duplicate_theme" />' : '<input type="hidden" name="request_type" value="create" />';
            document.getElementById('mydiv').innerHTML = str;
         }
</script>
<?php } ?>

<?php if ($theme_default != '') { ?>
<script type="text/javascript">
         function selectChanged() {
            var sel = document.getElementById('theme');
            var str = sel.selectedIndex ? '<input type="hidden" name="request_type" value="duplicate_theme" />' : '<input type="hidden" name="request_type" value="duplicate_theme" />';
            document.getElementById('mydiv').innerHTML = str;
         }
</script>
<?php } ?>		  

                    <div class="text-center mt-4">
                        <button type="submit" name="submit" class="btn btn-block btn-primary" data-is-ajax><?= l('create_biolink_modal.input.submit') ?></button>
                    </div>

 <?php if(!$themes[0]['link_id']): ?>                   
<input type="hidden" name="request_type" value="create" />
<?php endif ?>		  
                </form>
            </div>

        </div>
    </div>
</div>
<?php endif ?>

<?php if(settings()->links->files_is_enabled): ?>
<div class="modal fade" id="create_file" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">

            <div class="modal-header">
                <h5 class="modal-title"><?= l('create_file_modal.header') ?></h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="<?= l('global.close') ?>">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <div class="modal-body">
                <form name="create_file" method="post" role="form" enctype="multipart/form-data">
                    <input type="hidden" name="token" value="<?= \Altum\Csrf::get() ?>" required="required" />
                    <input type="hidden" name="request_type" value="create" />
                    <input type="hidden" name="type" value="file" />

                    <div class="notification-container"></div>

                    <div class="form-group">
                        <label for="file_file"><i class="fas fa-fw fa-sm fa-file text-muted mr-1"></i> <?= l('create_file_modal.input.file') ?></label>
                        <input id="file_file" type="file" name="file" accept="<?= \Altum\Uploads::get_whitelisted_file_extensions_accept('files') ?>" class="form-control-file altum-file-input" required="required" />
                        <small class="form-text text-muted"><?= sprintf(l('global.accessibility.whitelisted_file_extensions'), \Altum\Uploads::get_whitelisted_file_extensions_accept('files')) . ' ' . sprintf(l('global.accessibility.file_size_limit'), settings()->links->file_size_limit) ?></small>
                    </div>

                    <div class="form-group">
                        <label for="file_url"><i class="fas fa-fw fa-bolt fa-sm text-muted mr-1"></i> <?= l('create_link_modal.input.url') ?></label>
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <?php if(count($data->domains)): ?>
                                    <select name="domain_id" class="appearance-none custom-select form-control input-group-text">
                                        <?php if(settings()->links->main_domain_is_enabled || \Altum\Authentication::is_admin()): ?>
                                            <option value=""><?= remove_url_protocol_from_url(SITE_URL) ?></option>
                                        <?php endif ?>

                                        <?php foreach($data->domains as $row): ?>
                                            <option value="<?= $row->domain_id ?>"><?= remove_url_protocol_from_url($row->url) ?></option>
                                        <?php endforeach ?>
                                    </select>
                                <?php else: ?>
                                    <span class="input-group-text"><?= remove_url_protocol_from_url(SITE_URL) ?></span>
                                <?php endif ?>
                            </div>
                            <input
                                    id="file_url"
                                    type="text"
                                    class="form-control"
                                    name="url"
                                    maxlength="256"
                                    onchange="update_this_value(this, get_slug)"
                                    onkeyup="update_this_value(this, get_slug)"
                                    placeholder="<?= $this->user->plan_settings->custom_url ? l('global.url_slug_placeholder') : l('link.settings.url_placeholder_random') ?>"
                                <?= !$this->user->plan_settings->custom_url ? 'readonly="readonly"' : null ?>
                                <?= $this->user->plan_settings->custom_url ? null : 'data-toggle="tooltip" title="' . l('global.info_message.plan_feature_no_access') . '"' ?>
                            />
                        </div>
                        <small class="form-text text-muted"><?= l('link.settings.url_help') ?></small>
                    </div>

                    <div class="text-center mt-4">
                        <button type="submit" name="submit" class="btn btn-block btn-primary" data-is-ajax><?= l('create_file_modal.input.submit') ?></button>
                    </div>
                </form>
            </div>

        </div>
    </div>
</div>
<?php endif ?>

<?php if(settings()->links->vcards_is_enabled): ?>
<div class="modal fade" id="create_vcard" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">

            <div class="modal-header">
                <h5 class="modal-title"><?= l('create_vcard_modal.header') ?></h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="<?= l('global.close') ?>">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <div class="modal-body">
                <form name="create_file" method="post" role="form" enctype="multipart/form-data">
                    <input type="hidden" name="token" value="<?= \Altum\Csrf::get() ?>" required="required" />
                    <input type="hidden" name="request_type" value="create" />
                    <input type="hidden" name="type" value="vcard" />

                    <div class="notification-container"></div>

                    <div class="form-group">
                        <label for="vcard_url"><i class="fas fa-fw fa-bolt fa-sm text-muted mr-1"></i> <?= l('link.settings.url') ?></label>
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <?php if(count($data->domains)): ?>
                                    <select name="domain_id" class="appearance-none custom-select form-control input-group-text">
                                        <?php if(settings()->links->main_domain_is_enabled || \Altum\Authentication::is_admin()): ?>
                                            <option value=""><?= remove_url_protocol_from_url(SITE_URL) ?></option>
                                        <?php endif ?>

                                        <?php foreach($data->domains as $row): ?>
                                            <option value="<?= $row->domain_id ?>"><?= remove_url_protocol_from_url($row->url) ?></option>
                                        <?php endforeach ?>
                                    </select>
                                <?php else: ?>
                                    <span class="input-group-text"><?= remove_url_protocol_from_url(SITE_URL) ?></span>
                                <?php endif ?>
                            </div>
                            <input
                                    id="vcard_url"
                                    type="text"
                                    class="form-control"
                                    name="url"
                                    maxlength="256"
                                    onchange="update_this_value(this, get_slug)"
                                    onkeyup="update_this_value(this, get_slug)"
                                    placeholder="<?= $this->user->plan_settings->custom_url ? l('global.url_slug_placeholder') : l('link.settings.url_placeholder_random') ?>"
                                <?= !$this->user->plan_settings->custom_url ? 'readonly="readonly"' : null ?>
                                <?= $this->user->plan_settings->custom_url ? null : 'data-toggle="tooltip" title="' . l('global.info_message.plan_feature_no_access') . '"' ?>
                            />
                        </div>
                        <small class="form-text text-muted"><?= l('link.settings.url_help') ?></small>
                    </div>

                    <div class="text-center mt-4">
                        <button type="submit" name="submit" class="btn btn-block btn-primary" data-is-ajax><?= l('create_vcard_modal.input.submit') ?></button>
                    </div>
                </form>
            </div>

        </div>
    </div>
</div>
<?php endif ?>

<?php if(settings()->links->events_is_enabled): ?>
<div class="modal fade" id="create_event" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">

            <div class="modal-header">
                <h5 class="modal-title"><?= l('create_event_modal.header') ?></h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="<?= l('global.close') ?>">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <div class="modal-body">
                <form name="create_event" method="post" role="form" enctype="multipart/form-data">
                    <input type="hidden" name="token" value="<?= \Altum\Csrf::get() ?>" required="required" />
                    <input type="hidden" name="request_type" value="create" />
                    <input type="hidden" name="type" value="event" />

                    <div class="notification-container"></div>

                    <div class="form-group">
                        <label for="event_url"><i class="fas fa-fw fa-bolt fa-sm text-muted mr-1"></i> <?= l('link.settings.url') ?></label>
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <?php if(count($data->domains)): ?>
                                    <select name="domain_id" class="appearance-none custom-select form-control input-group-text">
                                        <?php if(settings()->links->main_domain_is_enabled || \Altum\Authentication::is_admin()): ?>
                                            <option value=""><?= remove_url_protocol_from_url(SITE_URL) ?></option>
                                        <?php endif ?>

                                        <?php foreach($data->domains as $row): ?>
                                            <option value="<?= $row->domain_id ?>"><?= remove_url_protocol_from_url($row->url) ?></option>
                                        <?php endforeach ?>
                                    </select>
                                <?php else: ?>
                                    <span class="input-group-text"><?= remove_url_protocol_from_url(SITE_URL) ?></span>
                                <?php endif ?>
                            </div>
                            <input
                                    id="event_url"
                                    type="text"
                                    class="form-control"
                                    name="url"
                                    maxlength="256"
                                    onchange="update_this_value(this, get_slug)"
                                    onkeyup="update_this_value(this, get_slug)"
                                    placeholder="<?= $this->user->plan_settings->custom_url ? l('global.url_slug_placeholder') : l('link.settings.url_placeholder_random') ?>"
                                <?= !$this->user->plan_settings->custom_url ? 'readonly="readonly"' : null ?>
                                <?= $this->user->plan_settings->custom_url ? null : 'data-toggle="tooltip" title="' . l('global.info_message.plan_feature_no_access') . '"' ?>
                            />
                        </div>
                        <small class="form-text text-muted"><?= l('link.settings.url_help') ?></small>
                    </div>

                    <div class="text-center mt-4">
                        <button type="submit" name="submit" class="btn btn-block btn-primary" data-is-ajax><?= l('create_event_modal.input.submit') ?></button>
                    </div>
                </form>
            </div>

        </div>
    </div>
</div>
<?php endif ?>

<?php if(settings()->links->static_is_enabled): ?>
<div class="modal fade" id="create_static" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">

                <div class="modal-header">
                    <h5 class="modal-title"><?= l('create_static_modal.header') ?></h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="<?= l('global.close') ?>">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>

                <div class="modal-body">
                    <form name="create_static" method="post" role="form" enctype="multipart/form-data">
                        <input type="hidden" name="token" value="<?= \Altum\Csrf::get() ?>" required="required" />
                        <input type="hidden" name="request_type" value="create" />
                        <input type="hidden" name="type" value="static" />

                        <div class="notification-container"></div>

                        <div class="form-group">
                            <label for="static_file"><i class="fa fa-fw fa-sm fa-file-zipper text-muted mr-1"></i> <?= l('create_static_modal.input.file') ?></label>
                            <input id="static_file" type="file" name="file" accept="<?= \Altum\Uploads::get_whitelisted_file_extensions_accept('static') ?>" class="form-control-file altum-file-input" required="required" />
                            <small class="form-text text-muted"><?= sprintf(l('global.accessibility.whitelisted_file_extensions'), \Altum\Uploads::get_whitelisted_file_extensions_accept('static')) . ' ' . sprintf(l('global.accessibility.file_size_limit'), settings()->links->static_size_limit) ?></small>
                            <small class="form-text text-muted"><?= sprintf(l('create_static_modal.input.file.inside_zip_whitelisted_file_extensions'), \Altum\Uploads::array_to_list_format(\Altum\Uploads::$uploads['static']['inside_zip_whitelisted_file_extensions'])) ?></small>
                            <small class="form-text text-muted"><?= l('create_static_modal.input.file.help1') ?></small>
                            <small class="form-text text-muted"><?= l('create_static_modal.input.file.help2') ?></small>
                        </div>

                        <div class="form-group">
                            <label for="static_url"><i class="fas fa-fw fa-bolt fa-sm text-muted mr-1"></i> <?= l('link.settings.url') ?></label>
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <?php if(count($data->domains)): ?>
                                        <select name="domain_id" class="appearance-none custom-select form-control input-group-text">
                                            <?php if(settings()->links->main_domain_is_enabled || \Altum\Authentication::is_admin()): ?>
                                                <option value=""><?= remove_url_protocol_from_url(SITE_URL) ?></option>
                                            <?php endif ?>

                                            <?php foreach($data->domains as $row): ?>
                                                <option value="<?= $row->domain_id ?>"><?= remove_url_protocol_from_url($row->url) ?></option>
                                            <?php endforeach ?>
                                        </select>
                                    <?php else: ?>
                                        <span class="input-group-text"><?= remove_url_protocol_from_url(SITE_URL) ?></span>
                                    <?php endif ?>
                                </div>
                                <input
                                        id="static_url"
                                        type="text"
                                        class="form-control"
                                        name="url"
                                        maxlength="256"
                                        onchange="update_this_value(this, get_slug)"
                                        onkeyup="update_this_value(this, get_slug)"
                                        placeholder="<?= $this->user->plan_settings->custom_url ? l('global.url_slug_placeholder') : l('link.settings.url_placeholder_random') ?>"
                                    <?= !$this->user->plan_settings->custom_url ? 'readonly="readonly"' : null ?>
                                    <?= $this->user->plan_settings->custom_url ? null : 'data-toggle="tooltip" title="' . l('global.info_message.plan_feature_no_access') . '"' ?>
                                />
                            </div>
                            <small class="form-text text-muted"><?= l('link.settings.url_help') ?></small>
                        </div>

                        <div class="text-center mt-4">
                            <button type="submit" name="submit" class="btn btn-block btn-primary" data-is-ajax><?= l('create_static_modal.input.submit') ?></button>
                        </div>
                    </form>
                </div>

            </div>
        </div>
    </div>
<?php endif ?>

<?php ob_start() ?>
<script>
    $('form[name="create_link"],form[name="create_biolink"],form[name="create_file"],form[name="create_vcard"],form[name="create_event"],form[name="create_static"]').on('submit', event => {
        let form = $(event.currentTarget)[0];
        let data = new FormData(form);

        let notification_container = event.currentTarget.querySelector('.notification-container');
        notification_container.innerHTML = '';
        pause_submit_button(event.currentTarget.querySelector('[type="submit"][name="submit"]'));

        $.ajax({
            type: 'POST',
            enctype: 'multipart/form-data',
            processData: false,
            contentType: false,
            cache: false,
            url: `${url}link-ajax`,
            data: data,
            dataType: 'json',
            success: (data) => {
                enable_submit_button(event.currentTarget.querySelector('[type="submit"][name="submit"]'));

                if(data.status == 'error') {
                    display_notifications(data.message, 'error', notification_container);
                }

                else if(data.status == 'success') {
                    display_notifications(data.message, 'success', notification_container);

                    setTimeout(() => {
                        $(event.currentTarget).modal('hide');
                        redirect(data.details.url, true);
                    }, 500);
                }
            },
            error: () => {
                enable_submit_button(event.currentTarget.querySelector('[type="submit"][name="submit"]'));
                display_notifications(<?= json_encode(l('global.error_message.basic')) ?>, 'error', notification_container);
            },
        });

        event.preventDefault();
    })
</script>
<?php \Altum\Event::add_content(ob_get_clean(), 'javascript') ?>
