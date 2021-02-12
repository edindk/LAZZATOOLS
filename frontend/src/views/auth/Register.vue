<template>
  <div class="d-flex align-items-center min-vh-100">
    <CContainer fluid>
      <CRow class="justify-content-center">
        <CCol md="6">
          <CCard class="mx-4 mb-0">
            <CCardBody class="p-4">
              <CForm>
                <h1>Registrer</h1>
                <CInput v-model="name"
                        placeholder="Navn"
                        type="text"
                        autocomplete="new-password"
                >
                  <template #prepend-content>
                    <CIcon name="cil-user"/>
                  </template>
                </CInput>
                <CInput v-model="email"
                        placeholder="E-mail"
                        autocomplete="email"
                        prepend="@"
                />
                <CInput v-model="password"
                        placeholder="Adgangskode"
                        type="password"
                        autocomplete="new-password"
                >
                  <template #prepend-content>
                    <CIcon name="cil-lock-locked"/>
                  </template>
                </CInput>
                <!--                <CInput-->
                <!--                    placeholder="Gentag adgangskode"-->
                <!--                    type="password"-->
                <!--                    autocomplete="new-password"-->
                <!--                    class="mb-4"-->
                <!--                >-->
                <!--                  <template #prepend-content>-->
                <!--                    <CIcon name="cil-lock-locked"/>-->
                <!--                  </template>-->
                <!--                </CInput>-->
                <div class="col text-center">
                  <CButton color="primary" class="px-4" id="registerbtn" v-on:click="register">Opret konto</CButton>
                </div>
              </CForm>
            </CCardBody>
          </CCard>
        </CCol>
      </CRow>
    </CContainer>
  </div>
</template>

<script>
export default {
  name: 'Register',
  data() {
    return {
      name: '',
      email: '',
      password: ''
    }
  },
  methods: {
    register() {
      this.$store.dispatch('register', {
        name: this.name,
        email: this.email,
        password: this.password
      })
          .then(response => {
            this.$router.push({name: 'login'})
          })
    }
  }
}
</script>
<style>
.d-flex {
  background-color: #F8F9FC !important;
}

.form-control:focus {
  outline: none !important;
  border: 1px solid #0FB5C8;
  box-shadow: 0 0 5px #0FB5C8;
}

#registerbtn {
  background-color: #29BB9C;
  border-color: transparent !important;
}

.btn:hover {
  color: lightgray;
}

.btn, .btn:focus, .btn:active {
  outline: none !important;
  box-shadow: none !important;
  color: white;
}

.form-group, p {
  font-family: "Sofia Pro Light";
}
</style>