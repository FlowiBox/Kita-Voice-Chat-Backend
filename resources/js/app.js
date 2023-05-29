require('./bootstrap');

import Vue from 'vue'

Vue.config.productionTip = false

Vue.component('test', require('./components/ExampleComponents.vue').default);

//initialize vue
const app = new Vue({
    el: '#app',
});