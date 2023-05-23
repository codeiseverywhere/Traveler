<template>
  <aside class="widget text-center recommended-posts">
    <strong
      class="text-uppercase widget-title"
      v-html="siteData.i18n.recommended"
    />
    <figure v-for="(post, key) in data" :key="key" class="recommended-post">
      <router-link :to="post.link">
        <img
          loading="lazy"
          :data-src="post.image.sizes.thumbnail.url"
          :alt="post.image.alt"
          :title="post.image.title"
          class="native-lazyload-js-fallback"
        />
      </router-link>
      <footer>
        <strong class="category-label" v-html="post.category" />
        <h3 class="post-title--alt font-italic recommended-post_title">
          <router-link :to="post.link" v-html="post.post_title" />
        </h3>
      </footer>
    </figure>
  </aside>
</template>

<script>
export default {
  name: 'm-recommended-for-you',
  props: ['data'],

  data() {
    return {
      siteData: window.siteData,
      origin: window.location.origin,
    };
  },

  created() {
    let _this = this;
    _this.data.forEach(function(post, index) {
      _this.data[index].link = post.link.replace(_this.origin, '');
    });
  },
};
</script>
