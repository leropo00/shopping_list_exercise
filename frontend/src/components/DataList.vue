<template>
  <div class="shadow-lg rounded-lg overflow-hidden mx-4 md:mx-10">
    <table class="w-full table-fixed">
      <thead>
        <tr class="bg-gray-100">
          <th class="w-1/4 py-4 px-6 text-left text-gray-600 font-bold uppercase">Item</th>
          <th class="w-1/4 py-4 px-6 text-left text-gray-600 font-bold uppercase">Status</th>
          <th class="w-1/4 py-4 px-6 text-left text-gray-600 font-bold uppercase">Actions</th>
        </tr>
      </thead>
      <tbody class="bg-white">
        <tr v-for="item in list" :key="item.id">
          <td class="py-4 px-6 border-b border-gray-200">
            {{ item.quantity }} X {{ item.item_name }}
          </td>
          <td class="py-4 px-6 border-b border-gray-200">{{ item.status }}</td>
          <td class="py-4 px-6 border-b border-gray-200"></td>
        </tr>
      </tbody>
    </table>
  </div>
</template>

<script setup>
import { onMounted } from 'vue'
const props = defineProps(['list'])
import usePurchaseListStore from '@/store/purchaseList'

const listStore = usePurchaseListStore()

function formatItem(item) {
  if (item.quantity == 1) {
    return item.item_name
  }
  return `{item.quantity} x {item.item_name}`
}

onMounted(() => {
  const evtSource = new EventSource(import.meta.env.VITE_API_BASE_URL + '/api/notifications')

  evtSource.onmessage = (e) => {
    console.log('onmessage')

    if (e.data == null || e.data.length == 0) {
      return
    }

    try {
      const dataParsed = JSON.parse(e.data)
      console.log(dataParsed)
      for (const element of dataParsed) {
        if (element.event == 'delete_all') {
          listStore.clearList()
        }
      }
    } catch (err) {
      console.log(err)
    }
  }
})
</script>
