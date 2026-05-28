import { mapGetters } from 'vuex';
import { ROLES } from '@/utils/constants';
export default {
  components: {},
  data() {
    return {};
  },
  computed: {
    ...mapGetters({
      user: 'auth/user',
    }),
  },
  methods: {
    itemRowBackground(item) {
      if (this.allowedRoles([ROLES.ID.guest])) {
        return '';
      }

      if (item.deleted_at) {
        return 'blue-grey lighten-5';
      } 
      
      const role = ROLES.ID.educational_department_deputy;
      if (this.allowedRoles([ROLES.ID.admin]) || this.allowedRoles([ROLES.ID.root])) {
        return item.status + ' lighten-5';
      } else {
        if (
          item.need_verification == 1 &&
          !item.user_verifications.find((i) => i.role_id == this.user.role_id || i.role_id == role)
        ) {
          return 'warning lighten-5';
        }
        if (
          item.user_verifications.find((i) => (i.role_id == this.user.role_id || i.role_id == role) && i.status == 0)
        ) {
          return 'error lighten-5';
        }
        if (
          item.need_verification == 1 &&
          item.user_verifications.find((i) => (i.role_id == this.user.role_id || i.role_id == role) && i.status == 1)
        ) {
          return 'success lighten-5';
        }
      }
    },
  },
};
