/**
 * First we will load all of this project's JavaScript dependencies which
 * includes Vue and other libraries. It is a great starting point when
 * building robust, powerful web applications using Vue and Laravel.
 */
import {
  InertiaApp
} from '@inertiajs/inertia-vue';
import Vuex from 'vuex';
import * as VueCookie from 'vue-cookie';
import mixinsCookie from './mixins/cookies'
import VueSweetalert2 from 'vue-sweetalert2';
import 'sweetalert2/dist/sweetalert2.min.css';
// Tell Vue to use the plugin

require('./bootstrap');

window.Vue = require('vue').default;

import vuetify from './vuetify'
import storeData from "./store/index" //--Vuex Coding file imported
const store = new Vuex.Store( //--Only 1st_bracket() here
  storeData
)

/**
 * Configure Inertia
 */

Vue.config.productionTip = false;
Vue.mixin({
  computed: {
    isMobile() {
      return +this.$root.newPixelRatio >= 4 || this.$vuetify.breakpoint.xs
    }
  },
  methods: {
    route: (...args) => window.route(...args).url()
  }
});
Vue.mixin(mixinsCookie);
Vue.use(InertiaApp);
Vue.use(VueCookie);

Vue.use(VueSweetalert2, {
  confirmButtonColor: '#3085d6',
  cancelButtonColor: '#d33',
});

const root = document.getElementById('app');

new Vue({
  vuetify,
  store,
  render: h => h(InertiaApp, {
    props: {
      initialPage: JSON.parse(root.dataset.page),
      resolveComponent: name => require(`./pages/${name}`).default
    }
  }),
  data() { return { newPixelRatio: this.getNewPixelRatioAndReSize() } },
  created() {
    window.addEventListener('resize', () => {
      this.newPixelRatio = this.getNewPixelRatioAndReSize();
    });
    setTimeout(() => {
      this.newPixelRatio = this.getNewPixelRatioAndReSize();
    }, 2000);
  },
  methods: {
    getNewPixelRatioAndReSize() {
      const newPixelRatio = window.devicePixelRatio || window.screen.availWidth / document.documentElement.clientWidth;
      const ndeDashboardDOM = document.getElementById("nde-dashboard-container");
      const ndeAuthDOM = document.getElementById("nde-auth-layout")

      if (ndeDashboardDOM) {
        if (newPixelRatio >= 4) {
          ndeDashboardDOM.style.maxHeight = 'calc(150vh - 4rem)';
        } else if (newPixelRatio >= 3) {
          ndeDashboardDOM.style.maxHeight = 'calc(135vh - 4rem)';
        } else {
          ndeDashboardDOM.style.maxHeight = 'calc(100vh - 4rem)';
        }
      }

      if (ndeAuthDOM) {
        if (newPixelRatio >= 3) {
          ndeAuthDOM.style.height = '140vh';
          ndeAuthDOM.style.width = '143vw';
        } else {
          ndeAuthDOM.style.height = '100vh';
          ndeAuthDOM.style.width = '100vw';
        }
      }

      window.document.body.style.zoom = newPixelRatio >= 3 ? '70%' : '';

      return newPixelRatio;
    },
  },
}).$mount(root);
