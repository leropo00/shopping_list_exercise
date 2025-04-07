<script setup>
import SvgImage from '@/assets/cart-shopping-list.svg?component'
import {useI18n} from 'vue-i18n' 
const {t} = useI18n();
</script>

<template>
  <div class="flex min-h-full flex-1 flex-col justify-center px-6 py-12 lg:px-8">
    <div class="sm:mx-auto sm:w-full sm:max-w-sm flex">
      <div class="grow">    
          <SvgImage width="40" height="40" />
      </div>
      <div class="grow text-2xl font-bold">{{ t('app_name') }}</div>
    </div>
    <slot></slot>
  </div>
</template>

<style scoped></style>
