<template>
  <div class="shadow-lg rounded-lg overflow-hidden mx-4 md:mx-10">
    <table class="w-full table-fixed">
      <thead>
        <tr class="bg-gray-100">
          <th class="w-1/4 py-4 px-6 text-left text-gray-600 font-bold uppercase">
            <button
              type="button"
              class="border-black outline p-2"
              :class="{ 'bg-sky-300': selectedType == ITEM_STATUS_UNCHECKED }"
              @click="showUnchecked()"
            >
              Purchase List
            </button>
          </th>
          <th class="w-1/4 py-4 px-6 text-left text-gray-600 font-bold uppercase">
            <button
              type="button"
              class="border-black outline p-2"
              :class="{ 'bg-sky-300': selectedType == ITEM_STATUS_IN_SHOPPING }"
              @click="showInShopping()"
            >
              In shopping
            </button>
          </th>
          <th class="w-1/4 py-4 px-6 text-left text-gray-600 font-bold uppercase">
            <button
              type="button"
              class="border-black outline p-2"
              @click="showChecked()"
              :class="{ 'bg-sky-300': selectedType == ITEM_STATUS_CHECKED }"
            >
              History
            </button>
          </th>
        </tr>
        <UncheckedTableHead v-if="selectedType == ITEM_STATUS_UNCHECKED" />
        <CheckedTableHead v-else-if="selectedType == ITEM_STATUS_CHECKED" />
      </thead>
      <UncheckedTableTbody v-if="selectedType == ITEM_STATUS_UNCHECKED" />
      <CheckedTableTbody v-else-if="selectedType == ITEM_STATUS_CHECKED" />
    </table>
  </div>
</template>

<script setup>
import { onMounted, computed, ref } from 'vue'
import useUserStore from '@/store/user.js'
import usePurchaseListStore from '@/store/purchaseList'
import axiosClient from '@/axios.js'
import {
  URL_CREATE_PURCHASE_ITEM,
  URL_UPDATE_PURCHASE_ITEM,
  URL_DELETE_PURCHASE_ITEM,
  HTTP_CODE_SUCCESS,
  HTTP_CODE_CREATED,
  HTTP_CODE_NO_CONTENT,
  ITEM_STATUS_UNCHECKED,
  ITEM_STATUS_CHECKED,
  ITEM_STATUS_IN_SHOPPING,
} from '../constants.js'

import UncheckedTableHead from '@/components/table/UncheckedTableHead.vue'
import UncheckedTableTbody from '@/components/table/UncheckedTableTbody.vue'

import CheckedTableHead from '@/components/table/CheckedTableHead.vue'
import CheckedTableTbody from '@/components/table/CheckedTableTbody.vue'

const selectedType = ref(ITEM_STATUS_UNCHECKED)

const listStore = usePurchaseListStore()

const userStore = useUserStore()
const user = computed(() => userStore.user)

onMounted(() => {
  const evtSource = new EventSource(import.meta.env.VITE_API_BASE_URL + '/api/notifications')

  evtSource.onmessage = async (e) => {
    console.log('onmessage')

    if (e.data == null || e.data.length == 0) {
      return
    }

    try {
      const dataParsed = JSON.parse(e.data)
      console.log(dataParsed)
      for (const element of dataParsed) {
        // actions before, add maybe
        if (element.user_id == user.id) {
          continue
        }
        if (element.event == 'add' || element.event == 'edit') {
          await listStore.fetchList()
          return
        } else if (element.event == 'delete_all') {
          listStore.clearList()
        } else if (element.event == 'delete' && element.record_id) {
          listStore.removeItem(element.record_id)
        }
      }
    } catch (err) {
      console.log(err)
    }
  }
})

function showUnchecked() {
  selectedType.value = ITEM_STATUS_UNCHECKED
}

function showInShopping() {
  selectedType.value = ITEM_STATUS_IN_SHOPPING
}

function showChecked() {
  selectedType.value = ITEM_STATUS_CHECKED
}
</script>
