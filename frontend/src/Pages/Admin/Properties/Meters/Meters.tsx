import { Table } from "antd";
import React from "react";
import { usePageStore } from "../../../../Stores/PageStore";
import { Plus, Edit, Eye } from "lucide-react";
import { Link, Outlet } from "react-router";
import { useOpenStore } from "../../../../Stores/OpenStore";
import type { ColumnsType } from "antd/es/table";

const Meters = () => {
  const { pages, pageSizes, setPage, setPageSize } = usePageStore();
  const { openForm, setOpenForm } = useOpenStore();

  const data = [
    {
      id: 1,
      name: "Đồng hồ điện 101",
      room_id: { id: 1, name: "Phòng 101" },
      meter_type: "Điện",
      meter_number: "DL12345678",
      status: 1,
    },
    {
      id: 2,
      name: "Đồng hồ nước 101",
      room_id: { id: 1, name: "Phòng 101" },
      meter_type: "Nước",
      meter_number: "NC87654321",
      status: 1,
    },
  ];

  const columns: ColumnsType<any> = [
    {
      title: "Tên đồng hồ",
      dataIndex: "name",
      key: "name",
    },
    {
      title: "Phòng",
      dataIndex: ["room_id", "name"],
      key: "room",
    },
    {
      title: "Loại",
      dataIndex: "meter_type",
      key: "meter_type",
      render: (type: string) => (
        <span className="px-2 py-1 rounded bg-blue-100 text-blue-800 text-sm font-medium">
          {type}
        </span>
      ),
    },
    {
      title: "Số hiệu",
      dataIndex: "meter_number",
      key: "meter_number",
    },
    {
      title: "Trạng thái",
      dataIndex: "status",
      key: "status",
      render: (status: number) => (
        <span
          className={`px-2 py-1 rounded text-sm font-medium ${
            status === 1
              ? "bg-green-100 text-green-800"
              : "bg-red-100 text-red-800"
          }`}
        >
          {status === 1 ? "Hoạt động" : "Không hoạt động"}
        </span>
      ),
    },
    {
      title: "Thao tác",
      key: "action",
      render: (_: any, record: any) => (
        <div className="flex gap-2">
          <Link
            to={`detailMeter/${record.id}`}
            onClick={() => setOpenForm(true)}
          >
            <button className="flex items-center gap-1 px-3 py-1 rounded bg-green-500 text-white hover:bg-green-600 text-sm">
              <Eye className="w-4 h-4" /> Xem
            </button>
          </Link>
          <Link to={`editMeter/${record.id}`} onClick={() => setOpenForm(true)}>
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
              <label className="text-[13px] pl-2 font-bold">Tên đồng hồ</label>
              <input
                type="text"
                placeholder="Tìm kiếm đồng hồ..."
                className="border border-gray-400 w-50 rounded-[10px] p-1 pl-2 focus:outline-none placeholder:text-[13px]"
              />
            </div>
            <Link to="createMeter" onClick={() => setOpenForm(true)}>
              <div className="flex items-center h-10 mt-2 pl-2 pr-2 w-content rounded-[10px] gap-1 bg-blue-400 p-1 text-black/60 font-semibold hover:text-white hover:font-bold cursor-pointer">
                <Plus className="w-5" /> thêm đồng hồ
              </div>
            </Link>
          </div>
          <Table
            rowKey="id"
            columns={columns}
            dataSource={data}
            pagination={{
              current: pages,
              pageSize: pageSizes,
              total: data?.length,
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

export default Meters;
