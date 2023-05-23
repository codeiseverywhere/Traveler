<template>
  <div class="where-to" v-if="properties">
    <h2 v-html="siteData.i18n.whereTo" />
    <ul class="where-to-links" v-html="properties" />
  </div>
</template>

<script>
export default {
  name: 'm-properties',
  props: {
    title: {
      default: '',
      required: true,
      type: String,
    },
    properties: {
      default: '',
      required: true,
      type: String,
    },
  },
  data() {
    return {
      branch_links: [],
      siteData: window.siteData,
    };
  },
  methods: {
    segmentTrackProperties: function(el) {
      window.analytics.trackLink(el, 'Properties click', {
        MM_Campaign_first_touch: 'Marriott Traveler',
        MM_Campaign_last_touch: 'Marriott Traveler',
        MM_Medium_first_touch: 'Web',
        MM_Medium_last_touch: 'Web',
        MM_Source_first_touch: 'Marriott',
        MM_Source_last_touch: 'Marriott',
        MM_Term_first_touch: 'Properties',
        MM_Term_last_touch: 'Properties',
        MM_Content_first_touch: this.title,
        MM_Content_last_touch: this.title,
        PropertyName: el.innerText,
        PropertyURI: el.href,
      })
    }
  },
  mounted() {
    const isSegmentLoaded = setInterval(() => {
      if ({}.hasOwnProperty.call(window, 'analytics') && this.properties) {
        clearInterval(isSegmentLoaded);
        Array.from(this.$el.querySelectorAll('.where-to-links a')).forEach((el) => {
          this.segmentTrackProperties(el)
        })
      }
    }, 300);

    /*
      if ( this.$el.childNodes.length ) {
        //if (process.env.NODE_ENV === 'production') {

      Array.from(this.$el.querySelectorAll(".where-to-links a")).forEach(
        (el) => {
          el.href =
            "https://marriott.app.link/?" +
            "%24deeplink_path=" +
            encodeURIComponent("marriott://mobile?fid=SR&ct=LosAngeles&cn=US&scid=41e74023-642d-4d4f-9af4-f8855b000000") +
            "&%24fallback_url=" +
            encodeURIComponent("" + el.href) +
            "&~campaign=hellothere"
        });

      /!*
      Array.from(this.$el.querySelectorAll('.where-to-links a')).forEach(
        (el) => {
          this.branch_links.push({
            channel: 'marriottTraveler',
            feature: 'onboarding',
            campaign: 'new product',
            stage: 'new user',
            tags: ['one', 'two', 'three'],
            data: {
              $og_title: el.innerText,
              $desktop_url: el.href,
            },
          });
        },
      );

      window.axios
        .post(
          'https://api2.branch.io/v1/url/bulk/key_live_jlOdk0lZqmew10LqP43TtdhaxDeFwnIj',
          this.branch_links,
        )
        .then((response) => {
          console.log(response);
        })
        .catch((e) => {
          console.log(e);
        });
        *!/
      //}
      }
      */
  },
};
</script>

<style lang="scss">
@import 'Properties';
</style>
