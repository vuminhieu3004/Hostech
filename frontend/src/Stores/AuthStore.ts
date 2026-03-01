import { create } from "zustand";
import type { ITokenStore } from "../Types/StoreType";
import type { IDecodeJWT } from "../Types/Auth.Type";
import { jwtDecode } from "jwt-decode";

export const useTokenStore = create<ITokenStore>((set, get) => ({
  token: "",
  role: null,
  isLoading: true,

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

  restoreToken: () => {
    const token = localStorage.getItem("token");

    if (token) {
      try {
        const decoded = jwtDecode<IDecodeJWT>(token);
        // Kiểm tra token chưa hết hạn
        if (decoded.exp && decoded.exp * 1000 > Date.now()) {
          set({
            token,
            role: decoded.user.role ?? null,
            isLoading: false,
          });
          return;
        }
      } catch (error) {
        // Token không valid
      }
    }

    // Token không có hoặc hết hạn
    set({ token: "", role: null, isLoading: false });
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
