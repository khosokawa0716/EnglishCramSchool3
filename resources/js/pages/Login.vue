<template>
  <div>
    <v-tabs>
      <v-tab @click="selectTab1()">ログイン</v-tab>
      <v-tab @click="selectTab2()">ユーザー登録</v-tab>
    </v-tabs>
    <div v-show="tab === 1">
      <v-form @submit.prevent="login">
        <div v-if="loginErrors" class="errors">
          <ul v-if="loginErrors.name">
            <li v-for="msg in loginErrors.name" :key="msg">{{ msg }}</li>
          </ul>
          <ul v-if="loginErrors.password">
            <li v-for="msg in loginErrors.password" :key="msg">{{ msg }}</li>
          </ul>
        </div>
        <v-text-field
          v-model="loginForm.name"
          :rules="nameRules"
          :counter="10"
          label="なまえ"
          required
        ></v-text-field>
        <v-text-field
          v-model="loginForm.password"
          :rules="passwordRules"
          label="パスワード"
          type="password"
          required
        ></v-text-field>
        <v-btn type="submit" color="primary">ログイン</v-btn>
      </v-form>
    </div>
    <div v-show="tab === 2">
      <v-form @submit.prevent="register">
        <div v-if="registerErrors" class="errors">
          <ul v-if="registerErrors.name">
            <li v-for="msg in registerErrors.name" :key="msg">{{ msg }}</li>
          </ul>
          <ul v-if="registerErrors.password">
            <li v-for="msg in registerErrors.password" :key="msg">{{ msg }}</li>
          </ul>
        </div>
        <v-text-field
          v-model="registerForm.name"
          :rules="nameRules"
          :counter="10"
          label="なまえ"
          required
        ></v-text-field>
        <v-text-field
          v-model="registerForm.password"
          :rules="passwordRules"
          :counter="10"
          type="password"
          label="パスワード"
          required
        ></v-text-field>
        <v-text-field
          v-model="registerForm.password_confirmation"
          :rules="passwordRules"
          :counter="10"
          type="password"
          label="パスワードかくにん"
          required
        ></v-text-field>
        <v-btn type="submit" color="primary">ユーザーとうろく</v-btn>
      </v-form>
    </div>
  </div>
</template>

<script>
export default {
  data() {
    return {
      tab: 1,
      loginForm: {
        name: '',
        password: '',
      },
      registerForm: {
        name: '',
        password: '',
        password_confirmation: '',
      },
      nameRules: [
        (v) => !!v || 'なまえはかならず入れてください',
        (v) => v.length <= 10 || 'なまえは１０もじ以下で入れてください',
      ],
      passwordRules: [
        (v) => !!v || 'パスワードはかならず入れてください',
        (v) => v.length <= 10 || 'パスワードは１０もじ以下で入れてください',
      ],
    }
  },
  computed: {
    apiStatus() {
      return this.$store.state.auth.apiStatus
    },
    loginErrors() {
      return this.$store.state.auth.loginErrorMessages
    },
    registerErrors() {
      return this.$store.state.auth.registerErrorMessages
    },
  },
  methods: {
    selectTab1() {
      this.tab = 1
    },
    selectTab2() {
      this.tab = 2
    },
    async login() {
      await this.$store.dispatch('auth/login', this.loginForm)
      if (this.apiStatus) {
        this.$router.push('/')
      }
    },
    async register() {
      await this.$store.dispatch('auth/register', this.registerForm)
      if (this.apiStatus) {
        this.$router.push('/')
      }
    },
    clearError() {
      this.$store.commit('auth/setLoginErrorMessages', null)
      this.$store.commit('auth/setRegisterErrorMessages', null)
    },
  },
  created() {
    this.clearError()
  },
}
</script>
