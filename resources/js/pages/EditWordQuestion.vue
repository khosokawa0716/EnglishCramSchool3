<template>
  <div>
    <h1>問題作成</h1>
    <ul>
      <li><router-link to="/admin">管理者のマイページ</router-link></li>
      <li><router-link to="/word-question-list">問題一覧</router-link></li>
    </ul>
    <div v-if="page === 1">
      <v-form @submit.prevent="editWordQuestion">
        <v-select
          v-model="editWordQuestionForm.group"
          :items="editWordQuestionForm.groupsName"
          label="グループ"
        ></v-select>
        <v-text-field
          v-model="editWordQuestionForm.japanese"
          :rules="japaneseRules"
          :counter="20"
          label="日本語"
          required
        ></v-text-field>
        <v-text-field
          v-model="editWordQuestionForm.choice1"
          :rules="choicesRules"
          :counter="30"
          label="選択肢1"
          required
        ></v-text-field>
        <v-text-field
          v-model="editWordQuestionForm.choice2"
          :rules="choicesRules"
          :counter="30"
          label="選択肢2"
          required
        ></v-text-field>
        <v-text-field
          v-model="editWordQuestionForm.choice3"
          :rules="choicesRules"
          :counter="30"
          label="選択肢3"
          required
        ></v-text-field>
      </v-form>
      <v-container class="px-0" fluid>
        <h3>正解の選択肢</h3>
        <v-radio-group v-model="editWordQuestionForm.answer">
          <v-radio
            v-for="n in 3"
            :key="n"
            :label="`選択肢 ${n}`"
            :value="n"
          ></v-radio>
        </v-radio-group>
      </v-container>
      <v-btn type="submit" color="secondary" @click="fetch">編集前に戻す</v-btn>
      <v-btn type="submit" color="primary" @click="nextPage">次へ</v-btn>
    </div>
    <div v-else>
      <div v-if="editErrors" class="errors">
        <ul v-if="editErrors.group">
          <li v-for="msg in editErrors.group" :key="msg">
            {{ msg }}
          </li>
        </ul>
        <ul v-if="editErrors.japanese">
          <li v-for="msg in editErrors.japanese" :key="msg">
            {{ msg }}
          </li>
        </ul>
        <ul v-if="editErrors.choice1">
          <li v-for="msg in editErrors.choice1" :key="msg">
            {{ msg }}
          </li>
        </ul>
        <ul v-if="editErrors.choice2">
          <li v-for="msg in editErrors.choice2" :key="msg">
            {{ msg }}
          </li>
        </ul>
        <ul v-if="editErrors.choice3">
          <li v-for="msg in editErrors.choice3" :key="msg">
            {{ msg }}
          </li>
        </ul>
        <ul v-if="editErrors.answer">
          <li v-for="msg in editErrors.choice1" :key="msg">
            {{ msg }}
          </li>
        </ul>
      </div>
      <v-container>
        <dl>
          <dt>グループ</dt>
          <dd>{{ editWordQuestionForm.group }}</dd>
          <dt>日本語</dt>
          <dd>{{ editWordQuestionForm.japanese }}</dd>
          <dt>選択肢1</dt>
          <dd>{{ editWordQuestionForm.choice1 }}</dd>
          <dt>選択肢2</dt>
          <dd>{{ editWordQuestionForm.choice2 }}</dd>
          <dt>選択肢3</dt>
          <dd>{{ editWordQuestionForm.choice3 }}</dd>
          <dt>正解の選択肢</dt>
          <dd>{{ editWordQuestionForm.answer }}</dd>
        </dl>
        <v-btn type="submit" color="secondary" @click="prePage">戻る</v-btn>
        <v-btn type="submit" color="primary" @click="update">更新</v-btn>
      </v-container>
    </div>
  </div>
</template>

<script>
import { OK, UNPROCESSABLE_ENTITY } from '../util'

export default {
  data() {
    return {
      page: 1,
      groupsId: [],
      editWordQuestionForm: {
        id: this.$route.params.id,
        group: '',
        group_id: 0,
        groupsName: [],
        japanese: '',
        choice1: '',
        choice2: '',
        choice3: '',
        answer: 1,
      },
      japaneseRules: [
        (v) => !!v || '日本語は必ず入れてください',
        (v) => v.length <= 20 || '日本語は２０もじ以下で入れてください',
      ],
      choicesRules: [
        (v) => !!v || '選択肢３つは必ず入れてください',
        (v) => v.length <= 30 || '選択肢は３０もじ以下で入れてください',
      ],
      editErrors: null,
    }
  },
  methods: {
    // 編集しようとする問題をとってくる
    async fetch() {
      // WordQuestionController@editの起動
      // 返却されたオブジェクトをresponseに代入
      const response = await axios.get(
        `/api/edit-word-question/${this.editWordQuestionForm.id}`
      )
      const responseGroup = await axios.get(`/api/create-group`)

      // エラーの処理
      if (response.status !== OK) {
        this.$store.commit('error/setCode', response.status)
        return false
      }
      // 成功の場合、問題の情報をプロパティに代入
      this.editWordQuestionForm.group_id = response.data.group_id
      this.editWordQuestionForm.japanese = response.data.japanese
      this.editWordQuestionForm.choice1 = response.data.choice1
      this.editWordQuestionForm.choice2 = response.data.choice2
      this.editWordQuestionForm.choice3 = response.data.choice3
      this.editWordQuestionForm.answer = response.data.answer
      this.editWordQuestionForm.groupsName = responseGroup.data.map(
        (obj) => obj.name
      )
      this.groupsId = responseGroup.data.map((obj) => obj.id) // responseGroupからidだけを取り出して新しい配列にする
      const isGroupId = (group_id) =>
        group_id === this.editWordQuestionForm.group_id // 配列をチェックする関数。引数がthis.editWordQuestionForm.group_idと正しいかどうか
      const index = this.groupsId.findIndex(isGroupId) // groupのidの配列からgroup_idと等しい数値のindexを探す
      this.editWordQuestionForm.group =
        this.editWordQuestionForm.groupsName[index]
    },
    async update() {
      const isGroupName = (group_name) =>
        group_name === this.editWordQuestionForm.group // 配列をチェックする関数。引数がthis.editWordQuestionForm.groupsNameと正しいかどうか
      // 選択されたgroupからselected_group_idを求める
      const groupNameIndex =
        this.editWordQuestionForm.groupsName.findIndex(isGroupName) // groupのidの配列からgroup_idと等しい数値のindexを探す
      const selected_group_id = this.groupsId[groupNameIndex]

      // 入力内容で、WordQuestionController@updateを起動
      // 返却されたオブジェクトをresponseに代入
      const response = await axios.put(
        `/api/edit-word-question/${this.editWordQuestionForm.id}`,
        this.editWordQuestionForm,
        {
          params: {
            selected_group_id: selected_group_id,
          },
        }
      )
      // バリデーションエラー
      if (response.status === UNPROCESSABLE_ENTITY) {
        this.editErrors = response.data.errors
        return false
      } else if (response.status !== OK) {
        // その他のエラー
        this.$store.commit('error/setCode', response.status)
        return false
      }
      this.$router.push('/admin')
    },
    clearError() {
      this.$store.commit('error/setCode', null)
    },
    nextPage() {
      this.page = 2
    },
    prePage() {
      this.page = 1
    },
  },
  created() {
    this.clearError()
  },
  // 初期値を反映させるために、画面遷移直後にfetchProjectメソッドを呼ぶ
  watch: {
    $route: {
      handler: function () {
        this.fetch()
      },
      immediate: true,
    },
  },
}
</script>
