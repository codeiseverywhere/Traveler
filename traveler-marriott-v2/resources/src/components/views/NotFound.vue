<template>
  <section class="not-found">
    <div class="grid-container">
      <header class="term-title">
        <h1 class="text-center">404</h1>
      </header>
      <div class="post-content text-center">
        <div class="h3" v-html="siteData.i18n.notFound"></div>
      </div>
    </div>
  </section>
</template>

<script>
export default {
  name: 'NotFound',
  data() {
    return {
      siteData: window.siteData,
    };
  },

  methods: {
    openModal: () => {
      window.searchModal.open();
    },
  },

  mounted() {
    document
      .querySelector('.openSearchModal')
      .addEventListener('click', this.openModal, false);
    document
      .querySelector('.openSearchModal')
      .addEventListener('touchend', this.openModal, false);
  },
};
</script>

<style lang="scss">
@import '../../../assets/sass/post';
</style>
