<template>
  <section class="page-alt" v-if="loaded === true">
    <div class="hero-wrapper top-hero">
      <div class="aspect-ratio-21_9">
        <video
          autoplay
          playsinline
          webkit-playsinline
          loop
          muted
          defaultMuted
          preload="auto"
          :poster="page.image.url"
          src="https://rvca738f6h5tbwmj3mxylox3-wpengine.netdna-ssl.com/wp-content/uploads/2019/10/TOGT-Hero-Video-Small_new.mp4"
          width="768"
          height="324"
        ></video>
      </div>
    </div>
    <div class="grid-container">
      <div v-html="page.content.rendered"></div>
    </div>
  </section>
</template>

<script>
export default {
  name: 'PageAltPreview',
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
                slug +
                '/revisions/',
            )
            .then((response) => {
              callback(response.data[0]);
            })
            .catch((e) => {
              console.log(e);
            });
        },
      },
    };
  },

  // Fetches posts when the component is created.
  created() {
    let _this, preview_id;
    _this = this;
    preview_id = _this.$route.query.page_id
      ? _this.$route.query.page_id
      : _this.$route.query.preview_id;

    _this.methods.getData(preview_id, function(data) {
      _this.page = data;
      _this.loaded = true;

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
    });
  },
};
</script>

<style lang="scss">
@import '../../../assets/sass/post';
</style>
