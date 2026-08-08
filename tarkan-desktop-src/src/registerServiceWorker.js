/* eslint-disable no-console */


import 'element-plus/es/components/message-box/style/css'

import { register } from 'register-service-worker'
import ElMessageBox from "element-plus/es/components/message-box";
import KT from "./tarkan/func/kt";


if (process.env.NODE_ENV === 'production') {
  register(`${process.env.BASE_URL}service-worker.js`, {
    ready (r) {

      window.$sc = r;

      console.log(
        'App is being served from cache by a service worker.\n' +
        'For more details, visit https://goo.gl/AFskqB'
      )
    },
    registered () {
      console.log('Service worker has been registered.')
    },
    cached () {
      console.log('Content has been cached for offline use.')
    },
    updatefound () {
      console.log('New content is downloading.')
    },
    updated () {
      console.log('New content is available; please refresh.');
      ElMessageBox.confirm(KT('NEW_VERSION_AVAILABLE'))
          .then(() => {
            window.location.reload(true);
          })
          .catch(() => {
            // catch error
          })
    },
    offline () {
      console.log('No internet connection found. App is running in offline mode.')
    },
    error (error) {
      console.error('Error during service worker registration:', error)
    }
  })
}
