<template>
  <tr class="bg-gray-300">
    <th colspan="3" class="w-2/3 py-4 px-6 text-center text-black font-bold uppercase table-cell sm:hidden">
      {{ t('data.checked.header.item_mobile')}}
    </th>
    <th colspan="2" class="w-2/3 py-4 px-6 text-left text-black font-bold uppercase hidden sm:table-cell">
      {{ t('data.checked.header.item')}}
    </th>
    <th class="w-1/3 py-4 px-6 text-left text-black font-bold uppercase hidden sm:table-cell">       
      {{ t('data.checked.header.date')}}
    </th>
  </tr>
</template>

<script setup>
import { ref } from 'vue'
import usePurchaseListStore from '@/store/purchaseList'
import axiosClient from '@/axios.js'
import { URL_CREATE_PURCHASE_ITEM, HTTP_CODE_CREATED } from '@/constants.js'
import { useNotification } from "@kyvg/vue3-notification";
import { formatErrorResponse } from '@/helpers.js'
import {useI18n} from 'vue-i18n' 
const {t} = useI18n();
const { notify }  = useNotification()

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
      notify({
        title: t("errors.title"),
        text: t(formatErrorResponse(error)),
        type: 'error',
      });
    })
}
</script>

<style scoped></style>
