<template>
  <div :data-id="dividerID" className="section-divider"></div>
</template>

<script>
export default {
  name: 'lgbtq-dividerr',
  props: ['content'],
  data() {
    return {
      dividerID: false,
      methods: {
        sanitizeID: (data) => {
          let htmlObject = document.createElement('div');
          htmlObject.innerHTML = data;
          return htmlObject
            .getElementsByTagName(`p`)[0]
            .textContent.replace(/ /g, '_')
            .toLowerCase();
        },
        setData: () => {
          let _this = this;

          _this.dividerID = _this.methods.sanitizeID(
            _this.content[0].innerContent[0],
          );
        },
      },
    };
  },
  created() {
    let _this = this;
    _this.methods.setData();
  },
};
</script>

<style lang="scss">
.section-divider {
  display: none;
}
</style>
