'use strict';
if(!window.top.aspkey){
	throw new Error('missing a public key');
}
const applicationServerPublicKey = window.top.aspkey;
//const pushButton = document.querySelector('.pushtoglbtn');
let isSubscribed = false;
let swRegistration = null;
function urlB64ToUint8Array(base64String) {
  const padding = '='.repeat((4 - base64String.length % 4) % 4);
  const base64 = (base64String + padding)
    .replace(/\-/g, '+')
    .replace(/_/g, '/');
  const rawData = window.atob(base64);
  const outputArray = new Uint8Array(rawData.length);
  for (let i = 0; i < rawData.length; ++i) {
    outputArray[i] = rawData.charCodeAt(i);
  }
  return outputArray;
}

function initialiseUI() {
  var toggler = document.getElementById("notifications_susbcription_toggle_checkbox");
  if (toggler === null) return;
  toggler.addEventListener('click', function() {
    console.log('clicked');
    if (isSubscribed) {
      console.log('unsubscribe user');
      unsubscribeUser();
    } else {
      console.log('subscribe user');
      subscribeUser();
    }
  });
  //updatebtn();
  swRegistration.pushManager.getSubscription()
  .then(function(subscription) {
    console.log("subscription:",subscription);
    isSubscribed = !(subscription === null);
    if (isSubscribed) {
      console.log('User IS subscribed.');
    } else {
      console.log('User is NOT subscribed.');
    }
    updateBtn();
  });
}

function updateBtn() {
  if (Notification.permission === 'denied') {
    document.getElementById("notifications_susbcription_status_label").innerHTML = 'Push Messaging Blocked.';
    //pushButton.disabled = true;
    document.getElementById("notifications_susbcription_toggle_checkbox").checked = false;
    document.getElementById("notifications_susbcription_toggle_checkbox").disabled = true;
    //updateSubscriptionOnServer(null);
    return;
  }
  console.log("isSubscribed = ",isSubscribed);
  if (isSubscribed) {
    document.getElementById("notifications_susbcription_status_label").innerHTML = ' Unsubscribe from Notifications';
    document.getElementById("notifications_susbcription_toggle_checkbox").checked = true;
  } else {
    document.getElementById("notifications_susbcription_status_label").innerHTML = ' Get Notifications of New Posts';
    document.getElementById("notifications_susbcription_toggle_checkbox").checked = false;
  }
  //pushButton.disabled = false;
}

function subscribeUser() {
  const applicationServerKey = urlB64ToUint8Array(applicationServerPublicKey);
  swRegistration.pushManager.subscribe({
    userVisibleOnly: true,
    applicationServerKey: applicationServerKey
  })
  .then(function(subscription) {
    console.log('User is subscribed.');
    updateSubscriptionOnServer(subscription, 'subscribe');
    isSubscribed = true;
    updateBtn();
  })
  .catch(function(err) {
    //console.log('Failed to subscribe the user: ', err);
    updateBtn();
  });
}

function unsubscribeUser() {
  swRegistration.pushManager.getSubscription()
  .then(function(subscription) {
    updateSubscriptionOnServer(subscription, 'unsubscribe');
    console.log('User is unsubscribed.');
    isSubscribed = false;
    updateBtn();
    return subscription.unsubscribe();
  }).catch(function(err) {
    console.log('Failed to unsubscribe the user: ', err);
    updateBtn();
  });
}

function updateSubscriptionOnServer(subscription, axn) {
  // TODO: Send subscription to application server
  if (subscription) {
    const key = subscription.getKey('p256dh');
    const token = subscription.getKey('auth');
    fetch(window.location.href, {
      method: 'post',
      headers: new Headers({
        'Content-Type': 'application/json'
      }),
      body: JSON.stringify({
        uid: window.uid,
        endpoint: subscription.endpoint,
        key: key ? btoa(String.fromCharCode.apply(null, new Uint8Array(subscription.getKey('p256dh')))) : null,
        token: token ? btoa(String.fromCharCode.apply(null, new Uint8Array(subscription.getKey('auth')))) : null,
        axn: axn
      })
    }).then(function(response) {
      return response.json();
    }).then(function(response) {
      if(response.status == 'ok'){
        console.log(response);
      }else{
        console.log(response.error);
      }
    }).catch(function(err) {
      // Error :(
      console.log('error',err);
    });
  } else {
    //subscriptionDetails.classList.add('is-invisible');
  }
}