<template>
  <tbody class="bg-white">
    <tr v-for="item in itemsList" :key="item.id">
      <td colspan="2" class="py-4 px-6 border-b border-gray-200">
        {{ formatItem(item) }}
      </td>
      <td class="py-4 px-6 border-b border-gray-200">{{ formatDate(new Date(item.checked_date), 'DD.MM.YYYY HH:mm:ss') }}</td>
    </tr>
  </tbody>
</template>

<script setup>
import { computed } from 'vue'
import { useDateFormat, useNow, formatDate } from '@vueuse/core'
import usePurchaseListStore from '@/store/purchaseList'
import { ITEM_STATUS_CHECKED } from '@/constants.js'

const listStore = usePurchaseListStore()
const itemsList = computed(() => {
  let list = listStore.data.filter((item) => item.status == ITEM_STATUS_CHECKED)
  list.sort((a, b) => b.checked_date.localeCompare(a.checked_date))
  return list
})

function formatItem(item) {
  if (item.checked_quantity == 1) {
    return item.item_name
  }
  return `${item.checked_quantity} x ${item.item_name}`
}
</script>

<style scoped></style>
