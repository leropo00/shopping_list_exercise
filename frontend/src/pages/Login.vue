<script setup>
import { ref } from 'vue'

import axiosClient from '../axios.js'
import router from '../router.js'
import { URL_LOGIN, URL_CSRF_COOKIE } from '@/constants.js'
import GuestLayout from '@/components/layout/GuestLayout.vue'
import {useI18n} from 'vue-i18n' 
const {t} = useI18n();

const data = ref({
  email: '',
  password: '',
})
const errorMessage = ref('')

function submit() {
  axiosClient.get(URL_CSRF_COOKIE).then((response) => {
    axiosClient
      .post(URL_LOGIN, data.value)
      .then((response) => {
        router.push({ name: 'Home' })
      })
      .catch((error) => {
        console.log(error.response)
        errorMessage.value = error.response.data.message
      })
  })
}
</script>

<template>
  <GuestLayout>
    <h2 class="mt-10 text-center text-2xl/9 font-bold tracking-tight text-gray-900">
      {{ t('login.title')}}
    </h2>

    <div v-if="errorMessage" class="mt-4 py-2 px-3 rounded text-white bg-red-400">
      {{ errorMessage }}
    </div>

    <div class="mt-4 sm:mx-auto sm:w-full sm:max-w-sm">
      <form @submit.prevent="submit" class="space-y-6">
        <div>
          <label for="email" class="block text-sm/6 font-medium text-gray-900">{{ t('login.field_email')}}
          </label>
          <div class="mt-2">
            <input
              type="email"
              name="email"
              id="email"
              autocomplete="email"
              required=""
              v-model="data.email"
              class="block w-full rounded-md bg-white px-3 py-1.5 text-base text-gray-900 outline outline-1 -outline-offset-1 outline-gray-300 placeholder:text-gray-400 focus:outline focus:outline-2 focus:-outline-offset-2 focus:outline-indigo-600 sm:text-sm/6"
            />
          </div>
        </div>

        <div>
          <div class="flex items-center justify-between">
            <label for="  password" class="block text-sm/6 font-medium text-gray-900"
              >{{ t('login.field_password')}}
              </label
            >
          </div>
          <div class="mt-2">
            <input
              type="password"
              name="password"
              id="password"
              autocomplete="current-password"
              required=""
              v-model="data.password"
              class="block w-full rounded-md bg-white px-3 py-1.5 text-base text-gray-900 outline outline-1 -outline-offset-1 outline-gray-300 placeholder:text-gray-400 focus:outline focus:outline-2 focus:-outline-offset-2 focus:outline-indigo-600 sm:text-sm/6"
            />
          </div>
        </div>

        <div>
          <button
            type="submit"
            class="flex w-full cursor-pointer justify-center rounded-md bg-indigo-600 px-3 py-1.5 text-sm/6 font-semibold text-white shadow-sm hover:bg-indigo-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600"
          >
          {{ t('login.sing_in')}}
          </button>
        </div>
      </form>

      <p class="mt-10 text-center text-sm/6 text-gray-500">
        {{ t('login.not_a_member')}}
        {{ ' ' }}
        <RouterLink
          :to="{ name: 'Signup' }"
          class="font-semibold text-indigo-600 hover:text-indigo-500"
        >
        {{ t('login.create_account')}}
        </RouterLink>
      </p>
    </div>
  </GuestLayout>
</template>

<style scoped></style>
