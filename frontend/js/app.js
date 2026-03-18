/*------------------------------------------------------------------

[Table of contents]

1. General
2. Dialog
3. Infinite Scroll
4. Notification
5. Photo Browser
6. Picker
7. Preloader
8. Pull To Refresh
9. Range Slider
10. Toasts
11. Chat
12. Calendar
13. Onboarding
14. Swiper
15. Switch Theme

------------------------------------------------------------------*/

// 1. General

"use strict";

var $$ = Dom7;

// #region Debug logging - Hypothesis testing
var DEBUG_LOG_ENDPOINT = 'http://127.0.0.1:7242/ingest/3ba32230-ec0a-40a1-bfce-6b4197209b56';
function debugLog(hypothesisId, location, message, data) {
  // Disabled to prevent ERR_CONNECTION_REFUSED in development
  return;
}
// Debug tab navigation
$$(document).on('click', '.tab-link', function(e) {
  var href = $$(this).attr('href');
  debugLog('A', 'app.js:tab-click', 'Tab clicked', {href: href, active: $$(this).hasClass('tab-link-active')});
});

// #endregion

var app = new Framework7({
  el: "#app",
  name: "Yui",
  theme: "ios",
  iosTranslucentBars: false,
  iosTranslucentModals: false,
  touch: {
    tapHold: true,
    disableContextMenu: false,
    passiveListener: true,
  },
  tabs: {
    swipeable: true,
  },
  view: {
    browserHistory: true,
    browserHistoryAnimate: Framework7.device.ios ? false : true,
    main: true,
  },
  routes: [
    {
      path: '/service/',
      url: 'pages/pages/service.html',
    },
    {
      path: '/services/',
      url: 'pages/pages/services.html',
    },
    {
      path: '/orders/',
      url: 'pages/pages/orders.html',
    },
    // Profile Page
    {
      path: '/profile/',
      url: 'pages/pages/profile.html',
    },
    // Sign In Page
    {
      path: '/signin/',
      url: 'pages/pages/signin.html',
    },
    // Sign Up Page
    {
      path: '/signup/',
      url: 'pages/pages/signup.html',
    },
    // Cart Page
    {
      path: '/cart/',
      url: 'pages/pages/cart.html',
    },
  ],
});

// Initialize main view manually so app.views.main exists
app.views.create('.view-main', {
  browserHistoryInitialMatch: false
});

// ====================
// Browser History Sync - Fix Back Button Issue
// ====================

// Framework7 với hash navigation (#!) sẽ tự xử lý browser history
// Không cần thêm popstate handler vì F7 đã quản lý

// Get base URL for fetching pages - handles both dev and production
function getPageBaseUrl() {
  return '/app/';
}

// Helper function to navigate using Framework7 router
function navigateToPage(path) {
  if (!app || !app.views || !app.views.main) return;
  app.views.main.router.navigate(path);
}

// Helper function to go back using Framework7 router
function navigateBack() {
  if (!app || !app.views || !app.views.main) return;

  var router = app.views.main.router;
  if (router.history.length > 1) {
    router.back();
  } else {
    // Nếu không có history, chuyển về tab home
    switchTab('home', null);
  }
}

// ====================
// Tab Navigation
// ====================
var currentTab = 'home';
var pageCache = {};
var navigationHistory = ['home'];
var tabPages = {};
var tabScrollPositions = {};
var tabOrder = ['home', 'services', 'orders', 'profile'];
var tabIndexMap = {
  home: 0,
  services: 1,
  orders: 2,
  profile: 3
};
var tabUrlMap = {
  home: 'pages/pages/home.html',
  services: 'pages/pages/services.html',
  orders: 'pages/pages/orders.html',
  profile: 'pages/pages/profile.html'
};
var tabTransitionMs = 260;
var tabSwiper = null;

// Handle link clicks within page container
document.addEventListener('click', function(e) {
  var link = e.target.closest('a');
  if (!link) return;
  
  if (link.classList.contains('back')) {
    e.preventDefault();
    e.stopPropagation();
    goBack();
    return;
  }
  
  var href = link.getAttribute('href');
  if (!href) return;
  
  // Only handle internal links that start with /
  if (href.startsWith('/') && !href.startsWith('//')) {
    var path = href.split('?')[0];

    // Handle tab-level routes (custom swiper handle)
    var tabMap = {
      '/': 'home',
      '/services/': 'services',
      '/orders/': 'orders',
      '/profile/': 'profile'
    };
    
    if (tabMap[path]) {
      e.preventDefault();
      e.stopPropagation();
      switchTab(tabMap[path], null);
    } else {
      // For other routes like /service/, /cart/, /signin/, use F7 explicit navigation
      e.preventDefault();
      e.stopPropagation();
      if (app && app.views && app.views.main) {
        app.views.main.router.navigate(href);
      }
    }
  }
});

// Go back to previous page
function goBack() {
  // Keep navigation store in sync
  if (window.NavigationStore) {
    window.NavigationStore.back();
  }

  if (app && app.views && app.views.main) {
    var router = app.views.main.router;

    // Use Framework7's back method - it handles URL automatically
    if (router.history.length > 1) {
      router.back();
    } else if (router.history.length <= 1) {
      // If history is 1 or 0, force remove the F7 page to reveal tabs underneath
      var $page = $$('.page-current');
      if ($page.length > 0) {
        // Apply slide-out animation
        $page.css('transition-duration', '400ms');
        $page.css('transform', 'translate3d(100%, 0, 0)');

        setTimeout(function() {
          $page.remove();
        }, 400);

        // Redirect effectively by manually setting router bounds and firing tab switch
        router.history = [];
        router.url = '';

        // Show tab bar
        var tabBarWrap = document.querySelector('.vs-tab-bar-wrap');
        if (tabBarWrap) {
          tabBarWrap.style.transition = 'opacity 400ms';
          tabBarWrap.style.opacity = '1';
          tabBarWrap.style.display = '';
        }
        if (typeof switchTab === 'function') switchTab('home', null);
      } else {
        router.history = [];
        router.url = '';
        if (typeof switchTab === 'function') switchTab('home', null);
      }
    }
  }
}

function loadServicePage(serviceId) {
  var pageContainer = document.getElementById('page-container');
  if (!pageContainer) return;

  // Hide tab bar on detail page
  var tabBarWrap = document.querySelector('.vs-tab-bar-wrap');
  if (tabBarWrap) tabBarWrap.style.display = 'none';

  // Prepend base URL to handle routing from subdirectories
  var fullUrl = getPageBaseUrl() + 'pages/pages/service.html';

  app.preloader.show();

  fetch(fullUrl)
    .then(function(response) { return response.text(); })
    .then(function(html) {
      pageContainer.innerHTML = html;
      app.preloader.hide();
      initServicePage(serviceId);
    })
    .catch(function(error) {
      app.preloader.hide();
      app.dialog.alert('Không thể tải trang dịch vụ.', 'Lỗi');
      console.error('loadServicePage error:', error);
    });
}

function loadPage(url) {
  // Thay vì loadPage, sử dụng F7 router để điều hướng.
  // Đảm bảo URL route khớp với cấu hình trong routes
  if (app && app.views && app.views.main) {
    // Chuyển đổi từ URL file thành route path nếu cần
    var path = url;
    if (url.includes('signin.html')) path = '/signin/';
    else if (url.includes('signup.html')) path = '/signup/';
    else if (url.includes('service.html')) path = '/service/';
    else if (url.includes('cart.html')) path = '/cart/';
    
    app.views.main.router.navigate(path);
  }
}

function afterPageLoaded(url, pageContainer) {
  if (!pageContainer) return;

  if (url.includes('signin.html') || url.includes('signup.html')) {
    initPasswordToggles(pageContainer);
    initAuthForms(pageContainer);
  }
}

function initPasswordToggles(root) {
  if (!root) return;

  var toggles = root.querySelectorAll('.js-toggle-password');
  toggles.forEach(function(toggleEl) {
    if (toggleEl.dataset && toggleEl.dataset.bound === '1') return;

    var wrapper = toggleEl.closest('div') || root;
    var passwordInput =
      wrapper.querySelector('.js-password-input') ||
      wrapper.querySelector('input[type="password"]') ||
      wrapper.querySelector('input[name="password"]');

    if (!passwordInput) return;

    function setVisible(visible) {
      passwordInput.type = visible ? 'text' : 'password';
      toggleEl.textContent = visible ? 'eye' : 'eye_slash';
      toggleEl.setAttribute('aria-label', visible ? 'Ẩn mật khẩu' : 'Hiện mật khẩu');
    }

    function toggle() {
      setVisible(passwordInput.type === 'password');
    }

    toggleEl.addEventListener('click', function(evt) {
      evt.preventDefault();
      toggle();
    });

    toggleEl.addEventListener('keydown', function(evt) {
      if (evt.key === 'Enter' || evt.key === ' ') {
        evt.preventDefault();
        toggle();
      }
    });

    if (toggleEl.dataset) toggleEl.dataset.bound = '1';
  });
}

// Setup generic delegate for auth forms
$$(document).on('submit', '#login-form', function(evt) {
  evt.preventDefault();
  var signinForm = evt.target;
  var fd = new FormData(signinForm);
  var email = fd.get('email');
  var password = fd.get('password');

  app.dialog.preloader('Đang đăng nhập...');
  window.AuthApi.login({ email: email, password: password })
    .then(function(res) {
      app.dialog.close();
      var user = res?.data?.user || res?.data?.data?.user || res?.data?.user;
      var token = res?.data?.token || res?.data?.data?.token || res?.data?.token;
      if (res && res.status && res.data) {
        user = res.data.user;
        token = res.data.token;
      }
      if (!token) {
        app.dialog.alert('Không nhận được token đăng nhập.', 'Lỗi');
        return;
      }
      window.AuthStore.setAuth(user || null, token);
      
      // Clear F7 router state and switch to home tab
      app.views.main.router.history = [];
      app.views.main.router.url = '';
      switchTab('home', null);
    })
    .catch(function(err) {
      app.dialog.close();
      app.dialog.alert(err?.data?.message || err?.message || 'Đăng nhập thất bại.', 'Lỗi');
    });
});

$$(document).on('submit', '#signup-form', function(evt) {
  evt.preventDefault();
  var signupForm = evt.target;
  var fd = new FormData(signupForm);
  var payload = {
    name: fd.get('name'),
    email: fd.get('email'),
    password: fd.get('password'),
    password_confirmation: fd.get('password_confirmation'),
  };

  app.dialog.preloader('Đang đăng ký...');
  window.AuthApi.register(payload)
    .then(function(res) {
      app.dialog.close();
      app.toast.create({
        text: 'Đăng ký thành công! Vui lòng đăng nhập.',
        position: 'top',
        closeTimeout: 3000,
        cssClass: 'color-green'
      }).open();
      if (app && app.views && app.views.main) {
        app.views.main.router.navigate('/signin/');
      }
    })
    .catch(function(err) {
      app.dialog.close();
      var msg = err?.data?.message || err?.message || 'Đăng ký thất bại.';
      app.dialog.alert(msg, 'Lỗi');
    });
});

function initAuthForms(root) {
  // Logic migrated to delegated events above to prevent race conditions
}

function ensureAuthedOrRedirect() {
  if (window.AuthStore && window.AuthStore.isAuthenticated) return true;
  if (app && app.views && app.views.main) {
    app.views.main.router.navigate('/signin/');
  }
  return false;
}

function initProfilePage() {
  if (!ensureAuthedOrRedirect()) return;

  var nameEl = document.getElementById('profile-name');
  var emailEl = document.getElementById('profile-email');

  function render(user) {
    if (!nameEl || !emailEl) return;
    nameEl.textContent = user?.name || '—';
    emailEl.textContent = user?.email || '—';
  }

  render(window.AuthStore.user);
  window.AuthStore.refreshProfile()
    .then(function(user) { render(user); })
    .catch(function(err) {
      if (err && err.status === 401) {
        window.AuthStore.clear();
        if (app && app.views && app.views.main) {
          app.views.main.router.navigate('/signin/');
        }
      }
    });
}

function initOrdersPage() {
  if (!ensureAuthedOrRedirect()) return;

  var loadingEl = document.getElementById('orders-loading');
  var errorEl = document.getElementById('orders-error');
  var emptyEl = document.getElementById('orders-empty');
  var listEl = document.getElementById('orders-list');

  function setState(state) {
    if (loadingEl) loadingEl.style.display = state === 'loading' ? 'block' : 'none';
    if (errorEl) errorEl.style.display = state === 'error' ? 'block' : 'none';
    if (emptyEl) emptyEl.style.display = state === 'empty' ? 'block' : 'none';
    if (listEl) listEl.style.display = state === 'list' ? 'block' : 'none';
  }

  function escapeHtml(text) {
    var div = document.createElement('div');
    div.textContent = text || '';
    return div.innerHTML;
  }

  function fmtDate(iso) {
    if (!iso) return '—';
    var d = new Date(iso);
    if (Number.isNaN(d.getTime())) return '—';
    return d.toLocaleDateString('vi-VN');
  }

  function renderOrders(orders) {
    if (!listEl) return;
    var html = orders.map(function(o) {
      var serviceName = o?.service?.name || o?.service_name || o?.service_data?.name || 'Dịch vụ';
      var amount = o?.amount || 0;
      var amountText = Number(amount).toLocaleString('vi-VN') + 'đ';
      var createdAt = fmtDate(o?.created_at);
      var expiresAt = fmtDate(o?.expires_at);
      var status = o?.status || 'pending';
      var isActive = status === 'paid' || status === 'processing';
      var badgeBg = isActive ? 'rgba(34, 197, 94, 0.1)' : 'var(--vs-bg-secondary)';
      var badgeColor = isActive ? 'var(--vs-success)' : 'var(--vs-text-secondary)';
      var icon = isActive ? 'checkmark_alt_circle' : 'time';
      var iconBg = isActive ? 'rgba(34, 197, 94, 0.1)' : 'var(--vs-bg-secondary)';
      var iconColor = isActive ? 'var(--vs-success)' : 'var(--vs-text-secondary)';
      var badgeText = isActive ? 'Đang hoạt động' : (status === 'expired' ? 'Đã hết hạn' : 'Chờ thanh toán');

      return (
        '<div class="vs-service-card detailed" style="margin-bottom: 16px;' + (isActive ? '' : 'opacity:0.75;') + '">' +
          '<div class="vs-service-card-top" style="align-items: flex-start;">' +
            '<div class="vs-service-icon-wrap">' +
              '<div class="vs-service-icon" style="background: ' + iconBg + '; color: ' + iconColor + ';">' +
                '<i class="icon f7-icons">' + icon + '</i>' +
              '</div>' +
              '<div class="text-align-left">' +
                '<h3 class="vs-service-name" style="margin-bottom: 4px;">' + escapeHtml(serviceName) + '</h3>' +
                '<p class="vs-service-duration" style="font-size: 13px;">Ngày mua: ' + createdAt + '</p>' +
                '<p class="vs-service-duration" style="font-size: 13px; color: var(--vs-text-primary);">Hết hạn: <span style="font-weight: 600;">' + expiresAt + '</span></p>' +
              '</div>' +
            '</div>' +
            '<div class="text-align-right">' +
              '<div class="vs-service-price" style="font-size: 15px;">' + amountText + '</div>' +
              '<span class="badge" style="background: ' + badgeBg + '; color: ' + badgeColor + '; margin-top: 4px; padding: 4px 8px; border-radius: 8px;">' + escapeHtml(badgeText) + '</span>' +
            '</div>' +
          '</div>' +
        '</div>'
      );
    }).join('');
    listEl.innerHTML = html;
  }

  window.retryLoadOrders = function() {
    load();
  };

  function load() {
    setState('loading');
    window.OrderApi.list()
      .then(function(res) {
        var orders = [];
        if (Array.isArray(res)) orders = res;
        else if (res && res.status && Array.isArray(res.data)) orders = res.data;
        else if (res && res.data && Array.isArray(res.data.items)) orders = res.data.items;
        else if (res && res.data && Array.isArray(res.data.data)) orders = res.data.data;
        else if (res && res.data && Array.isArray(res.data)) orders = res.data;
        if (!orders || orders.length === 0) {
          setState('empty');
          return;
        }
        renderOrders(orders);
        setState('list');
      })
      .catch(function(err) {
        if (err && err.status === 401) {
          window.AuthStore.clear();
          if (app && app.views && app.views.main) {
            app.views.main.router.navigate('/signin/');
          }
          return;
        }
        if (err && err.status === 404) {
          setState('empty');
          return;
        }
        setState('error');
      });
  }

  load();
}

window.logoutAndGoSignin = function() {
  var doLogout = function() {
    // Clear auth store
    if (window.AuthStore) {
      window.AuthStore.clear();
    }
    // Clear all auth-related localStorage
    try {
      localStorage.removeItem('token');
      localStorage.removeItem('auth_token');
      localStorage.removeItem('auth_user');
    } catch (e) {}

    // Reset stores
    if (window.ServiceStore) {
      window.ServiceStore.reset();
    }
    if (window.OrderStore) {
      window.OrderStore.reset();
    }

    if (app && app.views && app.views.main) {
      app.views.main.router.navigate('/signin/');
    }
  };

  if (!window.AuthStore || !window.AuthStore.isAuthenticated) {
    doLogout();
    return;
  }

  window.AuthApi.logout()
    .then(function() { doLogout(); })
    .catch(function() { doLogout(); });
};

function getTabIndex(tabName) {
  if (Object.prototype.hasOwnProperty.call(tabIndexMap, tabName)) return tabIndexMap[tabName];
  return 0;
}

function getAdjacentTab(tabName, direction) {
  var index = getTabIndex(tabName);
  var nextIndex = index + direction;
  if (nextIndex < 0 || nextIndex >= tabOrder.length) return null;
  return tabOrder[nextIndex];
}

function setTabIndicator(tabName) {
  var tabPill = document.querySelector('.vs-tab-pill');
  if (!tabPill) return;
  tabPill.style.setProperty('--vs-tab-index', String(getTabIndex(tabName)));
}

function setActiveTabLink(tabName) {
  var tabLinks = document.querySelectorAll('.vs-tab-item');
  tabLinks.forEach(function(link) {
    var isActive = link.getAttribute('data-tab') === tabName;
    link.classList.toggle('active', isActive);
    link.setAttribute('aria-current', isActive ? 'page' : 'false');
  });
}

function getTabScrollNode(tabName) {
  var page = tabPages[tabName];
  if (!page) return null;
  return page.querySelector('.page-content') || page;
}

function saveTabScroll(tabName) {
  if (!tabName) return;
  var node = getTabScrollNode(tabName);
  if (node) {
    tabScrollPositions[tabName] = node.scrollTop;
    return;
  }
  var scrollingEl = document.scrollingElement || document.documentElement;
  tabScrollPositions[tabName] = scrollingEl ? scrollingEl.scrollTop : 0;
}

function restoreTabScroll(tabName) {
  var nextScroll = Object.prototype.hasOwnProperty.call(tabScrollPositions, tabName) ? tabScrollPositions[tabName] : 0;
  requestAnimationFrame(function() {
    var node = getTabScrollNode(tabName);
    if (node) {
      node.scrollTop = nextScroll;
      return;
    }
    var scrollingEl = document.scrollingElement || document.documentElement;
    if (scrollingEl) scrollingEl.scrollTop = nextScroll;
  });
}

function getTabHtml(tabName) {
  if (pageCache[tabName]) {
    return Promise.resolve(pageCache[tabName]);
  }
  var url = tabUrlMap[tabName] || tabUrlMap.home;
  // Prepend base URL to handle routing from subdirectories
  var fullUrl = getPageBaseUrl() + url;
  return fetch(fullUrl)
    .then(function(response) {
      return response.text();
    })
    .then(function(html) {
      pageCache[tabName] = html;
      return html;
    });
}

function ensureTabLoaded(tabName) {
  var targetPage = tabPages[tabName];
  if (!targetPage) return Promise.resolve();
  
  if (targetPage.dataset.loaded === '1') {
    afterTabLoaded(tabName);
    restoreTabScroll(tabName);
    return Promise.resolve();
  }
  
  app.preloader.show();
  return getTabHtml(tabName).then(function(html) {
    targetPage.innerHTML = html;
    targetPage.dataset.loaded = '1';
    app.preloader.hide();
    afterTabLoaded(tabName);
    restoreTabScroll(tabName);
  }).catch(function(error) {
    app.preloader.hide();
    app.dialog.alert('Không thể tải trang. Vui lòng thử lại.', 'Lỗi');
    console.error('ensureTabLoaded error:', error);
  });
}

function switchTab(tabName, element) {
  var targetTab = tabUrlMap[tabName] ? tabName : 'home';

  var tabBarWrap = document.querySelector('.vs-tab-bar-wrap');
  if (tabBarWrap) tabBarWrap.style.display = '';

  // Track navigation
  if (window.NavigationStore && targetTab !== currentTab) {
    window.NavigationStore.push(targetTab, {});
  }

  // Let Framework7 router handle URL updates

  var targetIndex = getTabIndex(targetTab);
  if (tabSwiper) {
    if (targetTab === currentTab) {
      ensureTabLoaded(targetTab);
      return;
    }
    tabSwiper.slideTo(targetIndex, tabTransitionMs);
  }
}

function afterTabLoaded(tabName) {
  switch(tabName) {
    case 'home':
      initHomePage();
      break;
    case 'services':
      initServicesListPage();
      break;
    case 'orders':
      initOrdersPage();
      break;
    case 'profile':
      initProfilePage();
      break;
  }
}

function initTabSwiper() {
  if (tabSwiper) return;
  
  var reducedMotion = window.matchMedia && window.matchMedia('(prefers-reduced-motion: reduce)').matches;
  var speed = reducedMotion ? 0 : tabTransitionMs;
  
  // Populate tabPages map
  var slides = document.querySelectorAll('.vs-tab-page');
  slides.forEach(function(slide) {
    var tabName = slide.getAttribute('data-tab');
    if (tabName) {
      tabPages[tabName] = slide;
    }
  });

  tabSwiper = app.swiper.create('#main-swiper', {
    speed: speed,
    resistanceRatio: 0.65,
    on: {
      slideChange: function() {
        var activeIndex = tabSwiper.activeIndex;
        var targetTab = tabOrder[activeIndex];
        if (currentTab !== targetTab) {
          saveTabScroll(currentTab);
          currentTab = targetTab;
          
          setActiveTabLink(targetTab);
          setTabIndicator(targetTab);
          ensureTabLoaded(targetTab);
          
          var tabBarWrap = document.querySelector('.vs-tab-bar-wrap');
          if (tabBarWrap) tabBarWrap.style.display = '';
        }
      }
    }
  });
}

// Load home page on initial load
function initApp() {
  console.log('App: initApp called, readyState:', document.readyState);
  debugLog('D', 'app.js:DOMContentLoaded', 'DOM ready', {});
  initTabSwiper();
  setTimeout(function() {
    console.log('App: Loading initial tab (home)');
    debugLog('D', 'app.js:timeout', 'Calling ensureTabLoaded for home', {});
    ensureTabLoaded('home');
  }, 100);
}

// Run immediately or on DOMContentLoaded
if (document.readyState === 'loading') {
  document.addEventListener('DOMContentLoaded', initApp);
} else {
  // DOM already ready
  console.log('App: DOM already ready, readyState:', document.readyState);
  initApp();
}

// 2. Dialog

$$(document).on("page:init", '.page[data-name="dialog"]', function (e) {
  $$(".open-alert").on("click", function () {
    app.dialog.alert("Your subscription has been confirmed.");
  });

  $$(".open-confirm").on("click", function () {
    app.dialog.confirm("Confirm your subscription?", function () {
      app.dialog.alert("Confirmed!");
    });
  });
});

// 3. Infinite Scroll

$$(document).on("page:init", '.page[data-name="infinite-scroll"]', function (e) {
  var allowInfinite = true; // Loading flag
  var lastItemIndex = $$(".infinite-scroll-demo .post-horizontal").length; // Last loaded index
  var maxItems = 30; // Max items to load
  var itemsPerLoad = 5; // Append items per load

  // Attach 'infinite' event handler
  $$(".infinite-scroll-content").on("infinite", function () {
    if (!allowInfinite) return; // Exit, if loading in progress
    allowInfinite = false; // Set loading flag

    // Emulate 2s loading
    setTimeout(function () {
      allowInfinite = true; // Reset loading flag

      if (lastItemIndex >= maxItems) {
        // Nothing more to load, detach infinite scroll events to prevent unnecessary loadings
        app.infiniteScroll.destroy(".infinite-scroll-content");
        // Remove preloader from the DOM
        $$(".infinite-scroll-preloader").remove();
        return;
      }

      // Simulate new items generation
      var html = "";
      for (var i = lastItemIndex + 1; i <= lastItemIndex + itemsPerLoad; i++) {
        html +=
          '<a href="/single/" class="link post-horizontal">' +
          '<div class="infos">' +
          '<div class="post-category">Fashion</div>' +
          '<div class="post-title">The Importance of Supporting Local and Independent Fashion Brands</div>' +
          '<div class="post-date">2 hours ago</div>' +
          "</div>" +
          '<div class="post-image">' +
          (i + 1) +
          "</div>" +
          "</a>";
      }

      $$(".infinite-scroll-demo").append(html); // Append new items
      lastItemIndex = $$(".infinite-scroll-demo .post-horizontal").length; // Update last loaded index
    }, 2000);
  });
});

// 4. Notification

$$(document).on("page:init", '.page[data-name="notifications"]', function (e) {
  // Create notification with close button
  var notification = app.notification.create({
    icon: '<img src="img/avatars/small-avatar.jpg" alt="" class="notification-image" />',
    title: "Yui Mobile",
    subtitle: "Noah Campbell has started following you!",
    text: "Follow him back to expand your network!",
    closeButton: true,
  });

  // Open Notification
  $$(".open-notification").on("click", function () {
    notification.open();
  });
});

// 5. Photo Browser

$$(document).on("page:init", '.page[data-name="photo-browser"]', function (e) {
  var photoBrowserDark = app.photoBrowser.create({
    photos: ["img/images/1.jpg", "img/images/2.jpg", "img/images/3.jpg", "img/images/4.jpg", "img/images/5.jpg"],
    theme: "dark",
  });
  $$(".photo-browser-demo").on("click", function () {
    photoBrowserDark.open();
  });
});

// 6. Picker

$$(document).on("page:init", '.page[data-name="picker"]', function (e) {
  var pickerDevice = app.picker.create({
    inputEl: "#demo-picker-language",
    cols: [
      {
        textAlign: "center",
        values: ["Spanish", "English", "Arabic", "Hindi", "Portuguese", "Russian", "Japanese", "German"],
      },
    ],
  });
  var pickerMonth = app.picker.create({
    inputEl: "#demo-picker-month",
    cols: [
      {
        textAlign: "center",
        values: ["January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December"],
      },
    ],
  });
  var pickerDay = app.picker.create({
    inputEl: "#demo-picker-day",
    cols: [
      {
        textAlign: "center",
        values: ["1", "2", "3", "4", "5", "6", "7", "8", "9", "10", "11", "12", "13", "14", "15", "16", "17", "18", "19", "20", "21", "22", "23", "24", "25", "26", "27", "28", "29", "30", "31"],
      },
    ],
  });
  var pidckerYear = app.picker.create({
    inputEl: "#demo-picker-year",
    cols: [
      {
        textAlign: "center",
        values: ["1983", "1984", "1985", "1986", "1987", "1988", "1989", "1990", "1991", "1992", "1993", "1994", "1995", "1996", "1997", "1998", "1999", "2000", "2001", "2002", "2003", "2004", "2005"],
      },
    ],
  });
});

// 7. Preloader

$$(document).on("page:init", '.page[data-name="preloader"]', function (e) {
  $$(".open-preloader").on("click", function () {
    app.preloader.show();
    setTimeout(function () {
      app.preloader.hide();
    }, 2000);
  });
});

// 8. Pull To Refresh

$$(document).on("page:init", '.page[data-name="pull-to-refresh"]', function (e) {
  var pullToRefreshPage = $$(".ptr-content");
  // Add 'refresh' listener on it
  pullToRefreshPage.on("ptr:refresh", function (e) {
    // Emulate 2s loading and generate new items
    setTimeout(function () {
      var html =
        '<a href="/single/" class="link post-horizontal">' +
        '<div class="infos">' +
        '<div class="post-category">Fashion</div>' +
        '<div class="post-title">The Importance of Supporting Local and Independent Fashion Brands</div>' +
        '<div class="post-date">2 hours ago</div>' +
        "</div>" +
        '<div class="post-image">NEW</div>' +
        "</a>";
      // Prepend new element
      pullToRefreshPage.find(".post-list").prepend(html);
      // When loading done, we reset it
      app.ptr.done();
    }, 2000);
  });
});

// 9. Range Slider

$$(document).on("page:init", '.page[data-name="range-slider"]', function (e) {
  $$("#age-filter").on("range:change", function (e, range) {
    $$(".age-value").text(range[0] + " - " + range[1]);
  });
  $$("#price-filter").on("range:change", function (e, range) {
    $$(".price-value").text("$" + range[0] + " - $" + range[1]);
  });
});

// 10. Toasts

$$(document).on("page:init", '.page[data-name="toasts"]', function (e) {
  // Bottom toast
  var toastBottom = app.toast.create({
    text: "Thank you for your subscription!",
    closeTimeout: 2000,
  });
  $$(".open-toast-bottom").on("click", function () {
    toastBottom.open();
  });

  // Top toast
  var toastTop = app.toast.create({
    text: "Thank you for your subscription!",
    position: "top",
    closeTimeout: 2000,
  });
  $$(".open-toast-top").on("click", function () {
    toastTop.open();
  });

  // Center toast
  var toastCenter = app.toast.create({
    text: "Thank you for your subscription!",
    position: "center",
    closeTimeout: 2000,
  });
  $$(".open-toast-center").on("click", function () {
    toastCenter.open();
  });

  // Toast with close button
  var toastWithButton = app.toast.create({
    text: "Thank you for your subscription!",
    closeButton: true,
  });
  $$(".open-toast-button").on("click", function () {
    toastWithButton.open();
  });
});

// 11. Chat

// Initialize chat
$$(document).on("page:init", '.page[data-name="chat"]', function (e) {
  var messages = app.messages.create({
    el: ".messages",
    // Define styling rules, depending on what type of message it is
    firstMessageRule: function (message, previousMessage, nextMessage) {
      if (message.isTitle) return false;
      if (!previousMessage || previousMessage.type !== message.type || previousMessage.name !== message.name) return true;
      return false;
    },
    lastMessageRule: function (message, previousMessage, nextMessage) {
      if (message.isTitle) return false;
      if (!nextMessage || nextMessage.type !== message.type || nextMessage.name !== message.name) return true;
      return false;
    },
  });

  // Init Messagebar
  var messagebar = app.messagebar.create({
    el: ".messagebar",
  });

  // Response flag
  var responseInProgress = false;

  // Send Message
  $$(".send-link").on("click", function () {
    var text = messagebar.getValue().replace(/\n/g, "<br>").trim();

    // return if empty message
    if (!text.length) return;

    // Clear area
    messagebar.clear();

    // Return focus to area
    messagebar.focus();

    // Add message to messages
    messages.addMessage({
      text: text,
    });

    if (responseInProgress) return;
    // Receive dummy message
    receiveMessage();
  });

  function receiveMessage() {
    responseInProgress = true;
    setTimeout(function () {
      // Show typing indicator
      messages.showTyping({
        header: "Jack is typing...",
        avatar: "../img/avatars/5.jpg",
      });

      setTimeout(function () {
        // Add received dummy message
        messages.addMessage({
          text: "Amazing!!!",
          type: "received",
          avatar: "../img/avatars/5.jpg",
        });
        // Hide typing indicator
        messages.hideTyping();
        responseInProgress = false;
      }, 2000);
    }, 500);
  }
});

// 12. Calendar

$$(document).on("page:init", '.page[data-name="calendar"]', function (e) {
  var monthNames = ["January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December"];
  var calendarInline = app.calendar.create({
    containerEl: "#calendar",
    value: [new Date()],
    weekHeader: false,
    renderToolbar: function () {
      return (
        '<div class="toolbar calendar-custom-toolbar no-shadow">' +
        '<div class="toolbar-inner">' +
        '<div class="left">' +
        '<a href="#" class="link icon-only"><i class="icon icon-back ' +
        (app.theme === "md" ? "color-black" : "") +
        '"></i></a>' +
        "</div>" +
        '<div class="center"></div>' +
        '<div class="right">' +
        '<a href="#" class="link icon-only"><i class="icon icon-forward ' +
        (app.theme === "md" ? "color-black" : "") +
        '"></i></a>' +
        "</div>" +
        "</div>" +
        "</div>"
      );
    },
    on: {
      init: function (c) {
        $$(".calendar-custom-toolbar .center").text(monthNames[c.currentMonth] + ", " + c.currentYear);
        $$(".calendar-custom-toolbar .left .link").on("click", function () {
          calendarInline.prevMonth();
        });
        $$(".calendar-custom-toolbar .right .link").on("click", function () {
          calendarInline.nextMonth();
        });
      },
      monthYearChangeStart: function (c) {
        $$(".calendar-custom-toolbar .center").text(monthNames[c.currentMonth] + ", " + c.currentYear);
      },
    },
  });
});

// 13. Onboarding

$$(document).on("page:init", '.page[data-name="onboarding"]', function (e) {
  const swiperEl = document.querySelector(".swiper-onboarding");
  $$(".onboarding-next-button").on("click", () => {
    const totalSlides = swiperEl.swiper.slides.length;
    const currentSlide = swiperEl.swiper.activeIndex + 1;

    console.log(currentSlide + " / " + totalSlides);
    if (currentSlide == totalSlides) {
      app.views.current.router.back();
      return;
    }
    swiperEl.swiper.slideNext();

    if (currentSlide == totalSlides - 1) {
      $$(".onboarding-next-button").text("Start!");
      //$$(".onboarding-next-button").addClass("Start!");
    }
  });
});

// 14. Swiper

$$("swiper-slide a").on("click", function () {
  app.views.current.router.navigate($$(this).attr("data-href"));
});
$$(document).on("page:init", function (e) {
  $$("swiper-slide a").on("click", function () {
    app.views.current.router.navigate($$(this).attr("data-href"));
  });
});

// Debug: Log all page init events
$$(document).on('page:init', function(e) {
  console.log('F7 page:init:', e.detail);
});

// 15. Switch Theme

$$(".switch-theme").on("click", function () {
  $$(".page").toggleClass("page-theme-transition");
  $$(".page").transitionEnd(function(){
    $$(".page").toggleClass("page-theme-transition");
  });
  var isDark = $$("body").hasClass("dark");
  $$("body").toggleClass("dark");
  $$("html").toggleClass("dark");
  if (isDark) {
    $$(".switch-theme i").text("sun_max");
  } else {
    $$(".switch-theme i").text("moon_stars");
  }
  document.dispatchEvent(new CustomEvent('theme:change'));
});

// 16. Preload Pages

function preloadPages() {
  const pages = app.routes.map((route) => route.url);

  for (const page of pages) {
    // Prepend base URL to handle routing from subdirectories
    const fullUrl = getPageBaseUrl() + page;
    fetch(fullUrl)
      .then((response) => response.text())
      .then((content) => {
        const xhrEntry = {
          url: page,
          time: Date.now(),
          content: content,
        };
        app.router.cache.xhr.push(xhrEntry);
      })
      .catch((error) => console.error('preloadPages error:', error));
  }
}

preloadPages();

// ====================
// API Configuration
// ====================
// Use config from config.js - with safety check
function getApiBaseUrl() {
  if (window.AppConfig && window.AppConfig.apiBaseUrl) {
    return window.AppConfig.apiBaseUrl;
  }
  return 'https://tiemnhaduy.com/api/v1';
}
var API_BASE_URL = getApiBaseUrl();
console.log('API Base URL:', API_BASE_URL);

// ====================
// Service API Functions
// ====================

/**
 * Fetch default service from API
 * @returns {Promise<Object>} Service data with id, name, duration_days, price
 */
async function fetchDefaultService() {
    try {
        const response = await fetch(`${API_BASE_URL}/service/default`);
        if (!response.ok) {
            throw new Error('Failed to fetch service');
        }
        return response.json();
    } catch (error) {
        console.error('Error fetching service:', error);
        throw error;
    }
}

/**
 * Create a new order
 * @param {string} facebookProfileLink - Facebook profile URL
 * @returns {Promise<Object>} Order data with order_code, amount, expires_at, qr_content
 */
async function createOrder(facebookProfileLink) {
    try {
        const response = await fetch(`${API_BASE_URL}/orders`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
            },
            body: JSON.stringify({ facebook_profile_link: facebookProfileLink }),
        });
        
        if (!response.ok) {
            const error = await response.json();
            throw new Error(error.message || 'Failed to create order');
        }
        
        return response.json();
    } catch (error) {
        console.error('Error creating order:', error);
        throw error;
    }
}

/**
 * Check order status
 * @param {string} orderCode - Order code
 * @returns {Promise<Object>} Order data with status, amount, etc.
 */
async function checkOrderStatus(orderCode) {
    try {
        const response = await fetch(`${API_BASE_URL}/orders/${orderCode}`);
        if (!response.ok) {
            throw new Error('Failed to check order');
        }
        return response.json();
    } catch (error) {
        console.error('Error checking order:', error);
        throw error;
    }
}

// ====================
// Realtime (WebSocket) - Laravel Reverb with API-only authentication
// ====================

// Ensure Echo is loaded - Echo should be included via bootstrap.js which is loaded in the main HTML
let echo = null;
let orderChannel = null;
let currentOrderCode = null;

/**
 * Initialize Laravel Echo connection
 * @returns {Object} Echo instance
 */
function getEcho() {
    if (!echo && window.Echo) {
        echo = window.Echo;
    }
    return echo;
}

/**
 * Subscribe to order payment status updates
 * Uses Laravel Echo with API-based authentication (no CSRF needed)
 * 
 * @param {string} orderCode - Order code to subscribe to
 * @returns {Object} Channel object with event listeners
 */
function subscribeToOrder(orderCode) {
    const echoInstance = getEcho();
    
    if (!echoInstance) {
        console.error('Echo is not initialized. Make sure bootstrap.js is loaded.');
        app.dialog.alert('Realtime connection not available. Please refresh the page.', 'Connection Error');
        return null;
    }
    
    // Leave any existing order channel
    if (orderChannel && currentOrderCode) {
        echoInstance.leave('order.' + currentOrderCode);
    }
    
    // Store current order code
    currentOrderCode = orderCode;
    
    // Subscribe to private channel for this order
    // Laravel Echo automatically adds 'private-' prefix to channel names
    // Channel name will be: private-order.ORD-XXXXXXXXXX
    const channel = echoInstance.private('order.' + orderCode);
    orderChannel = channel;
    
    // Listen for payment events
    channel.listen('payment.pending', (data) => {
        console.log('Payment pending:', data);
        app.dialog.alert('Payment pending: ' + (data.message || 'Your order is being processed'), 'Pending');
    });
    
    channel.listen('payment.success', (data) => {
        console.log('Payment success:', data);

        // Stop countdown and status polling
        if (typeof stopCountdown === 'function') {
            stopCountdown();
        }
        if (pollingInterval) {
            clearInterval(pollingInterval);
            pollingInterval = null;
        }

        // Hide expiration section (countdown timer)
        var expirationSection = document.getElementById('expiration-section');
        if (expirationSection) {
            expirationSection.style.display = 'none';
        }

        // Hide payment instruction
        var paymentInstruction = document.getElementById('payment-instruction');
        if (paymentInstruction) {
            paymentInstruction.style.display = 'none';
        }

        // Show success section
        var successSection = document.getElementById('success-section');
        if (successSection) {
            successSection.style.display = 'flex';
        }

        // Update card header status
        var statusBadge = document.getElementById('order-status-badge');
        var statusText = document.getElementById('order-status-text');
        if (statusBadge) {
            statusBadge.textContent = 'Đã thanh toán';
            statusBadge.classList.remove('bg-color-green');
            statusBadge.classList.add('bg-color-blue');
        }
        if (statusText) {
            statusText.textContent = 'Thanh toán thành công';
        }

        // Update success message
        var successMessageEl = document.querySelector('.success-message');
        if (successMessageEl) {
            successMessageEl.innerHTML = `
                <i class="icon f7-icons" style="font-size: 40px; margin-bottom: 12px; color: #4CAF50;">checkmark_circle_fill</i>
                <h2>Thanh toán thành công!</h2>
                <p>
                    Dịch vụ của bạn đã được kích hoạt.
                    <br>
                    Cảm ơn bạn đã sử dụng dịch vụ.
                </p>
            `;
        }

        // Hide cancel button
        var cancelButton = document.querySelector('button[onclick="cancelOrder()"]');
        if (cancelButton) {
            cancelButton.style.display = 'none';
        }

        // Show success alert
        app.dialog.alert('Thanh toán thành công! Dịch vụ đã được kích hoạt.', 'Thành công');
    });
    
    channel.listen('payment.expired', (data) => {
        console.log('Payment expired:', data);
        app.dialog.alert('Payment expired. Please create a new order.', 'Expired');
    });
    
    // Error handling for channel subscription
    channel.error((error) => {
        console.error('Channel subscription error:', error);
        
        if (error.error && error.error.message) {
            // Authentication error
            app.dialog.alert('Could not connect to payment updates. Please try again.', 'Connection Error');
        } else {
            app.dialog.alert('Connection error. Check your internet connection.', 'Error');
        }
    });
    
    // Log successful subscription
    console.log('Subscribed to order channel:', 'order.' + orderCode);
    
    return channel;
}

/**
 * Disconnect from WebSocket
 */
function disconnectReverb() {
    if (echo && orderChannel && currentOrderCode) {
        echo.leave('order.' + currentOrderCode);
        orderChannel = null;
        currentOrderCode = null;
    }
}

/**
 * Check if Echo is connected
 * @returns {boolean} True if connected
 */
function isReverbConnected() {
    const echoInstance = getEcho();
    return echoInstance && echoInstance.connector && echoInstance.connector.socket && echoInstance.connector.socket.connection ? echoInstance.connector.socket.connection.isInitialized() : false;
}

// ====================
// Service Page Initialization
// ====================

// NOTE: Moved page:init handle to the global section at bottom to prevent duplicates

function initServicePage(serviceId) {
    var pageEl = document.querySelector('.page[data-name="service"]');
    if (!pageEl) return;

    // Track navigation to service detail
    if (window.NavigationStore) {
        window.NavigationStore.push('service-detail', { serviceId: serviceId });
    }

    var serviceInfoEl = pageEl.querySelector('#service-order-view');
    var orderFormEl = pageEl.querySelector('#order-form');
    var paymentInfoEl = pageEl.querySelector('#payment-info');
    var currentOrderCode = null;
    
    function loadServiceInfo() {
        app.preloader.show();
        
        var apiCall = serviceId ? ServiceApi.getById(serviceId) : ServiceApi.getDefault();
        
        apiCall
            .then(function(service) {
                var nameEl = document.getElementById('service-name');
                var durationEl = document.getElementById('service-duration');
                var priceEl = document.getElementById('service-price');
                var durationInfoEl = document.getElementById('info-duration');
                var priceInfoEl = document.getElementById('info-price');
                var btnPriceEl = document.getElementById('btn-price');
                var summaryNameEl = document.getElementById('summary-name');
                var summaryDurationEl = document.getElementById('summary-duration');
                var summaryPriceEl = document.getElementById('summary-price-top');
                var summaryTotalEl = document.getElementById('summary-total');
                var bottomTotalEl = document.getElementById('bottom-total');
                
                var priceText = service.price.toLocaleString('vi-VN');
                
                if (nameEl) nameEl.textContent = service.name;
                if (durationEl) durationEl.textContent = service.duration_days;
                if (priceEl) priceEl.textContent = priceText;
                
                if (durationInfoEl) durationInfoEl.textContent = service.duration_days;
                if (priceInfoEl) priceInfoEl.textContent = priceText;
                
                if (btnPriceEl) btnPriceEl.textContent = priceText;
                
                if (summaryNameEl) summaryNameEl.textContent = service.name;
                if (summaryDurationEl) summaryDurationEl.textContent = service.duration_days + ' ngày';
                if (summaryPriceEl) summaryPriceEl.textContent = priceText;
                if (summaryTotalEl) summaryTotalEl.textContent = priceText;
                if (bottomTotalEl) bottomTotalEl.textContent = priceText;
                
                if (serviceInfoEl) serviceInfoEl.style.display = 'block';
                app.preloader.hide();
            })
            .catch(function(error) {
                app.preloader.hide();
                app.dialog.alert('Không thể tải thông tin dịch vụ.', 'Lỗi');
                console.error('Service load error:', error);
            });
    }
    
    if (orderFormEl) {
        orderFormEl.addEventListener('submit', function(e) {
            e.preventDefault();
            
            var formData = new FormData(orderFormEl);
            var facebookLink = formData.get('facebook_profile_link');
            
            if (!facebookLink || !facebookLink.includes('facebook.com')) {
                app.dialog.alert('Vui lòng nhập link Facebook hợp lệ', 'Lỗi');
                return;
            }
            
            app.dialog.preloader('Đang tạo đơn hàng...');
            
            OrderApi.create(facebookLink)
                .then(function(orderData) {
                    app.dialog.close();
                    
                    currentOrderCode = orderData.order_code;
                    
                    var orderCodeEl = document.getElementById('order-code');
                    var orderAmountEl = document.getElementById('order-amount');
                    var orderExpiresEl = document.getElementById('order-expires');
                    var qrContainer = document.getElementById('qr-code');
                    var transferContentEl = document.getElementById('transfer-content');
                    
                    if (orderCodeEl) orderCodeEl.textContent = orderData.order_code;
                    if (orderAmountEl) orderAmountEl.textContent = orderData.amount.toLocaleString('vi-VN');
                    
                    var expiresDate = new Date(orderData.expires_at);
                    if (orderExpiresEl) orderExpiresEl.textContent = expiresDate.toLocaleString('vi-VN');
                    
                    if (qrContainer && orderData.qr_content) {
                        qrContainer.innerHTML = '<img src="' + orderData.qr_content + '" alt="QR Code" style="max-width: 200px;">';
                    }
                    if (transferContentEl) transferContentEl.textContent = orderData.order_code;
                    
                    orderFormEl.style.display = 'none';
                    if (paymentInfoEl) paymentInfoEl.style.display = 'block';
                    
                    subscribeToOrder(orderData.order_code);
                    startStatusPolling(orderData.order_code);
                })
                .catch(function(error) {
                    app.dialog.close();
                    app.dialog.alert(error.message || 'Không thể tạo đơn hàng.', 'Lỗi');
                    console.error('Order creation error:', error);
                });
        });
    }
    
    var pollingInterval = null;
    
    function startStatusPolling(orderCode) {
        if (pollingInterval) clearInterval(pollingInterval);

        pollingInterval = setInterval(function() {
            OrderApi.getStatus(orderCode)
                .then(function(orderData) {
                    if (orderData.status === 'paid') {
                        clearInterval(pollingInterval);
                        pollingInterval = null;
                        handlePaymentSuccess();
                    }
                })
                .catch(function(error) {
                    console.error('Polling error:', error);
                });
        }, 5000);
    }
    
    function handlePaymentSuccess() {
        var expirationSection = document.getElementById('expiration-section');
        var paymentInstruction = document.getElementById('payment-instruction');
        var successSection = document.getElementById('success-section');
        var statusBadge = document.getElementById('order-status-badge');
        var statusText = document.getElementById('order-status-text');
        
        if (expirationSection) expirationSection.style.display = 'none';
        if (paymentInstruction) paymentInstruction.style.display = 'none';
        if (successSection) successSection.style.display = 'flex';
        if (statusBadge) {
            statusBadge.textContent = 'Đã thanh toán';
            statusBadge.classList.remove('bg-color-green');
            statusBadge.classList.add('bg-color-blue');
        }
        if (statusText) statusText.textContent = 'Thanh toán thành công';
        
        var cancelButton = document.querySelector('button[onclick="cancelOrder()"]');
        if (cancelButton) cancelButton.style.display = 'none';
    }
    
    // Handle back button navigation
    var backButtons = pageEl.querySelectorAll('.link.back');
    backButtons.forEach(function(btn) {
        btn.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            goBack();
        });
    });
    
    loadServiceInfo();
}

// ====================
// Home Page Initialization
// ====================

var _homeUnsubscribe = null;

function initHomePage() {
    console.log('initHomePage: called');
    
    var containerEl = document.getElementById('services-container');
    var loadingEl = document.getElementById('services-loading');
    var errorEl = document.getElementById('services-error');
    
    if (!containerEl || !loadingEl || !errorEl) {
        console.error('initHomePage: DOM elements not found');
        return;
    }
    
    function formatDuration(days) {
        if (days >= 30) return Math.round(days / 30) + ' tháng';
        return days + ' ngày';
    }
    
    function formatPrice(price) {
        return price.toLocaleString('vi-VN');
    }
    
    function escapeHtml(text) {
        var div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }

    function renderServices(state) {
        console.log('initHomePage: renderServices called', state);
        
        if (state.loading) return;
        
        loadingEl.style.display = 'none';
        
        if (state.error) {
            errorEl.style.display = 'flex';
            containerEl.style.display = 'none';
            return;
        }

        var services = state.services || [];
        if (services.length === 0) {
            containerEl.innerHTML = '<p class="home-no-services">Chưa có dịch vụ nào</p>';
            containerEl.style.display = 'block';
            errorEl.style.display = 'none';
            return;
        }
        
        var isDark = document.body.classList.contains('dark');

        var servicesHtml = services.map(function(service) {
            var durationText = formatDuration(service.duration_days);
            var priceText = formatPrice(service.price);
            
            // Check if service is premium based on price or name as a simple heuristic
            var isPremium = service.price > 100000;
            var iconClass = isPremium ? 'vs-service-icon premium' : 'vs-service-icon';
            var iconName = isPremium ? 'crown' : 'tv';
            
            return '<a href="/service/?service=' + service.id + '" class="vs-service-card link">' +
                '<div class="vs-service-card-top">' +
                    '<div class="' + iconClass + '">' +
                        '<i class="icon f7-icons">' + iconName + '</i>' +
                    '</div>' +
                    '<div class="vs-service-details">' +
                        '<div class="vs-service-title-row">' +
                            '<h3 class="vs-service-name">' + escapeHtml(service.name) + '</h3>' +
                            '<div class="vs-service-price">' +
                                '<span class="vs-service-price-value">' + priceText + '</span>' +
                                '<span class="vs-service-price-currency">đ</span>' +
                            '</div>' +
                        '</div>' +
                        '<p class="vs-service-duration">' + durationText + '</p>' +
                    '</div>' +
                '</div>' +
                '<p class="vs-service-desc">' + escapeHtml(service.description || 'Xem tất cả kênh truyền hình cơ bản và nâng cao. Hỗ trợ đa thiết bị.') + '</p>' +
            '</a>';
        }).join('');
        
        containerEl.innerHTML = servicesHtml;
        containerEl.style.display = 'block';
        errorEl.style.display = 'none';
    }
    
    if (_homeUnsubscribe) {
        _homeUnsubscribe();
        _homeUnsubscribe = null;
    }
    
    _homeUnsubscribe = ServiceStore.subscribe(function(state) {
        renderServices(state);
    });
    
    loadingEl.style.display = 'flex';
    containerEl.style.display = 'none';
    errorEl.style.display = 'none';
    
    window.retryLoadHomeServices = function() {
        loadingEl.style.display = 'flex';
        containerEl.style.display = 'none';
        errorEl.style.display = 'none';
        ServiceStore.reset();
        ServiceStore.loadServices().catch(function(err) {
            console.error('retryLoadHomeServices error:', err);
            loadingEl.style.display = 'none';
            errorEl.style.display = 'flex';
        });
    };
    
    ServiceStore.reset();
    ServiceStore.loadServices().catch(function(err) {
        console.error('initHomePage: loadServices error:', err);
        loadingEl.style.display = 'none';
        errorEl.style.display = 'flex';
    });
}

var _servicesListUnsubscribe = null;
var _servicesSearchTimeout = null;

function initServicesListPage() {
    console.log('initServicesListPage: called');

    var containerEl = document.getElementById('services-list-container');
    var searchInput = document.querySelector('.vs-services-page .vs-search-input');
    if (!containerEl) {
        console.error('initServicesListPage: Container not found');
        return;
    }

    function escapeHtml(text) {
        var div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }

    function filterServicesByQuery(services, query) {
        if (!query || query.trim() === '') {
            return services;
        }
        var lowerQuery = query.toLowerCase().trim();
        return services.filter(function(service) {
            var nameMatch = service.name && service.name.toLowerCase().includes(lowerQuery);
            var descMatch = service.description && service.description.toLowerCase().includes(lowerQuery);
            var priceMatch = service.price && service.price.toString().includes(lowerQuery);
            var durationMatch = service.duration_days && service.duration_days.toString().includes(lowerQuery);
            return nameMatch || descMatch || priceMatch || durationMatch;
        });
    }

    function renderServicesList(state) {
        var searchInputEl = document.querySelector('.vs-services-page .vs-search-input');
        var searchQuery = searchInputEl ? searchInputEl.value : '';
        var services = filterServicesByQuery(state.services || [], searchQuery);

        if (state.loading) {
            containerEl.innerHTML = '<div class="text-align-center" style="padding: 40px;"><div class="preloader"></div><p style="margin-top: 10px; color: var(--vs-text-secondary);">Đang tải dịch vụ...</p></div>';
            return;
        }

        if (state.error) {
            containerEl.innerHTML = '<p class="text-align-center" style="padding: 40px; color: var(--vs-text-secondary);">Không thể tải danh sách dịch vụ.</p>';
            return;
        }

        if (services.length === 0) {
            var noResultMsg = searchQuery.trim() !== ''
                ? 'Không tìm thấy dịch vụ nào phù hợp với "' + escapeHtml(searchQuery) + '"'
                : 'Chưa có dịch vụ nào.';
            containerEl.innerHTML = '<p class="text-align-center" style="padding: 40px; color: var(--vs-text-secondary);">' + noResultMsg + '</p>';
            return;
        }

        var servicesHtml = services.map(function(service) {
            var durationDays = service.duration_days || 30;
            var durationText = 'Thời hạn: ' + (durationDays >= 30 ? Math.round(durationDays / 30) * 30 + ' ngày' : durationDays + ' ngày');
            var priceText = service.price ? service.price.toLocaleString('vi-VN') : '0';

            var isPremium = service.price > 100000;
            var iconClass = isPremium ? 'vs-service-icon premium' : 'vs-service-icon';
            var iconName = isPremium ? 'crown' : 'tv';
            var desc = escapeHtml(service.description || 'Xem tất cả kênh truyền hình cơ bản và nâng cao. Hỗ trợ đa thiết bị.');

            return '<div class="vs-service-card-detailed">' +
                '<div class="vs-service-card-top">' +
                    '<div class="' + iconClass + '">' +
                        '<i class="icon f7-icons">' + iconName + '</i>' +
                    '</div>' +
                    '<div class="vs-service-details">' +
                        '<div class="vs-service-title-row">' +
                            '<h3 class="vs-service-name">' + escapeHtml(service.name) + '</h3>' +
                            '<div class="vs-service-price">' + priceText + 'đ</div>' +
                        '</div>' +
                        '<p class="vs-service-duration">' + durationText + '</p>' +
                    '</div>' +
                '</div>' +
                '<p class="vs-service-desc">' + desc + '</p>' +
                '<div class="vs-service-actions" style="display:flex;gap:8px;margin-top:12px;">' +
                    '<button class="vs-service-cart-btn" onclick="event.preventDefault();event.stopPropagation();addToCart(' + service.id + ', 1, this);" style="flex:0 0 44px;height:40px;border-radius:12px;border:1px solid var(--vs-border-strong);background:transparent;display:flex;align-items:center;justify-content:center;cursor:pointer;">' +
                        '<i class="icon f7-icons" style="font-size:18px;color:var(--vs-accent);">cart_badge_plus</i>' +
                    '</button>' +
                    '<a href="/service/?service=' + service.id + '" class="vs-service-register-btn link" style="flex:1;">' +
                        'Đăng ký ngay <i class="icon f7-icons" style="font-size: 16px;">arrow_right</i>' +
                    '</a>' +
                '</div>' +
            '</div>';
        }).join('');

        containerEl.innerHTML = servicesHtml;
    }

    // Setup search input with debounce
    if (searchInput && !searchInput.dataset.bound) {
        searchInput.addEventListener('input', function(e) {
            var query = e.target.value;
            // Clear previous timeout
            if (_servicesSearchTimeout) {
                clearTimeout(_servicesSearchTimeout);
            }
            // Debounce 300ms
            _servicesSearchTimeout = setTimeout(function() {
                if (ServiceStore.isLoaded) {
                    renderServicesList({
                        services: ServiceStore.services,
                        loading: ServiceStore.loading,
                        error: ServiceStore.error
                    });
                }
            }, 300);
        });
        searchInput.dataset.bound = '1';
    }

    if (_servicesListUnsubscribe) {
        _servicesListUnsubscribe();
        _servicesListUnsubscribe = null;
    }

    _servicesListUnsubscribe = ServiceStore.subscribe(function(state) {
        renderServicesList(state);
    });

    if (ServiceStore.isLoaded) {
        renderServicesList({
            services: ServiceStore.services,
            loading: ServiceStore.loading,
            error: ServiceStore.error
        });
    } else {
        ServiceStore.loadServices().catch(function(err) {
            console.error('initServicesListPage: loadServices error:', err);
        });
    }
}

// ====================
// Helper Functions
// ====================

/**
 * Format currency to Vietnamese Dong
 * @param {number} amount - Amount in VND
 * @returns {string} Formatted amount
 */
function formatCurrency(amount) {
    return new Intl.NumberFormat('vi-VN', {
        style: 'currency',
        currency: 'VND',
        minimumFractionDigits: 0,
        maximumFractionDigits: 0
    }).format(amount);
}

/**
 * Navigate to service page
 * @param {Object} [params] - Optional parameters
 */
function navigateToService(params) {
    if (params) {
        app.views.current.router.navigate('/service/', { props: params });
    } else {
        app.views.current.router.navigate('/service/');
    }
}

// ====================
// Shopping Cart
// ====================
var cartSessionId = (function() {
  try {
    var sid = localStorage.getItem('vs_cart_session');
    if (!sid) {
      sid = 'cs_' + Date.now() + '_' + Math.random().toString(36).substr(2, 9);
      localStorage.setItem('vs_cart_session', sid);
    }
    return sid;
  } catch(e) { return ''; }
})();

function cartApiHeaders() {
  var headers = {
    'Content-Type': 'application/json',
    'Accept': 'application/json'
  };
  if (cartSessionId) headers['X-Cart-Session'] = cartSessionId;
  try {
    var token = localStorage.getItem('token');
    if (token) headers['Authorization'] = 'Bearer ' + token;
  } catch(e) {}
  return headers;
}

var CART_API = API_BASE_URL + '/cart';

// Framework7 Route Events
$$(document).on('page:init', '.page[data-name="service"]', function (e) {
  var serviceId = e.detail.route.query.service;
  initServicePage(serviceId);
});

$$(document).on('page:init', '.page[data-name="cart"]', function (e) {
  // Track navigation to cart
  if (window.NavigationStore) {
    window.NavigationStore.push('cart', {});
  }
  
  // Handle cart back button
  var pageEl = e.detail.el;
  if (pageEl) {
    var backBtn = pageEl.querySelector('.cart-back-btn');
    if (backBtn) {
      backBtn.addEventListener('click', function(evt) {
        evt.preventDefault();
        evt.stopPropagation();
        goBack();
      });
    }
  }
  
  loadCartPage();
});

$$(document).on('page:init', '.page[data-name="signin"]', function (e) {
  initPasswordToggles(e.detail.el);
  initAuthForms(e.detail.el);
});

$$(document).on('page:init', '.page[data-name="signup"]', function (e) {
  initPasswordToggles(e.detail.el);
  initAuthForms(e.detail.el);
});

// Handle Tab Bar Visibility globally using routeChange
if (app) {
  app.on('routeChange', function (newRoute, previousRoute, router) {
    var path = newRoute.path;
    var isTab = !path || path === '/' || path === '/home/' || path === '/services/' || path === '/orders/' || path === '/profile/';
    var tabBarWrap = document.querySelector('.vs-tab-bar-wrap');
    if (tabBarWrap) {
      tabBarWrap.style.display = isTab ? '' : 'none';
    }
    
    // Fix for browser back button leaving lingering pages
    if (isTab) {
       setTimeout(function() {
          var f7Pages = document.querySelectorAll('.page');
          f7Pages.forEach(function(el) {
             // If we're on a tab, remove any page that is NOT the main view container
             if (el && el.parentNode && !el.classList.contains('page-current') && !el.closest('.tabs')) {
                el.parentNode.removeChild(el); 
             }
          });
       }, 300);
    }
  });
}

window.openCartPage = function() {
  if (app && app.views && app.views.main) {
    // Track navigation to cart
    if (window.NavigationStore) {
      window.NavigationStore.push('cart', {});
    }
    app.views.main.router.navigate('/cart/');
  }
};

function fetchCart() {
  return fetch(CART_API, { headers: cartApiHeaders() })
    .then(function(r) { return r.json(); })
    .then(function(res) {
      if (res.status && res.data) {
        if (res.data.session_id) {
          cartSessionId = res.data.session_id;
          try { localStorage.setItem('vs_cart_session', cartSessionId); } catch(e) {}
        }
        updateCartBadge(res.data.items_count || 0);
        return res.data;
      }
      updateCartBadge(0);
      return { items: [], subtotal: 0, total: 0, items_count: 0 };
    })
    .catch(function() {
      return { items: [], subtotal: 0, total: 0, items_count: 0 };
    });
}

function addToCart(serviceId, quantity, btnEl) {
  if (btnEl) {
    flyToCartAnimation(btnEl);
  }
  return fetch(CART_API + '/items', {
    method: 'POST',
    headers: cartApiHeaders(),
    body: JSON.stringify({ service_id: serviceId, quantity: quantity || 1 })
  })
    .then(function(r) { return r.json(); })
    .then(function(res) {
      if (res.status && res.data) {
        if (res.data.session_id) {
          cartSessionId = res.data.session_id;
          try { localStorage.setItem('vs_cart_session', cartSessionId); } catch(e) {}
        }
        updateCartBadge(res.data.items_count || 0);
        app.toast.create({ text: 'Đã thêm vào giỏ hàng!', position: 'top', closeTimeout: 1500, cssClass: 'color-green' }).open();
        return res.data;
      } else {
        app.toast.create({ text: res.message || 'Không thể thêm vào giỏ.', position: 'top', closeTimeout: 2000 }).open();
        return null;
      }
    })
    .catch(function(err) {
      console.error('addToCart error:', err);
      app.toast.create({ text: 'Lỗi kết nối. Vui lòng thử lại.', position: 'top', closeTimeout: 2000 }).open();
      return null;
    });
}

function updateCartItem(itemId, quantity) {
  return fetch(CART_API + '/items/' + itemId, {
    method: 'PUT',
    headers: cartApiHeaders(),
    body: JSON.stringify({ quantity: quantity })
  })
    .then(function(r) { return r.json(); })
    .then(function(res) {
      if (res.status && res.data) {
        updateCartBadge(res.data.items_count || 0);
        return res.data;
      }
      return null;
    });
}

function removeCartItem(itemId) {
  return fetch(CART_API + '/items/' + itemId, {
    method: 'DELETE',
    headers: cartApiHeaders()
  })
    .then(function(r) { return r.json(); })
    .then(function(res) {
      if (res.status && res.data) {
        updateCartBadge(res.data.items_count || 0);
        return res.data;
      }
      return null;
    });
}

function clearCart() {
  app.dialog.confirm('Bạn có chắc muốn xoá toàn bộ giỏ hàng?', 'Xoá giỏ hàng', function() {
    fetch(CART_API, { method: 'DELETE', headers: cartApiHeaders() })
      .then(function(r) { return r.json(); })
      .then(function(res) {
        updateCartBadge(0);
        renderCartPage({ items: [], subtotal: 0, total: 0, items_count: 0 });
        app.toast.create({ text: 'Đã xoá giỏ hàng.', position: 'top', closeTimeout: 1500 }).open();
      });
  });
}

function updateCartBadge(count) {
  var badges = document.querySelectorAll('.vs-cart-badge');
  badges.forEach(function(badge) {
    if (count > 0) {
      badge.textContent = count > 99 ? '99+' : count;
      badge.style.display = 'flex';
    } else {
      badge.style.display = 'none';
    }
  });
}

// openCartPage is defined above using F7 router (window.openCartPage)

function loadCartPage() {
  var loading = document.getElementById('cart-loading');
  var empty = document.getElementById('cart-empty');
  var list = document.getElementById('cart-items-list');
  var bottomBar = document.getElementById('cart-bottom-bar');
  var clearBtn = document.getElementById('cart-clear-btn');

  if (loading) loading.style.display = '';
  if (empty) empty.style.display = 'none';
  if (list) list.style.display = 'none';
  if (bottomBar) bottomBar.style.display = 'none';
  if (clearBtn) clearBtn.style.display = 'none';

  fetchCart().then(function(data) {
    if (loading) loading.style.display = 'none';
    renderCartPage(data);
  });
}

function renderCartPage(data) {
  var empty = document.getElementById('cart-empty');
  var list = document.getElementById('cart-items-list');
  var bottomBar = document.getElementById('cart-bottom-bar');
  var totalPrice = document.getElementById('cart-total-price');
  var clearBtn = document.getElementById('cart-clear-btn');
  var loading = document.getElementById('cart-loading');

  if (loading) loading.style.display = 'none';

  if (!data || !data.items || data.items.length === 0) {
    if (empty) empty.style.display = '';
    if (list) list.style.display = 'none';
    if (bottomBar) bottomBar.style.display = 'none';
    if (clearBtn) clearBtn.style.display = 'none';
    return;
  }

  if (empty) empty.style.display = 'none';
  if (list) list.style.display = '';
  if (bottomBar) bottomBar.style.display = '';
  if (clearBtn) clearBtn.style.display = '';

  var html = '';
  data.items.forEach(function(item) {
    var priceFormatted = formatPrice(item.price);
    var subtotalFormatted = formatPrice(item.subtotal);
    var durationText = item.duration_days ? item.duration_days + ' ngày' : '';

    html += '<div class="vs-cart-item" id="cart-item-' + item.id + '">' +
      '<div class="vs-cart-item-icon"><i class="icon f7-icons">tv</i></div>' +
      '<div class="vs-cart-item-info">' +
        '<h3 class="vs-cart-item-name">' + escapeHtml(item.service_name) + '</h3>' +
        (durationText ? '<p class="vs-cart-item-duration">' + durationText + '</p>' : '') +
        '<div class="vs-cart-item-bottom">' +
          '<span class="vs-cart-item-price">' + subtotalFormatted + 'đ</span>' +
          '<div class="vs-qty-controls">' +
            '<button class="vs-qty-btn' + (item.quantity <= 1 ? ' disabled' : '') + '" onclick="changeCartQty(' + item.id + ', ' + (item.quantity - 1) + ')">−</button>' +
            '<span class="vs-qty-value">' + item.quantity + '</span>' +
            '<button class="vs-qty-btn' + (item.quantity >= 10 ? ' disabled' : '') + '" onclick="changeCartQty(' + item.id + ', ' + (item.quantity + 1) + ')">+</button>' +
          '</div>' +
        '</div>' +
      '</div>' +
      '<button class="vs-cart-item-delete" onclick="deleteCartItem(' + item.id + ')"><i class="icon f7-icons">trash</i></button>' +
    '</div>';
  });

  if (list) list.innerHTML = html;
  if (totalPrice) totalPrice.textContent = formatPrice(data.total) + 'đ';
}

function changeCartQty(itemId, newQty) {
  if (newQty < 1) {
    deleteCartItem(itemId);
    return;
  }
  app.preloader.show();
  updateCartItem(itemId, newQty).then(function(data) {
    app.preloader.hide();
    if (data) renderCartPage(data);
  });
}

function deleteCartItem(itemId) {
  var el = document.getElementById('cart-item-' + itemId);
  if (el) el.classList.add('removing');
  setTimeout(function() {
    removeCartItem(itemId).then(function(data) {
      if (data) renderCartPage(data);
    });
  }, 250);
}

function checkoutCart() {
  app.dialog.alert('Tính năng thanh toán giỏ hàng sẽ sớm ra mắt!', 'Thông báo');
}

function flyToCartAnimation(btnEl) {
  var cartIcon = document.querySelector('.vs-header-cart') || document.querySelector('.vs-cart-badge');
  if (!cartIcon || !btnEl) return;

  var btnRect = btnEl.getBoundingClientRect();
  var cartRect = cartIcon.getBoundingClientRect();

  var flyEl = document.createElement('div');
  flyEl.className = 'vs-fly-item';
  flyEl.innerHTML = '<i class="icon f7-icons">tv</i>';
  flyEl.style.top = btnRect.top + 'px';
  flyEl.style.left = btnRect.left + 'px';
  document.body.appendChild(flyEl);

  requestAnimationFrame(function() {
    flyEl.style.top = cartRect.top + 'px';
    flyEl.style.left = cartRect.left + 'px';
    flyEl.classList.add('flying');
  });

  setTimeout(function() {
    flyEl.remove();
  }, 700);
}

function formatPrice(num) {
  if (!num && num !== 0) return '0';
  return Number(num).toLocaleString('vi-VN');
}

// Init cart badge on app load
setTimeout(function() {
  fetch(CART_API + '/count', { headers: cartApiHeaders() })
    .then(function(r) { return r.json(); })
    .then(function(res) {
      if (res.status && res.data) {
        updateCartBadge(res.data.count || 0);
      }
    })
    .catch(function(err) {
      console.error('Cart count fetch error:', err);
    });
}, 500);

// Ensure Tab Bar logic is applied correctly after initial page load routing completes 
function enforceTabBarVisibility() {
  var hash = window.location.hash || '';
  var isAuth = hash.includes('/signin') || hash.includes('/signup');
  var tabBarWrap = document.querySelector('.vs-tab-bar-wrap');
  if (tabBarWrap) {
    tabBarWrap.style.display = isAuth ? 'none' : '';
  }
}
window.addEventListener('hashchange', enforceTabBarVisibility);
window.addEventListener('load', enforceTabBarVisibility);
enforceTabBarVisibility();
