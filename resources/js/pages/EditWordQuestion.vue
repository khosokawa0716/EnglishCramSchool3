<template>
  <div class="p-edit-question">
    <div class="p-edit-question__header">
      <h1 class="p-edit-question__title">問題編集</h1>
      <router-link
        to="/word-question-list"
        v-if="isAdmin"
        class="p-edit-question__link"
        >問題一覧</router-link
      >
    </div>
    <div class="p-edit-question__wrapper">
      <div class="p-edit-question__content">
        <div v-if="page === 1">
          <v-form @submit.prevent="editWordQuestion">
            <p class="p-edit-question__form-label">グループ</p>
            <v-select
              solo
              dense
              v-model="editWordQuestionForm.group"
              :items="editWordQuestionForm.groupsName"
            ></v-select>
            <p class="p-edit-question__form-label">日本語</p>
            <v-text-field
              solo
              dense
              v-model="editWordQuestionForm.japanese"
              :rules="japaneseRules"
              :counter="50"
              required
            ></v-text-field>
            <p class="p-edit-question__form-label">選択肢1</p>
            <v-text-field
              solo
              dense
              v-model="editWordQuestionForm.choice1"
              :rules="choicesRules"
              :counter="30"
              required
            ></v-text-field>
            <p class="p-edit-question__form-label">選択肢2</p>
            <v-text-field
              solo
              dense
              v-model="editWordQuestionForm.choice2"
              :rules="choicesRules"
              :counter="30"
              required
            ></v-text-field>
            <p class="p-edit-question__form-label">選択肢3</p>
            <v-text-field
              solo
              dense
              v-model="editWordQuestionForm.choice3"
              :rules="choicesRules"
              :counter="30"
              required
            ></v-text-field>
          </v-form>
          <v-container class="px-0" fluid>
            <h3>正解の選択肢</h3>
            <v-radio-group v-model="editWordQuestionForm.answer" row>
              <v-radio
                v-for="n in 3"
                :key="n"
                :label="`選択肢 ${n}`"
                :value="n"
              ></v-radio>
            </v-radio-group>
          </v-container>
          <div class="p-edit-question__btn">
            <v-btn type="submit" color="secondary" @click="fetch"
              >編集前に戻す</v-btn
            >
            <v-btn type="submit" color="primary" @click="nextPage">次へ</v-btn>
          </div>
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
            <p class="p-edit-question__form-label">
              この内容で更新します。よろしいですか？
            </p>
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
            <div class="p-edit-question__btn">
              <v-btn type="submit" color="secondary" @click="prePage"
                >戻る</v-btn
              >
              <v-btn type="submit" color="primary" @click="update">更新</v-btn>
            </div>
          </v-container>
        </div>
      </div>
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
        (v) => v.length <= 50 || '日本語は５０もじ以下で入れてください',
      ],
      choicesRules: [
        (v) => !!v || '選択肢３つは必ず入れてください',
        (v) => v.length <= 30 || '選択肢は３０もじ以下で入れてください',
      ],
      editErrors: null,
    }
  },
  computed: {
    isAdmin() {
      return this.$store.getters['auth/username'] === 'admin'
    },
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
<style lang="scss" scoped>
.p-edit-question {
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
  &__content {
    background-color: #f2efe8;
    box-shadow: 5px 5px 2px rgba(0, 0, 0, 0.1);
    width: 100%;
    padding: 18px 25px;
    height: 60vh;
    overflow-y: scroll;
  }
  &__form {
    padding: 40px;
    &-label {
      margin-bottom: 2px;
    }
  }
  &__btn {
    text-align: right;
    margin-top: 10px;
  }
  dl {
    display: flex;
    flex-wrap: wrap;
    width: 100%;
    margin-top: 10px;
  }
  dt {
    width: 20%;
    padding-bottom: 20px;
    margin-bottom: 10px;
  }
  dd {
    width: 80%;
    padding-bottom: 20px;
    margin-bottom: 10px;
  }
}
</style>
