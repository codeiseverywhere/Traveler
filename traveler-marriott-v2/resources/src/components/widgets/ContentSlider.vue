<template>
  <lazy-component @show="triggerSlider" class="content-slider">
    <div class="owl-carousel">
      <div class="owl-slide" v-for="(slide, key) in slides" :key="key">
        <div class="aspect-ratio-16_9">
          <img
            loading="lazy"
            class="orbit-image native-lazyload-js-fallback"
            :data-src="slide.image"
            :alt="slide.headline"
          />
        </div>
        <div class="owl-caption caption-container">
          <h3 class="post-title" v-html="slide.headline" />
          <div class="caption-content" v-html="slide.content"></div>
          <div class="caption-credit font-italic" v-html="slide.credit"></div>
        </div>
        <a :href="slide.link" class="overlay" />
      </div>
    </div>
  </lazy-component>
</template>

<script>
export default {
  name: 'm-content-slider',
  props: ['slides'],

  data() {
    return {
      owl: null,
      owl_options: {
        items: 1,
        nav: true,
        margin: 0,
        loop: true,
        dots: false,
        autoplay: true,
        callbacks: true,
        autoHeight: false,
        autoplayTimeout: 10000,
        autoplayHoverPause: true,
      },
      contentSlider: false,
      origin: window.location.origin,
    };
  },

  methods: {
    triggerSlider: function() {
      let _this = this;

      setTimeout(() => {
        _this.contentSlider = $('.content-slider .owl-carousel');
        if (_this.contentSlider.length) {
          _this.owl = _this.contentSlider.owlCarousel(_this.owl_options);
        }
      }, 100);
    },
  },

  destroyed() {
    let _this = this;
    if (_this.owl) {
      _this.owl.trigger('destroy.owl.carousel');
    }
  },
};
</script>
