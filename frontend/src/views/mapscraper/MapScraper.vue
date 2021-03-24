<template>
  <v-app id="mapscraper">
    <h4 class="pb-3">Map scraper</h4>
    <div>
      <div class="container col-md-12">
        <div class="row">
          <div class="col-md-3">
            <label class="mr-1 mt-2 mb-0"><h6>Kortindstillinger</h6></label>
            <div class="input-group">
              <input class="form-check-input" type="checkbox" value="" v-on:click="hideMarkersFunc" ref="hideMarkers">
              <label class="form-check-label">Skjul markers</label>
            </div>
            <div class="input-group mb-2">
              <input class="form-check-input" type="checkbox" value="" v-on:click="hideClustersFunc" ref="hideClusters">
              <label class="form-check-label">Skjul clusters</label>
            </div>
            <label class="mr-1 mt-2 mb-0"><h6>Medtag bynavn eller postnr. i resultatet</h6></label>
            <div class="input-group">
              <input class="form-check-input" type="checkbox" value="" v-model="showCity">
              <label class="form-check-label">Medtag bynavn</label>
            </div>
            <div class="input-group mb-2">
              <input class="form-check-input" type="checkbox" value="" v-model="showZipcode">
              <label class="form-check-label">Medtag postnr.</label>
            </div>
            <label class="mr-1 mt-2"><h6>Indtast radius i KM og tryk på kortet</h6></label>
            <input type="text" class="form-control mb-3" style="height: 38px" placeholder="Radius" ref="radius">
            <label class="mr-1 mt-2 mb-0"><h6>Resultat</h6></label>
            <textarea class="form-control mb-1" rows="10" ref="resultarea"></textarea>
            <v-btn color="#033760" id="resetbtn"
                   @click="reset()">
              Nulstil
            </v-btn>
          </div>
          <div class="col-md-6">
            <div id="map"></div>
          </div>
        </div>
      </div>
    </div>
  </v-app>
</template>

<script>
import axios from "axios";
import MarkerClusterer from '@googlemaps/markerclustererplus';

export default {
  name: "MapScraper",
  components: {},
  data() {
    return {
      showCity: true,
      showZipcode: true,
      map: null,
      searchArea: null,
      searchAreaMarker: null,
      circleAdded: false,
      resultList: [],
      listOfLatLng: [],
      hideMarkers: false,
      hideClusters: false,
      markers: [],
      markerClusterer: null
    }
  },
  mounted() {
    // Internt metodekald
    this.getCitiesApi()
  },
  created() {
    // Sætter headeren til at indeholde access_token som default
    axios.defaults.headers.common['Authorization'] = 'Bearer ' + localStorage.getItem('access_token')
  },
  methods: {
    getCitiesApi() {
      // Henter bynavne, længdegrader og breddegrader fra API og kalder setListOfLatLng
      axios
          .get('https://api.lazzatools.dk/api/city/all')
          .then(response => (this.setListOfLatLng(response)))
    },
    setListOfLatLng(response) {
      // Sætter listOfLatLng til response.data der modtages fra API-kaldet
      this.listOfLatLng = response.data

      // Looper igennem listen
      for (const key in this.listOfLatLng) {

        //Erstater komma med punktum for hvert koordinat
        this.listOfLatLng[key].lat = parseFloat(this.listOfLatLng[key].lat.replace(',', '.'))
        this.listOfLatLng[key].lng = parseFloat(this.listOfLatLng[key].lng.replace(',', '.'))
      }

      // Kalder init metoden
      this.init()
    },
    hideClustersFunc() {
      // Skjuler clusters på mappet alt efter om hideClusters er true eller false
      if (this.hideClusters) {
        this.hideClusters = false
        this.markerClusterer.setMap(this.map)
      } else if (!this.hideClusters) {
        this.hideClusters = true
        this.markerClusterer.setMap(null)
      }
    },
    // Skjuler markers på mappet alt efter om hideMarkers er true eller false
    hideMarkersFunc() {
      if (this.hideMarkers) {
        this.hideMarkers = false
        for (const key in this.markers) {
          this.markers[key].setVisible(true)
        }
      } else if (!this.hideMarkers) {
        this.hideMarkers = true
        for (const key in this.markers) {
          this.markers[key].setVisible(false)
        }
      }
    },
    init() {
      // Tømmer markers kollektionen
      this.markers = []

      // Opretter nyt Google Maps og opbevarer det i map
      this.map = new google.maps.Map(document.getElementById("map"), {
        zoom: 7,
        center: {lat: 56.26392, lng: 9.501785}
      })

      // Ved klik på mappet kaldes addCircle metoden
      this.map.addListener("click", (mapsMouseEvent) => {
        this.addCircle(mapsMouseEvent, this.map)
      })

      // Looper igennem listOfLatLng
      for (const key in this.listOfLatLng) {

        // Opretter markerOptions med længdegrader, breddegrader og bynavn
        const markerOptions = {
          map: this.map,
          position: {lat: this.listOfLatLng[key].lat, lng: this.listOfLatLng[key].lng},
          title: this.listOfLatLng[key].name + ' ' + this.listOfLatLng[key].zipcode,
        }

        // Opretter et infoWindow med bynavn og postnr.
        const infoWindow = new google.maps.InfoWindow({
          content: this.listOfLatLng[key].name + ' ' + this.listOfLatLng[key].zipcode
        })

        // Opretter et nyt marker objekt og sender markerOptions objektet med i parameterlisten
        const marker = new google.maps.Marker(markerOptions)

        // Tilføjer en listener på markeren, så infoWindow vises ved klik på marker
        marker.addListener("click", () => {
          infoWindow.open(this.map, marker)
        })

        // Pusher markeren ind i markers kollektionen
        this.markers.push(marker)
      }

      // Opretter markerClusterer ud fra markers listen og map objektet
      this.markerClusterer = new MarkerClusterer(this.map, this.markers, {
        imagePath: 'https://raw.githubusercontent.com/googlemaps/js-markerclustererplus/main/images/m',
      })
    },
    addCircle(mapsMouseEvent, map) {
      // Får længdegrader og breddegrader ud fra mapsMouseEvent og konverter til JSON
      const latLngClick = mapsMouseEvent.latLng.toJSON()

      // Henter indtastede radius
      const radius = this.$refs.radius.value

      // Hvis cirklen ikke er tilføjet til kortet og radius er større end 0 eksekveres indeholdet af if sætningen
      if (!this.circleAdded && radius > 0) {
        // Opretter nyt searchArea med options
        this.searchArea = new google.maps.Circle({
          strokeColor: "#216A90",
          strokeOpacity: 0.8,
          strokeWeight: 2,
          fillColor: "#216A90",
          fillOpacity: 0.35,
          map,
          center: latLngClick,
          radius: radius * 1000
        })

        // LAZZAWEB favicon
        const image = "https://lazzaweb.dk/wp-content/uploads/2020/12/cropped-lazzaweb-favicon-32x32.png"

        // Opretter searchAreaMarker med options
        this.searchAreaMarker = new google.maps.Marker({
          position: latLngClick,
          map: map,
          icon: image,
          draggable: true,
          animation: google.maps.Animation.DROP
        })

        // Tilføjer en listener til markeren når den flyttes
        this.searchAreaMarker.addListener("dragend", (e) => {
          this.searchArea.setOptions({
            center: e.latLng.toJSON()
          })
          this.map.panTo(this.searchAreaMarker.getPosition())

          // Eksekvering af findMarkersInArea metoden
          this.findMarkersInArea()
        })
        this.circleAdded = true

        // Eksekvering af findMarkersInArea metoden
        this.findMarkersInArea()
      }
    },
    findMarkersInArea() {
      // Tømmer resultList
      this.resultList = []

      // Looper igennem markers
      for (let i = 0; i < this.markers.length; i++) {
        // Måler distance mellem markers position og searchArea
        if (google.maps.geometry.spherical.computeDistanceBetween(this.markers[i].getPosition(), this.searchArea.getCenter()) <= this.searchArea.getRadius()) {
          // Pusher markers titel ind i resultList
          this.resultList.push(this.markers[i].title)

          // Kalder showResult metoden
          this.showResult()
        }
      }
    },
    showResult() {
      // Tjekker op på om showCity og showZipcode er true
      if (this.showCity && this.showZipcode) {
        // Tømmer resultarea
        this.$refs.resultarea.value = '';

        // Joiner resultList og sætter resultarea value
        this.$refs.resultarea.value = this.resultList.join('\n')
      }
      // Tjekker op på om showCity og showZipcode er false
      if (!this.showCity && !this.showZipcode) {
        // Tømmer resultarea
        this.$refs.resultarea.value = '';

        // Joiner resultList og sætter resultarea value
        this.$refs.resultarea.value = this.resultList.join('\n')
      }
      // Tjekker op på om showCity er true og showZipcode er false
      if (this.showCity && !this.showZipcode) {
        // Tømmer resultarea
        this.$refs.resultarea.value = '';

        // Looper igennem resultList
        for (const key in this.resultList) {
          // Splitter ved mellemrum og fjerner første element og sætter resultList[key] lig med det nye array
          this.resultList[key] = this.resultList[key].split(" ").shift()
        }
        // Joiner resultList og sætter resultarea value til listen
        this.$refs.resultarea.value = this.resultList.join('\n')
      }
      // Tjekker op på om showCity er false og showZipcode er true
      if (this.showZipcode && !this.showCity) {
        // Tømmer resultarea
        this.$refs.resultarea.value = '';

        // Looper igennem resultList
        for (const key in this.resultList) {
          // Splitter ved mellemrum og fjerner sidste element og sætter resultList[key] lig med det nye array
          this.resultList[key] = this.resultList[key].split(" ").pop()
        }
        // Joner resultList og sætter resultarea value lig med listen
        this.$refs.resultarea.value = this.resultList.join('\n')
      }
    },
    // Nulstiller alt
    reset() {
      this.hideClusters = false
      this.hideMarkers = false
      this.$refs.hideMarkers.checked = false
      this.$refs.hideClusters.checked = false

      this.showCity = true
      this.showZipcode = true

      this.$refs.resultarea.value = '';
      this.$refs.radius.value = '';
      this.resultList = []
      this.init()
      this.circleAdded = false
    }
  }
}
</script>

<style scoped>
h4 {
  font-family: "Sofia Pro Bold";
}

.btn:hover {
  color: lightgray;
}

.btn, .btn:focus, .btn:active {
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

.input-group {
  margin-left: 20px;
}

#mapscraper {
  margin-top: 10px;
  margin-left: 10px;
  margin-right: 10px;
}

#map {
  height: 550px;
  width: 1200px;
  display: inline-block;
}

#resetbtn:focus {
  outline: none;
}

#resetbtn {
  color: white;
}
</style>