import {defineStore} from "pinia";
import axiosClient from "../axios.js";

import {URL_GET_USER} from './../constants.js'

const useUserStore = defineStore('user', {
  state: () => ({
    user: null
  }),
  actions: {
    fetchUser() {
      return axiosClient.get(URL_GET_USER)
        .then(({data}) => {
          console.log(data)
          this.user = data
        })
    }
  }
})

export default useUserStore;