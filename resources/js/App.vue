<template>
  <v-app class="v-app">
    <Navbar />
    <main>
      <div class="container">
        <RouterView />
      </div>
    </main>
    <Footer />
  </v-app>
</template>
<script>
import Navbar from './components/Navbar.vue'
import Footer from './components/Footer.vue'
import { INTERNAL_SERVER_ERROR } from './util'

export default {
  components: {
    Navbar,
    Footer,
  },
  computed: {
    errorCode() {
      return this.$store.state.error.code
    },
  },
  watch: {
    errorCode: {
      handler(val) {
        if (val === INTERNAL_SERVER_ERROR) {
          this.$router.push('/500')
        }
      },
      immediate: true,
    },
    $route() {
      this.$store.commit('error/setCode', null)
    },
  },
}
</script>
<style lang="scss" scoped>
.v-app.v-application {
  background-color: #f2efe8;
}
.container {
  padding: 0;
  max-width: 900px;
  display: flex;
  justify-content: center;
}
main {
  margin: 0;
}
</style>
