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
          <input class="form-check-input" type="checkbox" value="" v-model="listOfBools.includeFirst">
          <label class="form-check-label">
            Inkluder første liste
          </label>
        </div>
        <div class="form-check">
          <input class="form-check-input" type="checkbox" value="" v-model="listOfBools.includeSecond">
          <label class="form-check-label">
            Inkluder anden liste
          </label>
        </div>
        <div class="form-check">
          <input class="form-check-input" type="checkbox" value="" v-model="listOfBools.includeThird">
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
          <input class="form-check-input" type="checkbox" value="" v-model="listOfBools.wrapWithQuotes">
          <label class="form-check-label">
            Wrap med quotes: " "
          </label>
        </div>
        <div class="form-check">
          <input class="form-check-input" type="checkbox" value="" v-model="listOfBools.wrapWithBrackets">
          <label class="form-check-label">
            Wrap med brackets: []
          </label>
        </div>
        <div class="form-check">
          <input class="form-check-input" type="checkbox" value="" v-model="listOfBools.wrapWithInput">
          <label class="form-check-label">Wrap med: <input type="text" style="width: 50px" ref="wrapInput"> <input
              type="text" style="width: 50px" ref="wrapInput2">
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

    <button type="button" class="btn btn-success" v-on:click="combine()">Generer resultat</button>
    <button type="button" class="btn btn-warning ml-2" v-on:click="clearOptions()">Nulstil valg</button>

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
        <button type="button" class="btn btn-success" v-on:click="">Gem i databasen</button>
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
      list1: [],
      list2: [],
      list3: [],
      listOfBools: [{
        includeFirst: true
      },
        {
          includeSecond: true
        },
        {
          includeThird: true
        },
        {
          wrapWithQuotes: false
        },
        {
          wrapWithBrackets: false
        },
        {
          wrapWithInput: false
        }]
    }
  },
  computed: {},
  methods: {
    getValuesFromTextAreas() {
      // gets all values from textarea1, 2 and 3
      this.list1 = this.$refs.textarea1.value.split("\n");
      this.list2 = this.$refs.textarea2.value.split("\n");
      this.list3 = this.$refs.textarea3.value.split("\n");
    },
    populateCombinedList(...args) {
      var dict = {};
      dict['includeAll'] = function (...args) {
        console.log('includeAll')

        let l1l2combined = []
        let finalList = []

        let val;
        for (const key in args[2]) {
          val = args[2][key]
          for (const key in args[3]) {
            l1l2combined.push(val + ' ' + args[3][key])
          }
        }
        for (const key in l1l2combined) {
          val = l1l2combined[key]
          for (const key in args[4]) {
            finalList.push(val + ' ' + args[4][key])
          }
        }
        for (const key in finalList) {
          args[1].push(finalList[key])
        }
      }
      dict['includeTwo'] = function (...args) {
        console.log('includeFirstAndThird')

        let finalList = []
        let val
        for (const key in args[2]) {
          val = args[2][key]
          for (const key in args[3]) {
            finalList.push(val + ' ' + args[3][key])
          }
        }

        for (const key in finalList) {

          args[1].push(finalList[key])
        }
      }
      dict[args[0]](...args)

    },
    combine() {
      this.emptyCombinedList()

      this.getValuesFromTextAreas()

      // empty the resultarea
      this.$refs.resultarea.value = '';

      // checks which checkboxes are checked and populate the combinedList
      if (this.listOfBools.includeFirst && this.listOfBools.includeSecond && this.listOfBools.includeThird) {
        this.populateCombinedList('includeAll', this.combinedList, this.list1, this.list2, this.list3)
      } else if (this.listOfBools.includeFirst && this.listOfBools.includeThird) {
        this.populateCombinedList('includeTwo', this.combinedList, this.list1, this.list3)
      } else if (this.listOfBools.includeSecond && this.listOfBools.includeThird) {
        this.populateCombinedList('includeTwo', this.combinedList, this.list2, this.list3)
      } else if (this.listOfBools.includeFirst && this.listOfBools.includeSecond) {
        this.populateCombinedList('includeTwo', this.combinedList, this.list1, this.list2)
      }

      this.loopThroughCheckboxes()
    },
    clearOptions() {
      for (const key in this.listOfBools) {
        this.listOfBools[key] = false
      }
    },
    emptyCombinedList() {
      // empties the combinedList
      this.combinedList = []
    },
    clearAll() {
      // clears everything
      this.$refs.resultarea.value = '';
      let list1 = this.$refs.textarea1.value = '';
      let list2 = this.$refs.textarea2.value = '';
      let list3 = this.$refs.textarea3.value = '';
    },
    showCombinedList() {
      // sets the value of resultarea to combinedList
      let newList = new Array()
      for (const key in this.combinedList) {
        newList.push(this.combinedList[key] + '\n')
      }
      this.combinedList = newList

      this.$refs.resultarea.value = this.combinedList.join("")
    },
    wrapTxtWithQuotes() {
      let newList = new Array();
      for (const key in this.combinedList) {
        const val = this.combinedList[key]
        newList.push('"' + val + '"')
      }
      this.combinedList = newList
    },
    wrapTxtWithBrackets() {
      let newList = new Array();
      for (const key in this.combinedList) {
        const val = this.combinedList[key]
        newList.push('[' + val + ']')
      }
      this.combinedList = newList
    },
    wrapTxtWithInput() {
      let newList = new Array();
      let wrapInput = this.$refs.wrapInput.value
      let wrapInput2 = this.$refs.wrapInput2.value

      for (const key in this.combinedList) {
        const val = this.combinedList[key]
        newList.push(wrapInput + ' ' + val + ' ' + wrapInput2)
      }
      this.combinedList = newList
    },
    loopThroughCheckboxes() {
      if (this.listOfBools.wrapWithQuotes) {
        this.wrapTxtWithQuotes()
      }
      if (this.listOfBools.wrapWithBrackets) {
        this.wrapTxtWithBrackets()
      }
      if (this.listOfBools.wrapWithInput) {
        this.wrapTxtWithInput()
      }
      this.showCombinedList()
    }

  }
}
</script>

<style scoped>
.btn-warning {
  color: white;
}
</style>

