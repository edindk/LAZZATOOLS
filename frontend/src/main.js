import 'core-js/stable'
import Vue from 'vue'
import App from './App'
import router from './router'
import CoreuiVue from '@coreui/vue'
import {iconsSet as icons} from './assets/icons/icons.js'
import store from './store'
import 'bootstrap'
import Modal from "@burhanahmeed/vue-modal-2";
import vuetify from './plugins/vuetify';


Vue.config.performance = true
Vue.use(CoreuiVue)
Vue.use(Modal, {
    componentName: "ModalVue"
});
Vue.prototype.$log = console.log.bind(console)

router.beforeEach((to, from, next) => {
    if (to.matched.some(record => record.meta.requiresAuth)) {
        if (!store.getters.loggedIn) {
            next({
                name: 'login',
            })
        } else {
            next()
        }
    } else if (to.matched.some(record => record.meta.requiresVisitor)) {
        if (store.getters.loggedIn) {
            next({
                name: 'dashboard',
            })
        } else {
            next()
        }
    } else {
        next()
    }
})

new Vue({
    el: '#app',
    router,
    store,
    icons,
    template: '<App/>',
    vuetify,

    components: {
        App
    }
})

