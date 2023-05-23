const RETINA_MEDIA_QUERY = "(-webkit-min-device-pixel-ratio: 2), (min-device-pixel-ratio: 2), (min-resolution: 192dpi)";

class VideoSwap {
  constructor(element) {
    this.$element = $(element);
    this.smallSource = this.$element.attr("data-small");
    this.mediumSource = this.$element.attr("data-medium");
    this.largeSource = this.$element.attr("data-large");
    this.addListeners();
  }
  addListeners() {
    $(window).on("load resize", this.swapVideo.bind(this));
  }
  swapVideo() {
    const hasMediumSource =
      typeof this.mediumSource !== typeof undefined &&
      this.mediumSource !== false;
    const hasLargeSource =
      typeof this.largeSource !== typeof undefined &&
      this.largeSource !== false;
    const isRetina = matchMedia(RETINA_MEDIA_QUERY).matches;
    const width = $(window).width();
    if (width >= 1280 && hasLargeSource) {
      this.changeTo(this.largeSource);
    } else if (width >= 600 && hasMediumSource) {
      if (!isRetina && hasLargeSource) {
        this.changeTo(this.largeSource);
      } else {
        this.changeTo(this.mediumSource);
      }
    } else {
      if (!isRetina && hasMediumSource) {
        this.changeTo(this.mediumSource);
      } else {
        this.changeTo(this.smallSource);
      }
    }
  }
  changeTo(to) {
    const from = this.$element.attr("src");
    if (from !== to) {
      this.$element.attr("src", to);
    }
  }
}

export default VideoSwap;
