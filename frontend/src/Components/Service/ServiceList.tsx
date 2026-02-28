import { useQuery, useMutation, useQueryClient } from "@tanstack/react-query";
import { Table, Button, Popconfirm, Tag, Tooltip, notification } from "antd";
import { Edit, Trash2, Plus } from "lucide-react";
import { getServices, deleteService } from "../../Api/ServiceApi";
import { ServiceCalcModeLabels } from "../../Types/ServiceTypes";
import type { Service } from "../../Types/ServiceTypes";
import { useServiceStore } from "../../Stores/useServiceStore";
// import { useTokenStore } from "../../Stores/AuthStore";

const ServiceList = () => {
    const { data: services, isLoading } = useQuery({ queryKey: ["services"], queryFn: getServices });
    const { openModal } = useServiceStore();
    const queryClient = useQueryClient();
    // const userRole = useTokenStore((state) => state.role);

    // Kiểm tra phân quyền đơn giản: chỉ 'admin' hoặc các vai trò cụ thể mới có thể sửa/xóa
    // Giả sử 'admin' là key của vai trò.
    // Để test, cho phép chỉnh sửa với mọi người hoặc kiểm tra nếu user tồn tại
    const canEdit = true; // userRole === "admin" || userRole === "manager";

    const deleteMutation = useMutation({
        mutationFn: deleteService,
        onSuccess: () => {
            notification.success({ message: "Xóa dịch vụ thành công" });
            queryClient.invalidateQueries({ queryKey: ["services"] });
        },
        onError: () => {
            notification.error({ message: "Xóa thất bại", description: "Có thể dịch vụ đang được sử dụng" });
        },
    });

    const columns = [
        {
            title: "Mã",
            dataIndex: "code",
            key: "code",
            fontWeight: "bold",
        },
        {
            title: "Tên dịch vụ",
            dataIndex: "name",
            key: "name",
            render: (text: string) => <span className="font-medium">{text}</span>,
        },
        {
            title: "Cách tính",
            dataIndex: "calc_mode",
            key: "calc_mode",
            render: (mode: keyof typeof ServiceCalcModeLabels) => (
                <Tag color="geekblue">{ServiceCalcModeLabels[mode] || mode}</Tag>
            ),
        },
        {
            title: "Đơn vị",
            dataIndex: "unit",
            key: "unit",
        },
        {
            title: "Đơn giá",
            dataIndex: "price",
            key: "price",
            render: (price: number) => <span>{price?.toLocaleString()} VNĐ</span>,
        },
        {
            title: "Trạng thái",
            dataIndex: "is_active",
            key: "is_active",
            render: (isActive: boolean) => (
                <Tag color={isActive ? "green" : "red"}>{isActive ? "Hoạt động" : "Ngưng"}</Tag>
            ),
        },
        {
            title: "Hành động",
            key: "action",
            render: (_: any, record: Service) => (
                <div className="flex gap-2">
                    <Tooltip title={!canEdit ? "Bạn không có quyền sửa" : "Sửa"}>
                        <Button
                            disabled={!canEdit}
                            icon={<Edit size={16} />}
                            onClick={() => openModal(record)}
                        />
                    </Tooltip>

                    <Popconfirm
                        title="Bạn có chắc muốn xóa dịch vụ này?"
                        onConfirm={() => deleteMutation.mutate(record.id)}
                        disabled={!canEdit}
                    >
                        <Tooltip title={!canEdit ? "Bạn không có quyền xóa" : "Xóa"}>
                            <Button disabled={!canEdit} danger icon={<Trash2 size={16} />} />
                        </Tooltip>
                    </Popconfirm>
                </div>
            ),
        },
    ];

    return (
        <div className="flex flex-col gap-4">
            <div className="flex justify-between items-center">
                <h2 className="text-lg font-bold">Danh sách dịch vụ</h2>
                {canEdit && (
                    <Button
                        type="primary"
                        icon={<Plus size={16} />}
                        onClick={() => openModal()}
                        className="bg-blue-600"
                    >
                        Thêm dịch vụ
                    </Button>
                )}
            </div>

            <Table
                dataSource={services}
                columns={columns}
                rowKey="id"
                loading={isLoading}
                pagination={{ pageSize: 10 }}
                scroll={{ x: 600 }}
            />
        </div>
    );
};

export default ServiceList;
