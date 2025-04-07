<template>
  <tr class="bg-gray-300">
    <th colspan="3" class="w-1/4 py-4 px-6 text-center text-black font-bold uppercase table-cell sm:hidden">        
        {{ t('data.unchecked.header.item_mobile')}}
    </th>

    <th class="w-1/4 py-4 px-6 text-left text-black font-bold uppercase hidden sm:table-cell">       
         {{ t('data.unchecked.header.item')}}
    </th>
    <th class="w-1/4 py-4 px-6 text-left text-black font-bold uppercase hidden sm:table-cell">
      {{ t('data.unchecked.header.quantity')}}
    </th>
    <th class="w-1/4 py-4 px-6 text-left text-black font-bold uppercase hidden sm:table-cell">
      {{ t('data.unchecked.header.actions')}}
    </th>
  </tr>
  <tr class="bg-white">
    <th colspan="2" class="w-1/2 py-4 px-6 text-left font-bold uppercase table-cell sm:hidden">
      <input
        type="text"
        class="border-black outline w-full"
        :placeholder="t('placeholder.item_name')"
        v-model="itemInsertedData.item_name"
        @keyup.enter="addItem()"
      />

      <input
        type="number"
        min="1"
        class="border-black outline w-16 mt-2"
        v-model="itemInsertedData.quantity"
        @keyup.enter="addItem()"
      />
    </th>
    <th class="w-1/2 py-4 px-6 text-left font-bold uppercase hidden sm:table-cell">
      <input
        type="text"
        class="border-black outline w-full"
        :placeholder="t('placeholder.item_name')"
        v-model="itemInsertedData.item_name"
        @keyup.enter="addItem()"
      />
    </th>
    <th class="w-1/2 py-4 px-6 text-left font-bold uppercase hidden sm:table-cell">
      <input
        type="number"
        min="1"
        class="border-black outline w-16 md:w-32"
        v-model="itemInsertedData.quantity"
        @keyup.enter="addItem()"
      />
    </th>
    <th class="w-1/2 py-4 px-6 text-left font-bold uppercase">
      <button
        type="button"
        class="cursor-pointer md:mr-3 lg:mr-6"
        :title="t('tooltip.add_item' )"
        @click="addItem()"
      >
        <PlusCircleIcon class="block size-6" aria-hidden="true" />
      </button>
      <button
        type="button"
        class="cursor-pointer"
        :title="t('tooltip.start_shopping' )"
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
import { useNotification } from "@kyvg/vue3-notification";

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
import {useI18n} from 'vue-i18n' 
const {t} = useI18n();

const { notify }  = useNotification()

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

  if (itemInsertedData.value.item_name.trim().length == 0) {
    notify({
      title: "Error",
      text: "Item name is empty",
      type: 'error',
    });
    return;
  }

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
        listStore.changeSelectedTab(ITEM_STATUS_IN_SHOPPING)
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
