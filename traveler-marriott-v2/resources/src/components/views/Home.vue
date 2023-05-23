<template>
  <section class="home grid-container" v-if="loaded === true">
    <vue-headful
      :title="page.yoast_meta.title"
      :description="page.yoast_meta.metadesc"
      :url="page.yoast_meta.canonical"
      :lang="page.yoast_meta.locale"
      :ogLocale="page.yoast_meta.locale"
      :image="page.image.url"
      :image-width="page.image.width"
      :image-height="page.image.height"
      :image-alt="page.image.alt"
    />
    <m-owl v-if="page.acf.hero_slider.length" :slides="page.acf.hero_slider" />
    <m-ad-unit v-if="page.acf.hero_slider.length" :src="topAdvert" />
    <div class="grid-x grid-padding-x split-content">
      <div class="cell large-9">
        <m-category-posts
          v-for="category in page.acf.featured_categories"
          :key="category.categories"
          :term-id="category.categories"
          filter="&homepage=true"
        />
      </div>
      <div class="cell large-3 sidebar">
        <template v-for="(widget, index) in page.acf.sidebar">
          <component
            v-if="widget.acf_fc_layout === 'm-ad-unit'"
            v-bind:is="widget.acf_fc_layout"
            :key="index"
            :src="widget.ad_id"
          />
          <component
            v-else-if="widget.acf_fc_layout === 'm-booking'"
            v-bind:is="widget.acf_fc_layout"
            :key="index"
            :type="'mobile'"
          />
          <component
            v-else-if="widget.acf_fc_layout === 'm-podcastsbehind'"
            v-bind:is="widget.acf_fc_layout"
            :key="index"
            :analyticsPageName="'Homepage'"
            :classPageName="'home'"
          />
          <component
            v-else-if="widget.acf_fc_layout === 'm-podcasts'"
            v-bind:is="widget.acf_fc_layout"
            :key="index"
            :analyticsPageName="'Homepage'"
            :classPageName="'home'"
          />
          <component
            v-else
            v-bind:is="widget.acf_fc_layout"
            :key="index"
            :data="widget.posts"
          />
          <component
            v-else-if="widget.acf_fc_layout === 'm-podcastsab'"
            v-bind:is="widget.acf_fc_layout"
            :key="index"
            :analyticsPageName="'Homepage'"
            :classPageName="'home'"
          />
         
        </template>
      </div>
    </div>
    <template v-for="(widget, index) in page.acf.bottom_content">
      <component
        v-if="widget.acf_fc_layout === 'm-ad-unit'"
        v-bind:is="widget.acf_fc_layout"
        :key="index"
        :src="widget.ad_id"
      />
      <component
        v-else-if="widget.acf_fc_layout === 'm-video-slider'"
        v-bind:is="widget.acf_fc_layout"
        :key="index"
        :slides="widget.slider"
      />
      <component
        v-else-if="widget.acf_fc_layout === 'm-content-slider'"
        v-bind:is="widget.acf_fc_layout"
        :key="index"
        :slides="widget.slider"
      />
      <component
        v-else-if="widget.acf_fc_layout === 'm-places'"
        v-bind:is="widget.acf_fc_layout"
        :key="index"
        :placeA="widget.featured_place_1"
        :placeB="widget.featured_place_2"
        :placeC="widget.featured_place_3"
      />
      <component
        v-else
        v-bind:is="widget.acf_fc_layout"
        :data="widget.posts"
        :key="index"
      />

    </template>
  </section>
</template>

<script>
const Owl = () =>
  import(/* webpackChunkName: "/chunk/widgets/Owl" */ '../widgets/Owl');
const Places = () =>
  import(/* webpackChunkName: "/chunk/widgets/Places" */ '../widgets/Places');
const AdUnit = () =>
  import(/* webpackChunkName: "/chunk/widgets/AdUnit" */ '../widgets/AdUnit');
const Podcasts = () =>
  import(
    /* webpackChunkName: "/chunk/widgets/Podcasts" */ '../widgets/Podcasts'
  );
const VideoOwl = () =>
  import(
    /* webpackChunkName: "/chunk/widgets/VideoOwl" */ '../widgets/VideoOwl'
  );
const Booking = () =>
  import(
    /* webpackChunkName: "/chunk/widgets/Booking" */ '../widgets/Booking/Booking'
  );
const EditorsPicks = () =>
  import(
    /* webpackChunkName: "/chunk/widgets/EditorsPicks" */ '../widgets/EditorsPicks'
  );
  const PodcastsAb = () =>
  import(
    /* webpackChunkName: "/chunk/widgets/PodcastsAb" */ '../widgets/PodcastsAb'
  );
  const PodcastsBehind = () =>
  import(
    /* webpackChunkName: "/chunk/widgets/PodcastsBehind" */ '../widgets/PodcastsBehind'
  );
const ContentSlider = () =>
  import(
    /* webpackChunkName: "/chunk/widgets/ContentSlider" */ '../widgets/ContentSlider'
  );
const CategoryPosts = () =>
  import(
    /* webpackChunkName: "/chunk/widgets/CategoryPosts" */ '../widgets/CategoryPosts'
  );
const RecommendedForYou = () =>
  import(
    /* webpackChunkName: "/chunk/widgets/RecommendedForYou" */ '../widgets/RecommendedForYou'
  );

export default {
  name: 'Home',
  data() {
    return {
      page: {},
      errors: [],
      podcasts: {},
      loaded: false,
      siteData: window.siteData,
      lang: '1' === window.siteData.site_id ? 'en' : 'es',
      topAdvert:
        window.advertising.adDesktop +
        Math.round(Math.random() * 10000000000000000),
      sideAdvert:
        window.advertising.adMobile +
        Math.round(Math.random() * 10000000000000000),
      methods: {
        getData: (slug, callback) => {
          window.axios
            .get(window.siteData.rest_base_page + 'wp/v2/pages?slug=home')
            .then((response) => {
              // JSON responses are automatically parsed.
              callback(response.data[0]);
            })
            .catch((e) => {
              console.log(e);
              this.errors.push(e);
            });
        },
        replaceKey: (obj, defaultAd, isGroup) => {
          if (obj !== '') {
            obj.forEach((el, index) => {
              if (el.acf_fc_layout.slice(0, 2) !== 'm-') {
                el.acf_fc_layout = 'm_' + el.acf_fc_layout;
              }
              el.acf_fc_layout = el.acf_fc_layout.replace(/_/gi, '-');
              if (el.acf_fc_layout === 'm-ad-unit') {
                if (!isNaN(el.ad_id) && '' !== el.ad_id) {
                  if (true === isGroup) {
                    el.ad_id =
                      window.siteData.current_site + 'ads/group/' + el.ad_id;
                  } else {
                    el.ad_id =
                      window.siteData.current_site +
                      'ads/single/' +
                      el.ad_id +
                      '?cb=' +
                      Math.round(Math.random() * 10000000000000000);
                  }
                } else {
                  el.ad_id = defaultAd;
                }
              }
              obj[index] = el;
            });
            return obj;
          }
        },
      },
    };
  },

  components: {
    'm-owl': Owl,
    'm-places': Places,
    'm-ad-unit': AdUnit,
    'm-booking': Booking,
    'm-podcasts': Podcasts,
    'm-video-slider': VideoOwl,
    'm-editors-picks': EditorsPicks,
    'm-podcastsab': PodcastsAb,
    'm-podcastsbehind': PodcastsBehind,
    'm-content-slider': ContentSlider,
    'm-category-posts': CategoryPosts,
    'm-recommended-for-you': RecommendedForYou,
  },

  watch: {
    $route(to, from) {
      let _this = this;
      //@todo: create a reset function because of routing problems
      _this.loaded = false;
      _this.topAdvert =
        window.advertising.adDesktop +
        Math.round(Math.random() * 10000000000000000);
      _this.sideAdvert =
        window.advertising.adMobile +
        Math.round(Math.random() * 10000000000000000);

      _this.methods.getData(to.params.pathMatch, (data) => {
        _this.page = data;
        _this.loaded = true;
      });
    },
    loaded(newVal, oldVal) {
      let _this = this;
      if (newVal === true) {
        //Hero components
        if (_this.page.acf.top_ad_unit) {
          _this.topAdvert =
            window.siteData.current_site +
            'ads/group/' +
            _this.page.acf.top_ad_unit +
            '?cb=' +
            Math.round(Math.random() * 10000000000000000);
        }

        //Sidebar components
        _this.methods.replaceKey(
          _this.page.acf.sidebar,
          _this.sideAdvert,
          true,
        );

        //Bottom content components
        _this.methods.replaceKey(
          _this.page.acf.bottom_content,
          _this.topAdvert,
          false,
        );
      }
    },
  },

  created() {
    let _this = this;
    _this.methods.getData(this.$route.params.pathMatch, (data) => {
      _this.page = data;
      _this.loaded = true;
    });
  },
};
</script>

<style lang="scss">
@import '../../../assets/sass/home';
</style>
