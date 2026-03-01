import React from "react";
import type { TMeter } from "../../../Types/Meter.Type";

const Meters = () => {
  const meters: TMeter[] = [
    {
      id: "550e8400-e29b-41d4-a716-446655440001",
      org_id: "org-001",
      room_id: "room-101",
      code: "EL-101",
      type: "ELECTRIC",
      installed_at: "2024-01-10",
    },
    {
      id: "550e8400-e29b-41d4-a716-446655440002",
      org_id: "org-001",
      room_id: "room-101",
      code: "WT-101",
      type: "WATER",
      installed_at: "2024-01-10",
    },
    {
      id: "550e8400-e29b-41d4-a716-446655440003",
      org_id: "org-001",
      room_id: "room-102",
      code: "EL-102",
      type: "ELECTRIC",
      installed_at: "2024-02-15",
    },
    {
      id: "550e8400-e29b-41d4-a716-446655440004",
      org_id: "org-001",
      room_id: "room-102",
      code: "WT-102",
      type: "WATER",
      installed_at: "2024-02-15",
    },
    {
      id: "550e8400-e29b-41d4-a716-446655440005",
      org_id: "org-002",
      room_id: "room-201",
      code: "EL-201",
      type: "ELECTRIC",
      installed_at: "2023-12-01",
    },
    {
      id: "550e8400-e29b-41d4-a716-446655440006",
      org_id: "org-002",
      room_id: "room-201",
      code: "WT-201",
      type: "WATER",
      installed_at: "2023-12-01",
    },
  ];
  return <div>Meters</div>;
};

export default Meters;
