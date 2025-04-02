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
  <div class="px-3 py-1">
    <form @submit.prevent="submit">
      <div>
        <label
          for="file-upload"
          class="relative cursor-pointer rounded-md bg-white font-semibold text-indigo-600 focus-within:outline-none focus-within:ring-2 focus-within:ring-indigo-600 focus-within:ring-offset-2 hover:text-indigo-500"
        >
          <span
            class="rounded-md bg-indigo-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600"
          >
            <input
              id="file-upload"
              name="file-upload"
              type="file"
              @input="submit($event.target.files[0])"
              class="sr-only"
            />
            Import data file
          </span>
        </label>
      </div>
    </form>
  </div>
</template>

<style scoped></style>
