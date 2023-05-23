<template>
  <lazy-component
    @show="triggerBlock"
    class="grid-x grid-padding-x large-up-3 places-posts"
  >
    <template v-if="topPosts.length">
      <div class="cell" v-for="(post, index) in topPosts" :key="index">
        <strong
          class="text-uppercase category-label category-label-2x"
          v-html="post.city"
        />
        <router-link :to="post.permalink" class="aspect-ratio-16_9">
          <img
            loading="lazy"
            :data-src="post.image.sizes.medium_large.url"
            :alt="post.image.alt"
            :width="post.image.sizes.medium_large.width"
            :height="post.image.sizes.medium_large.height"
            class="native-lazyload-js-fallback"
          />
        </router-link>
        <strong class="category-label" v-html="post.category" />
        <h3 class="post-title">
          <router-link :to="post.permalink" v-html="post.post_title" />
        </h3>
        <small
          v-if="post.sponsored === true"
          class="text-uppercase branded-post"
          >Sponsored</small
        >
      </div>
    </template>
    <div class="cell places-posts--secondary" v-if="posts.length">
      <strong
        class="text-uppercase category-label category-label-2x category-label-alt"
      >
        {{ siteData.i18n.morePlaces }}
      </strong>
      <div v-for="(post, index) in posts" class="places-post" :key="index">
        <strong class="category-label" v-html="post.category" />
        <h3 class="post-title--alt">
          <router-link :to="post.permalink" v-html="post.post_title" />
        </h3>
        <small
          v-if="post.sponsored === true"
          class="text-uppercase branded-post"
          >Sponsored</small
        >
      </div>
    </div>
  </lazy-component>
</template>

<script>
export default {
  name: 'm-places',
  props: ['placeA', 'placeB', 'placeC'],
  data() {
    return {
      posts: [],
      topPosts: [],
      siteData: window.siteData,
      origin: window.location.origin,
      methods: {
        getData: (slug, per_page, callback) => {
          let _this = this;

          window.axios
            .get(
              window.siteData.rest_base_page +
                'wp/v2/category-posts?term_id=' +
                slug +
                '&per_page=' +
                per_page,
            )
            .then((response) => {
              response.data.posts.forEach(function(post, index) {
                response.data.posts[index].permalink = post.permalink.replace(
                  _this.origin,
                  '',
                );
              });

              callback(response.data.posts);
            })
            .catch((e) => {
              console.log(e);
            });
        },
      },
    };
  },

  methods: {
    triggerBlock: function() {
      let _this = this;

      _this.methods.getData(_this.placeA, 1, (response) => {
        _this.topPosts.push(response[0]);
      });

      _this.methods.getData(_this.placeB, 1, (response) => {
        _this.topPosts.push(response[0]);
      });

      _this.methods.getData(_this.placeC, 4, (response) => {
        _this.posts = response;
      });
    },
  },
};
</script>
