export default {
  data() {
    return {
      moreBtn: '',
      active: true,
      headData: {},
      loaded: false,
      totalPosts: 3,
      popstate: false,
      topAdvert: false,
      currentPost: false,
    };
  },
  methods: {
    getOffset(el = null) {
      /**
       *  getOffset
       *  Get the current top/left coordinates of an element relative to the document.
       *
       *  @since 5.0
       *  @param {*} el
       */

      if (!el) {
        return false;
      }
      let rect = el.getBoundingClientRect(),
        scrollLeft = window.pageXOffset || document.documentElement.scrollLeft,
        scrollTop = window.pageYOffset || document.documentElement.scrollTop;
      return { top: rect.top + scrollTop, left: rect.left + scrollLeft };
    },

    onScroll(selector) {
      /**
       * onScroll
       * Scroll and touchstart events
       *
       * @since 1.0
       */

      let _this, url, scrollTop;

      _this = this;
      scrollTop = window.pageYOffset;

      if (scrollTop > 1) {
        window.setTimeout(function() {
          // Get container scroll position
          let fromTop = scrollTop + 30;
          let elements = document.querySelectorAll(selector);

          // Loop all posts
          let current = Array.prototype.filter.call(elements, function(n, i) {
            if (typeof _this.getOffset === 'function') {
              let divOffset = _this.getOffset(n);
              if (divOffset.top < fromTop) {
                return n;
              }
            }
          });

          // Get the data attributes of the current element
          let currentElement = _this.posts[current.length - 1];

          // Set URL if current URL is different than the container.
          if (typeof currentElement !== 'undefined') {
            _this.setURL(currentElement);
          }
          window.EventBus.fire('isVisible', current);
        }, 15);
      }
    },
  },
};
