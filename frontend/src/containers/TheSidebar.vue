<template>
  <CSidebar id="sidebar"
            fixed
            :minimize="minimize"
            :show="show"
            @update:show="(value) => $store.commit('set', ['sidebarShow', value])"
  >
    <CSidebarBrand class="d-md-down-none" to="/" id="sidebarBrand">
      <img src="../assets/logo/lazzaweb-logo-dark.svg" width="220px">
    </CSidebarBrand>

    <CRenderFunction flat :content-to-render="$options.nav"/>

    <CSidebarMinimizer id="sidebarFooter"
                       class="d-md-down-none"
                       @click.native="$store.commit('set', ['sidebarMinimize', !minimize])"
    />
  </CSidebar>
</template>

<script>
import nav from './_nav'

export default {
  name: 'TheSidebar',
  nav,
  computed: {
    show() {
      return this.$store.state.sidebarShow
    },
    minimize() {
      return this.$store.state.sidebarMinimize
    },
  }
}
</script>
<style>
#sidebarBrand, #sidebarFooter {
  background-color: white;
}
</style>