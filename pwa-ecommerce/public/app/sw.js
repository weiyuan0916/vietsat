self.addEventListener('push', function (event) {
  var payload = { title: 'Thông báo', body: '' };
  if (event.data) {
    try {
      var json = event.data.json();
      if (json.title) payload.title = json.title;
      if (json.body) payload.body = json.body;
      if (json.icon) payload.icon = json.icon;
      if (json.data) payload.data = json.data;
    } catch (e) {
      payload.body = event.data.text();
    }
  }
  var options = {
    body: payload.body,
    icon: payload.icon,
    data: payload.data || {},
  };
  event.waitUntil(self.registration.showNotification(payload.title, options));
});

self.addEventListener('notificationclick', function (event) {
  event.notification.close();
  var url = (event.notification.data && event.notification.data.url) ? event.notification.data.url : '/app/';
  event.waitUntil(
    self.clients.matchAll({ type: 'window', includeUncontrolled: true }).then(function (clientList) {
      for (var i = 0; i < clientList.length; i++) {
        var client = clientList[i];
        if ('focus' in client) {
          return client.focus();
        }
      }
      if (self.clients.openWindow) {
        return self.clients.openWindow(url);
      }
    })
  );
});
