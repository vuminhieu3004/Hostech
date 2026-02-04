import { create } from "zustand";
import type { ITokenStore } from "../Types/StoreType";
import type { IDecodeJWT } from "../Types/Auth.Type";
import { jwtDecode } from "jwt-decode";

export const useTokenStore = create<ITokenStore>((set, get) => ({
  token: localStorage.getItem("token") || "",
  role: null,

  setToken: (token: string) => {
    localStorage.setItem("token", token);

    try {
      const decoded = jwtDecode<IDecodeJWT>(token);

      set({
        token,
        role: decoded.user.role ?? null,
      });
    } catch (error) {
      set({ token: "", role: null });
    }
  },

  getRole: () => {
    return get().role;
  },

  getToken: () => {
    return get().token;
  },

  clearToken: () => {
    localStorage.removeItem("token");
    set({ token: "", role: null });
  },
}));
