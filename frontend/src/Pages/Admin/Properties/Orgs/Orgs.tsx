import { Table } from "antd";
import React from "react";
import { usePageStore } from "../../../../Stores/PageStore";
import { Plus, Edit, Eye } from "lucide-react";
import { Link, Outlet } from "react-router";
import { useOpenStore } from "../../../../Stores/OpenStore";
import type { ColumnsType } from "antd/es/table";

const Orgs = () => {
  const { pages, pageSizes, setPage, setPageSize } = usePageStore();
  const { openForm, setOpenForm } = useOpenStore();

  const mockOrgs = [
    {
      id: "550e8400-e29b-41d4-a716-446655440000",
      name: "Công ty TNHH TechNova",
      phone: "0901234567",
      email: "contact@technova.vn",
      address: "Tầng 5, 123 Nguyễn Trãi, Thanh Xuân, Hà Nội",
    },
    {
      id: "6fa459ea-ee8a-3ca4-894e-db77e160355e",
      name: "Công ty Cổ phần GreenSoft",
      phone: "0912345678",
      email: "info@greensoft.vn",
      address: "45 Lê Lợi, Quận 1, TP. Hồ Chí Minh",
    },
  ];

  const columns: ColumnsType<any> = [
    {
      title: "Tên tổ chức",
      dataIndex: "name",
      key: "name",
    },
    {
      title: "Email",
      dataIndex: "email",
      key: "email",
    },
    {
      title: "Số điện thoại",
      dataIndex: "phone",
      key: "phone",
    },
    {
      title: "Địa chỉ",
      dataIndex: "address",
      key: "address",
    },
    {
      title: "Thao tác",
      key: "action",
      render: (_: any, record: any) => (
        <div className="flex gap-2">
          <Link to={`detailOrg/${record.id}`} onClick={() => setOpenForm(true)}>
            <button className="flex items-center gap-1 px-3 py-1 rounded bg-green-500 text-white hover:bg-green-600 text-sm">
              <Eye className="w-4 h-4" /> Xem
            </button>
          </Link>
          <Link to={`editOrg/${record.id}`} onClick={() => setOpenForm(true)}>
            <button className="flex items-center gap-1 px-3 py-1 rounded bg-blue-500 text-white hover:bg-blue-600 text-sm">
              <Edit className="w-4 h-4" /> Sửa
            </button>
          </Link>
        </div>
      ),
    },
  ];

  return (
    <>
      {openForm == false ? (
        <section className="flex flex-col gap-5">
          <div className="flex items-center justify-between border p-2 border-gray-300 rounded-[10px]">
            <div className="flex flex-col gap-1">
              <label className="text-[13px] pl-2 font-bold">Tên tổ chức</label>
              <input
                type="text"
                placeholder="Tìm kiếm tổ chức..."
                className="border border-gray-400 w-50 rounded-[10px] p-1 pl-2 focus:outline-none placeholder:text-[13px]"
              />
            </div>
            <Link to="createOrg" onClick={() => setOpenForm(true)}>
              <div className="flex items-center h-10 mt-2 pl-2 pr-2 w-content rounded-[10px] gap-1 bg-blue-400 p-1 text-black/60 font-semibold hover:text-white hover:font-bold cursor-pointer">
                <Plus className="w-5" /> thêm tổ chức
              </div>
            </Link>
          </div>
          <Table
            rowKey="id"
            columns={columns}
            dataSource={mockOrgs}
            pagination={{
              current: pages,
              pageSize: pageSizes,
              total: mockOrgs?.length,
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

export default Orgs;
