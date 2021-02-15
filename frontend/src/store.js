import Vue from 'vue'
import Vuex from 'vuex'
import axios from 'axios'

Vue.use(Vuex)
axios.defaults.baseURL = 'http://api.lazzatools.dk/api'

const state = {
    token: localStorage.getItem('access_token') || null,
    sidebarShow: 'responsive',
    sidebarMinimize: false,
    user: {
        name: '',
        email: '',
    },
    success: null
}

const getters = {
    loggedIn(state) {
        return state.token !== null
    },
    userDetails(state) {
        return state.user
    },
    registeredSuccessfully(state) {
        return state.success
    }
}

const mutations = {
    toggleSidebarDesktop(state) {
        const sidebarOpened = [true, 'responsive'].includes(state.sidebarShow)
        state.sidebarShow = sidebarOpened ? false : 'responsive'
    },
    toggleSidebarMobile(state) {
        const sidebarClosed = [false, 'responsive'].includes(state.sidebarShow)
        state.sidebarShow = sidebarClosed ? true : 'responsive'
    },
    set(state, [variable, value]) {
        state[variable] = value
    },
    retrieveToken(state, token) {
        state.token = token
    },
    destroyToken(state) {
        state.token = null
    },
    storeToken(state, token) {
        state.token = token
    },
    storeUser(state, user) {
        state.user.name = user.name
        state.user.email = user.email
    },
    storeSuccess(state, success) {
        state.success = success
    }
}

const actions = {
    successfullyRegistered(context, success) {
        context.commit('storeSuccess', success)
    },
    register(context, data) {
        return new Promise((resolve, reject) => {
            axios.post('/register', {
                name: data.name,
                email: data.email,
                password: data.password,
            })
                .then(response => {
                    resolve(response)
                })
                .catch((error) => {
                    reject(error.response.data.errors)
                })
        })
    },
    retrieveToken(context, credentials) {

        return new Promise((resolve, reject) => {
            axios.post('/login', {
                username: credentials.username,
                password: credentials.password,
            })
                .then(response => {
                    const token = response.data.access_token

                    localStorage.setItem('access_token', token)
                    context.commit('retrieveToken', token)
                    resolve(response)
                })
                .catch((error) => {
                    reject(error.response.data.errors)
                })
        })
    },
    retrieveUser(context) {
        if (state.user.name === '' && state.user.email === '') {
            axios.defaults.headers.common['Authorization'] = 'Bearer ' + context.state.token
            return new Promise((resolve, reject) => {
                axios.get('/user')
                    .then(response => {
                        context.commit('storeUser', response.data)
                    })
                    .catch(error => {
                        console.log(error)
                    })
            })
        }
    },
    destroyToken(context) {
        axios.defaults.headers.common['Authorization'] = 'Bearer ' + context.state.token

        if (context.getters.loggedIn) {
            return new Promise((resolve, reject) => {
                axios.post('/logout')
                    .then(response => {
                        localStorage.removeItem('access_token')
                        context.commit('destroyToken')
                        resolve(response)
                        // console.log(response);
                        // context.commit('addTodo', response.data)
                    })
                    .catch(error => {
                        localStorage.removeItem('access_token')
                        context.commit('destroyToken')
                        reject(error)
                    })
            })
        }
    }
}


export default new Vuex.Store({
    state,
    getters,
    mutations,
    actions
})