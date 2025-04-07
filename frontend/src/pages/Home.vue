<template>
  <header class="bg-white shadow">
    <div class="mx-auto max-w-7xl px-4 py-6 sm:px-6 lg:px-8">
      <h1 class="text-3xl font-bold tracking-tight text-black text-center">{{ t('app_name')}}</h1>
    </div>
  </header>
  <main>
    <DataImportExport />
    <div class="mx-auto max-w-7xl px-4 py-6 sm:px-6 lg:px-8">
      <DataList />
    </div>
  </main>
</template>

<script setup>
import { onMounted } from 'vue'
import DataImportExport from '@/components/DataImportExport.vue'
import DataList from '@/components/DataList.vue'
import usePurchaseListStore from '@/store/purchaseList'
import {useI18n} from 'vue-i18n' 
const {t} = useI18n();

const listStore = usePurchaseListStore()

onMounted(async () => {
  await listStore.fetchList()
})
</script>

<style scoped></style>
