<template>
  <v-app-bar color="cyan lighten-5" absolute>
    <RouterLink to="mypage"> 英語塾 </RouterLink>
    <div v-if="isLogin">
      <span> {{ username }} </span>
      <v-btn color="primary" @click="logout">Logout</v-btn>
    </div>
    <div v-else>
      <RouterLink to="/login"> Login / Register </RouterLink>
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
