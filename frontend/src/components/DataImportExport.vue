<template>
  <main>
    <div
      class="flex flex-col md:flex-row justify-center items-center gap-4 md:gap-2 mx-auto max-w-7xl px-4 py-6 sm:px-6 lg:px-8"
    >
      <button
        type="submit"
        @click="refreshList()"
        class="cursor-pointer rounded-md bg-indigo-600 hover:bg-indigo-500 px-3 py-1 basis-full grow md:basis-1/4 w-full text-sm/6 font-semibold text-white shadow-sm focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2"
      >
         {{t('header.refresh')}}
      </button>
      <FileUpload />
      <button
        type="submit"
        @click="downloadJsonData()"
        class="cursor-pointer rounded-md bg-indigo-600 hover:bg-indigo-500 px-3 py-1 basis-full grow md:basis-1/4 w-full text-sm/6 font-semibold text-white shadow-sm focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2"
      >
         {{t('header.export')}}
      </button>

      <button
        type="submit"
        @click="deleteList()"
        class="cursor-pointer rounded-md bg-indigo-600 hover:bg-indigo-500 px-3 py-1 basis-full grow md:basis-1/4 w-full text-sm/6 font-semibold text-white shadow-sm focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2"
      >
        {{t('header.clear')}}
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
import { useNotification } from "@kyvg/vue3-notification";
import { formatErrorResponse } from '@/helpers.js'
import fileDownload from 'js-file-download'
import usePurchaseListStore from '@/store/purchaseList'
import FileUpload from '../components/FileUpload.vue'
import {useI18n} from 'vue-i18n' 
const {t} = useI18n();

const { notify }  = useNotification()
const listStore = usePurchaseListStore()

function downloadJsonData() {
  axiosClient.get(URL_EXPORT_JSON, { responseType: 'blob' }).then((response) => {
    if (response.status === HTTP_CODE_SUCCESS) {
      fileDownload(response.data, 'output.json')
    }
  }).catch((error) => {
    notify({
        title: t("errors.title"),
        text: t(formatErrorResponse(error)),
        type: 'error',
      });
  })
}

function deleteList() {
  axiosClient.delete(URL_DELETE_ALL_PURCHASE_ITEMS).then((response) => {
    if (response.status === HTTP_CODE_NO_CONTENT) {
      listStore.clearList()
    }
  }).catch((error) => {
    notify({
        title: t("errors.title"),
        text: t(formatErrorResponse(error)),
        type: 'error',
      });
  })
}

async function refreshList() {
  await listStore.fetchList()
}
</script>

<style scoped></style>
