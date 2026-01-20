export interface IOpenTypeStore {
  open: boolean;
  eyePassword: boolean;
  setOpen: (open: boolean) => void;
  setEyePassword: (eyePassword: boolean) => void;
}

export interface IPageStore {
  pages: number;
  pagesHouse: number;
  pageSizes: number;
  pageSizesHouse: number;
  setPage: (pages: number) => void;
  setPageHouse: (pagesHouse: number) => void;
  setPageSize: (pageSizes: number) => void;
  setPageSizesHouse: (pageSizesHouse: number) => void;
}
