import 'core-js/stable';
import '../lib/global-dependencies';

import Vue from 'vue';
import VueLazyload from 'vue-lazyload';

import router from './router';

const AppView = () =>
  import(/* webpackChunkName: "/chunk/AppView" */ './AppView');

import findLazyElements from '../lib/lazy-load';

try {
  window.axios = require('axios/index');
  window.axios.defaults.headers.common = {
    'X-WP-Nonce': window.siteData.nonce,
    'X-Requested-With': 'XMLHttpRequest',
  };
} catch (e) {
  throw new Error('Axios not available');
}

window.EventBus = new (class {
  constructor() {
    this.vue = new Vue();
  }

  fire(event, data = null) {
    this.vue.$emit(event, data);
  }

  listen(event, callback) {
    this.vue.$on(event, callback);
  }
})();

Vue.use(VueLazyload, {
  observer: true,
  dispatchEvent: true,
  lazyComponent: true,
  observerOptions: {
    root: null,
    rootMargin: '600px',
    threshold: [0],
  },
});

Vue.mixin({
  mounted() {
    findLazyElements();
    //$(this.$el).foundation();
  },
  updated() {
    this.$nextTick(function() {
      findLazyElements();
    });
  },
  destroyed() {
    // eslint-disable-next-line
    //$(this.$el).foundation.destroy();
  },
});

new Vue({
  el: '#app',
  router,
  render: (h) => h(AppView),
});

if (window.siteData.inApp !== 'true') {
  window.EventBus.listen('headerReady', () => {
    const Menu = () =>
      import(
        /* webpackChunkName: "/chunk/partials/WPMenu" */ '../components/widgets/WPMenu'
      );

    window.MenuView = new Vue({
      el: '#main-animated-menu',
      data: {
        menuName: 'primary',
      },
      router,
      computed: {
        mainMenu: {
          get: function() {
            return window.menu[this.menuName];
          },
          set: function(menuNameControl) {
            this.menuName = menuNameControl;
          },
        },
      },
      watch: {
        menuName(newVal, oldVal) {
          if (oldVal !== newVal) {
            window.EventBus.fire('menuChanged');
          }
        },
      },
      render: (h) => h(Menu),
    });
  });
}
