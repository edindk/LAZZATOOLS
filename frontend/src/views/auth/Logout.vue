<template>
</template>

<script>
export default {
  name: "Logout",
  created() {
    // Trigger destroyToken action i store.js og pusher til login view
    this.$store.dispatch('destroyToken')
        .then(response => {
          this.$router.push({name: 'login'})
        })
  }
}
</script>
