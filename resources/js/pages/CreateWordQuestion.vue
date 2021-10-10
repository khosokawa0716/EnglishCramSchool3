<template>
  <div class="p-create-question">
    <div class="p-create-question__header">
      <h1 class="p-create-question__title">問題作成</h1>
      <router-link to="/admin" v-if="isAdmin" class="p-create-question__link"
        >管理者のマイページ</router-link
      >
    </div>
    <div class="p-create-question__wrapper">
      <div class="p-create-question__content">
        <div v-if="page === 1">
          <v-form @submit.prevent="createWordQuestion">
            <p class="p-create-question__form-label">グループ</p>
            <v-select
              solo
              dense
              v-model="createWordQuestionForm.group"
              :items="createWordQuestionForm.groupsName"
            ></v-select>
            <p class="p-create-question__form-label">日本語</p>
            <v-text-field
              solo
              dense
              v-model="createWordQuestionForm.japanese"
              :rules="japaneseRules"
              :counter="20"
              required
            ></v-text-field>
            <p class="p-create-question__form-label">選択肢1</p>
            <v-text-field
              solo
              dense
              v-model="createWordQuestionForm.choice1"
              :rules="choicesRules"
              :counter="30"
              required
            ></v-text-field>
            <p class="p-create-question__form-label">選択肢2</p>
            <v-text-field
              solo
              dense
              v-model="createWordQuestionForm.choice2"
              :rules="choicesRules"
              :counter="30"
              required
            ></v-text-field>
            <p class="p-create-question__form-label">選択肢3</p>
            <v-text-field
              solo
              dense
              v-model="createWordQuestionForm.choice3"
              :rules="choicesRules"
              :counter="30"
              required
            ></v-text-field>
          </v-form>
          <v-container class="px-0" fluid>
            <p class="p-create-question__form-label">正解の選択肢</p>
            <v-radio-group v-model="createWordQuestionForm.answer" row>
              <v-radio
                v-for="n in 3"
                :key="n"
                :label="`${n}`"
                :value="n"
              ></v-radio>
            </v-radio-group>
          </v-container>
          <div class="p-create-question__btn">
            <v-btn type="submit" color="secondary" @click="clearForm"
              >クリア</v-btn
            >
            <v-btn type="submit" color="primary" @click="nextPage">次へ</v-btn>
          </div>
        </div>
        <div v-else>
          <div v-if="registerErrors" class="errors">
            <ul v-if="registerErrors.group">
              <li v-for="msg in registerErrors.group" :key="msg">
                {{ msg }}
              </li>
            </ul>
            <ul v-if="registerErrors.japanese">
              <li v-for="msg in registerErrors.japanese" :key="msg">
                {{ msg }}
              </li>
            </ul>
            <ul v-if="registerErrors.choice1">
              <li v-for="msg in registerErrors.choice1" :key="msg">
                {{ msg }}
              </li>
            </ul>
            <ul v-if="registerErrors.choice2">
              <li v-for="msg in registerErrors.choice2" :key="msg">
                {{ msg }}
              </li>
            </ul>
            <ul v-if="registerErrors.choice3">
              <li v-for="msg in registerErrors.choice3" :key="msg">
                {{ msg }}
              </li>
            </ul>
            <ul v-if="registerErrors.answer">
              <li v-for="msg in registerErrors.choice1" :key="msg">
                {{ msg }}
              </li>
            </ul>
          </div>
          <v-container>
            <p class="p-create-question__form-label">
              この内容で登録します。よろしいですか？
            </p>
            <dl>
              <dt>グループ：</dt>
              <dd>{{ createWordQuestionForm.group }}</dd>
              <dt>日本語：</dt>
              <dd>{{ createWordQuestionForm.japanese }}</dd>
              <dt>選択肢1：</dt>
              <dd>{{ createWordQuestionForm.choice1 }}</dd>
              <dt>選択肢2：</dt>
              <dd>{{ createWordQuestionForm.choice2 }}</dd>
              <dt>選択肢3：</dt>
              <dd>{{ createWordQuestionForm.choice3 }}</dd>
              <dt>正解の選択肢：</dt>
              <dd>{{ createWordQuestionForm.answer }}</dd>
            </dl>
            <div class="p-create-question__btn">
              <v-btn type="submit" color="secondary" @click="prePage"
                >戻る</v-btn
              >
              <v-btn type="submit" color="primary" @click="register"
                >登録</v-btn
              >
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
      createWordQuestionForm: {
        group: '',
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
      registerErrors: null,
    }
  },
  computed: {
    isAdmin() {
      return this.$store.getters['auth/username'] === 'admin'
    },
    groupId() {
      const isGroup = (group) => group === this.createWordQuestionForm.group
      const index = this.createWordQuestionForm.groupsName.findIndex(isGroup)
      return this.groupsId[index]
    },
  },
  methods: {
    async fetchGroups() {
      const response = await axios.get(`/api/create-group`)
      // エラーの処理
      if (response.status !== OK) {
        this.$store.commit('error/setCode', response.status)
        return false
      }
      // 成功の場合、問題の情報をプロパティに代入
      this.createWordQuestionForm.groupsName = response.data.map(
        (obj) => obj.name
      )
      this.groupsId = response.data.map((obj) => obj.id)
    },
    async register() {
      // 入力内容で、WordQuestionController@createを起動
      // 返却されたオブジェクトをresponseに代入
      const response = await axios.post(
        '/api/create-word-question/register',
        this.createWordQuestionForm,
        {
          params: {
            group_id: this.groupId,
          },
        }
      )
      // バリデーションエラー
      if (response.status === UNPROCESSABLE_ENTITY) {
        this.registerErrors = response.data.errors
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
    clearForm() {
      this.createWordQuestionForm.group = ''
      this.createWordQuestionForm.japanese = ''
      this.createWordQuestionForm.choice1 = ''
      this.createWordQuestionForm.choice2 = ''
      this.createWordQuestionForm.choice3 = ''
      this.createWordQuestionForm.answer = ''
    },
  },
  created() {
    this.clearError()
    this.fetchGroups()
  },
}
</script>
<style lang="scss" scoped>
.p-create-question {
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
