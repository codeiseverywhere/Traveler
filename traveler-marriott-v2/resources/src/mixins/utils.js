export default {
  data() {
    return {
      months: [
        'January',
        'February',
        'March',
        'April',
        'May',
        'June',
        'July',
        'August',
        'September',
        'October',
        'November',
        'December',
      ],
    };
  },

  methods: {
    getFormattedDate: function(time) {
      let date = new Date(time);
      return `${
        this.months[date.getMonth()]
      } ${date.getDate()}, ${date.getFullYear()}`;
    },

    goBack: function() {
      this.$router.go(-1);
    },

    getQueryString: function(name) {
      name = name.replace(/[[]/, '\\[').replace(/[\]]/, '\\]');
      let regex = new RegExp('[\\?&]' + name + '=([^&#]*)');
      let results = regex.exec(location.search);
      return results === null
        ? ''
        : decodeURIComponent(results[1].replace(/\+/g, ' '));
    },
  },
};

export const bodyTag = document.getElementsByTagName('body')[0];

export const isMobile = () => window.matchMedia('(max-width: 768px)').matches;

export const createTag = (url, id) => {
  const scriptTag = document.createElement('script');
  scriptTag.id = id;
  scriptTag.async = true;
  scriptTag.onload = () => {
    window.EventBus.fire('scriptReady', id);
  };
  scriptTag.type = 'text/javascript';
  scriptTag.src = url;
  bodyTag?.appendChild(scriptTag);
};

export const branchTag = () => {
  createTag('https://cdn.branch.io/branch-latest.min.js', 'branch-sdk');

  if (!{}.hasOwnProperty.call(window, 'branch')) {
    const branch_init = {
      _q: [],
      _v: 1,
    };
    const methods = [
      'addListener',
      'applyCode',
      'autoAppIndex',
      'banner',
      'closeBanner',
      'closeJourney',
      'creditHistory',
      'credits',
      'data',
      'deepview',
      'deepviewCta',
      'first',
      'getCode',
      'init',
      'link',
      'logout',
      'redeem',
      'referrals',
      'removeListener',
      'sendSMS',
      'setBranchViewData',
      'setIdentity',
      'track',
      'validateCode',
      'trackCommerceEvent',
      'logEvent',
      'disableTracking',
    ];

    methods.forEach((e) => {
      branch_init[e] = () => {
        b._q.push([r, arguments]);
      };
    });

    window.branch = branch_init;
  }
};

export const segmentTag = (key) => {
  const analytics = {}.hasOwnProperty.call(window, 'analytics')
    ? window.analytics
    : [];

  if (!analytics.initialize) {
    if (analytics.invoked) {
      window.console &&
        console.error &&
        console.error('Segment snippet included twice.');
    } else {
      analytics.invoked = !0;
      analytics.methods = [
        'trackSubmit',
        'trackClick',
        'trackLink',
        'trackForm',
        'pageview',
        'identify',
        'reset',
        'group',
        'track',
        'ready',
        'alias',
        'debug',
        'page',
        'once',
        'off',
        'on',
        'addSourceMiddleware',
        'addIntegrationMiddleware',
        'setAnonymousId',
        'addDestinationMiddleware',
      ];
      analytics.factory = (e) => {
        return () => {
          const t = Array.prototype.slice.call(arguments);
          t.unshift(e);
          analytics.push(t);
          return analytics;
        };
      };
      analytics.methods.forEach((key) => {
        analytics[key] = analytics.factory(key);
      });

      analytics.load = (key, e) => {
        createTag(
          'https://cdn.segment.com/analytics.js/v1/' +
            key +
            '/analytics.min.js',
          'segment-sdk',
        );
        analytics._loadOptions = e;
      };
      analytics.SNIPPET_VERSION = '4.13.1';

      analytics.load(key);

      analytics.page();
    }
  }
};
