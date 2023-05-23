export const foundationMixin = {
  mounted() {
    // eslint-disable-next-line
    $(this.$el).foundation();
  },
  destroyed() {
    // eslint-disable-next-line
    $(this.$el).foundation.destroy();
  },
};
