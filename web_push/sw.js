'use strict';
self.addEventListener('push', function (event) {
    if (!(self.Notification && self.Notification.permission === 'granted')) {
        return;
    }
    var _title;
    var _options;
    /*const sendNotification = body => {
      // you could refresh a notification badge here with postMessage API
      var title = _title;

      return self.registration.showNotification(title, {
          body,
      });
    };*/

    
    if (event.data) {
        var notification_data = JSON.parse(event.data.text());
        var message = notification_data.msg;
        _title = notification_data.title;
        _options = {
          body:notification_data.msg,
          icon:notification_data.icon,
          badge:notification_data.badge
        }
        self.notificationURL = notification_data.url;
        //event.waitUntil(sendNotification(message));
    }else{
        _title = 'Push Codelab';
        _options = {
          body: 'Yay it works.',
          icon: 'https://dart.gallery/imgs/icon.png',
          badge: 'https://dart.gallery/imgs/badge.png'
        };
    }
    event.waitUntil(self.registration.showNotification(_title, _options));
});
self.addEventListener('notificationclick', function(event) {
  console.log('[Service Worker] Notification click Received.');
  //console.log(event);
  //console.log(self);
  event.notification.close();

  event.waitUntil(
    clients.openWindow(self.notificationURL)//modified from tutorial to make it more dynamic
  );
});