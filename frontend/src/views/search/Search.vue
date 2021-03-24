<template>
  <v-app id="search">
    <h4 class="pb-3">Søgeordshøster</h4>
    <div class="col-md-6 offset-md-3 mt-2">
      <v-alert
          dismissible
          dense
          border="left"
          type="warning"
      >
        Værktøjet er endnu ikke klar til brug. Test værktøjet ved at indsætte Search keyword report og Search terms
        report.
        Bemærk at rapporterne skal være i filfomat .xlsx
      </v-alert>
    </div>
    <div class="pb-2">
      <div class="input-group col-md-5 offset-md-1">
        <v-file-input v-model="files"
                      chips
                      multiple
                      show-size
                      small-chips
        ></v-file-input>
        <div class="input-group-append pl-5 pt-5">
          <v-btn color="success" v-on:click="submitFiles" id="submitFiles" small>Generer resultat</v-btn>
        </div>
      </div>
      <div class="row">
        <div class="container col-md-10">
          <v-data-table :headers="headers" :items="this.searchList" class="elevation-1 pr-2" disable-pagination
                        :search="search"
                        hide-default-footer></v-data-table>
        </div>
      </div>
    </div>
  </v-app>

</template>

<script>
import axios from 'axios';

export default {
  name: "Search",
  data: () => ({
    files: null,
    searchList: [
      {
        search: ''
      }
    ],
    search: '',
    headers: [
      {text: "Søgeord", value: "search"}
    ]
  }),
  methods: {
    submitFiles() {
      // Opretter nyt FormData objekt
      let formData = new FormData()

      // Hvis files ikke er null, eksekver if sætningen
      if (this.files) {
        // Looper igennem files
        for (let file in this.files) {
          // Hvis navnet på filen er lig med Search terms report, eksekver if sætninge
          if (this.files[file].name === 'Search terms report.xlsx') {
            // Append filen til FormData objektet
            formData.append('search_terms_report', this.files[file])
          }
          // Hvis navnet på filen er lig med Search keyword report, eksekver if sætninge
          if (this.files[file].name === 'Search keyword report.xlsx') {
            // Append filen til FormData objektet
            formData.append('search_keyword_report', this.files[file])
          }
        }
      }
      axios({
        // API kald til /upload og sender filerne med
        method: "post",
        url: "https://api.lazzatools.dk/api/upload",
        data: formData,
        headers: {"Content-Type": "multipart/form-data"},
      })
          // Tager response fra API-kaldet og kalder setData og sender response med i parameteren
          .then(response => this.setData(response))
    },
    setData(response) {
      // Sætter searchList lig med response.data
      this.searchList = response.data
    },
  }
}
</script>

<style scoped>
#submitFiles:focus {
  outline: none !important;
}

#search {
  margin-top: 10px;
  margin-left: 10px;
  margin-right: 10px;
}
</style>