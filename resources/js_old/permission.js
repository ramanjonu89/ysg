import router from './router';
import store from './store';
import TokenService from '@/services/Token';
// import { getToken } from '@/utils/auth'; // get token from cookie
// import getPageTitle from '@/utils/get-page-title';


const whiteList = ['/login']; // no redirect whitelist

router.beforeEach(async(to, from, next) => {
  document.title = to.meta.title;

  // const hasToken = getToken();
  const hasToken = TokenService.hasToken();

  if (hasToken) {
    if (to.path === '/login') {
      // if is logged in, redirect to the home page
      next({ path: '/dashboard' });
    } else {
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

router.afterEach(() => {
  // finish progress bar
});
