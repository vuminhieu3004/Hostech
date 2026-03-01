import type { Invoice, InvoiceFormValues, InvoiceItem, InvoiceItemFormValues } from "../Types/InvoiceTypes";
import { InvoiceStatus } from "../Types/InvoiceTypes";

// ───── Dữ liệu giả lập ─────
const mockUser = {
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
};

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

const mockContract = {
    id: "contract-1",
    org_id: "org-1",
    status: "ACTIVE",
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
};

let mockInvoices: Invoice[] = [
    {
        id: "inv-1",
        org_id: "org-1",
        status: InvoiceStatus.ISSUED,
        period_start: "2026-02-01",
        period_end: "2026-02-28",
        issue_date: "2026-02-01",
        due_date: "2026-02-05",
        total_amount: 3500000,
        paid_amount: 0,
        debt: 3500000,
        property: mockProperty,
        room: mockRoom,
        contract: mockContract,
        items: [
            {
                id: "item-1",
                type: "rent",
                service_id: "",
                description: "Tiền thuê phòng tháng 2/2026",
                quantity: 1,
                unit_price: 3000000,
                amount: 3000000,
                meta: "",
                created_at: "2026-02-01",
            },
            {
                id: "item-2",
                type: "service",
                service_id: "ELECTRIC",
                description: "Tiền điện (100 kWh × 3,500 VNĐ)",
                quantity: 100,
                unit_price: 3500,
                amount: 350000,
                meta: "",
                created_at: "2026-02-01",
            },
            {
                id: "item-3",
                type: "service",
                service_id: "INTERNET",
                description: "Tiền Internet",
                quantity: 1,
                unit_price: 150000,
                amount: 150000,
                meta: "",
                created_at: "2026-02-01",
            },
        ],
        created_by: mockUser,
        issued_by: mockUser,
        issued_at: "2026-02-01",
        cancelled_at: null,
        created_at: "2026-02-01",
        updated_at: "2026-02-01",
        deleted_at: null,
    },
    {
        id: "inv-2",
        org_id: "org-1",
        status: InvoiceStatus.PAID,
        period_start: "2026-01-01",
        period_end: "2026-01-31",
        issue_date: "2026-01-01",
        due_date: "2026-01-05",
        total_amount: 3200000,
        paid_amount: 3200000,
        debt: 0,
        property: mockProperty,
        room: { ...mockRoom, id: "room-2", code: "P102", name: "Phòng 102" },
        contract: { ...mockContract, id: "contract-2" },
        items: [
            {
                id: "item-4",
                type: "rent",
                service_id: "",
                description: "Tiền thuê phòng tháng 1/2026",
                quantity: 1,
                unit_price: 3000000,
                amount: 3000000,
                meta: "",
                created_at: "2026-01-01",
            },
            {
                id: "item-5",
                type: "service",
                service_id: "WATER",
                description: "Tiền nước",
                quantity: 2,
                unit_price: 100000,
                amount: 200000,
                meta: "",
                created_at: "2026-01-01",
            },
        ],
        created_by: mockUser,
        issued_by: mockUser,
        issued_at: "2026-01-01",
        cancelled_at: null,
        created_at: "2026-01-01",
        updated_at: "2026-01-15",
        deleted_at: null,
    },
    {
        id: "inv-3",
        org_id: "org-1",
        status: InvoiceStatus.DRAFT,
        period_start: "2026-03-01",
        period_end: "2026-03-31",
        issue_date: "2026-03-01",
        due_date: "2026-03-05",
        total_amount: 0,
        paid_amount: 0,
        debt: 0,
        property: mockProperty,
        room: { ...mockRoom, id: "room-3", code: "P103", name: "Phòng 103" },
        contract: { ...mockContract, id: "contract-3" },
        items: [],
        created_by: mockUser,
        issued_by: null,
        issued_at: null,
        cancelled_at: null,
        created_at: "2026-02-25",
        updated_at: "2026-02-25",
        deleted_at: null,
    },
];

// Hóa đơn đã xóa mềm
let deletedInvoices: Invoice[] = [
    {
        id: "inv-deleted-1",
        org_id: "org-1",
        status: InvoiceStatus.CANCELLED,
        period_start: "2025-12-01",
        period_end: "2025-12-31",
        issue_date: "2025-12-01",
        due_date: "2025-12-05",
        total_amount: 3000000,
        paid_amount: 0,
        debt: 3000000,
        property: mockProperty,
        room: { ...mockRoom, id: "room-4", code: "P201", name: "Phòng 201" },
        contract: { ...mockContract, id: "contract-4" },
        items: [],
        created_by: mockUser,
        issued_by: null,
        issued_at: null,
        cancelled_at: "2025-12-10",
        created_at: "2025-12-01",
        updated_at: "2025-12-10",
        deleted_at: "2025-12-10",
    },
];

// Danh sách hợp đồng giả lập để chọn khi tạo hóa đơn
export const mockContractsForInvoice = [
    { id: "contract-1", label: "HĐ-001 – Phòng 101 – Nguyễn Văn A" },
    { id: "contract-2", label: "HĐ-002 – Phòng 102 – Trần Thị B" },
    { id: "contract-3", label: "HĐ-003 – Phòng 103 – Lê Minh C" },
];

const delay = (ms: number) => new Promise((res) => setTimeout(res, ms));

// ───── API Functions ─────

export const getInvoices = async (): Promise<Invoice[]> => {
    await delay(300);
    return [...mockInvoices];
};

export const getDeletedInvoices = async (): Promise<Invoice[]> => {
    await delay(300);
    return [...deletedInvoices];
};

export const getInvoiceById = async (id: string): Promise<Invoice> => {
    await delay(300);
    const invoice = mockInvoices.find((i) => i.id === id);
    if (!invoice) throw new Error("404");
    return { ...invoice, items: [...invoice.items] };
};

export const createInvoice = async (data: InvoiceFormValues): Promise<Invoice> => {
    await delay(300);
    const contract = mockContractsForInvoice.find((c) => c.id === data.contract_id);
    const newInvoice: Invoice = {
        id: crypto.randomUUID(),
        org_id: "org-1",
        status: data.status,
        period_start: data.period_start,
        period_end: data.period_end,
        issue_date: data.issue_date,
        due_date: data.due_date,
        total_amount: 0,
        paid_amount: 0,
        debt: 0,
        property: mockProperty,
        room: { ...mockRoom, name: contract?.label.split("–")[1]?.trim() ?? "Phòng" },
        contract: { ...mockContract, id: data.contract_id },
        items: [],
        created_by: mockUser,
        issued_by: null,
        issued_at: null,
        cancelled_at: null,
        created_at: new Date().toISOString(),
        updated_at: new Date().toISOString(),
        deleted_at: null,
    };
    mockInvoices = [newInvoice, ...mockInvoices];
    return newInvoice;
};

export const updateInvoice = async (id: string, data: InvoiceFormValues): Promise<Invoice> => {
    await delay(300);
    const index = mockInvoices.findIndex((i) => i.id === id);
    if (index === -1) throw new Error("404");

    const current = mockInvoices[index];

    // Tính lại paid_amount và debt theo trạng thái mới
    let paidAmount = current.paid_amount;
    let debt = current.debt;

    if (data.status === "PAID") {
        // Đã thanh toán: xóa nợ
        paidAmount = current.total_amount;
        debt = 0;
    } else if (data.status === "CANCELLED") {
        // Huỷ: không còn nợ
        debt = 0;
    } else {
        // Các trạng thái khác: giữ paid_amount hiện tại, tính lại debt
        debt = current.total_amount - paidAmount;
    }

    const updated = {
        ...current,
        ...data,
        paid_amount: paidAmount,
        debt,
        updated_at: new Date().toISOString(),
    };
    mockInvoices[index] = updated;
    mockInvoices = [...mockInvoices];
    return updated;
};

// Xóa mềm
export const deleteInvoice = async (id: string): Promise<void> => {
    await delay(300);
    const invoice = mockInvoices.find((i) => i.id === id);
    if (!invoice) throw new Error("404");
    deletedInvoices = [{ ...invoice, deleted_at: new Date().toISOString() }, ...deletedInvoices];
    mockInvoices = mockInvoices.filter((i) => i.id !== id);
};

// Khôi phục
export const restoreInvoice = async (id: string): Promise<void> => {
    await delay(300);
    const invoice = deletedInvoices.find((i) => i.id === id);
    if (!invoice) throw new Error("404");
    mockInvoices = [{ ...invoice, deleted_at: null }, ...mockInvoices];
    deletedInvoices = deletedInvoices.filter((i) => i.id !== id);
};

// Xóa vĩnh viễn
export const hardDeleteInvoice = async (id: string): Promise<void> => {
    await delay(300);
    deletedInvoices = deletedInvoices.filter((i) => i.id !== id);
};

// Thêm dòng chi phí
export const addInvoiceItem = async (invoiceId: string, data: InvoiceItemFormValues): Promise<InvoiceItem> => {
    await delay(300);
    const index = mockInvoices.findIndex((i) => i.id === invoiceId);
    if (index === -1) throw new Error("404");
    const newItem: InvoiceItem = {
        id: crypto.randomUUID(),
        type: data.type,
        service_id: data.service_id,
        description: data.description,
        quantity: data.quantity,
        unit_price: data.unit_price,
        amount: data.quantity * data.unit_price,
        meta: "",
        created_at: new Date().toISOString(),
    };
    const updatedItems = [...mockInvoices[index].items, newItem];
    const totalAmount = updatedItems.reduce((sum, item) => sum + item.amount, 0);
    mockInvoices[index] = {
        ...mockInvoices[index],
        items: updatedItems,
        total_amount: totalAmount,
        debt: totalAmount - mockInvoices[index].paid_amount,
        updated_at: new Date().toISOString(),
    };
    mockInvoices = [...mockInvoices];
    return newItem;
};

// Xóa dòng chi phí
export const removeInvoiceItem = async (invoiceId: string, itemId: string): Promise<void> => {
    await delay(300);
    const index = mockInvoices.findIndex((i) => i.id === invoiceId);
    if (index === -1) throw new Error("404");
    const updatedItems = mockInvoices[index].items.filter((item) => item.id !== itemId);
    const totalAmount = updatedItems.reduce((sum, item) => sum + item.amount, 0);
    mockInvoices[index] = {
        ...mockInvoices[index],
        items: updatedItems,
        total_amount: totalAmount,
        debt: totalAmount - mockInvoices[index].paid_amount,
        updated_at: new Date().toISOString(),
    };
    mockInvoices = [...mockInvoices];
};
