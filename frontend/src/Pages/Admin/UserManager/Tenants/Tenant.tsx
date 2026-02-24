import React from "react";
import type { User } from "../../../../Types/Users";

const Tenant = () => {
  const mockUsers: User[] = [
    {
      id: "111e8400-e29b-41d4-a716-446655440001",
      org_id: "550e8400-e29b-41d4-a716-446655440000",
      role: "ADMIN",
      full_name: "Nguyễn Văn Quý",
      phone: "0901000001",
      email: "admin@technova.vn",
    },
    {
      id: "111e8400-e29b-41d4-a716-446655440002",
      org_id: "550e8400-e29b-41d4-a716-446655440000",
      role: "MANAGER",
      full_name: "Trần Thị Mai",
      phone: "0901000002",
      email: "manager@technova.vn",
    },
    {
      id: "111e8400-e29b-41d4-a716-446655440003",
      org_id: "6fa459ea-ee8a-3ca4-894e-db77e160355e",
      role: "OWNER",
      full_name: "Lê Hoàng Anh",
      phone: "0901000003",
      email: "owner@greensoft.vn",
    },
    {
      id: "111e8400-e29b-41d4-a716-446655440004",
      org_id: "6fa459ea-ee8a-3ca4-894e-db77e160355e",
      role: "STAFF",
      full_name: "Phạm Minh Tuấn",
      phone: "0901000004",
      email: "staff@greensoft.vn",
    },
    {
      id: "111e8400-e29b-41d4-a716-446655440005",
      org_id: "7d444840-9dc0-11d1-b245-5ffdce74fad2",
      role: "TENANT",
      full_name: "Vũ Thanh Huyền",
      phone: "0901000005",
      email: "tenant@digitalhub.vn",
    },
    {
      id: "111e8400-e29b-41d4-a716-446655440006",
      org_id: "7d444840-9dc0-11d1-b245-5ffdce74fad2",
      role: "STAFF",
      full_name: "Đỗ Đức Nam",
      phone: "0901000006",
      email: "nam@digitalhub.vn",
    },
  ];

  return <div>Tenant</div>;
};

export default Tenant;
