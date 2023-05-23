<template>
  <aside class="widget editors-picks text-center">
    <strong class="text-uppercase widget-title" v-html="siteData.i18n.picks" />
    <ol class="no-bullet">
      <li v-for="(post, key) in data" :key="key" class="editors-pick">
        <em>{{ key + 1 }}.</em>
        <strong class="category-label" v-html="post.category" />
        <h3 class="post-title--alt">
          <router-link :to="post.link" v-html="post.post_title" />
        </h3>
      </li>
    </ol>
  </aside>
</template>

<script>
export default {
  name: 'm-editors-picks',
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
