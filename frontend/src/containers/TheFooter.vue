<template>
  <CFooter :fixed="false">
    <div class="mfs-auto">
      <a href="https://lazzaweb.dk/" target="_blank">LAZZAWEB</a>
      <span class="ml-1">&copy; {{ new Date().getFullYear() }}</span>
    </div>
  </CFooter>
</template>

<script>
export default {
  name: 'TheFooter'
}
</script>
