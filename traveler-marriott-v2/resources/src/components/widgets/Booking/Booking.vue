<template>
  <aside class="widget">
    <div :class="'booking grid-x animate-search-form ' + wrapperType">
      <div class="cell logo text-center">
        <img :src="logo" alt="Marriott.com - logo" />
      </div>
      <div class="cell auto">
        <form class="grid-x" @submit="searchHotels">
          <fieldset class="cell">
            <label>
              <span class="text-capitalize">{{
                siteData.i18n.destination
              }}</span>
              <img
                class="search-img"
                title="Search"
                alt="Search"
                src="/wp-content/themes/traveler-marriott-v2/public/images/search.svg"
              />
              <input
                type="text"
                class="single-search-destination"
                required
                minlength="2"
                v-model="destination"
                :placeholder="siteData.i18n.placeholder"
              />
            </label>
          </fieldset>
          <fieldset class="cell">
            <label>
              <span class="text-capitalize">{{ siteData.i18n.dates }}</span>
              <m-date-picker
                v-if="isModern"
                v-model="range"
                is-range
                :available-dates="{ start: start, end: end }"
                :input-debounce="500"
              >
                <template v-slot="{ inputValue, inputEvents }">
                  <input
                    class="text-left"
                    :placeholder="siteData.i18n.checkInOut"
                    :value="
                      inputValue.start
                        ? inputValue.start + ' - ' + inputValue.end
                        : ''
                    "
                    v-on="inputEvents.end"
                  />
                </template>
              </m-date-picker>
              <input v-else type="text" class="text-left" :placeholder="siteData.i18n.checkInOut" />
            </label>
          </fieldset>
          <div class="cell auto text-center">
            <button class="button black" type="submit">
              {{ siteData.i18n.findHotels }}
            </button>
          </div>
        </form>
      </div>
    </div>
  </aside>
</template>

<script>

export default {
  name: 'm-booking',
  props: {
    type: {
      default: 'desktop',
      required: true,
      type: String,
    },
  },
  data() {
    const today = new Date();
    const isIE =
      {}.hasOwnProperty.call(window, 'MSInputMethodContext') &&
      {}.hasOwnProperty.call(document, 'documentMode');
    return {
      destination: '',
      wrapperType: this.type,
      siteData: window.siteData,
      lang: '1' === window.siteData.site_id ? 'en' : 'es',
      isModern: process.env.BUILD_MODERN === 'true',
      start: today.getTime(),
      end: today.setFullYear(today.getFullYear() + 1),
      range: {
        start: '',
        end: '',
      },
      masks: {
        title: 'MMMM YYYY',
        weekdays: 'W',
        navMonths: 'MMM',
        input: ['L', 'YYYY-MM-DD', 'YYYY/MM/DD'],
        dayPopover: 'WWW, MMM D, YYYY',
        data: ['L', 'YYYY-MM-DD', 'YYYY/MM/DD'],
      },

    };
  },

  components: {
    'm-date-picker': function (resolve) {
      // This special require syntax will instruct Webpack to
      // automatically split your built code into bundles which
      // are loaded over Ajax requests.
      if (process.env.BUILD_MODERN === 'true') {
        require(['v-calendar/lib/components/date-picker.common'], resolve)
      } else {
        resolve({
          template: '<input type="text" class="text-left" placeholder="'+window.siteData.i18n.checkInOut+'" />'
        })
      }
    }
  },

  computed: {
    logo() {
      return `/wp-content/themes/traveler-marriott-v2/public/images/booking-logo-${this.wrapperType}-${this.lang}.png`;
    },
  },

  methods: {
    pushToGTM: (url, destination) => {
      if ({}.hasOwnProperty.call(window, 'GTMdataLayer')) {
        window.GTMdataLayer.push({
          event: 'trackEvent',
          eventCategory: 'Booking Widget',
          eventAction: destination,
          eventLabel: url,
        });
      }
    },

    searchHotels(e) {
      const _this = this;
      const target =
        '1' === _this.siteData.site_id
          ? 'https://www.marriott.com'
          : 'https://www.espanol.marriott.com';

      e.preventDefault();

      if (_this.destination !== '' && _this.range) {
        const url = `${target}/search/submitSearch.mi?searchType=Keyword&keywords=${
          _this.destination
        }&fromDate=${_this.range.start.toLocaleDateString(
          'en-US',
        )}&toDate=${_this.range.end.toLocaleDateString(
          'en-US',
        )}&flexibleDateSearch=false`;
        const win = window.open(url, '_blank');

        _this.pushToGTM(url, _this.destination);
        win.focus();
      }
    },
  },

  mounted() {
    const _this = this;

    if ( _this.isModern ) {
      const mediaQuery = window.matchMedia('(max-width: 769px)');

      if (mediaQuery.matches) {
        _this.wrapperType = 'mobile';
      }

      const mediaObserve = (e) => {
        _this.wrapperType = e.matches ? 'mobile' : _this.type;
      };

      mediaQuery.addEventListener('change', mediaObserve);
    }
  },
};
</script>

<style lang="scss">
@import 'Booking';
</style>
