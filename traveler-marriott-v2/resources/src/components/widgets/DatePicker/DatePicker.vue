<template>
  <m-date-picker
    v-model="range"
    is-range
    :available-dates="{ start: start, end: end }"
    :input-debounce="500"
  >
    <template v-slot="{ inputValue, inputEvents }">
      <input
        class="text-left test"
        :placeholder="siteData.i18n.checkInOut"
        :value="
          inputValue.start ? inputValue.start + ' - ' + inputValue.end : ''
        "
        v-on="inputEvents.end"
      />
    </template>
  </m-date-picker>
</template>

<script>
const VCalendar = () =>
  import(
    /* webpackChunkName: "/chunk/widgets/VCalendar" */ 'v-calendar/lib/components/date-picker.common'
  );

export default {
  name: 'date-picker',
  data() {
    const isIE =
      {}.hasOwnProperty.call(window, 'MSInputMethodContext') &&
      {}.hasOwnProperty.call(document, 'documentMode');
    const today = new Date();
    return {
      siteData: window.siteData,
      start: today.getTime(),
      end: today.setFullYear(today.getFullYear() + 1),
      range: {
        start: '',
        end: '',
      },
      masks: {
        title: 'MMMM YYYY',
        weekdays: 'W',
        navMonths: 'MMM',
        input: ['L', 'YYYY-MM-DD', 'YYYY/MM/DD'],
        dayPopover: 'WWW, MMM D, YYYY',
        data: ['L', 'YYYY-MM-DD', 'YYYY/MM/DD'],
      },
    };
  },

  components: {
    'm-date-picker': function (resolve) {
      // This special require syntax will instruct Webpack to
      // automatically split your built code into bundles which
      // are loaded over Ajax requests.
      if (process.env.BUILD_MODERN === 'true') {
        require(['v-calendar/lib/components/date-picker.common'], resolve)
      } else {
        resolve({
          template: '<div>I am async!</div>'
        })
      }
    }
  },};
</script>
