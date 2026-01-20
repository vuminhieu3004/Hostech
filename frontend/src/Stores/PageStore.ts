import { create } from "zustand";
import type { IPageStore } from "../Types/StoreType";

export const usePageStore = create<IPageStore>((set) => ({
  pages: 1,
  pagesHouse: 1,
  pageSizes: 1,
  pageSizesHouse: 1,
  setPage: (pages) => set({ pages }),
  setPageHouse: (pagesHouse) => set({ pagesHouse }),
  setPageSize: (pageSizes) => set({ pageSizes }),
  setPageSizesHouse: (pageSizesHouse) => set({ pageSizesHouse }),
}));
