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
const props = defineProps(['list'])
function formatItem(item) {
  if (item.quantity == 1) {
    return item.item_name
  }
  return `{item.quantity} x {item.item_name}`
}
</script>
