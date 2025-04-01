<template>
  <tr class="bg-gray-100">
    <th class="w-1/4 py-4 px-6 text-left text-gray-600 font-bold uppercase">Item</th>
    <th class="w-1/4 py-4 px-6 text-left text-gray-600 font-bold uppercase">Quantity</th>
    <th class="w-1/4 py-4 px-6 text-left text-gray-600 font-bold uppercase">Actions</th>
  </tr>
  <tr class="bg-white">
    <th class="w-1/2 py-4 px-6 text-left font-bold uppercase">
      <input
        type="text"
        class="border-black outline"
        id="new_item_name"
        placeholder="Item name"
        v-model="itemInsertedData.item_name"
        @keyup.enter="addItem()"
      />
    </th>
    <th class="w-1/2 py-4 px-6 text-left font-bold uppercase">
      <input
        type="number"
        min="1"
        class="border-black outline w-24"
        id="new_item_quantity"
        v-model="itemInsertedData.quantity"
        @keyup.enter="addItem()"
      />
    </th>
    <th class="w-1/2 py-4 px-6 text-left font-bold uppercase">
      <button type="button" class="border-black outline p-2" @click="addItem()">Add item</button>
    </th>
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
