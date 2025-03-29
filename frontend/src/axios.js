import axios from "axios";
import router from "./router.js";
import {HTTP_CODE_UNAUTHORIZED} from './constants.js'

const axiosClient = axios.create({
  baseURL: import.meta.env.VITE_API_BASE_URL,
  withCredentials: true,
  withXSRFToken: true,
});


console.log('axios');
console.log(import.meta.env.VITE_API_BASE_URL);

axiosClient.interceptors.response.use((response) => {
    return response;
  }, error => {
    if (error.response && error.response.status === HTTP_CODE_UNAUTHORIZED) {
      router.push({name: 'Login'});
    } 
    throw error;
 });


export default axiosClient;