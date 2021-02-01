<template>
  <div class="container">
    <h4>Combine, scrub og wrap</h4>

    <!-- Text areas for inputting words -->
    <div class="row">
      <div class="col-4">
        <div class="form-group">
          <textarea class="form-control" rows="5" ref="textarea1"></textarea>
        </div>
      </div>
      <div class="col-4">
        <div class="form-group">
          <textarea class="form-control" rows="5" ref="textarea2"></textarea>
        </div>
      </div>
      <div class="col-4">
        <div class="form-group">
          <textarea class="form-control" rows="5" ref="textarea3"></textarea>
        </div>
      </div>
    </div>

    <!-- Options -->
    <div class="row">
      <div class="col-4">
        <h5>Inkluder</h5>
        <div class="form-check">
          <input class="form-check-input" type="checkbox" value="" v-model="includeFirst">
          <label class="form-check-label">
            Inkluder første liste
          </label>
        </div>
        <div class="form-check">
          <input class="form-check-input" type="checkbox" value="" v-model="includeSecond">
          <label class="form-check-label">
            Inkluder anden liste
          </label>
        </div>
        <div class="form-check">
          <input class="form-check-input" type="checkbox" value="" v-model="includeThird">
          <label class="form-check-label">
            Inkluder tredje liste
          </label>
        </div>
      </div>

      <div class="col-4">
        <h5>Wrap</h5>
        <div class="form-check">
          <input class="form-check-input" type="checkbox" value="">
          <label class="form-check-label">
            Tilføj unwrapped
          </label>
        </div>
        <div class="form-check">
          <input class="form-check-input" type="checkbox" value="">
          <label class="form-check-label">
            Wrap med quotes: " "
          </label>
        </div>
        <div class="form-check">
          <input class="form-check-input" type="checkbox" value="">
          <label class="form-check-label">
            Wrap med brackets: []
          </label>
        </div>
        <div class="form-check">
          <input class="form-check-input" type="checkbox" value="">
          <label class="form-check-label">
            Wrap med:
          </label>
        </div>
        <div class="form-check">
          <input class="form-check-input" type="checkbox" value="">
          <label class="form-check-label">
            Wrap med:
          </label>
        </div>
      </div>

      <div class="col-4">
        <h5>Scrub</h5>
        <div class="form-check">
          <input class="form-check-input" type="checkbox" value="">
          <label class="form-check-label">
            Fjern duplikater
          </label>
        </div>
        <div class="form-check">
          <input class="form-check-input" type="checkbox" value="">
          <label class="form-check-label">
            Fjern ekstra spaces
          </label>
        </div>
        <div class="form-check">
          <input class="form-check-input" type="checkbox" value="">
          <label class="form-check-label">
            Fjern karakterne: INPUT FELT
          </label>
        </div>
        <div class="form-check">
          <input class="form-check-input" type="checkbox" value="">
          <label class="form-check-label">
            Fjern ord: INPUT FELT
          </label>
        </div>
        <div class="form-check">
          <input class="form-check-input" type="checkbox" value="">
          <label class="form-check-label">
            Fjern linjer der indeholder ordene: INPUT FELT
          </label>
        </div>
        <div class="form-check">
          <input class="form-check-input" type="checkbox" value="">
          <label class="form-check-label">
            Behold kun linjer der indeholder [DROPDOWN NUMBERS] ord
          </label>
        </div>
        <div class="form-check">
          <input class="form-check-input" type="checkbox" value="">
          <label class="form-check-label">
            Maks antal ord pr. linje [DROPDOWN NUMBERS]
          </label>
        </div>
      </div>
    </div>

    <button type="button" class="btn btn-success" v-on:click="combineList()">Generer resultat</button>

    <!-- Text area showing the result -->
    <div class="row">
      <div class="col-12 mt-3">
        <h4>Resultat</h4>
        <div class="form-group">
          <textarea class="form-control" rows="10" ref="resultarea"></textarea>
        </div>
      </div>
    </div>
    <div class="row">
      <div class="col-12">
        <button type="button" class="btn btn-success">Gem i databasen</button>
        <button type="button" class="btn btn-warning ml-2" id="clearbtn" v-on:click="clearAll()">Tøm listen</button>
      </div>
    </div>
  </div>

</template>

<script>

export default {
  name: "RevenueWire",
  components: {},
  data() {
    return {
      combinedList: [],
      list: [],
      includeFirst: true,
      includeSecond: true,
      includeThird: true
    }
  },
  computed: {},
  methods: {
    combineList() {
      this.$refs.resultarea.value = '';
      if (this.includeFirst && this.includeSecond && this.includeThird) {
        this.includeAll()
      } else if (this.includeFirst && this.includeThird) {
        this.includeFirstAndThird()
      } else if (this.includeFirst && this.includeSecond) {
        this.includeFirstAndSecond()
      }
    },
    includeFirstAndSecond() {
      // get values from text areas and split the values by \n
      let list1 = this.$refs.textarea1.value.split("\n");
      let list2 = this.$refs.textarea2.value.split("\n");

      // empty combinedList
      this.combinedList = []

      // combine the values from list1 with list2 and 3. Push the new values into combinedList
      for (const key in list1) {
        this.combinedList.push(list1[key] + ' ' + list2[key] + '\n')
      }
      this.showCombinedList()
    },
    includeFirstAndThird() {
      // get values from text areas and split the values by \n
      let list1 = this.$refs.textarea1.value.split("\n");
      let list3 = this.$refs.textarea3.value.split("\n");

      // empty combinedList
      this.combinedList = []

      // combine the values from list1 with list2 and 3. Push the new values into combinedList
      for (const key in list1) {
        this.combinedList.push(list1[key] + ' ' + list3[key] + '\n')
      }
      this.showCombinedList()
    },
    includeAll() {
      // get values from text areas and split the values by \n
      let list1 = this.$refs.textarea1.value.split("\n");
      let list2 = this.$refs.textarea2.value.split("\n");
      let list3 = this.$refs.textarea3.value.split("\n");

      // empty combinedList
      this.combinedList = []

      // combine the values from list1 with list2 and 3. Push the new values into combinedList
      for (const key in list1) {
        this.combinedList.push(list1[key] + ' ' + list2[key] + ' ' + list3[key] + '\n')
      }
      this.showCombinedList()
    },
    clearAll() {
      // clears everything
      this.$refs.resultarea.value = '';
      let list1 = this.$refs.textarea1.value = '';
      let list2 = this.$refs.textarea2.value = '';
      let list3 = this.$refs.textarea3.value = '';
    }
    ,
    showCombinedList() {
      this.$refs.resultarea.value = this.combinedList.join("")
    }
  }
}
</script>

<style scoped>
#clearbtn {
  color: white;
}
</style>

