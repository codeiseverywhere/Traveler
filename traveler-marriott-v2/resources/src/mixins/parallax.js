import ScrollMagic from 'scrollmagic';
import { isMobile } from './utils';

export default {
  name: 'Parallax',
  data: () => ({
    controller: false,
  }),
  watch: {
    $route() {
      let _this = this;
      _this.loaded = false;
      _this.controller = _this.controller.destroy(true);
    },
  },
  mounted() {
    let _this = this;
    setTimeout(() => {
      // ScrollMagic init
      _this.controller = new ScrollMagic.Controller({
        globalSceneOptions: {
          triggerHook: 'onLeave',
        },
      });

      // Parallax images
      if (isMobile && this.$el.querySelectorAll('.has-parallax').length) {
        Array.from(this.$el.querySelectorAll('.has-parallax')).forEach(
          (cur_panel) => {
            if (cur_panel.querySelectorAll('img').length === 0) {
              const parallax_wrapper = document.createElement('div');
              parallax_wrapper.classList.add('parallax-wrapper');
              cur_panel.appendChild(parallax_wrapper);

              const parallax_bg = document.createElement('div');
              parallax_bg.classList.add('parallax-bg');
              parallax_bg.style.backgroundImage =
                cur_panel.style.backgroundImage;
              parallax_wrapper.appendChild(parallax_bg);

              cur_panel.classList.add('no-native-parallax');

              new ScrollMagic.Scene({
                triggerElement: parallax_wrapper,
                duration: '100%',
                offset: 0,
              })
                .setPin(parallax_bg)
                .addTo(_this.controller);
            }
          },
        );
      }
    }, 700);
  },
  methods: {}, //any methods you want go here
};
