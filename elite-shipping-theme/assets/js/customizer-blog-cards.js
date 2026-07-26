(function ($) {
  'use strict';

  if (typeof wp === 'undefined' || typeof wp.customize !== 'function') {
    return;
  }

  var SETTING_ID = 'elite_blog_cards_list';

  function iconButton(className, icon, label, extraClass) {
    return (
      '<button type="button" class="elite-blog-cards-icon-btn ' +
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

  function blockLabel(type) {
    if (type === 'table') return 'Table';
    if (type === 'list') return 'List text';
    return 'Normal text';
  }

  function buildBlockHtml(type) {
    var label = blockLabel(type);
    return (
      '<div class="elite-blog-block" data-type="' +
      type +
      '">' +
      '<div class="elite-blog-block__head">' +
      '<span class="elite-blog-block__type">' +
      label +
      '</span>' +
      iconButton('elite-blog-remove-block', 'trash', 'Remove', 'elite-blog-cards-icon-btn--remove') +
      '</div>' +
      '<textarea class="elite-blog-block__content" rows="3" placeholder="' +
      label +
      '" aria-label="' +
      label +
      '"></textarea>' +
      '</div>'
    );
  }

  function buildSectionHtml() {
    return (
      '<div class="elite-blog-section">' +
      '<div class="elite-blog-section__top">' +
      '<input type="text" class="elite-blog-section__title" value="" placeholder="Small paragraph title" aria-label="Small paragraph title">' +
      '<div class="elite-blog-section__actions">' +
      iconButton('elite-blog-remove-section', 'trash', 'Remove', 'elite-blog-cards-icon-btn--remove') +
      iconButton('elite-blog-add-block-text', 'text', 'Add normal text') +
      iconButton('elite-blog-add-block-table', 'editor-table', 'Add table') +
      iconButton('elite-blog-add-block-list', 'editor-ul', 'Add list text') +
      '</div>' +
      '</div>' +
      '<div class="elite-blog-section__blocks"></div>' +
      '</div>'
    );
  }

  function buildParagraphHtml() {
    return (
      '<div class="elite-blog-paragraph">' +
      '<div class="elite-blog-paragraph__top">' +
      '<input type="text" class="elite-blog-paragraph__title" value="" placeholder="Paragraph title" aria-label="Paragraph title">' +
      '<div class="elite-blog-paragraph__actions">' +
      iconButton('elite-blog-remove-paragraph', 'trash', 'Remove', 'elite-blog-cards-icon-btn--remove') +
      iconButton('elite-blog-add-section', 'plus-alt', 'Add small paragraph') +
      '</div>' +
      '</div>' +
      '<div class="elite-blog-paragraph__sections"></div>' +
      '</div>'
    );
  }

  function buildFaqHtml() {
    return (
      '<div class="elite-blog-faq">' +
      '<div class="elite-blog-faq__top">' +
      '<input type="text" class="elite-blog-faq__title" value="" placeholder="FAQ title" aria-label="FAQ title">' +
      iconButton('elite-blog-remove-faq', 'trash', 'Remove FAQ', 'elite-blog-cards-icon-btn--remove') +
      '</div>' +
      '<textarea class="elite-blog-faq__text" rows="3" placeholder="FAQ text" aria-label="FAQ text"></textarea>' +
      '</div>'
    );
  }

  function readDetails($row) {
    var paragraphs = [];
    var faqs = [];

    $row.find('.elite-blog-paragraph').each(function () {
      var sections = [];
      $(this)
        .find('.elite-blog-section')
        .each(function () {
          var blocks = [];
          $(this)
            .find('.elite-blog-block')
            .each(function () {
              blocks.push({
                type: $(this).attr('data-type') || 'text',
                content: $.trim($(this).find('.elite-blog-block__content').val() || '')
              });
            });
          sections.push({
            title: $.trim($(this).find('.elite-blog-section__title').val() || ''),
            blocks: blocks
          });
        });
      paragraphs.push({
        title: $.trim($(this).find('.elite-blog-paragraph__title').val() || ''),
        sections: sections
      });
    });

    $row.find('.elite-blog-faq').each(function () {
      faqs.push({
        title: $.trim($(this).find('.elite-blog-faq__title').val() || ''),
        text: $.trim($(this).find('.elite-blog-faq__text').val() || '')
      });
    });

    return { paragraphs: paragraphs, faqs: faqs };
  }

  function readRows($wrap) {
    var items = [];

    $wrap.find('.elite-blog-cards-list-item').each(function () {
      items.push({
        title: $.trim($(this).find('.elite-blog-cards-list-item__title').val() || ''),
        date: $.trim($(this).find('.elite-blog-cards-list-item__date').val() || ''),
        image: parseInt($(this).find('.elite-blog-cards-list-item__image-id').val(), 10) || 0,
        intro: $.trim($(this).find('.elite-blog-cards-list-item__intro').val() || ''),
        details: readDetails($(this))
      });
    });

    return items;
  }

  function slugify(value) {
    return String(value || '')
      .toLowerCase()
      .replace(/[^a-z0-9]+/g, '-')
      .replace(/^-+|-+$/g, '');
  }

  function previewCardUrl($row) {
    if (!wp.customize.previewer || !wp.customize.settings || !wp.customize.settings.url) {
      return;
    }

    var title = $.trim($row.find('.elite-blog-cards-list-item__title').val() || '');
    var slug = slugify(title);
    if (!slug) {
      return;
    }

    var home = String(wp.customize.settings.url.home || '/').replace(/\/?$/, '/');
    var url = home + 'our-blog/?card=' + encodeURIComponent(slug);
    try {
      wp.customize.previewer.previewUrl.set(url);
    } catch (e) {
      // Previewer may not be ready yet.
    }
  }

  function syncSetting($wrap) {
    var setting = wp.customize(SETTING_ID);
    if (!setting) {
      return;
    }

    var items = readRows($wrap);
    var json = JSON.stringify(items);

    setting.set(json);
    $wrap.find('.elite-blog-cards-list-value').val(json);

    var $open = $wrap.find('.elite-blog-cards-list-item.is-details-open').first();
    if ($open.length) {
      previewCardUrl($open);
    }
  }

  function renumberRows($wrap) {
    $wrap.find('.elite-blog-cards-list-item').each(function (index) {
      $(this).attr('data-index', index);
      $(this)
        .find('.elite-blog-cards-list-item__num')
        .text('Card ' + String(index + 1));
    });
  }

  function toggleEmptyState($wrap) {
    var $list = $wrap.find('.elite-blog-cards-list-items');
    var hasRows = $list.find('.elite-blog-cards-list-item').length > 0;

    $list.find('.elite-blog-cards-list-items__empty').remove();

    if (!hasRows) {
      $list.append(
        '<li class="elite-blog-cards-list-items__empty">No cards yet. Click + to add a blog card.</li>'
      );
    }
  }

  function buildRowHtml(index) {
    var today = new Date();
    var yyyy = today.getFullYear();
    var mm = String(today.getMonth() + 1).padStart(2, '0');
    var dd = String(today.getDate()).padStart(2, '0');
    var dateValue = yyyy + '-' + mm + '-' + dd;

    return (
      '<li class="elite-blog-cards-list-item" data-index="' +
      index +
      '">' +
      '<div class="elite-blog-cards-list-item__top">' +
      '<span class="elite-blog-cards-list-item__num">Card ' +
      (index + 1) +
      '</span>' +
      '<div class="elite-blog-cards-list-item__actions">' +
      '<input type="date" class="elite-blog-cards-list-item__date" value="' +
      dateValue +
      '" aria-label="Date">' +
      '<input type="hidden" class="elite-blog-cards-list-item__image-id" value="0">' +
      iconButton('elite-blog-cards-select-image', 'format-image', 'Select image') +
      iconButton('elite-blog-cards-remove', 'trash', 'Remove', 'elite-blog-cards-icon-btn--remove') +
      '</div>' +
      '</div>' +
      '<input type="text" class="elite-blog-cards-list-item__title" value="" placeholder="Title" aria-label="Title">' +
      '<textarea class="elite-blog-cards-list-item__intro" rows="3" placeholder="Introduction" aria-label="Introduction"></textarea>' +
      '<div class="elite-blog-cards-list-item__details-bar">' +
      iconButton('elite-blog-cards-toggle-details', 'editor-paragraph', 'Details') +
      '<span class="elite-blog-cards-list-item__details-label">Details</span>' +
      '</div>' +
      '<div class="elite-blog-details-panel" hidden>' +
      '<div class="elite-blog-details-panel__head">' +
      '<span class="elite-blog-details-panel__title">Paragraphs</span>' +
      '<button type="button" class="button button-secondary elite-blog-add-paragraph">' +
      '<span class="dashicons dashicons-plus-alt2" aria-hidden="true"></span>' +
      '<span class="screen-reader-text">Add new paragraph</span>' +
      '</button>' +
      '</div>' +
      '<div class="elite-blog-paragraphs"></div>' +
      '<div class="elite-blog-details-panel__head elite-blog-details-panel__head--faqs">' +
      '<span class="elite-blog-details-panel__title">FAQs</span>' +
      '<button type="button" class="button button-secondary elite-blog-add-faq">' +
      '<span class="dashicons dashicons-plus-alt2" aria-hidden="true"></span>' +
      '<span class="screen-reader-text">Add new FAQs</span>' +
      '</button>' +
      '</div>' +
      '<div class="elite-blog-faqs"></div>' +
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

      $row.find('.elite-blog-cards-list-item__image-id').val(String(imageId));
      setImageButtonState($row.find('.elite-blog-cards-select-image'), imageUrl);
      syncSetting($wrap);
    });

    frame.open();
  }

  function bindControl($wrap) {
    if (!$wrap.length || $wrap.data('eliteBlogCardsBound')) {
      return;
    }

    $wrap.data('eliteBlogCardsBound', true);

    $wrap.on('click', '.elite-blog-cards-add', function (event) {
      event.preventDefault();
      var $list = $wrap.find('.elite-blog-cards-list-items');
      var index = $list.find('.elite-blog-cards-list-item').length;
      $list.append(buildRowHtml(index));
      toggleEmptyState($wrap);
      syncSetting($wrap);
    });

    $wrap.on('click', '.elite-blog-cards-remove', function (event) {
      event.preventDefault();
      $(this).closest('.elite-blog-cards-list-item').remove();
      renumberRows($wrap);
      toggleEmptyState($wrap);
      syncSetting($wrap);
    });

    $wrap.on('click', '.elite-blog-cards-select-image', function (event) {
      event.preventDefault();
      openMediaPicker($(this).closest('.elite-blog-cards-list-item'), $wrap);
    });

    $wrap.on('click', '.elite-blog-cards-toggle-details', function (event) {
      event.preventDefault();
      var $btn = $(this);
      var $row = $btn.closest('.elite-blog-cards-list-item');
      var $panel = $row.find('.elite-blog-details-panel');
      var isOpen = !$panel.prop('hidden');
      $panel.prop('hidden', isOpen);
      $btn.attr('aria-expanded', isOpen ? 'false' : 'true');
      $row.toggleClass('is-details-open', !isOpen);
      if (!isOpen) {
        previewCardUrl($row);
      }
    });

    $wrap.on('focus', '.elite-blog-cards-list-item__title, .elite-blog-cards-list-item__intro', function () {
      previewCardUrl($(this).closest('.elite-blog-cards-list-item'));
    });

    $wrap.on('click', '.elite-blog-add-paragraph', function (event) {
      event.preventDefault();
      $(this).closest('.elite-blog-details-panel').find('.elite-blog-paragraphs').append(buildParagraphHtml());
      syncSetting($wrap);
    });

    $wrap.on('click', '.elite-blog-remove-paragraph', function (event) {
      event.preventDefault();
      $(this).closest('.elite-blog-paragraph').remove();
      syncSetting($wrap);
    });

    $wrap.on('click', '.elite-blog-add-section', function (event) {
      event.preventDefault();
      $(this).closest('.elite-blog-paragraph').find('.elite-blog-paragraph__sections').first().append(buildSectionHtml());
      syncSetting($wrap);
    });

    $wrap.on('click', '.elite-blog-remove-section', function (event) {
      event.preventDefault();
      $(this).closest('.elite-blog-section').remove();
      syncSetting($wrap);
    });

    $wrap.on('click', '.elite-blog-add-block-text', function (event) {
      event.preventDefault();
      $(this).closest('.elite-blog-section').find('.elite-blog-section__blocks').first().append(buildBlockHtml('text'));
      syncSetting($wrap);
    });

    $wrap.on('click', '.elite-blog-add-block-table', function (event) {
      event.preventDefault();
      $(this).closest('.elite-blog-section').find('.elite-blog-section__blocks').first().append(buildBlockHtml('table'));
      syncSetting($wrap);
    });

    $wrap.on('click', '.elite-blog-add-block-list', function (event) {
      event.preventDefault();
      $(this).closest('.elite-blog-section').find('.elite-blog-section__blocks').first().append(buildBlockHtml('list'));
      syncSetting($wrap);
    });

    $wrap.on('click', '.elite-blog-remove-block', function (event) {
      event.preventDefault();
      $(this).closest('.elite-blog-block').remove();
      syncSetting($wrap);
    });

    $wrap.on('click', '.elite-blog-add-faq', function (event) {
      event.preventDefault();
      $(this).closest('.elite-blog-details-panel').find('.elite-blog-faqs').append(buildFaqHtml());
      syncSetting($wrap);
    });

    $wrap.on('click', '.elite-blog-remove-faq', function (event) {
      event.preventDefault();
      $(this).closest('.elite-blog-faq').remove();
      syncSetting($wrap);
    });

    $wrap.on(
      'input change',
      '.elite-blog-cards-list-item__title, .elite-blog-cards-list-item__date, .elite-blog-cards-list-item__intro, .elite-blog-paragraph__title, .elite-blog-section__title, .elite-blog-block__content, .elite-blog-faq__title, .elite-blog-faq__text',
      function () {
        syncSetting($wrap);
      }
    );
  }

  wp.customize.bind('ready', function () {
    wp.customize.control(SETTING_ID, function (control) {
      var $wrap = control.container.find('.elite-blog-cards-list-control');
      bindControl($wrap);
    });
  });
})(jQuery);
