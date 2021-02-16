<template>
  <div class="d-flex align-items-center min-vh-100">
    <CContainer fluid>
      <CRow class="justify-content-center">
        <CCol md="6">
          <loading :active.sync="isLoading"
                   :can-cancel="true"
                   :is-full-page="fullPage"
                   :color="color"
          >
          </loading>
          <CCard class="mx-4 mb-0">
            <CCardBody class="p-4">
              <CForm>
                <h4>Registrer</h4>
                <CAlert :show="badrequest"
                        color="danger"
                >
                  Et eller flere af felterne indeholder en fejl.
                  <li v-for="error in errors">{{ error }}</li>
                </CAlert>

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
                <CInput v-model="repeatPassword"
                        placeholder="Gentag adgangskode"
                        type="password"
                        autocomplete="new-password"
                >

                  <template #prepend-content>
                    <CIcon name="cil-lock-locked"/>
                  </template>
                </CInput>
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
import Loading from 'vue-loading-overlay'
import 'vue-loading-overlay/dist/vue-loading.css'

export default {
  name: 'Register',
  components: {
    Loading
  },
  data() {
    return {
      isLoading: false,
      fullPage: true,
      color: '#216A90',
      name: '',
      email: '',
      password: '',
      repeatPassword: '',
      errors: [],
      badrequest: false,
      successfullyRegistered: false
    }
  },
  methods: {
    register() {
      this.isLoading = true
      this.errors = []

      if (this.name && this.email && this.password && this.validatePassword(this.password, this.repeatPassword) && this.validateLength(this.password)) {
        this.badrequest = false

        this.$store.dispatch('register', {
          name: this.name,
          email: this.email,
          password: this.password
        })
            .then(response => {
              this.successfullyRegistered = true
              this.$store.dispatch('successfullyRegistered', this.successfullyRegistered)
              this.$router.push({name: 'login'})
            })
      } else {
        this.$store.dispatch('successfullyRegistered', this.successfullyRegistered)
        this.badrequest = true
        this.isLoading = false
      }
      if (!this.name) {
        this.errors.push('Navn påkrævet.')
      }
      if (!this.email) {
        this.errors.push('Email påkrævet.')
      } else if (!this.validEmail(this.email)) {
        this.errors.push('Ugyldig email.')
      }
      if (!this.password) {
        this.errors.push('Adgangskode påkrævet.')
      }
      if (!this.validateLength(this.password)) {
        this.errors.push('Adgangskoden skal minimum indeholde 6 tegn.')
      } else if (!this.validatePassword(this.password, this.repeatPassword)) {
        this.errors.push('Adgangskoderne stemmer ikke overens.')
      }
    },
    validEmail(email) {
      var re = /^(([^<>()[\]\\.,;:\s@"]+(\.[^<>()[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
      return re.test(email)
    },
    validatePassword(password, repeatPassword) {
      if (password === repeatPassword) {
        return true
      } else {
        return false
      }
    },
    validateLength(password) {
      if (password.length < 6) {
        return false
      } else {
        return true
      }
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