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

      <table class="table">
        <thead>
        <tr>
          <th scope="col">Domæne</th>
          <th scope="col">Udløber</th>
          <th scope="col">Oprettet</th>
          <th scope="col">Ejer</th>
          <th scope="col">HTTP response</th>
          <th scope="col"></th>
        </tr>
        </thead>
        <tbody>
        <tr v-for="whois in whoisData">
          <th scope="row">{{ whois.domainName }}</th>
          <td>{{ whois.expiresDate }}</td>
          <td>{{ whois.createdDate }}</td>
          <td>{{ whois.registrant }}</td>
          <td v-bind:style="{color:  whois.statusColor}">{{ whois.status }}</td>
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

export default {
  name: "Whois",
  components: {
    Loading
  },
  data() {
    return {
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
    setData(response) {
      this.whoisData = response.data
      this.$store.commit('storeWhois', response.data)

      for (const key in this.whoisData) {
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

      if (this.whoisData.status === 200) {
        this.txtColor = 'green';
      }
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
    }
  },
}
</script>

<style scoped>
h4 {
  font-family: "Sofia Pro Bold";
}

#addBtn {
  background-color: #29BB9C;
}

#trashBtn {
  color: black;
}

#trashBtn:hover {
  color: orangered;
}

.btn:hover {
  color: lightgray;
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