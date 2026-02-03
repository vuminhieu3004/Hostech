import { Table } from "antd";
import { usePageStore } from "../../../Stores/PageStore";
import { RightOutlined, DownOutlined } from "@ant-design/icons";
import { ChevronDown, ChevronRight, ChevronUp, Plus } from "lucide-react";

const Properties = () => {
  const { pages, pageSizes, setPage, setPageSize } = usePageStore();
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
  ];

  return (
    <>
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
          <div className="flex items-center h-10 mt-2 pl-2 pr-2 w-content rounded-[10px] gap-1 bg-blue-400 p-1 text-black/60 font-semibold hover:text-white hover:font-bold cursor-pointer">
            <Plus className="w-5" /> thêm nhà
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
    </>
  );
};

export default Properties;
