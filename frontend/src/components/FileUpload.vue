<template>
  <div class="basis-full grow md:basis-1/4 w-full py-1">
    <form @submit.prevent="submit">
      <div>
        <label for="file-upload">
          <div
            class="cursor-pointer rounded-md bg-indigo-600 hover:bg-indigo-500 px-3 py-1.5 w-full text-sm font-semibold text-white text-center shadow-sm hover:bg-indigo-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600"
          >
            <input
              id="file-upload"
              name="file-upload"
              type="file"
              @input="submit($event.target.files[0])"
              class="sr-only"
            />
            {{t('header.import')}}
          </div>
        </label>
      </div>
    </form>
  </div>
</template>

<script setup>
import axiosClient from '@/axios.js'
import { URL_IMPORT_JSON, HTTP_CODE_CREATED } from '../constants.js'
import usePurchaseListStore from '@/store/purchaseList'
import { useNotification } from "@kyvg/vue3-notification";
import { formatErrorResponse } from '@/helpers.js'
const { notify }  = useNotification()
import {useI18n} from 'vue-i18n' 
const {t} = useI18n();

const listStore = usePurchaseListStore()

function submit(file) {
  // workaround so that the same file can be repeatedly
  document.getElementById('file-upload').value = null
  const formData = new FormData()
  formData.append('file', file)
  axiosClient.post(URL_IMPORT_JSON, formData).then(async (res) => {
    if (res.status === HTTP_CODE_CREATED) {
      await listStore.fetchList()
    }
  }).catch((error) => {
    notify({
        title: t("errors.title"),
        text: t(formatErrorResponse(error)),
        type: 'error',
      });
  })
}
</script>

<style scoped></style>
