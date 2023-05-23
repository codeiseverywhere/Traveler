<template>
  <lazy-component @show="triggerSlider" class="video-slider">
    <div class="owl-carousel">
      <div class="item-video" v-for="slide in slides" :key="slide.ID">
        <div class="aspect-ratio-16_9 flex-video widescreen">
          <a class="owl-video" :href="slide.media" />
        </div>
        <div class="title-container text-center">
          <strong class="category-label text-uppercase">
            <a
              href="https://www.youtube.com/channel/UCpDtA9d5Z8SsNEnsVgwq9LA"
              target="_blank"
              rel="external"
              >{{ siteData.i18n.video }}</a
            >
          </strong>
          <h2 v-html="slide.title" />
        </div>
      </div>
    </div>
  </lazy-component>
</template>

<script>
export default {
  name: 'm-video-slider',
  props: ['slides'],

  data() {
    return {
      owl: null,
      owl_options: {
        items: 1,
        nav: true,
        loop: true,
        dots: false,
        video: true,
        autoplay: true,
        callbacks: true,
        lazyLoad: false,
        autoHeight: false,
        videoWidth: 1920,
        videoHeight: 1080,
        autoplayTimeout: 10000,
        autoplayHoverPause: true,
        responsive: {
          0: {
            mouseDrag: false,
            touchDrag: true,
            pullDrag: false,
          },
        },
      },
      siteData: window.siteData,
    };
  },

  methods: {
    triggerSlider: function() {
      let _this, videoSlider;
      _this = this;

      setTimeout(() => {
        videoSlider = $('.video-slider .owl-carousel');
        if (videoSlider.length) {
          _this.owl = videoSlider
            .owlCarousel(_this.owl_options)
            .on('play.owl.video', (el) => {
              $(el.currentTarget)
                .find('.title-container')
                .hide();
            })
            .on('stop.owl.video', (el) => {
              $(el.currentTarget)
                .find('.title-container')
                .show();
            });
        }
      }, 200);
    },
  },

  watch: {
    $route(to, from) {
      let _this = this;
      if (_this.owl) {
        _this.owl.trigger('destroy.owl.carousel');
      }

      _this.owl = null;
      _this.heroSlider = false;
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
