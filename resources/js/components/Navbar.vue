<template>
  <v-app-bar color="white" absolute class="v-navbar">
    <div><v-icon class="v-navbar__icon"> mdi-cat </v-icon>えいごじゅく</div>
    <div v-show="isLogin">
      <v-menu offset-y :nudge-left="7">
        <template v-slot:activator="{ on, attrs }">
          <div v-bind="attrs" v-on="on">
            <span> {{ username }} さん</span><v-icon>mdi-menu-down</v-icon>
          </div>
        </template>
        <v-list class="v-navbar__list">
          <v-list-item-title>ログアウト</v-list-item-title>
        </v-list>
      </v-menu>
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
<style lang="scss" scoped>
.v-navbar {
  &__icon {
    margin-right: 4px;
  }
  &__list {
    text-align: center;
    cursor: pointer;
    padding: 8px;
    &:hover {
      background-color: #f2efe8;
    }
  }
  ::v-deep .v-toolbar__content {
    justify-content: space-between;
  }
}
</style>
