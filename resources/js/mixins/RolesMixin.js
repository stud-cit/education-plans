import {mapGetters} from "vuex";
export default {
  components: {},
  data() {
    return {}
  },
  computed: {
    ...mapGetters({
      user: 'auth/user'
    })
  },
  methods: {
    allowedRoles(ids) {
      return checkRoles(ids, this.user)
    },
    exceptRoles(ids) {
      return !checkRoles(ids, this.user)
    },
  }
}
function checkRoles(ids, user) {
  if (user === null || user === undefined || !user.role_id) {
    return false;
  }
  return ids.includes(user.role_id);
}
