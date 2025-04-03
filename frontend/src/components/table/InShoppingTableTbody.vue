<template>
  <tbody class="bg-white">
    <tr v-for="item in itemsList" :key="item.id">
      <td class="py-4 px-6 border-b border-gray-200">
        {{ formatItem(item) }}
      </td>
      <td class="py-4 px-6 border-b border-gray-200">{{ formatQuantity(item) }}</td>
      <td v-if="userId == item.shopping_owner" class="py-4 px-6 border-b border-gray-200">
        <button
          type="button"
          class="cursor-pointer md:mr-3 lg:mr-6"
          title="Purcase Item"
          @click="updateCheckedQuantity(item.id, item.quantity)"
          v-if="item.checked_quantity == 0"
        >
          <DocumentCheckIcon class="block size-6" aria-hidden="true" />
        </button>
        <button
          type="button"
          class="cursor-pointer md:mr-3 lg:mr-6"
          title="Remove Purchased Item"
          v-if="item.quantity > 0"
          @click="startPartialCheckedQuantity(item)"
        >
          <ReceiptPercentIcon class="block size-6" aria-hidden="true" />
        </button>
        <button
          type="button"
          class="cursor-pointer"
          title="Remove Purchased Item"
          v-if="item.checked_quantity > 0"
          @click="updateCheckedQuantity(item.id, 0)"
        >
          <XCircleIcon class="block size-6" aria-hidden="true" />
        </button>
      </td>
    </tr>
  </tbody>
</template>

<script setup>
import { computed } from 'vue'
import usePurchaseListStore from '@/store/purchaseList'
import { XCircleIcon, DocumentCheckIcon, ReceiptPercentIcon } from '@heroicons/vue/24/solid'
import {
  ITEM_STATUS_IN_SHOPPING,
  URL_CHECK_QUANTITY_SHOPPING,
  HTTP_CODE_SUCCESS,
} from '@/constants.js'
import useUserStore from '@/store/user.js'
import axiosClient from '@/axios.js'

const listStore = usePurchaseListStore()
const itemsList = computed(() =>
  listStore.data.filter((item) => item.status == ITEM_STATUS_IN_SHOPPING),
)

const userStore = useUserStore()
const userId = computed(() => userStore.user.id)

function updateCheckedQuantity(itemId, checkedQuantity) {
  axiosClient
    .put(URL_CHECK_QUANTITY_SHOPPING + itemId, { checked_quantity: checkedQuantity })
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

function startPartialCheckedQuantity(item) {
  console.log(item)
}

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
