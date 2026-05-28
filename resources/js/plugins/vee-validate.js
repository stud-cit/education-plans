import Vue from 'vue';
import * as rules from 'vee-validate/dist/rules';
import {extend, localize, ValidationProvider, ValidationObserver } from 'vee-validate';

import ua from "vee-validate/dist/locale/uk.json";


Vue.component('ValidationProvider', ValidationProvider);
Vue.component('ValidationObserver', ValidationObserver);

Object.keys(rules).forEach(rule => {
    extend(rule, rules[rule]);
});

localize("ua", ua);
