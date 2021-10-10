<template>
  <v-app-bar color="white" class="v-navbar" absolute>
    <div class="v-navbar__wrapper">
      <div class="v-navbar__logo">
        <v-icon class="v-navbar__logo-icon" large> mdi-cat </v-icon>
        <span class="v-navbar__logo-title">えいごじゅく</span>
      </div>
      <div v-show="isLogin" class="v-navbar__username">
        <v-menu offset-y :nudge-left="7">
          <template v-slot:activator="{ on, attrs }">
            <div v-bind="attrs" v-on="on">
              <span> {{ username }} さん</span><v-icon>mdi-menu-down</v-icon>
            </div>
          </template>
          <v-list class="v-navbar__list">
            <v-list-item-title @click="logout()">ログアウト</v-list-item-title>
          </v-list>
        </v-menu>
      </div>
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
  &__wrapper {
    width: 900px;
    margin: 0 auto;
    padding: 0 25px;
    display: flex;
    justify-content: space-between;
  }
  &__logo {
    display: flex;
    align-items: center;
    &-icon {
      margin-right: 4px;
    }
    &-title {
      font-size: 24px;
    }
  }
  &__username {
    display: flex;
    align-items: center;
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
