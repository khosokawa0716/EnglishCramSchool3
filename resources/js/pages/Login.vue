<template>
  <div class="p-login">
    <div class="p-login__wrapper">
      <v-tabs background-color="#f2efe8">
        <v-tab @click="selectTab1()">ログイン</v-tab>
        <v-tab @click="selectTab2()">ユーザーとうろく</v-tab>
      </v-tabs>
      <div v-show="tab === 1" class="p-login__form">
        <v-form @submit.prevent="login">
          <div v-if="loginErrors" class="errors">
            <ul v-if="loginErrors.name">
              <li v-for="msg in loginErrors.name" :key="msg">{{ msg }}</li>
            </ul>
            <ul v-if="loginErrors.password">
              <li v-for="msg in loginErrors.password" :key="msg">{{ msg }}</li>
            </ul>
          </div>
          <p class="p-login__form-label">なまえ</p>
          <v-text-field
            solo
            dense
            v-model="loginForm.name"
            :rules="nameRules"
            :counter="10"
            required
          ></v-text-field>
          <p class="p-login__form-label">パスワード</p>
          <v-text-field
            solo
            dense
            v-model="loginForm.password"
            :rules="passwordRules"
            type="password"
            required
          ></v-text-field>
          <div class="p-login__form-btn">
            <v-btn type="submit" color="primary">ログイン</v-btn>
          </div>
        </v-form>
      </div>
      <div v-show="tab === 2" class="p-login__form">
        <v-form @submit.prevent="register">
          <div v-if="registerErrors" class="errors">
            <ul v-if="registerErrors.name">
              <li v-for="msg in registerErrors.name" :key="msg">{{ msg }}</li>
            </ul>
            <ul v-if="registerErrors.password">
              <li v-for="msg in registerErrors.password" :key="msg">
                {{ msg }}
              </li>
            </ul>
          </div>
          <p class="p-login__form-label">なまえ</p>
          <v-text-field
            solo
            dense
            v-model="registerForm.name"
            :rules="nameRules"
            :counter="10"
            required
          ></v-text-field>
          <p class="p-login__form-label">パスワード</p>
          <v-text-field
            solo
            dense
            v-model="registerForm.password"
            :rules="passwordRules"
            :counter="10"
            type="password"
            required
          ></v-text-field>
          <p class="p-login__form-label">パスワードかくにん</p>
          <v-text-field
            solo
            dense
            v-model="registerForm.password_confirmation"
            :rules="passwordRules"
            :counter="10"
            type="password"
            required
          ></v-text-field>
          <div class="p-login__form-btn">
            <v-btn type="submit" color="primary">ユーザーとうろく</v-btn>
          </div>
        </v-form>
      </div>
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
        this.$router.push('/mypage')
      }
    },
    async register() {
      await this.$store.dispatch('auth/register', this.registerForm)
      if (this.apiStatus) {
        this.$router.push('/mypage')
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
<style lang="scss" scoped>
.p-login {
  margin: 170px 0 100px;
  padding: 45px 50px;
  background-color: #fff;
  width: 700px;
  box-shadow: 5px 5px 2px rgba(0, 0, 0, 0.2);
  &__wrapper {
    background-color: #f2efe8;
    box-shadow: 5px 5px 2px rgba(0, 0, 0, 0.1);
    width: 100%;
  }
  &__form {
    padding: 40px;
    &-label {
      margin-bottom: 2px;
    }
    &-btn {
      text-align: right;
      margin-top: 10px;
    }
  }
}
</style>
