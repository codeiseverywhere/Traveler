<template>
  <div class="reveal revealSeach" id="searchModal" data-reveal>
    <form :action="siteData.links.search" method="GET">
      <label>
        <input
          class="search"
          type="text"
          name="s"
          :placeholder="siteData.i18n.searchPlaceholder"
        />
      </label>
      <button class="submit" type="submit">
        <img
          class="search-img"
          title="Search"
          alt="Search"
          src="/wp-content/themes/traveler-marriott-v2/public/images/search.svg"
        />
      </button>
    </form>
  </div>
</template>

<script>
export default {
  name: 'wpsearchbox',
  data() {
    return {
      siteData: window.siteData,
    };
  },
};
</script>
