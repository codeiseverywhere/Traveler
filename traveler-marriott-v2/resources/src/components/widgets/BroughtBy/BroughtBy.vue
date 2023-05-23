<template>
  <div :class="type">
    <small v-html="sponsoredLabel" />
    <a
      v-if="showSponsored"
      :href="data.link"
      target="_blank"
      rel="nofollow external"
    >
      <img
        loading="lazy"
        :data-src="data.logo"
        :alt="data.name"
        class="branded-category native-lazyload-js-fallback"
      />
    </a>
    <img
      loading="lazy"
      v-else
      :data-src="data.logo"
      :alt="data.name"
      class="branded-category native-lazyload-js-fallback"
    />
  </div>
</template>

<script>
export default {
  name: 'm-brought-by',
  props: ['data', 'type'],
  computed: {
    showSponsored() {
      return this.data.link;
    },
    sponsoredLabel() {
      return this.data.label && this.data.label !== ''
        ? this.data.label
        : window.siteData.i18n.presentedBy;
    },
  },
};
</script>

<style lang="scss">
@import 'BroughtBy';
</style>
