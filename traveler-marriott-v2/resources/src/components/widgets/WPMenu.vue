<template>
  <div
    v-if="loaded === true"
    class="top-bar"
    id="main-animated-menu"
    data-animate="fade-in fade-out"
  >
    <ul
      id="dropdown-menu"
      class="dropdown menu bottom-menu align-right align-bottom"
      data-dropdown-menu
      data-alignment="right"
    >
      <template v-for="(menuItem, index) in menu">
        <li
          :key="index"
          @mouseenter="showSubMenu"
          @mouseleave="hideSubMenu"
          @click="toggleSubMenu"
          role="menuitem"
          class="top-menu-item"
        >
          <template v-if="menuItem.url === '#'">
            <a class="text-uppercase">{{ menuItem.title }}</a>
          </template>
          <template v-else>
            <a class="text-uppercase" :href="menuItem.url">
              {{ menuItem.title }}
            </a>
          </template>
          <ul v-if="menuItem.children.length > 0">
            <li>
              <div class="grid-x no-bullet sub-menu">
                <div
                  v-for="childMenuItem in menuItem.children"
                  :key="childMenuItem.id"
                  :class="
                    'cell large-auto sub-menu-item ' + childMenuItem.classes
                  "
                >
                  <a
                    :href="childMenuItem.url"
                    class="sub-menu-heading text-uppercase"
                  >
                    {{ childMenuItem.title }}
                  </a>
                  <ul
                    v-if="childMenuItem.children.length > 0"
                    class="sub-menu-items no-bullet"
                  >
                    <li
                      v-for="grandchildMenuItem in childMenuItem.children"
                      :key="grandchildMenuItem.id"
                    >
                      <a :href="grandchildMenuItem.url">
                        {{ grandchildMenuItem.title }}
                      </a>
                    </li>
                  </ul>
                </div>
              </div>
            </li>
          </ul>
        </li>
      </template>
      <li class="show-for-large" v-if="menuName === 'primary'">
        <button @click="showSearchBox" class="">
          <img
            class="search-img"
            title="Search"
            alt="Search"
            :src="searchBtn"
          />
        </button>
      </li>
      <li
        @click="toggleSubMenu"
        role="menuitem"
        class="show-for-small-only top-menu-item"
        v-if="menuName === 'primary'"
      >
        <a class="text-uppercase">
          {{ siteData.i18n.magazines }}
        </a>
        <ul id="magazines">
          <li v-for="(magazine, index) in magazines" :key="index">
            <a
              :href="magazine.url"
              target="_blank"
              rel="nofollow noopener external"
            >
              <img loading="lazy" :src="magazine.src" :alt="magazine.name" />
            </a>
          </li>
        </ul>
      </li>
    </ul>
  </div>
</template>

<script>
export default {
  name: 'wpmenu',
  data() {
    return {
      errors: [],
      loaded: false,
      searchBtn: '',
      siteData: window.siteData,
      magazines: window.magazines,
      origin: window.location.origin,
    };
  },

  computed: {
    menuName: function() {
      return this.$parent.menuName;
    },
    menu: function() {
      return this.$parent.mainMenu;
    },
  },

  methods: {
    isTouchSupported: function() {
      let prefixes = ' -webkit- -moz- -o- -ms- '.split(' ');
      let mq = function(query) {
        return window.matchMedia(query).matches;
      };

      if (
        'ontouchstart' in window ||
        (window.DocumentTouch && document instanceof DocumentTouch)
      ) {
        return true;
      }

      // include the 'heartz' as a way to have a non matching MQ to help terminate the join
      // https://git.io/vznFH
      let query = ['(', prefixes.join('touch-enabled),('), 'heartz', ')'].join(
        '',
      );
      return mq(query);
    },
    showSearchBox: function() {
      window.searchModal.open();
    },
    hasChildren: function() {
      Array.from(document.querySelectorAll('.top-menu-item')).forEach((el) => {
        if (1 < el.children.length) {
          el.classList.add('is-dropdown-submenu-parent');
        } else {
          el.classList.remove('is-dropdown-submenu-parent');
        }
      });
    },
    toggleSubMenu: function(e) {
      if (true === this.isTouchSupported() && '' === e.target.href) {
        e.preventDefault();
        e.currentTarget.classList.toggle('is-active');
      }
    },
    showSubMenu: function(e) {
      if (true !== this.isTouchSupported()) {
        e.target.classList.add('is-active');
      }
    },
    hideSubMenu: function(e) {
      if (true !== this.isTouchSupported()) {
        e.target.classList.remove('is-active');
      }
    },
  },

  watch: {
    $route() {
      let _this = this;
      _this.loaded = false;
      setTimeout(() => {
        _this.loaded = true;
      }, 200);
    },
    loaded(newVal) {
      if (newVal === true) {
        this.$route.matched.some((record) => {
          switch (record.meta.view) {
            case 'PostACF':
            case 'PageHub':
            case 'PostBlock':
              this.searchBtn =
                '/wp-content/themes/traveler-marriott-v2/public/images/search-white.svg';
              break;
            default:
              window.MenuView.mainMenu = 'primary';
              this.searchBtn =
                '/wp-content/themes/traveler-marriott-v2/public/images/search.svg';
          }
        });

        Array.from(document.querySelectorAll('.top-menu-item')).forEach((el) =>
          el.classList.remove('is-dropdown-submenu-parent', 'is-active'),
        );

        setTimeout(() => this.hasChildren(), 500);
      }
    },
  },

  created() {
    window.EventBus.listen('menuChanged', () => {
      setTimeout(() => this.hasChildren(), 500);
    });
  },

  mounted() {
    setTimeout(() => {
      this.loaded = true;
    }, 200);
  },
};
</script>
