<template>
  <section
    :class="[
      attrs.isLeftLayout ? 'lgbtq-rich-text-left' : 'lgbtq-rich-text-right',
    ]"
    style="background-color: #000000;"
  >
    <!--THIS IS FOR BACKGROUND COLOR OPTION -->
    <div
      :class="'lgbtq-' + attrs.className"
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
              ? 'lgbtq-rich-text-left-container'
              : 'lgbtq-rich-text-right-container',
          ]"
        >
          <div
            data-scrollreveal="enter bottom"
            :class="[
              'lgbtq-section-inner-heading',
              attrs.hasBorderTop ? 'lgbtq-border-yes' : 'lgbtq-border-no',
            ]"
            v-if="
              content[0].innerHTML
                .concat(content[1].innerHTML)
                .replace(/<[^>]*>?/gm, '')
                .trim().length > 0
            "
            v-html="content[0].innerHTML + content[1].innerHTML"
          ></div>
          <div
            data-scrollreveal="enter bottom"
            class="lgbtq-section-inner-body"
            v-if="
              content[2].innerHTML
                .concat(content[3].innerHTML)
                .replace(/<[^>]*>?/gm, '')
                .trim().length > 0
            "
            v-html="content[2].innerHTML + content[3].innerHTML"
          ></div>
        </div>
      </div>
    </div>
  </section>
</template>

<script>
export default {
  name: 'l-rich-text',
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
              _this.content[4].innerBlocks[0].innerContent[0],
              'img',
            ),
            image_mobile: _this.methods.getMediaUrl(
              _this.content[5].innerBlocks[0].innerContent[0],
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
@import 'lgbtq-rich-text.scss';
</style>
