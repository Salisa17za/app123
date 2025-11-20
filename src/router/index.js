import { createRouter, createWebHistory } from 'vue-router'
import HomeView from '../views/HomeView.vue'

const routes = [
  {
    path: '/',
    name: 'home',
    component: HomeView
  },
  {
    path: '/about',
    name: 'about',
    component: () => import(/* webpackChunkName: "about" */ '../views/AboutView.vue')
  }
  ,
  {
    path: '/log',
    name: 'log',
    component: () => import(/* webpackChunkName: "about" */ '../views/Login.vue')
  }
  ,
  {
    path: '/show',
    name: 'show',
    component: () => import(/* webpackChunkName: "about" */ '../views/show.vue')
  }
  ,
  {
    path: '/man',
    name: 'man',
    component: () => import(/* webpackChunkName: "about" */ '../views/manage.vue')
  }
]

const router = createRouter({
  history: createWebHistory(process.env.BASE_URL),
  routes
})

export default router
