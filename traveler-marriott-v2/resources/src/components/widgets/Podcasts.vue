<template>
  <aside
    class="widget podcasts-widget"
    :class="{
      'show-all': showAllPodcasts,
      'text-center': !showAllPodcasts,
      [`${classPageName}-podcasts`]: true,
    }"
    ref="podcasts-container"
  >
    <strong v-if="!showAllPodcasts" class="text-uppercase widget-title"
      >Podcasts</strong
    >
    <m-podcast
      v-for="(podcast, key) in podcasts"
      :key="key"
      :analyticsPageName="analyticsPageName"
      :classPageName="classPageName"
      :podcast="podcast"
      :podcastKey="key"
      :showAllPodcasts="showAllPodcasts"
      :class="{ 'podcasts-widget__podcast': !showAllPodcasts }"
    />
    <div v-if="!showAllPodcasts">
      <strong class="more-podcasts category-label"
        ><a :href="siteData.current_site + 'podcasts/'"
          >Past Episodes&nbsp;&raquo;</a
        ></strong
      >
    </div>
  </aside>
</template>

<script>
const Podcast = () =>
  import(/* webpackChunkName: "/chunk/widgets/Podcast" */ '../widgets/Podcast');

export default {
  name: 'm-podcasts',
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
    showAllPodcasts: {
      default: false,
      required: false,
      type: Boolean,
    },
  },
  data() {
    return {
      podcasts: window.podcasts,
      siteData: window.siteData,
    };
  },
  components: {
    'm-podcast': Podcast,
  },
};
</script>

<style lang="scss">
.home .podcasts-widget .more-podcasts.category-label {
  font-size: 1rem;
}
</style>
