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
                :class="{
                  'bg-indigo-600 text-white': selectedType == ITEM_STATUS_UNCHECKED,
                }"
                @click="changeItemStatus(ITEM_STATUS_UNCHECKED)"
              >
                {{ t('tabs.purchase_list' )}}                
              </button>
              <button
                type="button"
                class="cursor-pointer rounded md:rounded-t-full border-black md:outline p-2 basis-full grow"
                :class="{
                  'bg-indigo-600 text-white': selectedType == ITEM_STATUS_IN_SHOPPING,
                }"
                @click="changeItemStatus(ITEM_STATUS_IN_SHOPPING)"
                v-if="inShoppingCounts > 0"
              >
                {{ t('tabs.in_shopping' )}}
              </button>
              <button
                type="button"
                class="cursor-pointer rounded md:rounded-t-full border-black md:outline p-2 basis-full grow"
                @click="changeItemStatus(ITEM_STATUS_CHECKED)"
                :class="{
                  'bg-indigo-600 text-white': selectedType == ITEM_STATUS_CHECKED,
                }"
                v-if="checkedCount > 0"
              >
                {{ t('tabs.history' )}}
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
import { computed, onMounted, onBeforeUnmount } from 'vue'
import ReconnectingEventSource from 'reconnecting-eventsource'
import useUserStore from '@/store/user.js'
import usePurchaseListStore from '@/store/purchaseList'
import {useI18n} from 'vue-i18n' 
const {t} = useI18n();
import {
  ITEM_STATUS_UNCHECKED,
  ITEM_STATUS_CHECKED,
  ITEM_STATUS_IN_SHOPPING,
  EVENTS_TRIGGERING_REQUEST,
  PURCHASE_LIST_EVENT_DELETE,
  PURCHASE_LIST_EVENT_DELETE_ALL,
} from '../constants.js'

import UncheckedTableHead from '@/components/table/UncheckedTableHead.vue'
import UncheckedTableTbody from '@/components/table/UncheckedTableTbody.vue'
import InShoppingTableHead from '@/components/table/InShoppingTableHead.vue'
import InShoppingTableTbody from '@/components/table/InShoppingTableTbody.vue'
import CheckedTableHead from '@/components/table/CheckedTableHead.vue'
import CheckedTableTbody from '@/components/table/CheckedTableTbody.vue'

const listStore = usePurchaseListStore()
const selectedType = computed(() => listStore.selectedType)

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
      
      // remove events from current user as this were already taken into account in interface
      const otherUsersEvents = dataParsed.filter(item => item.user_id != user.id)

      // certain events require to reload the whole list
      // if at least one of those is present reload the whole list
      if (otherUsersEvents.filter(item => EVENTS_TRIGGERING_REQUEST.includes(item.event)).length > 0 ) {
        await listStore.fetchList()
        return
      }

      // certain events can be easily done in interface without reading the whole list data
      // other events could also be added here, if changed data(for example checked_quantity)
      // would be stored in database event in json field
      for (const element of otherUsersEvents) {
       if (element.event == PURCHASE_LIST_EVENT_DELETE_ALL) {
          listStore.clearList()
        } else if (element.event == PURCHASE_LIST_EVENT_DELETE) {
          listStore.removeItem(element.record_id)
        }
      }
    } catch (err) {
      console.log(err)
    }
  }

  evtSource.onerror = async (e) => {
    console.log('error occured, refresh state, in case any events were lost')
    console.log(e);
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
  listStore.changeSelectedTab(status)
}
</script>
