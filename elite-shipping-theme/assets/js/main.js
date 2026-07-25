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
});
