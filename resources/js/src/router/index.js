import Token from "@/utils/token";
import Vue from 'vue';
import VueRouter from 'vue-router';

// Routes
import pages from './routes/pages';
import products from './routes/products';
import settings from './routes/settings';

Vue.use(VueRouter)

const router = new VueRouter({
  mode: 'history',
  base: process.env.BASE_URL,
  scrollBehavior() {
    return { x: 0, y: 0 }
  },
  routes: [
    {
      path: '/',
      name: 'dashboard',
      component: () => import('@/views/dashboard/Index.vue'),
      meta: {
        pageTitle: 'Dashboard',
        breadcrumb: [
          {
            text: 'Dashboard',
            active: true,
          },
        ],
        resource: 'dashboard',
        action: 'read',
      },
    },
    ...products,
    {
      path: '/orders',
      name: 'orders',
      component: () => import('@/views/orders/Index.vue'),
      meta: {
        pageTitle: 'Orders',
        breadcrumb: [
          {
            text: 'Orders',
          },
        ],
        resource: 'orders',
        action: 'update',
      },
    },
    {
      path: '/orders/:id',
      name: 'order-detail',
      component: () => import('@/views/orders/Detail.vue'),
      meta: {
        pageTitle: 'Order Detail',
        breadcrumb: [
          {
            text: 'Orders',
            to: '/orders'
          },
          {
            text: 'Order detail',
            active: true,
          },
        ],
        resource: 'orders',
        action: 'update',
      },
    },
    {
      path: '/coupon',
      name: 'coupons',
      component: () => import('@/views/coupon/Index.vue'),
      meta: {
        pageTitle: 'Coupons',
        breadcrumb: [
          {
            text: 'Coupons',
          },
        ],
        buttons: [
          {
            text: 'Add Coupons',
            linkTo: 'coupons.add'
          }
        ],
        resource: 'coupons',
        action: 'read',
      },
    },
    {
      path: '/coupon/add',
      name: 'coupons.add',
      component: () => import('@/views/coupon/Add.vue'),
      meta: {
        pageTitle: 'Add coupon',
        breadcrumb: [
          {
            text: 'Add new coupon',
          },
        ],
        resource: 'coupons',
        action: 'create',
      },
    },
    {
      path: '/coupon/:id/edit',
      meta: {
        pageTitle: 'Coupons',
        label: 'Edit',
        breadcrumb: [
          {
            text: 'Coupons',
            to: { name: 'coupons' }
          },
          {
            text: 'Edit',
            active: true,
          },
        ],
        resource: 'coupons',
        action: 'update',
      },
      name: 'coupons.edit',
      component: () => import('@/views/coupon/Add.vue'),
    },
    ...settings,
    {
      path: '*',
      redirect: 'error-404',
    },
    ...pages,
  ],
})

import { canNavigate } from '@/libs/acl/routeProtection'

const whiteList = ['/login']; // no redirect whitelist

router.beforeEach(async(to, from, next) => {
  document.title = to.meta.pageTitle || document.title

  const hasToken = Token.hasToken();
  if (hasToken) {
    if (to.path === '/login') {
      // if is logged in, redirect to the home page
      next({ name: 'dashboard' });
    } else {
      if (!canNavigate(to)) {
        next(from);
      } else {
        next();
      }
      next();
    }
  } else {
    if (whiteList.indexOf(to.matched[0] ? to.matched[0].path : '') !== -1) {
      // in the free login whitelist, go directly
      next();
    } else {
      // other pages that do not have permission to access are redirected to the login page.
      next(`/login?redirect=${to.path}`);
    }
  }
});

// ? For splash screen
// Remove afterEach hook if you are not using splash screen
router.afterEach(() => {
  // Remove initial loading
  const appLoading = document.getElementById('loading-bg')
  if (appLoading) {
    appLoading.style.display = 'none'
  }
})

export default router
