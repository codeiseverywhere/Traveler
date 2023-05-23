<template>
  <section class="lgbtq-icon-and-list-heading">
    <div class="lgbtq-icon-and-list__background">
      <picture>
        <source media="(max-width: 799px)" :srcset="mediaData.image_mobile" />
        <source media="(min-width: 800px)" :srcset="mediaData.image_desktop" />
        <img
          v-if="mediaData.image_desktop"
          :src="mediaData.image_desktop"
          alt=""
          class="lgbtq-background-image"
        />
      </picture>
    </div>
    <div class="lgbtq-icon-and-list__inner lgbtq-section-inner">
      <div class="lgbtq-icon-and-list__header">
        <div
          data-scrollreveal="enter bottom"
          v-html="content[0].innerHTML"
        ></div>
      </div>
    </div>

    <div class="lgbtq-icon-and-list__inner lgbtq-section-inner">
      <div class="lgbtq-icon-and-list__container">
        <div class="lgbtq-icon-and-list__list-container">
          <div
            class="lgbtq-icon-and-list__list-item"
            data-scrollreveal="enter bottom"
          >
            <div class="lgbtq-icon-and-list__list-item-title">
              <img
                v-if="mediaData.image_icon"
                :src="mediaData.image_icon"
                alt=""
              />
              <div class="lgbtq-eyebrow" v-html="content[4].innerHTML"></div>
            </div>
            <div class="rich-text">
              <div v-html="content[5].innerHTML"></div>
              <div class="lgbtq-body-copy" v-html="content[6].innerHTML"></div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>
</template>

<script>
export default {
  name: 'l-icon-list-heading',
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
              _this.content[1].innerBlocks[0].innerContent[0],
              'img',
            ),
            image_mobile: _this.methods.getMediaUrl(
              _this.content[2].innerBlocks[0].innerContent[0],
              'img',
            ),
            image_icon: _this.methods.getMediaUrl(
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
};
</script>
