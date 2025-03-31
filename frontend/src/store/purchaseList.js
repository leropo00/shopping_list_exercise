import { defineStore } from 'pinia'
import axiosClient from '../axios.js'
import { URL_GET_PURCHASE_ITEMS } from '../constants.js'

const usePurchaseListStore = defineStore('purchaseList', {
  state: () => ({
    data: [],
  }),
  actions: {
    async fetchList() {
      return axiosClient.get(URL_GET_PURCHASE_ITEMS).then(({ data }) => {
        this.data = data
      })
    },
    clearList() {
      this.data = []
    },
    removeItem(itemId) {
      this.data = this.data.filter((item) => item.id !== itemId)
    },
  },
})

export default usePurchaseListStore
