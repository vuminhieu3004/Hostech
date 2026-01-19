export interface IOpenTypeStore {
  open: boolean;
  eyePassword: boolean;
  setOpen: (open: boolean) => void;
  setEyePassword: (eyePassword: boolean) => void;
}
