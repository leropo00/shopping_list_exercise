<template>
  <tr class="bg-gray-300">
    <th class="w-1/2 py-4 px-6 text-left text-black font-bold uppercase">Item for purchase</th>
    <th colspan="2" class="w-1/2 py-4 px-6 text-left text-black font-bold uppercase">Purchased</th>
  </tr>
</template>

<script setup>
import { ref } from 'vue'
import usePurchaseListStore from '@/store/purchaseList'
import axiosClient from '@/axios.js'
import { URL_CREATE_PURCHASE_ITEM, HTTP_CODE_CREATED } from '@/constants.js'
</script>

<style scoped></style>
