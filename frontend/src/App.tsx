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
    { path: "/auth", Component: AuthPage },
    { path: "/otp/verify", Component: VerifyOTP },

    {
      path: "admin",
      Component: LayoutAdmin,
      children: [
        { path: "/admin", Component: Dashboard },
        { path: "statistical", Component: Statistical },
        {
          path: "properties",
          Component: Properties,
          children: [
            { path: "createProperty", Component: CreateProperty },
            { path: "editProperty/:id", Component: EditProperty },
          ],
        },
        {
          path: "orgs",
          Component: Orgs,
          children: [
            { path: "createOrg", Component: CreateOrg },
            { path: "editOrg/:id", Component: EditOrg },
          ],
        },
        {
          path: "floors",
          Component: Floors,
          children: [
            { path: "createFloor", Component: CreateFloor },
            { path: "editFloor/:id", Component: EditFloor },
          ],
        },
        {
          path: "rooms",
          Component: Rooms,
          children: [
            { path: "createRoom", Component: CreateRoom },
            { path: "editRoom/:id", Component: EditRoom },
          ],
        },
      ],
    },

    { path: "*", Component: Notfound },
  ]);
  return router;
}

export default App;
