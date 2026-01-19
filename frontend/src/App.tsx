import { useRoutes } from "react-router";
import AuthPage from "./Pages/Client/Auth";
import LayoutClient from "./Layouts/Client/LayoutClient";
import LayoutAdmin from "./Layouts/Admin/LayoutAdmin";
import Dashboard from "./Pages/Admin/Dashboard";

function App() {
  const router = useRoutes([
    { path: "/", Component: LayoutClient },
    { path: "/auth", Component: AuthPage },
    {
      path: "admin",
      Component: LayoutAdmin,
      children: [{ path: "/admin", Component: Dashboard }],
    },
  ]);
  return router;
}

export default App;
