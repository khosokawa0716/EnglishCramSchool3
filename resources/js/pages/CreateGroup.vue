<template>
  <div class="p-create-group">
    <div class="p-create-group__header">
      <h1 class="p-create-group__title">グループ作成</h1>
      <router-link to="/admin" v-if="isAdmin" class="p-create-group__link"
        >管理者のマイページ</router-link
      >
    </div>
    <div class="p-create-group__wrapper">
      <div class="p-create-group__content">
        <div v-if="registerErrors" class="errors">
          <ul v-if="registerErrors.group">
            <li v-for="msg in registerErrors.group" :key="msg">
              {{ msg }}
            </li>
          </ul>
        </div>
        <v-form @submit.prevent="register">
          <v-text-field
            solo
            dense
            v-model="group"
            :rules="groupRules"
            :counter="20"
            required
          ></v-text-field>
        </v-form>
        <div class="p-create-group__btn">
          <v-btn type="submit" color="primary" @click="register"
            >グループ登録</v-btn
          >
        </div>
      </div>
    </div>
    <div class="p-create-group__wrapper">
      <div class="p-create-group__wrapper--registered">
        <h3 class="p-create-group__sub-titile">登録されたグループ</h3>
        <ul v-for="group in groups" :key="group.index">
          <li>{{ group }}</li>
        </ul>
      </div>
    </div>
  </div>
</template>

<script>
import { OK, UNPROCESSABLE_ENTITY } from '../util'

export default {
  data() {
    return {
      group: '',
      groups: [],
      groupRules: [
        (v) => !!v || 'グループは必ず入れてください',
        (v) => v.length <= 20 || 'グループは２０もじ以下で入れてください',
      ],
      registerErrors: null,
    }
  },
  computed: {
    isAdmin() {
      return this.$store.getters['auth/username'] === 'admin'
    },
  },
  methods: {
    async fetch() {
      const response = await axios.get(`/api/create-group`)
      // エラーの処理
      if (response.status !== OK) {
        this.$store.commit('error/setCode', response.status)
        return false
      }
      // 成功の場合、問題の情報をプロパティに代入
      this.groups = []
      for (let i = 0; i < response.data.length; i++) {
        this.groups.push(response.data[i].name)
      }
    },
    async register() {
      // 重複していた場合は、入力値をクリアする
      if (this.groups.includes(this.group)) {
        this.group = ''
        return false
      }

      // 入力内容で、GroupController@createを起動
      // 返却されたオブジェクトをresponseに代入
      const response = await axios.post('/api/create-group/register', {
        name: this.group,
      })
      // バリデーションエラー
      if (response.status === UNPROCESSABLE_ENTITY) {
        this.registerErrors = response.data.errors
        return false
      } else if (response.status !== OK) {
        // その他のエラー
        this.$store.commit('error/setCode', response.status)
        return false
      }
      this.fetch()
    },
    clearError() {
      this.$store.commit('error/setCode', null)
    },
  },
  created() {
    this.fetch()
    this.clearError()
  },
}
</script>
<style lang="scss" scoped>
.p-create-group {
  margin-top: 80px;
  font-size: 16px;
  width: 850px;
  &__header {
    display: flex;
    justify-content: space-between;
  }
  &__link {
    display: flex;
    align-items: center;
  }
  &__title {
    font-size: 24px;
  }
  &__sub-title {
    font-size: 16px;
    font-weight: normal;
  }
  &__wrapper {
    background-color: #fff;
    box-shadow: 5px 5px 2px rgba(0, 0, 0, 0.1);
    width: 100%;
    margin-bottom: 20px;
    padding: 18px 25px;
    &--registered {
      height: 250px;
      overflow-y: scroll;
    }
  }
  &__content {
    background-color: #f2efe8;
    box-shadow: 5px 5px 2px rgba(0, 0, 0, 0.1);
    width: 100%;
    padding: 18px 25px;
  }
  &__btn {
    text-align: right;
    margin-top: 10px;
  }
}
</style>
