<template>
  <section class="single" v-if="loaded === true">
    <div class="grid-container">
      <div class="hero-wrapper">
        <div class="aspect-ratio-16_9" v-if="typeof post.image !== 'undefined'">
          <img
            loading="lazy"
            :src="post.image.url"
            :alt="post.image.alt"
            :title="post.image.title"
          />
        </div>
        <div class="title-container text-center">
          <div
            class="caption"
            v-html="post.image.caption"
            v-if="typeof post.image !== 'undefined'"
          ></div>
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
      <m-related-posts :id="post.parent" />
    </div>
  </section>
</template>

<script>
import { createTag } from "../../mixins/utils";

const RelatedPosts = () =>
  import(
    /* webpackChunkName: "/chunk/widgets/RelatedPosts" */ '../widgets/RelatedPosts'
  );
const BroughtBy = () =>
  import(
    /* webpackChunkName: "/chunk/widgets/BroughtBy" */ '../widgets/BroughtBy/BroughtBy'
  );

export default {
  name: 'PostPreview',
  data() {
    return {
      siteData: window.siteData,
      post: {},
      loaded: false,
      topAdvert: false,
      advertising: window.advertising,
      methods: {
        getData: (slug, callback) => {
          window.axios
            .get(
              window.siteData.rest_base_page +
                'wp/v2/posts/' +
                slug +
                '/revisions/',
            )
            .then((response) => {
              callback(response.data[0]);
            })
            .catch((e) => {
              console.log(e);
            });
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
    };
  },

  components: {
    'm-brought-by': BroughtBy,
    'm-related-posts': RelatedPosts,
  },

  // Fetches posts when the component is created.
  created() {
    let _this, preview_id;
    _this = this;
    preview_id = _this.$route.query.p
      ? _this.$route.query.p
      : _this.$route.query.preview_id;

    _this.methods.getData(preview_id, function(data) {
      _this.post = data;
      _this.loaded = true;

      setTimeout(function() {
        _this.methods.doFlexVideo();
        // disable if not needed on the site.
        // _this.methods.triggerMediaElement()
      }, 500);
    });
  },

  mounted() {
    setTimeout(() => {
      if (this.$el.querySelectorAll('.quizz-container').length) {
        if (typeof quizzrScript === 'undefined') {
          const quizzrScript = createTag('https://dcc4iyjchzom0.cloudfront.net/widget/loader.js', 'quizzr-jssdk');
        }
      }

      if (this.$el.querySelectorAll('.instagram-media').length) {
        if (typeof instaScript === 'undefined') {
          const instaScript = createTag('https://www.instagram.com/embed.js', 'instagram-jssdk');
        }
      }
    }, 500);
  },
};
</script>

<style lang="scss">
@import '../../lib/tiled-gallery-carousel-without-jetpack-dependencies';
@import '../../../assets/sass/post';
</style>
