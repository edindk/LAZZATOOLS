import Vue from 'vue'
import Router from 'vue-router'

// Containers
const TheContainer = () => import('@/containers/TheContainer')

// Views
const Dashboard = () => import('@/views/Dashboard')
const RevenueWire = () => import('@/views/revenueWire/RevenueWire')

// Views - Auth
const Login = () => import('@/views/auth/Login')
const Register = () => import('@/views/auth/Register')
const Logout = () => import('@/views/auth/Logout')

// Views - Pages
const Page404 = () => import('@/views/pages/Page404')
const Page500 = () => import('@/views/pages/Page500')

Vue.use(Router)

export default new Router({
    // sættes til history mode
    mode: 'hash',
    linkActiveClass: 'active',
    scrollBehavior: () => ({y: 0}),
    routes: configRoutes()
})

function configRoutes() {
    return [
        {
            path: '/',
            redirect: '/dashboard',
            name: 'LAZZATOOLS',
            component: TheContainer,
            meta: {
              requiresAuth: true,
            },
            children: [
                {
                    path: 'dashboard',
                    name: 'dashboard',
                    component: Dashboard
                },
                {
                    path: 'revenuewire',
                    name: 'revenueWire',
                    component: RevenueWire
                },
            ]
        },
        {
            path: '/register',
            name: 'register',
            component: Register,
            meta: {
                requiresVisitor: true
            }
        },
        {
            path: '/login',
            name: 'login',
            component: Login,
            meta: {
                requiresVisitor: true
            }
        },
        {
            path: '/logout',
            name: 'logout',
            component: Logout
        },
        {
            path: '/404',
            name: 'Page404',
            component: Page404
        },
        {
            path: '/500',
            name: 'Page500',
            component: Page500
        },
    ]
}

