<template>
  <div>
    <h1>グループ作成</h1>
    <ul>
      <li><router-link to="/admin">管理者のマイページ</router-link></li>
    </ul>
    <div v-if="registerErrors" class="errors">
      <ul v-if="registerErrors.group">
        <li v-for="msg in registerErrors.group" :key="msg">
          {{ msg }}
        </li>
      </ul>
    </div>
    <v-form @submit.prevent="register">
      <v-text-field
        v-model="group"
        :rules="groupRules"
        :counter="20"
        label="グループ"
        required
      ></v-text-field>
    </v-form>
    <v-btn type="submit" color="primary" @click="register">グループ登録</v-btn>
    <div>
      <span>登録されたグループ</span>
      <ul v-for="group in groups" :key="group.index">
        <li>{{ group }}</li>
      </ul>
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
      console.log('登録できました')
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
