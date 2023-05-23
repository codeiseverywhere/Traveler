<template>
  <div class="social-share">
    <div
      class="addthis_toolbox addthis_default_style addthis_32x32_style"
      :addthis:title="title"
      :addthis:url="url"
    >
      <a class="addthis_button_facebook" />
      <a class="addthis_button_twitter" />
      <a class="addthis_button_flipboard" />
      <a class="addthis_button_email" />
      <a class="addthis_button_compact" />
    </div>
  </div>
</template>

<script>
import { createTag } from '../../../mixins/utils';

export default {
  props: ['title', 'url'],

  beforeCreate() {
    createTag(
      'https://s7.addthis.com/js/300/addthis_widget.js#pubid=ra-54f0c6344030f20d',
      'addthis-sdk',
    );
    window.EventBus.listen('scriptReady', (id) => {
      if ('addthis' === id) {
        let addthis_config = {
          ui_508_compliant: true,
        };
        let addthis_share = {
          // ... other options
          url_transforms: {
            shorten: {
              twitter: 'bitly',
            },
          },
          shorteners: {
            bitly: {},
          },
        };
      }
    });

    window.scrollTo(0, 0);
  },

  mounted() {
    const isAddThisLoaded = setInterval(() => {
      if ({}.hasOwnProperty.call(window, 'addthis')) {
        clearInterval(isAddThisLoaded);
        window.addthis.toolbox('.addthis_toolbox');
        window.addthis.init();
      }
    }, 300);
  },
};
</script>

<style lang="scss">
@import 'SocialShare';
</style>
