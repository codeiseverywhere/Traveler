<template>
  <div class="hero-slider hero-wrapper">
    <div class="owl-carousel">
      <div class="owl-slide" v-for="slide in slides" :key="slide.ID">
        <div class="aspect-ratio-16_9">
          <router-link :to="slide.link">
            <img
              loading="lazy"
              class="owl-image"
              :src="slide.image.url"
              :alt="slide.image.alt"
            />
          </router-link>
        </div>
        <div class="title-container owl-caption text-center">
          <strong class="category-label" v-html="slide.category" />
          <h2>
            <router-link :to="slide.link" v-html="slide.post_title" />
          </h2>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  name: 'm-owl',
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
        responsive: {
          0: {
            lazyLoad: true,
            mouseDrag: false,
            touchDrag: true,
            pullDrag: false,
          },
          1024: {
            lazyLoad: false,
          },
        },
      },
      origin: window.location.origin,
    };
  },

  watch: {
    $route(to, from) {
      let _this = this;

      if (_this.owl) {
        _this.owl.trigger('destroy.owl.carousel');
      }

      _this.owl = null;
    },
  },

  mounted() {
    let _this;
    _this = this;
    _this.slides.forEach(function(slide, index) {
      _this.slides[index].link = slide.link.replace(_this.origin, '');

      if (typeof slide.title !== 'undefined') {
        _this.slides[index].post_title = slide.title.rendered;
      }
    });

    setTimeout(function() {
      _this.owl = $('.hero-slider .owl-carousel').owlCarousel(
        _this.owl_options,
      );
    }, 100);
  },

  destroyed() {
    let _this = this;
    if (_this.owl) {
      _this.owl.trigger('destroy.owl.carousel');
    }
  },
};
</script>
