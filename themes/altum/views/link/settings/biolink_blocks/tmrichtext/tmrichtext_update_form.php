<?php defined('ALTUMCODE') || die() ?>

<style>

@import url('https://fonts.googleapis.com/css2?family=Amatic+SC&family=Arsenal&family=Bad+Script&family=Caveat&family=Comfortaa:wght@300&family=Cormorant&family=Cormorant+Infant:wght@300&family=Cuprum&family=Exo+2:wght@300&family=Forum&family=Lobster&family=Marck+Script&family=Merriweather:wght@300&family=Oswald:wght@300&family=PT+Sans&family=Philosopher&family=Play&family=Poiret+One&family=Roboto+Condensed&family=Roboto+Slab:wght@300&family=Vollkorn&family=Yanone+Kaffeesatz:wght@300&display=swap');

.ql-toolbar.ql-snow .ql-picker.ql-expanded .ql-picker-options {
    border-color: #ccc;
    height: 12rem;
    overflow-y: scroll;
}

.ql-snow .ql-picker.ql-font .ql-picker-label[data-value=roboto]::before,
.ql-snow .ql-picker.ql-font .ql-picker-item[data-value=roboto]::before {
  content: 'Roboto' !important;
    font-family: 'Roboto', sans-serif;
}
  
.ql-snow .ql-picker.ql-font .ql-picker-label[data-value=amatic-sc]::before,
.ql-snow .ql-picker.ql-font .ql-picker-item[data-value=amatic-sc]::before {
  content: 'Amatic SC' !important;
    font-family: 'Amatic SC', cursive;
}
  
.ql-snow .ql-picker.ql-font .ql-picker-label[data-value=arsenal]::before,
.ql-snow .ql-picker.ql-font .ql-picker-item[data-value=arsenal]::before {
  content: 'Arsenal' !important;
    font-family: 'Arsenal', sans-serif;
}
  
.ql-snow .ql-picker.ql-font .ql-picker-label[data-value=bad-script]::before,
.ql-snow .ql-picker.ql-font .ql-picker-item[data-value=bad-script]::before {
  content: 'Bad Script' !important;
  font-family: 'Bad Script', cursive;
}
  
.ql-snow .ql-picker.ql-font .ql-picker-label[data-value=caveat]::before,
.ql-snow .ql-picker.ql-font .ql-picker-item[data-value=caveat]::before {
  content: 'Caveat' !important;
    font-family: 'Caveat', cursive;
}
  
.ql-snow .ql-picker.ql-font .ql-picker-label[data-value=comfortaa]::before,
.ql-snow .ql-picker.ql-font .ql-picker-item[data-value=comfortaa]::before {
  content: 'Comfortaa' !important;
    font-family: 'Comfortaa', cursive;
}
  
.ql-snow .ql-picker.ql-font .ql-picker-label[data-value=cormorant]::before,
.ql-snow .ql-picker.ql-font .ql-picker-item[data-value=cormorant]::before {
  content: 'Cormorant' !important;
    font-family: 'Cormorant', serif;
}
  
.ql-snow .ql-picker.ql-font .ql-picker-label[data-value=cormorant-infant]::before,
.ql-snow .ql-picker.ql-font .ql-picker-item[data-value=cormorant-infant]::before {
  content: 'Cormorant Infant' !important;
    font-family: 'Cormorant Infant', serif;
}
  
.ql-snow .ql-picker.ql-font .ql-picker-label[data-value=cuprum]::before,
.ql-snow .ql-picker.ql-font .ql-picker-item[data-value=cuprum]::before {
  content: 'Cuprum' !important;
    font-family: 'Cuprum', sans-serif;
}
  
.ql-snow .ql-picker.ql-font .ql-picker-label[data-value=exo-2]::before,
.ql-snow .ql-picker.ql-font .ql-picker-item[data-value=exo-2]::before {
  content: 'Exo 2' !important;
    font-family: 'Exo 2', sans-serif;
}
  
.ql-snow .ql-picker.ql-font .ql-picker-label[data-value=forum]::before,
.ql-snow .ql-picker.ql-font .ql-picker-item[data-value=forum]::before {
  content: 'Forum' !important;
    font-family: 'Forum', cursive;
}
  
.ql-snow .ql-picker.ql-font .ql-picker-label[data-value=lobster]::before,
.ql-snow .ql-picker.ql-font .ql-picker-item[data-value=lobster]::before {
  content: 'Lobster' !important;
    font-family: 'Lobster', cursive;
}
  
.ql-snow .ql-picker.ql-font .ql-picker-label[data-value=marck-script]::before,
.ql-snow .ql-picker.ql-font .ql-picker-item[data-value=marck-script]::before {
  content: 'Marck Script' !important;
    font-family: 'Marck Script', cursive;
}
  
.ql-snow .ql-picker.ql-font .ql-picker-label[data-value=merriweather]::before,
.ql-snow .ql-picker.ql-font .ql-picker-item[data-value=merriweather]::before {
  content: 'Merriweather' !important;
    font-family: 'Merriweather', serif;
}
  
.ql-snow .ql-picker.ql-font .ql-picker-label[data-value=oswald]::before,
.ql-snow .ql-picker.ql-font .ql-picker-item[data-value=oswald]::before {
  content: 'Oswald' !important;
    font-family: 'Oswald', sans-serif;
}
  
.ql-snow .ql-picker.ql-font .ql-picker-label[data-value=philosopher]::before,
.ql-snow .ql-picker.ql-font .ql-picker-item[data-value=philosopher]::before {
  content: 'Philosopher' !important;
    font-family: 'Philosopher', sans-serif;
}
  
.ql-snow .ql-picker.ql-font .ql-picker-label[data-value=play]::before,
.ql-snow .ql-picker.ql-font .ql-picker-item[data-value=play]::before {
  content: 'Play' !important;
  font-family: 'Play', sans-serif;
}
  
.ql-snow .ql-picker.ql-font .ql-picker-label[data-value=poiret-one]::before,
.ql-snow .ql-picker.ql-font .ql-picker-item[data-value=poiret-one]::before {
  content: 'Poiret One' !important;
    font-family: 'Poiret One', cursive;
}
  
.ql-snow .ql-picker.ql-font .ql-picker-label[data-value=pt-sans]::before,
.ql-snow .ql-picker.ql-font .ql-picker-item[data-value=pt-sans]::before {
  content: 'PT Sans' !important;
    font-family: 'PT Sans', sans-serif;
}
  
.ql-snow .ql-picker.ql-font .ql-picker-label[data-value=roboto-condensed]::before,
.ql-snow .ql-picker.ql-font .ql-picker-item[data-value=roboto-condensed]::before {
  content: 'Roboto Condensed' !important;
    font-family: 'Roboto Condensed', sans-serif;
}
  
.ql-snow .ql-picker.ql-font .ql-picker-label[data-value=roboto-slab]::before,
.ql-snow .ql-picker.ql-font .ql-picker-item[data-value=roboto-slab]::before {
  content: 'Roboto Slab' !important;
    font-family: 'Roboto Slab', serif;
}
  
.ql-snow .ql-picker.ql-font .ql-picker-label[data-value=vollkorn]::before,
.ql-snow .ql-picker.ql-font .ql-picker-item[data-value=vollkorn]::before {
  content: 'Vollkorn' !important;
    font-family: 'Vollkorn', serif;
}
  
.ql-snow .ql-picker.ql-font .ql-picker-label[data-value=yanone-kaffeesatz]::before,
.ql-snow .ql-picker.ql-font .ql-picker-item[data-value=yanone-kaffeesatz]::before {
  content: 'Yanone Kaffeesatz !important;
  font-family: 'Yanone Kaffeesatz', sans-serif;}


</style>


<form id="form_<?= $row->biolink_block_id ?>" name="update_biolink_" method="post" role="form">
    <input type="hidden" name="token" value="<?= \Altum\Csrf::get() ?>" required="required" />
    <input type="hidden" name="request_type" value="update" />
    <input type="hidden" name="block_type" value="tmrichtext" />
    <input type="hidden" name="biolink_block_id" value="<?= $row->biolink_block_id ?>" />

    <div class="notification-container"></div>

    <div class="form-group">

     <input id="<?= 'text_' . $row->biolink_block_id ?>" name="text" type="hidden">

        <div id="<?= 'editor-container_' . $row->biolink_block_id ?>"><?= $row->settings->text ?></div>
     </div>
   
   

            <div class="form-group">
                <label><i class="fas fa-fw fa-paint-brush fa-sm text-muted mr-1"></i> <?= l('create_biolink_tmrichtext_modal.text_color') ?></label>
                <input type="hidden" name="text_color" class="form-control" value="<?= $row->settings->text_color ?>" required="required" />
                <div class="text_color_pickr"></div>
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

<link href="https://cdn.quilljs.com/1.0.0/quill.snow.css" rel="stylesheet" />
<script src="https://cdn.quilljs.com/1.0.0/quill.js"></script>
<script>
// Add fonts to whitelist
var Font = Quill.import('formats/font');
// We do not add Aref Ruqaa since it is the default
Font.whitelist = ['roboto', 'amatic-sc', 'arsenal', 'bad-script', 'caveat', 'comfortaa', 'cormorant', 'cormorant-infant', 'cuprum', 'exo-2', 'forum', 'lobster', 'marck-script', 'merriweather', 'oswald', 'philosopher', 'play', 'poiret-one', 'pt-sans', 'roboto-condensed', 'roboto-slab', 'vollkorn', 'yanone-kaffeesatz'];
Quill.register(Font, true);

var quill_<?= $row->biolink_block_id ?> = new Quill('#<?= 'editor-container_' . $row->biolink_block_id ?>', {
  modules: {
    toolbar: [
      [{ 'font': ['roboto', 'amatic-sc', 'arsenal', 'bad-script', 'caveat', 'comfortaa', 'cormorant', 'cormorant-infant', 'cuprum', 'exo-2', 'forum', 'lobster', 'marck-script', 'merriweather', 'oswald', 'philosopher', 'play', 'poiret-one', 'pt-sans', 'roboto-condensed', 'roboto-slab', 'vollkorn', 'yanone-kaffeesatz']  }],
      [{ 'header': [1, 2, 3, 4, 5, 6, false] }],
      [{ 'size': ['small', false, 'large', 'huge'] }],
      ['bold', 'italic', 'underline', 'strike', 'link'],
      [{ 'align': [] }, { 'list': 'ordered'}, { 'list': 'bullet' }, { 'indent': '-1'}, { 'indent': '+1' }],
      [{ 'color': [] }, { 'background': [] }],
      ['blockquote', 'code-block', 'video'],
      [{ 'script': 'sub'}, { 'script': 'super' }], 
      [{ 'direction': 'rtl' }],
      ['clean']  
    ]
  },
  placeholder: '<?= l('create_biolink_tmrichtext_modal.placeholder') ?>',
  theme: 'snow'  // or 'bubble'
});

</script>


<script>
    var form_<?= $row->biolink_block_id ?> = document.querySelector('form[id=form_<?= $row->biolink_block_id ?>]');
form_<?= $row->biolink_block_id ?>.onsubmit = function() {
  
 var text = document.querySelector('input[id=<?= 'text_' . $row->biolink_block_id ?>]');
  text.value = quill_<?= $row->biolink_block_id ?>.root.innerHTML.trim();
console.log(text)
  return false;
};
</script>
