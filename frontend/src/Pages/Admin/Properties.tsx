import { Table } from "antd";
import { usePageStore } from "../../Stores/PageStore";
import { RightOutlined, DownOutlined } from "@ant-design/icons";
import { ChevronDown, ChevronRight, ChevronUp } from "lucide-react";

const Properties = () => {
  const {
    pages,
    pagesHouse,
    pageSizes,
    pageSizesHouse,
    setPage,
    setPageHouse,
    setPageSize,
    setPageSizesHouse,
  } = usePageStore();
  const dataSource = [
    {
      key: "1",
      areaName: "Khu A",
      houses: [
        {
          key: "1-1",
          name: "Nhà 01",
          owner: "Anh A",
          floor: [{ name: "Tầng 1", rooms: [{ name: "Phòng 1" }] }],
        },
      ],
    },
  ];

  const areaColumns = [
    {
      title: "Tên khu",
      dataIndex: "areaName",
      key: "areaName",
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

  const floorColumns = [
    {
      title: "Tên tầng",
      dataIndex: "name",
      key: "name",
    },
  ];

  const roomColumns = [
    {
      title: "Tên phòng",
      dataIndex: "name",
      key: "name",
    },
  ];
  return (
    <>
      <Table
        columns={areaColumns}
        dataSource={dataSource}
        expandable={{
          expandIcon: ({ expanded, onExpand, record }) => (
            <span
              className="cursor-pointer"
              onClick={(e) => onExpand(record, e)}
            >
              {expanded ? <ChevronDown /> : <ChevronRight />}
            </span>
          ),
          expandedRowRender: (record) => (
            <Table
              columns={houseColumns}
              dataSource={record.houses}
              expandable={{
                expandIcon: ({ expanded, onExpand, record }) => (
                  <span
                    className="cursor-pointer"
                    onClick={(e) => onExpand(record, e)}
                  >
                    {expanded ? <ChevronDown /> : <ChevronRight />}
                  </span>
                ),
                expandedRowRender: (record) => (
                  <Table
                    columns={floorColumns}
                    dataSource={record.floor}
                    expandable={{
                      expandIcon: ({ expanded, onExpand, record }) => (
                        <span
                          className="cursor-pointer"
                          onClick={(e) => onExpand(record, e)}
                        >
                          {expanded ? <ChevronDown /> : <ChevronRight />}
                        </span>
                      ),
                      expandedRowRender: (record) => (
                        <Table
                          columns={roomColumns}
                          dataSource={record.rooms}
                          pagination={false}
                          rowKey="key"
                        />
                      ),
                    }}
                    pagination={false}
                    rowKey="key"
                  />
                ),
              }}
              pagination={false}
              rowKey="key"
            />
          ),
        }}
        pagination={{
          current: pages,
          pageSize: pageSizes,
          total: dataSource.length,
          onChange: (p: number, ps: number) => {
            setPage(p), setPageSize(ps);
          },
        }}
      />
    </>
  );
};

export default Properties;
