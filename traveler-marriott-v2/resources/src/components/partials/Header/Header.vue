<template>
  <header>
    <div 
    v-if="siteData.inApp !== 'true'"
    id="main-header" :class="'main-header grid-container ' + headerClass"
    >
      <div class="grid-x align-justify">
        <div
          v-if="siteData.inApp !== 'true'"
          class="cell small-1 title-bar"
          data-responsive-toggle="main-animated-menu"
          data-hide-for="large"
        >
          <button class="menu-icon" type="button" data-toggle />
        </div>
        <div class="cell small-4 medium-3 large-2 align-logo">
          <a :href="siteData.current_site">
            <img loading="lazy" :src="logo" :alt="siteData.site_name" />
          </a>
        </div>
        <div
          v-if="siteData.inApp !== 'true'"
          class="cell auto align-self-middle"
        >
          <ul class="menu text-uppercase top-menu align-right">
            <li class="hide-for-large">
              <button @click="showSearchBox">
                <img
                  class="search-img"
                  title="Search"
                  alt="Search"
                  :src="searchBtn"
                />
              </button>
            </li>
            <li class="">
              <a
                :href="siteData.links.bookRoom"
                target="_blank"
                rel="external"
                class="alert button font-bold book-a-room-links"
              >
                {{ siteData.i18n.bookRoom }}
              </a>
            </li>
            <li
              class="hide-for-small-only more-magazines"
              @mouseleave="closeMagazines"
            >
              <a class="magazines-toggler" @click="toggleClass">
                {{ siteData.i18n.magazines }}
              </a>
              <ul id="magazines" class="magazines-dropdown hide">
                <li v-for="(magazine, index) in magazines" :key="index">
                  <a
                    :href="magazine.url"
                    target="_blank"
                    rel="nofollow noopener external"
                  >
                    <img
                      loading="lazy"
                      :src="magazine.src"
                      :alt="magazine.name"
                    />
                  </a>
                </li>
              </ul>
            </li>
          </ul>
          <div
            class="top-bar"
            id="main-animated-menu"
            data-animate="fade-in fade-out"
          ></div>
        </div>
      </div>
      <m-searchbox />
    </div>
  </header>
</template>
<script>
export default {
  props: ['limit'],

  data() {
    return {
      errors: [],
      loaded: false,
      titleBar: false,
      magazines: window.magazines,
      siteData: window.siteData,
      headerTheme: 'header-light',
      origin: window.location.origin,
      logoUrl: '/wp-content/themes/traveler-marriott-v2/public/images/logo.svg',
      searchBtn:
        '/wp-content/themes/traveler-marriott-v2/public/images/search.svg',
      topAdvert:
        '//ag.yieldoptimizer.com/ag/pt?pt=XZgJML5Cvk&t=f&cb=' +
        Math.round(Math.random() * 10000000000000000),
    };
  },

  computed: {
    logo() {
      return this.logoUrl;
    },
    headerClass() {
      return this.headerTheme;
    },
    isMobile() {
      return window.matchMedia('(max-width: 769px)').matches;
    },
  },

  methods: {
    headerType() {
      const _this = this;

      // @todo figure out why this is needed
      /*
      _this.siteData.current_site = _this.siteData.current_site.replace(
        _this.origin,
        '',
      );
      */

      _this.$route.matched.some((record) => {
        switch (record.meta.view) {
          case 'PostACF':
          case 'PageHub':
          case 'PostBlock':
            _this.headerTheme = 'header-dark';
            _this.logoUrl =
              '/wp-content/themes/traveler-marriott-v2/public/images/logo-white.svg';
            _this.searchBtn =
              '/wp-content/themes/traveler-marriott-v2/public/images/search-white.svg';
            break;
          default:
            _this.headerTheme = 'header-light';
            _this.logoUrl =
              '/wp-content/themes/traveler-marriott-v2/public/images/logo.svg';
            _this.searchBtn =
              '/wp-content/themes/traveler-marriott-v2/public/images/search.svg';
        }
      });

      if ('1' !== _this.siteData.site_id) {
        _this.logoUrl =
          '/wp-content/themes/traveler-marriott-v2/public/images/logo-es.svg';
      }
    },
    showSearchBox: function() {
      window.searchModal.open();
    },
    toggleClass: (e) => {
      e.target.classList.toggle('is-active');
      document.querySelector('#magazines').classList.toggle('hide');
    },
    closeMagazines: (e) => {
      document.querySelector('#magazines').classList.add('hide');
      document
        .querySelector('.more-magazines > .magazines-toggler')
        .classList.remove('is-active');
    },
    fireResponsiveToggle() {
      setTimeout(() => {
        if (this.isMobile && false === this.titleBar) {
          this.titleBar = new Foundation.ResponsiveToggle(
            document.querySelector('.title-bar'),
          );
        }
      }, 1000);
    },
  },

  watch: {
    $route() {
      this.titleBar = false;
      this.headerType();
      this.fireResponsiveToggle();
    },
  },

  components: {
    'm-searchbox': () =>
      import(
        /* webpackChunkName: "/chunk/widgets/WPSearchBox" */ '../../widgets/WPSearchBox'
      ),
  },

  created() {
    this.headerType();

    const isBranchLoaded = setInterval(() => {
      if ({}.hasOwnProperty.call(window, 'branch')) {
        clearInterval(isBranchLoaded);

        //   let branchLink =
        //   "https://marriott.app.link/?" +
        //   "%24deeplink_path=" +
        //   encodeURIComponent("marriott://mobile?pid=laL3UeoPy4") +
        //   "&%24fallback_url=" +
        //   encodeURIComponent("" + el.href) +
        //   "&%24campaign=TravelerIntegration"

        this.siteData.links.bookRoom =
          '1' !== this.siteData.site_id
            ? 'https://www.espanol.marriott.com'
            : 'https://marriott.app.link/laL3UeoPy4';
      }
    }, 300);
  },

  mounted() {
    window.EventBus.fire('headerReady');
    this.fireResponsiveToggle();
  },

  destroyed() {
    let _this = this;
    _this.titleBar.destroy();
  },
};
</script>

<style lang="scss">
@import 'Header';
</style>
