<template>
  <v-app-bar color="cyan lighten-5" absolute>
    <RouterLink to="/"> 英語塾 </RouterLink>
    <div v-show="isLogin">
      <span> {{ username }} さん</span>
      <v-btn color="primary" @click="logout">ログアウト</v-btn>
    </div>
  </v-app-bar>
</template>
<script>
export default {
  computed: {
    isLogin() {
      return this.$store.getters['auth/check']
    },
    username() {
      return this.$store.getters['auth/username']
    },
    apiStatus() {
      return this.$store.state.auth.apiStatus
    },
  },
  methods: {
    async logout() {
      await this.$store.dispatch('auth/logout')
      if (this.apiStatus) {
        this.$router.push('/login')
      }
    },
  },
}
</script>
