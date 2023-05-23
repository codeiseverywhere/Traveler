<template>
  <section
    class="storybooked-template-interactive-story"
    v-if="loaded === true"
  >
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
    <div v-html="post.content.rendered"></div>
    <m-social-share
      :title="post.yoast_meta.title"
      :url="post.yoast_meta.canonical"
    />
  </section>
</template>

<script>
const SocialShare = () =>
  import(
    /* webpackChunkName: "/chunk/widgets/SocialShare" */ '../../components/widgets/SocialShare/SocialShare'
  );

import '../../lib/tiled-gallery-carousel-without-jetpack-dependencies';
import magnificPopup from 'magnific-popup';
import ScrollMagic from 'scrollmagic';

window.ScrollMagic = ScrollMagic;
window.magnificPopup = magnificPopup;

export default {
  name: 'PostACF',
  props: ['slug', 'id', 'name', 'type'],
  data() {
    return {
      post: {},
      loaded: false,
      controller: false,
      siteData: window.siteData,
      topAdvert:
        window.advertising.adDesktop +
        Math.round(Math.random() * 10000000000000000),
      methods: {
        getData: (slug, callback) => {
          let type = this.type ? this.type : 'storybooked';

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
        interactiveHelpers: (
          interactive_slider,
          owl_options,
          interactive_panel,
          cur_panel,
          _this,
        ) => {
          interactive_slider
            .on('initialized.owl.carousel', function() {
              $('.main_title').fadeIn();
            })
            .owlCarousel(owl_options)
            .on('play.owl.video', function() {
              $(this)
                .parents('.interactive-media-wrapper')
                .find('.main_title')
                .hide();
            })
            .on('stop.owl.video', function() {
              $(this)
                .parents('.interactive-media-wrapper')
                .find('.main_title')
                .show();
            });

          if (interactive_panel.find('.map_title').length > 0) {
            let map_panel = interactive_panel;

            new ScrollMagic.Scene({
              triggerElement: cur_panel,
            })
              .setPin(cur_panel)
              .addTo(_this.controller)
              .on('start', function() {
                map_panel.find('.map_title').addClass('animated');
              });
          } else {
            new ScrollMagic.Scene({
              triggerElement: cur_panel,
            })
              .setPin(cur_panel)
              .addTo(_this.controller)
              .on('end', function() {
                //@todo: stop video when panel changes
              });
          }
        },
        loadInteractiveHelpers: () => {
          let _this;
          _this = this;

          setTimeout(() => {
            Array.from(
              document.querySelectorAll('.wp-audio-shortcode'),
            ).forEach((el) => {
              let player = new MediaElementPlayer(el, {
                success: (mediaElement, originalNode, instance) => {
                  // do things
                },
              });
            });

            $('.interactive-youtube a').magnificPopup({
              disableOn: 700,
              type: 'iframe',
              mainClass: 'mfp-fade',
              removalDelay: 160,
              preloader: false,
              fixedContentPos: false,
            });

            $('.image-popup').magnificPopup({
              type: 'image',
              closeOnContentClick: true,
              closeBtnInside: false,
              fixedContentPos: true,
              mainClass: 'mfp-no-margins mfp-with-zoom',
              image: {
                verticalFit: true,
              },
              zoom: {
                enabled: true,
                duration: 300,
              },
            });

            $('.gallery').magnificPopup({
              delegate: 'a',
              type: 'image',
              tLoading: 'Loading image #%curr%...',
              mainClass: 'mfp-img-mobile',
              gallery: {
                enabled: true,
                navigateByImgClick: true,
                preload: [0, 1], // Will preload 0 - before current, and 1 after the current image
              },
              image: {
                tError:
                  '<a href="%url%">The image #%curr%</a> could not be loaded.',
                titleSrc: function(item) {
                  return item.el
                    .parent()
                    .find('.caption')
                    .text();
                },
              },
            });

            // ScrollMagic init
            _this.controller = new ScrollMagic.Controller({
              globalSceneOptions: {
                triggerHook: 'onLeave',
              },
            });

            // get all block panels
            Array.from(document.querySelectorAll('div.panel')).forEach(
              (panel) => {
                // create scene for every slide

                let owl_options, interactive_slider, interactive_panel;

                interactive_panel = $(panel);
                interactive_slider = interactive_panel.find(
                  '.interactive-slider',
                );

                owl_options = {
                  autoHeight: true,
                  nav: false,
                  dots: false,
                  video: true,
                  videoWidth: 1920,
                  videoHeight: 1080,
                  callbacks: true,
                  items: 1,
                };

                if (2 <= interactive_slider.children().length) {
                  $.extend(owl_options, {
                    loop: true,
                    nav: true,
                    dots: true,
                  });
                }

                _this.methods.interactiveHelpers(
                  interactive_slider,
                  owl_options,
                  interactive_panel,
                  panel,
                  _this,
                );
              },
            );
          }, 800);
        },
      },
    };
  },

  components: {
    'm-social-share': SocialShare,
  },

  watch: {
    $route(to) {
      let _this = this;

      //We only trigger this if hash is empty because of the gallery plugin.
      if (to.hash === '') {
        //@todo: create a reset function because of routing problems
        _this.post = {};
        _this.loaded = false;
        _this.controller = _this.controller.destroy(true);

        _this.methods.getData(to.params.pathMatch, function(data) {
          _this.post = data;
          _this.loaded = true;
        });
      }
    },
    loaded(newVal) {
      if (newVal === true) {
        if (
          typeof this.post.acf.menu !== 'undefined' &&
          this.post.acf.menu !== ''
        ) {
          window.MenuView.mainMenu = this.post.acf.menu;
        }

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
@import '../../lib/tiled-gallery-carousel-without-jetpack-dependencies';
@import '../../../assets/sass/post';
@import '../../../assets/sass/interactive';
</style>
