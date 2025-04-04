<template>
  <tr class="bg-gray-300">
    <th class="w-1/2 py-4 px-6 text-left text-black font-bold uppercase">Item for purchase</th>
    <th class="w-1/2 py-4 px-6 text-left text-black font-bold uppercase">Purchased</th>
    <th class="w-1/2 py-4 px-6 text-left text-black font-bold uppercase">
      <button
        type="button"
        class="cursor-pointer rounded-md bg-indigo-600 hover:bg-indigo-500 px-3 py-1 w-full text-sm/6 font-semibold text-white shadow-sm focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2"
        v-if="shoppingOwners.includes(userId)"
        @click="finishShopping()"
      >
        Finish shopping
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
}
</script>

<style scoped></style>
