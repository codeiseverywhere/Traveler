<template>
  <iframe
    :src="url"
    frameborder="0"
    marginwidth="0"
    marginheight="0"
    scrolling="no"
    v-on:click="pushToGTM"
  ></iframe>
</template>

<script>
export default {
  props: ['src'],

  data() {
    return {
      url: null,
    };
  },

  methods: {
    pushToGTM: function() {
      if ({}.hasOwnProperty.call(window, 'GTMdataLayer')) {
        window.GTMdataLayer.push({
          event: 'trackEvent',
          eventCategory: 'Ad Banner',
          eventAction: 'click',
          eventLabel: 'Home Page 728x90',
        });
      }
    },
  },

  created() {
    let _this = this;
    _this.url = _this.src;
  },
};
</script>
