<template>
  <div>
    <h1>err test</h1>
  </div>
</template>

<script>
import api from '@/api';
import { API } from '@/api/constants-api';

export default {
  name: 'Err',
  created() {
    this.apiFromStudies().then((response) => console.log(response));
  },
  methods: {
    apiFromStudies() {
      return api.get(API.ERR);
    }
  }
}
</script>

