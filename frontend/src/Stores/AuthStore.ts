import { create } from "zustand";
import type { IMeStore } from "../Types/StoreType";
import { GetMe } from "../Services/Auth.service";
import type { IMe } from "../Types/Auth.Type";

export const useMeStore = create<IMeStore>((set) => ({
  me: "",

  getMe: async () => {
    const { data } = await GetMe();
    console.log(data.role);
    set({ me: data.role });
  },
}));
