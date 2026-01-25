import { Table } from "antd";
import React from "react";
import { usePageStore } from "../../../Stores/PageStore";

const Floors = () => {
  const { pages, pageSizes, setPage, setPageSize } = usePageStore();
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
  ];
  return (
    <>
      <Table
        rowKey="key"
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
    </>
  );
};

export default Floors;
