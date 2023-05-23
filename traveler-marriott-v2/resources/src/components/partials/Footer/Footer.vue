<template>
  <footer :class="'footer ' + footerClass">
    <m-booking type="desktop"></m-booking>
    <div 
         v-if="siteData.inApp !== 'true'"
         class="grid-container"
    >
      <div class="marriott-bonvoy-loyalty text-center">
        <img
          loading="lazy"
          :data-src="brandFooter"
          class="native-lazyload-js-fallback"
        />
      </div>
      <div class="grid-x grid-padding-x">


        <div class="tpin cell small-2 medium-1">
          <svg xmlns="http://www.w3.org/2000/svg" width="50" height="50">
            <g fill="none" fill-rule="nonzero" transform="translate(1 1)">
              <circle
                cx="24.022"
                cy="24.022"
                r="22.094"
                fill="#FFF"
                stroke="#FF9662"
                stroke-width="4.4"
              />
              <path
                fill="#231C19"
                d="M16.28 16.68h5.95v19.054h3.678V16.68h5.934v-3.242h-16.53z"
              />
            </g>
          </svg>
        </div>
        <div class="cell small-10 medium-6 footer-about">
          <p
            itemprop="description"
            itemscope=""
            itemtype="https://schema.org/Corporation"
          >
            {{ siteData.i18n.description }}
          </p>
          <strong class="category-label text-uppercase">
            <router-link :to="siteData.links.about">
              {{ siteData.i18n.about }}
            </router-link>
          </strong>
          <span>&nbsp;&nbsp;&nbsp;&nbsp;|&nbsp;&nbsp;&nbsp;</span>
          <strong class="category-label text-uppercase">
            <a :href="siteData.site_email">{{ siteData.i18n.contact }}</a>
          </strong>
        </div>
        <div class="cell medium-5 social-profiles text-right">
          <ul class="list-social">
            <li>{{ siteData.i18n.follow }}</li>
            <li>
              <a
                href="https://www.facebook.com/mbonvoytraveler/"
                target="_blank"
                rel="noreferrer noopener"
                ><img
                  title="Facebook"
                  alt="Facebook"
                  src="/wp-content/themes/traveler-marriott-v2/public/images/facebook-f.svg"
              /></a>
            </li>
            <li>
              <a
                href="https://twitter.com/mbonvoytraveler/"
                target="_blank"
                rel="noreferrer noopener"
                ><img
                  title="Twitter"
                  alt="Twitter"
                  src="/wp-content/themes/traveler-marriott-v2/public/images/twitter.svg"
              /></a>
            </li>
            <li>
              <a
                href="https://www.youtube.com/channel/UCpDtA9d5Z8SsNEnsVgwq9LA?sub_confirmation=1"
                target="_blank"
                rel="noreferrer noopener"
                ><img
                  title="Youtube"
                  alt="Youtube"
                  src="/wp-content/themes/traveler-marriott-v2/public/images/youtube.svg"
              /></a>
            </li>
            <li>
              <a
                href="https://www.pinterest.com/marriottbonvoytraveler/"
                target="_blank"
                rel="noreferrer noopener"
                ><img
                  title="Pinterest"
                  alt="Pinterest"
                  src="/wp-content/themes/traveler-marriott-v2/public/images/pinterest.svg"
              /></a>
            </li>
            <li>
              <a
                href="https://www.instagram.com/mbonvoytraveler/"
                target="_blank"
                rel="noreferrer noopener"
                ><img
                  title="Instagram"
                  alt="Instagram"
                  src="/wp-content/themes/traveler-marriott-v2/public/images/instagram.svg"
              /></a>
            </li>
          </ul>
        </div>
      </div>
      <div class="grid-x footer-copyright">
        <div class="cell large-1"></div>
        <div class="cell large-11 footer-about">
          <ul>
            <li class="text-uppercase">
              &copy;&nbsp;{{ new Date().getFullYear() }}&nbsp;
              <span
                itemprop="branchOf"
                itemscope
                itemtype="https://schema.org/Corporation"
              >
                <span itemprop="name">Marriott International, Inc.</span>
              </span>
            </li>
            <li>
              <strong class="category-label text-uppercase">
                <a
                  :href="siteData.links.tos"
                  target="_blank"
                  rel="noreferrer noopener"
                >
                  {{ siteData.i18n.tos }}
                </a>
              </strong>
            </li>
            <li>
              <strong class="category-label text-uppercase">
                <a
                  :href="siteData.links.privacy"
                  target="_blank"
                  rel="noreferrer noopener"
                >
                  {{ siteData.i18n.privacy }}
                </a>
              </strong>
            </li>
            <li>
              <strong
                id="ccpacontainer"
                class="category-label text-uppercase"
              />
            </li>
            <li>
              <strong id="teconsent" class="category-label text-uppercase" />
            </li>
          </ul>
        </div>
      </div>

    </div>
    <!--
    <div :class="'reveal modal-full ' + newsletterClass" id="newsletterModalMenu" data-reveal>
      <button class="close-button" data-close aria-label="Close" type="button">
        <span aria-hidden="true">&times;</span>
      </button>
      <a :href="siteData.links.about">
        <img src="/wp-content/themes/traveler-marriott-v2/public/images/popup.jpg"/>
      </a>
    </div>
    -->
  </footer>
</template>

<script>
const Booking = () =>
  import(
    /* webpackChunkName: "/chunk/widgets/Booking" */ '../../widgets/Booking/Booking'
  );

export default {
  data() {
    return {
      footerClass: false,
      newsletterClass: 'isHidden',
      siteData: window.siteData,
      brandFooter:
        '/wp-content/themes/traveler-marriott-v2/public/images/brand-footer.jpg',
      methods: {
        getCookie: (name) => {
          let b = document.cookie.match(
            '(^|;)\\s*' + name + '\\s*=\\s*([^;]+)',
          );
          return b ? b.pop() : '';
        },

        setCookie: (name, value, days) => {
          let expires = '';
          if (days) {
            let date = new Date();
            date.setTime(date.getTime() + days * 24 * 60 * 60 * 1000);
            expires = '; expires=' + date.toUTCString();
          }
          document.cookie = name + '=' + (value || '') + expires + '; path=/';
        },
      },
    };
  },

  components: {
    'm-booking': Booking,
  },

  methods: {
    footerType() {
      let _this = this;

      _this.$route.matched.some((record) => {
        switch (record.meta.view) {
          case 'PostACF':
          case 'PageHub':
          case 'PostBlock':
            _this.footerClass = 'footer-interactive';
            break;
          default:
            _this.footerClass = 'footer-regular';
        }
      });

      if ('1' !== this.siteData.site_id) {
        this.newsletterClass = 'isHidden';
      }
    },
  },

  watch: {
    $route() {
      let _this = this;
      _this.footerType();
    },
  },

  created() {
    let _this = this;
    _this.footerType();
  },

  mounted() {
    let _this = this;
    if ('1' !== _this.siteData.site_id) {
      _this.brandFooter =
        '/wp-content/themes/traveler-marriott-v2/public/images/brand-footer-es.jpg';
    }
    if (window.matchMedia('(max-width: 769px)').matches) {
      if ('1' !== _this.siteData.site_id) {
        _this.brandFooter =
          '/wp-content/themes/traveler-marriott-v2/public/images/brand-footer-tablet-es.jpg';
      } else {
        _this.brandFooter =
          '/wp-content/themes/traveler-marriott-v2/public/images/brand-footer-tablet.jpg';
      }
    }
  },
};
</script>

<style lang="scss">
@import 'Footer';
</style>
