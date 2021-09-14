<template>
  <div>
    <h1>英語塾（えいごじゅく）</h1>
    <p>このアプリは、たのしくえいごをまなぶことができます。</p>
    <div v-if="isLogin">
      <RouterLink to="/mypage"> {{ username }}さんのマイページ </RouterLink>
    </div>
    <div v-else>
      <RouterLink to="/login"> ログイン / ユーザーとうろく </RouterLink>
    </div>
  </div>
</template>
<script>
export default {
  computed: {
    isLogin() {
      return this.$store.getters['auth/check']
    },
    username() {
      return this.$store.getters['auth/username']
    },
  },
}
</script>
