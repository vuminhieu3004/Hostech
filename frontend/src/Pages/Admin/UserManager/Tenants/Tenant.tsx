import React from "react";
import type { User } from "../../../../Types/Users";
import { Link, Outlet, useNavigate } from "react-router";
import { usePageStore } from "../../../../Stores/PageStore";
import { useOpenStore } from "../../../../Stores/OpenStore";
import { Eye, Plus } from "lucide-react";
import { Table, Button, Tooltip } from "antd";

const Tenant = () => {
  const { pages, pageSizes, setPage, setPageSize } = usePageStore();
  const { openForm, setOpenForm } = useOpenStore();
  const navigate = useNavigate();
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

  const tenantColumns = [
    {
      title: "Tên quản lý",
      dataIndex: "full_name",
      key: "full_name",
    },
    {
      title: "Số điện thoại",
      dataIndex: "phone",
      key: "phone",
    },
    {
      title: "Email",
      dataIndex: "email",
      key: "email",
    },
    {
      title: "Thao tác",
      key: "action",
      render: (_: any, record: any) => (
        <div className="flex gap-2">
          <Tooltip title="Xem chi tiết">
            <Button
              icon={<Eye size={15} />}
              onClick={() => {
                setOpenForm(true);
                navigate(`detailTenant/${record.id}`);
              }}
              style={{
                backgroundColor: "#22c55e",
                borderColor: "#22c55e",
                color: "white",
              }}
            />
          </Tooltip>
        </div>
      ),
    },
  ];

  return (
    <>
      {openForm == false ? (
        <section className="flex flex-col gap-5">
          <div className="flex item-center justify-between border p-2 border-gray-300 rounded-[10px]">
            <div className="flex flex-col gap-1">
              <label className="text-[13px] pl-2 font-bold">Tên quản lý</label>
              <input
                type="text"
                placeholder="Tìm kiếm theo tên khu..."
                className="border border-gray-400 w-50 rounded-[10px] p-1 pl-2 focus:outline-none placeholder:text-[13px]"
              />
            </div>
            <Link to="createProperty" onClick={() => setOpenForm(true)}>
              <div className="flex items-center h-10 mt-2 pl-2 pr-2 w-content rounded-[10px] gap-1 bg-blue-400 p-1 text-black/60 font-semibold hover:text-white hover:font-bold cursor-pointer">
                <Plus className="w-5" /> thêm quản lý
              </div>
            </Link>
          </div>
          <Table
            rowKey="key"
            columns={tenantColumns}
            dataSource={mockUsers}
            pagination={{
              current: pages,
              pageSize: pageSizes,
              total: mockUsers?.length,
              onChange: (p: number, ps: number) => {
                setPage(p);
                setPageSize(ps);
              },
            }}
          />
        </section>
      ) : (
        <Outlet />
      )}
    </>
  );
};

export default Tenant;
