<template>
  <section class="lgbtq-icon-and-list" style="background-color: #000000;">
    <div v-for="(item, index) in blocks" v-if="item.blockName !== null">
      <l-icon-list-heading
        v-if="item.blockName === 'traveler-lgbtq/icon-list'"
        :content="item.innerBlocks"
      ></l-icon-list-heading>
      <l-icon-list-items
        v-if="item.blockName === 'traveler-lgbtq/icon-list-item'"
        :content="item.innerBlocks"
      ></l-icon-list-items>
    </div>
  </section>
</template>

<script>
const IconListHeading = () =>
  import(
    /* webpackChunkName: "/chunk/partials/lgbtq/IconList" */ '../../lgbtq/IconListHeading'
  );
const IconListItems = () =>
  import(
    /* webpackChunkName: "/chunk/partials/lgbtq/IconListItems" */ '../../lgbtq/IconListItems'
  );
export default {
  name: 'l-icon-list-parent',
  props: ['blocks'],
  components: {
    'l-icon-list-heading': IconListHeading,
    'l-icon-list-items': IconListItems,
  },
};
</script>

<style lang="scss">
@import 'lgbtq-icon-list.scss';
</style>
