<template>
  <div></div>
</template>

<script>

export default {
  name: 'Dashboard',
  components: {},
  created() {
    this.$store.dispatch('retrieveUser')
  }
}
</script>