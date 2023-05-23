<template>
  <lazy-component tag="aside" class="widget clearfix" @show="triggerBlock">
    <div class="related-posts" v-if="loaded === true && posts.length">
      <div class="text-center">
        <strong
          class="text-uppercase widget-title"
          v-html="siteData.i18n.relatedArticles"
        />
      </div>
      <div class="block-wrapper">
        <div class="grid-x grid-margin-x small-up-3 align-stretch">
          <div
            v-for="post in posts"
            :key="post.id"
            class="cell align-top related-post"
          >
            <a
              :href="post.permalink"
              class="related-post_image aspect-ratio-16_9"
            >
              <img
                loading="lazy"
                :data-src="post.image.sizes.medium_large.url"
                :alt="post.image.alt"
                :width="post.image.sizes.medium_large.width"
                :height="post.image.sizes.medium_large.height"
                class="native-lazyload-js-fallback"
              />
            </a>
            <div class="related-post_content">
              <strong class="category-label" v-html="post.category" />
              <h3 class="post-title">
                <a :href="post.permalink" v-html="post.post_title" />
              </h3>
            </div>
          </div>
        </div>
      </div>
    </div>
  </lazy-component>
</template>

<script>
export default {
  props: ['id'],

  data() {
    return {
      more: [],
      posts: [],
      errors: [],
      term: false,
      loaded: false,
      moreBox: false,
      siteData: window.siteData,
    };
  },

  methods: {
    triggerBlock() {
      let _this, per_page;
      _this = this;
      per_page = 3;

      _this.moreBox = document.querySelector('.more-box');

      if (_this.moreBox) {
        per_page += Number(_this.moreBox.getAttribute('data-number'));
      }

      window.axios
        .get(
          _this.siteData.rest_base_page +
            'wp/v2/related-posts?id=' +
            _this.id +
            '&per_page=' +
            per_page,
        )
        .then((response) => {
          _this.term = response.data.term;
          _this.posts = response.data.posts.slice(0, 3);
          _this.more = response.data.posts.slice(3);
          _this.loaded = true;
        })
        .catch((e) => {
          _this.errors.push(e);
        });
    },
  },

  watch: {
    $route(to, from) {
      let _this = this;
      //@todo: create a reset function because of routing problems
      _this.more = [];
      _this.posts = [];
    },
    more() {
      //@todo: test the isVisible mixin to call REST endpoint when the "more box" is visible
      let _this = this;
      if (0 < _this.more.length) {
        let moreTitle = document.createElement('strong');
        moreTitle.innerHTML = _this.siteData.i18n.moreIn + _this.term;
        _this.moreBox.appendChild(moreTitle);

        _this.more.forEach((el) => {
          let moreItem = document.createElement('a');
          moreItem.href = el.permalink;
          moreItem.innerHTML = el.post_title;
          _this.moreBox.appendChild(moreItem);
        });
      }
    },
  },
};
</script>
