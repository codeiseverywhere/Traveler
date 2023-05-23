<template>
  <div
    :id="'podcast' + podcastKey"
    class="podcast-container"
    :class="{
      'show-all': showAllPodcasts,
      'wp-playlist': showAllPodcasts,
      'wp-audio-playlist': showAllPodcasts,
      'wp-playlist-light': showAllPodcasts,
      [`${classPageName}-podcast`]: true,
    }"
    ref="podcast-container"
  >
    <template v-if="showAllPodcasts">
      <div v-if="currentTrack" class="wp-playlist-current-item">
        <div class="wp-playlist-description text-center">
          <h2 v-html="currentTrack.meta.album" />
          <p v-html="currentTrack.meta.summary" />
          <ul class="no-bullet">
            <li v-if="currentTrack.meta.itunes">
              <a
                :href="currentTrack.meta.itunes"
                target="_blank"
                rel="noopener external"
                >Apple Podcasts</a
              >
            </li>
            <li v-if="currentTrack.meta.google">
              <a
                :href="currentTrack.meta.google"
                target="_blank"
                rel="noopener external"
                >Google Podcasts</a
              >
            </li>
            <li v-if="currentTrack.meta.spotify">
              <a
                :href="currentTrack.meta.spotify"
                target="_blank"
                rel="noopener external"
                >Spotify</a
              >
            </li>
          </ul>
        </div>
        <div class="wp-playlist-meta-wrapper">
          <div class="wp-playlist-image">
            <img
              v-if="currentTrack.thumb"
              loading="lazy"
              :src="currentTrack.thumb.src"
              alt=""
              width="150"
              height="150"
            />
          </div>
          <div class="wp-playlist-caption text-left">
            <span
              v-if="currentTrack.meta.album"
              class="wp-playlist-item-meta wp-playlist-item-album"
              v-html="currentTrack.meta.album"
            />
            <span
              class="wp-playlist-item-meta wp-playlist-item-title"
              v-html="currentTrack.title"
            />
            <span
              v-if="currentTrack.meta.artist"
              class="wp-playlist-item-meta wp-playlist-item-artist"
              v-html="currentTrack.meta.artist"
            />
          </div>
        </div>
      </div>

      <audio
        class="podcast-player"
        width="100%"
        controls="controls"
        preload="none"
      >
        <source :type="currentTrack.type" :src="currentTrack.src" />
      </audio>

      <div class="wp-playlist-tracks">
        <div
          v-for="(track, trackKey) in podcast.tracks"
          :key="`${trackKey}-${currentTrack.title}`"
          class="wp-playlist-item"
          :class="{ 'wp-playlist-playing': track === currentTrack }"
        >
          <a
            class="wp-playlist-caption"
            :href="track.src"
            @click.prevent="onTrackClick(track, podcast)"
          >
            <span class="wp-playlist-item-title" v-html="track.title" />
            <span
              v-if="track.description"
              class="wp-playlist-item-description"
              v-html="track.description"
            />
          </a>
          <div
            v-if="track.meta.length_formatted"
            class="wp-playlist-item-length"
            v-html="track.meta.length_formatted"
          ></div>
        </div>
      </div>
    </template>
    <template v-else-if="podcast.tracks.length > 0">
      <a :href="'/podcasts/#podcast' + podcastKey">
        <img
          loading="lazy"
          :data-src="podcast.tracks[0].image.src"
          :alt="podcast.tracks[0].meta.album"
          class="native-lazyload-js-fallback"
        />
      </a>
      <div class="clearfix">
        <audio
          class="podcast-player"
          width="100%"
          controls="controls"
          preload="none"
        >
          <source :type="podcast.tracks[0].type" :src="podcast.tracks[0].src" />
        </audio>
        <h3
          class="post-title--alt podcasts-widget__podcast__title"
          :data-name="podcast.tracks[0].meta.album"
        >
          <a
            :href="'/podcasts/#podcast' + podcastKey"
            v-html="podcast.tracks[0].title"
          />
        </h3>
      </div>
    </template>
  </div>
</template>

<script>
export default {
  name: 'm-podcast',
  props: {
    analyticsPageName: {
      default: '',
      required: false,
      type: String,
    },
    classPageName: {
      default: '',
      required: false,
      type: String,
    },
    podcast: {
      required: true,
      type: Object,
    },
    podcastKey: {
      required: true,
      type: Number,
    },
    showAllPodcasts: {
      default: false,
      required: false,
      type: Boolean,
    },
  },
  data() {
    return {
      siteData: window.siteData,
      oldPodcastID: false,
      oldPodcastName: false,
      oldPausedTime: false,
      pausedTime: false,
      startTime: false,
      mediaElementPlayer: null,
      currentTrack: null,
      methods: {
        pad2: (number) => {
          return (number < 10 ? '0' : '') + number;
        },
        parseTime: (timeDiff, callback) => {
          let seconds, minutes, hours;

          // strip the milliseconds
          timeDiff /= 1000;

          // get seconds
          seconds = this.methods.pad2(Math.round(timeDiff % 60));

          // remove seconds from the date
          timeDiff = Math.floor(timeDiff / 60);

          // get minutes
          minutes = this.methods.pad2(Math.round(timeDiff % 60));

          // remove minutes from the date
          timeDiff = Math.floor(timeDiff / 60);

          // get hours
          hours = this.methods.pad2(Math.round(timeDiff % 24));

          //return time in a 00:00:00 format
          callback(hours + ':' + minutes + ':' + seconds);
        },
        pushToGTM: (eventCategory, eventAction, eventLabel) => {
          if ({}.hasOwnProperty.call(window, 'GTMdataLayer')) {
            window.GTMdataLayer.push({
              event: 'trackEvent',
              eventCategory: eventCategory,
              eventAction: eventAction,
              eventLabel: eventLabel,
            });
          }
        },
        mejsPlay: (index, player, _this) => {
          let timeDiff;
          player[index].media.addEventListener('play', (el) => {
            _this.podcastName = $(el.detail.target)
              .parents('.podcasts-widget__podcast')
              .find('.podcasts-widget__podcast__title')
              .data('name');
            _this.podcastEpisode = $(el.detail.target)
              .parents('.podcasts-widget__podcast')
              .find('.podcasts-widget__podcast__title')
              .text();

            _this.methods.pushToGTM(
              _this.podcastName,
              `${
                this.analyticsPageName ? this.analyticsPageName + ' ' : ''
              }play`,
              _this.podcastEpisode,
            );

            if (false === _this.oldPodcastID) {
              _this.startTime = new Date().getTime();
              _this.oldPodcastName = _this.podcastName;
              _this.oldPodcastID = _this.podcastEpisode;
            }

            if (false !== _this.pausedTime) {
              _this.pausedEndTime = new Date().getTime();
              _this.pausedTimeDiff = _this.pausedEndTime - _this.pausedTime;

              if (false === _this.oldPausedTime) {
                _this.oldPausedTime = _this.pausedTimeDiff;
              }
            }

            if (_this.podcastEpisode !== _this.oldPodcastID) {
              _this.endTime = new Date().getTime();
              timeDiff = _this.endTime - _this.startTime;

              if (false !== _this.pausedTime) {
                _this.pausedEndTime = new Date().getTime();
                _this.pausedTimeDiff = _this.pausedEndTime - _this.pausedTime;

                if (false !== _this.oldPausedTime) {
                  _this.pausedTimeDiff += _this.oldPausedTime;
                }

                timeDiff -= _this.pausedTimeDiff;

                _this.methods.parseTime(_this.pausedTimeDiff, (time) => {
                  _this.methods.pushToGTM(
                    _this.oldPodcastName,
                    `${
                      this.analyticsPageName ? this.analyticsPageName + ' ' : ''
                    }playData`,
                    _this.oldPodcastID + ' was paused for: ' + time,
                  );
                });
              }

              _this.methods.parseTime(timeDiff, (time) => {
                _this.methods.pushToGTM(
                  _this.oldPodcastName,
                  `${
                    this.analyticsPageName ? this.analyticsPageName + ' ' : ''
                  }playData`,
                  _this.oldPodcastID + ' was played for: ' + time,
                );
              });

              _this.oldPodcastName = _this.podcastName;
              _this.oldPodcastID = _this.podcastEpisode;
              _this.startTime = new Date().getTime();
            }
          });
        },
        mejsPause: (index, player, _this) => {
          player[index].media.addEventListener('pause', () => {
            _this.pausedTime = new Date().getTime();
            _this.methods.pushToGTM(
              _this.podcastName,
              `${
                this.analyticsPageName ? this.analyticsPageName + ' ' : ''
              }paused`,
              _this.podcastEpisode,
            );
          });
        },
        mejsEnd: (index, player, _this) => {
          let timeDiff;
          player[index].media.addEventListener('ended', () => {
            _this.endTime = new Date().getTime();

            timeDiff = _this.endTime - _this.startTime;

            if (false !== _this.pausedTime) {
              timeDiff -= _this.pausedEndTime - _this.pausedTime;
            }

            _this.methods.parseTime(timeDiff, (time) => {
              _this.methods.pushToGTM(
                _this.podcastName,
                `${
                  this.analyticsPageName ? this.analyticsPageName + ' ' : ''
                }playData`,
                _this.podcastEpisode + ' was played for: ' + time,
              );
            });

            _this.methods.pushToGTM(
              _this.podcastName,
              `${
                this.analyticsPageName ? this.analyticsPageName + ' ' : ''
              }completed`,
              _this.podcastEpisode,
            );
          });
        },
      },
    };
  },

  methods: {
    onTrackClick(track) {
      this.currentTrack = track;
      this.mediaElementPlayer.setSrc(track.src);
      this.mediaElementPlayer.play();
    },
  },

  watch: {
    loaded: (val, oldVal) => {},
  },

  created() {
    if (!this.currentTrack && this.podcast.tracks.length > 0) {
      this.currentTrack = this.podcast.tracks[0];
    }
  },

  mounted() {
    let _this = this;
    const features = ['playpause', 'current', 'progress'];
    if (this.showAllPodcasts) {
      features.push('duration');
      features.push('volume');
    }

    this.mediaElementPlayer = new MediaElementPlayer(
      this.$refs['podcast-container'].querySelector('.podcast-player'),
      {
        features,
        audioHeight: this.showAllPodcasts ? 40 : 30,
        success: (mediaElement, originalNode, instance) => {
          mediaElement.addEventListener('ended', () => {
            const currentTrackIndex = _this.podcast.tracks.indexOf(
              _this.currentTrack,
            );
            if (currentTrackIndex < _this.podcast.tracks.length - 1) {
              _this.currentTrack = _this.podcast.tracks[currentTrackIndex + 1];
              mediaElement.setSrc(_this.currentTrack.src);
              mediaElement.play();
            }
          });
        },
      },
    );

    /**
     * @todo: make this a method
     */
    setTimeout(() => {
      Object.keys(mejs.players).forEach((index) => {
        _this.methods.mejsPlay(index, mejs.players, _this);
        _this.methods.mejsPause(index, mejs.players, _this);
        _this.methods.mejsEnd(index, mejs.players, _this);
      });
    }, 500);
  },
};
</script>

<style lang="scss">
.mejs__time {
  font-weight: normal;
}

.mejs__horizontal-volume-total {
  background: rgba(255, 255, 255, 0.33);
  border-radius: 0;

  .mejs__horizontal-volume-current {
    background: rgba(255, 255, 255, 1);
    border-radius: 0;
  }
}
</style>
