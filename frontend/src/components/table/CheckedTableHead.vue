<template>
  <tr class="bg-gray-300">
    <th colspan="2" class="w-2/3 py-4 px-6 text-left text-black font-bold uppercase">
      Item purchased
    </th>
    <th class="w-1/3 py-4 px-6 text-left text-black font-bold uppercase">Purchased date</th>
  </tr>
</template>

<script setup>
import { ref } from 'vue'
import usePurchaseListStore from '@/store/purchaseList'
import axiosClient from '@/axios.js'
import { URL_CREATE_PURCHASE_ITEM, HTTP_CODE_CREATED } from '@/constants.js'

const listStore = usePurchaseListStore()

const itemInsertedData = ref({
  item_name: '',
  quantity: 1,
})

function addItem() {
  axiosClient
    .post(URL_CREATE_PURCHASE_ITEM, itemInsertedData.value)
    .then(async (response) => {
      if (response.status === HTTP_CODE_CREATED) {
        itemInsertedData.value.item_name = ''
        itemInsertedData.value.quantity = 1
        await listStore.fetchList()
      }
    })
    .catch((error) => {
      console.log(error)
      // errors.value = error.response.data.errors
    })
}
</script>

<style scoped></style>
