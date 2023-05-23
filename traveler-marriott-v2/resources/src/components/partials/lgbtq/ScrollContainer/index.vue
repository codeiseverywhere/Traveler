<template>
  <div>
    <nav class="lgbtq-navlink">
      <div class="lgbtq-navlink__desktop" data-reveal-animation="fade">
        <ul>
          <li
            class="lgbtq-navlink__desktop-nav-items"
            v-for="(item, index) in blocks"
            v-if="item.blockName == 'traveler-lgbtq/divider'"
          >
            <button
              class="lgbtq-navlink-nav-title"
              v-html="
                item.innerBlocks[0].innerHTML
                  .replace(/<\/?[^>]+(>|$)/g, '')
                  .trim()
              "
              v-on:click="scrollTo"
            ></button>
            <div class="lgbtq-navlink__scroll-progress-bar-container">
              <div class="lgbtq-navlink__scroll-progress-bar">
                <span ref="progress_bar"></span>
              </div>
            </div>
          </li>
        </ul>
      </div>
      <div class="lgbtq-navlink__mobile" data-reveal-animation="fade">
        <button id="lgbtq-navlink__mobile-button" v-on:click="toggleDrawer">
          {{ activeSection }}
        </button>
        <div class="lgbtq-navlink__drawer" v-show="mobile_drawer">
          <ul>
            <li
              class="lgbtq-navlink__mobile-nav-item "
              v-for="(item, index) in blocks"
              v-if="
                item.blockName == 'traveler-lgbtq/divider' &&
                  activeSection !=
                    item.innerBlocks[0].innerHTML
                      .replace(/<\/?[^>]+(>|$)/g, '')
                      .toUpperCase()
                      .trim()
              "
              :data-title="
                item.innerBlocks[0].innerHTML
                  .replace(/<\/?[^>]+(>|$)/g, '')
                  .trim()
              "
            >
              <button
                class="lgbtq-navlink-nav-title"
                v-html="
                  item.innerBlocks[0].innerHTML
                    .replace(/<\/?[^>]+(>|$)/g, '')
                    .trim()
                "
                v-on:click="scrollTo"
              ></button>
              <div class="lgbtq-navlink__scroll-progress-bar-container">
                <div class="lgbtq-navlink__scroll-progress-bar">
                  <span ref="progress_bar_mobile"></span>
                </div>
              </div>
            </li>
          </ul>
        </div>
      </div>
    </nav>

    <div v-for="(item, index) in blocks" v-if="item.blockName !== null">
      <l-rich-text
        v-if="item.blockName === 'traveler-lgbtq/rich-text'"
        :attrs="item.attrs"
        :content="item.innerBlocks"
      ></l-rich-text>
      <l-quote
        v-if="item.blockName === 'traveler-lgbtq/quote'"
        :content="item.innerBlocks"
        :attrs="item.attrs"
      ></l-quote>
      <l-full-video
        v-if="item.blockName === 'traveler-lgbtq/full-video'"
        :content="item.innerBlocks"
        :attrs="item.attrs"
      ></l-full-video>
      <l-full-image
        v-if="item.blockName === 'traveler-lgbtq/full-image'"
        :content="item.innerBlocks"
        :attrs="item.attrs"
      ></l-full-image>
      <lgbtq-divider
        v-if="item.blockName === 'traveler-lgbtq/divider'"
        :content="item.innerBlocks"
        ref="divider"
      ></lgbtq-divider>
      <l-icon-list-parent
        :blocks="blocks"
        v-if="item.blockName === 'traveler-lgbtq/icon-list'"
      ></l-icon-list-parent>
    </div>
  </div>
</template>

<script>
const FullVideo = () =>
  import(
    /* webpackChunkName: "/chunk/partials/lgbtq/FullVideo" */ '../FullVideo'
  );
const RichText = () =>
  import(
    /* webpackChunkName: "/chunk/partials/lgbtq/RichText" */ '../RichText'
  );
const Quote = () =>
  import(/* webpackChunkName: "/chunk/partials/lgbtq/Quote" */ '../Quote');

const FullImage = () =>
  import(
    /* webpackChunkName: "/chunk/partials/lgbtq/FullImage" */ '../FullImage'
  );

const Divider = () =>
  import(/* webpackChunkName: "/chunk/partials/lgbtq/Divider" */ '../Divider');

const IconList = () =>
  import(
    /* webpackChunkName: "/chunk/partials/lgbtq/IconList" */ '../IconList'
  );
import _ from 'lodash';

export default {
  name: 'l-scroll-container',
  props: ['blocks'],
  components: {
    'l-full-video': FullVideo,
    'l-rich-text': RichText,
    'l-quote': Quote,
    'l-full-image': FullImage,
    'lgbtq-divider': Divider,
    'l-icon-list-parent': IconList,
  },
  data() {
    return {
      mobile_drawer: false,
      activeSection: '',
      isScrolling: false,
      toggleDrawer: () => (this.mobile_drawer = !this.mobile_drawer),
      sanitizeID: (data) => {
        return data.replace(/ /g, '_').toLowerCase();
      },
      scrollTo: (e) => {
        let _this = this;
        let id = _this.sanitizeID(e.target.textContent);

        document.querySelector(`[data-id='${id}']`).scrollIntoView({
          behavior: 'smooth',
        });
      },
      getOffset: (element) => {
        var rect = element.getBoundingClientRect();
        return {
          left: rect.left + window.scrollX,
          top: rect.top + window.scrollY,
        };
      },
      inViewport: (element, element2) => {
        let bb = element.getBoundingClientRect();
        let bb2 = element2.getBoundingClientRect();

        return bb.top < 50 && bb2.top > 50;
      },
      handleProgressBar: (bbx, elemHeight, i, progressBars) => {},
      is_scrolling: () => {
        return (
          window.lastScrollTime &&
          new Date().getTime() < window.lastScrollTime + 500
        );
      },
    };
  },
  created() {},
  mounted() {
    let _this = this;
    let activeDelayedSection = '';

    setTimeout(() => {
      _this.activeSection = _this.$refs.divider[0].$el.dataset.id
        .replace(/_/g, ' ')
        .trim()
        .toUpperCase();

      $(window).scroll(
        _.throttle(() => {
          window.lastScrollTime = new Date().getTime();

          for (let i = 0; i < _this.$refs.divider.length; i++) {
            if (i < _this.$refs.divider.length - 1) {
              let bbx = _this.$refs.divider[i + 1].$el.getBoundingClientRect();
              let height =
                _this.getOffset(_this.$refs.divider[i + 1].$el).top -
                _this.getOffset(_this.$refs.divider[i].$el).top;

              if (
                _this.inViewport(
                  _this.$refs.divider[i].$el,
                  _this.$refs.divider[i + 1].$el,
                )
              ) {
                let progress = Math.round((bbx.top / height) * 100);

                setTimeout(() => {
                  if (_this.is_scrolling()) {
                    _this.activeSection = _this.$refs.divider[i].$el.dataset.id
                      .replace(/_/g, ' ')
                      .trim()
                      .toUpperCase();
                  }
                }, 800);
                for (let n = 0; n < _this.$refs.progress_bar.length; n++) {
                  if (n < i) {
                    $(_this.$refs.progress_bar[n]).width(0);
                  } else if (n > i) {
                    $(_this.$refs.progress_bar[n]).width('100%');
                  } else {
                    $(_this.$refs.progress_bar[n]).width(progress + '%');
                  }
                }
              }
            } else if (i == _this.$refs.divider.length - 1) {
              let postEl = document.querySelector('section.single');
              let bbx = postEl.getBoundingClientRect();

              let height =
                postEl.getBoundingClientRect().bottom +
                window.scrollY -
                _this.getOffset(_this.$refs.divider[i].$el).top;

              if (
                document.documentElement.scrollTop >=
                _this.getOffset(_this.$refs.divider[i].$el).top
              ) {
                let progress = Math.round((bbx.bottom / height) * 100);

                setTimeout(() => {
                  if (_this.is_scrolling()) {
                    _this.activeSection = _this.$refs.divider[i].$el.dataset.id
                      .replace(/_/g, ' ')
                      .trim()
                      .toUpperCase();
                  }
                }, 800);

                for (let n = 0; n < _this.$refs.progress_bar.length; n++) {
                  if (n < i) {
                    $(_this.$refs.progress_bar[n]).width(0);
                  } else if (n > i) {
                    $(_this.$refs.progress_bar[n]).width('100%');
                  } else {
                    $(_this.$refs.progress_bar[n]).width(progress + '%');
                  }
                }
              }
            }
          }
        }, 55),
      );
    }, 1000);
  },
};
</script>
