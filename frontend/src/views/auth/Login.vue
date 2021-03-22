<template>
  <div class="c-app flex-row align-items-center">
    <CContainer>
      <CRow class="justify-content-center">
        <CCol md="8">
          <loading :active.sync="isLoading"
                   :can-cancel="true"
                   :is-full-page="fullPage"
                   :color="color"
          >
          </loading>
          <CCardGroup>
            <CCard class="p-4">
              <CAlert v-if="registeredSuccessfully"
                      :show.sync="dismissCountDown"
                      closeButton
                      color="success"
                      fade
              >
                Registrering vellykket! Du vil modtage en verificeringsmail.
              </CAlert>
              <CAlert v-if="resetSuccess"
                      :show.sync="dismissCountDown"
                      closeButton
                      color="success"
                      fade
              >
                Adgangskode gendannet!
              </CAlert>
              <CAlert :show="badrequest"
                      color="danger"
              >
                Forkert email eller adgangskode.
              </CAlert>
              <CAlert :show="notVerified"
                      color="danger"
              >
                Du mangler at verificer din email.
                <li>
                  <CButton class="btn-md" id="resendBtn" v-on:click="resend">Gensend link</CButton>
                </li>
              </CAlert>
              <CCardBody action="#" @submit.prevent="login">
                <CForm>
                  <h4>Log ind</h4>
                  <CInput v-model="email"
                          placeholder="E-mail"
                          autocomplete="username email"
                  >
                    <template #prepend-content>
                      <CIcon name="cil-user"/>
                    </template>
                  </CInput>
                  <CInput v-model="password"
                          placeholder="Adgangskode"
                          type="password"
                          autocomplete="curent-password"
                  >
                    <template #prepend-content>
                      <CIcon name="cil-lock-locked"/>
                    </template>
                  </CInput>
                  <CRow>
                    <CCol class="text-center col-md-12">
                      <CButton class="btn-md px-4" id="loginbtn" v-on:click="login">Log ind</CButton>
                      <CButton class="btn-md px-4" id="resetPass" v-on:click="resetPass">Glemt adgangskode?</CButton>
                    </CCol>
                    <CCol col="6" class="text-right">
                      <CButton color="link" class="d-lg-none" v-on:click="register">Registrer</CButton>
                    </CCol>
                  </CRow>
                </CForm>
              </CCardBody>
            </CCard>
            <CCard
                color="primary"
                text-color="white"
                class="text-center py-5 d-md-down-none"
                body-wrapper
                id="register">
              <CCardBody>
                <h2>Registrer</h2>
                <p>Registrer nu og optimer arbejdsprocessen med LAZZATOOLS!</p>
                <CButton class="btn-md px-3" v-on:click="register"
                         color="light"
                         variant="outline"
                >
                  Registrer
                </CButton>
              </CCardBody>
            </CCard>
          </CCardGroup>
        </CCol>
      </CRow>
    </CContainer>
  </div>
</template>

<script>
import Loading from 'vue-loading-overlay'
import 'vue-loading-overlay/dist/vue-loading.css'
import axios from "axios";

export default {
  name: 'Login',
  components: {
    Loading
  },
  data() {
    return {
      dismissCountDown: 10,
      email: '',
      password: '',
      isLoading: false,
      fullPage: true,
      color: '#216A90',
      badrequest: false,
      registeredSuccessfully: false,
      resetSuccess: false,
      notVerified: false
    }
  },
  created() {
    axios.defaults.headers.common['Authorization'] = 'Bearer ' + localStorage.getItem('access_token')
    this.registeredSuccessfully = this.$store.getters.registeredSuccessfully
    this.resetSuccess = this.$store.getters.resetSuccess
  },
  methods: {
    resend() {
      axios
          .post('https://api.lazzatools.dk/api/email/verification', {
            email: this.email,
            password: this.password
          })
    },
    resetPass() {
      this.$router.push({name: 'resetemail'})
    },
    login() {

      this.isLoading = true
      this.badrequest = false
      this.$store.dispatch('retrieveToken', {
        username: this.email,
        password: this.password,
      })
          .then(response => {
            this.$router.push({name: 'dashboard'})
          })
          .catch((error) => {
            this.isLoading = false

            if (error.response.data === 'Email not verified') {
              this.notVerified = true
            } else {
              this.badrequest = true
            }

          })
    },
    register() {
      this.$router.push({name: 'register'})
    },
  }
}
</script>

<style>
.c-app {
  background-color: #F8F9FC !important;
}

h2, h4 {
  font-family: "Sofia Pro Bold";
}

.form-group, p {
  font-family: "Sofia Pro Light";
}

.btn {
  font-family: "Sofia Pro Regular";
}

#register {
  background-color: #0B6A90 !important;
}

@media screen and (max-width: 991px) {
  .btn-link {
    color: #033760 !important;
    text-align: center;
    display: block;
  }
}

#loginbtn {
  background-color: #29BB9C;
  border-color: transparent !important;
}

#resetPass {
  color: lightgray;
}

#resetPass:hover {
  color: black;
}

#resendBtn {
  color: #772b35;
}

#resendBtn:hover {
  color: white
}

.btn:hover {
  color: lightgray;
}

.btn, .btn:focus, .btn:active {
  outline: none !important;
  box-shadow: none !important;
  color: white;
}

.form-control:focus {
  outline: none !important;
  border: 1px solid #0FB5C8;
  box-shadow: 0 0 5px #0FB5C8;
}

</style>