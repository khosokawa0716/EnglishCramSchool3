<template>
  <div class="p-admin-mypage">
    <div class="p-admin-mypage__header">
      <h1 class="p-admin-mypage__title">管理者のマイページ</h1>
      <router-link to="/mypage" v-if="isAdmin" class="p-admin-mypage__link"
        >マイページ</router-link
      >
    </div>
    <div class="p-admin-mypage__wrapper">
      <ul>
        <li><router-link to="/create-group">グループ作成</router-link></li>
        <li><router-link to="/create-word-question">問題作成</router-link></li>
        <li><router-link to="/word-question-list">問題一覧</router-link></li>
      </ul>
    </div>
    <div class="p-admin-mypage__wrapper">
      <h3 class="p-admin-mypage__sub-title">最近の利用状況</h3>
      <v-container>
        <v-simple-table>
          <thead>
            <tr>
              <th>id</th>
              <th>ユーザー</th>
              <th>グループ</th>
              <th>正解した数</th>
              <th>問題の数</th>
              <th>問題を解いた日</th>
            </tr>
          </thead>
          <tbody>
            <template v-for="history in histories">
              <tr :key="history.id">
                <td>{{ history.id }}</td>
                <td>{{ findUserName(history.user_id) }}</td>
                <td>{{ findGroupName(history.group_id) }}</td>
                <td>{{ history.number_of_correct_answers }}</td>
                <td>{{ history.number_answers }}</td>
                <td>{{ history.created_at }}</td>
              </tr>
            </template>
          </tbody>
        </v-simple-table>
        <div class="text-center">
          <v-pagination
            v-model="page"
            :length="last_page"
            @input="fetch"
          ></v-pagination>
        </div>
      </v-container>
    </div>
  </div>
</template>

<script>
import { OK } from '../util'

export default {
  data() {
    return {
      histories: [],
      page: 1,
      last_page: 1,
      groupsId: [],
      groupsName: [],
      usersId: [],
      usersName: [],
    }
  },
  computed: {
    isAdmin() {
      return this.$store.getters['auth/username'] === 'admin'
    },
  },
  methods: {
    async fetch(page) {
      const response = await axios.get(`/api/admin`, {
        params: {
          page: parseInt(page),
        },
      })
      const responseGroup = await axios.get(`/api/create-group`)
      const responseUsers = await axios.get(`/api/users`)
      // エラーの処理
      if (response.status !== OK) {
        this.$store.commit('error/setCode', response.status)
        return false
      }
      // 成功の場合、問題の情報をプロパティに代入
      this.histories = response.data.data
      this.page = response.data.current_page
      this.last_page = response.data.last_page
      this.groupsId = responseGroup.data.map((obj) => obj.id) // responseGroupからidだけを取り出して新しい配列にする
      this.groupsName = responseGroup.data.map((obj) => obj.name) // responseGroupからnameだけを取り出して新しい配列にする
      this.usersId = responseUsers.data.map((obj) => obj.id) // responseGroupからidだけを取り出して新しい配列にする
      this.usersName = responseUsers.data.map((obj) => obj.name) // responseGroupからnameだけを取り出して新しい配列にする

      // URL変更
      let url = '/admin?page=' + this.page
      window.history.pushState(null, null, url)
    },
    findGroupName(val) {
      const isGroupId = (group_id) => group_id === val // 配列をチェックする関数。引数がvalと正しいかどうか
      const index = this.groupsId.findIndex(isGroupId) // groupのidの配列からgroup_idと等しい数値のindexを探す
      return this.groupsName[index]
    },
    findUserName(val) {
      const isUserId = (user_id) => user_id === val // 配列をチェックする関数。引数がvalと正しいかどうか
      const index = this.usersId.findIndex(isUserId) // groupのidの配列からgroup_idと等しい数値のindexを探す
      return this.usersName[index]
    },
  },
  created() {
    this.fetch(this.page)
  },
}
</script>
<style lang="scss" scoped>
.p-admin-mypage {
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
  }
}
</style>
