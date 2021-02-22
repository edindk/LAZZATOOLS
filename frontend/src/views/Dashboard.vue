<template>
  <div>
    <WhoisOptions></WhoisOptions>
  </div>
</template>

<script>

import WhoisOptions from "@/views/whois/WhoisOptions";

export default {
  name: 'Dashboard',
  components: {
    WhoisOptions
  },
  created() {
    this.$store.dispatch('retrieveUser')
  }
}
</script>