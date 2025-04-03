<template>
  <div class="shadow-lg rounded-lg overflow-hidden mx-4 md:mx-10">
    <table class="w-full table-fixed">
      <thead>
        <tr class="bg-gray-300 md:bg-white">
          <th colspan="3">
            <div
              class="flex flex-col md:flex-row justify-center items-center sm:gap-4 mx-auto max-w-7xl sm:px-4 sm:py-4 md:py-1 md:px-1"
            >
              <button
                type="button"
                class="cursor-pointer rounded md:rounded-t-full border-black md:outline p-2 basis-full grow"
                :class="{ 'bg-indigo-600 text-white': selectedType == ITEM_STATUS_UNCHECKED }"
                @click="changeItemStatus(ITEM_STATUS_UNCHECKED)"
              >
                Purchase List
              </button>
              <button
                type="button"
                class="cursor-pointer rounded md:rounded-t-full border-black md:outline p-2 basis-full grow"
                :class="{ 'bg-indigo-600 text-white': selectedType == ITEM_STATUS_IN_SHOPPING }"
                @click="changeItemStatus(ITEM_STATUS_IN_SHOPPING)"
                v-if="inShoppingCounts > 0"
              >
                In shopping
              </button>
              <button
                type="button"
                class="cursor-pointer rounded md:rounded-t-full border-black md:outline p-2 basis-full grow"
                @click="changeItemStatus(ITEM_STATUS_CHECKED)"
                :class="{ 'bg-indigo-600 text-white': selectedType == ITEM_STATUS_CHECKED }"
                v-if="checkedCount > 0"
              >
                History
              </button>
            </div>
          </th>
        </tr>
        <UncheckedTableHead v-if="selectedType == ITEM_STATUS_UNCHECKED" />
        <InShoppingTableHead v-if="selectedType == ITEM_STATUS_IN_SHOPPING" userId="user.id" />
        <CheckedTableHead v-else-if="selectedType == ITEM_STATUS_CHECKED" />
      </thead>
      <UncheckedTableTbody v-if="selectedType == ITEM_STATUS_UNCHECKED" />
      <InShoppingTableTbody v-if="selectedType == ITEM_STATUS_IN_SHOPPING" />
      <CheckedTableTbody v-else-if="selectedType == ITEM_STATUS_CHECKED" />
    </table>
  </div>
</template>

<script setup>
import { computed, ref, onMounted, onBeforeUnmount } from 'vue'
import ReconnectingEventSource from 'reconnecting-eventsource'
import useUserStore from '@/store/user.js'
import usePurchaseListStore from '@/store/purchaseList'
import {
  ITEM_STATUS_UNCHECKED,
  ITEM_STATUS_CHECKED,
  ITEM_STATUS_IN_SHOPPING,
} from '../constants.js'

import UncheckedTableHead from '@/components/table/UncheckedTableHead.vue'
import UncheckedTableTbody from '@/components/table/UncheckedTableTbody.vue'
import InShoppingTableHead from '@/components/table/InShoppingTableHead.vue'
import InShoppingTableTbody from '@/components/table/InShoppingTableTbody.vue'
import CheckedTableHead from '@/components/table/CheckedTableHead.vue'
import CheckedTableTbody from '@/components/table/CheckedTableTbody.vue'

const selectedType = ref(ITEM_STATUS_UNCHECKED)

const listStore = usePurchaseListStore()

const checkedCount = computed(
  () => listStore.data.filter((item) => item.status == ITEM_STATUS_CHECKED).length,
)
const inShoppingCounts = computed(
  () => listStore.data.filter((item) => item.status == ITEM_STATUS_IN_SHOPPING).length,
)

const userStore = useUserStore()
const user = computed(() => userStore.user)

const shoppingUserId = computed(() => {
  const data = listStore.data
    .filter((item) => item.status == ITEM_STATUS_IN_SHOPPING)
    .map((item) => item.shopping_owner)

  return data.length > 0 ? data[0] : null
})

let evtSource

onMounted(() => {
  console.log('mounted')
  evtSource = new ReconnectingEventSource(
    import.meta.env.VITE_API_BASE_URL + '/api/notifications',
    { withCredentials: true },
  )

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

  evtSource.onerror = async (e) => {
    console.log('error occured, refresh state, in case any events were lost')
    await listStore.fetchList()
  }
})
onBeforeUnmount(() => {
  try {
    evtSource.close()
  } catch (err) {
    console.log(err)
  }
})

function changeItemStatus(status) {
  selectedType.value = status
}
</script>
