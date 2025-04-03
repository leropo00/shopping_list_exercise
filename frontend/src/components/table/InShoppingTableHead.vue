<template>
  <tr class="bg-gray-300">
    <th class="w-1/2 py-4 px-6 text-left text-black font-bold uppercase">Item for purchase</th>
    <th class="w-1/2 py-4 px-6 text-left text-black font-bold uppercase">Purchased</th>
    <th class="w-1/2 py-4 px-6 text-left text-black font-bold uppercase">
      <button
        type="button"
        class="cursor-pointer rounded-md bg-indigo-600 hover:bg-indigo-500 px-3 py-1 w-full text-sm/6 font-semibold text-white shadow-sm focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2"
        @click="finishShopping()"
      >
        Finish shopping
      </button>
    </th>
  </tr>
</template>

<script setup>
import { ref } from 'vue'
import usePurchaseListStore from '@/store/purchaseList'
import axiosClient from '@/axios.js'
import { HTTP_CODE_SUCCESS, URL_FINISH_SHOPPING } from '@/constants.js'

const listStore = usePurchaseListStore()

function finishShopping() {
  axiosClient.post(URL_FINISH_SHOPPING).then(async (response) => {
    if (response.status === HTTP_CODE_SUCCESS) {
      await listStore.fetchList()
    }
  })
}
</script>

<style scoped></style>
