<template>
  <div>
    <h1>マイページ</h1>
    <ul v-if="isAdmin">
      <li><router-link to="/admin">管理者用のマイページ</router-link></li>
    </ul>
    <p>ようこそ {{ username }} さん</p>
    <h3>もんだいをとく</h3>
    <ul v-for="group in groups" :key="group.index">
      <li>
        <router-link :to="`/answer-word-question/${group.id}`">{{
          group.name
        }}</router-link>
      </li>
    </ul>
    <div>
      <h3>あしあと</h3>
      <v-container>
        <v-simple-table>
          <thead>
            <tr>
              <th>グループ</th>
              <th>正解した数</th>
              <th>問題の数</th>
              <th>問題を解いた日</th>
            </tr>
          </thead>
          <tbody>
            <template v-for="history in histories">
              <tr :key="history.id">
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
      groups: [],
      histories: [],
      page: 1,
      last_page: 1,
    }
  },
  computed: {
    isAdmin() {
      return this.$store.getters['auth/username'] === 'admin'
    },
    userid() {
      return this.$store.getters['auth/userid']
    },
    username() {
      return this.$store.getters['auth/username']
    },
  },
  methods: {
    async fetch(page) {
      const response = await axios.get(`/api/create-group`)
      const responseHistories = await axios.get(
        `/api/histories/${this.userid}`,
        {
          params: {
            page: parseInt(page),
          },
        }
      )
      // エラーの処理
      if (response.status !== OK) {
        this.$store.commit('error/setCode', response.status)
        return false
      }
      // 成功の場合、問題の情報をプロパティに代入
      this.groups = []
      for (let i = 0; i < response.data.length; i++) {
        let group = new Object()
        group.id = response.data[i].id
        group.name = response.data[i].name
        this.groups.push(group)
      }
      this.histories = responseHistories.data.data
      this.page = responseHistories.data.current_page
      this.last_page = responseHistories.data.last_page

      // URL変更
      let url = '/mypage?page=' + this.page
      window.history.pushState(null, null, url)
    },
    findGroupName(val) {
      const isGroupId = (group_id) => group_id === val // 配列をチェックする関数。引数がvalと正しいかどうか
      const groupsId = this.groups.map((obj) => obj.id) // goupsからidだけを取り出して新しい配列にする
      const groupsName = this.groups.map((obj) => obj.name) // groupsからnameだけを取り出して新しい配列にする
      const index = groupsId.findIndex(isGroupId) // groupのidの配列からgroup_idと等しい数値のindexを探す
      return groupsName[index]
    },
  },
  created() {
    this.fetch(this.page)
  },
}
</script>
