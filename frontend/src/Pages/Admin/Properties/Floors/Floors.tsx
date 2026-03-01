import { Table } from "antd";
import React from "react";
import { usePageStore } from "../../../../Stores/PageStore";
import { Plus, Edit, Eye } from "lucide-react";
import { Link, Outlet } from "react-router";
import { useOpenStore } from "../../../../Stores/OpenStore";

const Floors = () => {
  const { pages, pageSizes, setPage, setPageSize } = usePageStore();
  const { openForm, setOpenForm } = useOpenStore();

  const dataFloors = [
    {
      id: 1,
      name: "Tầng 1",
    },
  ];

  const floorColumns = [
    {
      title: "Tên tầng",
      dataIndex: "name",
      key: "name",
    },
    {
      title: "Thao tác",
      key: "action",
      render: (_: any, record: any) => (
        <div className="flex gap-2">
          <Link
            to={`detailFloor/${record.id}`}
            onClick={() => setOpenForm(true)}
          >
            <button className="flex items-center gap-1 px-3 py-1 rounded bg-green-500 text-white hover:bg-green-600 text-sm">
              <Eye className="w-4 h-4" /> Xem
            </button>
          </Link>
          <Link to={`editFloor/${record.id}`} onClick={() => setOpenForm(true)}>
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
          <div className="flex item-center justify-between border p-2 border-gray-300 rounded-[10px]">
            <div className="flex flex-col gap-1">
              <label className="text-[13px] pl-2 font-bold">Tên tầng</label>
              <input
                type="text"
                placeholder="Tìm kiếm tầng..."
                className="border border-gray-400 w-50 rounded-[10px] p-1 pl-2 focus:outline-none placeholder:text-[13px]"
              />
            </div>
            <Link to="createFloor" onClick={() => setOpenForm(true)}>
              <div className="flex items-center h-10 mt-2 pl-2 pr-2 w-content rounded-[10px] gap-1 bg-blue-400 p-1 text-black/60 font-semibold hover:text-white hover:font-bold cursor-pointer">
                <Plus className="w-5" /> thêm tầng
              </div>
            </Link>
          </div>
          <Table
            rowKey="id"
            columns={floorColumns}
            dataSource={dataFloors}
            pagination={{
              current: pages,
              pageSize: pageSizes,
              total: dataFloors?.length,
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

export default Floors;
