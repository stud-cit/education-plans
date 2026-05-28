<template>
  <div>
    <v-row :class="['cycle-subject', 'cycle', cycleIndexError == item.id ? 'error' : '', 'ma-0', 'mb-1']">
      <v-col cols="8" class="pa-0">
        <select :disabled="cycleIndex != item.id" v-model="item.list_cycle_id">
          <option style="color: black" :value="cycle.id" v-for="cycle in listCycles" :key="cycle.id">
            {{ cycle.title }}
          </option>
        </select>
      </v-col>
      <v-col cols="1" class="pa-0">
        <v-tooltip bottom>
          <template v-slot:activator="{ on, attrs }">
            <input type="checkbox" v-bind="attrs" v-on="on" :disabled="cycleIndex != item.id"
              v-model="item.has_discipline" />
          </template>
          <span>Цикл з навчальними дисциплінами</span>
        </v-tooltip>
      </v-col>
      <v-col cols="1" class="pa-0">
        <input type="number" min="0" class="credits" :disabled="cycleIndex != item.id" v-model="item.credit"
          @input="checkCredit(parentItem, item)" />
      </v-col>
      <v-col cols="1" class="pa-0"></v-col>
      <v-col class="pa-0 text-right">
        <v-tooltip bottom v-if="cycleIndex != item.id">
          <template v-slot:activator="{ on, attrs }">
            <v-btn :disabled="readOnly || isShortPlan" small icon @click="cycleIndex = item.id" v-bind="attrs"
              v-on="on">
              <v-icon>mdi-pencil</v-icon>
            </v-btn>
          </template>
          <span>Редагувати</span>
        </v-tooltip>
        <v-tooltip bottom v-else>
          <template v-slot:activator="{ on, attrs }">
            <v-btn small icon @click="saveCycle(item)" :disabled="item.title == ''" v-bind="attrs" v-on="on">
              <v-icon>mdi-floppy</v-icon>
            </v-btn>
          </template>
          <span>Зберегти</span>
        </v-tooltip>
        <v-tooltip bottom v-if="allowedRoles([ROLES.ID.admin, ROLES.ID.root, ROLES.ID.admin_department_postgraduate])">
          <template v-slot:activator="{ on, attrs }">
            <v-btn :disabled="readOnly || isShortPlan" small icon @click="delCycle(item)" v-bind="attrs" v-on="on">
              <v-icon>mdi-delete</v-icon>
            </v-btn>
          </template>
          <span>Видалити</span>
        </v-tooltip>
        <v-menu bottom right>
          <template v-slot:activator="{ on: menu, attrs }">
            <v-tooltip bottom>
              <template v-slot:activator="{ on: tooltip }">
                <v-btn :disabled="readOnly || isShortPlan" icon small v-bind="attrs" v-on="{ ...tooltip, ...menu }">
                  <v-icon>mdi-plus</v-icon>
                </v-btn>
              </template>
              <span>Додати</span>
            </v-tooltip>
          </template>
          <v-list>
            <v-list-item link
              v-if="allowedRoles([ROLES.ID.admin, ROLES.ID.root, ROLES.ID.admin_department_postgraduate])">
              <v-list-item-title @click="addCycle(item)">Цикл</v-list-item-title>
            </v-list-item>
            <v-list-item link>
              <v-list-item-title @click="addSubject(item)">Дисципліна</v-list-item-title>
            </v-list-item>
          </v-list>
        </v-menu>
      </v-col>
    </v-row>

    <v-row class="cycle-subject head ma-0 mb-1" v-if="item.subjects && item.subjects.length > 0">
      <v-col cols="5" class="pa-0 text-left"> Назва предмету </v-col>
      <v-col class="pa-0"> Лекцій </v-col>
      <v-col class="pa-0"> Практичних </v-col>
      <v-col class="pa-0"> Лабораторних </v-col>
      <v-col class="pa-0"> Сума годин </v-col>
      <v-col class="pa-0"> Кредитів </v-col>
      <v-col class="pa-0"> Екзамени в семестрах </v-col>
      <v-col class="pa-0"></v-col>
    </v-row>

    <SubjectItem v-for="(subject, subjectIndex) in item.subjects" :key="'subject' + subjectIndex + indexComponent"
      :subject="subject" :item="item" />

    <CycleItem :parentItem="item" v-bind:item="child" :index="subIndex" :indexComponent="indexComponent"
      :cycles="cycles" v-for="(child, subIndex) in item.cycles" :key="'cycle_' + child.id + indexComponent"
      @addCycle="addCycle" @addSubject="addSubject" @saveCycle="saveCycle" @editCycle="editCycle"
      @delCycle="delCycle" />
  </div>
</template>
<script>
import SubjectItem from '@/views/pages/plan/tabs/SubjectItem.vue';

import { ROLES } from '@/utils/constants';
import RolesMixin from '@/mixins/RolesMixin';
import { mapGetters } from 'vuex';

export default {
  name: 'CycleItem',
  components: {
    SubjectItem,
  },
  props: {
    item: {
      type: Object,
      required: true,
    },
    index: {
      type: Number,
      required: true,
    },
    indexComponent: {
      type: Number,
      required: true,
    },
    parentItem: {
      required: false,
    },
    data: {
      type: Object,
      required: false,
    },
    cycles: {
      type: Array,
      required: true,
    },
  },
  mixins: [RolesMixin],
  data() {
    return {
      ROLES,
      cycleIndex: null,
      cycleIndexError: null,
    };
  },
  mounted() {
    this.checkCredit(this.parentItem, this.item);
  },
  computed: {
    authUser() {
      return JSON.parse(localStorage.getItem('user'));
    },
    listCycles() {
      return this.cycles.filter((cycle) => {
        if (this.item.list_cycle.general) {
          return cycle.general;
        } else {
          return !cycle.general;
        }
      });
    },
    ...mapGetters({ isShortPlan: 'plans/isShortPlan', 'readOnly': 'plans/readOnly' }),
  },
  methods: {
    checkCredit(parentItem, item) {
      if (parentItem) {
        this.checkCreditMethod(
          parentItem,
          item,
          parentItem.credit,
          'Перевищена кількість кредитів в циклі ' + parentItem.title + ': ',
        );
      }
      // else {
      //   this.checkCreditMethod(
      //     this.data,
      //     item,
      //     this.data.credits,
      //     "Перевищена загальна кількість кредитів: "
      //   );
      // }
    },
    checkCreditMethod(data, item, limitCredit, message) {
      var cycles = data.cycles;
      var credints = [];
      (function flat(cycles) {
        cycles.forEach(function (el) {
          if (Array.isArray(el)) flat(el);
          else credints.push(el.credit);
        });
      })(cycles);
      let sumCredits = credints.reduce((prev, curr) => +prev + +curr, 0);
      if (limitCredit < sumCredits) {
        this.cycleIndexError = item.id;
        if (this.cycleIndex == null) {
          this.$store.dispatch('plans/setErrorsPlan', message + sumCredits + ' із ' + limitCredit);
        }
      } else {
        this.cycleIndexError = null;
        this.$store.dispatch('plans/setErrorsPlan', null);
      }
    },
    addSubject(item) {
      this.$emit('addSubject', item);
    },
    addCycle(item) {
      this.$emit('addCycle', item);
    },
    editCycle(item) {
      this.$emit('editCycle', item);
    },
    saveCycle(item) {
      this.$emit('saveCycle', item);
      this.cycleIndex = null;
    },
    delCycle(item) {
      this.$emit('delCycle', item);
    },
  },
};
</script>
<style>
.cycle-subject {
  width: 100%;
  padding: 5px 10px 5px 10px;
  background: #fff;
  box-shadow: 0px 1px 2px #dedede;
  border-radius: 5px;
  margin-bottom: 2px;
  font-size: 14px;
  text-align: center;
  display: flex;
  align-items: center;
}

.cycle-subject.error,
.cycle-subject.error input,
.cycle-subject.error select,
.cycle-subject.error i {
  color: #fff !important;
}

.cycle-subject select:disabled {
  color: #000 !important;
}

.cycle-subject.error select:disabled {
  color: #fff !important;
}

.cycle-subject input,
.cycle-subject select {
  width: 90%;
  text-align: center;
}

.cycle-subject.head {
  text-align: center;
  color: #9b9b9b;
}

.cycle-subject.cycle {
  background: #f8f8f8;
}

input.credits {
  padding-left: 14px;
}
</style>
