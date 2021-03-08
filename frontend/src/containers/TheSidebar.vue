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

    <CRenderFunction v-if="this.showFullSidebar" flat :content-to-render="$options.nav"/>
    <CRenderFunction v-else flat :content-to-render="$options.navGuest"/>
    <CSidebarMinimizer id="sidebarFooter"
                       class="d-md-down-none"
                       @click.native="$store.commit('set', ['sidebarMinimize', !minimize])"
    />
  </CSidebar>
</template>

<script>
import nav from './_nav'
import navGuest from './_navGuest'

export default {
  name: 'TheSidebar',
  nav,
  navGuest,
  data() {
    return {
      showFullSidebar: false
    }
  },
  computed: {
    show() {
      if (this.$store.getters.userDetails.email.split('@')[1] === 'lazzaweb.dk' && this.$store.getters.userDetails.email_verified_at) {
        this.showFullSidebar = true
      }

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

#sidebarMinimizer {
  background-color: black;
}
</style>