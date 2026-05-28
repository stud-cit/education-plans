import { ROLES } from '@/utils/constants';

export default {
  getItems(user) {
    // item
    const ListPlans = {
      title: 'Плани',
      icon: 'mdi-account-circle',
      route: 'ListPlans',
    };
    const SelectiveDisciplines = {
      title: 'Вибіркові дисципліни',
      route: 'SelectiveDisciplines',
    };
    const Settings = {
      title: 'Налаштування',
      icon: 'mdi-account-plus',
      route: 'Settings',
    };

    let rows = [];
    if (user) {
      switch (user.role_id) {
        case ROLES.ID.admin:
        case ROLES.ID.root:
        case ROLES.ID.faculty_institute:
          rows.push(ListPlans, SelectiveDisciplines, Settings);
          break;
        case ROLES.ID.training_department:
        case ROLES.ID.practice_department:
        case ROLES.ID.educational_department_deputy:
        case ROLES.ID.educational_department_chief:
        case ROLES.ID.department:
        case ROLES.ID.admin_department_postgraduate:
          rows.push(ListPlans, SelectiveDisciplines);
          break;
      }
    }

    return rows;
  },
};
