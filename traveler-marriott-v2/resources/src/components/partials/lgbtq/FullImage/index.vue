<template>
  <section class="lgbtq-full-image" :class="'lgbtq-' + attrs.className">
    <div
      class="lgbtq-full-image-desktop lgbtq-linear-gradient-top lgbtq-linear-gradient-bottom"
      v-bind:style="{ background: 'url(' + mediaData.image_desktop + ')' }"
    >
      <!-- THIS IS FOR IMAGE FULL WIDE DESKTOP OPTION -->
      <div class="lgbtq-full-image-wide" v-if="mediaData.image_wide_desktop">
        <img
          :src="mediaData.image_wide_desktop"
          alt=""
          class="lgbtq-image"
          data-scrollreveal="enter bottom"
        />
      </div>
      <!-- THIS IS FOR IMAGE 8 COLUMN WIDE DESKTOP OPTION -->
      <div class="lgbtq-full-image-half" v-if="mediaData.image_half_desktop">
        <img
          :src="mediaData.image_half_desktop"
          alt=""
          class="lgbtq-image"
          data-scrollreveal="enter bottom"
        />
      </div>
    </div>
    <div
      class="lgbtq-full-image-mobile lgbtq-linear-gradient-top lgbtq-linear-gradient-bottom"
      v-bind:style="{ background: 'url(' + mediaData.image_mobile + ')' }"
    >
      <!-- THIS IS FOR IMAGE FULL WIDE MOBILE OPTION -->
      <div class="lgbtq-full-image-wide" v-if="mediaData.image_wide_mobile">
        <img
          :src="mediaData.image_wide_mobile"
          alt=""
          class="lgbtq-image"
          data-scrollreveal="enter bottom"
        />
      </div>
      <!-- THIS IS FOR IMAGE 8 COLUMN WIDE MOBILE OPTION -->
      <div class="lgbtq-full-image-half" v-if="mediaData.image_half_mobile">
        <img
          :src="mediaData.image_half_mobile"
          alt=""
          class="lgbtq-image"
          data-scrollreveal="enter bottom"
        />
      </div>
    </div>
    <div class="lgbtq-full-image-caption" v-html="content[0].innerHTML"></div>
  </section>
</template>

<script>
export default {
  name: 'l-full-image',
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
              _this.content[1].innerBlocks[0].innerContent[0],
              'img',
            ),
            image_mobile: _this.methods.getMediaUrl(
              _this.content[2].innerBlocks[0].innerContent[0],
              'img',
            ),
            image_wide_desktop: _this.methods.getMediaUrl(
              _this.content[3].innerBlocks[0].innerContent[0],
              'img',
            ),
            image_wide_mobile: _this.methods.getMediaUrl(
              _this.content[4].innerBlocks[0].innerContent[0],
              'img',
            ),
            image_half_desktop: _this.methods.getMediaUrl(
              _this.content[5].innerBlocks[0].innerContent[0],
              'img',
            ),
            image_half_mobile: _this.methods.getMediaUrl(
              _this.content[6].innerBlocks[0].innerContent[0],
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
};
</script>

<style lang="scss">
@import 'lgbtq-full-image.scss';
</style>
