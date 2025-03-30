<template>
  <main>
    <div class="mx-auto max-w-7xl px-4 py-6 sm:px-6 lg:px-8">
      <button
        type="submit"
        @click="downloadJsonData()"
        class="rounded-md bg-red-600 px-3 py-1 text-sm/6 font-semibold text-white shadow-sm hover:bg-red-700 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-red-700"
      >
        Prenesi podatke
      </button>
    </div>
  </main>
</template>

<script setup>
import axiosClient from '../axios.js'
import { HTTP_CODE_SUCCESS, URL_EXPORT_JSON } from '../constants.js'
import fileDownload from 'js-file-download'

function downloadJsonData() {
  axiosClient.get(URL_EXPORT_JSON, { responseType: 'blob' }).then((response) => {
    if (response.status === HTTP_CODE_SUCCESS) {
      fileDownload(response.data, 'output.json')
    }
  })
}
</script>

<style scoped></style>
