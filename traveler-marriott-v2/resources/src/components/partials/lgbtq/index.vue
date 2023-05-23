<template>
  <div>
    <l-banner
      v-if="blocks[0].blockName === 'traveler-lgbtq/hero'"
      :content="blocks[0].innerBlocks"
    ></l-banner>
    <l-scroll-container :blocks="blocks"></l-scroll-container>
  </div>
</template>

<script>
const Banner = () =>
  import(
    /* webpackChunkName: "/chunk/partials/lgbtq/Banner" */ '../lgbtq/Banner'
  );

const ScrollContainer = () =>
  import(
    /* webpackChunkName: "/chunk/partials/lgbtq/ScrollContainer" */ '../lgbtq/ScrollContainer'
  );

export default {
  name: 'lgbtq',
  props: ['blocks'],
  components: {
    'l-banner': Banner,
    'l-scroll-container': ScrollContainer,
  },
  mounted() {},
};
</script>

<style lang="scss">
@import 'css/animate-reveal.scss';
@import 'css/background-gradient.scss';
@import 'css/default.scss';
@import 'css/site-max-width.scss';
@import 'css/section-inner.scss';
@import 'css/navlink.scss';
@import 'css/heading.scss';
@import 'css/body-copy.scss';
@import 'css/eyebrow.scss';
</style>
