import { Table } from "antd";
import React from "react";
import { usePageStore } from "../../../Stores/PageStore";

const Zones = () => {
  const { pages, pageSizes, setPage, setPageSize } = usePageStore();
  const dataZones = [
    {
      key: "1",
      areaName: "Khu A",
    },
  ];
  const zoneColumns = [
    {
      title: "Tên khu",
      dataIndex: "areaName",
      key: "areaName",
    },
  ];
  return (
    <>
      <Table
        rowKey="key"
        columns={zoneColumns}
        dataSource={dataZones}
        pagination={{
          current: pages,
          pageSize: pageSizes,
          total: dataZones?.length,
          onChange: (p: number, ps: number) => {
            setPage(p);
            setPageSize(ps);
          },
        }}
      />
    </>
  );
};

export default Zones;
