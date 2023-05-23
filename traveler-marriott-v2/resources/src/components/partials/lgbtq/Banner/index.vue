<template>
  <section class="lgbtq-hero-banner">
    <div class="lgbtq-video-container">
      <video
        autoplay
        class="lgbtq-video video-swap"
        :data-small="mediaData.video_mobile"
        :data-medium="mediaData.video_desktop"
        :data-large="mediaData.video_desktop"
        muted
        loop
        playsinline
      >
        <source :src="mediaData.video_desktop" type="video/mp4" />
      </video>
      <div class="lgbtq-section-inner">
        <div class="lgbtq-hero-content">
          <div
            class="lgbtq-hero-content-heading"
            data-scrollreveal="enter bottom"
            v-html="content[1].innerHTML + content[0].innerHTML"
          ></div>
          <div
            class="lgbtq-hero-content-body"
            data-scrollreveal="enter bottom"
            v-html="content[2].innerHTML + content[3].innerHTML"
          ></div>
        </div>
      </div>
    </div>
  </section>
</template>

<script>
export default {
  name: 'l-banner',
  props: ['content'],
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
              _this.content[4].innerBlocks[0].innerContent[0],
              'img',
            ),
            image_mobile: _this.methods.getMediaUrl(
              _this.content[5].innerBlocks[0].innerContent[0],
              'img',
            ),
            video_desktop: _this.methods.getMediaUrl(
              _this.content[6].innerBlocks[0].innerContent[0],
              'video',
            ),
            video_mobile: _this.methods.getMediaUrl(
              _this.content[7].innerBlocks[0].innerContent[0],
              'video',
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
@import 'block-hero-banner.scss';
</style>
