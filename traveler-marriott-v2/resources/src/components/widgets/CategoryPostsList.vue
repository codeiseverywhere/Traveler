<template>
  <div class="align-middle category-post category-post-0">
    <router-link
      :to="content.link"
      class="category-post_image aspect-ratio-16_9"
    >
      <img
        loading="lazy"
        :data-src="content.image.sizes.medium.url"
        :alt="content.image.alt"
        :title="content.image.title"
        class="native-lazyload-js-fallback"
      />
    </router-link>
    <div class="category-post_content">
      <strong class="category-label" v-html="content.category" />
      <h3 class="post-title">
        <router-link :to="content.link" v-html="content.post_title" />
      </h3>
      <div class="post-excerpt" v-html="content.excerpt.rendered"></div>
      <small
        v-if="content.sponsored === true"
        class="text-uppercase branded-post"
        >Sponsored</small
      >
    </div>
  </div>
</template>

<script>
export default {
  name: 'CategoryPostsList',
  props: ['content'],
  data() {
    return {
      origin: window.location.origin,
    };
  },

  watch: {
    $route(to, from) {
      let _this = this;
      _this.content.link = _this.content.link.replace(_this.origin, '');
      if (typeof _this.content.title !== 'undefined') {
        _this.content.post_title = _this.content.title.rendered;
      }
    },
  },

  created() {
    let _this = this;
    _this.content.link = _this.content.link.replace(_this.origin, '');
    if (typeof _this.content.title !== 'undefined') {
      _this.content.post_title = _this.content.title.rendered;
    }
  },
};
</script>
