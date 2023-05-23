<template>
  <section class="category-child" v-if="loaded === true">
    <vue-headful
      :title="category.yoast_meta.title"
      :description="category.yoast_meta.metadesc"
      :url="category.yoast_meta.canonical"
      :lang="category.yoast_meta.locale"
      :ogLocale="category.yoast_meta.locale"
    />
    <div class="grid-container">
      <header class="term-title text-center">
        <h1>{{ name }}</h1>
        <m-brought-by
          v-if="typeof category.custom_meta.sponsored !== 'undefined'"
          :data="category.custom_meta.sponsored"
          type="brought-by-banner"
        />
      </header>
      <m-owl v-if="slider.length" :slides="slider" />
      <m-ad-unit v-if="slider.length" :src="!isMobile ? topAdvertDesktop : topAdvertMobile" />
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
    <m-ad-unit v-if="slider.length" :src="!isMobile ? topAdvertDesktop : topAdvertMobile" />
  </section>
</template>

<script>

const Owl = () =>
  import(/* webpackChunkName: "/chunk/widgets/Owl" */ '../widgets/Owl');
const AdUnit = () =>
  import(/* webpackChunkName: "/chunk/widgets/AdUnit" */ '../widgets/AdUnit');
const BroughtBy = () =>
  import(
    /* webpackChunkName: "/chunk/widgets/BroughtBy" */ '../widgets/BroughtBy/BroughtBy'
  );
const MorePosts = () =>
  import(
    /* webpackChunkName: "/chunk/widgets/MorePosts" */ '../widgets/MorePosts/MorePosts'
  );
const CategoryPostsList = () =>
  import(
    /* webpackChunkName: "/chunk/widgets/CategoryPostsList" */ '../widgets/CategoryPostsList'
  );

import { isMobile } from '../../mixins/utils';

export default {
  name: 'CategoryChild',
  props: ['slug', 'id', 'name', 'isChild'],
  data() {
    return {
      page: 1,
      slider: {},
      posts: {},
      moreBtn: '',
      category: {},
      loaded: false,
      totalPosts: 0,
      siteData: window.siteData,
      topAdvertDesktop:
        window.advertising.adDesktop +
        Math.round(Math.random() * 10000000000000000),
      topAdvertMobile:
        window.advertising.adMobile +
        Math.round(Math.random() * 10000000000000000),
      isMobile: isMobile(),
      methods: {
        getData: (slug, callback) => {
          window.axios
            .get(
              window.siteData.rest_base_page + 'wp/v2/categories?slug=' + slug,
            )
            .then((response) => {
              callback(response.data[0]);
              //console.log(response.data[0]);
            })
            .catch((e) => {
              console.log(e);
            });
        },
        getPosts: (termId, callback) => {
          window.axios
            .get(
              window.siteData.rest_base_page +
                'wp/v2/posts?per_page=15&categories=' +
                termId +
                '&page=' +
                this.page,
            )
            .then((response) => {
              this.page += 1;
              this.totalPosts = response.headers['x-wp-total'] - 3;

              if (this.totalPosts <= 12) {
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
    'm-owl': Owl,
    'm-ad-unit': AdUnit,
    'm-brought-by': BroughtBy,
    'm-more-posts': MorePosts,
    'm-category-posts-list': CategoryPostsList,
  },

  methods: {
    loadMorePosts() {
      this.methods.getPosts(this.id, (postsData) => {
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
      _this.slider = {};
      _this.posts = {};
      _this.category = {};
      _this.loaded = false;
      _this.totalPosts = 0;

      /**
       * @todo figure a way to know if the next link is post, page or category
       */
      _this.methods.getData(to.params.pathMatch, (data) => {
        _this.category = data;
        _this.loaded = true;
      });
    },
    loaded(newVal, oldVal) {
      let _this = this;
      if (newVal === true) {
        if (_this.category.custom_meta.adDesktop) {
          _this.topAdvertDesktop =
            _this.category.custom_meta.adDesktop +
            Math.round(Math.random() * 10000000000000000);
        }
        if (_this.category.custom_meta.adMobile) {
          _this.topAdvertMobile =
            _this.category.custom_meta.adMobile +
            Math.round(Math.random() * 10000000000000000);
        }

        /*
        if (
          Foundation.MediaQuery.is('small only') &&
          _this.category.custom_meta.adMobile
        ) {
          _this.topAdvert =
            _this.category.custom_meta.adMobile +
            Math.round(Math.random() * 10000000000000000);
        }
        */
      }
    },
  },

  // Fetches posts when the component is created.
  created() {
    let _this = this;

    window.EventBus.listen('loadPosts', _this.loadMorePosts);

    _this.methods.getData(_this.$route.params.pathMatch, (data) => {
      _this.category = data;
      _this.loaded = true;
    });

    //Get all the category posts
    _this.methods.getPosts(_this.id, (postsData) => {
      _this.slider = postsData.slice(0, 3);
      _this.posts = postsData.slice(3);
    });
  },
};
</script>

<style lang="scss">
@import '../../../assets/sass/home';
@import '../../../assets/sass/category';
</style>
