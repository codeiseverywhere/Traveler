<template>
  <section class="podcast">
    <vue-headful
      v-if="loaded === true"
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
    <div class="grid-container" v-if="loaded === true">
      <header class="term-title">
        <h1 class="text-center" v-html="page.title.rendered" />
      </header>
      <div class="hero-wrapper">
        <div class="aspect-ratio-16_9">
          <img
            loading="lazy"
            :src="page.image.url"
            :alt="page.image.alt"
            :title="page.image.title"
          />
        </div>
      </div>
      <div class="podcasts-wrapper">
        <div v-if="page.content" v-html="page.content.rendered"></div>
      </div>
      <m-ad-unit :src="topAdvert" />

    </div>

     <!--
    <div class="grid-container">
      <div class="podcasts-wrapper">
       
          <m-podcasts :showAllPodcasts="true" :classPageName="'podcasts'" />
    

      </div>
    </div>
        -->
  </section>
</template>

<script>
const AdUnit = () =>
  import(/* webpackChunkName: "/chunk/widgets/AdUnit" */ '../widgets/AdUnit');
const Podcasts = () =>
  import(
    /* webpackChunkName: "/chunk/widgets/Podcasts" */ '../widgets/Podcasts'
  );

export default {
  name: 'Podcasts',
  data() {
    return {
      page: {},
      loaded: false,
      topAdvert: false,
      siteData: window.siteData,
      podcasts: window.podcasts,
      advertising: window.advertising,
      oldPodcastID: false,
      oldPodcastName: false,
      oldPausedTime: false,
      pausedTime: false,
      startTime: false,
      methods: {
        getData: (slug, callback) => {
          window.axios
            .get(window.siteData.rest_base_page + 'wp/v2/pages?slug=' + slug)
            .then((response) => {
              // JSON responses are automatically parsed.
              callback(response.data[0]);
            })
            .catch((e) => {
              console.log(e);
            });
        },
      },
    };
  },

  components: {
    'm-ad-unit': AdUnit,
    'm-podcasts': Podcasts,
  },

  watch: {
    $route(to) {
      let _this = this;
      _this.methods.getData(to.params.pathMatch, (data) => {
        _this.page = data;
        _this.loaded = true;
      });
    },
  },

  // Fetches posts when the component is created.
  created() {
    let _this = this;
    _this.methods.getData(_this.$route.params.pathMatch, (data) => {
      _this.page = data;
      _this.loaded = true;
    });
    _this.topAdvert =
      _this.advertising.adDesktop +
      Math.round(Math.random() * 10000000000000000);
  },
};
</script>

<style lang="scss">
@import '../../../assets/sass/podcasts';
</style>
