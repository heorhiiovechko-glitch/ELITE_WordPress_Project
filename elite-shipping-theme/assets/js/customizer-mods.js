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

  function sortedChoiceEntries(choices) {
    return Object.keys(choices || {})
      .map(function (key) {
        return {
          id: parseInt(key, 10) || 0,
          label: choices[key]
        };
      })
      .filter(function (item) {
        return item.id > 0;
      })
      .sort(function (a, b) {
        return a.label.localeCompare(b.label, undefined, { sensitivity: 'base', numeric: true });
      });
  }

  function buildSelectOptions(categoryId, choices) {
    var options = '<option value="0">— Select category —</option>';

    sortedChoiceEntries(choices).forEach(function (item) {
      var selected = item.id === categoryId ? ' selected' : '';
      options +=
        '<option value="' +
        item.id +
        '"' +
        selected +
        '>' +
        $('<div>').text(item.label).html() +
        '</option>';
    });

    return options;
  }

  function readRows($wrap) {
    var items = [];

    $wrap.find('.elite-mods-list-item').each(function () {
      var $row = $(this);
      var $select = $row.find('.elite-mods-list-item__select');
      var categoryId = parseInt($select.val(), 10) || 0;
      var title = categoryId > 0 ? $.trim($select.find('option:selected').text() || '') : '';

      items.push({
        category_id: categoryId,
        title: title,
        image: parseInt($row.find('.elite-mods-list-item__image-id').val(), 10) || 0
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

  function applyCategoryImage($row, categoryId, categoryImages) {
    var imageId = categoryId > 0 ? parseInt(categoryImages[categoryId], 10) || 0 : 0;
    var $button = $row.find('.elite-mods-select-image');

    $row.find('.elite-mods-list-item__image-id').val(String(imageId));
    $row.find('.elite-mods-list-item__category-id').val(String(categoryId));

    if (imageId > 0 && wp.media && wp.media.attachment) {
      var attachment = wp.media.attachment(imageId);
      attachment.fetch().then(function () {
        setImageButtonState($button, attachment.get('url') || '');
      }).catch(function () {
        setImageButtonState($button, '');
      });
      return;
    }

    setImageButtonState($button, '');
  }

  function buildRowHtml(index, categoryId, choices) {
    return (
      '<li class="elite-mods-list-item" data-index="' +
      index +
      '">' +
      '<span class="elite-mods-list-item__num">' +
      (index + 1) +
      '.</span>' +
      '<select class="elite-mods-list-item__select" aria-label="Category">' +
      buildSelectOptions(categoryId, choices) +
      '</select>' +
      '<input type="hidden" class="elite-mods-list-item__title" value="">' +
      '<input type="hidden" class="elite-mods-list-item__category-id" value="' +
      categoryId +
      '">' +
      '<input type="hidden" class="elite-mods-list-item__image-id" value="0">' +
      '<div class="elite-mods-list-item__actions">' +
      iconButton('elite-mods-select-image', 'format-image', 'Select image') +
      iconButton('elite-mods-remove', 'trash', 'Remove', 'elite-mods-icon-btn--remove') +
      '</div>' +
      '</li>'
    );
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

  function bindControl($wrap, choices, categoryImages) {
    if (!$wrap.length || $wrap.data('eliteModsBound')) {
      return;
    }

    $wrap.data('eliteModsBound', true);

    $wrap.on('click', '.elite-mods-add', function (event) {
      event.preventDefault();

      var $list = $wrap.find('.elite-mods-list-items');
      var index = $list.find('.elite-mods-list-item').length;

      $list.append(buildRowHtml(index, 0, choices));
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

    $wrap.on('change', '.elite-mods-list-item__select', function () {
      var $row = $(this).closest('.elite-mods-list-item');
      var categoryId = parseInt($(this).val(), 10) || 0;
      var title = categoryId > 0 ? $.trim($(this).find('option:selected').text() || '') : '';

      $row.find('.elite-mods-list-item__title').val(title);
      applyCategoryImage($row, categoryId, categoryImages);
      syncSetting($wrap);
    });

    $wrap.on('click', '.elite-mods-select-image', function (event) {
      event.preventDefault();
      openMediaPicker($(this).closest('.elite-mods-list-item'), $wrap);
    });
  }

  function initControl(control) {
    var $wrap = control.container.find('.elite-mods-list-control');
    var choices = control.params.choices || {};
    var categoryImages = control.params.categoryImages || {};

    bindControl($wrap, choices, categoryImages);
  }

  wp.customize.bind('ready', function () {
    wp.customize.control(SETTING_ID, function (control) {
      initControl(control);
    });
  });
})(jQuery);
