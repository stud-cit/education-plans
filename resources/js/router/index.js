import Vue from 'vue';
import VueRouter from 'vue-router';
import store from '@/store';
import { ROLES } from '@/utils/constants';

import Plans from '../views/pages/Plans.vue';
import Layout from '../views/Layout';
import CreatePlan from '@/views/pages/plan/CreatePlan';
import SelectiveDisciplines from '@/views/pages/SelectiveDisciplines/SelectiveDisciplines';

const allRoles = () => Object.values(ROLES.ID);
const exceptRoles = (role_ids) => allRoles().filter((key) => !role_ids.includes(key));

Vue.use(VueRouter);
const BREADCRUMBS = {
  SETTINGS: [
    { text: 'Робота з планами', to: { name: 'ListPlans' } },
    { text: 'Налаштування', to: { name: 'Settings' } },
  ],
};

const routes = [
  {
    path: '/err',
    name: 'err',
    meta: { guest: true },
    component: () => import(/* webpackChunkName: "error" */ '@/views/Err'),
  },
  {
    path: '/',
    component: Layout,
    children: [
      {
        path: '',
        name: 'ListPlans',
        component: Plans,
        meta: {
          requiresAuth: true,
          header: 'Робота з планами',
          accessIsAllowed: allRoles(),
          breadCrumb: [{ text: 'Робота з планами' }],
        },
      },
      {
        path: 'selective-disciplines',
        name: 'SelectiveDisciplines',
        component: SelectiveDisciplines,
        meta: {
          requiresAuth: true,
          accessIsAllowed: exceptRoles([ROLES.ID.guest]),
          header: 'Вибіркові дисципліни',
          breadCrumb: [{ text: 'Вибіркові дисципліни' }],
        },
      },
      {
        path: 'selective-disciplines-catalog',
        name: 'SelectiveDisciplinesCatalog',
        component: () =>
          import(
            /* webpackChunkName: "selective-disciplines-catalog" */
            '@/views/pages/SelectiveDisciplines/SelectiveDisciplinesCatalog'
          ),
        meta: {
          requiresAuth: true,
          accessIsAllowed: exceptRoles([ROLES.ID.guest]),
          header: 'Вибіркові дисципліни (каталог)',
          breadCrumb: [
            {
              text: 'Вибіркові дисципліни',
              to: { name: 'SelectiveDisciplines' },
            },
            { text: 'Вибіркові дисципліни (каталог)' },
          ],
        },
      },
      {
        path: 'catalog-specialties',
        component: () => import('@/views/SimpleLayout'),
        children: [
          {
            path: '',
            name: 'CatalogSpecialties',
            component: () =>
              import(
                /* webpackChunkName: "catalog-specialties"*/
                '@/views/pages/SelectiveDisciplines/CatalogSpecialties'
              ),
            meta: {
              requiresAuth: true,
              accessIsAllowed: exceptRoles([ROLES.ID.guest]),
              header: 'Вибіркові дисципліни за спеціальністю (каталог)',
              breadCrumb: [
                {
                  text: 'Вибіркові дисципліни',
                  to: { name: 'SelectiveDisciplines' },
                },
                { text: 'Каталоги вибіркових дисциплін за спеціальністю' },
              ],
            },
          },
          {
            path: ':id',
            name: 'CatalogSpecialty',
            component: () =>
              import(
                /* webpackChunkName: "catalog-specialty" */
                '@/views/pages/SelectiveDisciplines/CatalogSpecialties/catalog'
              ),
            meta: {
              requiresAuth: true,
              header: 'Каталог',
              accessIsAllowed: exceptRoles([ROLES.ID.guest]),
              breadCrumb: [
                {
                  text: 'Вибіркові дисципліни',
                  to: { name: 'SelectiveDisciplines' },
                },
                {
                  text: 'Каталоги вибіркових дисциплін за спеціальністю',
                  to: { name: 'CatalogSpecialties' },
                },
                { text: 'Каталог' },
              ],
            },
          },
        ],
      },
      {
        path: 'catalog-education-programs',
        component: () => import('@/views/SimpleLayout'),
        children: [
          {
            path: '',
            name: 'CatalogEducationPrograms',
            component: () =>
              import(
                /* webpackChunkName: "catalog-education-programs" */
                '@/views/pages/SelectiveDisciplines/CatalogEducationPrograms'
              ),
            meta: {
              requiresAuth: true,
              accessIsAllowed: exceptRoles([ROLES.ID.guest]),
              header: 'Вибіркові дисципліни за освітньою програмою (каталог)',
              breadCrumb: [
                {
                  text: 'Вибіркові дисципліни',
                  to: { name: 'SelectiveDisciplines' },
                },
                { text: 'Каталоги вибіркових дисциплін за освітньою програмою' },
              ],
            },
          },
          {
            path: ':id',
            name: 'CatalogEducationProgram',
            component: () =>
              import(
                /* webpackChunkName: "catalog-education-program-id" */
                '@/views/pages/SelectiveDisciplines/CatalogEducationPrograms/catalog'
              ),
            meta: {
              requiresAuth: true,
              header: 'Каталог',
              accessIsAllowed: exceptRoles([ROLES.ID.guest]),
              breadCrumb: [
                {
                  text: 'Вибіркові дисципліни',
                  to: { name: 'SelectiveDisciplines' },
                },
                {
                  text: 'Каталоги вибіркових дисциплін за освітньою програмою',
                  to: { name: 'CatalogEducationPrograms' },
                },
                { text: 'Каталог' },
              ],
            },
          },
        ],
      },
    ],
  },
  {
    path: '/plan/pdf',
    component: Layout,
    children: [
      {
        path: 'speciality/:id',
        name: 'PlanCatalogSpecialityPdf',
        component: () =>
          import(/* webpackChunkName: "catalog-speciality-pdf" */ '@/views/pages/plan/CatalogSpecialityPDF'),
        meta: {
          requiresAuth: true,
          accessIsAllowed: allRoles(),
          header: 'PDF Спеціальності',
        },
      },
      {
        path: 'education-program/:id',
        name: 'PlanCatalogEducationProgramPdf',
        component: () =>
          import(/* webpackChunkName: "catalog-edu-program-pdf" */ '@/views/pages/plan/CatalogEducationProgramPDF'),
        meta: {
          requiresAuth: true,
          accessIsAllowed: allRoles(),
          header: 'PDF Освітньої програми',
        },
      },
    ],
  },
  {
    path: '/plan',
    component: Layout,
    children: [
      {
        path: 'create',
        name: 'CreatePlan',
        component: CreatePlan,
        meta: {
          requiresAuth: true,
          accessIsAllowed: [
            ROLES.ID.admin,
            ROLES.ID.root,
            ROLES.ID.training_department,
            ROLES.ID.practice_department,
            ROLES.ID.educational_department_deputy,
            ROLES.ID.educational_department_chief,
            ROLES.ID.admin_department_postgraduate,
          ],
          header: 'Створення нового плану',
          breadCrumb: [
            {
              text: 'Робота з планами',
              to: { name: 'ListPlans' },
            },
            {
              text: 'Створення нового плану',
            },
          ],
        },
      },
      {
        path: 'edit/:id/:title',
        name: 'EditPlan',
        component: CreatePlan,
        meta: {
          requiresAuth: true,
          accessIsAllowed: exceptRoles([ROLES.ID.guest]),
          header: 'Редагування плану',
          breadCrumb() {
            const paramToPageB = this.$route.params.title;
            return [{ text: 'Робота з планами', to: { name: 'ListPlans' } }, { text: paramToPageB }];
          },
        },
      },
      {
        path: 'preview/:id/:title',
        name: 'PreviewPlan',
        component: () => import(/* webpackChunkName: "preview-plan" */ '@/views/pages/plan/PreviewPlan'),
        meta: {
          requiresAuth: true,
          accessIsAllowed: allRoles(),
          header: 'Попередній перегляд плану',
          breadCrumb() {
            const params = this.$route.params;
            return [
              { text: 'Робота з планами', to: { name: 'ListPlans' } },
              { text: params.title, to: { name: 'EditPlan', params } },
              { text: 'Попередній перегляд' },
            ];
          },
        },
      },
    ],
  },
  {
    path: '/settings',
    component: Layout,
    children: [
      {
        path: '',
        name: 'Settings',
        component: () => import(/* webpackChunkName: "settings" */ '@/views/pages/settings/Settings'),
        meta: {
          accessIsAllowed: [ROLES.ID.admin, ROLES.ID.root, ROLES.ID.faculty_institute],
          header: 'Налаштування',
          breadCrumb: [{ text: 'Робота з планами', to: { name: 'ListPlans' } }, { text: 'Налаштування' }],
        },
      },
      {
        path: 'users',
        name: 'SettingUsers',
        component: () => import(/* webpackChunkName: "setting-users" */ '@/views/pages/settings/Users/Users'),
        meta: {
          requiresAuth: true,
          accessIsAllowed: [ROLES.ID.admin, ROLES.ID.root, ROLES.ID.faculty_institute],
          header: 'Налаштування користувачів',
          breadCrumb: [...BREADCRUMBS.SETTINGS, { text: 'Налаштування користувачів' }],
        },
      },
      {
        path: 'form-study',
        name: 'FormStudy',
        component: () => import(/* webpackChunkName: "form-study" */ '@/views/pages/settings/FormStudy'),
        meta: {
          requiresAuth: true,
          accessIsAllowed: [ROLES.ID.admin, ROLES.ID.root],
          header: 'Форма навчання',
          breadCrumb: [...BREADCRUMBS.SETTINGS, { text: 'Форма навчання' }],
        },
      },
      {
        path: 'form-organizations',
        name: 'FormOrganization',
        component: () =>
          import(
            /* webpackChunkName: "form-organizations" */
            '@/views/pages/settings/FormOrganization'
          ),
        meta: {
          requiresAuth: true,
          accessIsAllowed: [ROLES.ID.admin, ROLES.ID.root],
          header: 'Форма організації навчання',
          breadCrumb: [...BREADCRUMBS.SETTINGS, { text: 'Форма організації навчання' }],
        },
      },
      {
        path: 'restriction-editor',
        component: () => import('@/views/SimpleLayout'),
        children: [
          {
            path: '',
            name: 'RestrictionEditor',
            component: () =>
              import(
                /* webpackChunkName: "restriction-editor" */
                '@/views/pages/settings/RestrictionEditor'
              ),
            meta: {
              requiresAuth: true,
              accessIsAllowed: [ROLES.ID.admin, ROLES.ID.root],
              header: 'Редактор обмежень',
              breadCrumb: [...BREADCRUMBS.SETTINGS, { text: 'Редактор обмежень' }],
            },
          },
          {
            path: 'create',
            name: 'RestrictCreate',
            component: () =>
              import(/* webpackChunkName: "restrict-create" */ '@/views/pages/settings/RestrictionEditor/create'),
            meta: {
              requiresAuth: true,
              accessIsAllowed: [ROLES.ID.admin, ROLES.ID.root],
              header: 'Додавання налаштувань',
              breadCrumb: [
                ...BREADCRUMBS.SETTINGS,
                {
                  text: 'Редактор обмежень',
                  to: { name: 'RestrictionEditor' },
                },
                { text: 'Додавання налаштувань' },
              ],
            },
          },
        ],
      },

      {
        path: 'study-term',
        name: 'StudyTerm',
        component: () => import(/* webpackChunkName: "study-term" */ '@/views/pages/settings/StudyTerm'),
        meta: {
          requiresAuth: true,
          accessIsAllowed: [ROLES.ID.admin, ROLES.ID.root],
          header: 'Термін навчання',
          breadCrumb: [...BREADCRUMBS.SETTINGS, { text: 'Термін навчання' }],
        },
      },
      {
        path: 'subject-languages',
        name: 'SubjectLanguage',
        component: () => import(/* webpackChunkName: "subject-language" */ '@/views/pages/settings/SubjectLanguage'),
        meta: {
          requiresAuth: true,
          accessIsAllowed: [ROLES.ID.admin, ROLES.ID.root],
          header: 'Мова викладання',
          breadCrumb: [...BREADCRUMBS.SETTINGS, { text: 'Мова викладання' }],
        },
      },
      {
        path: 'catalog-groups',
        name: 'CatalogGroup',
        component: () => import(/* webpackChunkName: "catalog-group" */ '@/views/pages/settings/CatalogGroup'),
        meta: {
          requiresAuth: true,
          accessIsAllowed: [ROLES.ID.admin, ROLES.ID.root],
          header: 'Група дисциплін',
          breadCrumb: [...BREADCRUMBS.SETTINGS, { text: 'Група дисциплін' }],
        },
      },
      {
        path: 'education-level',
        name: 'EducationLevel',
        component: () => import(/* webpackChunkName: "education-level" */ '@/views/pages/settings/EducationLevel'),
        meta: {
          requiresAuth: true,
          accessIsAllowed: [ROLES.ID.admin, ROLES.ID.root],
          header: 'Рівень знань',
          breadCrumb: [...BREADCRUMBS.SETTINGS, { text: 'Рівень знань' }],
        },
      },
      {
        path: 'position',
        name: 'Position',
        component: () => import(/* webpackChunkName: "position" */ '@/views/pages/settings/Position'),
        meta: {
          requiresAuth: true,
          accessIsAllowed: [ROLES.ID.admin, ROLES.ID.root],
          header: 'Посади',
          breadCrumb: [...BREADCRUMBS.SETTINGS, { text: 'Посади' }],
        },
      },
      {
        path: 'notes',
        name: 'Note',
        component: () => import(/* webpackChunkName: "note" */ '@/views/pages/settings/Note'),
        meta: {
          requiresAuth: true,
          accessIsAllowed: [ROLES.ID.admin, ROLES.ID.root],
          header: 'Примітки',
          breadCrumb: [...BREADCRUMBS.SETTINGS, { text: 'Примітки' }],
        },
      },
      {
        path: 'list-cycles',
        name: 'ListCycle',
        component: () => import(/* webpackChunkName: "list-cycle" */ '@/views/pages/settings/ListCycle'),
        meta: {
          requiresAuth: true,
          accessIsAllowed: [ROLES.ID.admin, ROLES.ID.root],
          header: 'Цикли',
          breadCrumb: [...BREADCRUMBS.SETTINGS, { text: 'Цикли' }],
        },
      },
      {
        path: 'catalog-helpers',
        name: 'CatalogHelpers',
        component: () =>
          import(/* webpackChunkName: "catalog-helpers"*/ '@/views/pages/settings/Helpers/SubjectHelpers'),
        meta: {
          requiresAuth: true,
          accessIsAllowed: [ROLES.ID.admin, ROLES.ID.root],
          header: 'Підказки',
          breadCrumb: [...BREADCRUMBS.SETTINGS, { text: 'Підказки' }],
        },
      },
      {
        path: 'logs',
        name: 'Logs',
        component: () => import(/* webpackChunkName:  "logs" */ '@/views/pages/settings/Logs'),
        meta: {
          requiresAuth: true,
          accessIsAllowed: [ROLES.ID.admin, ROLES.ID.root],
          header: 'Активності',
          breadCrumb: [...BREADCRUMBS.SETTINGS, { text: 'Активності' }],
        },
      },
      {
        path: 'handbook',
        name: 'HandbookUpload',
        component: () => import(/* webpackChunkName: "handbook" */ '@/views/pages/settings/Helpers/Handbook'),
        meta: {
          requiresAuth: true,
          accessIsAllowed: allRoles(),
          header: 'Довідник',
          breadCrumb: [...BREADCRUMBS.SETTINGS, { text: 'Довідник' }],
        },
      },
    ],
    // route level code-splitting
    // this generates a separate chunk (about.[hash].js) for this route
    // which is lazy-loaded when the route is visited.
  },
  {
    path: '*',
    name: 'NotFoundPage',
    component: () => import(/* webpackChunkName: "not-found" */ '@/views/NotFoundPage'),
    meta: { guest: true },
  },
  {
    path: '/503',
    name: 'MaintenanceMode',
    component: () => import(/* webpackChunkName: "maintenance-mode"*/ '@/views/MaintenanceMode'),
    meta: { guest: true },
  },
  {
    path: '/403',
    name: 'Forbidden',
    component: () => import(/* webpackChunkName: "forbidden" */ '@/views/Forbidden'),
    meta: { guest: true },
  },
  {
    path: '/401',
    name: 'Unauthorized',
    component: () => import(/* webpackChunkName: "unauthorized" */ '@/views/Unauthorized'),
    meta: { guest: true },
  },
];

const router = new VueRouter({
  mode: 'history',
  base: process.env.BASE_URL,
  routes,
});

const getUserRoleId = async () => {
  let userRoleId = store.getters['auth/user'];

  // if (userRoleId === null) {
  await store.dispatch('auth/getUserData');
  userRoleId = store.getters['auth/user'];
  // }
  return userRoleId.role_id;
};

// this method on to get cabinet token
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
    }
  }

  next();
});

// this method to check user and role_id
router.beforeEach(async (to, from, next) => {
  const guest = to.matched.some((record) => record.meta.guest);

  if (!guest) {
    const accessIsAllowed = to.meta.accessIsAllowed;
    const userRoleId = store.getters['auth/user'].role_id;

    if (accessIsAllowed !== undefined) {
      if (accessIsAllowed.includes(userRoleId)) {
        next();
      } else {
        next({ name: 'Forbidden' });
      }
    } else {
      if (!guest) {
        next({ name: 'Forbidden' });
      }
      next();
    }
  }
  next();
});

export default router;
