import { Select, Table, Button, Tooltip } from "antd";
import { formatStatusRoom } from "../../../../Constants/Helper";
import { Plus, Edit, Eye } from "lucide-react";
import { Link, Outlet, useNavigate } from "react-router";
import { useOpenStore } from "../../../../Stores/OpenStore";
import type { ColumnsType } from "antd/es/table";

const Rooms = () => {
  const { openForm, setOpenForm } = useOpenStore();
  const navigate = useNavigate();

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
          <Tooltip title="Xem chi tiết">
            <Button
              icon={<Eye size={15} />}
              onClick={() => {
                setOpenForm(true);
                navigate(`detailRoom/${record.id}`);
              }}
              style={{
                backgroundColor: "#22c55e",
                borderColor: "#22c55e",
                color: "white",
              }}
            />
          </Tooltip>
          <Tooltip title="Sửa">
            <Button
              icon={<Edit size={15} />}
              onClick={() => {
                setOpenForm(true);
                navigate(`editRoom/${record.id}`);
              }}
              style={{
                backgroundColor: "#0ea5e9",
                borderColor: "#0ea5e9",
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
          <div className="flex items-center justify-between border p-2 border-gray-300 rounded-[10px]">
            <div className="flex flex-col gap-1">
              <label className="text-[13px] pl-2 font-bold">Tên phòng</label>
              <input
                type="text"
                placeholder="Tìm kiếm phòng..."
                className="border border-gray-400 w-50 rounded-[10px] p-1 pl-2 focus:outline-none placeholder:text-[13px]"
              />
            </div>
            <div className="flex gap-2">
              <Button
                type="primary"
                icon={<Plus size={15} />}
                onClick={() => {
                  setOpenForm(true);
                  navigate("createRoom");
                }}
                className="bg-blue-600"
              >
                Thêm phòng
              </Button>
            </div>
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
