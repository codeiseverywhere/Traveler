<template>
  <section class="search-archive">
    <vue-headful
      :title="siteData.i18n.searchResults + ' ' + name"
      :head="{ 'meta[name=robots]': { content: 'noindex,nofollow' } }"
    />
    <div class="grid-container">
      <header class="term-title" v-if="posts.length">
        <h1 class="text-center">
          {{ siteData.i18n.searchResults }}&nbsp;&lsquo;{{ name }}&rsquo;
        </h1>
      </header>
      <div class="category-posts category-list" v-if="posts.length">
        <m-search-list
          v-for="child in posts"
          :key="child.ID"
          :content="child"
        />
      </div>
    </div>
  </section>
</template>

<script>
const SearchList = () =>
  import(
    /* webpackChunkName: "/chunk/widgets/SearchList" */ '../widgets/SearchList'
  );

export default {
  name: 'Search',
  data() {
    return {
      posts: {},
      name: '',
      loaded: false,
      siteData: window.siteData,
      methods: {
        getData: (query, callback) => {
          this.name = query;
          window.axios
            .get(
              window.siteData.rest_base_page +
                'wp/v2/relevanssi_search?keyword=' +
                query,
            )
            .then((response) => {
              // JSON responses are automatically parsed.
              callback(response.data);
            })
            .catch((e) => {
              console.log(e);
            });
        },
      },
    };
  },

  components: {
    'm-search-list': SearchList,
  },

  created() {
    let _this, query;
    _this = this;
    query = _this.$route.query.s
      ? _this.$route.query.s
      : _this.$route.params.pathMatch;
    if (query) {
      this.methods.getData(query, (data) => {
        _this.posts = data;
        _this.loaded = true;
      });
    }
  },
};
</script>

<style lang="scss">
@import '../../../assets/sass/home';
@import '../../../assets/sass/category';
</style>
