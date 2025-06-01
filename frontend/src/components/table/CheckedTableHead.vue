<template>
  <tr class="bg-gray-300">
    <th colspan="3" class="w-2/3 py-4 px-6 text-center text-black font-bold uppercase table-cell sm:hidden">
      {{ t('data.checked.header.item_mobile')}}
    </th>
    <th colspan="2" class="w-2/3 py-4 px-6 text-left text-black font-bold uppercase hidden sm:table-cell">
      {{ t('data.checked.header.item')}}
    </th>
    <th class="w-1/3 py-4 px-6 text-left text-black font-bold uppercase hidden sm:table-cell">       
      {{ t('data.checked.header.date')}}
    </th>
  </tr>
</template>

<script setup>
import {useI18n} from 'vue-i18n' 
const {t} = useI18n();
</script>

<style scoped></style>
