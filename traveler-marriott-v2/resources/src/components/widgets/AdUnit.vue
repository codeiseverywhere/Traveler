<template>
  <lazy-component
    tag="aside"
    class="ad-wrapper"
    @show="triggerBlock"
    v-if="src"
  >
    <!--    <span>Advertisement</span>-->
    <iframe
      :src="src"
      frameborder="0"
      marginwidth="0"
      marginheight="0"
      scrolling="no"
      v-on:click="pushToGTM"
    ></iframe>
  </lazy-component>
</template>

<script>
export default {
  name: 'm-ad-unit',
  props: ['src'],
  methods: {
    triggerBlock: function() {
      let _this = this;
      _this.$el.width = '300px';
    },
    pushToGTM: () => {
      let _this, eventLabel;
      _this = this;

      eventLabel = _this.src.replace(/([&?]cb=).*/, '');

      if ({}.hasOwnProperty.call(window, 'GTMdataLayer')) {
        window.GTMdataLayer.push({
          event: 'trackEvent',
          eventCategory: 'Ad Banner',
          eventAction: 'click',
          eventLabel: eventLabel,
        });
      }
    },
  },
};
</script>
