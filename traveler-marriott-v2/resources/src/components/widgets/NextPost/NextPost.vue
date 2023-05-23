<template>
  <aside class="next-post" v-if="data">
    <div
      :class="
        'align-middle category-post category-post-0 long-delay slow ' + position
      "
    >
      <router-link
        :to="data.link"
        class="category-post_image aspect-ratio-16_9"
      >
        <img
          loading="lazy"
          :data-src="data.image.url"
          :alt="data.image.alt"
          :title="data.image.title"
          :width="data.image.width"
          :height="data.image.height"
          class="native-lazyload-js-fallback"
        />
      </router-link>
      <div class="category-post_content">
        <strong class="text-uppercase" v-html="siteData.i18n.next" />
        <h5>
          <router-link :to="data.link" v-html="data.title" />
        </h5>
        <router-link
          :to="data.link"
          class="button warning"
          v-html="siteData.i18n.readMore"
        />
      </div>
    </div>
  </aside>
</template>

<script>
export default {
  name: 'NextPost',
  props: ['data'],
  data() {
    return {
      post: {},
      position: '',
      loaded: false,
      observer: false,
      observerConfig: {
        root: document.querySelector('.post-content'),
        rootMargin: '100px',
        threshold: [0],
      },
      siteData: window.siteData,
      origin: window.location.origin,
    };
  },

  methods: {
    triggerBlock: function() {
      let _this = this;
      _this.position = 'fixed long-delay slow slide-out-right mui-leave';
    },
  },

  watch: {
    $route(to, from) {
      document.querySelector('.next-post').classList.remove('fixed');
    },
  },

  mounted() {
    let _this = this;

    _this.data.link = _this.data.link.replace(_this.origin, '');

    /**
     * Uses the Intersection Observer API to validate our position and lazy load content
     * https://developer.mozilla.org/en-US/docs/Web/API/Intersection_Observer_API
     * @type {IntersectionObserver}
     */
    if ('IntersectionObserver' in window) {
      _this.observer = new IntersectionObserver(function(entries) {
        entries.forEach(function(entry) {
          if (entry.intersectionRatio > 0.2) {
            _this.position = 'fixed slide-out-right mui-leave';
          } else {
            _this.position = 'fixed hide';
          }
        });
      }, _this.observerConfig);

      Array.from(document.querySelectorAll('.next-post')).forEach((element) => {
        _this.observer.observe(element);
      });
    }
  },
};
</script>
