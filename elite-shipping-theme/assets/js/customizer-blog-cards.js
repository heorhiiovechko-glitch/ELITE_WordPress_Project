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
      '" title="' +
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

  function contentMeta(type) {
    if (type === 'table') {
      return { mark: '#', placeholder: 'Table content', markClass: 'table' };
    }
    if (type === 'list') {
      return { mark: '•', placeholder: 'List content', markClass: 'list' };
    }
    return { mark: '-', placeholder: 'Small paragraph content', markClass: 'text' };
  }

  function buildContentRowHtml(type) {
    var meta = contentMeta(type);
    var posFields =
      type === 'table'
        ? '<span class="elite-blog-section__pos">' +
          '<span class="elite-blog-spin" title="Row number (1 = header)">' +
          '<button type="button" class="elite-blog-spin__btn elite-blog-spin__up" data-spin="up" aria-label="Increase row">▲</button>' +
          '<input type="number" class="elite-blog-section__row" value="1" min="1" max="50" step="1" aria-label="Row number">' +
          '<button type="button" class="elite-blog-spin__btn elite-blog-spin__down" data-spin="down" aria-label="Decrease row">▼</button>' +
          '</span>' +
          '<span class="elite-blog-spin" title="Column number">' +
          '<button type="button" class="elite-blog-spin__btn elite-blog-spin__up" data-spin="up" aria-label="Increase column">▲</button>' +
          '<input type="number" class="elite-blog-section__col" value="1" min="1" max="20" step="1" aria-label="Column number">' +
          '<button type="button" class="elite-blog-spin__btn elite-blog-spin__down" data-spin="down" aria-label="Decrease column">▼</button>' +
          '</span>' +
          '</span>'
        : '';
    return (
      '<div class="elite-blog-content-row" data-type="' +
      type +
      '">' +
      '<span class="elite-blog-field-mark elite-blog-field-mark--' +
      meta.markClass +
      '">' +
      meta.mark +
      '</span>' +
      '<input type="text" class="elite-blog-section__content" value="" placeholder="' +
      meta.placeholder +
      '" aria-label="' +
      meta.placeholder +
      '">' +
      posFields +
      iconButton('elite-blog-remove-content', 'trash', 'Remove content', 'elite-blog-cards-icon-btn--remove') +
      '</div>'
    );
  }

  function buildTitleRowHtml(number) {
    return (
      '<div class="elite-blog-section__title-row">' +
      '<span class="elite-blog-field-mark elite-blog-field-mark--title">s' +
      String(number || 1) +
      '</span>' +
      '<input type="text" class="elite-blog-section__title" value="" placeholder="Small paragraph title" aria-label="Small paragraph title">' +
      iconButton('elite-blog-remove-section', 'trash', 'Remove small title', 'elite-blog-cards-icon-btn--remove') +
      '</div>'
    );
  }

  function renumberSmallTitles($paragraph) {
    $paragraph.find('.elite-blog-section__title-row .elite-blog-field-mark--title').each(function (index) {
      $(this).text('s' + String(index + 1));
    });
  }

  function buildParagraphToolbarHtml() {
    return (
      '<div class="elite-blog-paragraph__toolbar">' +
      iconButton('elite-blog-add-section', 'plus-alt', 'New small title') +
      '<button type="button" class="elite-blog-cards-icon-btn elite-blog-add-content" data-type="text" aria-label="Add text content" title="Add text content">' +
      '<span class="dashicons dashicons-text" aria-hidden="true"></span>' +
      '<span class="screen-reader-text">Add text content</span>' +
      '</button>' +
      '<button type="button" class="elite-blog-cards-icon-btn elite-blog-add-content" data-type="table" aria-label="Add table content" title="Add table content">' +
      '<span class="dashicons dashicons-editor-table" aria-hidden="true"></span>' +
      '<span class="screen-reader-text">Add table content</span>' +
      '</button>' +
      '<button type="button" class="elite-blog-cards-icon-btn elite-blog-add-content" data-type="list" aria-label="Add list content" title="Add list content">' +
      '<span class="dashicons dashicons-editor-ul" aria-hidden="true"></span>' +
      '<span class="screen-reader-text">Add list content</span>' +
      '</button>' +
      '</div>'
    );
  }

  function buildParagraphHtml(number) {
    return (
      '<div class="elite-blog-paragraph">' +
      '<div class="elite-blog-paragraph__top">' +
      '<span class="elite-blog-paragraph__num">Paragraph ' +
      String(number) +
      '</span>' +
      iconButton('elite-blog-remove-paragraph', 'trash', 'Remove', 'elite-blog-cards-icon-btn--remove') +
      '</div>' +
      '<input type="text" class="elite-blog-paragraph__title" value="" placeholder="Paragraph title" aria-label="Paragraph title">' +
      buildParagraphToolbarHtml() +
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
      var current = null;

      $(this)
        .find('.elite-blog-paragraph__sections')
        .first()
        .children('.elite-blog-section__title-row, .elite-blog-content-row')
        .each(function () {
          var $item = $(this);

          if ($item.hasClass('elite-blog-section__title-row')) {
            current = {
              title: $.trim($item.find('.elite-blog-section__title').val() || ''),
              hasTitle: true,
              blocks: []
            };
            sections.push(current);
            return;
          }

          if ($item.hasClass('elite-blog-content-row')) {
            var type = $item.attr('data-type') || 'text';
            if (type !== 'text' && type !== 'table' && type !== 'list') {
              type = 'text';
            }
            // Keep consecutive content rows in the same section so table
            // cells (row/col) can build one grid. Only start a section if none.
            if (!current) {
              current = { title: '', hasTitle: false, blocks: [] };
              sections.push(current);
            }
            var block = {
              type: type,
              content: $.trim($item.find('.elite-blog-section__content').val() || '')
            };
            if (type === 'table') {
              var row = parseInt($item.find('.elite-blog-section__row').val(), 10) || 1;
              var col = parseInt($item.find('.elite-blog-section__col').val(), 10) || 1;
              block.row = Math.max(1, Math.min(50, row));
              block.col = Math.max(1, Math.min(20, col));
            }
            current.blocks.push(block);
          }
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
        short_text: $.trim($(this).find('.elite-blog-cards-list-item__short-text').val() || ''),
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
  }

  function renumberRows($wrap) {
    $wrap.find('.elite-blog-cards-list-item').each(function (index) {
      $(this).attr('data-index', index);
      $(this)
        .find('.elite-blog-cards-list-item__num')
        .text('Card ' + String(index + 1));
    });
  }

  function renumberParagraphs($row) {
    $row.find('.elite-blog-paragraph').each(function (index) {
      $(this)
        .find('.elite-blog-paragraph__num')
        .first()
        .text('Paragraph ' + String(index + 1));
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
      '<input type="text" class="elite-blog-cards-list-item__short-text" value="" placeholder="Short text" aria-label="Short text">' +
      '<div class="elite-blog-paragraphs"></div>' +
      '<button type="button" class="button button-secondary elite-blog-add-paragraph">Add new paragraph</button>' +
      '<div class="elite-blog-faqs-block">' +
      '<div class="elite-blog-faqs-block__head">' +
      '<span class="elite-blog-faqs-block__title">FAQs</span>' +
      '<button type="button" class="button button-secondary elite-blog-add-faq">Add new FAQ</button>' +
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

    $wrap.on('focus', '.elite-blog-cards-list-item__title, .elite-blog-cards-list-item__intro, .elite-blog-paragraph__title', function () {
      previewCardUrl($(this).closest('.elite-blog-cards-list-item'));
    });

    $wrap.on('click', '.elite-blog-add-paragraph', function (event) {
      event.preventDefault();
      var $row = $(this).closest('.elite-blog-cards-list-item');
      var next = $row.find('.elite-blog-paragraph').length + 1;
      $row.find('.elite-blog-paragraphs').first().append(buildParagraphHtml(next));
      renumberParagraphs($row);
      previewCardUrl($row);
      syncSetting($wrap);
    });

    $wrap.on('click', '.elite-blog-remove-paragraph', function (event) {
      event.preventDefault();
      var $row = $(this).closest('.elite-blog-cards-list-item');
      $(this).closest('.elite-blog-paragraph').remove();
      renumberParagraphs($row);
      syncSetting($wrap);
    });

    $wrap.on('click', '.elite-blog-add-section', function (event) {
      event.preventDefault();
      var $paragraph = $(this).closest('.elite-blog-paragraph');
      var $sections = $paragraph.find('.elite-blog-paragraph__sections').first();
      var next = $sections.find('.elite-blog-section__title-row').length + 1;
      $sections.append(buildTitleRowHtml(next));
      renumberSmallTitles($paragraph);
      syncSetting($wrap);
    });

    $wrap.on('click', '.elite-blog-add-content', function (event) {
      event.preventDefault();
      var type = $(this).attr('data-type') || 'text';
      var $paragraph = $(this).closest('.elite-blog-paragraph');
      $paragraph.find('.elite-blog-paragraph__sections').first().append(buildContentRowHtml(type));
      syncSetting($wrap);
    });

    $wrap.on('click', '.elite-blog-remove-section', function (event) {
      event.preventDefault();
      var $paragraph = $(this).closest('.elite-blog-paragraph');
      $(this).closest('.elite-blog-section__title-row').remove();
      renumberSmallTitles($paragraph);
      syncSetting($wrap);
    });

    $wrap.on('click', '.elite-blog-remove-content', function (event) {
      event.preventDefault();
      $(this).closest('.elite-blog-content-row').remove();
      syncSetting($wrap);
    });

    $wrap.on('click', '.elite-blog-spin__btn', function (event) {
      event.preventDefault();
      var $input = $(this).closest('.elite-blog-spin').find('input').first();
      if (!$input.length) {
        return;
      }
      var min = parseInt($input.attr('min'), 10);
      var max = parseInt($input.attr('max'), 10);
      var value = parseInt($input.val(), 10) || 1;
      if (isNaN(min)) {
        min = 1;
      }
      if (isNaN(max)) {
        max = 99;
      }
      value = $(this).attr('data-spin') === 'up' ? Math.min(max, value + 1) : Math.max(min, value - 1);
      $input.val(String(value)).trigger('change');
    });

    $wrap.on('click', '.elite-blog-add-faq', function (event) {
      event.preventDefault();
      $(this).closest('.elite-blog-faqs-block').find('.elite-blog-faqs').append(buildFaqHtml());
      syncSetting($wrap);
    });

    $wrap.on('click', '.elite-blog-remove-faq', function (event) {
      event.preventDefault();
      $(this).closest('.elite-blog-faq').remove();
      syncSetting($wrap);
    });

    $wrap.on(
      'input change',
      '.elite-blog-cards-list-item__title, .elite-blog-cards-list-item__date, .elite-blog-cards-list-item__intro, .elite-blog-cards-list-item__short-text, .elite-blog-paragraph__title, .elite-blog-section__title, .elite-blog-section__content, .elite-blog-section__row, .elite-blog-section__col, .elite-blog-faq__title, .elite-blog-faq__text',
      function () {
        syncSetting($wrap);
      }
    );
  }

  wp.customize.bind('ready', function () {
    wp.customize.control(SETTING_ID, function (control) {
      var $wrap = control.container.find('.elite-blog-cards-list-control');
      bindControl($wrap);
      $wrap.find('.elite-blog-cards-list-item').each(function () {
        renumberParagraphs($(this));
        $(this).find('.elite-blog-paragraph').each(function () {
          renumberSmallTitles($(this));
        });
      });
    });
  });
})(jQuery);
