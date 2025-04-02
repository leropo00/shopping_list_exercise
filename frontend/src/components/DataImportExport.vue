<template>
  <main>
    <div
      class="flex flex-col md:flex-row justify-center items-center gap-2 mx-auto max-w-7xl px-4 py-6 sm:px-6 lg:px-8"
    >
      <FileUpload />

      <button
        type="submit"
        @click="downloadJsonData()"
        class="rounded-md mr-4 bg-red-600 px-3 py-1 text-sm/6 font-semibold text-white shadow-sm hover:bg-red-700 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-red-700"
      >
        Export data
      </button>

      <button
        type="submit"
        @click="deleteList()"
        class="rounded-md bg-red-600 px-3 py-1 text-sm/6 font-semibold text-white shadow-sm hover:bg-red-700 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-red-700"
      >
        Clear List
      </button>
    </div>
  </main>
</template>

<script setup>
import axiosClient from '@/axios.js'
import {
  HTTP_CODE_SUCCESS,
  HTTP_CODE_NO_CONTENT,
  URL_EXPORT_JSON,
  URL_DELETE_ALL_PURCHASE_ITEMS,
} from '../constants.js'
import fileDownload from 'js-file-download'
import usePurchaseListStore from '@/store/purchaseList'
import FileUpload from '../components/FileUpload.vue'
const listStore = usePurchaseListStore()

function downloadJsonData() {
  axiosClient.get(URL_EXPORT_JSON, { responseType: 'blob' }).then((response) => {
    if (response.status === HTTP_CODE_SUCCESS) {
      fileDownload(response.data, 'output.json')
    }
  })
}

function deleteList() {
  axiosClient.delete(URL_DELETE_ALL_PURCHASE_ITEMS).then((response) => {
    if (response.status === HTTP_CODE_NO_CONTENT) {
      listStore.clearList()
    }
  })
}
</script>

<style scoped></style>
