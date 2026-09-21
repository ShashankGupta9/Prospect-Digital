/* ==========================================================================
   Prospect Digital — main scripts (vanilla JS, no dependencies)
   --------------------------------------------------------------------------
   1. Sticky header & scroll progress
   2. Mobile navigation drawer (focus trap, ESC, backdrop)
   3. Mega-menu toggles for touch / small screens
   4. Scroll reveal animations
   5. Back-to-top button
   6. Enquiry form enhancements
   7. Marquee helper
   Everything degrades gracefully: with JavaScript disabled the site is still
   fully navigable because the markup uses real links, real buttons and
   <details> for the FAQ.
   ========================================================================== */
(function () {
  'use strict';

  var reduceMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

  /* requestAnimationFrame with a setTimeout fallback, so throttled handlers
     keep working even in environments where it is unavailable. */
  var raf = (typeof window.requestAnimationFrame === 'function')
    ? function (cb) { return window.requestAnimationFrame(cb); }
    : function (cb) { return window.setTimeout(cb, 16); };

  /* ----------------------------------------------------------------------
     1. STICKY HEADER & SCROLL PROGRESS
     ---------------------------------------------------------------------- */
  function initHeader() {
    var header = document.getElementById('siteHeader');
    var bar = document.getElementById('scrollProgress');
    if (!header) { return; }

    var ticking = false;

    function update() {
      var y = window.pageYOffset || document.documentElement.scrollTop;
      header.classList.toggle('is-stuck', y > 8);

      if (bar) {
        var doc = document.documentElement;
        var max = doc.scrollHeight - window.innerHeight;
        var pct = max > 0 ? Math.min(100, Math.max(0, (y / max) * 100)) : 0;
        bar.style.width = pct + '%';
      }
      ticking = false;
    }

    window.addEventListener('scroll', function () {
      if (!ticking) {
        raf(update);
        ticking = true;
      }
    }, { passive: true });

    update();
  }

  /* ----------------------------------------------------------------------
     2. MOBILE NAVIGATION DRAWER
     ---------------------------------------------------------------------- */
  function initNav() {
    var nav = document.getElementById('primaryNav');
    var toggle = document.getElementById('navToggle');
    var backdrop = document.getElementById('navBackdrop');
    var mq = window.matchMedia('(min-width: 992px)');
    if (!nav || !toggle) { return; }

    var lastFocused = null;

    /* Which focusable elements are genuinely visible inside the drawer.
       NOTE: offsetParent is null for anything inside a position:fixed element
       (which the drawer is), so visibility is tested with getClientRects(). */
    function isVisible(el) {
      if (el.hasAttribute('disabled') || el.getAttribute('aria-hidden') === 'true') { return false; }
      if (!el.getClientRects().length) { return false; }
      if (el.closest('[hidden]')) { return false; }
      var style = window.getComputedStyle(el);
      return style.visibility !== 'hidden' && style.display !== 'none';
    }

    function focusables() {
      return Array.prototype.filter.call(
        nav.querySelectorAll('a[href], button:not([disabled]), input, select, textarea, [tabindex]:not([tabindex="-1"])'),
        isVisible
      );
    }

    function openNav() {
      lastFocused = document.activeElement;
      nav.classList.add('is-open');
      toggle.setAttribute('aria-expanded', 'true');
      toggle.setAttribute('aria-label', 'Close navigation menu');
      document.body.classList.add('is-locked');

      // Reset all submenus so all items (Services, Products, Work, Store, About, Contact) are visible
      var submenus = nav.querySelectorAll('.site-nav__item--has-menu');
      Array.prototype.forEach.call(submenus, function (sub) {
        sub.setAttribute('data-open', 'false');
        var btn = sub.querySelector('.site-nav__toggle');
        if (btn) { btn.setAttribute('aria-expanded', 'false'); }
        var p = sub.querySelector('.mega-menu');
        if (p) { p.hidden = true; }
      });

      if (backdrop) {
        backdrop.hidden = false;
        raf(function () { backdrop.classList.add('is-visible'); });
      }
      toggle.classList.add('is-active');
    }

    function closeNav(returnFocus) {
      nav.classList.remove('is-open');
      toggle.setAttribute('aria-expanded', 'false');
      toggle.setAttribute('aria-label', 'Open navigation menu');
      toggle.classList.remove('is-active');
      document.body.classList.remove('is-locked');
      if (backdrop) {
        backdrop.classList.remove('is-visible');
        window.setTimeout(function () { backdrop.hidden = true; }, 300);
      }
      if (returnFocus && lastFocused && typeof lastFocused.focus === 'function') {
        lastFocused.focus();
      }
    }

    function toggleNav(e) {
      if (e && e.preventDefault) { e.preventDefault(); }
      if (e && e.stopPropagation) { e.stopPropagation(); }
      if (toggle.getAttribute('aria-expanded') === 'true') {
        closeNav(true);
      } else {
        openNav();
      }
    }
    window.pdToggleNav = toggleNav;

    toggle.addEventListener('click', toggleNav);

    if (backdrop) {
      backdrop.addEventListener('click', function () { closeNav(true); });
    }

    // Close after choosing a destination
    nav.addEventListener('click', function (event) {
      var link = event.target.closest ? event.target.closest('a[href]') : null;
      if (link && !mq.matches) { closeNav(false); }
    });

    // Keyboard behaviour: ESC to close, Tab cycles inside the drawer
    document.addEventListener('keydown', function (event) {
      if (toggle.getAttribute('aria-expanded') !== 'true') { return; }
      if (event.key === 'Escape') { closeNav(true); return; }
      if (event.key !== 'Tab') { return; }

      var items = focusables();
      if (!items.length) { return; }
      var first = items[0];
      var last = items[items.length - 1];

      if (event.shiftKey && document.activeElement === first) {
        event.preventDefault(); last.focus();
      } else if (!event.shiftKey && document.activeElement === last) {
        event.preventDefault(); first.focus();
      }
    });

    // ============================================================
// Services / Products submenu toggle
// ============================================================

var submenuToggles = nav.querySelectorAll('.site-nav__toggle');

Array.prototype.forEach.call(submenuToggles, function (btn) {

  btn.addEventListener('click', function (event) {
    event.preventDefault();
    event.stopPropagation();

    var parent = btn.closest('.site-nav__item--has-menu');

    if (!parent) return;

    var menu = parent.querySelector('.mega-menu');

    if (!menu) return;

    var isOpen = parent.getAttribute('data-open') === 'true';

    // Close all other submenus
    Array.prototype.forEach.call(
      nav.querySelectorAll('.site-nav__item--has-menu'),
      function (other) {

        var otherBtn = other.querySelector('.site-nav__toggle');
        var otherMenu = other.querySelector('.mega-menu');

        other.setAttribute('data-open', 'false');

        if (otherBtn) {
          otherBtn.setAttribute('aria-expanded', 'false');
        }

        if (otherMenu) {
          otherMenu.hidden = true;
        }
      }
    );

    // Open clicked submenu if it was closed
    if (!isOpen) {
      parent.setAttribute('data-open', 'true');
      btn.setAttribute('aria-expanded', 'true');
      menu.hidden = false;
    }
  });

});

    // Reset state when the viewport grows into the desktop layout
    function handleViewport() {
      if (mq.matches && toggle.getAttribute('aria-expanded') === 'true') { closeNav(false); }
      if (mq.matches && backdrop) { backdrop.hidden = true; }
    }
    if (mq.addEventListener) { mq.addEventListener('change', handleViewport); }
    else if (mq.addListener) { mq.addListener(handleViewport); }
  }

  /* ----------------------------------------------------------------------
     3. MEGA-MENU TOGGLES (touch and small screens)
     ---------------------------------------------------------------------- */
  function initMegaMenus() {
    var items = document.querySelectorAll('.site-nav__item--has-menu');

    function isDesktop() {
      return window.matchMedia('(min-width: 992px)').matches;
    }

    function syncState() {
      var desktop = isDesktop();
      Array.prototype.forEach.call(items, function (item) {
        var panel = item.querySelector('.mega-menu');
        var button = item.querySelector('.site-nav__toggle');
        if (!panel) { return; }
        if (desktop) {
          panel.hidden = false;
        } else {
          var isOpen = item.getAttribute('data-open') === 'true';
          panel.hidden = !isOpen;
          if (button) {
            button.setAttribute('aria-expanded', isOpen ? 'true' : 'false');
          }
        }
      });
    }

    window.addEventListener('resize', syncState);

    Array.prototype.forEach.call(items, function (item) {
      var button = item.querySelector('.site-nav__toggle');
      var panel = item.querySelector('.mega-menu');
      if (!panel) { return; }

      var timeoutId = null;

      function setOpen(open) {
        item.setAttribute('data-open', open ? 'true' : 'false');
        if (button) {
          button.setAttribute('aria-expanded', open ? 'true' : 'false');
        }
        if (isDesktop()) {
          panel.hidden = false;
        } else {
          panel.hidden = !open;
        }
      }

      setOpen(false);

      if (button) {
        button.removeAttribute('hidden');
        button.addEventListener('click', function (e) {
          e.preventDefault();
          e.stopPropagation();
          var isOpen = item.getAttribute('data-open') === 'true';
          setOpen(!isOpen);
        });
      }

      // Smooth hover on desktop with subtle debounce
      item.addEventListener('mouseenter', function () {
        if (!isDesktop()) { return; }
        clearTimeout(timeoutId);
        setOpen(true);
      });

      item.addEventListener('mouseleave', function () {
        if (!isDesktop()) { return; }
        timeoutId = setTimeout(function () {
          setOpen(false);
        }, 120);
      });

      // Accessible keyboard navigation
      item.addEventListener('focusin', function () {
        if (!isDesktop()) { return; }
        clearTimeout(timeoutId);
        setOpen(true);
      });

      item.addEventListener('focusout', function (event) {
        if (!isDesktop()) { return; }
        if (!item.contains(event.relatedTarget)) {
          setOpen(false);
        }
      });
    });

    syncState();
  }

  /* ----------------------------------------------------------------------
     4. SCROLL REVEAL
     Two triggers, so content can never be stranded invisible:
       • IntersectionObserver, when the browser supports it (efficient);
       • a throttled scroll/resize sweep using element positions.
     Anything already on screen is revealed straight away. The animation is
     preserved for the rest of the page, and prefers-reduced-motion skips it.
     ---------------------------------------------------------------------- */
  function initReveal() {
    var nodes = Array.prototype.slice.call(document.querySelectorAll('[data-reveal]'));
    if (!nodes.length) { return; }

    function showAll() {
      nodes.forEach(function (n) { n.classList.add('is-visible'); });
      nodes = [];
    }

    if (reduceMotion || typeof IntersectionObserver !== 'function' || typeof window.requestAnimationFrame !== 'function') {
      showAll();
      return;
    }

    var pending = nodes;          // elements still waiting to be revealed

    function reveal(el) {
      el.classList.add('is-visible');
      pending = pending.filter(function (n) { return n !== el; });
    }

    /* Cheap position sweep: only looks at elements that are still hidden. */
    function sweep() {
      if (!pending.length) { return; }
      var vh = window.innerHeight || document.documentElement.clientHeight;
      pending.slice().forEach(function (el) {
        var box = el.getBoundingClientRect();
        // Reveal anything at or above the reveal line — including items the
        // visitor has already scrolled past, so nothing can stay invisible.
        if (box.top < vh - 40) { reveal(el); }
      });
    }

    var ticking = false;
    function onScroll() {
      if (ticking) { return; }
      ticking = true;
      raf(function () { sweep(); ticking = false; });
    }

    var observer = new IntersectionObserver(function (entries) {
      entries.forEach(function (entry) {
        if (entry.isIntersecting) {
          reveal(entry.target);
          observer.unobserve(entry.target);
        }
      });
    }, { rootMargin: '0px 0px -6% 0px', threshold: 0.05 });

    nodes.forEach(function (n) {
      var box = n.getBoundingClientRect();
      var vh = window.innerHeight || document.documentElement.clientHeight;
      if (box.top < vh && box.bottom > 0) {
        reveal(n);                       // already on screen: show without delay
      } else {
        observer.observe(n);
      }
    });

    window.addEventListener('scroll', onScroll, { passive: true });
    window.addEventListener('resize', onScroll, { passive: true });
    window.addEventListener('load', onScroll);
    sweep();
  }

  /* ----------------------------------------------------------------------
     5. BACK TO TOP
     ---------------------------------------------------------------------- */
  function initToTop() {
    var button = document.getElementById('toTop');
    if (!button) { return; }

    var ticking = false;
    function update() {
      var y = window.pageYOffset || document.documentElement.scrollTop;
      if (y > 600) { button.hidden = false; } else { button.hidden = true; }
      ticking = false;
    }
    window.addEventListener('scroll', function () {
      if (!ticking) { raf(update); ticking = true; }
    }, { passive: true });
    update();

    button.addEventListener('click', function () {
      window.scrollTo({ top: 0, behavior: reduceMotion ? 'auto' : 'smooth' });
      var main = document.getElementById('main');
      if (main) { main.focus({ preventScroll: true }); }
    });
  }

  /* ----------------------------------------------------------------------
     6. ENQUIRY FORM ENHANCEMENTS & CONTACT PAGE MICRO-INTERACTIONS
     ---------------------------------------------------------------------- */
  function initForms() {
    // 1. Copy Buttons (Ref code & Email)
    document.addEventListener('click', function (e) {
      var copyRefBtn = e.target.closest('#copyRefBtn');
      if (copyRefBtn) {
        var refTextEl = document.getElementById('enquiryRefText');
        if (refTextEl) {
          var ref = refTextEl.textContent.trim();
          if (navigator.clipboard) {
            navigator.clipboard.writeText(ref);
          }
          var orig = copyRefBtn.textContent;
          copyRefBtn.textContent = 'Copied!';
          copyRefBtn.style.background = '#059669';
          copyRefBtn.style.color = '#ffffff';
          setTimeout(function () {
            copyRefBtn.textContent = orig;
            copyRefBtn.style.background = '';
            copyRefBtn.style.color = '';
          }, 2200);
        }
      }

      var copyEmailBtn = e.target.closest('#copyEmailBtn');
      if (copyEmailBtn) {
        var email = copyEmailBtn.getAttribute('data-email');
        if (email && navigator.clipboard) {
          navigator.clipboard.writeText(email);
          var origText = copyEmailBtn.textContent;
          copyEmailBtn.textContent = 'Copied!';
          copyEmailBtn.style.background = '#0284c7';
          copyEmailBtn.style.color = '#ffffff';
          setTimeout(function () {
            copyEmailBtn.textContent = origText;
            copyEmailBtn.style.background = '';
            copyEmailBtn.style.color = '';
          }, 2200);
        }
      }
    });

    var forms = document.querySelectorAll('form[data-form]');

    Array.prototype.forEach.call(forms, function (form) {
      // 2. Interactive Service Selection Chips
      var serviceChips = form.querySelectorAll('.service-chip');
      var serviceSelect = form.querySelector('select[name="service"]');
      if (serviceChips.length && serviceSelect) {
        Array.prototype.forEach.call(serviceChips, function (chip) {
          chip.addEventListener('click', function () {
            var val = chip.getAttribute('data-service-value');
            serviceSelect.value = val;
            Array.prototype.forEach.call(serviceChips, function (c) {
              c.classList.remove('is-active');
              c.setAttribute('aria-pressed', 'false');
            });
            chip.classList.add('is-active');
            chip.setAttribute('aria-pressed', 'true');
            serviceSelect.removeAttribute('aria-invalid');
            var errorP = form.querySelector('#' + form.id + '-service-error');
            if (errorP) { errorP.style.display = 'none'; }
          });
        });
      }

      // 3. Interactive Budget Range Chips
      var budgetChips = form.querySelectorAll('.budget-chip');
      var budgetSelect = form.querySelector('select[name="budget"]');
      if (budgetChips.length && budgetSelect) {
        Array.prototype.forEach.call(budgetChips, function (chip) {
          chip.addEventListener('click', function () {
            var val = chip.getAttribute('data-budget-value');
            budgetSelect.value = val;
            Array.prototype.forEach.call(budgetChips, function (c) {
              c.classList.remove('is-active');
              c.setAttribute('aria-pressed', 'false');
            });
            chip.classList.add('is-active');
            chip.setAttribute('aria-pressed', 'true');
          });
        });
      }

      function showFormErrors(errors, fieldErrors) {
        // Remove existing error block if any
        var oldAlert = form.querySelector('.alert--error');
        if (oldAlert) {
          oldAlert.parentNode.removeChild(oldAlert);
        }

        // Highlight fields
        if (fieldErrors && typeof fieldErrors === 'object') {
          for (var fieldName in fieldErrors) {
            if (Object.prototype.hasOwnProperty.call(fieldErrors, fieldName)) {
              var input = form.querySelector('[name="' + fieldName + '"]');
              if (input) {
                input.setAttribute('aria-invalid', 'true');
              }
            }
          }
        }

        // Create alert element
        var alertBox = document.createElement('div');
        alertBox.className = 'alert alert--error';
        alertBox.setAttribute('role', 'alert');
        alertBox.setAttribute('tabindex', '-1');
        alertBox.style.marginBottom = '1.5rem';

        var title = document.createElement('p');
        title.className = 'alert__title';
        title.textContent = (errors.length === 1)
          ? 'One thing needs fixing:'
          : 'Please fix these ' + errors.length + ' items:';
        alertBox.appendChild(title);

        var list = document.createElement('ul');
        list.className = 'alert__list';
        errors.forEach(function (msg) {
          var li = document.createElement('li');
          li.textContent = msg;
          list.appendChild(li);
        });
        alertBox.appendChild(list);

        var chipsGroup = form.querySelector('.chips-group');
        if (chipsGroup) {
          form.insertBefore(alertBox, chipsGroup);
        } else {
          form.insertBefore(alertBox, form.firstChild);
        }

        alertBox.scrollIntoView({ block: 'center', behavior: reduceMotion ? 'auto' : 'smooth' });
        alertBox.focus();
      }

      form.addEventListener('submit', function (event) {
        // Client-side quick check
        var invalid = [];
        Array.prototype.forEach.call(form.querySelectorAll('[required]'), function (field) {
          var value = field.type === 'checkbox' ? field.checked : field.value.trim();
          if (!value) {
            invalid.push(field);
            field.setAttribute('aria-invalid', 'true');
          } else {
            field.removeAttribute('aria-invalid');
          }
        });

        var email = form.querySelector('input[type="email"]');
        if (email && email.value.trim() && !/^[^\s@]+@[^\s@]+\.[^\s@]{2,}$/.test(email.value.trim())) {
          if (invalid.indexOf(email) === -1) { invalid.push(email); }
          email.setAttribute('aria-invalid', 'true');
        }

        if (invalid.length) {
          event.preventDefault();
          var first = invalid[0];
          first.focus();
          first.scrollIntoView({ block: 'center', behavior: reduceMotion ? 'auto' : 'smooth' });
          return;
        }

        // Intercept with AJAX submission
        event.preventDefault();

        var submit = form.querySelector('button[type="submit"]');
        var label = submit ? submit.querySelector('.btn-text') || submit.querySelector('span') : null;
        var originalLabel = label ? label.textContent : '';

        if (submit) {
          submit.disabled = true;
          submit.setAttribute('aria-busy', 'true');
          if (label) { label.textContent = 'Sending Enquiry…'; }
        }

        var actionUrl = form.getAttribute('action') || window.location.href;
        var formData = new FormData(form);

        fetch(actionUrl, {
          method: 'POST',
          body: formData,
          headers: {
            'Accept': 'application/json',
            'X-Requested-With': 'XMLHttpRequest'
          }
        })
        .then(function (res) {
          return res.json().then(function (data) {
            return { ok: res.ok, status: res.status, data: data };
          }).catch(function () {
            return { ok: res.ok, status: res.status, data: null };
          });
        })
        .then(function (result) {
          if (result.ok && result.data && result.data.ok) {
            // Success! Render modern glass success card without jarring page reload
            var container = document.getElementById('enquiryFormContainer');
            var ref = result.data.reference || ('PD-' + Date.now().toString(36).toUpperCase());

            if (container) {
              container.innerHTML = [
                '<div class="enquiry-success-glass" id="enquiryResult" role="status" tabindex="-1">',
                  '<div class="enquiry-success__icon">✓</div>',
                  '<h2 class="enquiry-success__title">Enquiry Received!</h2>',
                  '<p class="enquiry-success__lead">',
                    'Thank you for reaching out. Our engineering team has received your project details and will review your requirements within one working day.',
                  '</p>',
                  '<div class="enquiry-ref-box">',
                    '<span style="font-size:0.85rem;color:var(--ink-soft)">Tracking Ref:</span>',
                    '<span class="enquiry-ref-code" id="enquiryRefText">' + ref + '</span>',
                    '<button type="button" class="btn-copy-ref" id="copyRefBtn" title="Copy Reference">Copy</button>',
                  '</div>',
                  '<div style="display:flex;align-items:center;justify-content:center;flex-wrap:wrap;gap:0.85rem">',
                    '<a class="btn btn--brand btn--sm" href="https://wa.me/917000127225" target="_blank" rel="noopener">Continue on WhatsApp</a>',
                    '<a class="btn btn--outline btn--sm" href="tel:+917000127225">Call +91 7000-12-7225</a>',
                  '</div>',
                '</div>'
              ].join('');
              container.scrollIntoView({ behavior: reduceMotion ? 'auto' : 'smooth', block: 'start' });
              if (history.replaceState) {
                history.replaceState(null, '', window.location.pathname + '?sent=1#enquiry');
              }
            } else {
              window.location.href = result.data.redirect || (window.location.pathname + '?sent=1#enquiry');
            }
          } else {
            // Restore button
            if (submit) {
              submit.disabled = false;
              submit.removeAttribute('aria-busy');
              if (label) { label.textContent = originalLabel || 'Send Enquiry Now'; }
            }

            var errors = (result.data && result.data.errors && result.data.errors.length)
              ? result.data.errors
              : ['Submission could not be completed. Please check your details or reach us directly via phone or WhatsApp.'];
            showFormErrors(errors, result.data ? result.data.field_errors : null);
          }
        })
        .catch(function () {
          // If fetch completely fails, fallback to native submit
          if (submit) {
            submit.disabled = false;
            submit.removeAttribute('aria-busy');
            if (label) { label.textContent = originalLabel || 'Send Enquiry Now'; }
          }
          form.submit();
        });
      });

      // Clear the invalid state as soon as the visitor starts fixing a field
      Array.prototype.forEach.call(form.querySelectorAll('.field__input, .modern-input, .modern-textarea, input[type="checkbox"]'), function (field) {
        field.addEventListener('input', function () {
          if (field.getAttribute('aria-invalid') === 'true') {
            field.removeAttribute('aria-invalid');
          }
        });
        field.addEventListener('change', function () {
          if (field.getAttribute('aria-invalid') === 'true') {
            field.removeAttribute('aria-invalid');
          }
        });
      });
    });
  }

  /* ----------------------------------------------------------------------
     7. MARQUEE — duplicate the track so the loop has no visible gap
     ---------------------------------------------------------------------- */
  function initMarquee() {
    var marquees = document.querySelectorAll('.marquee');
    Array.prototype.forEach.call(marquees, function (marquee) {
      var track = marquee.querySelector('.marquee__track');
      if (!track || marquee.querySelector('.marquee__track--clone')) { return; }
      var clone = track.cloneNode(true);
      clone.classList.add('marquee__track--clone');
      clone.setAttribute('aria-hidden', 'true');
      marquee.appendChild(clone);
    });
  }

  /* ----------------------------------------------------------------------
     8. STORE & E-COMMERCE INTERACTIONS
     ---------------------------------------------------------------------- */
  function initStore() {
    // 1. Product Image Gallery Switcher
    var mainImg = document.getElementById('productMainImg');
    var thumbBtns = document.querySelectorAll('.product-thumb-btn');

    if (mainImg && thumbBtns.length) {
      Array.prototype.forEach.call(thumbBtns, function (btn) {
        btn.addEventListener('click', function () {
          var fullSrc = btn.getAttribute('data-full-src');
          if (fullSrc) {
            mainImg.src = fullSrc;
          }
          Array.prototype.forEach.call(thumbBtns, function (b) {
            b.classList.remove('is-active');
          });
          btn.classList.add('is-active');
        });
      });
    }

    // 2. Product Detail Specs/Overview Tabs
    var tabBtns = document.querySelectorAll('.product-tab-btn');
    var tabPanels = document.querySelectorAll('.product-tab-panel');

    if (tabBtns.length && tabPanels.length) {
      Array.prototype.forEach.call(tabBtns, function (btn) {
        btn.addEventListener('click', function () {
          var targetId = btn.getAttribute('data-tab-target');
          if (!targetId) { return; }

          Array.prototype.forEach.call(tabBtns, function (b) {
            b.classList.remove('is-active');
            b.setAttribute('aria-selected', 'false');
          });
          Array.prototype.forEach.call(tabPanels, function (p) {
            p.classList.remove('is-active');
          });

          btn.classList.add('is-active');
          btn.setAttribute('aria-selected', 'true');
          var targetPanel = document.getElementById(targetId);
          if (targetPanel) {
            targetPanel.classList.add('is-active');
          }
        });
      });
    }

    // 3. Product Quantity Stepper (+ and - buttons)
    document.addEventListener('click', function (e) {
      var stepperBtn = e.target.closest ? e.target.closest('.qty-btn') : null;
      if (!stepperBtn) { return; }

      var stepperWrap = stepperBtn.closest('.product-qty-stepper') || stepperBtn.closest('.cart-qty-stepper');
      if (!stepperWrap) { return; }

      var input = stepperWrap.querySelector('.qty-input') || stepperWrap.querySelector('input[name="quantity"]');
      if (!input) { return; }

      var currentVal = parseInt(input.value, 10) || 1;
      var action = stepperBtn.getAttribute('data-action');
      var minVal = parseInt(input.getAttribute('min'), 10) || 1;
      var maxVal = parseInt(input.getAttribute('max'), 10) || 99;

      if (action === 'inc' && currentVal < maxVal) {
        input.value = currentVal + 1;
      } else if (action === 'dec' && currentVal > minVal) {
        input.value = currentVal - 1;
      }

      // If in cart table, submit the update form automatically
      var cartForm = stepperWrap.closest('form[data-cart-update-form]');
      if (cartForm) {
        cartForm.submit();
      }
    });

    // 4. Wishlist Button Interactive Heart Toggle
    document.addEventListener('click', function (e) {
      var wishBtn = e.target.closest ? e.target.closest('.store-wishlist-btn, .btn-wishlist-toggle') : null;
      if (!wishBtn) { return; }

      var form = wishBtn.closest('form[data-wishlist-form]');
      if (!form) {
        // Toggle active visual state
        wishBtn.classList.toggle('is-active');
      }
    });

    // 5. "Notify Me" Form Submission
    var notifyForms = document.querySelectorAll('[data-store-notify-form]');
    Array.prototype.forEach.call(notifyForms, function (form) {
      form.addEventListener('submit', function (e) {
        e.preventDefault();
        var input = form.querySelector('.store-notify-input');
        var btn = form.querySelector('.store-notify-btn');
        var msg = form.parentNode ? form.parentNode.querySelector('.store-notify-msg') : null;

        if (input && input.value.trim()) {
          if (btn) {
            btn.disabled = true;
            btn.textContent = 'Subscribed!';
          }
          if (msg) {
            msg.style.display = 'block';
          }
          input.value = '';
          input.disabled = true;
        }
      });
    });

    // 6. Checkout Payment Method Card Selector
    var paymentCards = document.querySelectorAll('.payment-method-card');
    if (paymentCards.length) {
      Array.prototype.forEach.call(paymentCards, function (card) {
        card.addEventListener('click', function () {
          Array.prototype.forEach.call(paymentCards, function (c) {
            c.classList.remove('is-selected');
          });
          card.classList.add('is-selected');
          var radio = card.querySelector('input[type="radio"]');
          if (radio) {
            radio.checked = true;
          }
        });
      });
    }
  }

  /* ----------------------------------------------------------------------
     BOOT
     ---------------------------------------------------------------------- */
  function boot() {
    // Each feature is isolated: if one throws (an unusual browser, a conflicting
    // plugin), the rest of the site keeps working. Scroll-reveal is listed last
    // so that a failure there can never affect navigation or the enquiry form.
    [initHeader, initNav, initMegaMenus, initForms, initStore, initToTop, initMarquee, initReveal]
      .forEach(function (init) {
        try {
          init();
        } catch (error) {
          if (window.console && console.warn) { console.warn('Prospect Digital: a script failed', error); }
        }
      });
  }

  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', boot);
  } else {
    boot();
  }
})();
