<template>
  <section class="lgbtq-icon-and-list-items">
    <div class="lgbtq-icon-and-list__inner lgbtq-section-inner">
      <div class="lgbtq-icon-and-list__container">
        <div class="lgbtq-icon-and-list__list-container">
          <!-- ICON LIST ITEM -->
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
              <div class="lgbtq-eyebrow" v-html="content[1].innerHTML"></div>
            </div>
            <div class="rich-text">
              <!-- This will be the Classic Editor -->
              <div v-html="content[2].innerHTML"></div>
              <div class="lgbtq-body-copy" v-html="content[3].innerHTML"></div>
              <img
                v-if="mediaData.image_desktop"
                :src="mediaData.image_desktop"
                alt=""
              />
              <div class="lgbtq-body-copy" v-html="content[5].innerHTML"></div>
            </div>
          </div>
          <!-- END -->
        </div>
      </div>
    </div>
  </section>
</template>

<script>
export default {
  name: 'l-icon-list-items',
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
            image_icon: _this.methods.getMediaUrl(
              _this.content[0].innerBlocks[0].innerContent[0],
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
  mounted() {
    setTimeout(() => {
      let liEl = $('.lgbtq-body-copy ul li');
      liEl.each((i, el) => {
        if ($(el).text() === '') {
          $(el).hide();
        }
      });
    }, 800);
  },
};
</script>

<style lang="scss">
@import 'icon-list-items';
</style>
