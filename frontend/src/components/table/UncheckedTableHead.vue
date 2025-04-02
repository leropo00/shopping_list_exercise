<template>
  <tr class="bg-gray-300">
    <th class="w-1/4 py-4 px-6 text-left text-black font-bold uppercase">Item</th>
    <th class="w-1/4 py-4 px-6 text-left text-black font-bold uppercase">Quantity</th>
    <th class="w-1/4 py-4 px-6 text-left text-black font-bold uppercase">Actions</th>
  </tr>
  <tr class="bg-white">
    <th class="w-1/2 py-4 px-6 text-left font-bold uppercase">
      <input
        type="text"
        class="border-black outline w-full"
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
        class="border-black outline w-8 md:w-32"
        id="new_item_quantity"
        v-model="itemInsertedData.quantity"
        @keyup.enter="addItem()"
      />
    </th>
    <th class="w-1/2 py-4 px-6 text-left font-bold uppercase">
      <button
        type="button"
        class="cursor-pointer md:mr-3 lg:mr-6"
        title="Add item"
        @click="addItem()"
      >
        <PlusCircleIcon class="block size-6" aria-hidden="true" />
      </button>
      <button
        type="button"
        class="cursor-pointer"
        title="Shop for items"
        @click="startShopping()"
        v-if="inShoppingCounts == 0 && uncheckedCounts > 0"
      >
        <ShoppingCartIcon class="block size-6" aria-hidden="true" />
      </button>
    </th>
  </tr>
</template>

<script setup>
import { ref, computed } from 'vue'
import usePurchaseListStore from '@/store/purchaseList'
import axiosClient from '@/axios.js'
import {
  URL_CREATE_PURCHASE_ITEM,
  URL_START_SHOPPING,
  HTTP_CODE_CREATED,
  HTTP_CODE_SUCCESS,
  ITEM_STATUS_UNCHECKED,
  ITEM_STATUS_IN_SHOPPING,
} from '@/constants.js'
import { PlusCircleIcon, ShoppingCartIcon } from '@heroicons/vue/24/solid'

const listStore = usePurchaseListStore()

const uncheckedCounts = computed(
  () => listStore.data.filter((item) => item.status == ITEM_STATUS_UNCHECKED).length,
)

const inShoppingCounts = computed(
  () => listStore.data.filter((item) => item.status == ITEM_STATUS_IN_SHOPPING).length,
)

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

function startShopping() {
  axiosClient
    .post(URL_START_SHOPPING)
    .then(async (response) => {
      if (response.status === HTTP_CODE_SUCCESS) {
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
