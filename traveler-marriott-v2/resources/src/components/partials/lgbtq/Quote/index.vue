<template>
  <section
    :class="[attrs.isLeftLayout ? 'lgbtq-quote-left' : 'lgbtq-quote-right']"
    style="background-color: #000000;"
  >
    <div
      :class="[attrs.className ? 'lgbtq-' + attrs.className : 'lgbtq']"
      class="lgbtq-section-image-background lgbtq-linear-gradient-top lgbtq-linear-gradient-bottom"
    >
      <div class="lgbtq-bg-desktop" v-if="mediaData.image_desktop">
        <img
          :src="mediaData.image_desktop"
          alt=" "
          class="lgbtq-background-image"
          data-scrollreveal="enter bottom"
        />
      </div>
      <div class="lgbtq-bg-mobile" v-if="mediaData.image_mobile">
        <img
          :src="mediaData.image_mobile"
          alt=" "
          class="lgbtq-background-image"
          data-scrollreveal="enter bottom"
        />
      </div>
      <div class="lgbtq-section-inner">
        <div
          :class="[
            attrs.isLeftLayout
              ? 'lgbtq-quote-left-container'
              : 'lgbtq-quote-right-container',
          ]"
        >
          <div class="lgbtq-quote-body" data-scrollreveal="enter bottom">
            <div class="lgbtq-quote-divider-container">
              <hr class="lgbtq-quote-divider" data-aos="line-grow" />
            </div>
            <div v-html="content[1].innerHTML + content[0].innerHTML"></div>
            <div class="lgbtq-quote-divider-container">
              <hr class="lgbtq-quote-divider" data-aos="line-grow" />
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>
</template>

<script>
export default {
  name: 'l-quote',
  props: ['content', 'attrs'],
  data() {
    return {
      mediaData: false,
      methods: {
        getMediaUrl: (source, tag) => {
          let htmlObject = document.createElement('div');
          htmlObject.innerHTML = source;
          return htmlObject.getElementsByTagName(`${tag}`)[0].src;
        },
        setMediaData: () => {
          let _this = this;

          _this.mediaData = {
            image_desktop: _this.methods.getMediaUrl(
              _this.content[2].innerBlocks[0].innerContent[0],
              'img',
            ),
            image_mobile: _this.methods.getMediaUrl(
              _this.content[3].innerBlocks[0].innerContent[0],
              'img',
            ),
          };
        },
      },
    };
  },
  created() {
    let _this = this;
    _this.methods.setMediaData();
  },
  mounted() {},
};
</script>

<style lang="scss">
@import 'lgbtq-quote.scss';
</style>
