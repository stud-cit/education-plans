<template>
  <div>
    <v-navigation-drawer
      fixed
      left
      temporary
      :value="panelOpen"
      @input="
        (v) => {
          toggle(v);
        }
      "
      width="360"
    >
      <template v-slot:prepend>
        <v-list-item two-line>
          <v-list-item-content>
            <v-list-item-title>Панель розробника</v-list-item-title>
          </v-list-item-content>
        </v-list-item>
      </template>

      <v-divider></v-divider>

      <v-list dense>
        <v-list-item>
          <v-select
            v-if="user && roles"
            v-model="user.role_id"
            :items="roles"
            append-outer-icon="mdi-account-group-outline"
            hide-details
            item-value="id"
            item-text="label"
            label="Роль"
            @change="apiSetRole"
          ></v-select>
        </v-list-item>
        <v-btn class="mt-4 ml-4" color="primary" @click="apply"> Застосувати </v-btn>
      </v-list>
    </v-navigation-drawer>
  </div>
</template>

<script>
import api from '@/api';
import { API } from '@/api/constants-api';
import RolesMixin from '@/mixins/RolesMixin';
import { ROLES } from '@/utils/constants';
import { mapGetters, mapActions } from 'vuex';

export default {
  name: 'DevelopmentSettings',
  data: () => ({
    ROLES,
    states: null,
  }),
  mixins: [RolesMixin],
  computed: {
    ...mapGetters({
      panelOpen: 'developmentSettings/panelOpen',
      roles: 'developmentSettings/roles',
      user: 'auth/user',
    }),
  },
  mounted() {
    this.apiGetRoles();
  },
  methods: {
    ...mapActions({
      toggle: 'developmentSettings/toggle',
      apiGetRoles: 'developmentSettings/setRoles',
    }),
    apiSetRole(id) {
      api.patch(API.SET_USER_ROLE, this.user.id, { role_id: id });
    },
    apply() {
      location.reload();
    },
  },
};
</script>

<style scoped></style>
