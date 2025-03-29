<script setup>
import axiosClient from "../axios.js";
import router from "../router.js";
import useUserStore from "../store/user.js";
import {computed} from "vue";

const userStore = useUserStore()

const user = computed(() => userStore.user)

function logout() {
  axiosClient.post(URL_LOGOUT)
      .then((response) => {
        router.push({name: 'Login'})
      })
}
</script>

<template>
    <RouterView/>
</template>

<style scoped>
  
</style>