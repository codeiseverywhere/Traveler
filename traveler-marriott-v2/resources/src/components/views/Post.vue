<template>
  <section class="single" v-if="loaded === true">
    <vue-headful
      :title="headData.title"
      :description="headData.description"
      :url="headData.url"
      :lang="headData.lang"
      :ogLocale="headData.lang"
      :image="headData.image.src"
      :image-width="headData.image.width"
      :image-height="headData.image.height"
      :image-alt="headData.image.alt"
    />
    <div
      class="grid-container wp-embed-responsive single-post"
      v-for="(post, key) in posts"
      :key="key"
      :id="'post-' + post.id"
    >
      <div class="hero-wrapper">
        <div class="aspect-ratio-16_9">
          <img
            loading="lazy"
            :src="post.image.url"
            :alt="post.image.alt"
            :title="post.title.rendered"
          />
        </div>
        <div class="title-container text-center">
          <div class="caption" v-html="post.image.caption"></div>
          <strong class="category-label" v-html="post.category" />
          <h1 v-html="post.title.rendered" />
          <em class="byline">
            {{ siteData.i18n.byline }}&nbsp;
            <span v-html="post.author_data" />
          </em>
          <m-brought-by
            v-if="post.sponsored !== false"
            :data="post.sponsored"
            type="marriott-featured-logo"
          />
        </div>
      </div>

      <div
        class="post-content"
        v-if="loaded === true"
        v-html="post.content.rendered"
      ></div>
      <div class="tags" v-if="loaded === true && post.tags_data !== false">
        <strong v-html="siteData.i18n.tags" />&nbsp;
        <span v-html="post.tags_data" />
      </div>
      <aside class="highlight-block">
        <m-properties
          :properties="getProperties(post)"
          :title="post.title.rendered"
        />
        <m-booking type="desktop"></m-booking>
        <m-moments :title="post.title.rendered" />
      </aside>
      <m-ad-unit :src="topAdvert" />
      <m-related-posts v-if="loaded === true" :id="post.id" />
    </div>
    <div :class="'text-center ' + moreBtn">
      <m-more-posts
        :posts="posts.length"
        :totalPosts="totalPosts"
        :single="true"
      />
    </div>
    <m-social-share :title="headData.title" :url="headData.canonical" />
  </section>
</template>

<script>
import '../../lib/tiled-gallery-carousel-without-jetpack-dependencies';
import { createTag } from '../../mixins/utils';
import Parallax from '../../mixins/parallax';

const Properties = () =>
  import(
    /* webpackChunkName: "/chunk/widgets/Properties" */ '../widgets/Properties/Properties'
  );
const Moments = () =>
  import(
    /* webpackChunkName: "/chunk/widgets/Moments" */ '../widgets/Moments/Moments'
  );
const AdUnit = () =>
  import(/* webpackChunkName: "/chunk/widgets/AdUnit" */ '../widgets/AdUnit');
const RelatedPosts = () =>
  import(
    /* webpackChunkName: "/chunk/widgets/RelatedPosts" */ '../widgets/RelatedPosts'
  );
const BroughtBy = () =>
  import(
    /* webpackChunkName: "/chunk/widgets/BroughtBy" */ '../widgets/BroughtBy/BroughtBy'
  );
const MorePosts = () =>
  import(
    /* webpackChunkName: "/chunk/widgets/MorePosts" */ '../widgets/MorePosts/MorePosts'
  );
const SocialShare = () =>
  import(
    /* webpackChunkName: "/chunk/widgets/SocialShare" */ '../widgets/SocialShare/SocialShare'
  );
const Booking = () =>
  import(
    /* webpackChunkName: "/chunk/widgets/Booking" */ '../widgets/Booking/Booking'
  );
export default {
  name: 'Post',
  data() {
    return {
      windowWidth: window.innerWidth,
      owl: null,
      posts: [],
      timer: null,
      moreBtn: '',
      active: true,
      headData: {},
      loaded: false,
      totalPosts: 3,
      popstate: false,
      topAdvert: false,
      currentPost: false,
      owl_options: {
        items: 1,
        margin: 0,
        nav: true,
        loop: true,
        dots: false,
        autoplay: false,
        autoHeight: false,
      },
      siteData: window.siteData,
      advertising: window.advertising,
      methods: {
        getData: (slug, callback) => {
          let _this = this;

          window.axios
            .get(
              window.siteData.rest_base_page +
                'wp/v2/posts?per_page=1&slug=' +
                slug,
            )
            .then((response) => {
              /*
               * @todo: find a better way to navigate the content and remove the first h5
               * the first h5 tag was used by post authors to add the main image caption.
               */
              let response_content;
              response_content = $(response.data[0].content.rendered);

              if ($(response_content[0]).is('h5')) {
                if (response.data[0].image.caption === '') {
                  response.data[0].image.caption =
                    response_content[0].innerHTML;
                }
                //response.data[0].content.rendered = $(response.data[0].content.rendered).html().replace(response_content[0].outerHTML, '')
              }

              callback(response.data[0]);
            })
            .catch((e) => {
              console.log(e);
            });
        },

        handleData: (data) => {
          let _this = this;
          _this.currentPost = data;
          _this.methods.setHeadData(data);
          _this.posts = _this.posts.concat(data);
          _this.loaded = true;
        },

        setHeadData: (post) => {
          let _this = this;

          if (post.yoast_meta) {
            _this.headData = {
              title: post.yoast_meta.title,
              description: post.yoast_meta.metadesc,
              url: post.yoast_meta.canonical,
              lang: post.yoast_meta.locale,
              image: {
                src: post.image.url,
                width: post.image.width,
                height: post.image.height,
                alt: post.image.alt,
              },
            };
          }
        },

        pushToGTM: (path) => {
          if ({}.hasOwnProperty.call(window, 'GTMdataLayer')) {
            window.GTMdataLayer.push({
              event: 'trackPageview',
              url: path,
            });
          }
        },

        track_event: function(_this) {
          if (typeof dataLayer === 'undefined') {
            const dataLayer = {};
          }
          if (!{}.hasOwnProperty.call(dataLayer, 'article')) {
            dataLayer.article = {};
          }
          dataLayer.article.categories = _this.currentPost.categories_names.join(
            ' | ',
          );

          if ({}.hasOwnProperty.call(window, 'GTMdataLayer')) {
            $('.where-to a, .post-content p a, .related-articles a').prop({
              target: '_blank',
              rel: 'noreferrer noopener',
            });
            $(document)
              .on('click', '.post-content p a', function() {
                var url = $(this).prop('href');
                window.GTMdataLayer.push({
                  event: 'trackEvent',
                  eventCategory: 'YellowLinks',
                  eventAction: 'click',
                  eventLabel: url,
                });
              })
              .on('click', '.magazines-toggler', function() {
                var url = $(this).data('toggle');
                window.GTMdataLayer.push({
                  event: 'trackEvent',
                  eventCategory: 'OtherMagazines',
                  eventAction: 'open',
                  eventLabel: url,
                });
              })
              .on('click', '.magazines-dropdown a', function() {
                var url = $(this).prop('href');
                window.GTMdataLayer.push({
                  event: 'trackEvent',
                  eventCategory: 'OtherMagazines',
                  eventAction: 'click',
                  eventLabel: url,
                });
              })
              .on('click', '.related-posts a', function() {
                var url = $(this).prop('href');
                window.GTMdataLayer.push({
                  event: 'trackEvent',
                  eventCategory: 'Related Articles',
                  eventAction: 'click',
                  eventLabel: url,
                });
              });
          }
        },

        doFlexVideo: () => {
          Array.from(
            document.querySelectorAll(
              'iframe[src*="youtube.com"], iframe[src*="youtu.be"], iframe[src*="vimeo.com"]',
            ),
          ).forEach((iframe) => {
            if (false === iframe.parentNode.classList.contains('flex-video')) {
              let wrapper = document.createElement('div');
              iframe.parentNode.insertBefore(wrapper, null);
              wrapper.classList.add('flex-video');

              if (iframe.width / iframe.height > 1.5) {
                wrapper.classList.add('widescreen');
              }

              iframe.parentNode.removeChild(iframe);
              wrapper.appendChild(iframe);
            }
          });
        },

        triggerMediaElement: () => {
          Array.from(document.querySelectorAll('.wp-audio-playlist')).forEach(
            (el) => {
              new MediaElementPlayer(el, {
                success: (mediaElement, originalNode, instance) => {
                  // do things
                },
              });
            },
          );
        },
      },
      mounted() {
        window.onresize = () => {
          this.windowWidth = window.innerWidth;
        };
      },
    };
  },

  mixins: [Parallax],

  components: {
    'm-moments': Moments,
    'm-properties': Properties,
    'm-ad-unit': AdUnit,
    'm-brought-by': BroughtBy,
    'm-more-posts': MorePosts,
    'm-social-share': SocialShare,
    'm-related-posts': RelatedPosts,
    'm-booking': Booking,
  },

  methods: {
    getProperties(post) {
      let properties = '';

      if (typeof post.acf.where_to_stay_links !== 'undefined') {
        properties = post.acf.where_to_stay_links;
      }

      return properties;
    },

    loadMorePosts() {
      let _this = this;

      if (_this.posts.length) {
        _this.methods.getData(
          _this.posts[_this.posts.length - 1].previous.name,
          function(data) {
            _this.currentPost = data;
            _this.posts = _this.posts.concat(data);

            window.EventBus.fire('loadedPosts');
            window.EventBus.fire('isPostLoaded');
          },
        );
      }
    },

    postLoaded() {
      let _this, target;
      _this = this;

      //Extract Ad ID from post and overwrite default ad.
      if (
        typeof _this.currentPost.acf.top_ad_unit !== 'undefined' &&
        _this.currentPost.acf.top_ad_unit !== ''
      ) {
        _this.topAdvert =
          window.siteData.current_site +
          'ads/group/' +
          _this.currentPost.acf.top_ad_unit +
          '?cb=';
      } else {
        _this.topAdvert = _this.advertising.adDesktop;
      }
      _this.topAdvert += Math.round(Math.random() * 10000000000000000);

      window.setTimeout(() => {
        target = $('#post-' + _this.currentPost.id);
        _this.methods.doFlexVideo();
        _this.methods.track_event(_this);

        let postSlider = $('.huge-it-carousel .owl-carousel');
        if (postSlider.length) {
          _this.owl = postSlider.owlCarousel(_this.owl_options);
        }

        if (this.$el.querySelectorAll('.quizz-container').length) {
          if (typeof quizzrScript === 'undefined') {
            const quizzrScript = createTag(
              'https://dcc4iyjchzom0.cloudfront.net/widget/loader.js',
              'quizzr-jssdk',
            );
          }
        }

        if (this.$el.querySelectorAll('.instagram-media').length) {
          if (typeof instaScript === 'undefined') {
            const instaScript = createTag(
              'https://www.instagram.com/embed.js',
              'instagram-jssdk',
            );
          }
        }
      }, 300);
    },

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

    setURL(post) {
      /**
       * setURL
       * Set the browser URL to current permalink
       *
       * @id string
       * @permalink string
       * @title string
       * @page string
       * @return null
       * @since 1.0
       */

      let _this = this;

      let state = {
        postID: post.id,
        permalink: post.link,
        title: post.title.rendered,
      };

      // If pushState is enabled
      if (typeof window.history.pushState === 'function') {
        history.replaceState(state, state.title, state.permalink);

        _this.methods.setHeadData(post);

        if (typeof window.addthis !== 'undefined') {
          window.addthis.toolbox('.addthis_toolbox');
          window.addthis.layers.refresh;
        }

        // Google Analytics - send pageview
        if (post.displayed !== true) {
          _this.methods.pushToGTM(post.link);

          post.displayed = true;
        }
      }
    },

    onScroll() {
      /**
       * onScroll
       * Scroll and touchstart events
       *
       * @since 1.0
       */

      let _this, url, scrollTop;

      _this = this;
      url = window.location.href.split('?')[0];
      scrollTop = window.pageYOffset;

      // if (_this.timer) {
      //   clearTimeout(_this.timer);
      // }

      if (_this.active && !_this.popstate && scrollTop > 1) {
        _this.timer = window.setTimeout(function() {
          // Get container scroll position
          let fromTop = scrollTop + 30;
          let posts = document.querySelectorAll('.single-post');

          // Loop all posts
          let current = Array.prototype.filter.call(posts, function(n, i) {
            if (typeof _this.getOffset === 'function') {
              let divOffset = _this.getOffset(n);
              if (divOffset.top < fromTop) {
                return n;
              }
            }
          });

          // Get the data attributes of the current element
          let currentPost = _this.posts[current.length - 1];

          // Set URL if current URL is different than the container.
          if (typeof currentPost !== 'undefined' && url !== currentPost.link) {
            _this.setURL(currentPost);
          }
        }, 15);
      }
    },

    scrollToPost(id) {
      /**
       * scrollToPost
       * Smooth scroll user to current post
       * @since 1.0
       */

      let _this, target, top;
      _this = this;
      top = 0;
      target = document.querySelector('#post-' + id);
      if (target) {
        // Confirm target has children, if not move to top of page. (offset fix_
        target = target.hasChildNodes()
          ? target
          : document.querySelector('body');

        top =
          typeof _this.getOffset === 'function'
            ? _this.getOffset(target).top
            : target.offsetTop;
      }

      // Scroll window to position
      window.setTimeout(function() {
        window.scrollTo(0, top);
        //$(document).scrollTop(0);
      }, 50);

      // Set popstate flag to false after transition is done
      window.setTimeout(function() {
        _this.popstate = false;
      }, 250);
    },

    onPopState(event) {
      /**
       * onPopState
       * Fires when users click back or forward browser buttons
       *
       * @since 1.0
       */

      let _this = this;

      if (_this.active) {
        _this.popstate = true;
        let id;
        if (event.state) {
          id = event.state.postID;
        } else {
          _this.popstate = true;
          id = _this.posts[0].id;
        }

        // Move to post
        _this.popstate = true;
        _this.scrollToPost(id);
      }
    },
  },

  watch: {
    $route(to, from) {
      let _this = this;

      //@todo: create a reset function because of routing problems
      _this.owl = null;
      _this.posts = window.posts ? window.posts : [];
      _this.timer = null;
      _this.moreBtn = '';
      _this.headData = {};
      _this.loaded = false;
      _this.popstate = false;
      _this.topAdvert = false;
      _this.currentPost = false;
      _this.owl_options = {
        items: 1,
        margin: 0,
        nav: true,
        loop: true,
        dots: false,
        video: true,
        autoplay: false,
        autoHeight: false,
      };
      _this.advertising = window.advertising;

      window.EventBus.listen('isPostLoaded', _this.postLoaded);

      _this.methods.getData(to.params.pathMatch, function(data) {
        _this.methods.handleData(data);
      });
    },

    loaded(newVal, oldVal) {
      let _this = this;
      if (newVal === true) {
        window.EventBus.fire('isPostLoaded');

        window.addEventListener('scroll', _this.onScroll);
        window.addEventListener('touchstart', _this.onScroll);

        /**
         * popstate
         * Window popstate eventlistener
         *
         * @since 1.0
         */
        window.addEventListener('popstate', function(event) {
          if (typeof window.history.pushState == 'function') {
            _this.onPopState(event);
          }
        });
      }
    },
  },

  // Fetches posts when the component is created.
  created() {
    let _this = this;

    window.EventBus.listen('isPostLoaded', _this.postLoaded);
    window.EventBus.listen('loadPosts', _this.loadMorePosts);

    if (window.posts) {
      window.posts.forEach(function(data) {
        data.displayed = true;
        _this.methods.handleData(data);
      });
    } else {
      _this.methods.getData(this.$route.params.pathMatch, function(data) {
        data.displayed = true;
        _this.methods.handleData(data);
      });
    }
  },

  destroyed() {
    let _this = this;

    if (_this.owl) {
      _this.owl.trigger('destroy.owl.carousel');
    }

    _this.owl = null;
    _this.posts = [];
    _this.timer = null;
    _this.moreBtn = '';
    _this.headData = {};
    _this.loaded = false;
    _this.popstate = false;
    _this.topAdvert = false;
    _this.currentPost = false;
    _this.owl_options = {
      items: 1,
      margin: 0,
      nav: true,
      loop: true,
      dots: false,
      video: true,
      autoplay: false,
      autoHeight: false,
    };
    _this.advertising = window.advertising;

    window.removeEventListener('scroll', _this.handleScroll);
    window.removeEventListener('touchstart', _this.handleScroll);
  },
};
</script>

<style lang="scss">
@import '../../lib/tiled-gallery-carousel-without-jetpack-dependencies';
@import '../../../assets/sass/post';
</style>
