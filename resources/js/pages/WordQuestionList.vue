<template>
  <div>
    <h1>問題一覧</h1>
    <ul>
      <li><router-link to="/admin">管理者のマイページ</router-link></li>
    </ul>
    <div>
      <v-container>
        <v-simple-table>
          <thead>
            <tr>
              <th>id</th>
              <th>グループ</th>
              <th>日本語</th>
              <th>選択肢1</th>
              <th>選択肢2</th>
              <th>選択肢3</th>
              <th>正解の選択肢</th>
              <th>作成日時</th>
            </tr>
          </thead>
          <tbody>
            <template v-for="question in questions">
              <tr :key="question.id">
                <td>
                  <router-link :to="`/edit-word-question/${question.id}`">{{
                    question.id
                  }}</router-link>
                </td>
                <td>{{ findGroupName(question.group_id) }}</td>
                <td>{{ question.japanese }}</td>
                <td>{{ question.choice1 }}</td>
                <td>{{ question.choice2 }}</td>
                <td>{{ question.choice3 }}</td>
                <td>{{ question.answer }}</td>
                <td>{{ question.created_at }}</td>
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
      questions: [],
      page: 1,
      last_page: 1,
      groupsId: [],
      groupsName: [],
    }
  },
  methods: {
    // 問題をとってくる
    async fetch(page) {
      const response = await axios.get(`/api/word-question-list`, {
        params: {
          page: parseInt(page),
        },
      })
      const responseGroup = await axios.get(`/api/create-group`)
      // エラーの処理
      if (response.status !== OK) {
        this.$store.commit('error/setCode', response.status)
        return false
      }
      // 成功の場合、問題の情報をプロパティに代入
      this.questions = response.data.data
      this.groupsId = responseGroup.data.map((obj) => obj.id) // responseGroupからidだけを取り出して新しい配列にする
      this.groupsName = responseGroup.data.map((obj) => obj.name) // responseGroupからnameだけを取り出して新しい配列にする
      this.page = response.data.current_page
      this.last_page = response.data.last_page
      // URL変更
      let url = '/word-question-list?page=' + this.page
      window.history.pushState(null, null, url)
    },
    findGroupName(val) {
      const isGroupId = (group_id) => group_id === val // 配列をチェックする関数。引数がvalと正しいかどうか
      const index = this.groupsId.findIndex(isGroupId) // groupのidの配列からgroup_idと等しい数値のindexを探す
      return this.groupsName[index]
    },
  },
  created() {
    this.fetch(this.page)
  },
}
</script>
