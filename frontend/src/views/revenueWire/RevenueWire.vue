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
        <div class="form-check" v-for="include in listOfIncludes"
             v-bind:value="{bool: include.bool, text: include.name}">
          <input class="form-check-input" type="checkbox" v-model="include.bool">
          <label class="form-check-label">
            {{ include.name }}
          </label>
        </div>
      </div>

      <div class="col-4">
        <h5>Wrap</h5>
        <div class="form-check">
          <input class="form-check-input" type="checkbox" value="" v-model="listOfBools.unwrapped">
          <label class="form-check-label">
            Unwrap
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
          <input class="form-check-input" type="checkbox" value="" v-model="listOfBools.wrapWithSymbol">
          <label class="form-check-label">Wrap med symboler: <input type="text" style="width: 50px" ref="wrapSymbol"
                                                                    placeholder="-"> <input
              type="text" style="width: 50px" ref="wrapSymbol2" placeholder="+">
          </label>
        </div>
        <div class="form-check mt-1">
          <input class="form-check-input" type="checkbox" value="" v-model="listOfBools.wrapWithWords">
          <label class="form-check-label">
            Wrap med ord: <input type="text" style="width: 50px" ref="wrapWord" placeholder="køb"> <input
              type="text" style="width: 50px" ref="wrapWord2" placeholder="nu">
          </label>
        </div>
      </div>

      <div class="col-4">
        <h5>Scrub</h5>
        <div class="form-check">
          <input class="form-check-input" type="checkbox" value="" v-model="listOfBools.removeDupli">
          <label class="form-check-label">
            Fjern duplikater
          </label>
        </div>
        <div class="form-check">
          <input class="form-check-input" type="checkbox" value="" v-model="listOfBools.removeExtraSpa">
          <label class="form-check-label">
            Fjern ekstra spaces
          </label>
        </div>
        <div class="form-check mt-1">
          <input class="form-check-input" type="checkbox" value="" v-model="listOfBools.removeSymb">
          <label class="form-check-label">
            Fjern tegn: <input type="text" ref="removeSymb"
                               placeholder="@#$/\%^&*">
          </label>
        </div>
        <div class="form-check mt-1">
          <input class="form-check-input" type="checkbox" value="" v-model="listOfBools.removeWord">
          <label class="form-check-label">
            Fjern ord: <input type="text" ref="removeWord"
                              placeholder="køb nu">
          </label>
        </div>
        <div class="form-check mt-1">
          <input class="form-check-input" type="checkbox" value="" v-model="listOfBools.removeLine">
          <label class="form-check-label">
            Fjern linjer der indeholder: <input type="text" ref="removeLine"
                                                placeholder="køb nu">
          </label>
        </div>
        <div class="form-check mt-1">
          <input class="form-check-input" type="checkbox" value="" v-model="listOfBools.numberOfWords">
          <label class="form-check-label">
            Behold kun linjer der indeholder maks: <input type="number" ref="numberOfWords" style="width: 50px"
                                                          placeholder="14">
          </label>
        </div>

        <div class="form-check mt-2">
          <input class="form-check-input" type="checkbox" value="" v-model="listOfBools.format">
          <label class="form-check-label">
            Ændring af formatet:
          </label>
          <select class="form-select ml-1" v-model="selected">
            <option v-for="format in listOfFormats" v-bind:value="{id: format.id, text: format.name}">{{ format.name }}
            </option>
          </select>
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
      <div class="col-12 mb-4">
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
      selected: '',
      listOfFormats: [
        {id: 1, name: 'initial caps'},
        {id: 1, name: 'all initial caps'},
        {id: 2, name: 'all lowercase'},
        {id: 3, name: 'all uppercase'},
      ],
      listOfIncludes: [
        {bool: true, name: 'Inkluder første liste'},
        {bool: true, name: 'Inkluder anden liste'},
        {bool: true, name: 'Inkluder tredje liste'}
      ],
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
          unwrapped: false
        },
        {
          wrapWithQuotes: false
        },
        {
          wrapWithBrackets: false
        },
        {
          wrapWithSymbol: false
        },
        {
          wrapWithWords: false
        },
        {
          removeDupli: false
        },
        {
          removeExtraSpa: false
        },
        {
          removeSymb: false
        },
        {
          removeWord: false
        },
        {
          removeLine: false
        },
        {
          numberOfWords: false
        },
        {
          format: false
        }]
    }
  }
  ,
  computed: {}
  ,
  methods: {
    getValuesFromTextAreas() {
      // gets all values from textarea1, 2 and 3
      this.list1 = this.$refs.textarea1.value.split("\n");
      this.list2 = this.$refs.textarea2.value.split("\n");
      this.list3 = this.$refs.textarea3.value.split("\n");
    }
    ,
    populateCombinedList(...args) {
      var dict = {};
      dict['includeAll'] = function (...args) {

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

    }
    ,
    combine() {
      this.emptyCombinedList()

      this.getValuesFromTextAreas()

      // empty the resultarea
      this.$refs.resultarea.value = '';
      // checks which checkboxes are checked and populates the combinedList
      if (this.listOfIncludes[0].bool && this.listOfIncludes[1].bool && this.listOfIncludes[2].bool) {
        this.populateCombinedList('includeAll', this.combinedList, this.list1, this.list2, this.list3)
      } else if (this.listOfIncludes[0].bool && this.listOfIncludes[2].bool) {
        this.populateCombinedList('includeTwo', this.combinedList, this.list1, this.list3)
      } else if (this.listOfIncludes[1].bool && this.listOfIncludes[2].bool) {
        this.populateCombinedList('includeTwo', this.combinedList, this.list2, this.list3)
      } else if (this.listOfIncludes[0].bool && this.listOfIncludes[1].bool) {
        this.populateCombinedList('includeTwo', this.combinedList, this.list1, this.list2)
      }

      this.checkThroughCheckboxes()
    }
    ,
    clearOptions() {
      for (const key in this.listOfBools) {
        this.listOfBools[key] = false
      }
      this.$refs.removeWord.value = '';
      this.$refs.removeSymb.value = '';
      this.$refs.wrapSymbol.value = '';
      this.$refs.wrapSymbol2.value = '';
      this.$refs.wrapWord.value = '';
      this.$refs.wrapWord2.value = '';
    }
    ,
    emptyCombinedList() {
      // empties the combinedList
      this.combinedList = []
    }
    ,
    clearAll() {
      // clears everything
      this.emptyCombinedList()
      this.$refs.resultarea.value = '';
      this.$refs.textarea1.value = '';
      this.$refs.textarea2.value = '';
      this.$refs.textarea3.value = '';
    }
    ,
    showCombinedList() {
      // sets the value of resultarea to combinedList
      let arr = []
      for (const key in this.combinedList) {
        arr.push(this.combinedList[key] + '\n')
      }
      this.combinedList = arr

      this.$refs.resultarea.value = this.combinedList.join("")
    }
    ,
    unwrap() {
      for (const combinedKey in this.combinedList) {
        let newValue = this.combinedList[combinedKey].replace(/[^æøå\w ]+/g, '')
        this.combinedList.push(newValue)
      }
    },
    wrapTxtWithQuotes() {

      for (const combinedKey in this.combinedList) {
        let value = this.combinedList[combinedKey]
        this.combinedList.push('"' + value + '"')
      }

      // let arr = []
      // for (const key in this.combinedList) {
      //   const value = this.combinedList[key]
      //   arr.push('"' + value + '"')
      // }
      // this.combinedList = arr
    }
    ,
    wrapTxtWithBrackets() {
      for (const combinedKey in this.combinedList) {
        if (!this.combinedList[combinedKey].includes('"', '"')) {
          let value = this.combinedList[combinedKey]
          this.combinedList.push('[' + value + ']')
        } else {

        }
      }

      // let arr = []
      // for (const key in this.combinedList) {
      //   const value = this.combinedList[key]
      //   arr.push('[' + value + ']')
      // }
      // this.combinedList = arr
    }
    ,
    wrapTxtWithInput() {
      const wrapSymbol = this.$refs.wrapSymbol.value
      const wrapSymbol2 = this.$refs.wrapSymbol2.value
      let arr = []

      for (const key in this.combinedList) {
        const val = this.combinedList[key]
        arr.push(wrapSymbol + val + wrapSymbol2)
      }
      this.combinedList = arr
    },
    wrapTxtWithWords() {
      let arr = []
      const wrapWord = this.$refs.wrapWord.value
      const wrapWord2 = this.$refs.wrapWord2.value

      for (const key in this.combinedList) {
        const value = this.combinedList[key]
        arr.push(wrapWord + ' ' + value + ' ' + wrapWord2)
      }
      this.combinedList = arr
    },
    removeDuplicates() {
      let arr = [...new Set(this.combinedList)];
      this.combinedList = arr
    },
    removeExtraSpacing() {
      let arr = new Array()
      for (const key in this.combinedList) {
        let value = this.combinedList[key]
        let newValue = value.replace(/\s+/g, ' ').trim()
        arr.push(newValue)
      }
      this.combinedList = arr
    },
    removeSymbols() {
      const valueFromInput = this.$refs.removeSymb.value
      let symbolList = valueFromInput.split('');

      for (const combinedKey in this.combinedList) {
        for (const symbolKey in symbolList) {
          while (this.combinedList[combinedKey].includes(symbolList[symbolKey])) {
            let newValue = this.combinedList[combinedKey].replaceAll(symbolList[symbolKey], '')
            this.combinedList[combinedKey] = newValue
          }
        }
      }
    },
    removeWord() {
      const valueFromInput = this.$refs.removeWord.value
      let wordList = valueFromInput.split(/\s+/);

      for (const wordKey in wordList) {
        for (const combinedKey in this.combinedList) {
          while (this.combinedList[combinedKey].includes(wordList[wordKey])) {
            let newValue = this.combinedList[combinedKey].replaceAll(wordList[wordKey], '')
            this.combinedList[combinedKey] = newValue
          }
        }
      }
    },
    removeLine() {
      const valueFromInput = this.$refs.removeLine.value
      let wordList = valueFromInput.split(/\s+/);
      let arr = this.combinedList

      for (const wordKey in wordList) {
        for (const combinedKey in this.combinedList) {
          while (this.combinedList[combinedKey].includes(wordList[wordKey])) {
            arr.push(this.combinedList[combinedKey] = '')
            // console.log('inside of if')
            // this.combinedList[combinedKey] = ''
            arr = this.combinedList.filter(item => item)
          }
        }
      }
      this.combinedList = arr
    },
    numberOfWords() {
      const valueFromInput = this.$refs.numberOfWords.value
      let arr = []

      for (const combinedKey in this.combinedList) {
        if (this.combinedList[combinedKey].length > valueFromInput) {
          this.combinedList[combinedKey] = ''
        } else {
          arr.push(this.combinedList[combinedKey])
        }
      }
      arr = arr.filter(item => item)
      this.combinedList = arr
    },
    allLowercase() {
      for (const combinedKey in this.combinedList) {
        let newValue = this.combinedList[combinedKey].toLowerCase()
        this.combinedList[combinedKey] = newValue
      }
    },
    initialCaps() {
      for (const combinedKey in this.combinedList) {
        let value = this.combinedList[combinedKey].toString()
        let newValue = value.charAt(0).toUpperCase() + value.substr(1).toLowerCase()
        this.combinedList[combinedKey] = newValue
      }
    },
    initialCapsAll() {
      for (const combinedKey in this.combinedList) {
        let newValue = this.combinedList[combinedKey].replace(/\w\S*/g, function (txt) {
          return txt.charAt(0).toUpperCase() + txt.substr(1).toLowerCase()
        })
        this.combinedList[combinedKey] = newValue
      }
    },
    allUppercase() {
      for (const combinedKey in this.combinedList) {
        let newValue = this.combinedList[combinedKey].toString().toUpperCase()
        this.combinedList[combinedKey] = newValue
      }
    },
    selectedFormat() {
      switch (this.selected.text) {
        case 'initial caps':
          this.initialCaps()
          break;
        case 'all initial caps':
          this.initialCapsAll()
          break;
        case 'all lowercase':
          this.allLowercase()
          break;
        case 'all uppercase':
          this.allUppercase()
          break;

      }
    },
    checkThroughCheckboxes() {
      this.listOfBools.unwrapped ? this.unwrap() : null
      this.listOfBools.format ? this.selectedFormat() : null
      this.listOfBools.numberOfWords ? this.numberOfWords() : null
      this.listOfBools.removeLine ? this.removeLine() : null
      this.listOfBools.removeWord ? this.removeWord() : null
      this.listOfBools.removeSymb ? this.removeSymbols() : null
      this.listOfBools.wrapWithWords ? this.wrapTxtWithWords() : null
      this.listOfBools.removeExtraSpa ? this.removeExtraSpacing() : null
      this.listOfBools.removeDupli ? this.removeDuplicates() : null
      this.listOfBools.wrapWithQuotes ? this.wrapTxtWithQuotes() : null
      this.listOfBools.wrapWithBrackets ? this.wrapTxtWithBrackets() : null
      this.listOfBools.wrapWithSymbol ? this.wrapTxtWithInput() : null

      this.showCombinedList()
    },
  }
}
</script>

<style scoped>
.btn-warning {
  color: white;
}
</style>
