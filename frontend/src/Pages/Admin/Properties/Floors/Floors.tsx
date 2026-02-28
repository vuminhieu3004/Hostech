import { Table, Button, Tooltip } from "antd";
import React from "react";
import { usePageStore } from "../../../../Stores/PageStore";
import { Plus, Edit, Eye } from "lucide-react";
import { Link, Outlet, useNavigate } from "react-router";
import { useOpenStore } from "../../../../Stores/OpenStore";

const Floors = () => {
  const { pages, pageSizes, setPage, setPageSize } = usePageStore();
  const { openForm, setOpenForm } = useOpenStore();
  const navigate = useNavigate();

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
          <Tooltip title="Xem chi tiết">
            <Button
              icon={<Eye size={15} />}
              onClick={() => {
                setOpenForm(true);
                navigate(`detailFloor/${record.id}`);
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
                navigate(`editFloor/${record.id}`);
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
          <div className="flex item-center justify-between border p-2 border-gray-300 rounded-[10px]">
            <div className="flex flex-col gap-1">
              <label className="text-[13px] pl-2 font-bold">Tên tầng</label>
              <input
                type="text"
                placeholder="Tìm kiếm tầng..."
                className="border border-gray-400 w-50 rounded-[10px] p-1 pl-2 focus:outline-none placeholder:text-[13px]"
              />
            </div>
            <Button
              type="primary"
              icon={<Plus size={15} />}
              onClick={() => {
                setOpenForm(true);
                navigate("createFloor");
              }}
              className="bg-blue-600"
            >
              Thêm tầng
            </Button>
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
