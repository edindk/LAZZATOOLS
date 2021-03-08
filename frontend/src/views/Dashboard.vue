<template>
  <div>
    <WhoisOptions v-if="isLazzaEmployee"></WhoisOptions>
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
  },
  computed: {
    isLazzaEmployee() {
      return this.$store.getters.userDetails.email.split('@')[1] === 'lazzaweb.dk' && this.$store.getters.userDetails.email_verified_at
    }
  }
}
</script>