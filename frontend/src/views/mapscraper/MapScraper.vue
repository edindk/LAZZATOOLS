<template xmlns="http://www.w3.org/1999/html">
  <div>
    <h4 class="pb-3">Map scraper</h4>
    <div class="container col-md-12">
      <div class="row">
        <div class="col-md-2">
          <label class="mr-1 mt-2"><h6>Indtast radius i KM og tryk på kortet</h6></label>
          <input type="text" class="form-control mb-3" style="height: 38px" placeholder="Radius" ref="radius">
          <label class="mr-1 mt-2"><h6>Resultat</h6></label>
          <textarea class="form-control mb-1" rows="10" ref="resultarea"></textarea>
          <button type="button" class="btn mt-1" id="resetbtn" v-on:click="reset">Nulstil</button>
        </div>
        <div class="col-md-6">
          <div id="map"></div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  name: "MapScraper",
  components: {},
  data() {
    return {
      map: null,
      searchArea: null,
      searchAreaMarker: null,
      circleAdded: false,
      resultList: [],
      listOfLatLng: [
        {name: 'Horsens', lat: 55.85855190477934, lng: 9.841137104805311},
        {name: 'Aalborg', lat: 57.05, lng: 9.93},
      ],
      markers: [],
    }
  },
  mounted() {
    this.init()
  },
  methods: {
    init() {
      this.map = new google.maps.Map(document.getElementById("map"), {
        zoom: 7,
        center: {lat: 56.26392, lng: 9.501785}
      })
      this.map.addListener("click", (mapsMouseEvent) => {
        this.addCircle(mapsMouseEvent, this.map)
      })

      for (const key in this.listOfLatLng) {
        const markerOptions = {
          map: this.map,
          position: {lat: this.listOfLatLng[key].lat, lng: this.listOfLatLng[key].lng},
          title: this.listOfLatLng[key].name
        }
        const infoWindow = new google.maps.InfoWindow({
          content: this.listOfLatLng[key].name
        })
        const marker = new google.maps.Marker(markerOptions)
        marker.addListener("click", () => {
          infoWindow.open(this.map, marker)
        })
        this.markers.push(marker)
      }
    },
    addCircle(mapsMouseEvent, map) {
      const latLngClick = mapsMouseEvent.latLng.toJSON()
      const radius = this.$refs.radius.value

      if (!this.circleAdded && radius > 0) {
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

        const image = "https://lazzaweb.dk/wp-content/uploads/2020/12/cropped-lazzaweb-favicon-32x32.png"
        this.searchAreaMarker = new google.maps.Marker({
          position: latLngClick,
          map: map,
          icon: image,
          draggable: true,
          animation: google.maps.Animation.DROP
        })

        this.searchAreaMarker.addListener("dragend", (e) => {
          this.searchArea.setOptions({
            center: e.latLng.toJSON()
          })
          this.map.panTo(this.searchAreaMarker.getPosition())
          this.findMarkersInArea()
        })
        this.circleAdded = true
        this.findMarkersInArea()
      }
    },
    findMarkersInArea() {
      this.resultList = []
      for (let i = 0; i < this.markers.length; i++) {
        if (google.maps.geometry.spherical.computeDistanceBetween(this.markers[i].getPosition(), this.searchArea.getCenter()) <= this.searchArea.getRadius()) {
          this.resultList.push(this.markers[i].title)
          this.showResult()
        }
      }
    },
    showResult() {
      this.$refs.resultarea.value = '';
      this.$refs.resultarea.value = this.resultList.join('\n')
    },
    reset() {
      this.$refs.resultarea.value = '';
      this.$refs.radius.value = '';
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

#map {
  height: 550px;
  width: 1200px;
  display: inline-block;
}

#resetbtn {
  background-color: #033760;
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
</style>