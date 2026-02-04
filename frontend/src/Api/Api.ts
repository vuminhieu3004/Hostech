import axios from "axios";
import { useTokenStore } from "../Stores/AuthStore";

const Api = axios.create({
  baseURL: "http://127.0.0.1:8000/api/",
  headers: {
    "Content-Type": "application/json",
  },
});

Api.interceptors.request.use((config) => {
  const token = useTokenStore.getState().getToken();
  if (token) {
    config.headers.Authorization = `Bearer ${token}`;
  }
  return config;
});

export default Api;
