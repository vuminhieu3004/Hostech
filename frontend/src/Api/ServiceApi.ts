import type { Service, ServiceFormValues } from "../Types/ServiceTypes";
import { ServiceCalcMode } from "../Types/ServiceTypes";

// Dữ liệu giả lập ban đầu
const initialServices: Service[] = [
    {
        id: "uuid-1",
        org_id: "org-1",
        code: "ELECTRIC",
        name: "Điện",
        calc_mode: ServiceCalcMode.PER_METER,
        unit: "kWh",
        is_recurring: true,
        is_active: true,
        price: 3500,
    },
    {
        id: "uuid-2",
        org_id: "org-1",
        code: "WATER_PERSON",
        name: "Nước sinh hoạt",
        calc_mode: ServiceCalcMode.PER_PERSON,
        unit: "Người",
        is_recurring: true,
        is_active: true,
        price: 100000,
    },
    {
        id: "uuid-3",
        org_id: "org-1",
        code: "INTERNET",
        name: "Internet",
        calc_mode: ServiceCalcMode.PER_ROOM,
        unit: "Phòng",
        is_recurring: true,
        is_active: true,
        price: 200000,
    }
];

// Lưu trữ trong bộ nhớ cho phiên làm việc
let mockServices = [...initialServices];

const delay = (ms: number) => new Promise((resolve) => setTimeout(resolve, ms));

export const getServices = async (): Promise<Service[]> => {
    await delay(300);
    return [...mockServices];
};

export const createService = async (data: ServiceFormValues): Promise<Service> => {
    await delay(300);
    const newService: Service = {
        id: crypto.randomUUID(), // Sử dụng API có sẵn của trình duyệt
        org_id: "org-1", // Mặc định tổ chức
        ...data,
    };
    mockServices = [newService, ...mockServices];
    return newService;
};

export const updateService = async (id: string, data: ServiceFormValues): Promise<Service> => {
    await delay(300);
    const index = mockServices.findIndex((s) => s.id === id);
    if (index === -1) throw new Error("Service not found");

    const updatedService = { ...mockServices[index], ...data };
    mockServices[index] = updatedService;
    mockServices = [...mockServices]; // Cập nhật tham chiếu để kích hoạt re-render nếu cần
    return updatedService;
};

export const deleteService = async (id: string): Promise<void> => {
    await delay(300);
    mockServices = mockServices.filter((s) => s.id !== id);
};
