import { defineStore } from 'pinia'
import axiosClient from '../axios.js'
import { URL_GET_PURCHASE_ITEMS } from '../constants.js'
import { ITEM_STATUS_UNCHECKED } from '../constants.js'

const usePurchaseListStore = defineStore('purchaseList', {
  state: () => ({
    data: [],
    selectedType: ITEM_STATUS_UNCHECKED,
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
    changeSelectedTab(selectedType) {
      this.selectedType = selectedType
    },
  },
})

export default usePurchaseListStore
