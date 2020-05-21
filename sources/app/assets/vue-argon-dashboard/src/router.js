import Vue from 'vue'
import Router from 'vue-router'
import DashboardLayout from './layout/DashboardLayout'
import AuthLayout from './layout/AuthLayout'
import store from "./store";
Vue.use(Router);

const router = new Router({
  linkExactActiveClass: 'active',
  routes: [
    {
      path: '/',
      redirect: 'dashboard',
      component: DashboardLayout,
      children: [
        {
          path: '/dashboard',
          name: 'dashboard',
          meta: {
            requiresAuth: true
          },
          // route level code-splitting
          // this generates a separate chunk (about.[hash].js) for this route
          // which is lazy-loaded when the route is visited.
          component: () => import(/* webpackChunkName: "demo" */ './views/Dashboard.vue')
        },
        {
          path: '/locations',
          meta: { requiresAuth: true },
          component: () => import('./views/Locations/index'),
          children: [
            {
              name: 'locations',
              path: '',
              meta: { requiresAuth: true},
              component: () => import('./views/Locations/table')
            },
            {
              path: ':id/edit',
              name: 'location-edit',
              meta: { requiresAuth: true },
              component: () => import('./views/Locations/edit')
            },
            {
              path: 'create',
              name: 'location-create',
              meta: { requiresAuth: true },
              component: () => import('./views/Locations/create')
            },
          ]
        },
        {
          path: '/icons',
          name: 'icons',
          component: () => import(/* webpackChunkName: "demo" */ './views/Icons.vue')
        },
        {
          path: '/profile',
          name: 'profile',
          meta: {
            requiresAuth: true
          },
          component: () => import(/* webpackChunkName: "demo" */ './views/UserProfile.vue')
        },
        {
          path: '/maps',
          name: 'maps',
          meta: {
            requiresAuth: true
          },
          component: () => import(/* webpackChunkName: "demo" */ './views/Maps.vue')
        },
        {
          path: '/tables',
          name: 'tables',
          meta: {
            requiresAuth: true
          },
          component: () => import(/* webpackChunkName: "demo" */ './views/Tables.vue')
        },
        {
          path: '/page-builder',
          name: 'Page Builder',
          component: () => import(/* webpackChunkName: "demo" */ './views/PageBuilder.vue')
        }
      ]
    },
    {
      path: '/',
      redirect: 'login',
      component: AuthLayout,
      children: [
        {
          path: '/login',
          name: 'login',
          component: () => import(/* webpackChunkName: "demo" */ './views/Login.vue')
        },
        {
          path: '/register',
          name: 'register',
          component: () => import(/* webpackChunkName: "demo" */ './views/Register.vue')
        }
      ]
    },
    {
      path: '/place/:id',
      name: 'location-outside',
      meta: { requiresAuth: false },
      component: () => import('./views/Locations/outside')
    },
    {
      path: '/locations/:id/builder',
      name: 'location-builder',
      meta: { requiresAuth: true },
      component: () => import('./views/Locations/builder')
    }
  ]
});

router.beforeEach((to, from, next) => {
  if (to.matched.some(record => record.meta.requiresAuth)) {
    if (store.getters.isLoggedIn) {
      next();
      return
    }
    next('/login');
  } else {
    next();
  }
});

export default router