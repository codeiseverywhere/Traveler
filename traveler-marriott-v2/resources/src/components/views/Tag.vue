<template>
  <section class="tag-archive" v-if="loaded === true">
    <vue-headful
      :title="tag.yoast_meta.title"
      :description="tag.yoast_meta.metadesc"
      :url="tag.yoast_meta.canonical"
      :lang="tag.yoast_meta.locale"
      :ogLocale="tag.yoast_meta.locale"
    />
    <div class="grid-container">
      <header class="term-title">
        <h1 class="text-center">
          {{ siteData.i18n.articlesTagged }}&nbsp;&lsquo;{{ tag.name }}&rsquo;
        </h1>
      </header>
      <div class="category-posts category-list" v-if="posts.length">
        <m-category-posts-list
          v-for="child in posts"
          :key="child.ID"
          :content="child"
        />
      </div>
      <div :class="'text-center ' + moreBtn">
        <m-more-posts
          :posts="posts.length"
          :totalPosts="totalPosts"
          :single="false"
        />
      </div>
    </div>
    <m-ad-unit v-if="posts.length" :src="topAdvert" />
  </section>
</template>

<script>
const AdUnit = () =>
  import(/* webpackChunkName: "/chunk/widgets/AdUnit" */ '../widgets/AdUnit');
const MorePosts = () =>
  import(
    /* webpackChunkName: "/chunk/widgets/MorePosts" */ '../widgets/MorePosts/MorePosts'
  );
const CategoryPostsList = () =>
  import(
    /* webpackChunkName: "/chunk/widgets/CategoryPostsList" */ '../widgets/CategoryPostsList'
  );

export default {
  name: 'Tag',
  data() {
    return {
      page: 1,
      tag: [],
      posts: [],
      moreBtn: '',
      loaded: false,
      totalPosts: 0,
      siteData: window.siteData,
      topAdvert:
        window.advertising.adDesktop +
        Math.round(Math.random() * 10000000000000000),
      methods: {
        getData: (slug, callback) => {
          window.axios
            .get(window.siteData.rest_base_page + 'wp/v2/tags?slug=' + slug)
            .then((response) => {
              callback(response.data[0]);
            })
            .catch((e) => {
              console.log(e);
            });
        },
        getPosts: (termId, callback) => {
          window.axios
            .get(
              window.siteData.rest_base_page +
                'wp/v2/posts?per_page=15&tags=' +
                termId +
                '&page=' +
                this.page,
            )
            .then((response) => {
              this.page += 1;
              this.totalPosts = response.headers['x-wp-total'];

              if (this.totalPosts <= 15) {
                this.moreBtn = 'hide';
              }
              callback(response.data);
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
    'm-more-posts': MorePosts,
    'm-category-posts-list': CategoryPostsList,
  },

  methods: {
    loadMorePosts() {
      this.methods.getPosts(this.tag.id, (postsData) => {
        this.posts = this.posts.concat(postsData);

        window.EventBus.fire('loadedPosts');
      });
    },
  },

  watch: {
    $route(to, from) {
      let _this = this;
      //@todo: create a reset function because of routing problems
      _this.page = 1;
      _this.tag = {};
      _this.posts = {};
      _this.loaded = false;
      _this.totalPosts = 0;

      /**
       * @todo figure a way to know if the next link is post, page or tag
       */
      _this.methods.getData(to.params.pathMatch, (data) => {
        _this.tag = data;

        //Get all the tag posts
        _this.methods.getPosts(_this.tag.id, (postsData) => {
          _this.posts = postsData;
          _this.loaded = true;
        });
      });
    },
  },

  // Fetches posts when the component is created.
  created() {
    let _this = this;

    window.EventBus.listen('loadPosts', _this.loadMorePosts);

    _this.methods.getData(_this.$route.params.pathMatch, (data) => {
      _this.tag = data;

      //Get all the tag posts
      _this.methods.getPosts(_this.tag.id, (postsData) => {
        _this.posts = postsData;
        _this.loaded = true;
      });
    });
  },
};
</script>

<style lang="scss">
@import '../../../assets/sass/home';
@import '../../../assets/sass/category';
</style>
