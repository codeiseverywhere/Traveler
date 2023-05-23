<template>
  <section class="category-city" v-if="loaded === true">
    <vue-headful
      :title="name + ' Archives | ' + siteData.site_name"
      :description="category.yoast_meta.description || ''"
      :url="siteData.current_site + slug"
      :lang="category.yoast_meta.locale || 'en_US'"
      :head="{
        'meta[property=\'og:type\']': {
          content: 'website',
        },
      }"
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
      <m-ad-unit v-if="slider.length" :src="topAdvert" />
      <div class="grid-x grid-padding-x split-content">
        <div class="cell large-9 category-list--alt" v-if="sidePosts.length">
          <m-category-posts-list
            v-for="child in sidePosts"
            :key="child.ID"
            :content="child"
          />
        </div>
        <div class="cell large-3 sidebar">
          <template v-for="(widget, index) in category.acf.sidebar">
            <component
              v-if="widget.acf_fc_layout === 'm-ad-unit'"
              :key="index"
              v-bind:is="widget.acf_fc_layout"
              :src="widget.ad_id"
            />
            <aside
              v-else-if="widget.acf_fc_layout === 'm-where-to-stay'"
              :key="index"
              class="widget text-center"
            >
              <strong class="text-uppercase widget-title">
                {{ siteData.i18n.whereToStay }}&nbsp;{{ name }}
              </strong>
              <div v-html="widget.where_to_stay" class="where-to-list"></div>
            </aside>
          </template>
        </div>
      </div>
      <div class="category-posts category-list" v-if="posts.length">
        <strong class="category-label category-label-2x text-uppercase">
          {{ siteData.i18n.moreStories }}{{ name }}{{ siteData.i18n.stories }}
        </strong>
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

export default {
  name: 'CategoryCityChild',
  props: ['slug', 'id', 'name', 'isChild'],
  data() {
    return {
      page: 1,
      slider: {},
      posts: {},
      moreBtn: '',
      category: {},
      sidePosts: {},
      loaded: false,
      totalPosts: 0,
      ids: [],
      siteData: window.siteData,
      topAdvert:
        window.advertising.adDesktop +
        Math.round(Math.random() * 10000000000000000),
      sideAdvert:
        window.advertising.adMobile +
        Math.round(Math.random() * 10000000000000000),
      methods: {
        getData: (slug, callback) => {
          window.axios
            .get(
              window.siteData.rest_base_page +
                'wp/v2/categories?slug=' +
                slug.split('/'),
            )
            .then((response) => {
              this.ids = response.data.map((category) => category.id);
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
                'wp/v2/category-posts?per_page=15&tax_query[0]=' +
                termId[0] +
                '&tax_query[1]=' +
                termId[1] +
                '&page=' +
                this.page,
            )
            .then((response) => {
              this.page += 1;
              this.totalPosts = response.headers['x-wp-total'] - 7;

              if (this.totalPosts <= 8) {
                this.moreBtn = 'hide';
              }
              callback(response.data.posts);
            })
            .catch((e) => {
              console.log(e);
            });
        },
        replaceKey: (arr, defaultAd) => {
          if (false !== arr) {
            arr.forEach((el, index) => {
              if (el.acf_fc_layout.slice(0, 2) !== 'm-') {
                el.acf_fc_layout = 'm_' + el.acf_fc_layout;
              }
              el.acf_fc_layout = el.acf_fc_layout.replace(/_/gi, '-');
              if (el.acf_fc_layout === 'm-ad-unit') {
                if (!isNaN(el.ad_id) && '' !== el.ad_id) {
                  el.ad_id =
                    window.siteData.current_site +
                    'ads/single/' +
                    el.ad_id +
                    '?cb=' +
                    Math.round(Math.random() * 10000000000000000);
                } else {
                  el.ad_id = defaultAd;
                }
              }
              arr[index] = el;
            });
            return arr;
          }
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
      this.methods.getPosts(this.ids, (postsData) => {
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
      _this.moreBtn = '';
      _this.category = {};
      _this.sidePosts = {};
      _this.totalPosts = 0;
      _this.loaded = false;

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
        //Sidebar components
        if (_this.category.acf.length === 0) {
          _this.category.acf = {
            sidebar: [],
          };
          _this.category.acf.sidebar.push({
            acf_fc_layout: 'ad-unit',
            ad_id: '',
          });
        }

        if (_this.category.custom_meta.adDesktop) {
          _this.topAdvert =
            _this.category.custom_meta.adDesktop +
            Math.round(Math.random() * 10000000000000000);
        }

        if (
          Foundation.MediaQuery.is('small only') &&
          _this.category.custom_meta.adMobile
        ) {
          _this.topAdvert =
            _this.category.custom_meta.adMobile +
            Math.round(Math.random() * 10000000000000000);
        }

        if (_this.category.custom_meta.adMobile) {
          _this.sideAdvert =
            _this.category.custom_meta.adMobile +
            Math.round(Math.random() * 10000000000000000);
        }

        _this.methods.replaceKey(_this.category.acf.sidebar, _this.sideAdvert);

        //Get all the category posts
        _this.methods.getPosts(_this.ids, (postsData) => {
          _this.slider = postsData.slice(0, 3);
          _this.sidePosts = postsData.slice(3, 7);
          _this.posts = postsData.slice(7);
        });
      }
    },
  },

  created() {
    let _this = this;

    window.EventBus.listen('loadPosts', _this.loadMorePosts);

    _this.methods.getData(_this.$route.params.pathMatch, (data) => {
      _this.category = data;
      _this.loaded = true;
    });
  },
};
</script>

<style lang="scss">
@import '../../../assets/sass/home';
@import '../../../assets/sass/category';
</style>
