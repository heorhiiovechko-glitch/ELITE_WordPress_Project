(function ($) {
  'use strict';

  if (typeof wp === 'undefined' || typeof wp.customize !== 'function') {
    return;
  }

  var SETTING_ID = 'elite_mods_display_list';

  function iconButton(className, icon, label, extraClass) {
    return (
      '<button type="button" class="elite-mods-icon-btn ' +
      className +
      (extraClass ? ' ' + extraClass : '') +
      '" aria-label="' +
      label +
      '">' +
      '<span class="dashicons dashicons-' +
      icon +
      '" aria-hidden="true"></span>' +
      '<span class="screen-reader-text">' +
      label +
      '</span>' +
      '</button>'
    );
  }

  function readRows($wrap) {
    var items = [];

    $wrap.find('.elite-mods-list-item').each(function () {
      items.push({
        title: $.trim($(this).find('.elite-mods-list-item__title').val() || ''),
        image: parseInt($(this).find('.elite-mods-list-item__image-id').val(), 10) || 0
      });
    });

    return items;
  }

  function syncSetting($wrap) {
    var setting = wp.customize(SETTING_ID);
    if (!setting) {
      return;
    }

    var items = readRows($wrap);
    var json = JSON.stringify(items);

    setting.set(json);
    $wrap.find('.elite-mods-list-value').val(json);
  }

  function renumberRows($wrap) {
    $wrap.find('.elite-mods-list-item').each(function (index) {
      $(this).attr('data-index', index);
      $(this).find('.elite-mods-list-item__num').text(String(index + 1) + '.');
    });
  }

  function toggleEmptyState($wrap) {
    var $list = $wrap.find('.elite-mods-list-items');
    var hasRows = $list.find('.elite-mods-list-item').length > 0;

    $list.find('.elite-mods-list-items__empty').remove();

    if (!hasRows) {
      $list.append(
        '<li class="elite-mods-list-items__empty">No cards in the display list yet. Click Add to create modification cards for the homepage.</li>'
      );
    }
  }

  function buildRowHtml(index) {
    return (
      '<li class="elite-mods-list-item" data-index="' +
      index +
      '">' +
      '<span class="elite-mods-list-item__num">' +
      (index + 1) +
      '.</span>' +
      '<input type="text" class="elite-mods-list-item__title" value="" placeholder="Card title" aria-label="Card title">' +
      '<input type="hidden" class="elite-mods-list-item__image-id" value="0">' +
      '<div class="elite-mods-list-item__actions">' +
      iconButton('elite-mods-select-image', 'format-image', 'Select image') +
      iconButton('elite-mods-remove', 'trash', 'Remove', 'elite-mods-icon-btn--remove') +
      '</div>' +
      '</li>'
    );
  }

  function setImageButtonState($button, imageUrl) {
    if (imageUrl) {
      $button
        .addClass('has-image')
        .css('background-image', 'url(' + imageUrl + ')')
        .attr('aria-label', 'Change image');
    } else {
      $button
        .removeClass('has-image')
        .css('background-image', '')
        .attr('aria-label', 'Select image');
    }
  }

  function openMediaPicker($row, $wrap) {
    var frame = wp.media({
      title: 'Select image',
      button: { text: 'Use image' },
      library: { type: 'image' },
      multiple: false
    });

    frame.on('select', function () {
      var attachment = frame.state().get('selection').first().toJSON();
      var imageId = parseInt(attachment.id, 10) || 0;
      var imageUrl = attachment.url || '';

      $row.find('.elite-mods-list-item__image-id').val(String(imageId));
      setImageButtonState($row.find('.elite-mods-select-image'), imageUrl);
      syncSetting($wrap);
    });

    frame.open();
  }

  function bindControl($wrap) {
    if (!$wrap.length || $wrap.data('eliteModsBound')) {
      return;
    }

    $wrap.data('eliteModsBound', true);

    $wrap.on('click', '.elite-mods-add', function (event) {
      event.preventDefault();

      var $list = $wrap.find('.elite-mods-list-items');
      var index = $list.find('.elite-mods-list-item').length;

      $list.append(buildRowHtml(index));
      toggleEmptyState($wrap);
      syncSetting($wrap);
    });

    $wrap.on('click', '.elite-mods-remove', function (event) {
      event.preventDefault();

      $(this).closest('.elite-mods-list-item').remove();
      renumberRows($wrap);
      toggleEmptyState($wrap);
      syncSetting($wrap);
    });

    $wrap.on('input change', '.elite-mods-list-item__title', function () {
      syncSetting($wrap);
    });

    $wrap.on('click', '.elite-mods-select-image', function (event) {
      event.preventDefault();
      openMediaPicker($(this).closest('.elite-mods-list-item'), $wrap);
    });
  }

  wp.customize.bind('ready', function () {
    wp.customize.control(SETTING_ID, function (control) {
      var $wrap = control.container.find('.elite-mods-list-control');
      bindControl($wrap);
    });
  });
})(jQuery);
