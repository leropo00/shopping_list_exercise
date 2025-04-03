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
          <XCircleIcon class="block size-6" aria-hidden="true" />
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
import { XCircleIcon, DocumentCheckIcon } from '@heroicons/vue/24/solid'
import { ITEM_STATUS_IN_SHOPPING } from '@/constants.js'
import useUserStore from '@/store/user.js'

const listStore = usePurchaseListStore()
const itemsList = computed(() =>
  listStore.data.filter((item) => item.status == ITEM_STATUS_IN_SHOPPING),
)

const userStore = useUserStore()
const userId = computed(() => userStore.user.id)

function updateCheckedQuantity(itemId, checkedQuantity) {}

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
