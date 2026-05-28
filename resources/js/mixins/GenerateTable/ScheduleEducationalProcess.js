export default {
  components: {},
  data() {
    return {
      year: {
        weeks: 0,
        header: [],
        courses: []
      },
      month: [
        'Січень', 'Лютий', 'Березень',
        'Квітень', 'Травень', 'Червень',
        'Липень', 'Серпень', 'Вересень',
        'Жовтень', 'Листопад', 'Грудень'
      ]
    }
  },
  methods: {
    generateTable(plan) {
      for (let index = 0; index < plan.study_term.course; index++) {
        this.year.courses.push([]);
      }

      for (let index = 0; index < 12; index++) {
        const date = new Date(plan.year, 9 + index, 0);
        const countWeek = this.getWeeks(date.getFullYear(), date.getMonth(), 0);
        this.year.weeks += countWeek;

        this.year.header.push({
          monthTitle: this.month[date.getMonth()],
          countWeek
        });

        for (let course = 0; course < this.year.courses.length; course++) {
          for (let countWeekIndex = 0; countWeekIndex < countWeek; countWeekIndex++) {
            this.year.courses[course].push({
              month: index,
              course,
              val: ""
            });
          }
        }
      }
    },

    getWeeks(year, month) {
      var l=new Date(year, month+1, 0);
      var result = Math.floor( (l.getDate()- (l.getDay()?l.getDay():7))/7+1);
      return result;
    },
  }
}
