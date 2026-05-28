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
            <v-list-item-title class="text-center font-weight-bold">Довідка</v-list-item-title>
          </v-list-item-content>
        </v-list-item>
      </template>

      <v-divider></v-divider>

      <v-list dense>
        <v-list-item>
          <v-chip
            label
            color="success lighten-5"
          ></v-chip>
          <span>
            &nbsp;&#9472;&nbsp;
            Успіх, верифіковано
          </span>
        </v-list-item>
        <v-list-item>
          <v-chip
            label
            color="warning lighten-5"
          ></v-chip>
          <span>
            &nbsp;&#9472;&nbsp;
            Очікується, проходить верифікацію
          </span>
        </v-list-item>
        <v-list-item>
          <v-chip
            label
            color="error lighten-5"
          ></v-chip>
          <span>
            &nbsp;&#9472;&nbsp;
            Помилка, не верифіковано
          </span>
        </v-list-item>
        <v-list-item>
          <div class="mx-auto font-weight-bold ">
            Позначки
          </div>
        </v-list-item>

        <v-list-item v-for="(icon, index) in icons" :key="'icon_' + index">
          <v-icon :color="mdi.color" v-for="(mdi, i) in icon.icons" :key="'mdi_' + i" v-html="mdi.icon"></v-icon>
          <span>
            &nbsp;&#9472;&nbsp;
            {{ icon.label }}
          </span>
        </v-list-item>
<!--        <v-list-item>-->

<!--          <span>-->
<!--            &nbsp;&#9472;&nbsp;-->

<!--          </span>-->
<!--        </v-list-item>-->
        <v-list-item>
          <v-btn class="mt-4 mx-auto" color="primary" :to="{ name: 'HandbookUpload' }"> Детальніше </v-btn>
        </v-list-item>

        <v-list-item v-if="feedbackUrl">
          <v-btn class="mt-4 mx-auto" color="warning" :href="feedbackUrl" target="_blank"> Зворотній зв'язок </v-btn>
        </v-list-item>
      </v-list>

    </v-navigation-drawer>
  </div>
</template>

<script>

import { mapGetters, mapActions } from 'vuex';

export default {
  name: 'Helper',
  data: () => ({
    states: null,
    feedbackUrl: process.env.VUE_APP_FEEDBACK,
    icons: [
      {
        icons: [
          {icon: 'mdi-pencil-outline', color: 'primary'},
          {icon: 'mdi-square-edit-outline', color: 'primary'},
        ],
        label: 'Редагувати'
      },
      {
        icons: [
          {icon: 'mdi-content-copy', color: 'primary'}
        ],
        label: 'Скопіювати'
      },
      {
        icons: [
          {icon: 'mdi-eye', color: 'primary'}
        ],
        label: 'Перегляд'
      },
      {
        icons: [
          {icon: 'mdi-trash-can-outline', color: 'error'},
          {icon: 'mdi-archive-arrow-down-outline', color: 'error'},
        ],
        label: 'Видалити/архівувати'
      },
      {
        icons: [
          {icon: 'mdi-plus', color: 'primary'},
        ],
        label: 'Створити/додати'
      },
      {
        icons: [
          {icon: 'mdi-cog-outline', color: ''},
        ],
        label: 'Налаштування'
      },
      {
        icons: [
          {icon: 'mdi-cloud-download-outline', color: 'success'},
        ],
        label: 'Завантаження'
      },
      {
        icons: [
          {icon: 'mdi-pdf-box', color: 'error'},
        ],
        label: 'Формат PDF'
      },
      {
        icons: [
          {icon: 'mdi-file-excel', color: 'success'},
        ],
        label: 'Формат Excel'
      },
    ]
  }),
  computed: {
    ...mapGetters({
      panelOpen: 'navbarHelper/panelOpen',
    }),
  },
  methods: {
    ...mapActions({
      toggle: 'navbarHelper/toggle',
    }),

    apply() {
      location.reload();
    },
  },
};
</script>

<style scoped></style>
