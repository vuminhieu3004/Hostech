import { Table } from "antd";
import { usePageStore } from "../../../Stores/PageStore";
import { RightOutlined, DownOutlined } from "@ant-design/icons";
import { ChevronDown, ChevronRight, ChevronUp } from "lucide-react";

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
      <Table
        columns={houseColumns}
        dataSource={dataSource}
        pagination={{
          current: pages,
          pageSize: pageSizes,
          total: dataSource.length,
          onChange: (p: number, ps: number) => {
            (setPage(p), setPageSize(ps));
          },
        }}
      />
    </>
  );
};

export default Properties;
