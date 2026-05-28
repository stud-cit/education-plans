export default {
  components: {},
  data() {
    return {}
  },
  methods: {
    GlobalHandlingRequestParameters(permitted, params) {
      let obj = {};
      for (let param in params) {
        if (Object.prototype.hasOwnProperty.call(permitted, param)) {
          if (params[param] !== '' && params[param] !== null && params[param].lengths !== 0) {
            obj[permitted[param]] = params[param];
          }
        }
      }
      return obj;
    },
    GlobalSumPropertyInArray(items, prop) {
      let res = 0;
      items.forEach((item) => {
        res += item[prop];
        if (item.subjects) {
          item.subjects.forEach((item2) => {
            res += item2[prop];
          });
        }
      });
      return +res.toFixed(2);
    },
    GlobalFakerYears() {
      let years = []
      const limit = 10
      const currentYear = new Date().getFullYear()
      let i = 1;
      while (i < limit) {
        years.push(currentYear + i, currentYear - i)
        i++;
      }
      years.push(currentYear)
      return years.sort();
    },
  }
}
