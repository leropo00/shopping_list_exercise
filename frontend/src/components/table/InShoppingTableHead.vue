<template>
  <tr class="bg-gray-300">
    <th class="w-1/2 py-4 px-6 text-left text-black font-bold uppercase table-cell sm:hidden">
      {{ t('data.in_shopping.header.item_mobile')}}
    </th>
    <th colspan="2" class="w-1/2 sm:w-1/3 py-4 px-6 text-left text-black font-bold uppercase table-cell sm:hidden">
      <button
        type="button"
        class="cursor-pointer rounded-md bg-indigo-600 hover:bg-indigo-500 px-3 py-1 w-full text-sm/6 font-semibold text-white shadow-sm focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2"
        v-if="shoppingOwners.includes(userId)"
        @click="finishShopping()"
      >
        {{ t('data.in_shopping.header.shopping_button')}}
      </button>
    </th>

    <th class="w-1/3 py-4 px-6 text-left text-black font-bold uppercase hidden sm:table-cell">
      {{ t('data.in_shopping.header.item')}}
    </th>
    <th class="w-1/3 py-4 px-6 text-left text-black font-bold uppercase hidden sm:table-cell">
      {{ t('data.in_shopping.header.quantity')}}
    </th>
    <th class="w-1/2 sm:w-1/3 py-4 px-6 text-left text-black font-bold uppercase hidden sm:table-cell">
      <button
        type="button"
        class="cursor-pointer rounded-md bg-indigo-600 hover:bg-indigo-500 px-3 py-1 w-full text-sm/6 font-semibold text-white shadow-sm focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2"
        v-if="shoppingOwners.includes(userId)"
        @click="finishShopping()"
      >
          {{ t('data.in_shopping.header.shopping_button')}}
      </button>
    </th>
  </tr>
</template>

<script setup>
import { computed } from 'vue'
import usePurchaseListStore from '@/store/purchaseList'
import useUserStore from '@/store/user.js'
import axiosClient from '@/axios.js'
import { HTTP_CODE_SUCCESS, ITEM_STATUS_UNCHECKED, URL_FINISH_SHOPPING } from '@/constants.js'
import {useI18n} from 'vue-i18n' 
const {t} = useI18n();

const listStore = usePurchaseListStore()

const userStore = useUserStore()
const userId = computed(() => userStore.user.id)

const shoppingOwners = computed(() =>
  listStore.data
    .map((item) => item.shopping_owner)
    // filtering of only unique values
    .filter((value, index, array) => array.indexOf(value) === index),
)

function finishShopping() {
  axiosClient.post(URL_FINISH_SHOPPING).then(async (response) => {
    if (response.status === HTTP_CODE_SUCCESS) {
      listStore.changeSelectedTab(ITEM_STATUS_UNCHECKED)
      await listStore.fetchList()
    }
  })
  .catch((error) => {
      console.log(error.response.data)
      console.log(error.response.data.message)
      console.log(error.response.data.message.errors[0])
  })
}
</script>

<style scoped></style>
