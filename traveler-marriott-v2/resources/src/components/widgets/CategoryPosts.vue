<template>
  <div class="widget category-posts clearfix" v-if="loaded">
    <strong
      class="category-label category-label-2x text-uppercase category-posts-name"
    >
      <router-link :to="term.link" v-html="term.name" />
    </strong>
    <m-brought-by
      v-if="typeof term.custom_meta.sponsored !== 'undefined'"
      :data="term.custom_meta.sponsored"
      type="brought-by-banner_inline"
    />
    <div
      v-for="(post, index) in topPosts"
      :key="index"
      :class="'align-middle category-post category-post-' + index"
    >
      <router-link
        :to="post.permalink"
        class="category-post_image aspect-ratio-16_9"
      >
        <img
          loading="lazy"
          :data-src="post.image.sizes.medium.url"
          :alt="post.image.alt"
          :width="post.image.sizes.medium.width"
          :height="post.image.sizes.medium.height"
          class="native-lazyload-js-fallback"
        />
      </router-link>
      <div class="category-post_content">
        <strong class="category-label" v-html="post.category" />
        <h3 class="post-title">
          <router-link :to="post.permalink" v-html="post.post_title" />
        </h3>
        <small
          v-if="post.sponsored !== false"
          class="text-uppercase branded-post"
          >Sponsored</small
        >
      </div>
    </div>
    <div class="grid-x medium-up-2 align-stretch category-post--secondary">
      <div
        v-for="(post, index) in posts"
        :key="index"
        :class="'cell align-top category-post category-post-' + (index + 1)"
      >
        <div>
          <strong class="category-label" v-html="post.category" />
          <h3 class="post-title--alt">
            <router-link :to="post.permalink" v-html="post.post_title" />
          </h3>
          <small
            v-if="post.sponsored !== false"
            class="text-uppercase branded-post"
            >Sponsored</small
          >
        </div>
      </div>
      <div class="cell align-top">
        <strong class="category-label category-label-2x">
          <router-link :to="term.link"
            >{{ siteData.i18n.more }} {{ term.name }}&nbsp;&raquo;</router-link
          >
        </strong>
      </div>
    </div>
    <slot />
  </div>
</template>

<script>
const BroughtBy = () =>
  import(
    /* webpackChunkName: "/chunk/widgets/BroughtBy" */ '../widgets/BroughtBy/BroughtBy'
  );

export default {
  name: 'm-category-posts',
  props: ['termId', 'alt', 'filter'],

  data() {
    return {
      term: [],
      posts: [],
      errors: [],
      topPosts: [],
      loaded: false,
      siteData: window.siteData,
      origin: window.location.origin,
    };
  },

  components: {
    'm-brought-by': BroughtBy,
  },

  methods: {
    triggerLoaded() {
      let _this = this;
      window.axios
        .get(
          window.siteData.rest_base_page +
            'wp/v2/category-posts?term_id=' +
            _this.termId +
            _this.filter,
        )
        .then((response) => {
          response.data.posts.forEach(function(post, index) {
            response.data.posts[index].permalink = post.permalink.replace(
              _this.origin,
              '',
            );
          });
          response.data.term.link = response.data.term.link.replace(
            _this.origin,
            '',
          );

          _this.topPosts = response.data.posts.slice(0, 1);
          _this.posts = response.data.posts.slice(1);
          _this.term = response.data.term;
          _this.loaded = true;
        })
        .catch((e) => {
          _this.errors.push(e);
        });
    },
  },

  created() {
    this.triggerLoaded();
  },
};
</script>
