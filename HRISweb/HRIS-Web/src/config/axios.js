import axios from 'axios'

const api = axios.create({
  baseURL: import.meta.env.VITE_API_BASE_URL || "https://www.rheycrystalconstructionservices.com/snl-hr/backend/public/api",
  headers: {
    "Content-Type": "application/json",
  },
});
export default api