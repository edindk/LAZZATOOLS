<template>
  <v-app>
    <div class="pb-2">
      <div class="input-group col-md-12">
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
        <div class="col-md-2">
          <p>test</p>
          <p>test</p>
          <p>test</p>
          <p>test</p>
          <p>test</p>
          <p>test</p>
          <p>test</p>
          <p>test</p>
          <p>test</p>
          <p>test</p>
          <p>test</p>

        </div>
        <div class="container col-md-9">
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
      let formData = new FormData()

      if (this.files) {
        for (let file in this.files) {
          if (this.files[file].name === 'Search terms report.xlsx') {
            formData.append('search_terms_report', this.files[file])
          }
          if (this.files[file].name === 'Search keyword report.xlsx') {
            formData.append('search_keyword_report', this.files[file])
          }
        }
      }
      axios({
        method: "post",
        url: "https://api.lazzatools.dk/api/upload",
        data: formData,
        headers: {"Content-Type": "multipart/form-data"},
      })
          .then(response => this.setData(response))
    },
    setData(response) {
      this.searchList = response.data

      console.log(this.searchList)
    },
  }
}
</script>

<style scoped>
#submitFiles:focus {
  outline: none !important;
}
</style>