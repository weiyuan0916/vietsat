(function () {
  'use strict';

  function urlBase64ToUint8Array(base64String) {
    var padding = '='.repeat((4 - (base64String.length % 4)) % 4);
    var base64 = (base64String + padding).replace(/-/g, '+').replace(/_/g, '/');
    var rawData = atob(base64);
    var outputArray = new Uint8Array(rawData.length);
    for (var i = 0; i < rawData.length; ++i) {
      outputArray[i] = rawData.charCodeAt(i);
    }
    return outputArray;
  }

  function getSwScopeUrl() {
    return new URL('.', window.location.href).href;
  }

  var WebPush = {
    fetchVapidKey: function () {
      var api = new window.ApiService(window.AppConfig.apiBaseUrl);
      return api.get('/webpush/vapid-public-key').then(function (res) {
        var pk = res && res.data && res.data.public_key ? res.data.public_key : null;
        if (!pk && res && res.public_key) pk = res.public_key;
        if (!pk) throw new Error('No VAPID public key');
        return pk;
      });
    },

    subscribe: function (orderCode) {
      if (!('serviceWorker' in navigator) || !('PushManager' in window)) {
        return Promise.resolve(false);
      }
      var scopeUrl = getSwScopeUrl();
      return navigator.serviceWorker
        .register(new URL('sw.js', scopeUrl).href, { scope: scopeUrl })
        .then(function (registration) {
          return registration.pushManager.getSubscription().then(function (existing) {
            if (existing) return existing;
            return WebPush.fetchVapidKey().then(function (publicKey) {
              return registration.pushManager.subscribe({
                userVisibleOnly: true,
                applicationServerKey: urlBase64ToUint8Array(publicKey),
              });
            });
          });
        })
        .then(function (subscription) {
          if (!subscription) return false;
          var j = subscription.toJSON();
          var api = new window.ApiService(window.AppConfig.apiBaseUrl);
          return api
            .post('/orders/' + encodeURIComponent(orderCode) + '/push-subscriptions', {
              endpoint: j.endpoint,
              keys: j.keys,
              content_encoding: j.encoding || null,
            })
            .then(function () {
              return true;
            });
        })
        .catch(function (err) {
          console.warn('WebPush.subscribe:', err);
          return false;
        });
    },

    markPaidTest: function (orderCode) {
      var api = new window.ApiService(window.AppConfig.apiBaseUrl);
      return api.post('/orders/' + encodeURIComponent(orderCode) + '/mark-paid-test', {});
    },
  };

  window.WebPush = WebPush;
})();
