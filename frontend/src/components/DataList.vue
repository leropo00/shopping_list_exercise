<template>
  <div class="shadow-lg rounded-lg overflow-hidden mx-4 md:mx-10">
    <table class="w-full table-fixed">
      <thead>
        <tr class="bg-gray-100">
          <th class="w-1/4 py-4 px-6 text-left text-gray-600 font-bold uppercase">Item</th>
          <th class="w-1/4 py-4 px-6 text-left text-gray-600 font-bold uppercase">Status</th>
          <th class="w-1/4 py-4 px-6 text-left text-gray-600 font-bold uppercase">Actions</th>
        </tr>
        <tr class="bg-white">
          <th class="w-1/2 py-4 px-6 text-left font-bold uppercase">
            <input
              type="text"
              class="border-black outline"
              id="new_item_name"
              v-model="itemInsertedData.item_name"
            />
          </th>
          <th class="w-1/2 py-4 px-6 text-left font-bold uppercase">
            <input
              type="number"
              min="1"
              class="border-black outline w-24"
              id="new_item_quantity"
              v-model="itemInsertedData.quantity"
            />
          </th>
          <th class="w-1/2 py-4 px-6 text-left font-bold uppercase">
            <button type="button" class="border-black outline" @click="addItem()">Add item</button>
          </th>
        </tr>
      </thead>
      <tbody class="bg-white">
        <tr v-for="item in list" :key="item.id">
          <template v-if="item.id == itemUpdatedData.item_id">
            <td class="py-4 px-6 border-b border-gray-200">
              <input
                type="text"
                class="border-black outline"
                id="updated_item_name"
                v-model="itemUpdatedData.item_name"
              />
            </td>
            <td class="py-4 px-6 border-b border-gray-200">
              <input
                type="number"
                min="1"
                class="border-black outline w-24"
                id="updated_item_quantity"
                v-model="itemUpdatedData.quantity"
              />
            </td>
            <td class="py-4 px-6 border-b border-gray-200">
              <button
                type="button"
                class="border-black outline mr-4"
                @click="updateItemData(item.id)"
              >
                Update Item
              </button>
              <button type="button" class="border-black outline" @click="cancelUpdate()">
                Cancel update
              </button>
            </td>
          </template>

          <template v-else>
            <td class="py-4 px-6 border-b border-gray-200">
              {{ formatItem(item) }}
            </td>
            <td class="py-4 px-6 border-b border-gray-200">{{ item.status }}</td>
            <td class="py-4 px-6 border-b border-gray-200">
              <button
                type="button"
                class="border-black outline mr-4"
                @click="prepareItemForEdit(item)"
              >
                Edit item
              </button>
              <button type="button" class="border-black outline" @click="deleteItem(item.id)">
                Delete item
              </button>
            </td>
          </template>
        </tr>
      </tbody>
    </table>
  </div>
</template>

<script setup>
import { onMounted, computed, ref } from 'vue'
import useUserStore from '@/store/user.js'
import usePurchaseListStore from '@/store/purchaseList'
import axiosClient from '../axios.js'
import {
  URL_CREATE_PURCHASE_ITEM,
  URL_UPDATE_PURCHASE_ITEM,
  URL_DELETE_PURCHASE_ITEM,
  HTTP_CODE_SUCCESS,
  HTTP_CODE_CREATED,
  HTTP_CODE_NO_CONTENT,
} from '../constants.js'

const props = defineProps(['list'])

const listStore = usePurchaseListStore()
const userStore = useUserStore()
const user = computed(() => userStore.user)

const itemInsertedData = ref({
  item_name: '',
  quantity: 1,
})
const itemUpdatedData = ref({
  item_id: null,
  item_name: '',
  quantity: 1,
})

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

function addItem() {
  axiosClient
    .post(URL_CREATE_PURCHASE_ITEM, itemInsertedData.value)
    .then(async (response) => {
      if (response.status === HTTP_CODE_CREATED) {
        itemInsertedData.value.item_name = ''
        itemInsertedData.value.quantity = 1
        await listStore.fetchList()
      }
    })
    .catch((error) => {
      console.log(error)
      // errors.value = error.response.data.errors
    })
}

function prepareItemForEdit(item) {
  itemUpdatedData.value.item_id = item.id
  itemUpdatedData.value.item_name = item.item_name
  itemUpdatedData.value.quantity = item.quantity
}

function updateItemData() {
  axiosClient
    .put(URL_UPDATE_PURCHASE_ITEM + itemUpdatedData.value.item_id, itemUpdatedData.value)
    .then(async (response) => {
      if (response.status === HTTP_CODE_SUCCESS) {
        cancelUpdate()
        await listStore.fetchList()
      }
    })
    .catch((error) => {
      console.log(error)
      // errors.value = error.response.data.errors
    })

  // itemUpdatedData.value
}

function cancelUpdate() {
  itemUpdatedData.value.item_id = null
  itemUpdatedData.value.item_name = ''
  itemUpdatedData.value.quantity = 1
}

function deleteItem(itemId) {
  axiosClient.delete(URL_DELETE_PURCHASE_ITEM + itemId).then((response) => {
    if (response.status === HTTP_CODE_NO_CONTENT) {
      listStore.removeItem(itemId)
    }
  })
}

function formatItem(item) {
  if (item.quantity == 1) {
    return item.item_name
  }
  return `${item.quantity} x ${item.item_name}`
}
</script>
