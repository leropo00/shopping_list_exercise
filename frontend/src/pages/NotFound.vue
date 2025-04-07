<template>
  <main class="grid min-h-full place-items-center bg-white px-6 py-24 sm:py-32 lg:px-8">
    <div class="text-center">
      <h1 class="mt-4 text-5xl font-semibold tracking-tight text-balance text-gray-900 sm:text-7xl">
        {{ t('not_found.title')}}
      </h1>
      <p class="mt-6 text-lg font-medium text-pretty text-gray-500 sm:text-xl/8">
        {{ t('not_found.message')}}
      </p>
      <div class="mt-10 flex items-center justify-center gap-x-6">
        <a
          href="/"
          class="rounded-md bg-indigo-600 px-3.5 py-2.5 text-sm font-semibold text-white color-white shadow-xs hover:bg-indigo-500 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600"
          >{{ t('not_found.back')}}</a
        >
      </div>
    </div>
  </main>
</template>

<script setup>
import {useI18n} from 'vue-i18n' 
const {t} = useI18n();
</script>

<style scoped></style>
