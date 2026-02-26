export type TMeter = {
  id: string;
  org_id: string;
  room_id: string;
  code: string;
  type: "ELECTRIC" | "WATER";
  installed_at: string;
};
