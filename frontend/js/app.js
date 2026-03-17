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
  fetch(DEBUG_LOG_ENDPOINT, {
    method: 'POST',
    headers: {'Content-Type': 'application/json'},
    body: JSON.stringify({
      hypothesisId: hypothesisId,
      location: location,
      message: message,
      data: data,
      timestamp: Date.now()
    })
  }).catch(function() {});
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
  // Initialize tabs
  tabs: {
    swipeable: true,
  },
  view: {
    browserHistory: true,
    browserHistoryAnimate: Framework7.device.ios ? false : true,
    main: true,
  },
  routes: [
    // Home Page (Landing Page) - init true for initial load
    {
      path: '/',
      url: 'pages/pages/home.html',
      options: {
        init: true
      }
    },
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
  ],
});

// ====================
// Tab Navigation
// ====================
var currentTab = 'home';
var pageCache = {};
var navigationHistory = ['home'];

// Handle link clicks within page container
document.addEventListener('click', function(e) {
  var link = e.target.closest('a.link');
  if (!link) return;
  
  var href = link.getAttribute('href');
  if (!href) return;
  
  // Only handle internal links that start with /
  if (href.startsWith('/') && !href.startsWith('//')) {
    e.preventDefault();
    e.stopPropagation();
    
    // Parse query string if any
    var path = href.split('?')[0];
    var query = {};
    if (href.includes('?')) {
      href.split('?')[1].split('&').forEach(function(param) {
        var parts = param.split('=');
        query[parts[0]] = parts[1];
      });
    }
    
    // Handle service page with service ID
    if (path === '/service/' || path === '/service') {
      navigationHistory.push('service');
      loadServicePage(query.service);
      return;
    }
    
    // Handle tab-level routes
    var tabMap = {
      '/': 'home',
      '/services/': 'services',
      '/orders/': 'orders',
      '/profile/': 'profile'
    };
    
    if (tabMap[path]) {
      switchTab(tabMap[path], null);
      return;
    }
    
    // Handle signin page
    if (path === '/signin/' || path === '/signin') {
      navigationHistory.push('signin');
      loadPage('pages/pages/signin.html');
      return;
    }
    
    console.log('Navigation to:', path, query);
  }
});

// Go back to previous page
function goBack() {
  // Show tab bar
  var tabBarWrap = document.querySelector('.vs-tab-bar-wrap');
  if (tabBarWrap) tabBarWrap.style.display = '';
  
  if (navigationHistory.length > 1) {
    navigationHistory.pop();
    var prevPage = navigationHistory[navigationHistory.length - 1];
    
    if (prevPage === 'home' || prevPage === 'services' || prevPage === 'orders' || prevPage === 'profile') {
      switchTab(prevPage, null);
    } else {
      switchTab('home', null);
    }
  } else {
    switchTab('home', null);
  }
  navigationHistory = [currentTab];
}

function loadServicePage(serviceId) {
  var pageContainer = document.getElementById('page-container');
  if (!pageContainer) return;
  
  // Hide tab bar on detail page
  var tabBarWrap = document.querySelector('.vs-tab-bar-wrap');
  if (tabBarWrap) tabBarWrap.style.display = 'none';
  
  app.preloader.show();
  
  fetch('pages/pages/service.html')
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
  var pageContainer = document.getElementById('page-container');
  if (!pageContainer) return;
  
  // Hide tab bar on non-tab pages
  var tabBarWrap = document.querySelector('.vs-tab-bar-wrap');
  if (tabBarWrap) tabBarWrap.style.display = 'none';
  
  app.preloader.show();
  
  fetch(url)
    .then(function(response) { return response.text(); })
    .then(function(html) {
      pageContainer.innerHTML = html;
      app.preloader.hide();
    })
    .catch(function(error) {
      app.preloader.hide();
      app.dialog.alert('Không thể tải trang.', 'Lỗi');
      console.error('loadPage error:', error);
    });
}

function switchTab(tabName, element) {
  if (tabName === currentTab && pageCache[tabName]) {
    return;
  }
  
  // Show tab bar
  var tabBarWrap = document.querySelector('.vs-tab-bar-wrap');
  if (tabBarWrap) tabBarWrap.style.display = '';
  
  var tabLinks = document.querySelectorAll('.vs-tab-item');
  tabLinks.forEach(function(link) {
    link.classList.remove('active');
    if (link.getAttribute('data-tab') === tabName) {
      link.classList.add('active');
    }
  });
  
  var pageContainer = document.getElementById('page-container');
  var url = '';
  
  switch(tabName) {
    case 'home':
      url = 'pages/pages/home.html';
      break;
    case 'services':
      url = 'pages/pages/services.html';
      break;
    case 'orders':
      url = 'pages/pages/orders.html';
      break;
    case 'profile':
      url = 'pages/pages/profile.html';
      break;
    default:
      url = 'pages/pages/home.html';
  }
  
  if (pageCache[tabName]) {
    pageContainer.innerHTML = pageCache[tabName];
    currentTab = tabName;
    afterTabLoaded(tabName);
    return;
  }
  
  app.preloader.show();
  
  fetch(url)
    .then(function(response) {
      return response.text();
    })
    .then(function(html) {
      pageCache[tabName] = html;
      pageContainer.innerHTML = html;
      currentTab = tabName;
      app.preloader.hide();
      afterTabLoaded(tabName);
    })
    .catch(function(error) {
      app.preloader.hide();
      app.dialog.alert('Không thể tải trang. Vui lòng thử lại.', 'Lỗi');
      console.error('switchTab error:', error);
    });
}

function afterTabLoaded(tabName) {
  switch(tabName) {
    case 'home':
      initHomePage();
      break;
    case 'services':
      initServicesListPage();
      break;
  }
}

// Load home page on initial load
function initApp() {
  console.log('App: initApp called, readyState:', document.readyState);
  debugLog('D', 'app.js:DOMContentLoaded', 'DOM ready', {});
  setTimeout(function() {
    console.log('App: Calling switchTab(home)');
    debugLog('D', 'app.js:timeout', 'Calling switchTab', {});
    switchTab('home', null);
  }, 500);
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
    fetch(page)
      .then((response) => response.text())
      .then((content) => {
        const xhrEntry = {
          url: page,
          time: Date.now(),
          content: content,
        };
        app.router.cache.xhr.push(xhrEntry);
      })
      .catch((error) => console.error(error));
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
  // Fallback for development
  var host = window.location.hostname;
  if (host === 'localhost' || host === '127.0.0.1') {
    // For local dev, you may need to change this to your actual API URL
    // Or make sure Laravel is running
    return 'https://tiemnhaduy.com/api/v1';
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

$$(document).on('page:init', '.page[data-name="service"]', function (e) {
    var serviceId = e.detail.route.query.service;
    initServicePage(serviceId);
});

function initServicePage(serviceId) {
    var pageEl = document.querySelector('.page[data-name="service"]');
    if (!pageEl) return;

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
            var iconName = isPremium ? 'star_fill' : 'tv';
            
            return '<a href="/service/?service=' + service.id + '" class="vs-service-card link">' +
                '<div class="vs-service-card-top">' +
                    '<div class="' + iconClass + '">' +
                        '<i class="icon f7-icons">' + iconName + '</i>' +
                    '</div>' +
                    '<div class="vs-service-details">' +
                        '<div class="vs-service-title-row">' +
                            '<h3 class="vs-service-name">' + escapeHtml(service.name) + '</h3>' +
                            '<div class="vs-service-price">' + priceText + 'đ</div>' +
                        '</div>' +
                        '<p class="vs-service-duration">Thời hạn: ' + durationText + '</p>' +
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

function initServicesListPage() {
    console.log('initServicesListPage: called');
    
    var containerEl = document.getElementById('services-list-container');
    if (!containerEl) {
        console.error('initServicesListPage: Container not found');
        return;
    }
    
    function escapeHtml(text) {
        var div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }

    function renderServicesList(state) {
        if (state.loading) {
            containerEl.innerHTML = '<div class="text-align-center" style="padding: 40px;"><div class="preloader"></div><p style="margin-top: 10px; color: var(--vs-text-secondary);">Đang tải dịch vụ...</p></div>';
            return;
        }
        
        if (state.error) {
            containerEl.innerHTML = '<p class="text-align-center" style="padding: 40px; color: var(--vs-text-secondary);">Không thể tải danh sách dịch vụ.</p>';
            return;
        }
        
        var services = state.services || [];
        if (services.length === 0) {
            containerEl.innerHTML = '<p class="text-align-center" style="padding: 40px; color: var(--vs-text-secondary);">Chưa có dịch vụ nào.</p>';
            return;
        }
        
        var servicesHtml = services.map(function(service) {
            var durationDays = service.duration_days || 30;
            var durationText = 'Thời hạn: ' + (durationDays >= 30 ? Math.round(durationDays / 30) * 30 + ' ngày' : durationDays + ' ngày');
            var priceText = service.price ? service.price.toLocaleString('vi-VN') : '0';
            
            var isPremium = service.price > 100000;
            var iconClass = isPremium ? 'vs-service-icon premium' : 'vs-service-icon';
            var iconName = isPremium ? 'star_fill' : 'tv';
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
                '<a href="/service/?service=' + service.id + '" class="vs-service-register-btn link">' +
                    'Đăng ký ngay <i class="icon f7-icons" style="font-size: 16px;">arrow_right</i>' +
                '</a>' +
            '</div>';
        }).join('');
        
        containerEl.innerHTML = servicesHtml;
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
