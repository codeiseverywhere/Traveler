<template>
  <section class="page" v-if="loaded === true">
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
    <div class="grid-container">
      <div v-if="page.image.url !== null" class="hero-wrapper">
        <div class="aspect-ratio-16_9">
          <img
            loading="lazy"
            :src="page.image.url"
            :alt="page.image.alt"
            :title="page.image.title"
          />
        </div>
        <div class="title-container text-center">
          <h1 v-html="page.title.rendered" />
        </div>
      </div>
      <div class="post-content" v-html="page.content.rendered"></div>
    </div>
  </section>
</template>

<script>
export default {
  name: 'Page',
  data() {
    return {
      page: {},
      loaded: false,
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

  watch: {
    $route(to, from) {
      let _this = this;
      //@todo: create a reset function because of routing problems
      _this.loaded = false;

      _this.methods.getData(to.params.pathMatch, (data) => {
        _this.page = data;
        _this.loaded = true;
      });
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
</style>
