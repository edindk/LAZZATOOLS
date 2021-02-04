<template>
  <router-view></router-view>
</template>

<script>
export default {
  name: 'App'
}
</script>

<style lang="scss">
// Import Main styles for this application
@import 'assets/scss/style';

@font-face {
  font-family: "Sofia Pro Bold";
  src: local("Sofia Pro Bold"),
  url('./fonts/Mostardesign - Sofia Pro Bold.otf') format("truetype");
}
@font-face {
  font-family: "Sofia Pro Regular";
  src: local("Sofia Pro Regular"),
  url('./fonts/Mostardesign - Sofia Pro Bold.otf') format("truetype");
}
@font-face {
  font-family: "Sofia Pro Light";
  src: local("Sofia Pro Light"),
  url('./fonts/Mostardesign - Sofia Pro Light.otf') format("truetype");
}
</style>
