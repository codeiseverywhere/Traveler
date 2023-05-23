<template>
  <aside v-if="'1' === siteData.site_id" class="grid-container">
    <lazy-component
      class="pubexchange_module widget"
      id="pubexchange_below_content"
      :data-pubexchange-module-id="pubxModuleID"
      @show="triggerBlock"
    />
  </aside>
</template>

<script>
import { createTag } from '../../../mixins/utils';

export default {
  data() {
    return {
      observer: false,
      pubxModuleID: '1981',
      pubxID: 'marriott_traveler',
      siteData: window.siteData,
    };
  },

  methods: {
    triggerBlock: function() {
      let _this = this;

      if ('1' !== window.siteData.site_id) {
        _this.pubxID = 'marriott_traveler_espaol';
        _this.pubxModuleID = '2979';
      }

      window.PUBX = {}.hasOwnProperty.call(window, 'PUBX')
        ? window.PUBX
        : { pub: _this.pubxID, discover: false, lazy: true };
      if (!document.getElementById('pubexchange-jssdk')) {
        createTag(
          'https://main.pubexchange.com/loader.min.js',
          'pubexchange-jssdk',
        );
      }
    },
  },

  mounted() {
    let _this = this;

    // Create an observer instance linked to the callback function
    _this.observer = new MutationObserver(function(mutationsList, observer) {
      for (let mutation of mutationsList) {
        if (mutation.type === 'childList') {
          Array.from(_this.$el.querySelectorAll('.pe-module')).forEach((el) => {
            if (false === el.parentNode.classList.contains('block-wrapper')) {
              let wrapper = document.createElement('div');
              el.parentNode.insertBefore(wrapper, null);
              wrapper.classList.add('block-wrapper');

              el.parentNode.removeChild(el);
              wrapper.appendChild(el);
            }
          });
        }
      }
    });

    // Start observing the target node for configured mutations
    _this.observer.observe(_this.$el, {
      attributes: true,
      childList: true,
      subtree: true,
    });
  },

  destroyed() {
    let _this = this;

    _this.observer.disconnect();
  },
};
</script>

<style lang="scss">
@import 'PubExchange';
</style>
