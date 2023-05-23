<template>
  <section class="taste-of" v-if="loaded === true">
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
export default {
  name: 'TasteOfPreview',
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
                '/' +
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
    preview_id = _this.$route.query.p
      ? _this.$route.query.p
      : _this.$route.query.preview_id;

    _this.methods.getData(preview_id, (data) => {
      _this.post = data;
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
