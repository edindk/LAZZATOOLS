<template>
  <div class="container">
    <h4 id="title">Combine, scrub og wrap</h4>

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
        <div class="input-group mb-2 mt-2" v-for="include in listOfIncludes"
             v-bind:value="{bool: include.bool, text: include.name}">
          <input class="form-check-input" type="checkbox" v-model="include.bool">
          <label class="form-check-label">
            {{ include.name }}
          </label>
        </div>
      </div>

      <div class="col-4">
        <h5>Wrap</h5>
        <div class="input-group mb-2 mt-2" v-for="wrap in listOfWrapOptions"
             v-bind:value="{bool: wrap.bool, text: wrap.name}">
          <input class="form-check-input" type="checkbox" value="" v-model="wrap.bool">
          <label class="form-check-label">
            {{ wrap.name }}
          </label>
        </div>

        <div class="input-group mb-2 mt-2">
          <input class="form-check-input" type="checkbox" value="" v-model="listOfOtherOptions.wrapWithSymbol">
          <label class="form-check-label">Wrap med symboler:</label>
          <div class="input-group col-sm-5">
            <input class="form-control form-control-sm" type="text" ref="wrapSymbol" placeholder="-">
            <input class="form-control form-control-sm" type="text" ref="wrapSymbol2" placeholder="+">
          </div>
        </div>


        <div class="input-group mb-2 mt-2">
          <input class="form-check-input" type="checkbox" value="" v-model="listOfOtherOptions.wrapWithWords">
          <label class="form-check-label">Wrap med ord:</label>
          <div class="input-group col-sm-5">
            <input class="form-control form-control-sm" type="text" ref="wrapWord" placeholder="køb">
            <input class="form-control form-control-sm" type="text" ref="wrapWord2" placeholder="nu">
          </div>
        </div>
        <div class="input-group mb-2 mt-2">
          <input class="form-check-input" type="checkbox" value="" v-model="listOfOtherOptions.replaceSymbol">
          <label class="form-check-label">Erstat med:</label>
          <div class="input-group col-sm-5">
            <input class="form-control form-control-sm" type="text" ref="replaceSymb" placeholder="køb">
            <input class="form-control form-control-sm" type="text" ref="replaceSymb2" placeholder="nu">
          </div>
        </div>
      </div>

      <div class="col-4">
        <h5>Scrub</h5>
        <div class="input-group mb-2 mt-2" v-for="scrub in listOfScrubOptions"
             v-bind:value="{bool:scrub.bool, text: scrub.name}">
          <input class="form-check-input" type="checkbox" value="" v-model="scrub.bool">
          <label class="form-check-label">
            {{ scrub.name }}
          </label>
        </div>
        <div class="input-group mb-2 mt-2">
          <input class="form-check-input" type="checkbox" value="" v-model="listOfOtherOptions.removeSymb">
          <label class="form-check-label">Fjern tegn:</label>
          <div class="input-group col-sm-5">
            <input class="form-control form-control-sm" type="text" ref="removeSymb" placeholder="@#$/\%^&*">
          </div>
        </div>
        <div class="input-group mb-2 mt-2">
          <input class="form-check-input" type="checkbox" value="" v-model="listOfOtherOptions.removeWord">
          <label class="form-check-label">Fjern ord:</label>
          <div class="input-group col-sm-5">
            <input class="form-control form-control-sm" type="text" ref="removeWord" placeholder="køb">
          </div>
        </div>

        <div class="input-group mb-2 mt-2">
          <input class="form-check-input" type="checkbox" value="" v-model="listOfOtherOptions.removeLine">
          <label class="form-check-label">Fjern linjer der indeholder:</label>
          <div class="input-group col-sm-5">
            <input class="form-control form-control-sm" type="text" ref="removeLine" placeholder="køb">
          </div>
        </div>

        <div class="input-group mb-2 mt-2">
          <input class="form-check-input" type="checkbox" value="" v-model="listOfOtherOptions.numberOfWords">
          <label class="form-check-label">Maks antal tegn:</label>
          <div class="input-group col-sm-5">
            <input class="form-control form-control-sm" type="number" ref="numberOfWords" placeholder="14">
          </div>
        </div>

        <div class="input-group mb-2 mt-2 mb-4">
          <input class="form-check-input" type="checkbox" value="" v-model="listOfOtherOptions.format">
          <label class="form-check-label">Ændring af formatet:</label>
          <div class="col-sm-5">
            <select class="form-control form-control-sm form-select" v-model="selected">
              <option v-for="format in listOfFormats" v-bind:value="{id: format.id, text: format.name}">
                {{ format.name }}
              </option>
            </select>
          </div>
        </div>

      </div>
    </div>

    <!-- Text area showing the result -->
    <div class="row">
      <div class="col-12 mt-3">
        <h4>Resultat</h4>
        <div class="col-sm-5" id="sort" style="width: 200px">
          <label>Sorter</label>
          <select class="form-control form-control-sm form-select ml-2 mb-1" v-model="selectedSort"
                  v-on:change="checkSelectedSort">
            <option v-for="sort in listOfSorts" v-bind:value="{id: sort.id, text: sort.name}">
              {{ sort.name }}
            </option>
          </select>
        </div>
        <div class="form-group">
          <textarea class="form-control" rows="10" ref="resultarea"></textarea>
        </div>
      </div>
    </div>
    <div class="row">
      <div class="col-12 mb-4">
        <button type="button" class="btn" id="generatebtn" v-on:click="combine()">Generer resultat</button>
        <button type="button" class="btn ml-1" id="resetbtn" v-on:click="clear()">Nulstil</button>
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
      selectedSort: '',
      listOfSorts: [
        {id: 1, name: 'alfabetisk'},
        {id: 2, name: 'antal anslag'},
      ],
      listOfIncludes: [
        {bool: true, name: 'Inkluder første liste'},
        {bool: true, name: 'Inkluder anden liste'},
        {bool: true, name: 'Inkluder tredje liste'}
      ],
      listOfWrapOptions: [
        // {bool: false, name: 'Unwrap'},
        {bool: false, name: 'Wrap med quotes " "'},
        {bool: false, name: 'Wrap med brackets [ ]'}
      ],
      listOfScrubOptions: [
        {bool: false, name: 'Fjern duplikater'},
        {bool: false, name: 'Fjern ekstra spaces'}
      ],
      listOfOtherOptions: [
        {includeFirst: true},
        {includeSecond: true},
        {includeThird: true},
        {wrapWithSymbol: false},
        {wrapWithWords: false},
        {removeSymb: false},
        {removeWord: false},
        {removeLine: false},
        {numberOfWords: false},
        {format: false},
        {replaceSymbol: false}]
    }
  }
  ,
  methods: {
    // Henter alt tekst fra textarea1, 2 og 3
    getValuesFromTextAreas() {
      this.list1 = this.$refs.textarea1.value.split("\n");
      this.list2 = this.$refs.textarea2.value.split("\n");
      this.list3 = this.$refs.textarea3.value.split("\n");
    },
    // Indsætter kombinerede sætninger i combinedList
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

    },
    combine() {
      // Tømmer combinedList
      this.emptyCombinedList()

      // Henter tekst fra textAreas
      this.getValuesFromTextAreas()

      // Tømmer resultarea
      this.$refs.resultarea.value = '';

      // Tjekker hvilke række der skal medtages i resultarea
      if (this.listOfIncludes[0].bool && this.listOfIncludes[1].bool && this.listOfIncludes[2].bool) {
        this.populateCombinedList('includeAll', this.combinedList, this.list1, this.list2, this.list3)
      } else if (this.listOfIncludes[0].bool && this.listOfIncludes[2].bool) {
        this.populateCombinedList('includeTwo', this.combinedList, this.list1, this.list3)
      } else if (this.listOfIncludes[1].bool && this.listOfIncludes[2].bool) {
        this.populateCombinedList('includeTwo', this.combinedList, this.list2, this.list3)
      } else if (this.listOfIncludes[0].bool && this.listOfIncludes[1].bool) {
        this.populateCombinedList('includeTwo', this.combinedList, this.list1, this.list2)
      }

      // Kalder checkThroughCheckboxes
      this.checkThroughCheckboxes()
    },
    // Sætter alle bools til false og tømmer inputfelter samt textareas
    clear() {
      // Sætter alle bools i listOfOtherOptions til false
      for (const key in this.listOfOtherOptions) {
        this.listOfOtherOptions[key] = false
      }

      // Sætter alle bools i listOfIncludes til false
      for (const key in this.listOfIncludes) {
        this.listOfIncludes[key].bool = false
      }

      // Sætter inputfelter og textareas til at være tomme
      this.$refs.removeWord.value = '';
      this.$refs.removeSymb.value = '';
      this.$refs.wrapSymbol.value = '';
      this.$refs.wrapSymbol2.value = '';
      this.$refs.wrapWord.value = '';
      this.$refs.wrapWord2.value = '';
      this.$refs.resultarea.value = '';
      this.$refs.textarea1.value = '';
      this.$refs.textarea2.value = '';
      this.$refs.textarea3.value = '';
      this.$refs.wrapSymbol.value = '';
      this.$refs.wrapSymbol2.value = '';
      this.$refs.wrapWord.value = '';
      this.$refs.wrapWord2.value = '';
      this.$refs.replaceSymb.value = '';
      this.$refs.replaceSymb2.value = '';
      this.$refs.removeSymb.value = '';
      this.$refs.removeWord.value = '';
      this.$refs.removeLine.value = '';
      this.$refs.numberOfWords.value = '';

      // Kalder emptyCombinedList metoden
      this.emptyCombinedList()
    },
    // Sætter combinedList til at være tom
    emptyCombinedList() {
      this.combinedList = []
    },
    // Sætter resultarea til combinedList
    showCombinedList() {
      let arr = []
      for (const key in this.combinedList) {
        arr.push(this.combinedList[key] + '\n')
      }
      this.combinedList = arr

      this.$refs.resultarea.value = this.combinedList.join("")
    },
    // Unwrapper teksten så combinedList både indeholder wrapped og unwrapped versioner af teksten
    unwrap() {
      for (const combinedKey in this.combinedList) {
        let newValue = this.combinedList[combinedKey].replace(/[^æøå\w ]+/g, '')
        this.combinedList.push(newValue)
      }
    },
    // Wrapper teksten med quotes
    wrapTxtWithQuotes() {
      for (const combinedKey in this.combinedList) {
        let value = this.combinedList[combinedKey]
        this.combinedList.push('"' + value + '"')
      }
    },
    // Wrapper teksten med brackets
    wrapTxtWithBrackets() {
      for (const combinedKey in this.combinedList) {
        if (!this.combinedList[combinedKey].includes('"', '"')) {
          let value = this.combinedList[combinedKey]
          this.combinedList.push('[' + value + ']')
        } else {
          // Do nothing
        }
      }
    },
    // Wrapper teksten med symboler fra inputfelter
    wrapTxtWithSymb() {
      // Henter symboler fra inputfelterne
      const wrapSymbol = this.$refs.wrapSymbol.value
      const wrapSymbol2 = this.$refs.wrapSymbol2.value
      // let arr = []

      // Looper igennem combinedList
      for (const key in this.combinedList) {
        const val = this.combinedList[key]
        // Tilføjer symbol i starten og for enden af symbolet og indsætter i midlertidlig list
        this.combinedList.push(wrapSymbol + val + wrapSymbol2)
      }
      // combinedList sættes til arr
      // this.combinedList = arr
    },
    // Wrapper teksten med ord fra inputfelter
    wrapTxtWithWords() {
      let arr = []
      // Henter ordene fra inputfelterne
      const wrapWord = this.$refs.wrapWord.value
      const wrapWord2 = this.$refs.wrapWord2.value

      // Looper igennem combinedList
      for (const key in this.combinedList) {
        const value = this.combinedList[key]
        // Indsætter det nye element i midlertidlig liste
        arr.push(wrapWord + ' ' + value + ' ' + wrapWord2)
      }
      // combinedList sættes til arr
      this.combinedList = arr
    },
    // Erstater et symbol med et andet symbol
    replaceSymbol() {
      // Henter symboler ud fra to inputfelter
      const valueFromInput = this.$refs.replaceSymb.value
      const valueFromInput2 = this.$refs.replaceSymb2.value

      // Looper igennem combinedList
      for (const combinedKey in this.combinedList) {
        // Så længe et element indeholder symbolet loopes der
        while (this.combinedList[combinedKey].includes(valueFromInput)) {
          // Erstater symbolet med et andet symbol
          let newValue = this.combinedList[combinedKey].replaceAll(valueFromInput, valueFromInput2)
          // Det nye element indsættes i combinedList
          this.combinedList[combinedKey] = newValue
        }
      }
    },
    // Fjerner duplikater i combinedList
    removeDuplicates() {
      let arr = [...new Set(this.combinedList)];
      this.combinedList = arr
    },
    // Fjerner ekstra spacing
    removeExtraSpacing() {
      let arr = []
      for (const combinedKey in this.combinedList) {
        let value = this.combinedList[combinedKey]
        let newValue = value.replace(/\s+/g, ' ').trim()
        arr.push(newValue)
      }
      this.combinedList = arr
    },
    // Fjerner symboler
    removeSymbols() {
      // Henter symbolerne fra inputfelt
      const valueFromInput = this.$refs.removeSymb.value
      // For hvert symbol indsættes symbolet som element i symbolList
      let symbolList = valueFromInput.split('');

      // For hvert element i combinedList loopes der
      for (const combinedKey in this.combinedList) {
        // For hvert symbol i symbolList loopes der
        for (const symbolKey in symbolList) {
          // Så længe et element indeholder symbolet loopes der
          while (this.combinedList[combinedKey].includes(symbolList[symbolKey])) {
            // Erstat symbolet med en tom værdi og indsæt i combiendList
            let newValue = this.combinedList[combinedKey].replaceAll(symbolList[symbolKey], '')
            this.combinedList[combinedKey] = newValue
          }
        }
      }
    },
    // Fjerner et ord fra elementet
    removeWord() {
      // Henter ordene fra inputfelt
      const valueFromInput = this.$refs.removeWord.value
      // For hvert ord indsættes ordet i wordList
      let wordList = valueFromInput.split(/\s+/);

      // For hvert ord der er i wordList loopes der
      for (const wordKey in wordList) {
        // For hvert element i combiendList loopes der
        for (const combinedKey in this.combinedList) {
          // Så længe elementet indeholder ordet loopes der
          while (this.combinedList[combinedKey].includes(wordList[wordKey])) {
            // Erstater ordet med en tom værdi. Det nye element sættes ind i combinedList
            let newValue = this.combinedList[combinedKey].replaceAll(wordList[wordKey], '')
            this.combinedList[combinedKey] = newValue
          }
        }
      }
    },
    // Fjerner element fra combinedList, hvis elementet indeholder et specifikt ord
    removeLine() {
      // Henter ord fra inputfelt
      const valueFromInput = this.$refs.removeLine.value
      // For hvert ord indsættes ordet i wordList
      let wordList = valueFromInput.split(/\s+/);
      // Midlertidlig liste sættes til combinedList
      let arr = this.combinedList

      // For hvert ord der er i wordList loopes der
      for (const wordKey in wordList) {
        // For hvert element i combinedList loopes der
        for (const combinedKey in this.combinedList) {
          // Så længe et givent element indeholder ordet fra wordList loopes der
          while (this.combinedList[combinedKey].includes(wordList[wordKey])) {
            // Elementet fra combinedList sættes til at være tom
            arr.push(this.combinedList[combinedKey] = '')
            // Filtrer den midlertidlige liste og fjerne tomme elementer
            arr = this.combinedList.filter(item => item)
          }
        }
      }
      // combinedList sættes til at være arr
      this.combinedList = arr
    },
    // Tjekker op på at antallet af tegn ikke overstiger x antal tegn
    numberOfWords() {
      // Henter værdi fra input
      const valueFromInput = this.$refs.numberOfWords.value
      let arr = []

      // Looper igennem combinedList
      for (const combinedKey in this.combinedList) {
        // Hvis længden er større end værdi fra input, sættes elementet til tom
        if (this.combinedList[combinedKey].length > valueFromInput) {
          this.combinedList[combinedKey] = ''
          // Ellers indsættes elementet i den midlertidlige liste
        } else {
          arr.push(this.combinedList[combinedKey])
        }
      }
      // Filtrer listen for at fjerne tomme elementer
      arr = arr.filter(item => item)
      // combinedList sættes til at være arr
      this.combinedList = arr
    },
    // Hvert element i combinedList sættes til lowerCase
    allLowercase() {
      for (const combinedKey in this.combinedList) {
        let newValue = this.combinedList[combinedKey].toLowerCase()
        this.combinedList[combinedKey] = newValue
      }
    },
    // Hvert element i combiendList sættes til initialCaps
    initialCaps() {
      for (const combinedKey in this.combinedList) {
        let value = this.combinedList[combinedKey].toString()
        let newValue = value.charAt(0).toUpperCase() + value.substr(1).toLowerCase()
        this.combinedList[combinedKey] = newValue
      }
    },
    // Hvert element i combiendList sættes til initialCapsAll
    initialCapsAll() {
      for (const combinedKey in this.combinedList) {
        let newValue = this.combinedList[combinedKey].replace(/\w\S*/g, function (txt) {
          return txt.charAt(0).toUpperCase() + txt.substr(1).toLowerCase()
        })
        this.combinedList[combinedKey] = newValue
      }
    },
    // Hvert element i combinedList sættes til UpperCase
    allUppercase() {
      for (const combinedKey in this.combinedList) {
        let newValue = this.combinedList[combinedKey].toString().toUpperCase()
        this.combinedList[combinedKey] = newValue
      }
    },
    // Tjekker op på hvilket format der er valgt og eksekver tilhørende metode
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
    sortByLength() {
      this.combinedList.sort(function (a, b) {
        return b.length - a.length;
      });
      this.$refs.resultarea.value = this.combinedList.join("")
    },
    sortByAlphabetical() {
      this.combinedList.sort(function (a, b) {
        function getCode(c) {
          c = c.toLowerCase();
          if (c.substring(0, 2) == 'aa') return 300;
          switch (c.charCodeAt(0)) {
            case 229 : //å
              return 299;
              break;
            case 248 : //ø
              return 298;
              break;
            case 230 : //æ
              return 297;
              break;
            default :
              return c.charCodeAt(0);
              break;
          }
        }

        return getCode(a) - getCode(b);
      });
      this.$refs.resultarea.value = this.combinedList.join("")
    },
    checkSelectedSort() {
      switch (this.selectedSort.text) {
        case 'alfabetisk':
          console.log('alfabetisk')
          this.sortByAlphabetical()
          break;
        case 'antal anslag':
          console.log('antal anslag')
          this.sortByLength()
          break;
      }
    },
    // Hvis boolean er true så eksekver metode, ellers så gør intet
    checkThroughCheckboxes() {
      // Tjekker op på hvilke checkboxe der er true i Wrap delen
      // this.listOfWrapOptions[0].bool ? this.unwrap() : null
      this.listOfOtherOptions.wrapWithWords ? this.wrapTxtWithWords() : null
      this.listOfWrapOptions[0].bool ? this.wrapTxtWithQuotes() : null
      this.listOfWrapOptions[1].bool ? this.wrapTxtWithBrackets() : null
      this.listOfOtherOptions.wrapWithSymbol ? this.wrapTxtWithSymb() : null
      this.listOfOtherOptions.replaceSymbol ? this.replaceSymbol() : null

      // Tjekker op på hvilke checkboxe der er true i Scrub delen
      this.listOfScrubOptions[0].bool ? this.removeDuplicates() : null
      this.listOfOtherOptions.removeSymb ? this.removeSymbols() : null
      this.listOfScrubOptions[1].bool ? this.removeExtraSpacing() : null
      this.listOfOtherOptions.removeWord ? this.removeWord() : null
      this.listOfOtherOptions.removeLine ? this.removeLine() : null
      this.listOfOtherOptions.numberOfWords ? this.numberOfWords() : null
      this.listOfOtherOptions.format ? this.selectedFormat() : null

      this.showCombinedList()
    },
  }
}
</script>

<style scoped>

#resetbtn {
  background-color: #033760;
}

#generatebtn {
  background-color: #29BB9C;
}

#sort {
  display: flex;
  right: 15px;
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

.input-group {
  margin-left: 20px
}

h4, h5 {
  font-family: "Sofia Pro Bold";
}

.form-check-label {
  margin-left: 3px;
  width: 150px;
  display: inline-block;
  font-family: "Sofia Pro Light";
}

.form-control:focus {
  outline: none !important;
  border: 1px solid #0FB5C8;
  box-shadow: 0 0 5px #0FB5C8;
}
</style>
