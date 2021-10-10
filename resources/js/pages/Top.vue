<template>
  <div class="p-top">
    <p>えいごじゅくは、クイズでえいごをまなぶことができます。</p>
    <p v-if="isLogin">
      <RouterLink to="/mypage"> マイページ </RouterLink>
    </p>
    <p v-else>
      はじめに<RouterLink to="/login"> ログイン / ユーザーとうろく </RouterLink
      >してください。
    </p>
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
<style lang="scss" scoped>
.p-top {
  margin-top: 220px;
  font-size: 18px;
  text-align: center;
}
</style>
