import Router from 'vue-router';
import Vue from 'vue';

// Components
const Tag = () =>
  import(
    /* webpackChunkName: "/chunk/view/Tag" */ '../../components/views/Tag'
  );
const Home = () =>
  import(
    /* webpackChunkName: "/chunk/view/Home" */ '../../components/views/Home'
  );
const Author = () =>
  import(
    /* webpackChunkName: "/chunk/view/Author" */ '../../components/views/Author'
  );
const Podcasts = () =>
  import(
    /* webpackChunkName: "/chunk/view/Podcasts" */ '../../components/views/Podcasts'
  );
const Category = () =>
  import(
    /* webpackChunkName: "/chunk/view/Category" */ '../../components/views/Category'
  );
const NotFound = () =>
  import(
    /* webpackChunkName: "/chunk/view/NotFound" */ '../../components/views/NotFound'
  );
const PostPreview = () =>
  import(
    /* webpackChunkName: "/chunk/view/PostPreview" */ '../../components/views/PostPreview'
  );
const CategoryCity = () =>
  import(
    /* webpackChunkName: "/chunk/view/CategoryCity" */ '../../components/views/CategoryCity'
  );
const CategoryCityChild = () =>
  import(
    /* webpackChunkName: "/chunk/view/CategoryCityChild" */ '../../components/views/CategoryCityChild'
  );
const TasteOfPreview = () =>
  import(
    /* webpackChunkName: "/chunk/view/TasteOfPreview" */ '../../components/views/TasteOfPreview'
  );
const PageAltPreview = () =>
  import(
    /* webpackChunkName: "/chunk/view/PageAltPreview" */ '../../components/views/PageAltPreview'
  );
const CategoryChild = () =>
  import(
    /* webpackChunkName: "/chunk/view/CategoryChild" */ '../../components/views/CategoryChild'
  );
const Post = () =>
  import(
    /* webpackChunkName: "/chunk/view/Post" */ '../../components/views/Post'
  );
const PostBlock = () =>
  import(
    /* webpackChunkName: "/chunk/view/PostBlock" */ '../../components/views/PostBlock'
  );
const PostACF = () =>
  import(
    /* webpackChunkName: "/chunk/view/PostACF" */ '../../components/views/PostACF'
  );
const PostLGBTQ = () =>
  import(
    /* webpackChunkName: "/chunk/view/PostLGBTQ" */ '../../components/views/PostLGBTQ'
  );
const Page = () =>
  import(
    /* webpackChunkName: "/chunk/view/Page" */ '../../components/views/Page'
  );
const PageHub = () =>
  import(
    /* webpackChunkName: "/chunk/view/PageHub" */ '../../components/views/PageHub'
  );
const Search = () =>
  import(
    /* webpackChunkName: "/chunk/view/Search" */ '../../components/views/Search'
  );
const Headful = () =>
  import(/* webpackChunkName: "/chunk/vueHeadful" */ 'vue-headful');

Vue.use(Router);
Vue.component('vue-headful', Headful);

let routes, preparePathFromArray;

preparePathFromArray = function(_routes) {
  let output = [];
  if (Array.isArray(_routes)) {
    _routes.forEach(function(_route) {
      output.push(_route.slug);
    });
  }
  return output.join('|');
};

//If it's English
if ('1' === window.siteData.site_id) {
  routes = [
    {
      path: '/',
      name: 'Home',
      component: Home,
      meta: { view: 'Home' },
    },
    {
      path: '/search',
      name: 'Search',
      component: Search,
      meta: { view: 'Search' },
      props: true,
      children: [
        {
          path: ':pathMatch',
          name: 'SearchChild',
          component: Search,
          query: 's',
          props: true,
        },
      ],
    },
    {
      path: '/preview',
      alias: '/preview/:category/:pathMatch',
      component: PostPreview,
      meta: { view: 'Post' },
      props: true,
    },
    {
      path: '/preview-page',
      alias: '/preview-page/:pathMatch',
      component: PageAltPreview,
      meta: { view: 'PageHub' },
      props: true,
    },
    {
      path: '/preview-taste-of',
      alias: '/preview-taste-of/:pathMatch',
      component: TasteOfPreview,
      meta: { view: 'PostBlock' },
      props: true,
    },
    {
      path: '/preview-storybooked',
      alias: '/preview-storybooked/:pathMatch',
      component: TasteOfPreview,
      meta: { view: 'PostACF' },
      props: true,
    },
    {
      path: '/preview-storybooked',
      alias: '/preview-storybooked/:pathMatch',
      name: 'StorybookedPreview',
      component: TasteOfPreview,
      props: true,
    },
    {
      path: '/(about-us)',
      name: 'PageAbout',
      component: Page,
      meta: { view: 'PageAbout' },
      props: true,
    },
    {
      path: '/(podcasts)',
      name: 'Podcasts',
      component: Podcasts,
      meta: { view: 'Podcasts' },
      props: true,
    },
    {
      path: '/tag/:pathMatch',
      name: 'Tag',
      component: Tag,
      meta: { view: 'Tag' },
      props: true,
    },
    {
      path: '/author/:pathMatch',
      name: 'Author',
      component: Author,
      meta: { view: 'Author' },
      props: true,
    },
  ];

  if (typeof window.siteData.routes.storybooked !== 'undefined') {
    window.siteData.routes.storybooked.forEach(function(_route) {
      routes.push({
        path: '/storybooked/(' + _route.slug + ')',
        component: eval(_route.template),
        meta: { view: _route.template },
        props: {
          slug: _route.slug,
          id: _route.id,
          name: _route.name,
          type: _route.type,
        },
      });
    });
  }

  if (typeof window.siteData.routes.a_taste_of !== 'undefined') {
    window.siteData.routes.a_taste_of.forEach(function(_route) {
      routes.push({
        path: '/a-taste-of/(' + _route.slug + ')',
        component: eval(_route.template),
        meta: { view: _route.template },
        props: {
          slug: _route.slug,
          id: _route.id,
          name: _route.name,
          type: _route.type,
        },
      });
    });
  }

  if (typeof window.siteData.routes.posts !== 'undefined') {
    window.siteData.routes.posts.forEach(function(_route) {
      routes.push({
        path: '/:category/(' + _route.slug + ')',
        component: eval(_route.template),
        meta: { view: _route.template },
        props: {
          slug: _route.slug,
          id: _route.id,
          name: _route.name,
          type: _route.type,
        },
      });
    });
  }
  if (typeof window.siteData.routes.lgbtq !== 'undefined') {
    window.siteData.routes.lgbtq.forEach(function(_route) {
      routes.push({
        path: '/lgbt-travel/(' + _route.slug + ')',
        component: PostLGBTQ,
        meta: { view: _route.template },
        props: {
          slug: _route.slug,
          id: _route.id,
          name: _route.name,
        },
      });
    });
  }

  if (typeof window.siteData.routes.hubs !== 'undefined') {
    window.siteData.routes.hubs.forEach(function(_route) {
      routes.push({
        path: '/(' + _route.slug + ')',
        component: PageHub,
        meta: { view: _route.template },
        props: {
          slug: _route.slug,
          id: _route.id,
          name: _route.name,
        },
      });
    });
  }

  if (typeof window.siteData.routes.categories !== 'undefined') {
    window.siteData.routes.categories.forEach(function(_route) {
      routes.push({
        path: '/(' + _route.slug + ')',
        component: eval(_route.template),
        meta: { view: _route.template },
        props: {
          slug: _route.slug,
          id: _route.id,
          name: _route.name,
          isCity: _route.is_city,
          isChild: _route.is_child,
        },
      });
    });
  }

  routes.push(
    {
      path: '/:category/:pathMatch',
      name: 'Post',
      component: Post,
      meta: { view: 'Post' },
      props: true,
    },
    {
      path: '/*',
      name: 'NotFound',
      component: NotFound,
      meta: { view: 'NotFound' },
      props: true,
    },
  );
}
//If it's Spanish
else {
  routes = [
    {
      path: '/es/',
      name: 'Home',
      component: Home,
    },
    {
      path: '/es/buscador',
      name: 'Search',
      component: Search,
      props: true,
      children: [
        {
          path: ':pathMatch',
          name: 'SearchChild',
          component: Search,
          query: 's',
          props: true,
        },
      ],
    },
    {
      path: '/es/preview',
      alias: '/es/preview/:category/:pathMatch',
      name: 'postPreview',
      component: PostPreview,
      props: true,
    },
    {
      path: '/es/(quienes-somos)',
      name: 'PageAbout',
      component: Page,
      props: true,
    },
    {
      path: '/es/tag/:pathMatch',
      name: 'Tag',
      component: Tag,
      props: true,
    },
    {
      path: '/es/autor/:pathMatch',
      name: 'Author',
      component: Author,
      props: true,
    },
  ];

  if (typeof window.siteData.routes.categories !== 'undefined') {
    window.siteData.routes.categories.forEach(function(_route) {
      routes.push({
        path: '/es/(' + _route.slug + ')',
        component: eval(_route.template),
        meta: { view: _route.template },
        props: {
          slug: _route.slug,
          id: _route.id,
          name: _route.name,
          isCity: _route.is_city,
          isChild: _route.is_child,
        },
      });
    });
  }
  routes.push(
    {
      path: '/es/:category/:pathMatch',
      name: 'Post',
      component: Post,
      meta: { view: 'Post' },
      props: true,
    },
    {
      path: '/es/*',
      name: 'NotFound',
      component: NotFound,
      meta: { view: 'NotFound' },
      props: true,
    },
  );
}

/*
routes.push(
  {
    //@todo find a better way to include interactive pages
    path: '/:pathMatch',
    name: 'Page',
    component: StoryBookedArticle,
    props: true
  }
)
*/

const router = new Router({
  mode: 'history',
  base: '',
  routes,
  scrollBehavior(to, from, savedPosition) {
    if (savedPosition) {
      //return savedPosition
      return { x: 0, y: 0 };
    } else {
      return { x: 0, y: 0 };
    }
  },
});

export default router;
