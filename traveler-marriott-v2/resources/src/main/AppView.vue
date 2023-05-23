<template>
  <main id="app" class="page-wrapper pageview">
    <m-header />
    <router-view />
    <m-footer />
  </main>
</template>

<script>
import { branchTag, createTag, segmentTag } from '../mixins/utils';

export default {
  name: 'traveler',

  components: {
    'm-header': () =>
      import(
        /* webpackChunkName: "/chunk/partials/Header" */ '../components/partials/Header/Header'
      ),
    'm-footer': () =>
      import(
        /* webpackChunkName: "/chunk/partials/Footer" */ '../components/partials/Footer/Footer'
      ),
  },

  watch: {
    $route(to, from) {
      if ({}.hasOwnProperty.call(window, 'GTMdataLayer')) {
        window.GTMdataLayer.push({
          event: 'trackPageview',
          url: window.location.href,
        });
      }
      if ({}.hasOwnProperty.call(window, 'analytics')) {
        window.analytics.page({
          path: window.location.pathname,
          referrer: from.path,
          url: window.location.href,
        });
      }
    },
  },

  mounted() {
    window.addEventListener(
      'load',
      () => {
        window.searchModal = new Foundation.Reveal($('#searchModal'));

        if (Foundation.MediaQuery.is('small only')) {
          window.advertising.adDesktop = window.advertising.adMobile;
        }

        if (process.env.NODE_ENV === 'production') {
          createTag(
            `https://assets.adobedtm.com/launch-${
              process.env.BUILD_ENV === 'production'
                ? 'EN3963523be4674e5591a9c4d516697352'
                : 'ENc5bce53fadfd42dbb08b27acb53dc333-staging'
            }.min.js`,
            'dtm-sdk',
          );

          // Load Branch
          branchTag();

          if ( '1' === window.siteData.site_id ) {
            // Load Segment
            segmentTag(
              process.env.BUILD_ENV === 'production'
                ? 'lHKy6lyFFjGauSvQqm6kbA4YEDFkCUEY'
                : '2eiSBaVvub9xDGDwWywwcIan1OvGmOaJ',
            );
          }
        }
      },
      { once: true },
    );
  },
};
</script>

<style lang="scss">
@import '../lib/global-dependencies';
</style>
