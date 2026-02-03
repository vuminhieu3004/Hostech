import { useRoutes } from "react-router";
import AuthPage from "./Pages/Client/Login";
import { Navigate } from "react-router";
import LayoutAdmin from "./Layouts/Admin/LayoutAdmin";
import Dashboard from "./Pages/Admin/Dashboard";
import Statistical from "./Pages/Admin/Statistical";
import Properties from "./Pages/Admin/Properties/Properties";
import Zones from "./Pages/Admin/Properties/Zones";
import Floors from "./Pages/Admin/Properties/Floors";
import Rooms from "./Pages/Admin/Properties/Rooms";
import VerifyOTP from "./Pages/Client/VerifyOTP";
import Notfound from "./Pages/Client/404";

function App() {
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
        { path: "properties", Component: Properties },
        { path: "zones", Component: Zones },
        { path: "floors", Component: Floors },
        { path: "rooms", Component: Rooms },
      ],
    },

    { path: "*", Component: Notfound },
  ]);
  return router;
}

export default App;
