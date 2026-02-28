import type { Contract, ContractFormValues, ContractUser } from "../Types/ContractTypes";
import { ContractStatus } from "../Types/ContractTypes";

// ───── Dữ liệu giả lập ban đầu ─────

// Danh sách người thuê (dùng cho select trong form)
export const mockTenants: ContractUser[] = [
    {
        id: "user-1",
        org_id: "org-1",
        full_name: "Nguyễn Văn A",
        phone: "0901234567",
        email: "nguyenvana@example.com",
        email_verified_at: "2025-01-01",
        phone_verified_at: "2025-01-01",
        is_active: "1",
        mfa_enabled: "0",
        last_login_at: "2026-02-01",
        roles: "tenant",
        permissions: "",
        created_at: "2025-01-01",
        updated_at: "2025-01-01",
    },
    {
        id: "user-2",
        org_id: "org-1",
        full_name: "Trần Thị B",
        phone: "0912345678",
        email: "tranthib@example.com",
        email_verified_at: "2025-03-01",
        phone_verified_at: "2025-03-01",
        is_active: "1",
        mfa_enabled: "0",
        last_login_at: "2026-01-15",
        roles: "tenant",
        permissions: "",
        created_at: "2025-03-01",
        updated_at: "2025-03-01",
    },
    {
        id: "user-3",
        org_id: "org-1",
        full_name: "Lê Minh C",
        phone: "0987654321",
        email: "leminhc@example.com",
        email_verified_at: null,
        phone_verified_at: null,
        is_active: "1",
        mfa_enabled: "0",
        last_login_at: null,
        roles: "tenant",
        permissions: "",
        created_at: "2026-01-10",
        updated_at: "2026-01-10",
    },
];

const mockUser = mockTenants[0];

const mockProperty = {
    id: "prop-1",
    code: "NHA01",
    name: "Nhà trọ Bình Thạnh",
    address: "123 Đinh Bộ Lĩnh, Bình Thạnh, TP.HCM",
    note: "",
    use_floors: true,
    default_billing_cycle: "monthly",
    default_due_day: "5",
    default_cutoff_day: "28",
    bank_accounts: "",
    floors: [],
    rooms: [],
    created_at: "2025-01-01",
    updated_at: "2025-01-01",
};

const mockRoom = {
    id: "room-1",
    code: "P101",
    name: "Phòng 101",
    type: "single",
    area: "20",
    floor: "1",
    capacity: "2",
    base_price: "3000000",
    status: "occupied",
    description: "",
    amenities: "",
    utilities: "",
    created_at: "2025-01-01",
    updated_at: "2025-01-01",
};

let mockContracts: Contract[] = [
    {
        id: "contract-1",
        org_id: "org-1",
        status: ContractStatus.ACTIVE,
        property: mockProperty,
        room: mockRoom,
        members: [
            {
                id: "member-1",
                contract_id: "contract-1",
                user: mockUser,
                role: "tenant",
                is_primary: "1",
                joined_at: "2026-01-01",
                left_at: null,
                created_at: "2026-01-01",
                updated_at: "2026-01-01",
            },
        ],
        start_date: "2026-01-01",
        end_date: "2026-12-31",
        rent_price: 3000000,
        deposit_amount: 6000000,
        billing_cycle: "monthly",
        due_day: "5",
        cutoff_day: "28",
        join_code: "ABC123",
        join_code_expires_at: "2026-02-01",
        created_by: mockUser,
        created_at: "2026-01-01",
        updated_at: "2026-01-01",
        deleted_at: null,
    },
    {
        id: "contract-2",
        org_id: "org-1",
        status: ContractStatus.EXPIRED,
        property: mockProperty,
        room: { ...mockRoom, id: "room-2", code: "P102", name: "Phòng 102" },
        members: [],
        start_date: "2025-01-01",
        end_date: "2025-12-31",
        rent_price: 2500000,
        deposit_amount: 5000000,
        billing_cycle: "monthly",
        due_day: "5",
        cutoff_day: "28",
        join_code: "XYZ789",
        join_code_expires_at: "2025-02-01",
        created_by: mockUser,
        created_at: "2025-01-01",
        updated_at: "2025-01-01",
        deleted_at: null,
    },
    {
        id: "contract-3",
        org_id: "org-1",
        status: ContractStatus.DRAFT,
        property: mockProperty,
        room: { ...mockRoom, id: "room-3", code: "P103", name: "Phòng 103" },
        members: [],
        start_date: "2026-03-01",
        end_date: "2027-03-01",
        rent_price: 3500000,
        deposit_amount: 7000000,
        billing_cycle: "monthly",
        due_day: "5",
        cutoff_day: "28",
        join_code: "DEF456",
        join_code_expires_at: "2026-04-01",
        created_by: mockUser,
        created_at: "2026-02-01",
        updated_at: "2026-02-01",
        deleted_at: null,
    },
];

// Hợp đồng đã xóa mềm
let deletedContracts: Contract[] = [
    {
        id: "contract-deleted-1",
        org_id: "org-1",
        status: ContractStatus.TERMINATED,
        property: mockProperty,
        room: { ...mockRoom, id: "room-4", code: "P201", name: "Phòng 201" },
        members: [],
        start_date: "2024-01-01",
        end_date: "2024-12-31",
        rent_price: 2000000,
        deposit_amount: 4000000,
        billing_cycle: "monthly",
        due_day: "5",
        cutoff_day: "28",
        join_code: "GHI321",
        join_code_expires_at: "2024-02-01",
        created_by: mockUser,
        created_at: "2024-01-01",
        updated_at: "2024-06-01",
        deleted_at: "2024-06-01",
    },
];

const delay = (ms: number) => new Promise((res) => setTimeout(res, ms));

// ───── API Functions ─────

export const getContracts = async (): Promise<Contract[]> => {
    await delay(300);
    return [...mockContracts];
};

export const getDeletedContracts = async (): Promise<Contract[]> => {
    await delay(300);
    return [...deletedContracts];
};

export const getContractById = async (id: string): Promise<Contract> => {
    await delay(300);
    const contract = mockContracts.find((c) => c.id === id);
    if (!contract) throw new Error("404");
    return { ...contract };
};

export const createContract = async (data: ContractFormValues): Promise<Contract> => {
    await delay(300);
    // Tìm người thuê chính từ danh sách mock
    const primaryUser = mockTenants.find((u) => u.id === data.primary_user_id) ?? mockUser;
    const contractId = crypto.randomUUID();
    const newContract: Contract = {
        id: contractId,
        org_id: "org-1",
        status: data.status,
        property: mockProperty,
        room: { ...mockRoom, id: data.room_id },
        members: [
            {
                id: crypto.randomUUID(),
                contract_id: contractId,
                user: primaryUser,
                role: "tenant",
                is_primary: "1",
                joined_at: data.start_date,
                left_at: null,
                created_at: new Date().toISOString(),
                updated_at: new Date().toISOString(),
            },
        ],
        start_date: data.start_date,
        end_date: data.end_date,
        rent_price: data.rent_price,
        deposit_amount: data.deposit_amount,
        billing_cycle: data.billing_cycle,
        due_day: data.due_day,
        cutoff_day: data.cutoff_day,
        join_code: "",
        join_code_expires_at: "",
        created_by: mockUser,
        created_at: new Date().toISOString(),
        updated_at: new Date().toISOString(),
        deleted_at: null,
    };
    mockContracts = [newContract, ...mockContracts];
    return newContract;
};

export const updateContract = async (id: string, data: ContractFormValues): Promise<Contract> => {
    await delay(300);
    const index = mockContracts.findIndex((c) => c.id === id);
    if (index === -1) throw new Error("404");
    const updated = {
        ...mockContracts[index],
        ...data,
        updated_at: new Date().toISOString(),
    };
    mockContracts[index] = updated;
    mockContracts = [...mockContracts];
    return updated;
};

// Xóa mềm (soft delete)
export const deleteContract = async (id: string): Promise<void> => {
    await delay(300);
    const contract = mockContracts.find((c) => c.id === id);
    if (!contract) throw new Error("404");
    const deleted = { ...contract, deleted_at: new Date().toISOString() };
    deletedContracts = [deleted, ...deletedContracts];
    mockContracts = mockContracts.filter((c) => c.id !== id);
};

// Khôi phục hợp đồng đã xóa
export const restoreContract = async (id: string): Promise<void> => {
    await delay(300);
    const contract = deletedContracts.find((c) => c.id === id);
    if (!contract) throw new Error("404");
    const restored = { ...contract, deleted_at: null };
    mockContracts = [restored, ...mockContracts];
    deletedContracts = deletedContracts.filter((c) => c.id !== id);
};

// Xóa vĩnh viễn
export const hardDeleteContract = async (id: string): Promise<void> => {
    await delay(300);
    deletedContracts = deletedContracts.filter((c) => c.id !== id);
};
