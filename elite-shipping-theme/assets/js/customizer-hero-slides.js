(function ($) {
  'use strict';

  if (typeof wp === 'undefined' || typeof wp.customize !== 'function') {
    return;
  }

  var SETTING_ID = 'elite_hero_slides_list';

  function iconButton(className, icon, label, extraClass) {
    return (
      '<button type="button" class="elite-hero-slides-icon-btn ' +
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

    $wrap.find('.elite-hero-slides-list-item').each(function () {
      items.push({
        image: parseInt($(this).find('.elite-hero-slides-list-item__image-id').val(), 10) || 0
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
    $wrap.find('.elite-hero-slides-list-value').val(json);
  }

  function renumberRows($wrap) {
    $wrap.find('.elite-hero-slides-list-item').each(function (index) {
      $(this).attr('data-index', index);
      $(this)
        .find('.elite-hero-slides-list-item__num')
        .text('Image ' + String(index + 1));
    });
  }

  function toggleEmptyState($wrap) {
    var $list = $wrap.find('.elite-hero-slides-list-items');
    var hasRows = $list.find('.elite-hero-slides-list-item').length > 0;

    $list.find('.elite-hero-slides-list-items__empty').remove();

    if (!hasRows) {
      $list.append(
        '<li class="elite-hero-slides-list-items__empty">No backgrounds yet. Click + to add a slide image.</li>'
      );
    }
  }

  function buildRowHtml(index) {
    return (
      '<li class="elite-hero-slides-list-item" data-index="' +
      index +
      '">' +
      '<span class="elite-hero-slides-list-item__num">Image ' +
      (index + 1) +
      '</span>' +
      '<input type="hidden" class="elite-hero-slides-list-item__image-id" value="0">' +
      '<div class="elite-hero-slides-list-item__actions">' +
      iconButton('elite-hero-slides-select-image', 'format-image', 'Select image') +
      iconButton('elite-hero-slides-remove', 'trash', 'Remove', 'elite-hero-slides-icon-btn--remove') +
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
      var imageUrl =
        (attachment.sizes && attachment.sizes.thumbnail && attachment.sizes.thumbnail.url) ||
        attachment.url ||
        '';

      $row.find('.elite-hero-slides-list-item__image-id').val(String(imageId));
      setImageButtonState($row.find('.elite-hero-slides-select-image'), imageUrl);
      syncSetting($wrap);
    });

    frame.open();
  }

  function bindControl($wrap) {
    if (!$wrap.length || $wrap.data('eliteHeroSlidesBound')) {
      return;
    }

    $wrap.data('eliteHeroSlidesBound', true);

    $wrap.on('click', '.elite-hero-slides-add', function (event) {
      event.preventDefault();

      var $list = $wrap.find('.elite-hero-slides-list-items');
      var index = $list.find('.elite-hero-slides-list-item').length;

      $list.append(buildRowHtml(index));
      toggleEmptyState($wrap);
      syncSetting($wrap);
    });

    $wrap.on('click', '.elite-hero-slides-remove', function (event) {
      event.preventDefault();

      $(this).closest('.elite-hero-slides-list-item').remove();
      renumberRows($wrap);
      toggleEmptyState($wrap);
      syncSetting($wrap);
    });

    $wrap.on('click', '.elite-hero-slides-select-image', function (event) {
      event.preventDefault();
      openMediaPicker($(this).closest('.elite-hero-slides-list-item'), $wrap);
    });
  }

  wp.customize.bind('ready', function () {
    wp.customize.control(SETTING_ID, function (control) {
      var $wrap = control.container.find('.elite-hero-slides-list-control');
      bindControl($wrap);
    });
  });
})(jQuery);
