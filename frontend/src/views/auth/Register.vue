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
      // Starter register spinner
      this.isLoading = true

      // Sætter errors kollektionen til tom
      this.errors = []

      // Hvis sandt eksekver indeholdet af if sætningen
      if (this.name && this.email && this.password && this.validatePassword(this.password, this.repeatPassword) && this.validateLength(this.password)) {
        // Sætter badrequest til false
        this.badrequest = false

        // Trigger register action i store.js og sender name, email og password med
        this.$store.dispatch('register', {
          name: this.name,
          email: this.email,
          password: this.password
        })
            .then(response => {
              // Sætter successfullyRegistered til true
              this.successfullyRegistered = true

              // Ændre succes state i store.js til true
              this.$store.commit('storeSuccess', this.successfullyRegistered)

              // Ændre resetSuccess state i store.js til false
              this.$store.commit('storeResetSuccess', false)

              // Pusher til login view
              this.$router.push({name: 'login'})
            })
      }
      // Hvis ikke sandt eksekver else sætningen
      else {
        // Ændre succes state i store.js til false
        this.$store.dispatch('successfullyRegistered', this.successfullyRegistered)

        // Sætter badrequest til true
        this.badrequest = true

        // Stopper register spinner
        this.isLoading = false
      }
      // Hvis navn ikke er indtastet, push fejlmeddelelse til errors kollektionen
      if (!this.name) {
        this.errors.push('Navn påkrævet.')
      }
      // Hvis email ikke er indtastet, push fejlmeddelelse til errors kollektionen
      if (!this.email) {
        this.errors.push('Email påkrævet.')
      }
      // Hvis valideringen af mailen ikke er true, push fejlmeddelelse til errors kollektionen
      else if (!this.validEmail(this.email)) {
        this.errors.push('Ugyldig email.')
      }
      // Hvis password ikke er indtastet, push fejlmeddelelse til errors kollektionen
      if (!this.password) {
        this.errors.push('Adgangskode påkrævet.')
      }
      // Hvis valideringen af adgangskodens længde ikke er true, push fejlmeddelelse til errors kollektionen
      if (!this.validateLength(this.password)) {
        this.errors.push('Adgangskoden skal minimum indeholde 6 tegn.')
      }
      // Hvis de indtastede adgangskoder ikke stemmer overens, push fejlmeddelelse til errors kollektionen
      else if (!this.validatePassword(this.password, this.repeatPassword)) {
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

#registerbtn {
  background-color: #29BB9C;
  border-color: transparent !important;
}
</style>