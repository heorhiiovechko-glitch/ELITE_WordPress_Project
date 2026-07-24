document.addEventListener('DOMContentLoaded', function () {
  var chat = document.querySelector('.elite-chat-launcher');
  if (chat) {
    chat.addEventListener('click', function () {
      if (window.Tawk_API && window.Tawk_API.maximize) {
        window.Tawk_API.maximize();
      }
    });
  }

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
      return isModTrack && isModsMobile() ? 2 : 1;
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

  function getGallerySlides(gallery) {
    if (!gallery) {
      return [];
    }

    return gallery.querySelectorAll(
      '.flex-viewport .woocommerce-product-gallery__image, .flex-viewport .slides > li, :scope > .woocommerce-product-gallery__wrapper > .woocommerce-product-gallery__image'
    );
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

  function getActiveThumbIndex(gallery) {
    var items = getGalleryThumbItems(gallery);
    var activeIndex = items.findIndex(function (item) {
      return item.classList.contains('flex-active');
    });

    return activeIndex >= 0 ? activeIndex : 0;
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

  function ensureGalleryMainShell(gallery, frame) {
    if (!gallery || !frame) {
      return null;
    }

    if (frame.closest('.apex-gallery-main')) {
      return frame.closest('.apex-gallery-main');
    }

    var shell = document.createElement('div');
    shell.className = 'apex-gallery-main';
    frame.parentNode.insertBefore(shell, frame);
    shell.appendChild(frame);

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

    var saleBadge = gallery.parentElement
      ? gallery.parentElement.querySelector('.apex-single-sale-badge')
      : null;
    if (!saleBadge) {
      saleBadge = document.querySelector('.elite-single-product .apex-single-sale-badge');
    }
    if (saleBadge && !overlayLayer.contains(saleBadge)) {
      overlayLayer.appendChild(saleBadge);
    }
  }

  function goToGallerySlide(gallery, index) {
    var items = getGalleryThumbItems(gallery);
    if (!items.length) {
      return;
    }

    var targetIndex = ((index % items.length) + items.length) % items.length;

    if (window.jQuery) {
      var galleryApi = window.jQuery(gallery).data('product_gallery');
      if (galleryApi && galleryApi.flexslider && typeof galleryApi.flexslider.flexAnimate === 'function') {
        galleryApi.flexslider.flexAnimate(targetIndex);
        window.requestAnimationFrame(function () {
          updateGalleryThumbStrip(gallery);
        });
        return;
      }
    }

    var targetImg = items[targetIndex].querySelector('img');
    if (targetImg) {
      targetImg.click();
    }

    window.setTimeout(function () {
      updateGalleryThumbStrip(gallery);
    }, 60);
  }

  function activateGalleryThumb(gallery, index) {
    goToGallerySlide(gallery, index);
  }

  function navigateGallerySlide(gallery, direction) {
    var items = getGalleryThumbItems(gallery);
    if (items.length <= 1) {
      return;
    }

    var nextIndex = (getActiveThumbIndex(gallery) + direction + items.length) % items.length;
    goToGallerySlide(gallery, nextIndex);
  }

  function scrollGalleryThumbStrip(gallery, direction) {
    var items = getGalleryThumbItems(gallery);
    var metrics = getThumbMetrics(gallery);
    var maxOffset = Math.max(0, items.length - metrics.visible);
    var offset = getGalleryThumbOffset(gallery) + direction;

    offset = Math.max(0, Math.min(maxOffset, offset));
    setGalleryThumbOffset(gallery, offset);
    updateGalleryThumbStrip(gallery);
  }

  function updateGalleryMainArrows(gallery) {
    var shell = gallery ? gallery.querySelector('.apex-gallery-main') : null;
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

  function updateGalleryThumbStrip(gallery) {
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
    var activeIndex = getActiveThumbIndex(gallery);
    var offset = getGalleryThumbOffset(gallery);

    if (activeIndex < offset) {
      offset = activeIndex;
    } else if (activeIndex >= offset + metrics.visible) {
      offset = activeIndex - metrics.visible + 1;
    }

    offset = Math.max(0, Math.min(maxOffset, offset));
    setGalleryThumbOffset(gallery, offset);

    nav.style.transform = 'translateX(-' + (offset * (metrics.size + metrics.gap)) + 'px)';

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

    var maxWidth = getGalleryMaxWidth();
    var shellWidth = shell ? shell.offsetWidth : gallery.offsetWidth;
    var width = Math.min(shellWidth, maxWidth);
    var height = Math.round(width * 2 / 3);

    frame.style.width = width + 'px';
    frame.style.height = height + 'px';

    getGallerySlides(gallery).forEach(function (slide) {
      slide.style.width = width + 'px';
      slide.style.height = height + 'px';
      slide.style.display = 'grid';
      slide.style.placeItems = 'center';
    });

    updateGalleryThumbStrip(gallery);

    if (window.jQuery) {
      var galleryApi = window.jQuery(gallery).data('product_gallery');
      if (galleryApi && galleryApi.flexslider && typeof galleryApi.flexslider.resize === 'function') {
        galleryApi.flexslider.resize();
      }
    }
  }

  function initSingleProductGallery() {
    var gallery = document.querySelector('.elite-single-product .woocommerce-product-gallery');
    if (!gallery) {
      return;
    }

    var frame = getGalleryFrame(gallery);
    if (!frame) {
      return;
    }

    var shell = ensureGalleryMainShell(gallery, frame);
    initGalleryMainArrows(gallery, shell);
    ensureGalleryOverlays(gallery, shell);
    initGalleryThumbStrip(gallery);
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
    window.jQuery(document.body).on('click', '.elite-single-product .flex-control-nav li', function () {
      var activeGallery = document.querySelector('.elite-single-product .woocommerce-product-gallery');
      window.setTimeout(function () {
        updateGalleryThumbStrip(activeGallery);
      }, 50);
      window.setTimeout(function () {
        updateGalleryThumbStrip(activeGallery);
      }, 250);
    });
  }
});
