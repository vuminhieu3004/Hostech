import axios from "axios";

const token = localStorage.getItem("access_token");
const Api = axios.create({
  baseURL: "http://127.0.0.1:8000/api/",
  headers: {
    Authorization: `Bearer ${token}`,
    "Content-Type": "application/json",
  },
});

export default Api;
