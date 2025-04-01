<template>
  <tbody class="bg-white">
    <tr v-for="item in itemsList" :key="item.id">
        <td class="py-4 px-6 border-b border-gray-200">
          {{ item.item_name }}
        </td>
        <td class="py-4 px-6 border-b border-gray-200">{{ formatQuantity(item) }}</td>
        <td class="py-4 px-6 border-b border-gray-200">
          <td class="py-4 px-6 border-b border-gray-200">{{ item.checked_date }}</td>
        </td>
    </tr>
  </tbody>
</template>

<script setup>
import { computed } from 'vue'
import usePurchaseListStore from '@/store/purchaseList'
import {
  ITEM_STATUS_CHECKED,
} from '@/constants.js'

const listStore = usePurchaseListStore()
const itemsList = computed(() =>
  listStore.data.filter((item) => item.status == ITEM_STATUS_CHECKED),
)

function formatQuantity(item) {
  if (item.quantity == item.checked_quantity) {
    return item.quantity;
  }
  return `${item.checked_quantity} out of ${item.quantity}`
}


function formatItem(item) {
  if (item.quantity == 1) {
    return item.item_name
  }
  return `${item.quantity} x ${item.item_name}`
}

</script>

<style scoped></style>
