<template>
  <div class="p-answer-question">
    <h1 class="p-answer-question__title">もんだいをとく</h1>
    <div class="p-answer-question__wrapper">
      <v-container v-if="end" class="p-answer-question__content">
        <h3 class="p-answer-question__sub-title">もんだいしゅうりょう</h3>
        <p>
          {{ questions.length }}もんのうち{{
            numberOfCorrectAnswers
          }}もんせいかい！
        </p>
        <p v-show="questions.length === numberOfCorrectAnswers">
          ぜんもんせいかい、すごいね！
        </p>
        <router-link to="/mypage" class="p-answer-question__link"
          >マイページ</router-link
        >
      </v-container>
      <v-container v-else class="p-answer-question__content">
        <h3 class="p-answer-question__sub-title">{{ findGroupName(id) }}</h3>
        <ul v-for="(question, index) in questions" :key="question.id">
          <li v-show="index === current">
            <p>{{ index + 1 }}/{{ questions.length }}</p>
            <span class="p-answer-question__japanese">{{
              question.japanese
            }}</span>
            <v-radio-group v-model="selectedAnswers[index]">
              <v-radio :label="question.choice1" :value="1" />
              <v-radio :label="question.choice2" :value="2" />
              <v-radio :label="question.choice3" :value="3" />
            </v-radio-group>
          </li>
        </ul>
        <v-dialog v-model="dialog" persistent width="500">
          <template v-slot:activator="{ on, attrs }">
            <v-btn
              color="primary"
              dark
              v-bind="attrs"
              v-on="on"
              @click="answerTheQuestion"
            >
              かいとう
            </v-btn>
          </template>
          <v-card>
            <v-card-title class="text-h5 grey lighten-2">
              {{ judgement }}
            </v-card-title>
            <v-card-text>
              <ul class="p-answer-question__answer">
                <li>あなたがえらんだかいとう： {{ selectedAnswer }}</li>
                <li>せいかい： {{ answer }}</li>
              </ul>
            </v-card-text>
            <v-divider></v-divider>
            <v-card-actions>
              <v-spacer></v-spacer>
              <v-btn v-if="lastQuestion" color="primary" @click="endQuestion">
                もんだいしゅうりょう
              </v-btn>
              <v-btn v-else color="primary" @click="nextQuestion">
                つぎのもんだい
              </v-btn>
            </v-card-actions>
          </v-card>
        </v-dialog>
      </v-container>
    </div>
  </div>
</template>

<script>
import { OK, UNPROCESSABLE_ENTITY } from '../util'

export default {
  data() {
    return {
      questions: [],
      answers: [],
      choices: [],
      judgement: '',
      end: false,
      numberOfCorrectAnswers: 0,
      current: 0,
      dialog: false,
      selectedAnswers: [],
      selectedAnswer: '',
      answer: '',
      id: this.$route.params.id,
      groupsId: [],
      groupsName: [],
    }
  },
  computed: {
    lastQuestion() {
      return this.current + 1 === this.questions.length
    },
    userid() {
      return this.$store.getters['auth/userid']
    },
  },
  methods: {
    // 問題をとってくる
    async fetch() {
      const response = await axios.get(`/api/answer-word-question/${this.id}`)
      const responseGroup = await axios.get(`/api/create-group`)
      // エラーの処理
      if (response.status !== OK) {
        this.$store.commit('error/setCode', response.status)
        return false
      }
      // 成功の場合、問題の情報をプロパティに代入
      this.groupsId = responseGroup.data.map((obj) => obj.id) // responseGroupからidだけを取り出して新しい配列にする
      this.groupsName = responseGroup.data.map((obj) => obj.name) // responseGroupからnameだけを取り出して新しい配列にする
      this.questions = response.data
      for (let i = 0; i < this.questions.length; i++) {
        this.selectedAnswers.push(1)
        this.answers.push(this.questions[i].answer)
        let choiceArray = []
        choiceArray.push(this.questions[i].choice1)
        choiceArray.push(this.questions[i].choice2)
        choiceArray.push(this.questions[i].choice3)
        this.choices.push(choiceArray)
      }
    },
    answerTheQuestion() {
      const currentIndex = this.current
      const currentSelectedAnswerIndex = this.selectedAnswers[currentIndex] - 1
      const currentAnswerIndex = this.answers[currentIndex] - 1
      this.selectedAnswer =
        this.choices[currentIndex][currentSelectedAnswerIndex]
      this.answer = this.choices[currentIndex][currentAnswerIndex]
      if (currentSelectedAnswerIndex === currentAnswerIndex) {
        this.judgement = 'せいかいです！'
        this.numberOfCorrectAnswers++
      } else {
        this.judgement = 'ざんねん、ちがうよ...'
      }
    },
    nextQuestion() {
      this.dialog = false
      this.current++
    },
    endQuestion() {
      this.dialog = false
      this.end = true
      this.current++
      this.registerHistory()
    },
    findGroupName(val) {
      const isGroupId = (group_id) => group_id === Number(val) // 配列をチェックする関数。引数がvalと正しいかどうか
      const index = this.groupsId.findIndex(isGroupId) // groupのidの配列からgroup_idと等しい数値のindexを探す
      return this.groupsName[index]
    },
    async registerHistory() {
      // 入力内容で、HistoryController@createを起動
      // 返却されたオブジェクトをresponseに代入
      const response = await axios.post('/api/answer-word-question/register', {
        user_id: this.userid,
        group_id: Number(this.id),
        number_of_correct_answers: this.numberOfCorrectAnswers,
        number_answers: this.questions.length,
      })
      // バリデーションエラー
      if (response.status === UNPROCESSABLE_ENTITY) {
        // this.registerErrors = response.data.errors
        return false
      } else if (response.status !== OK) {
        // その他のエラー
        this.$store.commit('error/setCode', response.status)
        return false
      }
    },
  },
  created() {
    this.fetch()
  },
}
</script>
<style lang="scss" scoped>
.p-answer-question {
  margin-top: 80px;
  font-size: 16px;
  width: 850px;
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
  &__japanese {
    font-size: 24px;
    font-weight: bold;
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
  &__answer {
    margin-top: 10px;
    font-size: 16px;
  }
}
</style>
