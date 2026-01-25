import { Select } from "antd";
import { formatStatusRoom } from "../../../Constants/Helper";

const Rooms = () => {
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
    {
      id: 3,
      name: "Phòng 3",
      zone_id: { id: 1, name: "Khu 1" },
      floor_id: { id: 3, name: "Tầng 3" },
      status: 3,
    },
    {
      id: 4,
      name: "Phòng 4",
      zone_id: { id: 1, name: "Khu 1" },
      floor_id: { id: 4, name: "Tầng 4" },
      status: 4,
    },
    {
      id: 5,
      name: "Phòng 5",
      zone_id: { id: 1, name: "Khu 1" },
      floor_id: { id: 1, name: "Tầng 1" },
      status: 1,
    },
    {
      id: 10,
      name: "Phòng 10",
      zone_id: { id: 1, name: "Khu 1" },
      floor_id: { id: 1, name: "Tầng 1" },
      status: 4,
    },
    {
      id: 11,
      name: "Phòng 11",
      zone_id: { id: 1, name: "Khu 1" },
      floor_id: { id: 1, name: "Tầng 1" },
      status: 1,
    },
    {
      id: 12,
      name: "Phòng 12",
      zone_id: { id: 1, name: "Khu 1" },
      floor_id: { id: 1, name: "Tầng 1" },
      status: 2,
    },
    {
      id: 13,
      name: "Phòng 13",
      zone_id: { id: 1, name: "Khu 1" },
      floor_id: { id: 1, name: "Tầng 1" },
      status: 2,
    },
    {
      id: 14,
      name: "Phòng 14",
      zone_id: { id: 1, name: "Khu 1" },
      floor_id: { id: 1, name: "Tầng 1" },
      status: 3,
    },
  ];
  return (
    <>
      <div>
        <div className="mb-5">
          <div className="flex flex-col w-50 justify-center">
            <label htmlFor="" className="pl-2 p-1 font-semibold">
              Lọc theo trạng thái
            </label>
            <Select
              defaultValue="Chọn trang thái"
              style={{ width: 200 }}
              options={[
                {
                  label: <span>Trạng thái</span>,
                  title: "Trạng thái",
                  options: [
                    { label: <span>Phòng trống</span>, value: "1" },
                    { label: <span>Phòng đang ở</span>, value: "2" },
                    { label: <span>Phòng đang sửa</span>, value: "3" },
                    { label: <span>Phòng đặt chỗ</span>, value: "4" },
                  ],
                },
              ]}
            />
          </div>
        </div>
        <section className="flex flex-wrap items-center justify-between gap-4">
          {data?.map((item, index) => (
            <div
              key={index}
              className={`w-56 h-50 p-3 flex flex-col justify-center gap-3 border border-gray-300 rounded-2xl ${item.status == 1 ? "bg-green-400 text-white font-semibold shadow-sm shadow-green-400" : item.status == 2 ? "bg-violet-500 text-white font-semibold shadow-sm shadow-violet-400" : item.status == 3 ? "bg-red-500 text-white font-semibold shadow-sm shadow-red-400" : item.status == 4 ? "bg-gray-400 text-white font-semibold shadow-sm shadow-gray-400" : ""}`}
            >
              <p>
                <span>Tên phòng:</span> {item.name}
              </p>
              <p>
                <span>Thuộc tầng:</span> {item.floor_id.name}
              </p>
              <p>
                <span>Thuộc khu:</span> {item.zone_id.name}
              </p>
              <p>
                <span>Trạng thái: </span> {formatStatusRoom(item.status)}
              </p>
            </div>
          ))}
        </section>
      </div>
    </>
  );
};

export default Rooms;
