<script setup>
import SvgImage from './../assets/cart-shopping-list.svg?component'

</script>

<template>
  <div class="flex min-h-full flex-1 flex-col justify-center px-6 py-12 lg:px-8">
    <div class="sm:mx-auto sm:w-full sm:max-w-sm">
      <SvgImage  width="40" height="40"  />
    </div>
    <slot></slot>
  </div>
</template>

<style scoped>

</style> 