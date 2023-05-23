<template>
  <li
    :id="moment.ProductId"
    class="cell related-moment"
    :data-city="moment.City"
    :data-country="moment.Country"
  >
    <span class="aspect_ratio-16_9">
      <img
        :src="moment.ProductImage"
        width="300"
        height="199"
        :alt="moment.ProductName"
      />
    </span>
    <span class="moment_title" v-html="moment.ProductName" />From
    <strong v-html="'$' + moment.Price" /><br />
    Earn
    <strong
      v-if="moment.LoyaltyDetails[0].loyaltyProgramId === 'MAR'"
      v-html="moment.LoyaltyDetails[0].loyaltyPoints"
    />
    <span v-html="moment.LoyaltyDetails[0].loyaltyProgramDisplayName" />
    <br />
    <a
      :href="moment.ProductUrl + '?scid=2446299e-e416-480f-9392-409f6df1ad2d'"
      class="overlay"
      target="_blank"
      @click="trackLinkClick"
      rel="noreferrer noopener"
    />
  </li>
</template>

<script>
export default {
  name: 'm-moment',
  props: {
    moment: {
      default: () => ({}),
      required: false,
      type: Object,
    },
    title: {
      default: '',
      required: true,
      type: String
    }
  },
  methods: {
    trackLinkClick: function(event) {
      if ({}.hasOwnProperty.call(window, 'GTMdataLayer')) {
        window.GTMdataLayer.push({
          event: 'trackEvent',
          eventCategory: 'Marriott Moments',
          eventAction: 'click',
          eventLabel: event.target.href
        });
      }

      // Track Segment Activities
      if ({}.hasOwnProperty.call(window, 'analytics')) {
        window.analytics.track('Cross promotion click', {
          ProductIDs: this.moment.ProductId,
          ProductName: this.moment.ProductName,
          destination: this.moment.City && this.moment.Country ? this.moment.City + ', ' + this.moment.Country : '',
          'destination city': this.moment.City,
          'destination country': this.moment.Country,
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

    }
  },

  segmentTrackActivities: function() {
  },

};
</script>
