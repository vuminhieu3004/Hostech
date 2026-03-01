import React, { useState, useEffect } from "react";
import { Button, message, Modal, Tag } from "antd";
import { useNavigate, useParams } from "react-router";
import { ArrowLeft, Edit, Trash2, Building2 } from "lucide-react";
import { useOpenStore } from "../../../../Stores/OpenStore";

interface RoomDetail {
  name: string;
  floor_id: { id: number; name: string };
  zone_id: { id: number; name: string };
  status: number;
}

const DetailRoom = () => {
  const navigate = useNavigate();
  const { id } = useParams();
  const closeForm = useOpenStore((state) => state.setOpenForm);
  const [data, setData] = useState<RoomDetail | null>(null);

  useEffect(() => {
    const mockData: RoomDetail = {
      name: "Phòng 101",
      floor_id: { id: 1, name: "Tầng 1" },
      zone_id: { id: 1, name: "Khu A" },
      status: 1,
    };
    setData(mockData);
  }, [id]);

  const handleEdit = () => {
    closeForm(true);
    navigate(`/admin/properties/editRoom/${id}`);
  };

  const handleDelete = () => {
    Modal.confirm({
      title: "Xóa phòng",
      content: "Bạn có chắc chắn muốn xóa phòng này?",
      okText: "Xóa",
      cancelText: "Hủy",
      okType: "danger",
      onOk() {
        message.success("Xóa phòng thành công!");
        closeForm(false);
        navigate(-1);
      },
    });
  };

  if (!data) return <div className="p-5">Đang tải...</div>;

  const statusConfig = {
    1: { label: "Trống", color: "green" },
    2: { label: "Đã cho thuê", color: "blue" },
    3: { label: "Bảo trì", color: "orange" },
  };

  const currentStatus = statusConfig[
    data.status as keyof typeof statusConfig
  ] || { label: "Không xác định", color: "default" };

  return (
    <div className="flex flex-col gap-6 p-5 max-w-3xl mx-auto">
      <div className="flex items-center justify-between mb-6">
        <div className="flex items-center gap-3">
          <button
            onClick={() => {
              navigate(-1);
              closeForm(false);
            }}
            className="p-2 hover:bg-gray-100 rounded-lg transition"
          >
            <ArrowLeft className="w-5 h-5" />
          </button>
          <h1 className="text-3xl font-bold text-gray-800">Chi tiết phòng</h1>
        </div>
        <div className="flex gap-2">
          <Button
            type="primary"
            icon={<Edit className="w-4 h-4" />}
            onClick={handleEdit}
            className="bg-blue-500 hover:bg-blue-600"
          >
            Sửa
          </Button>
          <Button
            danger
            icon={<Trash2 className="w-4 h-4" />}
            onClick={handleDelete}
          >
            Xóa
          </Button>
        </div>
      </div>

      <div className="bg-white rounded-lg shadow-md p-6 border-l-4 border-blue-500">
        <h2 className="text-lg font-semibold text-gray-700 mb-4">
          Thông tin phòng
        </h2>
        <div className="space-y-4">
          <div className="flex items-center gap-3">
            <Building2 className="w-6 h-6 text-blue-500" />
            <div>
              <p className="text-sm text-gray-600">Tên phòng</p>
              <p className="text-2xl font-bold text-gray-800">{data.name}</p>
            </div>
          </div>
          <div className="p-3 bg-blue-50 rounded-lg border border-blue-200">
            <p className="text-sm text-blue-600 font-medium mb-2">Trạng thái</p>
            <Tag color={currentStatus.color} className="text-base px-3 py-1">
              {currentStatus.label}
            </Tag>
          </div>
        </div>
      </div>

      <div className="grid grid-cols-2 gap-4">
        <div className="bg-white rounded-lg shadow-md p-4 border-l-4 border-purple-500">
          <p className="text-sm text-gray-600 font-medium">Tầng</p>
          <p className="text-lg font-bold text-purple-800 mt-2">
            {data.floor_id.name}
          </p>
        </div>
        <div className="bg-white rounded-lg shadow-md p-4 border-l-4 border-pink-500">
          <p className="text-sm text-gray-600 font-medium">Khu vực</p>
          <p className="text-lg font-bold text-pink-800 mt-2">
            {data.zone_id.name}
          </p>
        </div>
      </div>

      <div className="bg-gradient-to-r from-gray-50 to-gray-100 rounded-lg p-6">
        <h2 className="text-lg font-semibold text-gray-700 mb-4">
          Thông tin bổ sung
        </h2>
        <div className="grid grid-cols-2 gap-4">
          <div>
            <span className="text-sm text-gray-600">Mã phòng:</span>
            <p className="text-gray-800 font-medium">#RM{id}</p>
          </div>
          <div>
            <span className="text-sm text-gray-600">Lần cập nhật:</span>
            <p className="text-gray-800 font-medium">24/02/2026</p>
          </div>
          <div>
            <span className="text-sm text-gray-600">Ngày tạo:</span>
            <p className="text-gray-800 font-medium">24/02/2026</p>
          </div>
          <div>
            <span className="text-sm text-gray-600">Người tạo:</span>
            <p className="text-gray-800 font-medium">Admin</p>
          </div>
        </div>
      </div>
    </div>
  );
};

export default DetailRoom;
