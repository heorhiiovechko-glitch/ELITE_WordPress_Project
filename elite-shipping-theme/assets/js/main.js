(function () {
  function patchFlexsliderDirectionNav() {
    var $ = window.jQuery;
    if (!$ || !$.fn.flexslider || $.fn.flexslider.__eliteNoDirectionNav) {
      return !!($ && $.fn.flexslider && $.fn.flexslider.__eliteNoDirectionNav);
    }

    var original = $.fn.flexslider;
    $.fn.flexslider = function (options) {
      if (this.hasClass('woocommerce-product-gallery')) {
        if (typeof options === 'object' && options !== null) {
          options = $.extend({}, options, { directionNav: false });
        }
      }

      return original.apply(this, arguments);
    };
    $.fn.flexslider.__eliteNoDirectionNav = true;
    return true;
  }

  if (!patchFlexsliderDirectionNav()) {
    var patchAttempts = 0;
    var patchTimer = window.setInterval(function () {
      if (patchFlexsliderDirectionNav() || ++patchAttempts > 80) {
        window.clearInterval(patchTimer);
      }
    }, 50);
  }
})();

document.addEventListener('DOMContentLoaded', function () {
  var hero = document.querySelector('.apex-hero');
  if (hero) {
    var track = hero.querySelector('.apex-hero-bg-track');
    var slides = hero.querySelectorAll('.apex-hero-bg-slide');
    var prev = hero.querySelector('.apex-hero-slider-prev');
    var next = hero.querySelector('.apex-hero-slider-next');
    var dotsWrap = hero.querySelector('.apex-hero-slider-dots');
    var autoplayMs = 6000;
    var index = 0;
    var timer = null;

    if (track && slides.length) {
      function goTo(nextIndex) {
        index = (nextIndex + slides.length) % slides.length;
        track.style.transform = 'translateX(-' + (index * 100) + '%)';

        if (dotsWrap) {
          dotsWrap.querySelectorAll('button').forEach(function (dot, dotIndex) {
            var isActive = dotIndex === index;
            dot.classList.toggle('is-active', isActive);
            dot.setAttribute('aria-current', isActive ? 'true' : 'false');
          });
        }
      }

      function buildDots() {
        if (!dotsWrap) return;
        dotsWrap.innerHTML = '';

        slides.forEach(function (_, slideIndex) {
          var dot = document.createElement('button');
          dot.type = 'button';
          dot.setAttribute('data-index', String(slideIndex));
          dot.setAttribute('aria-label', 'Go to slide ' + (slideIndex + 1));
          if (slideIndex === 0) {
            dot.classList.add('is-active');
            dot.setAttribute('aria-current', 'true');
          }
          dot.addEventListener('click', function () {
            goTo(parseInt(dot.getAttribute('data-index'), 10));
            restartAutoplay();
          });
          dotsWrap.appendChild(dot);
        });
      }

      function stopAutoplay() {
        if (timer) {
          clearInterval(timer);
          timer = null;
        }
      }

      function restartAutoplay() {
        stopAutoplay();
        timer = setInterval(function () {
          goTo(index + 1);
        }, autoplayMs);
      }

      if (prev) {
        prev.addEventListener('click', function () {
          goTo(index - 1);
          restartAutoplay();
        });
      }

      if (next) {
        next.addEventListener('click', function () {
          goTo(index + 1);
          restartAutoplay();
        });
      }

      buildDots();
      goTo(0);
      restartAutoplay();

      hero.addEventListener('mouseenter', stopAutoplay);
      hero.addEventListener('mouseleave', restartAutoplay);
      hero.addEventListener('focusin', stopAutoplay);
      hero.addEventListener('focusout', restartAutoplay);
    }
  }

  function isModsMobile() {
    return window.innerWidth <= 768;
  }

  document.querySelectorAll('.apex-carousel-wrap').forEach(function (wrap) {
    var track = wrap.querySelector('.apex-mod-track') || wrap.querySelector('.apex-popular-track');
    var prev = wrap.querySelector('.apex-arrow-prev');
    var next = wrap.querySelector('.apex-arrow-next');
    var band = wrap.closest('.apex-mods-band');
    var dotsContainer = band ? band.querySelector('.apex-dots') : null;
    if (!track || !prev || !next) return;

    var isModTrack = track.classList.contains('apex-mod-track');
    var cardSelector = isModTrack ? '.apex-mod-card' : '.apex-product-card';

    function getCards() {
      return Array.prototype.slice.call(track.querySelectorAll(cardSelector));
    }

    function cardsPerPage() {
      if (isModTrack && isModsMobile()) {
        return 2;
      }
      if (isModTrack) {
        return getModsVisibleCount();
      }
      return 1;
    }

    function getModsVisibleCount() {
      var card = track.querySelector(cardSelector);
      if (!card) return 5;
      var gap = parseFloat(getComputedStyle(track).gap) || 14;
      var cardWidth = card.getBoundingClientRect().width + gap;
      if (cardWidth <= 0) return 5;
      return Math.max(1, Math.floor((track.clientWidth + gap) / cardWidth));
    }

    function pageCount() {
      var cards = getCards();
      return Math.max(1, Math.ceil(cards.length / cardsPerPage()));
    }

    function scrollStep() {
      var card = track.querySelector(cardSelector);
      if (!card) return 260;
      var gap = parseFloat(getComputedStyle(track).gap) || 16;
      var step = card.getBoundingClientRect().width + gap;
      if (isModTrack && isModsMobile()) {
        return step * 2;
      }
      if (isModTrack) {
        return step * getModsVisibleCount();
      }
      return step;
    }

    function getActiveCardIndex(cards) {
      var trackRect = track.getBoundingClientRect();
      var index = 0;
      var minDistance = Infinity;

      cards.forEach(function (card, i) {
        var distance = Math.abs(card.getBoundingClientRect().left - trackRect.left);
        if (distance < minDistance) {
          minDistance = distance;
          index = i;
        }
      });

      return index;
    }

    function getCurrentPage() {
      return Math.floor(getActiveCardIndex(getCards()) / cardsPerPage());
    }

    function updateDots() {
      if (!dotsContainer || !isModTrack || !isModsMobile()) return;
      var page = getCurrentPage();
      dotsContainer.querySelectorAll('button').forEach(function (dot, i) {
        dot.classList.toggle('on', i === page);
      });
    }

    function buildDots() {
      if (!dotsContainer || !isModTrack) return;

      dotsContainer.innerHTML = '';

      if (!isModsMobile()) {
        dotsContainer.setAttribute('aria-hidden', 'true');
        return;
      }

      var pages = pageCount();
      dotsContainer.removeAttribute('aria-hidden');
      dotsContainer.setAttribute('role', 'tablist');
      dotsContainer.setAttribute('aria-label', 'Carousel pages');

      for (var i = 0; i < pages; i++) {
        var dot = document.createElement('button');
        dot.type = 'button';
        dot.className = i === 0 ? 'on' : '';
        dot.setAttribute('aria-label', 'Go to page ' + (i + 1));
        dot.dataset.page = String(i);
        dotsContainer.appendChild(dot);
      }

      dotsContainer.querySelectorAll('button').forEach(function (dot) {
        dot.addEventListener('click', function () {
          scrollToPage(parseInt(dot.dataset.page, 10));
        });
      });
    }

    function scrollToPage(page) {
      var cards = getCards();
      if (!cards.length) return;
      var index = Math.min(page * cardsPerPage(), cards.length - 1);
      cards[index].scrollIntoView({
        behavior: 'smooth',
        inline: 'start',
        block: 'nearest'
      });
    }

    function scrollCarousel(direction) {
      var cards = getCards();
      if (!cards.length) return;

      if (isModTrack && isModsMobile()) {
        var nextPage = getCurrentPage() + direction;
        nextPage = Math.max(0, Math.min(pageCount() - 1, nextPage));
        scrollToPage(nextPage);
        return;
      }

      if (isModTrack) {
        var cards = getCards();
        if (!cards.length) return;
        var visible = getModsVisibleCount();
        var currentIndex = getActiveCardIndex(cards);
        var nextIndex = currentIndex + direction * visible;
        nextIndex = Math.max(0, Math.min(cards.length - 1, nextIndex));
        cards[nextIndex].scrollIntoView({
          behavior: 'smooth',
          inline: 'start',
          block: 'nearest'
        });
        return;
      }

      track.scrollBy({ left: direction * scrollStep(), behavior: 'smooth' });
    }

    prev.addEventListener('click', function () {
      scrollCarousel(-1);
    });
    next.addEventListener('click', function () {
      scrollCarousel(1);
    });

    track.addEventListener('scroll', function () {
      window.requestAnimationFrame(updateDots);
    });

    window.addEventListener('resize', function () {
      buildDots();
      updateDots();
    });

    buildDots();
    updateDots();
  });

  var navDropdowns = document.querySelectorAll('.elite-nav-dropdown');

  function closeNavDropdown(dropdown) {
    if (!dropdown) return;
    dropdown.classList.remove('is-open');
    var toggle = dropdown.querySelector('.elite-nav-dropdown-toggle');
    if (toggle) {
      toggle.setAttribute('aria-expanded', 'false');
    }
  }

  function closeAllNavDropdowns() {
    navDropdowns.forEach(closeNavDropdown);
  }

  navDropdowns.forEach(function (dropdown) {
    var toggle = dropdown.querySelector('.elite-nav-dropdown-toggle');
    var menu = dropdown.querySelector('.elite-nav-dropdown-menu');
    if (!toggle || !menu) return;

    toggle.addEventListener('click', function (event) {
      event.preventDefault();
      event.stopPropagation();
      var isOpen = dropdown.classList.contains('is-open');
      closeAllNavDropdowns();
      if (!isOpen) {
        dropdown.classList.add('is-open');
        toggle.setAttribute('aria-expanded', 'true');
      }
    });

    menu.querySelectorAll('a').forEach(function (link) {
      link.addEventListener('click', function () {
        closeNavDropdown(dropdown);
      });
    });
  });

  document.addEventListener('click', function (event) {
    navDropdowns.forEach(function (dropdown) {
      if (!dropdown.contains(event.target)) {
        closeNavDropdown(dropdown);
      }
    });
  });

  document.addEventListener('keydown', function (event) {
    if (event.key === 'Escape') {
      closeAllNavDropdowns();
    }
  });

  var menuToggle = document.querySelector('.elite-menu-toggle');
  var mobileNav = document.getElementById('elite-mobile-nav');
  var mobileOverlay = document.getElementById('elite-mobile-nav-overlay');
  var mobileClose = mobileNav ? mobileNav.querySelector('.elite-mobile-nav-close') : null;
  var mobileTabs = mobileNav ? mobileNav.querySelectorAll('.elite-mobile-nav-tab') : [];
  var mobilePanels = mobileNav ? mobileNav.querySelectorAll('.elite-mobile-nav-panel') : [];
  var resetMobileLiveSearch = function () {};

  function setMobileNavTab(tabName) {
    mobileTabs.forEach(function (tab) {
      var isActive = tab.getAttribute('data-tab') === tabName;
      tab.classList.toggle('is-active', isActive);
      tab.setAttribute('aria-selected', isActive ? 'true' : 'false');
    });

    mobilePanels.forEach(function (panel) {
      var isActive = panel.getAttribute('data-panel') === tabName;
      panel.classList.toggle('is-active', isActive);
      panel.hidden = !isActive;
    });
  }

  function closeMobileNav() {
    if (!menuToggle || !mobileNav) return;
    resetMobileLiveSearch();
    document.body.classList.remove('elite-nav-open');
    menuToggle.setAttribute('aria-expanded', 'false');
    menuToggle.setAttribute('aria-label', 'Open menu');
    mobileNav.hidden = true;
    if (mobileOverlay) {
      mobileOverlay.hidden = true;
      mobileOverlay.setAttribute('aria-hidden', 'true');
    }
  }

  function openMobileNav() {
    if (!menuToggle || !mobileNav) return;
    menuToggle.setAttribute('aria-expanded', 'true');
    menuToggle.setAttribute('aria-label', 'Close menu');
    mobileNav.hidden = false;
    if (mobileOverlay) {
      mobileOverlay.hidden = false;
      mobileOverlay.setAttribute('aria-hidden', 'false');
    }
    setMobileNavTab('menu');
    requestAnimationFrame(function () {
      document.body.classList.add('elite-nav-open');
    });
  }

  if (menuToggle && mobileNav) {
    menuToggle.addEventListener('click', function () {
      if (document.body.classList.contains('elite-nav-open')) {
        closeMobileNav();
      } else {
        openMobileNav();
      }
    });

    if (mobileClose) {
      mobileClose.addEventListener('click', closeMobileNav);
    }

    if (mobileOverlay) {
      mobileOverlay.addEventListener('click', closeMobileNav);
    }

    mobileTabs.forEach(function (tab) {
      tab.addEventListener('click', function () {
        setMobileNavTab(tab.getAttribute('data-tab'));
      });
    });

    mobileNav.querySelectorAll('.elite-mobile-nav-group-toggle').forEach(function (toggle) {
      toggle.addEventListener('click', function () {
        var submenu = toggle.nextElementSibling;
        var expanded = toggle.getAttribute('aria-expanded') === 'true';
        toggle.setAttribute('aria-expanded', expanded ? 'false' : 'true');
        if (submenu) {
          submenu.hidden = expanded;
        }
      });
    });

    mobileNav.querySelectorAll('.elite-mobile-nav-panel a').forEach(function (link) {
      link.addEventListener('click', closeMobileNav);
    });

    document.addEventListener('keydown', function (event) {
      if (event.key === 'Escape') {
        closeMobileNav();
      }
    });

    window.addEventListener('resize', function () {
      if (window.innerWidth > 1024) {
        closeMobileNav();
      }
    });

    initLiveSearch({
      root: mobileNav,
      onActive: function (active) {
        mobileNav.classList.toggle('elite-mobile-nav--searching', active);
      },
      onResultClick: function () {
        closeMobileNav();
      },
      statusNode: mobileNav.querySelector('.elite-mobile-nav-live-search-status')
    }, function (resetFn) {
      resetMobileLiveSearch = resetFn;
    });
  }

  function initLiveSearch(options, setResetFn) {
    var config = window.eliteShippingLiveSearch || {};
    var root = options && options.root;
    if (!root || !config.ajaxUrl) {
      return;
    }

    var searchInput = root.querySelector('.elite-live-search-input');
    var searchClear = root.querySelector('.elite-live-search-clear');
    var searchForm = root.querySelector('form');
    var resultsWrap = root.querySelector('.elite-live-search-results');
    var resultsList = root.querySelector('.elite-live-search-list');
    var resultsStatus = options.statusNode || null;
    var minChars = parseInt(config.minChars, 10) || 2;
    var debounceTimer = null;
    var requestId = 0;
    var activeController = null;

    if (!searchInput || !resultsWrap || !resultsList) {
      return;
    }

    function escapeHtml(value) {
      return String(value || '')
        .replace(/&/g, '&amp;')
        .replace(/</g, '&lt;')
        .replace(/>/g, '&gt;')
        .replace(/"/g, '&quot;')
        .replace(/'/g, '&#39;');
    }

    function escapeRegExp(value) {
      return String(value || '').replace(/[.*+?^${}()|[\]\\]/g, '\\$&');
    }

    function highlightTitle(title, term) {
      var safeTitle = escapeHtml(title);
      var trimmed = String(term || '').trim();
      if (!trimmed) {
        return safeTitle;
      }

      return safeTitle.replace(new RegExp(escapeRegExp(trimmed), 'ig'), function (match) {
        return '<span class="elite-live-search-match">' + match + '</span>';
      });
    }

    function setSearching(active) {
      if (typeof options.onActive === 'function') {
        options.onActive(active);
      }
      searchInput.setAttribute('aria-expanded', active ? 'true' : 'false');
      resultsWrap.hidden = !active;
    }

    function resetLiveSearch() {
      if (activeController) {
        activeController.abort();
        activeController = null;
      }
      searchInput.value = '';
      if (searchClear) {
        searchClear.hidden = true;
      }
      resultsList.innerHTML = '';
      if (resultsStatus) {
        resultsStatus.textContent = '';
      }
      setSearching(false);
    }

    function renderResults(items, term) {
      if (!items.length) {
        resultsList.innerHTML = '<li class="elite-live-search-empty">No products found for &ldquo;' + escapeHtml(term) + '&rdquo;.</li>';
        if (resultsStatus) {
          resultsStatus.textContent = 'No products found.';
        }
        return;
      }

      resultsList.innerHTML = items.map(function (item) {
        return (
          '<li class="elite-live-search-item" role="option">' +
            '<a class="elite-live-search-link" href="' + escapeHtml(item.url) + '">' +
              '<span class="elite-live-search-media">' +
                (item.image ? '<img src="' + escapeHtml(item.image) + '" alt="" loading="lazy">' : '') +
              '</span>' +
              '<span class="elite-live-search-copy">' +
                '<span class="elite-live-search-title">' + highlightTitle(item.title, term) + '</span>' +
                '<span class="elite-live-search-price">' + (item.price_html || '') + '</span>' +
              '</span>' +
            '</a>' +
          '</li>'
        );
      }).join('');

      if (resultsStatus) {
        resultsStatus.textContent = items.length + ' product' + (items.length === 1 ? '' : 's') + ' found.';
      }
    }

    function fetchResults(term) {
      var currentRequest = ++requestId;

      if (activeController) {
        activeController.abort();
      }

      activeController = new AbortController();
      resultsList.innerHTML = '<li class="elite-live-search-loading">Searching…</li>';

      var url = new URL(config.ajaxUrl, window.location.origin);
      url.searchParams.set('action', config.action || 'elite_live_product_search');
      url.searchParams.set('nonce', config.nonce || '');
      url.searchParams.set('term', term);

      fetch(url.toString(), {
        method: 'GET',
        credentials: 'same-origin',
        signal: activeController.signal
      })
        .then(function (response) {
          return response.json();
        })
        .then(function (payload) {
          if (currentRequest !== requestId) {
            return;
          }

          var items = payload && payload.success && payload.data ? payload.data.items || [] : [];
          renderResults(items, term);
        })
        .catch(function (error) {
          if (error && error.name === 'AbortError') {
            return;
          }
          resultsList.innerHTML = '<li class="elite-live-search-empty">Search unavailable. Please try again.</li>';
        })
        .finally(function () {
          if (currentRequest === requestId) {
            activeController = null;
          }
        });
    }

    function handleInput() {
      var term = searchInput.value.trim();

      if (searchClear) {
        searchClear.hidden = term.length === 0;
      }

      if (term.length < minChars) {
        if (activeController) {
          activeController.abort();
          activeController = null;
        }
        resultsList.innerHTML = '';
        setSearching(false);
        return;
      }

      setSearching(true);
      resultsList.innerHTML = '<li class="elite-live-search-loading">Searching…</li>';
      clearTimeout(debounceTimer);
      debounceTimer = setTimeout(function () {
        fetchResults(term);
      }, 100);
    }

    searchInput.addEventListener('input', handleInput);

    searchInput.addEventListener('focus', function () {
      root.classList.add('is-focused');
      if (searchInput.value.trim().length >= minChars) {
        setSearching(true);
      }
    });

    searchInput.addEventListener('blur', function () {
      window.setTimeout(function () {
        if (!root.contains(document.activeElement)) {
          root.classList.remove('is-focused');
        }
      }, 120);
    });

    if (searchClear) {
      searchClear.addEventListener('click', function () {
        resetLiveSearch();
        searchInput.focus();
      });
    }

    if (searchForm) {
      searchForm.addEventListener('submit', function (event) {
        var term = searchInput.value.trim();
        if (term.length >= minChars) {
          return;
        }
        event.preventDefault();
      });
    }

    resultsList.addEventListener('click', function (event) {
      if (event.target.closest('a') && typeof options.onResultClick === 'function') {
        options.onResultClick();
      }
    });

    if (typeof setResetFn === 'function') {
      setResetFn(resetLiveSearch);
    }
  }

  var headerSearch = document.getElementById('elite-header-search');
  if (headerSearch) {
    var headerTrigger = headerSearch.querySelector('.elite-header-search-trigger');
    var headerInput = headerSearch.querySelector('.elite-live-search-input');

    if (headerTrigger && headerInput) {
      headerTrigger.addEventListener('click', function () {
        headerSearch.classList.add('is-focused');
        headerTrigger.setAttribute('aria-expanded', 'true');
        headerInput.focus();
      });

      headerInput.addEventListener('focus', function () {
        headerSearch.classList.add('is-focused');
        headerTrigger.setAttribute('aria-expanded', 'true');
      });
    }

    initLiveSearch({
      root: headerSearch,
      onActive: function (active) {
        headerSearch.classList.toggle('is-searching', active);
        if (headerTrigger) {
          headerTrigger.setAttribute('aria-expanded', active ? 'true' : 'false');
        }
      }
    });
  }

  function initQuoteDrawer() {
    var drawer = document.getElementById('elite-quote-drawer');
    var overlay = document.getElementById('elite-quote-drawer-overlay');
    if (!drawer || !overlay) {
      return;
    }

    function openQuoteDrawer() {
      if (typeof closeMobileNav === 'function' && document.body.classList.contains('elite-nav-open')) {
        closeMobileNav();
      }
      drawer.hidden = false;
      overlay.hidden = false;
      drawer.setAttribute('aria-hidden', 'false');
      overlay.setAttribute('aria-hidden', 'false');
      document.body.classList.add('elite-quote-drawer-open');
    }

    function closeQuoteDrawer() {
      drawer.hidden = true;
      overlay.hidden = true;
      drawer.setAttribute('aria-hidden', 'true');
      overlay.setAttribute('aria-hidden', 'true');
      document.body.classList.remove('elite-quote-drawer-open');
    }

    document.querySelectorAll('.js-elite-quote-drawer-open').forEach(function (trigger) {
      trigger.addEventListener('click', function (event) {
        event.preventDefault();
        openQuoteDrawer();
      });
    });

    var closeBtn = drawer.querySelector('.elite-quote-drawer-close');
    if (closeBtn) {
      closeBtn.addEventListener('click', closeQuoteDrawer);
    }

    overlay.addEventListener('click', closeQuoteDrawer);

    document.addEventListener('keydown', function (event) {
      if (event.key === 'Escape' && document.body.classList.contains('elite-quote-drawer-open')) {
        closeQuoteDrawer();
      }
    });

    if (/[?&]quote=(sent|error)(?:&|$)/.test(window.location.search)) {
      openQuoteDrawer();
    }
  }

  initQuoteDrawer();

  function initCartDrawer() {
    var drawer = document.getElementById('elite-cart-drawer');
    var overlay = document.getElementById('elite-cart-drawer-overlay');
    var fab = document.querySelector('.elite-cart-fab');
    if (!drawer || !overlay) {
      return;
    }

    function closeQuoteDrawerIfOpen() {
      var quoteDrawer = document.getElementById('elite-quote-drawer');
      var quoteOverlay = document.getElementById('elite-quote-drawer-overlay');
      if (!document.body.classList.contains('elite-quote-drawer-open')) {
        return;
      }
      if (quoteDrawer) {
        quoteDrawer.hidden = true;
        quoteDrawer.setAttribute('aria-hidden', 'true');
      }
      if (quoteOverlay) {
        quoteOverlay.hidden = true;
        quoteOverlay.setAttribute('aria-hidden', 'true');
      }
      document.body.classList.remove('elite-quote-drawer-open');
    }

    function openCartDrawer() {
      if (typeof closeMobileNav === 'function' && document.body.classList.contains('elite-nav-open')) {
        closeMobileNav();
      }
      closeQuoteDrawerIfOpen();
      drawer.hidden = false;
      overlay.hidden = false;
      drawer.setAttribute('aria-hidden', 'false');
      overlay.setAttribute('aria-hidden', 'false');
      document.body.classList.add('elite-cart-drawer-open');
      if (fab) {
        fab.setAttribute('aria-expanded', 'true');
      }
    }

    function closeCartDrawer() {
      drawer.hidden = true;
      overlay.hidden = true;
      drawer.setAttribute('aria-hidden', 'true');
      overlay.setAttribute('aria-hidden', 'true');
      document.body.classList.remove('elite-cart-drawer-open');
      if (fab) {
        fab.setAttribute('aria-expanded', 'false');
      }
    }

    document.querySelectorAll('.js-elite-cart-drawer-open').forEach(function (trigger) {
      trigger.addEventListener('click', function (event) {
        event.preventDefault();
        if (document.body.classList.contains('elite-cart-drawer-open')) {
          closeCartDrawer();
          return;
        }
        openCartDrawer();
      });
    });

    var closeBtn = drawer.querySelector('.elite-cart-drawer-close');
    if (closeBtn) {
      closeBtn.addEventListener('click', closeCartDrawer);
    }

    overlay.addEventListener('click', closeCartDrawer);

    document.addEventListener('keydown', function (event) {
      if (event.key === 'Escape' && document.body.classList.contains('elite-cart-drawer-open')) {
        closeCartDrawer();
      }
    });

    if (window.jQuery) {
      window.jQuery(document.body).on('added_to_cart', function () {
        openCartDrawer();
      });
    }
  }

  initCartDrawer();

  function getToastTopOffset() {
    var headerBar = document.querySelector('.elite-site-header-bar');
    if (headerBar) {
      return Math.round(headerBar.getBoundingClientRect().bottom + 12);
    }
    return 120;
  }

  function updateToastTopOffset() {
    document.documentElement.style.setProperty('--elite-toast-top', getToastTopOffset() + 'px');
  }

  updateToastTopOffset();
  window.addEventListener('resize', updateToastTopOffset);

  function showAddToCartToast(message) {
    var text = message || 'Successfully added product to cart';
    var existing = document.querySelector('.elite-cart-toast');
    if (existing) {
      existing.remove();
    }

    var toast = document.createElement('div');
    toast.className = 'elite-cart-toast';
    toast.setAttribute('role', 'status');
    toast.setAttribute('aria-live', 'polite');
    toast.textContent = text;
    toast.style.top = getToastTopOffset() + 'px';
    document.body.appendChild(toast);

    window.requestAnimationFrame(function () {
      toast.classList.add('is-visible');
    });

    window.setTimeout(function () {
      toast.classList.remove('is-visible');
      toast.classList.add('is-hiding');
    }, 2600);

    window.setTimeout(function () {
      if (toast.parentNode) {
        toast.parentNode.removeChild(toast);
      }
    }, 3200);
  }

  function applyCartFragments(fragments) {
    if (!fragments) {
      return;
    }

    Object.keys(fragments).forEach(function (selector) {
      var node = document.querySelector(selector);
      if (!node) {
        return;
      }
      var wrap = document.createElement('div');
      wrap.innerHTML = fragments[selector];
      var replacement = wrap.firstElementChild;
      if (replacement && node.parentNode) {
        node.parentNode.replaceChild(replacement, node);
      }
    });
  }

  function initShopProductAddToCart() {
    if (!window.eliteShippingAddToCart || !window.eliteShippingAddToCart.ajaxUrl) {
      return;
    }

    document.addEventListener('click', function (event) {
      var button = event.target.closest('.apex-shop-product-add, .apex-wishlist-item-cart');
      if (!button || button.classList.contains('is-loading')) {
        return;
      }

      event.preventDefault();
      event.stopPropagation();
      event.stopImmediatePropagation();

      var productId = button.getAttribute('data-product_id');
      if (!productId) {
        return;
      }

      if (!button.classList.contains('ajax_add_to_cart')) {
        window.location.href = button.getAttribute('href');
        return;
      }

      button.classList.add('is-loading');

      var body = new URLSearchParams();
      body.append('product_id', productId);
      body.append('quantity', button.getAttribute('data-quantity') || '1');

      fetch(window.eliteShippingAddToCart.ajaxUrl, {
        method: 'POST',
        credentials: 'same-origin',
        headers: {
          'Content-Type': 'application/x-www-form-urlencoded; charset=UTF-8'
        },
        body: body.toString()
      })
        .then(function (response) {
          return response.json();
        })
        .then(function (response) {
          button.classList.remove('is-loading');
          if (!response) {
            return;
          }
          if (response.error && response.product_url) {
            window.location.href = response.product_url;
            return;
          }
          applyCartFragments(response.fragments);
          if (window.eliteWishlistUI && typeof window.eliteWishlistUI.applyAfterAddToCart === 'function') {
            window.eliteWishlistUI.applyAfterAddToCart(productId, response.fragments);
          }
          showAddToCartToast('Successfully added product to cart');
        })
        .catch(function () {
          button.classList.remove('is-loading');
          window.location.href = button.getAttribute('href');
        });
    }, true);
  }

  initShopProductAddToCart();

  function initEliteWishlist() {
    var config = window.eliteShippingWishlist;
    if (!config || !config.ajaxUrl || !config.nonce) {
      return;
    }

    var ids = Array.isArray(config.ids) ? config.ids.map(Number) : [];

    function updateFabCount(count) {
      var safeCount = Number(count) || 0;
      document.querySelectorAll('.elite-wishlist-fab-count, [data-elite-wishlist-badge]').forEach(function (badge) {
        badge.textContent = String(safeCount);
        badge.classList.toggle('is-empty', !safeCount);
      });
      document.querySelectorAll('.elite-wishlist-fab').forEach(function (fab) {
        var label = safeCount === 1
          ? 'Wishlist, 1 item'
          : 'Wishlist, ' + safeCount + ' items';
        fab.setAttribute('aria-label', label);
      });

      var syncNode = document.querySelector('.elite-wishlist-sync');
      if (syncNode) {
        syncNode.setAttribute('data-count', String(safeCount));
        syncNode.setAttribute('data-ids', ids.join('|'));
      }
    }

    function updateCountLabel(count) {
      var labelEl = document.querySelector('[data-elite-wishlist-count-label]');
      if (!labelEl || !config.i18n) {
        return;
      }
      var template = count === 1 ? (config.i18n.item || '%d item') : (config.i18n.items || '%d items');
      labelEl.textContent = template.replace('%d', String(count));
    }

    function syncButtons(productId, inWishlist, label) {
      document.querySelectorAll('.js-elite-wishlist-toggle[data-product_id="' + productId + '"]').forEach(function (btn) {
        btn.classList.toggle('is-active', !!inWishlist);
        btn.setAttribute('aria-label', label);
        var tip = btn.querySelector('.apex-shop-product-add-tooltip');
        if (tip) {
          tip.textContent = label;
        }
        var text = btn.querySelector('.js-elite-wishlist-label');
        if (text) {
          text.textContent = label;
        }
      });
    }

    function renderEmptyState() {
      var content = document.querySelector('[data-elite-wishlist-content]');
      if (!content) {
        return;
      }
      var shopUrl = (window.eliteShippingCartDrawer && window.eliteShippingCartDrawer.cartUrl)
        ? window.eliteShippingCartDrawer.cartUrl.replace(/\/cart\/?$/, '/shop/')
        : '/shop/';
      content.innerHTML =
        '<div class="apex-wishlist-empty">' +
        '<p>' + (config.i18n.empty || 'Your wishlist is empty.') + '</p>' +
        '<a class="apex-wishlist-empty-btn" href="' + shopUrl + '">' + (config.i18n.shop || 'Browse products') + '</a>' +
        '</div>';

      var clearBtn = document.querySelector('.js-elite-wishlist-clear');
      if (clearBtn && clearBtn.parentNode) {
        clearBtn.parentNode.removeChild(clearBtn);
      }
    }

    function removeWishlistRow(productId) {
      var row = document.querySelector('.apex-wishlist-item[data-product_id="' + productId + '"]');
      if (!row) {
        return;
      }
      row.classList.add('is-removing');
      window.setTimeout(function () {
        if (row.parentNode) {
          row.parentNode.removeChild(row);
        }
        if (!document.querySelector('.apex-wishlist-item')) {
          renderEmptyState();
        }
      }, 220);
    }

    function applyWishlistState(nextIds, removedProductId) {
      ids = Array.isArray(nextIds) ? nextIds.map(Number) : [];
      config.ids = ids;
      updateFabCount(ids.length);
      updateCountLabel(ids.length);

      if (removedProductId) {
        syncButtons(removedProductId, false, config.i18n.add || 'Add to wishlist');
        removeWishlistRow(removedProductId);
      }

      document.querySelectorAll('.js-elite-wishlist-toggle[data-product_id]').forEach(function (btn) {
        var pid = Number(btn.getAttribute('data-product_id'));
        var active = ids.indexOf(pid) !== -1;
        var label = active ? (config.i18n.remove || 'Remove from wishlist') : (config.i18n.add || 'Add to wishlist');
        syncButtons(pid, active, label);
      });
    }

    function parseIdsFromSyncNode(node) {
      if (!node) {
        return null;
      }
      var raw = node.getAttribute('data-ids') || '';
      if (!raw) {
        return [];
      }
      var parts = raw.indexOf('|') !== -1 ? raw.split('|') : raw.split(',');
      return parts.map(function (value) {
        return Number(value);
      }).filter(Boolean);
    }

    function applyAfterAddToCart(productId, fragments) {
      var nextIds = null;
      var pid = Number(productId);

      if (fragments && fragments['div.elite-wishlist-sync']) {
        var wrap = document.createElement('div');
        wrap.innerHTML = fragments['div.elite-wishlist-sync'];
        nextIds = parseIdsFromSyncNode(wrap.querySelector('.elite-wishlist-sync'));
      } else {
        var syncNode = document.querySelector('.elite-wishlist-sync');
        nextIds = parseIdsFromSyncNode(syncNode);
      }

      if (nextIds === null) {
        nextIds = ids.filter(function (id) {
          return id !== pid;
        });
      }

      applyWishlistState(nextIds, pid);
    }

    window.eliteWishlistUI = {
      applyAfterAddToCart: applyAfterAddToCart,
      applyWishlistState: applyWishlistState,
      refreshBadge: function () {
        updateFabCount(ids.length);
      }
    };

    if (window.jQuery) {
      window.jQuery(document.body).on('added_to_cart', function (event, fragments, cartHash, button) {
        var productId = button && button.length ? button.data('product_id') : null;
        if (!productId && button && button.attr) {
          productId = button.attr('data-product_id');
        }
        if (productId) {
          applyAfterAddToCart(productId, fragments || {});
        }
      });

      // WooCommerce fragment cache can overwrite badges — restore wishlist count after refresh.
      window.jQuery(document.body).on('wc_fragments_refreshed wc_fragments_loaded', function () {
        updateFabCount(ids.length);
      });
    }

    // Guard against late fragment replaces after first paint.
    window.setTimeout(function () {
      updateFabCount(ids.length);
    }, 500);
    window.setTimeout(function () {
      updateFabCount(ids.length);
    }, 1500);

    function toggleRequest(productId, button) {
      if (button) {
        button.classList.add('is-loading');
      }

      var body = new URLSearchParams();
      body.append('action', 'elite_wishlist_toggle');
      body.append('nonce', config.nonce);
      body.append('product_id', String(productId));

      return fetch(config.ajaxUrl, {
        method: 'POST',
        credentials: 'same-origin',
        headers: {
          'Content-Type': 'application/x-www-form-urlencoded; charset=UTF-8'
        },
        body: body.toString()
      })
        .then(function (response) {
          return response.json();
        })
        .then(function (payload) {
          if (button) {
            button.classList.remove('is-loading');
          }
          if (!payload || !payload.success || !payload.data) {
            return null;
          }

          var data = payload.data;
          ids = Array.isArray(data.ids) ? data.ids.map(Number) : ids;
          config.ids = ids;
          updateFabCount(data.count || 0);
          updateCountLabel(data.count || 0);
          syncButtons(data.product_id, data.in_wishlist, data.label || (data.in_wishlist ? config.i18n.remove : config.i18n.add));

          if (typeof showAddToCartToast === 'function') {
            showAddToCartToast(data.message || (data.in_wishlist ? config.i18n.added : config.i18n.removed));
          }

          return data;
        })
        .catch(function () {
          if (button) {
            button.classList.remove('is-loading');
          }
          return null;
        });
    }

    document.addEventListener('click', function (event) {
      var button = event.target.closest('.js-elite-wishlist-toggle');
      if (!button || button.classList.contains('is-loading')) {
        return;
      }

      var productId = button.getAttribute('data-product_id');
      if (!productId) {
        return;
      }

      event.preventDefault();
      event.stopPropagation();
      event.stopImmediatePropagation();

      toggleRequest(productId, button).then(function (data) {
        if (!data) {
          return;
        }

        var row = button.closest('.apex-wishlist-item');
        if (row && !data.in_wishlist) {
          row.classList.add('is-removing');
          window.setTimeout(function () {
            if (row.parentNode) {
              row.parentNode.removeChild(row);
            }
            if (!data.count) {
              renderEmptyState();
            }
          }, 220);
        }
      });
    }, true);

    var clearBtn = document.querySelector('.js-elite-wishlist-clear');
    if (clearBtn) {
      clearBtn.addEventListener('click', function () {
        if (clearBtn.classList.contains('is-loading')) {
          return;
        }
        clearBtn.classList.add('is-loading');

        var body = new URLSearchParams();
        body.append('action', 'elite_wishlist_clear');
        body.append('nonce', config.nonce);

        fetch(config.ajaxUrl, {
          method: 'POST',
          credentials: 'same-origin',
          headers: {
            'Content-Type': 'application/x-www-form-urlencoded; charset=UTF-8'
          },
          body: body.toString()
        })
          .then(function (response) {
            return response.json();
          })
          .then(function (payload) {
            clearBtn.classList.remove('is-loading');
            if (!payload || !payload.success) {
              return;
            }
            applyWishlistState([], null);
            renderEmptyState();
            if (typeof showAddToCartToast === 'function') {
              showAddToCartToast(config.i18n.removed || 'Wishlist cleared.');
            }
          })
          .catch(function () {
            clearBtn.classList.remove('is-loading');
          });
      });
    }

    updateFabCount(ids.length);
  }

  initEliteWishlist();

  var shopFiltersToggle = document.querySelector('.apex-shop-filters-toggle');
  var shopSidebar = document.getElementById('apex-shop-sidebar');
  var shopFiltersOverlay = document.getElementById('apex-shop-filters-overlay');
  var shopFiltersClose = shopSidebar ? shopSidebar.querySelector('.apex-shop-filters-close') : null;

  function closeShopFilters() {
    document.body.classList.remove('elite-shop-filters-open');
    if (shopFiltersToggle) {
      shopFiltersToggle.setAttribute('aria-expanded', 'false');
      shopFiltersToggle.setAttribute('aria-label', 'Open filters');
    }
    if (shopFiltersOverlay) {
      shopFiltersOverlay.hidden = true;
      shopFiltersOverlay.setAttribute('aria-hidden', 'true');
    }
  }

  function openShopFilters() {
    document.body.classList.add('elite-shop-filters-open');
    if (shopFiltersToggle) {
      shopFiltersToggle.setAttribute('aria-expanded', 'true');
      shopFiltersToggle.setAttribute('aria-label', 'Close filters');
    }
    if (shopFiltersOverlay) {
      shopFiltersOverlay.hidden = false;
      shopFiltersOverlay.setAttribute('aria-hidden', 'false');
    }
  }

  if (shopFiltersToggle && shopSidebar) {
    shopFiltersToggle.addEventListener('click', function () {
      if (document.body.classList.contains('elite-shop-filters-open')) {
        closeShopFilters();
      } else {
        openShopFilters();
      }
    });

    if (shopFiltersClose) {
      shopFiltersClose.addEventListener('click', closeShopFilters);
    }

    if (shopFiltersOverlay) {
      shopFiltersOverlay.addEventListener('click', closeShopFilters);
    }

    document.addEventListener('keydown', function (event) {
      if (event.key === 'Escape' && document.body.classList.contains('elite-shop-filters-open')) {
        closeShopFilters();
      }
    });

    window.addEventListener('resize', function () {
      if (window.innerWidth > 768) {
        closeShopFilters();
      }
    });
  }

  document.querySelectorAll('.elite-single-product form.cart .quantity').forEach(function (quantityWrap) {
    var input = quantityWrap.querySelector('input.qty');
    if (!input || quantityWrap.querySelector('.qty-btn')) {
      return;
    }

    var minus = document.createElement('button');
    minus.type = 'button';
    minus.className = 'qty-btn qty-btn--minus';
    minus.setAttribute('aria-label', 'Decrease quantity');
    minus.textContent = '−';

    var plus = document.createElement('button');
    plus.type = 'button';
    plus.className = 'qty-btn qty-btn--plus';
    plus.setAttribute('aria-label', 'Increase quantity');
    plus.textContent = '+';

    quantityWrap.insertBefore(minus, input);
    quantityWrap.appendChild(plus);

    function updateQty(delta) {
      var min = parseFloat(input.min) || 1;
      var max = input.max ? parseFloat(input.max) : Infinity;
      var step = parseFloat(input.step) || 1;
      var value = parseFloat(input.value) || min;
      value = Math.min(max, Math.max(min, value + delta * step));
      input.value = value;
      input.dispatchEvent(new Event('change', { bubbles: true }));
    }

    minus.addEventListener('click', function () {
      updateQty(-1);
    });

    plus.addEventListener('click', function () {
      updateQty(1);
    });
  });

  var GALLERY_THUMB_VISIBLE = 4;

  function getGalleryMaxWidth() {
    return window.matchMedia('(max-width: 1024px)').matches ? 960 : 1080;
  }

  function getGalleryFrame(gallery) {
    if (!gallery) {
      return null;
    }

    return gallery.querySelector('.flex-viewport') || gallery.querySelector(':scope > .woocommerce-product-gallery__wrapper');
  }

  function getGalleryTrack(gallery) {
    var frame = getGalleryFrame(gallery);
    if (!frame) {
      return null;
    }

    if (
      frame.classList.contains('woocommerce-product-gallery__wrapper') ||
      frame.classList.contains('slides')
    ) {
      return frame;
    }

    return frame.querySelector('.woocommerce-product-gallery__wrapper') || frame.querySelector('.slides');
  }

  function getGallerySlides(gallery) {
    var track = getGalleryTrack(gallery);
    if (!track) {
      return [];
    }

    return Array.prototype.slice.call(
      track.querySelectorAll(':scope > .woocommerce-product-gallery__image, :scope > li')
    ).filter(function (slide) {
      if (slide.classList.contains('clone')) {
        return false;
      }

      return slide.querySelector('img:not(.zoomImg), a img:not(.zoomImg)');
    });
  }

  function getGallerySlideWidth(gallery) {
    if (gallery && gallery.dataset.slideWidth) {
      var cached = parseFloat(gallery.dataset.slideWidth, 10);
      if (!isNaN(cached) && cached > 0) {
        return cached;
      }
    }

    var frame = getGalleryFrame(gallery);
    if (frame) {
      var measured = Math.round(frame.getBoundingClientRect().width);
      if (measured > 0) {
        return measured;
      }
    }

    return 0;
  }

  function getActiveSlideIndex(gallery) {
    if (gallery && gallery.dataset.activeSlide) {
      var stored = parseInt(gallery.dataset.activeSlide, 10);
      if (!isNaN(stored)) {
        return stored;
      }
    }

    var slides = getGallerySlides(gallery);
    var activeIndex = slides.findIndex(function (slide) {
      return slide.classList.contains('flex-active-slide');
    });

    if (activeIndex >= 0) {
      return activeIndex;
    }

    var frame = getGalleryFrame(gallery);
    var width = getGallerySlideWidth(gallery);
    if (frame && width > 0) {
      return Math.min(slides.length - 1, Math.max(0, Math.round(frame.scrollLeft / width)));
    }

    return resolveGallerySlideIndex(gallery, getActiveThumbIndex(gallery));
  }

  function getActiveThumbIndex(gallery) {
    var items = getGalleryThumbItems(gallery);
    var activeIndex = items.findIndex(function (item) {
      return item.classList.contains('apex-thumb-active') || item.classList.contains('flex-active');
    });

    return activeIndex >= 0 ? activeIndex : 0;
  }

  function getSlideCount(gallery) {
    return getGallerySlides(gallery).length || getGalleryThumbItems(gallery).length;
  }

  function setGalleryThumbActive(gallery, activeIndex) {
    var items = getGalleryThumbItems(gallery);
    if (!items.length) {
      return;
    }

    if (activeIndex < 0) {
      activeIndex = 0;
    }
    if (activeIndex >= items.length) {
      activeIndex = items.length - 1;
    }

    items.forEach(function (item, itemIndex) {
      var isActive = itemIndex === activeIndex;
      item.classList.toggle('apex-thumb-active', isActive);
      item.classList.toggle('flex-active', isActive);
    });
  }

  function getGalleryThumbSrc(thumbItem) {
    var img = thumbItem ? thumbItem.querySelector('img') : null;
    if (!img) {
      return '';
    }

    return img.currentSrc || img.src || img.getAttribute('src') || '';
  }

  function getGallerySlideSrc(slide) {
    var img = slide ? slide.querySelector('img:not(.zoomImg)') : null;
    if (img) {
      return img.currentSrc || img.src || img.getAttribute('src') || '';
    }

    var link = slide ? slide.querySelector('a') : null;
    return link ? link.href || '' : '';
  }

  function normalizeGallerySrc(src) {
    if (!src) {
      return '';
    }

    try {
      var url = new URL(src, window.location.href);
      return url.pathname + url.search;
    } catch (error) {
      return src.split('?')[0];
    }
  }

  function resolveGallerySlideIndex(gallery, thumbIndex) {
    var slides = getGallerySlides(gallery);
    var thumbs = getGalleryThumbItems(gallery);
    if (!slides.length || !thumbs.length) {
      return 0;
    }

    if (thumbIndex < 0) {
      return 0;
    }
    if (thumbIndex >= thumbs.length) {
      thumbIndex = thumbs.length - 1;
    }
    if (slides.length === thumbs.length) {
      return thumbIndex;
    }

    var thumbSrc = normalizeGallerySrc(getGalleryThumbSrc(thumbs[thumbIndex]));
    for (var i = 0; i < slides.length; i++) {
      if (normalizeGallerySrc(getGallerySlideSrc(slides[i])) === thumbSrc) {
        return i;
      }
    }

    return Math.min(thumbIndex, slides.length - 1);
  }

  function resolveGalleryThumbIndex(gallery, slideIndex) {
    var slides = getGallerySlides(gallery);
    var thumbs = getGalleryThumbItems(gallery);
    if (!slides.length || !thumbs.length) {
      return 0;
    }

    if (slideIndex < 0) {
      return 0;
    }
    if (slideIndex >= slides.length) {
      slideIndex = slides.length - 1;
    }
    if (slides.length === thumbs.length) {
      return slideIndex;
    }

    var slideSrc = normalizeGallerySrc(getGallerySlideSrc(slides[slideIndex]));
    for (var i = 0; i < thumbs.length; i++) {
      if (normalizeGallerySrc(getGalleryThumbSrc(thumbs[i])) === slideSrc) {
        return i;
      }
    }

    return Math.min(slideIndex, thumbs.length - 1);
  }

  function setGallerySlideIndex(gallery, index, animate, thumbIndex) {
    var slides = getGallerySlides(gallery);
    var track = getGalleryTrack(gallery);
    var frame = getGalleryFrame(gallery);
    if (!slides.length || !track || !frame) {
      return;
    }

    var width = getGallerySlideWidth(gallery);
    if (width <= 0) {
      layoutGalleryTrack(gallery);
      width = getGallerySlideWidth(gallery);
      if (width <= 0) {
        return;
      }
    }

    var count = slides.length;
    var targetIndex = ((index % count) + count) % count;
    var activeThumbIndex = typeof thumbIndex === 'number'
      ? thumbIndex
      : resolveGalleryThumbIndex(gallery, targetIndex);
    var left = targetIndex * width;

    gallery.dataset.activeSlide = String(targetIndex);
    track.style.marginLeft = '0';
    track.style.transform = 'none';

    if (animate && typeof frame.scrollTo === 'function') {
      frame.scrollTo({ left: left, behavior: 'smooth' });
    } else {
      frame.scrollLeft = left;
    }

    slides.forEach(function (slide, slideIndex) {
      slide.classList.toggle('flex-active-slide', slideIndex === targetIndex);
      slide.style.visibility = 'visible';
      slide.style.opacity = '1';
    });

    setGalleryThumbActive(gallery, activeThumbIndex);
    updateGalleryThumbStrip(gallery);
  }

  function bindGalleryImageLoad(gallery) {
    if (!gallery || gallery.dataset.apexImageLoadBound === '1') {
      return;
    }

    gallery.dataset.apexImageLoadBound = '1';
    gallery.querySelectorAll('.woocommerce-product-gallery__image img:not(.zoomImg)').forEach(function (image) {
      if (image.complete) {
        return;
      }

      image.addEventListener('load', function () {
        layoutGalleryTrack(gallery);
      }, { once: true });
    });
  }

  function layoutGalleryTrack(gallery) {
    var frame = getGalleryFrame(gallery);
    var track = getGalleryTrack(gallery);
    var slides = getGallerySlides(gallery);
    if (!gallery || !frame || !track || !slides.length) {
      return;
    }

    var shell = getGalleryMainShell(gallery);
    var maxWidth = getGalleryMaxWidth();
    var shellWidth = shell ? shell.offsetWidth : gallery.offsetWidth;
    var width = Math.min(shellWidth || maxWidth, maxWidth);

    if (width <= 0) {
      window.requestAnimationFrame(function () {
        layoutGalleryTrack(gallery);
      });
      return;
    }

    var height = Math.round(width * 2 / 3);

    gallery.dataset.slideWidth = String(width);
    gallery.classList.add('apex-gallery-ready');

    frame.style.width = width + 'px';
    frame.style.height = height + 'px';
    frame.style.maxWidth = width + 'px';
    frame.style.overflowX = 'hidden';
    frame.style.overflowY = 'hidden';

    track.style.display = 'flex';
    track.style.flexDirection = 'row';
    track.style.flexWrap = 'nowrap';
    track.style.alignItems = 'stretch';
    track.style.height = height + 'px';
    track.style.width = (width * slides.length) + 'px';
    track.style.marginLeft = '0';
    track.style.transform = 'none';
    track.style.transition = 'none';

    slides.forEach(function (slide) {
      slide.style.flex = '0 0 ' + width + 'px';
      slide.style.width = width + 'px';
      slide.style.minWidth = width + 'px';
      slide.style.maxWidth = width + 'px';
      slide.style.height = height + 'px';
      slide.style.float = 'none';
      slide.style.display = 'block';
      slide.style.visibility = 'visible';
      slide.style.opacity = '1';
      slide.style.position = 'relative';
    });

    setGallerySlideIndex(gallery, getActiveSlideIndex(gallery), false);
  }

  function removeFlexsliderDirectionNav(gallery) {
    var scopes = [];

    if (gallery) {
      scopes.push(gallery);
      var imagesWrap = gallery.closest('.images');
      var product = gallery.closest('.product');
      if (imagesWrap) {
        scopes.push(imagesWrap);
      }
      if (product) {
        scopes.push(product);
      }
    }

    scopes.push(document.querySelector('.elite-single-product'));

    scopes.forEach(function (scope) {
      if (!scope) {
        return;
      }

      scope.querySelectorAll('.flex-direction-nav').forEach(function (nav) {
        nav.remove();
      });

      scope.querySelectorAll('a.flex-prev, a.flex-next').forEach(function (link) {
        if (link.closest('.apex-gallery-main-arrow, .apex-gallery-thumb-arrow')) {
          return;
        }
        link.remove();
      });
    });
  }

  function watchGalleryDirectionNav(gallery) {
    if (!gallery || gallery.dataset.apexNavWatch === '1') {
      return;
    }

    gallery.dataset.apexNavWatch = '1';

    var scopes = [gallery];
    var imagesWrap = gallery.closest('.images');
    if (imagesWrap) {
      scopes.push(imagesWrap);
    }

    var observer = new MutationObserver(function () {
      removeFlexsliderDirectionNav(gallery);
    });

    scopes.forEach(function (scope) {
      observer.observe(scope, { childList: true, subtree: true });
    });
  }

  function disableFlexsliderNavigation(gallery) {
    if (!window.jQuery) {
      return;
    }

    var galleryApi = window.jQuery(gallery).data('product_gallery');
    if (!galleryApi || !galleryApi.flexslider) {
      return;
    }

    var slider = galleryApi.flexslider;
    if (typeof slider.stop === 'function') {
      slider.stop();
    }

    slider.resize = function () {};
    slider.setProps = function () {};
    slider.flexAnimate = function (target) {
      setGallerySlideIndex(gallery, target, false);
    };

    window.jQuery(gallery).find('.flex-control-nav li').off('click');
    window.jQuery(gallery).find('.flex-control-nav li img').css('opacity', '');
    removeFlexsliderDirectionNav(gallery);
  }

  function handleGalleryThumbClick(event) {
    var item = event.target.closest('.flex-control-nav li');
    if (!item) {
      return;
    }

    var nav = item.closest('.flex-control-nav');
    var gallery = nav ? nav.closest('.woocommerce-product-gallery') : null;
    if (!gallery || !gallery.closest('.elite-single-product')) {
      return;
    }

    event.preventDefault();
    event.stopPropagation();

    var items = getGalleryThumbItems(gallery);
    var thumbIndex = items.indexOf(item);
    if (thumbIndex < 0) {
      return;
    }

    setGalleryThumbActive(gallery, thumbIndex);
    setGallerySlideIndex(gallery, resolveGallerySlideIndex(gallery, thumbIndex), false, thumbIndex);
  }

  function bindGalleryThumbClicks(gallery) {
    if (!gallery) {
      return;
    }

    var nav = gallery.querySelector('.flex-control-nav');
    if (!nav || nav.dataset.apexThumbBound === '1') {
      return;
    }

    nav.dataset.apexThumbBound = '1';
    nav.addEventListener('click', handleGalleryThumbClick, true);
  }

  function stepGallerySlide(gallery, direction) {
    var count = getSlideCount(gallery);
    if (count <= 1 || !direction) {
      return;
    }

    setGallerySlideIndex(gallery, getActiveSlideIndex(gallery) + direction, true);
  }

  function goToGallerySlide(gallery, index) {
    setGallerySlideIndex(gallery, index, false);
  }

  function activateGalleryThumb(gallery, index) {
    goToGallerySlide(gallery, index);
  }

  function navigateGallerySlide(gallery, direction) {
    stepGallerySlide(gallery, direction);
  }

  function syncSingleProductGallery() {
    var gallery = document.querySelector('.elite-single-product .woocommerce-product-gallery');
    var frame = getGalleryFrame(gallery);
    if (!gallery || !frame) {
      return;
    }

    unwrapGalleryLayout(gallery);

    var shell = ensureGalleryMainShell(gallery, frame);
    initGalleryMainArrows(gallery, shell);
    initGalleryThumbStrip(gallery);
    ensureGalleryOverlays(gallery, shell);
    bindGalleryImageLoad(gallery);
    disableFlexsliderNavigation(gallery);
    removeFlexsliderDirectionNav(gallery);
    watchGalleryDirectionNav(gallery);
    bindGalleryThumbClicks(gallery);
    layoutGalleryTrack(gallery);
    syncGalleryThumbVisualState(gallery);
  }

  function syncGalleryThumbVisualState(gallery) {
    if (!gallery) {
      return;
    }

    var slideIndex = getActiveSlideIndex(gallery);
    setGalleryThumbActive(gallery, resolveGalleryThumbIndex(gallery, slideIndex));
  }

  function getGalleryThumbItems(gallery) {
    if (!gallery) {
      return [];
    }

    var nav = gallery.querySelector('.flex-control-nav');
    if (!nav) {
      return [];
    }

    return Array.prototype.slice.call(nav.querySelectorAll('li'));
  }

  function getThumbMetrics(gallery) {
    var items = getGalleryThumbItems(gallery);
    var nav = gallery ? gallery.querySelector('.flex-control-nav') : null;
    var size = items.length ? items[0].offsetWidth : 144;
    var gap = 10;

    if (nav) {
      var navStyle = window.getComputedStyle(nav);
      gap = parseFloat(navStyle.columnGap || navStyle.gap || '10') || 10;
    }

    return {
      size: size || 144,
      gap: gap,
      visible: GALLERY_THUMB_VISIBLE,
    };
  }

  function getGalleryThumbOffset(gallery) {
    return gallery ? (gallery.dataset.thumbOffset ? parseInt(gallery.dataset.thumbOffset, 10) : 0) : 0;
  }

  function setGalleryThumbOffset(gallery, offset) {
    if (!gallery) {
      return;
    }

    gallery.dataset.thumbOffset = String(Math.max(0, offset));
  }

  function unwrapGalleryThumbRail(gallery) {
    if (!gallery) {
      return;
    }

    var rail = gallery.querySelector('.apex-gallery-thumb-rail');
    if (!rail) {
      return;
    }

    var thumbs = rail.querySelector('.flex-control-nav');
    if (thumbs) {
      gallery.appendChild(thumbs);
    }

    rail.remove();
  }

  function unwrapGalleryLayout(gallery) {
    if (!gallery) {
      return;
    }

    var layout = gallery.querySelector('.apex-gallery-layout');
    if (!layout) {
      return;
    }

    var main = layout.querySelector('.apex-gallery-main');
    var strip = layout.querySelector('.apex-gallery-thumb-strip');

    if (main) {
      gallery.insertBefore(main, layout);
    }
    if (strip) {
      gallery.appendChild(strip);
    }

    layout.remove();
  }

  function getGalleryMainShell(gallery) {
    if (!gallery) {
      return null;
    }

    var shells = gallery.querySelectorAll(':scope > .apex-gallery-main');
    var index;
    for (index = 0; index < shells.length; index++) {
      if (shells[index].querySelector('.flex-viewport, .woocommerce-product-gallery__wrapper')) {
        return shells[index];
      }
    }

    return shells[0] || null;
  }

  function cleanupDuplicateGalleryMainShells(gallery, keepShell) {
    if (!gallery) {
      return;
    }

    gallery.querySelectorAll(':scope > .apex-gallery-main').forEach(function (shell) {
      if (shell === keepShell) {
        return;
      }

      Array.prototype.slice.call(shell.children).forEach(function (child) {
        if (
          child.classList.contains('flex-viewport') ||
          child.classList.contains('woocommerce-product-gallery__wrapper')
        ) {
          keepShell.insertBefore(child, keepShell.firstChild);
          return;
        }

        if (child.classList.contains('apex-gallery-overlays') && !keepShell.querySelector('.apex-gallery-overlays')) {
          keepShell.appendChild(child);
          return;
        }

        if (child.classList.contains('apex-gallery-main-arrow')) {
          var direction = child.classList.contains('apex-gallery-main-arrow--prev') ? 'prev' : 'next';
          if (!keepShell.querySelector('.apex-gallery-main-arrow--' + direction)) {
            keepShell.appendChild(child);
          }
        }
      });

      shell.remove();
    });
  }

  function ensureGalleryMainShell(gallery, frame) {
    if (!gallery || !frame) {
      return null;
    }

    var activeShell = frame.closest('.apex-gallery-main');
    if (activeShell) {
      cleanupDuplicateGalleryMainShells(gallery, activeShell);
      return activeShell;
    }

    var primaryShell = getGalleryMainShell(gallery);
    if (primaryShell) {
      primaryShell.insertBefore(frame, primaryShell.firstChild);
      cleanupDuplicateGalleryMainShells(gallery, primaryShell);
      return primaryShell;
    }

    var shell = document.createElement('div');
    shell.className = 'apex-gallery-main';
    gallery.insertBefore(shell, frame);
    shell.appendChild(frame);
    cleanupDuplicateGalleryMainShells(gallery, shell);
    return shell;
  }

  function initGalleryMainArrows(gallery, shell) {
    if (!gallery || !shell || shell.querySelector('.apex-gallery-main-arrow--prev')) {
      return;
    }

    var prevBtn = document.createElement('button');
    prevBtn.type = 'button';
    prevBtn.className = 'apex-gallery-main-arrow apex-gallery-main-arrow--prev';
    prevBtn.setAttribute('aria-label', 'Previous image');
    prevBtn.innerHTML = '<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" aria-hidden="true"><path d="M15 18l-6-6 6-6"/></svg>';

    var nextBtn = document.createElement('button');
    nextBtn.type = 'button';
    nextBtn.className = 'apex-gallery-main-arrow apex-gallery-main-arrow--next';
    nextBtn.setAttribute('aria-label', 'Next image');
    nextBtn.innerHTML = '<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" aria-hidden="true"><path d="M9 18l6-6-6-6"/></svg>';

    shell.appendChild(prevBtn);
    shell.appendChild(nextBtn);

    prevBtn.addEventListener('click', function () {
      navigateGallerySlide(gallery, -1);
    });
    nextBtn.addEventListener('click', function () {
      navigateGallerySlide(gallery, 1);
    });
  }

  function initGalleryThumbStrip(gallery) {
    if (!gallery) {
      return;
    }

    unwrapGalleryThumbRail(gallery);

    if (gallery.querySelector('.apex-gallery-thumb-strip')) {
      bindGalleryThumbClicks(gallery);
      updateGalleryThumbStrip(gallery);
      return;
    }

    var nav = gallery.querySelector('.flex-control-nav');
    if (!nav) {
      return;
    }

    var strip = document.createElement('div');
    strip.className = 'apex-gallery-thumb-strip';

    var prevBtn = document.createElement('button');
    prevBtn.type = 'button';
    prevBtn.className = 'apex-gallery-thumb-arrow apex-gallery-thumb-arrow--prev';
    prevBtn.setAttribute('aria-label', 'Scroll thumbnails left');
    prevBtn.innerHTML = '<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" aria-hidden="true"><path d="M15 18l-6-6 6-6"/></svg>';

    var viewport = document.createElement('div');
    viewport.className = 'apex-gallery-thumb-viewport';

    var nextBtn = document.createElement('button');
    nextBtn.type = 'button';
    nextBtn.className = 'apex-gallery-thumb-arrow apex-gallery-thumb-arrow--next';
    nextBtn.setAttribute('aria-label', 'Scroll thumbnails right');
    nextBtn.innerHTML = '<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" aria-hidden="true"><path d="M9 18l6-6-6-6"/></svg>';

    gallery.appendChild(strip);
    strip.appendChild(prevBtn);
    strip.appendChild(viewport);
    viewport.appendChild(nav);
    strip.appendChild(nextBtn);

    prevBtn.addEventListener('click', function () {
      scrollGalleryThumbStrip(gallery, -1);
    });
    nextBtn.addEventListener('click', function () {
      scrollGalleryThumbStrip(gallery, 1);
    });

    setGalleryThumbOffset(gallery, 0);
    bindGalleryThumbClicks(gallery);
    updateGalleryThumbStrip(gallery);
  }

  function ensureGalleryOverlays(gallery, shell) {
    if (!gallery || !shell) {
      return;
    }

    var overlayLayer = shell.querySelector('.apex-gallery-overlays');
    if (!overlayLayer) {
      overlayLayer = document.createElement('div');
      overlayLayer.className = 'apex-gallery-overlays';
      shell.appendChild(overlayLayer);
    }

    var expandBtn = gallery.querySelector('.apex-gallery-expand') || shell.querySelector('.apex-gallery-expand');
    if (!expandBtn) {
      expandBtn = document.createElement('button');
      expandBtn.type = 'button';
      expandBtn.className = 'apex-gallery-expand';
      expandBtn.setAttribute('aria-label', 'Open image gallery');
      expandBtn.innerHTML = '<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M8 3H5a2 2 0 0 0-2 2v3m18 0V5a2 2 0 0 0-2-2h-3m0 18h3a2 2 0 0 0 2-2v-3M3 16v3a2 2 0 0 0 2 2h3"/></svg>';
      expandBtn.addEventListener('click', function (event) {
        event.preventDefault();
        var trigger = gallery.querySelector('.woocommerce-product-gallery__trigger');
        if (trigger) {
          trigger.click();
          return;
        }

        var activeLink = gallery.querySelector('.woocommerce-product-gallery__image.flex-active-slide a');
        if (!activeLink) {
          activeLink = gallery.querySelector('.woocommerce-product-gallery__image a');
        }
        if (activeLink) {
          activeLink.click();
        }
      });
    }

    if (!overlayLayer.contains(expandBtn)) {
      overlayLayer.appendChild(expandBtn);
    }
  }

  function scrollGalleryThumbStrip(gallery, direction) {
    var items = getGalleryThumbItems(gallery);
    var metrics = getThumbMetrics(gallery);
    var maxOffset = Math.max(0, items.length - metrics.visible);
    var offset = getGalleryThumbOffset(gallery) + direction;

    offset = Math.max(0, Math.min(maxOffset, offset));
    setGalleryThumbOffset(gallery, offset);
    updateGalleryThumbStrip(gallery, { preserveOffset: true });
  }

  function updateGalleryMainArrows(gallery) {
    var shell = gallery ? getGalleryMainShell(gallery) : null;
    if (!shell) {
      return;
    }

    var items = getGalleryThumbItems(gallery);
    var prevBtn = shell.querySelector('.apex-gallery-main-arrow--prev');
    var nextBtn = shell.querySelector('.apex-gallery-main-arrow--next');
    var show = items.length > 1;

    if (prevBtn) {
      prevBtn.style.display = show ? 'inline-flex' : 'none';
      prevBtn.disabled = !show;
      prevBtn.style.pointerEvents = show ? 'auto' : 'none';
    }
    if (nextBtn) {
      nextBtn.style.display = show ? 'inline-flex' : 'none';
      nextBtn.disabled = !show;
      nextBtn.style.pointerEvents = show ? 'auto' : 'none';
    }
  }

  function updateGalleryThumbStrip(gallery, options) {
    options = options || {};

    if (!gallery) {
      return;
    }

    var strip = gallery.querySelector('.apex-gallery-thumb-strip');
    var nav = gallery.querySelector('.flex-control-nav');
    if (!strip || !nav) {
      return;
    }

    var items = getGalleryThumbItems(gallery);
    var metrics = getThumbMetrics(gallery);
    var maxOffset = Math.max(0, items.length - metrics.visible);
    var activeIndex = getActiveSlideIndex(gallery);
    var offset = getGalleryThumbOffset(gallery);

    if (!options.preserveOffset) {
      if (activeIndex < offset) {
        offset = activeIndex;
      } else if (activeIndex >= offset + metrics.visible) {
        offset = activeIndex - metrics.visible + 1;
      }

      offset = Math.max(0, Math.min(maxOffset, offset));
      setGalleryThumbOffset(gallery, offset);
    }

    nav.style.transform = 'translateX(-' + (offset * (metrics.size + metrics.gap)) + 'px)';

    var viewport = strip.querySelector('.apex-gallery-thumb-viewport');
    var visibleSlots = items.length > metrics.visible ? metrics.visible : Math.min(items.length, metrics.visible);
    var viewportWidth = (visibleSlots * metrics.size) + (Math.max(0, visibleSlots - 1) * metrics.gap);

    if (viewport) {
      viewport.style.width = viewportWidth + 'px';
    }

    var prevBtn = strip.querySelector('.apex-gallery-thumb-arrow--prev');
    var nextBtn = strip.querySelector('.apex-gallery-thumb-arrow--next');
    var showArrows = items.length > metrics.visible;

    strip.classList.toggle('is-static', !showArrows);

    if (prevBtn) {
      prevBtn.disabled = !showArrows || offset <= 0;
    }
    if (nextBtn) {
      nextBtn.disabled = !showArrows || offset >= maxOffset;
    }

    updateGalleryMainArrows(gallery);
  }

  function initSingleProductGallery() {
    syncSingleProductGallery();
  }

  initSingleProductGallery();
  window.setTimeout(initSingleProductGallery, 300);
  window.setTimeout(syncSingleProductGallery, 800);
  window.setTimeout(syncSingleProductGallery, 1500);
  window.addEventListener('resize', syncSingleProductGallery);
  window.addEventListener('load', syncSingleProductGallery);

  if (window.jQuery) {
    window.jQuery(document.body).on('wc-product-gallery-after-init', '.woocommerce-product-gallery', syncSingleProductGallery);
  }

  function parsePaypalAmount(value) {
    if (typeof value !== 'string') {
      return 0;
    }

    var cleaned = value.replace(/[^0-9.,]/g, '').replace(/,/g, '');
    var amount = parseFloat(cleaned);
    return isNaN(amount) ? 0 : amount;
  }

  function formatPaypalMoney(amount) {
    return '$' + amount.toLocaleString('en-US', {
      minimumFractionDigits: 2,
      maximumFractionDigits: 2
    });
  }

  function buildPaypalPlans(amount) {
    var plans = [
      { months: 6, factor: 1.0771875 },
      { months: 12, factor: 1.146356875 },
      { months: 24, factor: 1.292990625 }
    ];

    return plans.map(function (plan) {
      var total = amount * plan.factor;
      var monthly = total / plan.months;
      var interest = total - amount;

      return {
        months: plan.months,
        monthly: monthly,
        total: total,
        interest: interest,
        apr: amount > 0 ? 26 : 0
      };
    });
  }

  function renderPaypalPlans(container, amount) {
    if (!container) {
      return;
    }

    container.innerHTML = buildPaypalPlans(amount).map(function (plan) {
      return ''
        + '<div class="apex-paypal-plan">'
        + '<p class="apex-paypal-plan-headline">' + formatPaypalMoney(plan.monthly) + '/mo. for ' + plan.months + ' months</p>'
        + '<div class="apex-paypal-plan-meta">'
        + '<div class="apex-paypal-plan-meta-item"><span class="apex-paypal-plan-meta-label">Est. APR*</span><span class="apex-paypal-plan-meta-value">' + plan.apr + '%</span></div>'
        + '<div class="apex-paypal-plan-meta-item"><span class="apex-paypal-plan-meta-label">Interest</span><span class="apex-paypal-plan-meta-value">' + formatPaypalMoney(plan.interest) + '</span></div>'
        + '<div class="apex-paypal-plan-meta-item"><span class="apex-paypal-plan-meta-label">Total</span><span class="apex-paypal-plan-meta-value">' + formatPaypalMoney(plan.total) + '</span></div>'
        + '</div>'
        + '</div>';
    }).join('');
  }

  var paypalModal = document.getElementById('apex-paypal-monthly-modal');
  var paypalOpeners = document.querySelectorAll('[data-paypal-monthly-open]');
  var paypalAmountInput = paypalModal ? paypalModal.querySelector('[data-paypal-amount-input]') : null;
  var paypalPlansWrap = paypalModal ? paypalModal.querySelector('[data-paypal-plans]') : null;
  var paypalLastFocus = null;

  function openPaypalModal() {
    if (!paypalModal) {
      return;
    }

    paypalLastFocus = document.activeElement;
    paypalModal.hidden = false;
    paypalModal.setAttribute('aria-hidden', 'false');
    document.body.classList.add('apex-paypal-modal-open');

    var amount = paypalAmountInput
      ? parsePaypalAmount(paypalAmountInput.dataset.paypalAmountRaw || paypalAmountInput.value)
      : 0;

    if (paypalAmountInput && amount > 0) {
      paypalAmountInput.value = formatPaypalMoney(amount);
    }

    renderPaypalPlans(paypalPlansWrap, amount);

    var dialog = paypalModal.querySelector('.apex-paypal-modal-dialog');
    if (dialog) {
      dialog.focus();
    }
  }

  function closePaypalModal() {
    if (!paypalModal) {
      return;
    }

    paypalModal.hidden = true;
    paypalModal.setAttribute('aria-hidden', 'true');
    document.body.classList.remove('apex-paypal-modal-open');

    if (paypalLastFocus && typeof paypalLastFocus.focus === 'function') {
      paypalLastFocus.focus();
    }
  }

  paypalOpeners.forEach(function (button) {
    button.addEventListener('click', openPaypalModal);
  });

  if (paypalModal) {
    paypalModal.querySelectorAll('[data-paypal-modal-close]').forEach(function (node) {
      node.addEventListener('click', closePaypalModal);
    });

    document.addEventListener('keydown', function (event) {
      if (event.key === 'Escape' && !paypalModal.hidden) {
        closePaypalModal();
      }
    });
  }

  if (paypalAmountInput && paypalPlansWrap) {
    var syncPaypalPlans = function () {
      var amount = parsePaypalAmount(paypalAmountInput.value);
      renderPaypalPlans(paypalPlansWrap, amount);
    };

    paypalAmountInput.addEventListener('input', syncPaypalPlans);
    paypalAmountInput.addEventListener('blur', function () {
      var amount = parsePaypalAmount(paypalAmountInput.value);
      if (amount > 0) {
        paypalAmountInput.value = formatPaypalMoney(amount);
      }
      syncPaypalPlans();
    });
  }

  document.querySelectorAll('.apex-shop-price-filter').forEach(function (form) {
    var minInput = form.querySelector('input[name="min_price"]');
    var maxInput = form.querySelector('input[name="max_price"]');
    var minRange = form.querySelector('.apex-shop-price-slider-min');
    var maxRange = form.querySelector('.apex-shop-price-slider-max');
    var rangeFill = form.querySelector('.apex-shop-price-slider-range');
    var minLabel = form.querySelector('[data-price-min-display]');
    var maxLabel = form.querySelector('[data-price-max-display]');

    if (!minInput || !maxInput || !minRange || !maxRange || !rangeFill) {
      return;
    }

    var floor = parseFloat(form.dataset.priceFloor || minRange.min || '0');
    var ceiling = parseFloat(form.dataset.priceCeiling || maxRange.max || '0');
    var step = parseFloat(form.dataset.priceStep || minRange.step || '1');
    var currency = form.dataset.currencySymbol || '£';

    function formatShopPrice(value) {
      var amount = Math.round(parseFloat(value) || 0);
      return currency + ' ' + amount.toLocaleString('en-GB');
    }

    function clampValues() {
      var minVal = parseFloat(minRange.value);
      var maxVal = parseFloat(maxRange.value);

      if (minVal > maxVal - step) {
        if (form.activeSlider === minRange) {
          minVal = maxVal - step;
        } else {
          maxVal = minVal + step;
        }
      }

      minVal = Math.max(floor, Math.min(minVal, ceiling));
      maxVal = Math.max(floor + step, Math.min(maxVal, ceiling));

      minRange.value = String(minVal);
      maxRange.value = String(maxVal);
      return { minVal: minVal, maxVal: maxVal };
    }

    function syncHiddenInputs(minVal, maxVal) {
      minInput.value = minVal <= floor ? '' : String(Math.round(minVal));
      maxInput.value = maxVal >= ceiling ? '' : String(Math.round(maxVal));
    }

    function updatePriceUI() {
      var values = clampValues();
      var span = ceiling - floor || 1;
      var start = ((values.minVal - floor) / span) * 100;
      var end = ((values.maxVal - floor) / span) * 100;

      rangeFill.style.left = start + '%';
      rangeFill.style.width = Math.max(0, end - start) + '%';

      if (minLabel) {
        minLabel.textContent = formatShopPrice(values.minVal);
      }
      if (maxLabel) {
        maxLabel.textContent = formatShopPrice(values.maxVal);
      }

      syncHiddenInputs(values.minVal, values.maxVal);
    }

    minRange.addEventListener('input', function () {
      form.activeSlider = minRange;
      updatePriceUI();
    });

    maxRange.addEventListener('input', function () {
      form.activeSlider = maxRange;
      updatePriceUI();
    });

    minInput.addEventListener('change', function () {
      if (minInput.value !== '') {
        minRange.value = minInput.value;
      } else {
        minRange.value = String(floor);
      }
      updatePriceUI();
    });

    maxInput.addEventListener('change', function () {
      if (maxInput.value !== '') {
        maxRange.value = maxInput.value;
      } else {
        maxRange.value = String(ceiling);
      }
      updatePriceUI();
    });

    updatePriceUI();
  });

  if (document.body.classList.contains('elite-cart-page')) {
    var cartUpdateTimer = null;

    function submitCartUpdate() {
      var form = document.querySelector('.apex-cart-form');
      var updateBtn = form ? form.querySelector('[name="update_cart"]') : null;
      if (!form || !updateBtn) {
        return;
      }

      if (typeof form.requestSubmit === 'function') {
        form.requestSubmit(updateBtn);
      } else {
        updateBtn.click();
      }
    }

    function queueCartUpdate() {
      if (window.innerWidth > 767) {
        return;
      }

      window.clearTimeout(cartUpdateTimer);
      cartUpdateTimer = window.setTimeout(submitCartUpdate, 450);
    }

    document.addEventListener('click', function (event) {
      var button = event.target.closest('.apex-cart-qty-btn');
      if (!button) {
        return;
      }

      event.preventDefault();
      var wrap = button.closest('.apex-checkout-qty-wrap');
      if (!wrap) {
        return;
      }

      var input = wrap.querySelector('.apex-cart-qty');
      if (!input) {
        return;
      }

      var value = parseInt(input.value, 10) || 0;
      if (button.getAttribute('data-action') === 'minus') {
        value = Math.max(0, value - 1);
      } else {
        value += 1;
      }

      input.value = String(value);
      queueCartUpdate();
    });

    document.addEventListener('change', function (event) {
      if (!event.target.matches('.apex-cart-qty')) {
        return;
      }
      queueCartUpdate();
    });
  }

  if (document.body.classList.contains('elite-checkout-page')) {
    function eliteKeepPaymentRadiosVisible() {
      document.querySelectorAll('.apex-checkout-order-card #payment input[name="payment_method"]').forEach(function (input) {
        input.style.setProperty('display', 'inline-block', 'important');
        input.style.setProperty('opacity', '1', 'important');
        input.style.setProperty('visibility', 'visible', 'important');
      });
    }

    eliteKeepPaymentRadiosVisible();

    if (window.jQuery) {
      window.jQuery(document.body).on('updated_checkout init_checkout', eliteKeepPaymentRadiosVisible);
    }
  }

  if (document.body.classList.contains('elite-checkout-page') && window.eliteCheckout) {
    var checkoutQtyTimer = null;

    function syncCheckoutQuantity(input, quantity) {
      var key = input.getAttribute('data-key');
      if (!key) {
        return;
      }

      window.clearTimeout(checkoutQtyTimer);
      checkoutQtyTimer = window.setTimeout(function () {
        var body = new window.FormData();
        body.append('action', 'elite_checkout_update_qty');
        body.append('nonce', window.eliteCheckout.nonce);
        body.append('cart_key', key);
        body.append('quantity', String(quantity));

        window.fetch(window.eliteCheckout.ajaxUrl, {
          method: 'POST',
          credentials: 'same-origin',
          body: body
        }).then(function (response) {
          return response.json();
        }).then(function () {
          if (window.jQuery) {
            window.jQuery(document.body).trigger('update_checkout');
          } else {
            window.location.reload();
          }
        });
      }, 350);
    }

    document.addEventListener('click', function (event) {
      var button = event.target.closest('.apex-checkout-qty-btn');
      if (!button) {
        return;
      }

      event.preventDefault();
      var wrap = button.closest('.apex-checkout-qty-wrap');
      if (!wrap) {
        return;
      }

      var input = wrap.querySelector('.apex-checkout-qty');
      if (!input) {
        return;
      }

      var value = parseInt(input.value, 10) || 0;
      if (button.getAttribute('data-action') === 'minus') {
        value = Math.max(0, value - 1);
      } else {
        value += 1;
      }

      input.value = String(value);
      syncCheckoutQuantity(input, value);
    });

    document.addEventListener('change', function (event) {
      var input = event.target.closest('.apex-checkout-qty');
      if (!input) {
        return;
      }

      var value = parseInt(input.value, 10) || 0;
      syncCheckoutQuantity(input, value);
    });
  }

  if (document.body.classList.contains('elite-single-product-page')) {
    var expressSelectors = [
      '.payment_request_button_container',
      '#wc-stripe-express-checkout-element'
    ];

    function relocateProductExpressCheckout() {
      var slot = document.querySelector('[data-express-checkout-slot="true"]');
      var summary = document.querySelector('.elite-single-product .summary, .summary.entry-summary');
      if (!slot || !summary) {
        return;
      }

      expressSelectors.forEach(function (selector) {
        summary.querySelectorAll(selector).forEach(function (node) {
          if (slot.contains(node) || node.closest('.apex-single-trust-box')) {
            return;
          }
          slot.appendChild(node);
        });
      });
    }

    relocateProductExpressCheckout();

    window.setTimeout(relocateProductExpressCheckout, 500);
    window.setTimeout(relocateProductExpressCheckout, 1500);
    window.setTimeout(relocateProductExpressCheckout, 3000);

    if (typeof MutationObserver !== 'undefined') {
      var summary = document.querySelector('.elite-single-product .summary, .summary.entry-summary');
      if (summary) {
        var observer = new MutationObserver(function () {
          relocateProductExpressCheckout();
        });
        observer.observe(summary, { childList: true, subtree: true });
        window.setTimeout(function () {
          observer.disconnect();
        }, 8000);
      }
    }
  }

  /* My Account: Login / Register toggle */
  var authRoot = document.getElementById('customer_login');
  if (authRoot && authRoot.classList.contains('apex-account-auth--toggle')) {
    var authButtons = authRoot.querySelectorAll('[data-auth-panel].apex-account-auth-toggle-btn');
    var authPanels = authRoot.querySelectorAll('.apex-account-auth-panel[data-auth-panel]');

    function setAuthPanel(panelName) {
      authRoot.setAttribute('data-active', panelName);

      authButtons.forEach(function (btn) {
        var isActive = btn.getAttribute('data-auth-panel') === panelName;
        btn.classList.toggle('is-active', isActive);
        btn.setAttribute('aria-selected', isActive ? 'true' : 'false');
      });

      authPanels.forEach(function (panel) {
        var isActive = panel.getAttribute('data-auth-panel') === panelName;
        panel.classList.toggle('is-active', isActive);
        if (isActive) {
          panel.removeAttribute('hidden');
        } else {
          panel.setAttribute('hidden', '');
        }
      });
    }

    authButtons.forEach(function (btn) {
      btn.addEventListener('click', function () {
        setAuthPanel(btn.getAttribute('data-auth-panel'));
      });
    });
  }
});
