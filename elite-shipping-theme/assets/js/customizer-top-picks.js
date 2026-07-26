(function ($) {
  'use strict';

  if (typeof wp === 'undefined' || typeof wp.customize !== 'function') {
    return;
  }

  var SETTING_ID = 'elite_top_picks_category_ids';

  function parseIds(raw) {
    if (Array.isArray(raw)) {
      return raw.map(function (id) {
        return parseInt(id, 10) || 0;
      });
    }

    if (typeof raw === 'string' && raw.length) {
      try {
        var parsed = JSON.parse(raw);
        if (Array.isArray(parsed)) {
          return parsed.map(function (id) {
            return parseInt(id, 10) || 0;
          });
        }
      } catch (error) {
        return [];
      }
    }

    return [];
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

  function readRows($wrap) {
    var ids = [];

    $wrap.find('.elite-top-picks-list-item').each(function () {
      var value = parseInt($(this).find('.elite-top-picks-list-item__select').val(), 10) || 0;
      ids.push(value);
    });

    return ids;
  }

  function syncSetting($wrap) {
    var setting = wp.customize(SETTING_ID);
    if (!setting) {
      return;
    }

    var ids = readRows($wrap);

    setting.set(JSON.stringify(ids));
    $wrap.find('.elite-top-picks-list-value').val(JSON.stringify(ids));
  }

  function renumberRows($wrap) {
    $wrap.find('.elite-top-picks-list-item').each(function (index) {
      $(this).attr('data-index', index);
      $(this).find('.elite-top-picks-list-item__num').text(String(index + 1) + '.');
    });
  }

  function toggleEmptyState($wrap) {
    var $list = $wrap.find('.elite-top-picks-list-items');
    var hasRows = $list.find('.elite-top-picks-list-item').length > 0;

    $list.find('.elite-top-picks-list-items__empty').remove();

    if (!hasRows) {
      $list.append(
        '<li class="elite-top-picks-list-items__empty">No categories in the display list yet. Click Add to choose categories for the homepage.</li>'
      );
    }
  }

  function buildRowHtml(index, termId, choices) {
    var options = '<option value="0">— Select category —</option>';

    sortedChoiceEntries(choices).forEach(function (item) {
      var selected = item.id === termId ? ' selected' : '';
      options +=
        '<option value="' +
        item.id +
        '"' +
        selected +
        '>' +
        $('<div>').text(item.label).html() +
        '</option>';
    });

    return (
      '<li class="elite-top-picks-list-item" data-index="' +
      index +
      '">' +
      '<span class="elite-top-picks-list-item__num">' +
      (index + 1) +
      '.</span>' +
      '<select class="elite-top-picks-list-item__select" aria-label="Category">' +
      options +
      '</select>' +
      '<button type="button" class="button-link elite-top-picks-remove">Remove</button>' +
      '</li>'
    );
  }

  function bindControl($wrap, choices) {
    if (!$wrap.length || $wrap.data('eliteTopPicksBound')) {
      return;
    }

    $wrap.data('eliteTopPicksBound', true);

    $wrap.on('click', '.elite-top-picks-add', function (event) {
      event.preventDefault();

      var $list = $wrap.find('.elite-top-picks-list-items');
      var index = $list.find('.elite-top-picks-list-item').length;

      $list.append(buildRowHtml(index, 0, choices));
      toggleEmptyState($wrap);
      syncSetting($wrap);
    });

    $wrap.on('click', '.elite-top-picks-remove', function (event) {
      event.preventDefault();

      $(this).closest('.elite-top-picks-list-item').remove();
      renumberRows($wrap);
      toggleEmptyState($wrap);
      syncSetting($wrap);
    });

    $wrap.on('change', '.elite-top-picks-list-item__select', function () {
      syncSetting($wrap);
    });
  }

  function initControl(control) {
    var $wrap = control.container.find('.elite-top-picks-list-control');
    var choices = control.params.choices || {};

    bindControl($wrap, choices);
  }

  wp.customize.bind('ready', function () {
    wp.customize.control(SETTING_ID, function (control) {
      initControl(control);
    });
  });
})(jQuery);
