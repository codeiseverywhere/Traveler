<template>
  <section class="page-alt" v-if="loaded === true">
    <vue-headful
      :title="page.yoast_meta.title"
      :description="page.yoast_meta.metadesc"
      :url="page.yoast_meta.canonical"
      :lang="page.yoast_meta.locale"
      :ogLocale="page.yoast_meta.locale"
      :image="page.image.url"
      :image-width="page.image.width"
      :image-height="page.image.height"
      :image-alt="page.image.alt"
    />
    <div class="hero-wrapper top-hero">
      <div :class="aspectRatioClass">
        <video
          v-if="coverVideo !== ''"
          autoplay
          playsinline
          webkit-playsinline
          loop
          muted
          defaultMuted
          preload="auto"
          :poster="page.image.url"
          :width="page.acf.video.width"
          :height="page.acf.video.height"
        >
          <source :src="coverVideo" type="video/mp4" />
        </video>
        <div
          v-else
          class="hero-bg"
          :style="{
            'background-image': 'url(' + page.image.url + ')',
          }"
        >
          <h1 :class="heroTitle" v-html="page.title.rendered" />
        </div>
      </div>
    </div>
    <div class="grid-container">
      <div v-html="page.content.rendered"></div>

      <div :class="showMoments">
        <m-moments :title="page.title.rendered" />
      </div>
    </div>
  </section>
</template>

<script>

import magnificPopup from 'magnific-popup';
window.magnificPopup = magnificPopup;
function gcd(a, b) {
  return b === 0 ? a : gcd(b, a % b);
}

import '../../lib/tiled-gallery-carousel-without-jetpack-dependencies';
import Parallax from '../../mixins/parallax';

const Moments = () =>
  import(
    /* webpackChunkName: "/chunk/widgets/Moments" */ '../widgets/Moments/Moments'
  );

export default {
  name: 'PageHub',
  props: ['slug', 'id', 'name', 'type'],
  data() {
    return {
      page: {},
      loaded: false,
      methods: {
        getData: (slug, callback) => {
          let type = this.type ? this.type : 'pages';

          window.axios
            .get(
              window.siteData.rest_base_page +
                'wp/v2/' +
                type +
                '?slug=' +
                slug,
            )
            .then((response) => {
              // JSON responses are automatically parsed.
              callback(response.data[0]);
            })
            .catch((e) => {
              console.log(e);
            });
        },
        loadInteractiveHelpers: () => {
          setTimeout(() => {
            $('.wp-block-gallery').magnificPopup({
              delegate: 'a',
              type: 'image',
              tLoading: 'Loading image #%curr%...',
              mainClass: 'mfp-img-mobile',
              gallery: {
                enabled: true,
                navigateByImgClick: true,
                preload: [1, 2], // Will preload 0 - before current, and 1 after the current image
              },
              image: {
                tError:
                  '<a href="%url%">The image #%curr%</a> could not be loaded.',
                titleSrc: function(item) {
                  return item.el
                    .parent()
                    .find('figcaption')
                    .html();
                },
              },
            });
          }, 800);
        },
      },
    };
  },

  mixins: [Parallax],

  components: {
    'm-moments': Moments,
  },

  computed: {
    coverVideo() {
      return this.page.acf.video !== false ? this.page.acf.video.url : '';
    },
    aspectRatioClass() {
      let _class = 'aspect-ratio-16_9';

      if (this.page.acf.video !== false && this.page.acf.video.height <= 400) {
        _class = 'aspect-ratio-21_9';
      }
      return _class;
    },
    showMoments() {
      let _class = '';

      if (this.page.acf.visibility_on_page === 'hide') {
        _class = 'hideMoments';
      }
      return _class;
    },
    heroTitle() {
      let _class = 'hero-title';

      if (this.page.acf.moments == '') {
        _class = 'hero-title';
      }
      return _class;
    },
  },

  watch: {
    $route(to) {
      let _this = this;
      //@todo: create a reset function because of routing problems
      _this.loaded = false;

      _this.methods.getData(to.params.pathMatch, (data) => {
        _this.page = data;
        _this.loaded = true;
      });
    },
    loaded(newVal) {
      if (newVal === true) {
        if (
          typeof this.page.acf.menu !== 'undefined' &&
          this.page.acf.menu !== ''
        ) {
          window.MenuView.mainMenu = this.page.acf.menu;
        }

        setTimeout(() => {
          let heroSlider = $('.hero-slider .owl-carousel');
          if (heroSlider.length) {
            heroSlider.owlCarousel({
              items: 1,
              nav: true,
              margin: 0,
              loop: true,
              dots: false,
              autoplay: false,
              callbacks: true,
              autoHeight: false,
              autoplayTimeout: 10000,
              autoplayHoverPause: true,
            });
          }

          let videoSlider = $('.video-slider .owl-carousel');
          if (videoSlider.length) {
            videoSlider
              .owlCarousel({
                items: 1,
                nav: true,
                loop: true,
                dots: false,
                video: true,
                autoplay: false,
                callbacks: true,
                videoWidth: 1920,
                autoHeight: false,
                videoHeight: 1080,
                autoplayTimeout: 10000,
                autoplayHoverPause: true,
                responsive: {
                  0: {
                    margin: 5,
                    stagePadding: 15,
                  },
                  769: {
                    margin: 0,
                    stagePadding: 0,
                  },
                },
              })
              .on('play.owl.video', function() {
                $(this)
                  .find('.title-container')
                  .hide();
              })
              .on('stop.owl.video', function() {
                $(this)
                  .find('.title-container')
                  .show();
              });
          }

          let contentSlider = $('.content-slider .owl-carousel');
          if (contentSlider.length) {
            contentSlider.owlCarousel({
              items: 1,
              nav: true,
              margin: 0,
              loop: true,
              dots: false,
              autoplay: false,
              callbacks: true,
              autoHeight: false,
              autoplayTimeout: 10000,
              autoplayHoverPause: true,
            });
          }
        }, 100);
        this.methods.loadInteractiveHelpers();
      }
    },
  },

  // Fetches posts when the component is created.
  created() {
    let _this = this;
    this.methods.getData(this.$route.params.pathMatch, (data) => {
      _this.page = data;
      _this.loaded = true;
    });
  },
};
</script>

<style lang="scss">
@import '../../../assets/sass/post';
@import '../../../assets/sass/home';
@import '../../../assets/sass/category';
.hero-title {
  position: absolute;
  top: 50%;
  transform: translate3d(-50%, -50%, 0);
  left: 50%;
  z-index: 10;
  display: block;
  font-size: 4rem;
}
</style>
