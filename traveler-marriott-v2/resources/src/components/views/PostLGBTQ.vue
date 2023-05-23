<template>
  <section class="single" v-if="loaded === true">
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

    <m-lgbtq :blocks="post.lgbtq_blocks"></m-lgbtq>

    <m-social-share
      :title="post.yoast_meta.title"
      :url="post.yoast_meta.canonical"
    />
  </section>
</template>

<script>
import LGBTQ_scripts from '../../mixins/lgbtq';
const Lgbtq = () =>
  import(/* webpackChunkName: "/chunk/partials/lgbtq" */ '../partials/lgbtq');

const SocialShare = () =>
  import(
    /* webpackChunkName: "/chunk/widgets/SocialShare" */ '../../components/widgets/SocialShare/SocialShare'
  );

export default {
  name: 'PostLGBTQ',
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
          window.axios
            .get(window.siteData.rest_base_page + 'wp/v2/posts?slug=' + slug)
            .then((response) => {
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
  mixins: [LGBTQ_scripts],
  components: {
    'm-social-share': SocialShare,
    'm-lgbtq': Lgbtq,
  },
  watch: {
    $route(to, from) {
      let _this = this;
      //@todo: create a reset function because of routing problems
      _this.loaded = false;

      _this.methods.getData(to.params.pathMatch, (data) => {
        _this.post = data;
        _this.loaded = true;
      });
    },
  },

  // Fetch posts when the component is created.
  created() {
    let _this = this;
    this.methods.getData(this.$route.params.pathMatch, (data) => {
      _this.post = data;
      _this.loaded = true;
    });
  },
};
</script>

<style lang="scss">
@import '../../../assets/sass/post';
</style>
