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
              <CAlert v-if="emailSentSuccessfully"
                      :show.sync="dismissCountDown"
                      closeButton
                      color="success"
                      fade
              >
                Email med gendannelses link er sendt!
              </CAlert>
              <CForm>
                <h4>Gendan adgangskode</h4>
                <CAlert :show="badrequest"
                        color="danger"
                >
                  Feltet indeholder en eller flere fejl.
                  <li v-for="error in errors">{{ error }}</li>
                </CAlert>

                <CInput v-model="email"
                        placeholder="E-mail"
                        autocomplete="email"
                        prepend="@"
                />
                <div class="col text-center">
                  <CButton color="primary" class="px-4" id="resetbtn" v-on:click="reset">Send gendannelses link
                  </CButton>
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
      dismissCountDown: 10,
      isLoading: false,
      fullPage: true,
      color: '#216A90',
      email: '',
      errors: [],
      badrequest: false,
      emailSentSuccessfully: false
    }
  },
  methods: {
    reset() {
      // Starter spinner
      this.isLoading = true

      // Sætter errors kollektionen til tom
      this.errors = []

      // Hvs email ikke er null, eksekver indeholdet af if sætningen
      if (this.email) {

        // Trigger resetEmail action i store.js og sender email med
        this.$store.dispatch('resetEmail', {
          email: this.email,
        })
            .then(response => {
              // Sættes til true, hvis der ingen fejl opstod
              this.emailSentSuccessfully = true

              // Stopper spinner
              this.isLoading = false
            })
            .catch(error => {
              // Sætter badrequest til true
              this.badrequest = true

              // Stopper spinner
              this.isLoading = false

              // Pusher fejlmeddelelse til errors
              this.errors.push('Ugyldig email indtastet.')
            })
      } else {
        // Sætter badrequest til true
        this.badrequest = true

        // Stopper spinner
        this.isLoading = false
      }

      // Hvis email ikke er indtastet push fejlmeddelelse til errors
      if (!this.email) {
        this.errors.push('Email påkrævet.')
      }
      // Hvis email ikke er valid push fejlmeddelelse til errors
      else if (!this.validEmail(this.email)) {
        this.errors.push('Ugyldig email.')
      }
    },

    // Validering af email
    validEmail(email) {
      var re = /^(([^<>()[\]\\.,;:\s@"]+(\.[^<>()[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
      return re.test(email)
    },
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