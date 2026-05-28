<template>
  <v-container>
    <div class="settings">
      <template v-for="item in items">
        <v-card v-if="item.allowed" class="d-flex flex-column justify-space-between" :key="item.title" elevation="2">
          <v-card-text>
            <p class="text-h6 text--primary text-center">{{ item.title }}</p>
          </v-card-text>
          <v-card-actions>
            <v-btn depressed block :to="{ name: item.to }"> Налаштувати </v-btn>
          </v-card-actions>
        </v-card>
      </template>
    </div>
  </v-container>
</template>
<script>
import RolesMixin from '@/mixins/RolesMixin';

export default {
  name: 'Settings',
  data() {
    return {
      items: [],
      nameRoutes: [
        'RestrictionEditor',
        'StudyTerm',
        'FormStudy',
        'FormOrganization',
        'EducationLevel',
        'SettingUsers',
        'Position',
        'Note',
        'ListCycle',
        'CatalogHelpers',
        'SubjectLanguage',
        'CatalogGroup',
        'Logs',
        'HandbookUpload'
      ],
    };
  },
  mixins: [RolesMixin],
  mounted() {
    this.items = this.getItems();
  },
  methods: {
    getItems() {
      return this.$router
        .getRoutes()
        .filter((item) => this.nameRoutes.includes(item.name))
        .map((item) => {
          return { title: item.meta.header, to: item.name, allowed: this.allowedRoles(item.meta.accessIsAllowed) };
        });
    },
  },
};
</script>

<style scoped>
.settings {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(260px, 1fr));
  gap: 20px;
}
</style>
