<script setup>
import axiosClient from '@/axios.js'
import { URL_IMPORT_JSON, HTTP_CODE_CREATED } from '../constants.js'
import usePurchaseListStore from '@/store/purchaseList'

const listStore = usePurchaseListStore()

function submit(file) {
  // workaround so that the same file can be repeatedly
  document.getElementById('file-upload').value = null
  const formData = new FormData()
  formData.append('file', file)
  axiosClient.post(URL_IMPORT_JSON, formData).then(async (res) => {
    console.log(res)
    if (res.status === HTTP_CODE_CREATED) {
      await listStore.fetchList()
    }
  })
}
</script>

<template>
  <div class="basis-full grow md:basis-1/4 w-full py-1">
    <form @submit.prevent="submit">
      <div>
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
          Import data file
        </div>
      </div>
    </form>
  </div>
</template>

<style scoped></style>
