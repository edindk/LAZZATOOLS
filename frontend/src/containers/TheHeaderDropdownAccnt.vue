<template>
  <CDropdown v-if="loggedIn"
             inNav
             class="c-header-nav-items"
             placement="bottom-end"
             add-menu-classes="pt-0"
  >
    <template #toggler>
      <CHeaderNavLink>
        <div class="c-avatar">
          <CIcon name="cilUser"/>
        </div>
      </CHeaderNavLink>
    </template>
    <CDropdownHeader tag="div" class="text-center" color="light">
      <strong>{{ this.user.name }}</strong>
    </CDropdownHeader>
    <CDropdownItem v-on:click="logout">
      <CIcon name="cil-lock-locked"/>
      Log ud
    </CDropdownItem>
  </CDropdown>
</template>

<script>
import axios from "axios";

export default {
  name: 'TheHeaderDropdownAccnt',
  data() {
    return {
      user: {}
    }
  },
  created() {
    axios.defaults.headers.common['Authorization'] = 'Bearer ' + localStorage.getItem('access_token')
    this.user = this.$store.getters.userDetails
  },
  computed: {
    loggedIn() {
      return this.$store.getters.loggedIn
    }
  },
  methods: {
    logout() {
      this.$router.push({name: 'logout'})
    }
  }
}
</script>

<style scoped>
.c-icon {
  margin-right: 0.3rem;
}
</style>