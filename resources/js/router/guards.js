import store from '@/store';

const getUserRoleId = async () => {
  let user = store.getters['auth/user'];

  // If user is not loaded, attempt to fetch data
  if (!user || !user.role_id) {
    await store.dispatch('auth/getUserData');
    user = store.getters['auth/user'];
  }
  
  return user ? user.role_id : null;
};

export const registerGuards = (router) => {
  // Guard 1: Handle Cabinet Token and Authentication
  router.beforeEach(async (to, from, next) => {
    const guest = to.matched.some((record) => record.meta.guest);

    if (!guest) {
      if (localStorage.getItem('cabinetToken')) {
        await getUserRoleId();
        next();
      } else if ('key' in to.query && to.query.key != null) {
        localStorage.setItem('cabinetToken', to.query.key);
        await getUserRoleId();
        next();
      } else {
        window.location.replace(
          process.env.VUE_APP_CABINET_APP_URL +
          process.env.VUE_APP_CABINET_APP_SERVICE +
          process.env.VUE_APP_CABINET_APP_TOKEN,
        );
        return; // Prevent falling through to next()
      }
    } else {
      next();
    }
  });

  // Guard 2: Handle Role-based Authorization
  router.beforeEach(async (to, from, next) => {
    const guest = to.matched.some((record) => record.meta.guest);

    if (!guest) {
      const accessIsAllowed = to.meta.accessIsAllowed;
      const user = store.getters['auth/user'];
      const userRoleId = user ? user.role_id : null;

      if (accessIsAllowed !== undefined) {
        if (userRoleId && accessIsAllowed.includes(userRoleId)) {
          next();
        } else {
          next({ name: 'Forbidden' });
        }
      } else {
        // If meta.accessIsAllowed is missing and it's not a guest route
        next({ name: 'Forbidden' });
      }
    } else {
      next();
    }
  });
};
