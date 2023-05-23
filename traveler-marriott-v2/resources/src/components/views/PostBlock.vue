<template>
  <section class="taste-of" v-if="loaded === true">
    <vue-headful
      :title="post.yoast_meta.title"
      :description="post.yoast_meta.metadesc"
      :url="post.yoast_meta.canonical"
      :lang="post.yoast_meta.locale"
      :ogLocale="post.yoast_meta.locale"
      :image="post.image.url"
      :image-width="post.image.width"
      :image-height="post.image.height"
      :image-alt="post.image.alt"
    />
    <div v-if="post.image.url !== null" class="hero-wrapper top-hero">
      <div class="aspect-ratio-16_9">
        <div
          class="hero-bg"
          :style="{
            'background-image': 'url(' + post.image.url + ')',
          }"
        ></div>
      </div>
      <div class="title-container text-center">
        <div
          class="caption"
          v-html="post.image.caption"
          v-if="post.image.caption !== ''"
        ></div>
        <strong class="category-label" v-html="post.category" />
        <h1 v-html="post.title.rendered" />
        <em class="byline">
          {{ siteData.i18n.byline }}&nbsp;
          <span v-html="post.author_data" />
        </em>
      </div>
    </div>
    <div class="grid-container wp-embed-responsive">
      <div class="post-content" v-html="post.content.rendered"></div>
    </div>
  </section>
</template>

<script>
import magnificPopup from 'magnific-popup';
import Parallax from '../../mixins/parallax';

window.magnificPopup = magnificPopup;
export default {
  name: 'PostBlock',
  props: ['slug', 'id', 'name', 'type'],
  data() {
    return {
      post: {},
      loaded: false,
      siteData: window.siteData,
      methods: {
        getData: (slug, callback) => {
          let type = this.type ? this.type : 'taste-of';

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

  watch: {
    $route(to) {
      let _this = this;
      //@todo: create a reset function because of routing problems
      _this.loaded = false;

      _this.methods.getData(to.params.pathMatch, (data) => {
        _this.post = data;
        _this.loaded = true;
      });
    },
    loaded(newVal) {
      if (newVal === true) {
        if (
          typeof this.post.acf.menu !== 'undefined' &&
          this.post.acf.menu !== ''
        ) {
          window.MenuView.mainMenu = this.post.acf.menu;
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
    _this.methods.getData(_this.$route.params.pathMatch, (data) => {
      _this.post = data;
      _this.loaded = true;
    });
  },
};
</script>

<style lang="scss">
@import '../../../assets/sass/post';
</style>
