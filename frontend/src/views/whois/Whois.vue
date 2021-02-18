<template xmlns="http://www.w3.org/1999/html">
  <div>
    <h4 class="pb-3">Domæne status</h4>
    <div class="col-12">
      <div class="row">
        <div class="col-md-4 pb-2">
          <div class="input-group">
            <input type="text" class="form-control" style="height: 38px" placeholder="Søg efter domæne..."
                   v-model="searchDomain" v-on:change="showAll">
            <span class="input-group-btn">
        <button class="btn btn-secondary" type="button" v-on:click="search"><CIcon name="cil-search"/></button>
      </span>
          </div>
        </div>
        <div class="col-8">
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
          <th scope="col">Status</th>
        </tr>
        </thead>
        <tbody>
        <tr v-for="whois in whoisData">
          <th scope="row">{{ whois.domainName }}</th>
          <td>{{ whois.expiresDate }}</td>
          <td>{{ whois.createdDate }}</td>
          <td>{{ whois.registrant }}</td>
          <td v-if="whois.status"><span class="badge badge-success">Aktiv</span></td>
          <td v-else="!whois.status"><span class="badge badge-danger">Inaktiv</span></td>
        </tr>
        </tbody>
      </table>
    </div>
  </div>
</template>

<script>
import axios from "axios";

export default {
  name: "Whois",
  components: {},
  data() {
    return {
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

      this.whoisData.forEach(function (element) {
        element.status = ''
      })

      for (const key in this.whoisData) {
        const currentDate = new Date();
        const givenDate = new Date(this.whoisData[key].expiresDate)

        if (givenDate > currentDate) {
          this.whoisData[key].status = true
        } else {
          this.whoisData[key].status = false
        }
      }
    },
    search() {
      let arr = []

      for (const key in this.whoisData) {
        if (this.whoisData[key].domainName === this.searchDomain) {
          arr.push(this.whoisData[key])
        }
      }
      this.whoisData = arr
    },
    showAll() {
      if (this.searchDomain === '') {
        this.apiCall()
      }
    },
    addDomain() {
      axios
          .post('https://api.lazzatools.dk/api/whois/store', {
            domain: this.domainToAdd
          })
          .then(this.apiCall)
          .then(this.domainToAdd = '')
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