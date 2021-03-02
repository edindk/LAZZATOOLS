<template xmlns="http://www.w3.org/1999/html">
  <div>
    <h4 class="pb-3">Domæne status</h4>
    <CAlert v-show="successfullyAdded"
            color="success"
            fade
    >
      Domæne tilføjet!
    </CAlert>
    <CAlert v-show="successfullyDeleted"
            color="danger"
            fade
    >
      Domæne blev slettet.
    </CAlert>
    <loading :active.sync="isLoading"
             :can-cancel="true"
             :is-full-page="fullPage"
             :color="color"
    >
    </loading>
    <div class="col-12">
      <div class="row">
        <div class="col-md-2 pb-2">
          <div class="input-group">
            <input type="text" class="form-control" style="height: 38px" placeholder="Søg efter domæne..."
                   v-model="searchDomain" v-on:change="search">
          </div>
        </div>
        <div class="col-10">
          <button type="button" class="btn float-right float-right" id="addBtn" @click="$vm2.open('modal-1')">+ Tilføj
            domæne
          </button>
        </div>
      </div>

      <modal-vue
          @on-close="$vm2.close('modal-1')"
          name="modal-1"
          :headerOptions="{
        title: 'Tilføj domæne',
      }"
          :footerOptions="{
        btn1: 'Annuller',
        btn1Style: {
          backgroundColor: '#033760'
        },
        btn2: 'Tilføj domæne',
        btn2Style: {
          backgroundColor: '#29BB9C',
        },
        btn2OnClick: () => {
          addDomain();
          $vm2.close('modal-1');
        },
        btn1OnClick: () => {
          $vm2.close('modal-1');
        },
      }"
      >
        <div class="col-md-8 align-items-center offset-2">
          <input type="text" class="form-control" style="height: 38px" placeholder="Indtast domæne"
                 v-model="domainToAdd">
        </div>
      </modal-vue>

      <table class="table" v-bind="whoisData">
        <thead>
        <tr>
          <th scope="col">Domæne</th>
          <th scope="col">Udløber</th>
          <th scope="col">Oprettet</th>
          <th scope="col">Ejer</th>
          <th scope="col">HTTP response</th>
          <th scope="col"></th>
          <th scope="col"></th>
        </tr>
        </thead>
        <tbody>
        <tr v-for="whois in whoisData">
          <th scope="row" v-if="whois.domainSpinner">
            <clip-loader :loading="whois.domainSpinner" :color="spinnerColor"
                         :size="spinnerSize"></clip-loader>
          </th>
          <th scope="row" v-else>{{ whois.domainName }}</th>
          <td v-if="whois.expiresSpinner">
            <clip-loader :loading="whois.expiresSpinner" :color="spinnerColor"
                         :size="spinnerSize"></clip-loader>
          </td>
          <td v-else>{{ whois.expiresDate }}</td>
          <td v-if="whois.createdSpinner">
            <clip-loader :loading="whois.createdSpinner" :color="spinnerColor"
                         :size="spinnerSize"></clip-loader>
          </td>
          <td v-else>{{ whois.createdDate }}</td>
          <td v-if="whois.registrantSpinner">
            <clip-loader :loading="whois.registrantSpinner" :color="spinnerColor"
                         :size="spinnerSize"></clip-loader>
          </td>
          <td v-else>{{ whois.registrant }}</td>
          <td style="text-align: center" v-bind:style="{color: whois.statusColor}" v-if="whois.status">
            {{ whois.status }}
          </td>
          <td v-if="!whois.status">
            <clip-loader :loading="whois.statusSpinner" :color="spinnerColor"
                         :size="spinnerSize"></clip-loader>
            <button v-if="whois.showBtn" type="button" class="btn btn-sm" id="btnShow"
                    v-on:click="statusCodeApiCall(whois)">Vis status
            </button>
          </td>
          <td>
            <button type="button" class="btn btn-sm" id="updateBtn" v-on:click="updateWhoisApiCall(whois)">
              Opdater
            </button>
          </td>
          <td v-on:click="deleteDomain(whois.domainName)">
            <button class="btn btn-icon btn-icon-active" id="trashBtn">
              <CIcon name="cilTrash"/>
            </button>
          </td>

        </tr>
        </tbody>
      </table>
    </div>
  </div>
</template>

<script>
import axios from "axios";
import Loading from 'vue-loading-overlay'
import 'vue-loading-overlay/dist/vue-loading.css'
import ClipLoader from 'vue-spinner/src/ClipLoader.vue'

export default {
  name: "Whois",
  components: {
    Loading,
    ClipLoader
  },
  data() {
    return {
      spinnerColor: 'green',
      spinnerSize: '20px',
      statusAdded: false,
      successfullyDeleted: false,
      successfullyAdded: false,
      isLoading: true,
      fullPage: true,
      color: '#216A90',
      whoisData: [],
      searchDomain: null,
      domainToAdd: null
    }
  },
  created() {
    this.apiCall()
  },
  methods: {
    apiCall() {
      axios
          .get('https://api.lazzatools.dk/api/whois/all')
          .then(response => (this.setData(response)))
    },
    statusCodeApiCall(whois) {
      whois.statusSpinner = true
      whois.showBtn = false

      for (const key in this.whoisData) {
        if (this.whoisData[key].domainName === whois.domainName) {
          this.$set(this.whoisData[key] = whois)
        }
      }

      return new Promise((resolve, reject) => {
        axios.post('https://api.lazzatools.dk/api/whois/statuscode', {
          domainName: whois.domainName
        })
            .then(response => {
              this.addStatus(response.data, whois)
              resolve(response)
            })
      })

    },
    addStatus(status, whois) {
      whois.status = status
      whois.statusSpinner = false

      for (const key in this.whoisData) {
        if (this.whoisData[key].domainName === whois.domainName) {
          this.$set(this.whoisData, key, whois)
          if (this.whoisData[key].status) {
            if (this.whoisData[key].status.toString().startsWith(2)) {
              this.whoisData[key].statusColor = 'green'
            } else if (this.whoisData[key].status.toString().startsWith(3)) {
              this.whoisData[key].statusColor = 'blue'
            } else if (this.whoisData[key].status.toString().startsWith(4)) {
              this.whoisData[key].statusColor = 'orange'
            } else if (this.whoisData[key].status.toString().startsWith(5)) {
              this.whoisData[key].statusColor = 'red'
            }
          }
        }
      }
    },
    setData(response) {
      this.whoisData = response.data

      for (const key in this.whoisData) {
        this.whoisData[key].statusSpinner = false
        this.whoisData[key].domainSpinner = false
        this.whoisData[key].expiresSpinner = false
        this.whoisData[key].createdSpinner = false
        this.whoisData[key].registrantSpinner = false
        this.whoisData[key].showBtn = true
      }

      this.$store.commit('storeWhois', response.data)

      this.isLoading = false;
    },
    search() {
      let arr = []
      let isFound = false;

      for (const key in this.whoisData) {
        if (this.whoisData[key].domainName.includes(this.searchDomain)) {
          arr.push(this.whoisData[key])
          isFound = true;
        }
      }
      if (isFound) {
        this.whoisData = arr
        isFound = false
      }
      if (this.searchDomain === "") {
        this.whoisData = this.$store.getters.getWhoisData
      }
    },
    addDomain() {
      axios
          .post('https://api.lazzatools.dk/api/whois/store', {
            domain: this.domainToAdd
          })
          .then(this.apiCall)
          .then(this.domainToAdd = '')
          .then(this.changeSuccessfullyAdded)

    },
    changeSuccessfullyAdded() {
      this.showSuccessfullyAdded()
      setTimeout(this.hideSuccessfullyAdded, 5000)
    },
    showSuccessfullyAdded() {
      this.$nextTick(() => {
        this.successfullyAdded = true
      })
    },
    hideSuccessfullyAdded() {
      this.$nextTick(() => {
        this.successfullyAdded = false
      })
    },
    changeSuccessfullyDeleted() {
      this.showSuccessfullyDeleted()
      setTimeout(this.hideSuccessfullyDeleted, 5000)
    },
    showSuccessfullyDeleted() {
      this.$nextTick(() => {
        this.successfullyDeleted = true
      })
    },
    hideSuccessfullyDeleted() {
      this.$nextTick(() => {
        this.successfullyDeleted = false
      })
    },
    deleteDomain(domainName) {
      axios
          .post('https://api.lazzatools.dk/api/whois/delete', {
            domain: domainName
          })
          .then(this.whoisData = this.whoisData = this.$store.getters.getWhoisData)
          .then(this.changeSuccessfullyDeleted)
    },
    updateWhoisApiCall(whois) {
      whois.domainSpinner = true
      whois.expiresSpinner = true
      whois.createdSpinner = true
      whois.registrantSpinner = true
      whois.status = null
      whois.showBtn = true

      for (const key in this.whoisData) {
        if (this.whoisData[key].domainName === whois.domainName) {
          this.$set(this.whoisData[key] = whois)
        }
      }

      return new Promise((resolve, reject) => {
        axios.post('https://api.lazzatools.dk/api/whois/updatewhois', {
          domainName: whois.domainName
        })
            .then(response => {
              this.updateWhoisData(response.data, whois)
              resolve(response)
            })
      })
    },
    updateWhoisData(data, whois) {

      whois.domainName = data.domainName
      whois.expiresDate = data.expiresDate
      whois.createdDate = data.createdDate
      whois.registrant = data.registrant

      whois.domainSpinner = false
      whois.expiresSpinner = false
      whois.createdSpinner = false
      whois.registrantSpinner = false
      whois.showBtn = true

      for (const key in this.whoisData) {
        if (this.whoisData[key].domainName === whois.domainName) {
          this.$set(this.whoisData[key], this.whoisData[key].domainSpinner, false)
          this.$set(this.whoisData[key], this.whoisData[key].expiresSpinner, false)
          this.$set(this.whoisData[key], this.whoisData[key].createdSpinner, false)
          this.$set(this.whoisData[key], this.whoisData[key].registrantSpinner, false)
          this.$set(this.whoisData[key], this.whoisData[key].showBtn, true)
          this.$set(this.whoisData, this.whoisData[key], whois)
        }
      }

      this.$store.commit('storeWhois', this.whoisData)
    }
  }
}
</script>

<style scoped>
h4 {
  font-family: "Sofia Pro Bold";
}

#addBtn, #btnShow {
  background-color: #29BB9C;
}

#updateBtn {
  background-color: #033760;
}

#trashBtn {
  color: black;
}

#trashBtn:hover {
  color: orangered;
}

.form-control {
  font-family: "Sofia Pro Light";
}

.btn, .btn:focus, .btn:active {
  outline: none !important;
  box-shadow: none !important;
  font-family: "Sofia Pro Regular";
  color: white;
}

.btn:hover {
  color: lightgray;
}

.form-check-label {
  margin-left: 3px;
  width: 150px;
  display: inline-block;
  font-family: "Sofia Pro Light";
}

.table {
  font-family: "Sofia Pro Light";
}

.form-control:focus {
  outline: none !important;
  border: 1px solid #0FB5C8;
  box-shadow: 0 0 5px #0FB5C8;
}
</style>