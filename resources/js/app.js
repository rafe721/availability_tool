
require('./bootstrap');

window.Vue = require('vue');

import Datepicker from 'vuejs-datepicker';
import NumberInputSpinner from 'vue-number-input-spinner';
import moment from 'moment';
import Formatter from './Formatter';

window.moment = moment;

// Filters to format dates in the Front ent..
Vue.filter("displayDayOfWeek", function (aDate) {
    return moment(aDate).format('ddd').toUpperCase();
});
Vue.filter("displayDayOfWeekFull", function (aDate) {
    return moment(aDate).format('dddd');
});
Vue.filter("displayDay", function (aDate) {
    return moment(aDate).format('DD').toUpperCase();
});
Vue.filter("displayMonth", function (aDate) {
    return moment(aDate).format('MMM');
});
Vue.filter("displayYear", function (aDate) {
    return moment(aDate).format('YYYY');
});
Vue.filter("displayDate", function (aDate) {
    return moment(aDate).format('MMMM DD YYYY');
});

// required components
Vue.prototype.$formatter = new Formatter();
Vue.component('datepicker', Datepicker);
Vue.component('NumberInputSpinner', NumberInputSpinner);
Vue.component('availability-component', require('./components/AvailabilityComponent.vue'));


const app = new Vue({
    el: '#app'
});
