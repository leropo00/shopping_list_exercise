<template>
  <tbody class="bg-white">
    <tr v-for="item in itemsList" :key="item.id">
      <td class="py-4 px-6 border-b border-gray-200">
        {{ formatItem(item) }}
      </td>
      <td class="py-4 px-6 border-b border-gray-200" v-if="item.id == itemPartialyCheckedData.item_id">
        <input
            type="number"
            min="0"
            :max="itemPartialyCheckedData.max_checked_quantity"
            class="border-black outline w-12 md:w-32"
            id="updated_item_checked_quantity"
            v-model="itemPartialyCheckedData.checked_quantity"
            @keyup.enter="updatePartialCheckedQuantity()"
          />
      </td>
      <td v-else class="py-4 px-6 border-b border-gray-200">{{ formatQuantity(item) }}</td>
      <td v-if="userId == item.shopping_owner" class="py-4 px-6 border-b border-gray-200">
        <template v-if="item.id == itemPartialyCheckedData.item_id">
          <button
            type="button"
            class="cursor-pointer md:mr-3 lg:mr-6"
            title="Update Checked Quantity"
            @click="updatePartialCheckedQuantity()"
          >
            <CheckCircleIcon class="block size-6" aria-hidden="true" />
          </button>
          <button
            type="button"
            class="cursor-pointer md:mr-3 lg:mr-6"
            @click="cancelPartialUpdate()"
            title="Cancel update"
          >
            <XCircleIcon class="block size-6" aria-hidden="true" />
          </button>
        </template>
        <template v-else>
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
              title="Select Purchased Quantity"
              v-if="item.quantity > 1"
              @click="startPartialCheckedQuantity(item)"
            >
              <PencilSquareIcon class="block size-6" aria-hidden="true" />
            </button>
            <button
              type="button"
              class="cursor-pointer"
              title="Remove Purchased Item"
              v-if="item.checked_quantity > 0"
              @click="updateCheckedQuantity(item.id, 0)"
            >
              <MinusCircleIcon class="block size-6" aria-hidden="true" />
            </button>
          </template>
      </td>
    </tr>
  </tbody>
</template>

<script setup>
import { computed, ref } from 'vue'
import usePurchaseListStore from '@/store/purchaseList'
import { MinusCircleIcon, CheckCircleIcon, XCircleIcon, DocumentCheckIcon, PencilSquareIcon } from '@heroicons/vue/24/solid'
import {
  ITEM_STATUS_IN_SHOPPING,
  URL_CHECK_QUANTITY_SHOPPING,
  HTTP_CODE_SUCCESS,
} from '@/constants.js'
import useUserStore from '@/store/user.js'
import axiosClient from '@/axios.js'

const userStore = useUserStore()
const userId = computed(() => userStore.user.id)

const listStore = usePurchaseListStore()
const itemsList = computed(() =>
  listStore.data.filter((item) => item.status == ITEM_STATUS_IN_SHOPPING),
)
const itemPartialyCheckedData = ref({
  item_id: null,
  checked_quantity: 1,
  max_checked_quantity: 1,
})

function updatePartialCheckedQuantity() {
  updateCheckedQuantity(itemPartialyCheckedData.value.item_id, itemPartialyCheckedData.value.checked_quantity, true);
}

function cancelPartialUpdate() {
  itemPartialyCheckedData.value.item_id = null
  itemPartialyCheckedData.value.item_name = ''
  itemPartialyCheckedData.value.quantity = 1
}

function updateCheckedQuantity(itemId, checkedQuantity, isPartial = false) {
  axiosClient
    .put(URL_CHECK_QUANTITY_SHOPPING + itemId, { checked_quantity: checkedQuantity })
    .then(async (response) => {
      if (response.status === HTTP_CODE_SUCCESS) {
        cancelPartialUpdate()
        await listStore.fetchList()
      }
    })
    .catch((error) => {
      console.log(error)
      // errors.value = error.response.data.errors
    })
}

function startPartialCheckedQuantity(item) {
  itemPartialyCheckedData.value.item_id = item.id
  itemPartialyCheckedData.value.checked_quantity = item.checked_quantity > 0 ? item.checked_quantity: 1;
  itemPartialyCheckedData.value.max_checked_quantity = item.quantity
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
