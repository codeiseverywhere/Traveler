<template>
  <button
    class="button text-uppercase bold MorePosts"
    v-html="siteData.i18n.loadMore"
  />
</template>

<script>
export default {
  name: 'MorePosts',
  props: ['posts', 'totalPosts', 'single'],
  data() {
    return {
      page: 1,
      main: false,
      timer: false,
      button: false,
      loadMoreSettings: {
        speed: 250,
        loading: false,
        proceed: false,
        window: window,
        finished: true,
        scroll_distance: -500,
      },
      siteData: window.siteData,
    };
  },
  methods: {
    isVisible() {
      /**
       * isVisible
       *
       * Check to see if element is visible before loading posts
       *
       * @since 1.0
       */
      let _this = this;

      // Check for a width and height to determine visibility
      _this.visible =
        _this.button.clientWidth > 0 && _this.button.clientHeight > 0;
      return _this.visible;
    },

    handleScroll() {
      /**
       * handleScroll
       *
       * Load posts as user scrolls the page
       *
       * @since 1.0
       */
      let _this = this;

      if (_this.timer) {
        //clearTimeout(_this.timer);
      }

      _this.timer = setTimeout(function() {
        if (_this.isVisible()) {
          let trigger = _this.button.getBoundingClientRect();
          let btnPos =
            Math.round(
              trigger.top -
                _this.button.offsetHeight +
                350 -
                _this.loadMoreSettings.window.innerHeight,
            ) + _this.loadMoreSettings.scroll_distance;
          let offsetButton =
            btnPos <= _this.loadMoreSettings.window.innerHeight &&
            Math.abs(btnPos) <= _this.loadMoreSettings.window.innerHeight;
          let scrollTrigger = btnPos <= 0 && offsetButton;

          let hasMaxedLimit = Number(_this.posts) >= Number(_this.totalPosts);

          if (hasMaxedLimit) {
            _this.button.parentElement.remove();
          }

          // Standard Scroll
          if (
            !_this.loadMoreSettings.loading &&
            _this.loadMoreSettings.finished &&
            scrollTrigger &&
            !hasMaxedLimit &&
            _this.loadMoreSettings.proceed
          ) {
            _this.loadMoreSettings.loading = true;
            _this.loadMoreSettings.finished = false;

            window.EventBus.fire('loadPosts');
          }
        }
      }, 50);
    },

    triggerDone() {
      /**
       * triggerDone
       *
       * Resets the loading and finished variables to enable a new set of posts.
       *
       * @since 1.0
       */
      let _this = this;

      _this.loadMoreSettings.loading = false;
      _this.loadMoreSettings.finished = true;
    },
  },

  created() {
    let _this = this;

    window.EventBus.listen('loadedPosts', _this.triggerDone);

    window.addEventListener('scroll', _this.handleScroll);
    window.addEventListener('touchstart', _this.handleScroll);
  },

  mounted() {
    let _this = this;
    _this.button = document.querySelector('.MorePosts');

    // Flag to prevent unnecessary loading of posts on initial page load.
    setTimeout(function() {
      _this.loadMoreSettings.proceed = true;
    }, _this.loadMoreSettings.speed);
  },
  destroyed() {
    let _this = this;

    window.removeEventListener('scroll', _this.handleScroll);
    window.removeEventListener('touchstart', _this.handleScroll);
  },
};
</script>

<style lang="scss">
@import 'MorePosts';
</style>
