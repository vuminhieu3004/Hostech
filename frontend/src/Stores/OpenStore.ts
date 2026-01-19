import { create } from "zustand";
import type { IOpenTypeStore } from "../Types/StoreType";

export const useOpenStore = create<IOpenTypeStore>((set) => ({
  open: false,
  eyePassword: false,
  setOpen: (open) => set({ open }),
  setEyePassword: (eyePassword) => set({ eyePassword }),
}));
