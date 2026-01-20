import { create } from "zustand";
import type { IOpenMenu, IOpenTypeStore } from "../Types/StoreType";

export const useOpenStore = create<IOpenTypeStore>((set) => ({
  open: false,
  eyePassword: false,
  setOpen: (open) => set({ open }),
  setEyePassword: (eyePassword) => set({ eyePassword }),
}));

export const useOpenMenu = create<IOpenMenu>((set) => ({
  openMenu1: false,
  openMenu2: false,
  openMenu3: false,
  setOpenMenu1: (openMenu1) => set({ openMenu1 }),
  setOpenMenu2: (openMenu2) => set({ openMenu2 }),
  setOpenMenu3: (openMenu3) => set({ openMenu3 }),
}));
