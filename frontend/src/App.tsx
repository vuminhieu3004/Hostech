import { useRoutes } from "react-router";
import { useEffect } from "react";
import AuthPage from "./Pages/Client/Login";
import { Navigate } from "react-router";
import LayoutAdmin from "./Layouts/Admin/LayoutAdmin";
import Dashboard from "./Pages/Admin/Dashboard";
import Statistical from "./Pages/Admin/Statistical";

import Floors from "./Pages/Admin/Properties/Floors/Floors";

import VerifyOTP from "./Pages/Client/VerifyOTP";
import Notfound from "./Pages/Client/404";
import { useTokenStore } from "./Stores/AuthStore";
import Properties from "./Pages/Admin/Properties/Property/Properties";
import Rooms from "./Pages/Admin/Properties/Rooms/Rooms";
import CreateProperty from "./Pages/Admin/Properties/Property/Create";
import Orgs from "./Pages/Admin/Properties/Orgs/Orgs";
import CreateOrg from "./Pages/Admin/Properties/Orgs/create";
import CreateFloor from "./Pages/Admin/Properties/Floors/create";
import CreateRoom from "./Pages/Admin/Properties/Rooms/create";
import EditProperty from "./Pages/Admin/Properties/Property/edit";
import EditOrg from "./Pages/Admin/Properties/Orgs/edit";
import EditFloor from "./Pages/Admin/Properties/Floors/edit";
import EditRoom from "./Pages/Admin/Properties/Rooms/edit";
import DetailProperty from "./Pages/Admin/Properties/Property/detail";
import DetailOrg from "./Pages/Admin/Properties/Orgs/detail";
import DetailFloor from "./Pages/Admin/Properties/Floors/detail";
import DetailRoom from "./Pages/Admin/Properties/Rooms/detail";
import Meters from "./Pages/Admin/Properties/Meters/Meters";
import CreateMeter from "./Pages/Admin/Properties/Meters/create";
import EditMeter from "./Pages/Admin/Properties/Meters/edit";
import DetailMeter from "./Pages/Admin/Properties/Meters/detail";
import Tenant from "./Pages/Admin/UserManager/Tenants/Tenant";
import DetailTenant from "./Pages/Admin/UserManager/Tenants/detail";
import Manager from "./Pages/Admin/UserManager/Manager/Manager";
import DetailManager from "./Pages/Admin/UserManager/Manager/detail";
import Staff from "./Pages/Admin/UserManager/Staff/Staff";
import DetailStaff from "./Pages/Admin/UserManager/Staff/detail";
import Services from "./Pages/Admin/Services/Services";
import CreateService from "./Pages/Admin/Services/Create";
import EditService from "./Pages/Admin/Services/Edit";
import Contracts from "./Pages/Admin/Contracts/Contracts";
import CreateContract from "./Pages/Admin/Contracts/Create";
import EditContract from "./Pages/Admin/Contracts/Edit";
import ContractDetail from "./Pages/Admin/Contracts/Detail";
import DeletedContracts from "./Pages/Admin/Contracts/DeletedContracts";
import Invoices from "./Pages/Admin/Invoices/Invoices";
import CreateInvoice from "./Pages/Admin/Invoices/Create";
import EditInvoice from "./Pages/Admin/Invoices/Edit";
import InvoiceDetail from "./Pages/Admin/Invoices/Detail";
import DeletedInvoices from "./Pages/Admin/Invoices/DeletedInvoices";

function App() {
  const restoreToken = useTokenStore((state) => state.restoreToken);
  const isLoading = useTokenStore((state) => state.isLoading);

  useEffect(() => {
    restoreToken();
  }, []);

  if (isLoading) {
    return <div>Đang tải...</div>;
  }

  const router = useRoutes([
    { path: "/", element: <Navigate to={"/auth"} replace /> },
    // Router Quản lý Đăng nhập và xác thực
    { path: "/auth", Component: AuthPage },
    { path: "/otp/verify", Component: VerifyOTP },

    {
      path: "admin",
      Component: LayoutAdmin,
      children: [
        // Router Quản lý Tổ chức, Nhà, Tầng, Phòng
        { path: "/admin", Component: Dashboard },
        { path: "statistical", Component: Statistical },
        {
          path: "properties",
          Component: Properties,
          children: [
            { path: "createProperty", Component: CreateProperty },
            { path: "editProperty/:id", Component: EditProperty },
            { path: "detailProperty/:id", Component: DetailProperty },
          ],
        },
        {
          path: "orgs",
          Component: Orgs,
          children: [
            { path: "createOrg", Component: CreateOrg },
            { path: "editOrg/:id", Component: EditOrg },
            { path: "detailOrg/:id", Component: DetailOrg },
          ],
        },
        {
          path: "floors",
          Component: Floors,
          children: [
            { path: "createFloor", Component: CreateFloor },
            { path: "editFloor/:id", Component: EditFloor },
            { path: "detailFloor/:id", Component: DetailFloor },
          ],
        },
        {
          path: "rooms",
          Component: Rooms,
          children: [
            { path: "createRoom", Component: CreateRoom },
            { path: "editRoom/:id", Component: EditRoom },
            { path: "detailRoom/:id", Component: DetailRoom },
          ],
        },

        //Router quản lý meters
        {
          path: "meters",
          Component: Meters,
          children: [
            { path: "createMeter", Component: CreateMeter },
            { path: "editMeter/:id", Component: EditMeter },
            { path: "detailMeter/:id", Component: DetailMeter },
          ],
        },
        //Router Quản lý người dùng
        {
          path: "tenant",
          Component: Tenant,
          children: [{ path: "detailTenant/:id", Component: DetailTenant }],
        },
        {
          path: "manager",
          Component: Manager,
          children: [{ path: "detailManager/:id", Component: DetailManager }],
        },
        {
          path: "staff",
          Component: Staff,
          children: [{ path: "detailStaff/:id", Component: DetailStaff }],
        },

        //Router Quản lý dịch vụ
        {
          path: "services",
          Component: Services,
          children: [
            { path: "createService", Component: CreateService },
            { path: "editService/:id", Component: EditService },
          ],
        },

        //Router Quản lý hợp đồng
        {
          path: "contracts",
          Component: Contracts,
          children: [
            { path: "create", Component: CreateContract },
            { path: "edit/:id", Component: EditContract },
            { path: "detail/:id", Component: ContractDetail },
            { path: "deleted", Component: DeletedContracts },
          ],
        },

        //Router Quản lý hóa đơn
        {
          path: "invoices",
          Component: Invoices,
          children: [
            { path: "create", Component: CreateInvoice },
            { path: "edit/:id", Component: EditInvoice },
            { path: "detail/:id", Component: InvoiceDetail },
            { path: "deleted", Component: DeletedInvoices },
          ],
        },
      ],
    },

    { path: "*", Component: Notfound },
  ]);
  return router;
}

export default App;
