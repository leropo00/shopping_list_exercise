<template>
  <tbody class="bg-white">
    <tr v-for="item in itemsList" :key="item.id">
      <td colspan="2" class="py-4 px-6 border-b border-gray-200">
        {{ formatItem(item) }}
      </td>
      <td class="py-4 px-6 border-b border-gray-200">{{ item.checked_date }}</td>
    </tr>
  </tbody>
</template>

<script setup>
import { computed } from 'vue'
import { useDateFormat, useNow } from '@vueuse/core'
import usePurchaseListStore from '@/store/purchaseList'
import { ITEM_STATUS_CHECKED } from '@/constants.js'

const formatted = useDateFormat(useNow(), 'YYYY-MM-DD HH:mm:ss')

const listStore = usePurchaseListStore()
const itemsList = computed(() => {
  let list = listStore.data.filter((item) => item.status == ITEM_STATUS_CHECKED)

  list.sort((a, b) => b.checked_date.localeCompare(a.checked_date))
  return list
})

function formatQuantity(item) {
  if (item.quantity == item.checked_quantity) {
    return item.quantity
  }
  return `${item.checked_quantity} / ${item.quantity}`
}

function formatItem(item) {
  if (item.quantity == 1) {
    return item.item_name
  }
  return `${item.quantity} x ${item.item_name}`
}
</script>

<style scoped></style>
