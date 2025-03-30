<template>
  Messages sent
  <div>{{ message }}</div>
  <div>{{ status }}</div>
  Messages ending
</template>

<script>
import { ref } from 'vue'
import { useEventSource } from '@vueuse/core'

export default {
  setup() {
    // Define a reactive variable to store the received message
    const message = ref('')

    // Use the useEventSource hook to listen to events from the server
    const { data, error, status } = useEventSource(
      import.meta.env.VITE_API_BASE_URL + '/api/notifications',
    )

    console.log('useEventSource')
    console.log('data value')
    console.log(data?.value)

    // Watch for changes in the 'data' from the EventSource
    data?.value && (message.value = data.value)

    // Optionally, handle any errors or statuses
    if (error?.value) {
      console.error('Error in EventSource:', error.value)
    }

    // Return reactive properties to the template
    return {
      message,
      status,
    }
  },
}
</script>

<style scoped></style>
