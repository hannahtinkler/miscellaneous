data = {
  element: '#app',
  tabs: [],
  defaultTab: null,
}


class TabbedWidget {
  /**
   * Builds data object for Vue component and adds navigation element to the
   * element specified
   * @param  {object} options
   * @return {void}
   */
  constructor (options) {
    data.tabs = this.getTabsFromMarkup()
    data = $.extend({}, data, options || {})
    $(data.element).prepend('<tab-navigation></tab-navigation>')

    new Vue({ 'el': data.element })
  }

  /**
   * Generates tab list from markup
   * @return {array}
   */
  getTabsFromMarkup () {
    let tabs = []

    Array.from(document.querySelectorAll('[data-tab-title]')).forEach(
      function (item) {
        tabs.push({ title: item.dataset.tabTitle, active: false })
      }
    )

    return tabs
  }
}


Vue.component('tab-navigation', {
  template: `<ul class="tabs__navigation">
        <li v-for="tab in tabs"
          :class="tab.active ? 'active' : ''"
          class="tabs__navigation-item"
          v-on:click="switchTab(tab.title)"
        >
          <a class="tabs__navigation-item-link" href="#">{{ tab.title }}</a>
        </li>
    </ul>`,

  data: function () {
    return data
  },

  methods: {
    /**
     * Activates either teh tab specified or the first in the markup
     * @return {void}
     */
    initialiseActiveTab: function () {
      this.switchTab(this.defaultTab ? this.defaultTab : this.tabs[0].title)
    },

    /**
     * Marks the tab with the given title as active and hides all other tab
     * content
     * @param  {string} tabTitle
     * @return {void}
     */
    switchTab: function (tabTitle) {
      $('[data-tab-title]').hide()

      this.tabs.map(function (tab) {
          tab.active = tab.title === tabTitle ? true : false
      })

      $('[data-tab-title="' + tabTitle + '"]').show()
    }
  },

  mounted () {
    this.initialiseActiveTab()
  },
})
