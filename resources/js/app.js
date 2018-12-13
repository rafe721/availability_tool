
require('./bootstrap');

window.Vue = require('vue');

import { Form, HasError, AlertError } from 'vform';
import Datepicker from 'vuejs-datepicker';
import NumberInputSpinner from 'vue-number-input-spinner';
import vueNumberSpinner from 'vue-number-spinner';
import moment from 'moment';
import Formatter from './Formatter';

window.Form = Form;
window.moment = moment;

// const files = require.context('./', true, /\.vue$/i)
// files.keys().map(key => Vue.component(key.split('/').pop().split('.')[0], files(key)))

Vue.filter("displayDayOfWeek", function (aDate) {
    return moment(aDate).format('ddd').toUpperCase();
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

Vue.prototype.$formatter = new Formatter();
Vue.component('datepicker', Datepicker);
Vue.component('NumberInputSpinner', NumberInputSpinner);
Vue.component('vue-number-spinner', vueNumberSpinner);

Vue.component(HasError.name, HasError);
Vue.component(AlertError.name, AlertError);
Vue.component('availability-component', require('./components/AvailabilityComponent.vue'));


const app = new Vue({
    el: '#app'
});
