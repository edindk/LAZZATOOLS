<template>
  <v-app style="height: 600px">
    <v-data-table :headers="headers" :items="this.whoisData" class="elevation-1" disable-pagination :search="search"
                  sort-by="expiresDate" hide-default-footer height="600px" fixed-header>
      <template v-slot:top>
        <v-toolbar flat>
          <v-col md="3">
            <v-text-field
                v-model="search"
                append-icon="mdi-magnify"
                label="Søg"
                single-line
                hide-details
            ></v-text-field>
          </v-col>
          <v-spacer></v-spacer>
          <v-dialog v-model="dialog" max-width="500px">
            <template v-slot:activator="{ on, attrs }">
              <v-btn color="success" dark v-bind="attrs" v-on="on" id="addBtn">+Tilføj domæne</v-btn>
            </template>
            <v-card>
              <v-card-title>
                <span class="headline">{{ formTitle }}</span>
              </v-card-title>

              <v-card-text>
                <v-container>
                  <v-row>
                    <v-col cols="12">
                      <v-text-field v-model="editedItem.name"
                                    label="Domæne">
                      </v-text-field>
                    </v-col>
                  </v-row>
                </v-container>
              </v-card-text>

              <v-card-actions>
                <v-spacer></v-spacer>
                <v-btn id="finalAddBtn" color="blue darken-1" text @click="save">Tilføj</v-btn>
              </v-card-actions>
            </v-card>
          </v-dialog>
        </v-toolbar>
      </template>
      <template v-slot:item.domainName="{item}">
        <strong>{{ item.domainName }}</strong>
      </template>
      <template v-slot:item.expiresDate="{item}">
        <div v-bind:style="{color: item.expiresDateColor}">{{ item.expiresDate }}</div>
      </template>
      <template v-slot:item.status="{item}">
        <v-btn id="showStatusBtn" v-if="item.showStatusBtn" style="margin-left: 10px" color="green darken-1" text
               @click="statusCodeApiCall(item)">
          <v-icon style="margin-right: 10px">mdi-application</v-icon>
          Vis status
        </v-btn>
        <div v-if="item.statusCode" v-bind:style="{color: item.statusColor}">{{ item.statusCode }}</div>
      </template>
      <template v-slot:item.actions="{item}">
        <v-btn id="updateBtn" color="blue darken-1" text @click="updateWhoisApiCall(item)">
          <v-icon style="margin-right: 10px">mdi-reload</v-icon>
          Opdater
        </v-btn>
        <v-btn id="deleteBtn" color="red" text @click="deleteItem(item)">
          <v-icon style="margin-right: 10px">mdi-delete</v-icon>
          Slet
        </v-btn>
      </template>

    </v-data-table>
  </v-app>
</template>

<script>
import axios from 'axios';

export default {
  name: 'Whois',
  watch: {
    dialog(val) {
      val || this.close()
    },
  },
  data: () => ({
    editedItem: {
      name: ''
    },
    dialog: false,
    search: '',
    whoisData: [],
    headers: [
      {text: "Domæne", value: "domainName"},
      {text: "Udløber", value: "expiresDate"},
      {text: "Oprettet", value: "createdDate"},
      {text: "Ejer", value: "registrant"},
      {text: "Status", value: "status", sortable: false},
      {text: "", value: 'actions', sortable: false},
    ],
  }),
  computed: {
    formTitle() {
      return 'Tilføj domæne'
    },
  },
  methods: {
    save() {
      axios
          .post('https://api.lazzatools.dk/api/whois/store', {
            domain: this.editedItem.name
          })
          .then(this.getWhois)

      this.close()
    },
    close() {
      this.dialog = false
      this.$nextTick(() => {
        this.editedItem.name = ''
      })
    },
    getWhois() {
      axios
          .get('https://api.lazzatools.dk/api/whois/all')
          .then(response => this.setData(response))
    },
    setData(response) {
      this.whoisData = response.data
      let now = new Date()

      for (const key in this.whoisData) {
        this.whoisData[key].showStatusBtn = true
        this.whoisData[key].statusCode = ''

        let expiresDate = new Date(this.whoisData[key].expiresDate)
        if (expiresDate < now) {
          this.whoisData[key].expiresDateColor = 'red'
        } else {
          const oneDay = 24 * 60 * 60 * 1000
          const expiresDate = new Date(this.whoisData[key].expiresDate)
          const now = new Date()
          const diffDays = Math.round(Math.abs((expiresDate - now) / oneDay))
          if (diffDays <= 31) {
            this.whoisData[key].expiresDateColor = 'orange'
          }
        }
      }
    },
    statusCodeApiCall(item) {
      return new Promise((resolve, reject) => {
        axios.post('https://api.lazzatools.dk/api/whois/statuscode', {
          domainName: item.domainName
        })
            .then(response => {
              this.addStatus(response.data, item)
              resolve(response)
            })
      })
    },
    addStatus(response, item) {
      item.showStatusBtn = false
      item.statusCode = response


      for (const key in this.whoisData) {
        if (this.whoisData[key].domainName === item.domainName) {
          this.$set(this.whoisData, key, item)
          if (this.whoisData[key].statusCode.toString().startsWith(2)) {
            this.whoisData[key].statusColor = 'green'
          } else if (this.whoisData[key].statusCode.toString().startsWith(3)) {
            this.whoisData[key].statusColor = 'blue'
          } else if (this.whoisData[key].statusCode.toString().startsWith(4)) {
            this.whoisData[key].statusColor = 'orange'
          } else if (this.whoisData[key].statusCode.toString().startsWith(5)) {
            this.whoisData[key].statusColor = 'red'
          }
        }
      }
    },
    updateWhoisApiCall(item) {
      for (const key in this.whoisData) {
        if (this.whoisData[key].domainName === item.domainName) {
          return new Promise((resolve, reject) => {
            axios.post('https://api.lazzatools.dk/api/whois/updatewhois', {
              domainName: item.domainName
            })
                .then(response => {
                  this.updateWhoisData(response.data, item)
                  resolve(response)
                })
          })
        }
      }
    },
    updateWhoisData(data, item) {
      item.domainName = data.domainName
      item.expiresDate = data.expiresDate
      item.createdDate = data.createdDate
      item.registrant = data.registrant

      for (const key in this.whoisData) {
        if (this.whoisData[key].domainName === item.domainName) {
          this.$set(this.whoisData[key], this.whoisData[key].showStatusBtn, true)
          this.$set(this.whoisData, key, item)
        }
      }
      this.getWhois()
    },
    deleteItem(item) {
      axios
          .post('https://api.lazzatools.dk/api/whois/delete', {
            domain: item.domainName
          })
          .then(this.getWhois)
    }
  },
  created() {
    this.getWhois()
  },
};
</script>
<style scope>
#addBtn:focus, #showStatusBtn:focus, #updateBtn:focus, #deleteBtn:focus, #finalAddBtn:focus {
  outline: none !important;
}

</style>
