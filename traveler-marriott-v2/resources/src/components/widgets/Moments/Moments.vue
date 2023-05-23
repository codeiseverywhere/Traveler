<template>
  <lazy-component tag="div" class="moments" @show="preGetMoments">
    <h2
      v-html="siteData.i18n.thingsToDo"
      v-if="loaded === true && moments.length"
    />
    <ul
      class="grid-x grid-margin-x medium-up-3 related-moments"
      v-if="loaded === true && moments.length"
    >
      <m-moment v-for="(moment, key) in moments" :key="key" :moment="moment" :title="title" />
    </ul>
  </lazy-component>
</template>

<script>
const Moment = () =>
  import(/* webpackChunkName: "/chunk/widgets/Moment" */ './Moment');

const PlacePass = {
  config: {
    place_pass_api_host: 'https://partner.pp-api.com',
    place_pass_api_key: '1109c10c28f54db6a7018a5f4b23727a',
    partner_id: '84389150-8f48-43b7-ac6b-25c5f1fb955a',
  },
  apis: {
    destination_autocomplete: '/data-api/partners/places/autocomplete',
    search: '/searches/search',
  },
  constants: {
    search_results_per_page: '3',
    default_rewards_scheme: 'MAR',
  },
};

export default {
  name: 'm-moments',
  props: {
    title: {
      default: '',
      required: false,
      type: String,
    },
  },
  data() {
    return {
      moments: [],
      city: false,
      loaded: false,
      momentsData: false,
      siteData: window.siteData,
    };
  },

  methods: {
    makeRequest: (method, url, data = null) => {
      return window.axios({
        method: method,
        headers: {
          'partner-id': PlacePass.config.partner_id,
          'Ocp-Apim-Subscription-Key': PlacePass.config.place_pass_api_key,
          'Content-Type': 'application/json',
        },
        data: data,
        url,
      });
    },

    segmentTrackImpression: function(response) {
      if ({}.hasOwnProperty.call(window, 'analytics')) {
        let ProductIDs, ProductNames, destination, city, country;
        ProductIDs = [];
        ProductNames = [];

        response.hits.forEach(function(el) {
          ProductIDs.push(el.ProductId);
          ProductNames.push(el.ProductName);
        });

        if (response.hits.length >= 1) {
          city = response.hits[0].City;
          country = response.hits[0].Country;
          destination = city + ', ' + country;
        }

        window.analytics.track('Cross promotion impression', {
          ProductIDs: ProductIDs,
          ProductNames: ProductNames,
          destination: destination,
          'destination city': city,
          'destination country': country,
          MM_Campaign_first_touch: 'Marriott Traveler',
          MM_Campaign_last_touch: 'Marriott Traveler',
          MM_Medium_first_touch: 'Web',
          MM_Medium_last_touch: 'Web',
          MM_Source_first_touch: 'Marriott',
          MM_Source_last_touch: 'Marriott',
          MM_Term_first_touch: 'Experiences',
          MM_Term_last_touch: 'Experiences',
          MM_Content_first_touch: this.title,
          MM_Content_last_touch: this.title,
        });
      }
    },

    preGetMoments: function() {
      if ('1' === window.siteData.site_id) {
        this.momentsData = this.$el.parentNode.parentNode.querySelector(
          '.moments-data',
        );

        if (this.momentsData && this.momentsData.dataset.cityCode !== '') {
          let url =
            PlacePass.config.place_pass_api_host +
            PlacePass.apis.destination_autocomplete +
            '?count=1&q=' +
            this.momentsData.dataset.cityCode;

          this.makeRequest('GET', url)
            .then((result) => {
              this.getMoments(result.data.places[0].PlaceId);
            })
            .catch((e) => {
              console.log(e);
            });
        } else {
          this.getMoments(false);
        }
      }
    },

    getMoments: function(city) {
      if ('1' === window.siteData.site_id) {
        let data,
          url,
          query = '';

        url = PlacePass.config.place_pass_api_host + PlacePass.apis.search;

        if (this.momentsData) {
          query = this.momentsData.dataset.query;
        }

        data = {
          hitsPerPage: 3,
          isBookable: true,
          pageNumber: 0,
          query: query,
          sortBy: 'topRated',
        };

        if (city) {
          data.location = {
            geoLocationId: city,
          };
        }

        this.makeRequest('POST', url, JSON.stringify(data))
          .then((result) => {
            result.data.hits.forEach((hit) => {
              hit.ProductImage = hit.ProductImage.replace(
                'http://',
                'https://',
              );
              this.moments.push(hit);
            });

            this.segmentTrackImpression(result.data);

            this.loaded = true;
          })
          .catch((e) => {
            console.log(e);
          });
      }
    },
  },

  components: {
    'm-moment': Moment,
  },
};
</script>

<style lang="scss">
@import 'Moments';
</style>
