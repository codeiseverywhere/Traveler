import { isMobile, bodyTag } from './utils';
import ScrollReveal from '../../assets/js/animate-on-reveal';
import VideoSwap from '../../assets/js/videoswap';

export default {
  name: 'LGBTQ_scripts',
  watch: {
    $route() {
      let _this = this;
      _this.loaded = false;
    },
  },
  mounted() {
    setTimeout(() => {
      if (isMobile) {
        bodyTag.classList.add('iOS');
        $('.lgbtq-full-image .lgbtq-full-image-desktop').css(
          'background-attachment',
          'initial!important',
        );
        $('.lgbtq-full-image .lgbtq-full-image-mobile').css(
          'background-attachment',
          'initial!important',
        );
      }

      new ScrollReveal({
        viewportFactor: 0.22,
        reset: false,
        init: true,
      });

      $('.video-swap').each((index, element) => {
        new VideoSwap(element);
      });

      //hero-video
      const $video = $('.lgbtq-video');
      let lastScrollTop = 0,
        delta = 15;
      $(window).on('scroll', function() {
        if ($video.length) {
          const scroll = $(this).scrollTop();
          if (Math.abs(lastScrollTop - scroll) <= delta) {
            return;
          }
          if (scroll > lastScrollTop && scroll < 100) {
            // downscroll code
            $video[0].play();
            $video.addClass('play');
          } else {
            // upscroll code
            //$video[0].pause();
            $video.removeClass('play');
          }
          lastScrollTop = scroll;
          if (scroll === lastScrollTop && scroll < delta) {
            $video[0].pause();
            $video.removeClass('play');
          }
        }
      });
    }, 700);
  },
  methods: {}, //any methods you want go here
};
