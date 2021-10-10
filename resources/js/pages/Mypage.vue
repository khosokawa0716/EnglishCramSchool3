<template>
  <div class="p-mypage">
    <div class="p-mypage__header">
      <h1 class="p-mypage__title">マイページ</h1>
      <router-link to="/admin" v-if="isAdmin" class="p-mypage__link"
        >管理者用のマイページ</router-link
      >
    </div>
    <div class="p-mypage__wrapper">
      <div class="p-mypage__wrapper--question">
        <h3 class="p-mypage__sub-title">もんだいをとく</h3>
        <ul v-for="group in groups" :key="group.index">
          <li>
            <router-link :to="`/answer-word-question/${group.id}`">{{
              group.name
            }}</router-link>
          </li>
        </ul>
      </div>
    </div>
    <div class="p-mypage__wrapper">
      <div class="p-mypage__wrapper--history">
        <h3 class="p-mypage__sub-title">あしあと</h3>
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
<style lang="scss" scoped>
.p-mypage {
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
    &--question {
      height: 150px;
      overflow-y: scroll;
    }
    &--history {
      height: 300px;
      overflow-y: scroll;
    }
  }
}
</style>
