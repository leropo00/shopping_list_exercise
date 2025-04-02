<template>
  <tbody class="bg-white">
    <tr v-for="item in itemsList" :key="item.id">
      <td class="py-4 px-6 border-b border-gray-200">
        {{ formatItem(item) }}
      </td>
      <td class="py-4 px-6 border-b border-gray-200">{{ formatQuantity(item) }}</td>
      <td class="py-4 px-6 border-b border-gray-200">{{ shoppingUserId }}</td>
    </tr>
  </tbody>
</template>

<script setup>
import { computed } from 'vue'
import usePurchaseListStore from '@/store/purchaseList'
import { ITEM_STATUS_IN_SHOPPING } from '@/constants.js'

const listStore = usePurchaseListStore()
const itemsList = computed(() =>
  listStore.data.filter((item) => item.status == ITEM_STATUS_IN_SHOPPING),
)

const shoppingUserId = computed(() => {
  const data = listStore.data
    .filter((item) => item.status == ITEM_STATUS_IN_SHOPPING)
    .map((item) => item.shopping_owner)

  return data.length > 0 ? data[0] : null
})

function formatQuantity(item) {
  if (!item.checked_quantity) {
    return 'NO'
  }

  if (item.quantity == item.checked_quantity) {
    return 'YES'
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
