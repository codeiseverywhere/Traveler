<template>
  <section class="lgbtq-full-video">
    <div
      :class="'lgbtq-' + attrs.className"
      class="lgbtq-video-container lgbtq-linear-gradient-top lgbtq-linear-gradient-bottom"
    >
      <video
        autoplay
        class="lgbtq-video video-swap"
        :data-small="mediaData.video_mobile"
        :data-medium="mediaData.video_desktop"
        :data-large="mediaData.video_desktop"
        loop
        muted
        playsinline
        data-scrollreveal="enter bottom"
      >
        <source :src="mediaData.video_desktop" type="video/mp4" />
      </video>
    </div>
    <div class="lgbtq-full-video-caption" v-html="content[0].innerHTML"></div>
  </section>
</template>

<script>
export default {
  name: 'l-full-video',
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
            video_desktop: _this.methods.getMediaUrl(
              _this.content[1].innerBlocks[0].innerContent[0],
              'video',
            ),
            video_mobile: _this.methods.getMediaUrl(
              _this.content[2].innerBlocks[0].innerContent[0],
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
@import 'lgbtq-block-full-video.scss';
</style>
