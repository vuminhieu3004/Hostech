import { Table, Button, Tooltip } from "antd";
import { usePageStore } from "../../../../Stores/PageStore";
import { Plus, Edit, Eye } from "lucide-react";
import { Link, Outlet, useNavigate } from "react-router";
import { useOpenStore } from "../../../../Stores/OpenStore";

const Properties = () => {
  const { pages, pageSizes, setPage, setPageSize } = usePageStore();
  const { openForm, setOpenForm } = useOpenStore();
  const navigate = useNavigate();
  const dataSource = [
    {
      key: "1-1",
      name: "Nhà 01",
      owner: "Anh A",
    },
  ];

  const houseColumns = [
    {
      title: "Tên nhà",
      dataIndex: "name",
      key: "name",
    },
    {
      title: "Chủ nhà",
      dataIndex: "owner",
      key: "owner",
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
                navigate(`detailProperty/${record.key}`);
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
                navigate(`editProperty/${record.key}`);
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
              <label className="text-[13px] pl-2 font-bold">Tên nhà</label>
              <input
                type="text"
                placeholder="Tìm kiếm theo tên khu..."
                className="border border-gray-400 w-50 rounded-[10px] p-1 pl-2 focus:outline-none placeholder:text-[13px]"
              />
            </div>
            <div className="flex gap-2">
              <Button
                type="primary"
                icon={<Plus size={15} />}
                onClick={() => {
                  setOpenForm(true);
                  navigate("createProperty");
                }}
                className="bg-blue-600"
              >
                Thêm nhà
              </Button>
            </div>
          </div>
          <Table
            rowKey="key"
            columns={houseColumns}
            dataSource={dataSource}
            pagination={{
              current: pages,
              pageSize: pageSizes,
              total: dataSource?.length,
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

export default Properties;
