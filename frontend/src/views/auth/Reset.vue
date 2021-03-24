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
                <h4>Gendan adgangskode</h4>
                <CAlert :show="badrequest"
                        color="danger"
                >
                  Et eller flere af felterne indeholder en fejl.
                  <li v-for="error in errors">{{ error }}</li>
                </CAlert>

                <CInput v-model="email"
                        placeholder="E-mail"
                        autocomplete="email"
                        prepend="@"
                />
                <CInput v-model="password"
                        placeholder="Ny adgangskode"
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
                  <CButton color="primary" class="px-4" id="resetbtn" v-on:click="reset">Gendan</CButton>
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
      email: '',
      password: '',
      repeatPassword: '',
      errors: [],
      badrequest: false,
      resetSuccess: false
    }
  },
  methods: {
    reset() {
      // Starter reset spinner
      this.isLoading = true

      // Sætter errors kollektionen til tom
      this.errors = []

      // Henter resettoken fra URL'en
      let token = this.$route.query.resettoken

      // Tjekker op på om de indtastede oplysninger er valide
      if (this.email && this.password && this.validatePassword(this.password, this.repeatPassword) && this.validateLength(this.password)) {
        // Sætter badrequest til false
        this.badrequest = false

        // Trigger reset action i store.js og sender oplysningerne med samt resettoken
        this.$store.dispatch('reset', {
          email: this.email,
          token: token,
          password: this.password,
          password_confirmation: this.repeatPassword
        })
            .then(response => {
              // Sætter successfully til true
              this.successfully = true

              // Trigger resetSuccess action i store.js og sender resetSuccess med
              this.$store.dispatch('resetSuccess', this.resetSuccess)

              // Pusher til login view
              this.$router.push({name: 'login'})
            })
            .catch(error => {
              // Sætter badrequest til true
              this.badrequest = true

              // Stopper spinner
              this.isLoading = false

              // Pusher fejlmeddelelse til errors kollektionen
              this.errors.push('Prøv igen.')
            })
      } else {
        // Trigger resetSuccess action i store.js og sender resetSuccess med
        this.$store.dispatch('resetSuccess', this.resetSuccess)

        // Sætter badrequest til true
        this.badrequest = true

        // Stopper spinner
        this.isLoading = false
      }

      // Hvis badrequest er true eksekver indeholdet af if sætningen
      if (this.badrequest) {
        this.isLoading = false
        this.errors.push('Prøv igen.')
      }
      // Hvis email ikke er indtastet, push fejlmeddelelse til errors kollektionen
      if (!this.email) {
        this.errors.push('Email påkrævet.')
      }
      // Hvis email ikke er valid, push fejlmeddelelse til erros kollektionen
      else if (!this.validEmail(this.email)) {
        this.errors.push('Ugyldig email.')
      }
      // Hvis adgangskode ikke er indtastet, push fejlmeddelelse til errors kollektionen
      if (!this.password) {
        this.errors.push('Adgangskode påkrævet.')
      }
      // Hvis adgangskoden ikke er valid, push fejlmeddelse til errors kollektionen
      if (!this.validateLength(this.password)) {
        this.errors.push('Adgangskoden skal minimum indeholde 6 tegn.')
      } else if (!this.validatePassword(this.password, this.repeatPassword)) {
        this.errors.push('Adgangskoderne stemmer ikke overens.')
      }
    },
    validEmail(email) {
      // Symbolerne der skal tjekkes op på
      var re = /^(([^<>()[\]\\.,;:\s@"]+(\.[^<>()[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;

      // Tjekker op på alt efter om mailen indeholder symbolerne og returnerer enten true eller false
      return re.test(email)
    },
    validatePassword(password, repeatPassword) {
      // Tjekker op på om de indtastede adgangskoder stemmer overens og returnerer true eller false
      if (password === repeatPassword) {
        return true
      } else {
        return false
      }
    },
    validateLength(password) {
      // Tjekker op på at længden af adgangskoden er mindre end 6 og returnerer false ellers returners true
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

#resetbtn {
  background-color: #29BB9C;
  border-color: transparent !important;
}
</style>