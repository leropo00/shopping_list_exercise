<template>
  <select @change="handleChange" v-model="selectedOption">
    <option v-for="option in options" :value="option.value">
      {{ option.label }}
    </option>
  </select>
</template>

<script setup>
import {useI18n} from 'vue-i18n'
import {ref} from "vue";

const {locale} = useI18n();

const options = [
 {
   label: 'English',
   value: 'en'
 },
 {
   label: 'Slovensko',
   value: 'sl'
 }
];

const currentLanguage = options.find((x) => locale.value.startsWith(x.value)).value
const selectedOption = ref(currentLanguage);

function handleChange() {
  locale.value = selectedOption.value
}

</script>

<style scoped></style>
