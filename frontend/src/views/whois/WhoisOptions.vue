<template>
  <div>
    <CCol xl="12">
      <CCard>
        <CCardHeader>
          <h5>Domæne status</h5>
        </CCardHeader>
        <CCardBody>
          <div>
            <CCard class="mb-1">
              <CButton
                  block
                  class="text-left shadow-none card-header"
                  @click="accordion = accordion === 0 ? false : 0"
              >
                <h6 class="m-0">WhoisXMLAPI</h6>
              </CButton>
              <CCollapse :show="accordion === 0">
                <CCardBody>
                  <CAlert v-if="successfullyUpdated"
                          :show.sync="dismissCountDown"
                          closeButton
                          color="success"
                          fade
                  >
                    API-nøglen til WhoisXMLAPI er nu opdateret.
                  </CAlert>
                  <p>Her kan du ændre API-nøglen til WhoisXMLAPI.</p>

                  <p>1. Gå til whoisxmlapi.com.
                    <br/>
                    2. Under "My products" finder du API-nøglen.
                    <br/>
                  </p>
                  <div class="row">
                    <div class="col-md-4 pb-2">
                      <div class="input-group">
                        <input type="text" class="form-control" style="height: 38px" placeholder="API-nøgle"
                               ref="whoisApiKey">
                      </div>
                    </div>

                    <button type="button" class="btn float-right float-right btn-sm" style="height: 38px"
                            id="updateBtn" v-on:click="storeWhoisXmlApiKey">Opdater nøgle
                    </button>

                  </div>
                </CCardBody>
              </CCollapse>
            </CCard>
          </div>
        </CCardBody>
      </CCard>
    </CCol>
  </div>
</template>

<script>
import axios from "axios";

export default {
  name: "WhoisOptions",
  data() {
    return {
      successfullyUpdated: false,
      dismissCountDown: 10,
      accordion: 0,
    }
  },
  methods: {
    storeWhoisXmlApiKey() {
      const apiKey = this.$refs.whoisApiKey.value
      axios
          .post('https://api.lazzatools.dk/api/whois/credentials/store', {
            key: apiKey
          })
          .then(this.successfullyUpdated = true)
    }
  }
}
</script>

<style scoped>
h5, h6 {
  font-family: "Sofia Pro Bold";
}

p {
  font-family: "Sofia Pro Light";
}

.btn {
  border: 0;
}

#updateBtn {
  background-color: #29BB9C;
}

.btn:hover {
  color: lightgray;
}

.btn-sm, .btn-sm:focus, .btn-sm:active {
  outline: none !important;
  box-shadow: none !important;
  font-family: "Sofia Pro Regular";
  color: white;
}

.form-control:focus {
  outline: none !important;
  border: 1px solid #0FB5C8;
  box-shadow: 0 0 5px #0FB5C8;
}
</style>