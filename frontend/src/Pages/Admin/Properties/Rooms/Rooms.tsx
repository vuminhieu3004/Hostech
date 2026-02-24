import { Select, Table } from "antd";
import { formatStatusRoom } from "../../../../Constants/Helper";
import { Plus, Edit, Eye } from "lucide-react";
import { Link, Outlet } from "react-router";
import { useOpenStore } from "../../../../Stores/OpenStore";
import type { ColumnsType } from "antd/es/table";

const Rooms = () => {
  const { openForm, setOpenForm } = useOpenStore();

  const data = [
    {
      id: 1,
      name: "Phòng 1",
      zone_id: { id: 1, name: "Khu 1" },
      floor_id: { id: 1, name: "Tầng 1" },
      status: 1,
    },
    {
      id: 2,
      name: "Phòng 2",
      zone_id: { id: 1, name: "Khu 1" },
      floor_id: { id: 2, name: "Tầng 2" },
      status: 2,
    },
  ];

  const columns: ColumnsType<any> = [
    {
      title: "Tên phòng",
      dataIndex: "name",
      key: "name",
    },
    {
      title: "Tầng",
      dataIndex: ["floor_id", "name"],
      key: "floor",
    },
    {
      title: "Khu",
      dataIndex: ["zone_id", "name"],
      key: "zone",
    },
    {
      title: "Trạng thái",
      dataIndex: "status",
      key: "status",
      render: (status: number) => formatStatusRoom(status),
    },
    {
      title: "Thao tác",
      key: "action",
      render: (_: any, record: any) => (
        <div className="flex gap-2">
          <Link
            to={`detailRoom/${record.id}`}
            onClick={() => setOpenForm(true)}
          >
            <button className="flex items-center gap-1 px-3 py-1 rounded bg-green-500 text-white hover:bg-green-600 text-sm">
              <Eye className="w-4 h-4" /> Xem
            </button>
          </Link>
          <Link to={`editRoom/${record.id}`} onClick={() => setOpenForm(true)}>
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
              <label className="text-[13px] pl-2 font-bold">Tên phòng</label>
              <input
                type="text"
                placeholder="Tìm kiếm phòng..."
                className="border border-gray-400 w-50 rounded-[10px] p-1 pl-2 focus:outline-none placeholder:text-[13px]"
              />
            </div>
            <Link to="createRoom" onClick={() => setOpenForm(true)}>
              <div className="flex items-center h-10 mt-2 pl-2 pr-2 w-content rounded-[10px] gap-1 bg-blue-400 p-1 text-black/60 font-semibold hover:text-white hover:font-bold cursor-pointer">
                <Plus className="w-5" /> thêm phòng
              </div>
            </Link>
          </div>
          <Table columns={columns} dataSource={data} rowKey="id" />
        </section>
      ) : (
        <Outlet />
      )}
    </>
  );
};

export default Rooms;
