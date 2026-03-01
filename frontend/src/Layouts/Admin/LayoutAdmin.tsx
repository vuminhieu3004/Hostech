import Header from "./Header";
import { Outlet } from "react-router";
import SidebarAdmin from "./Sidebar";
import Authorization from "../../Components/Auth/verifyLogin";
import { useTokenStore } from "../../Stores/AuthStore";

const LayoutAdmin = () => {
  const role = useTokenStore((state) => state.role);
  return (
    <>
      <Authorization role={role || ""} allowRole={["OWNER", "MANAGER"]}>
        <div className="flex m-4 border h-screen border-gray-300 overflow-hidden rounded-2xl">
          <SidebarAdmin />
          <main className="w-full flex flex-col overflow-scroll">
            <Header />
            <div className="ml-4 mr-4 mt-4">
              <Outlet />
            </div>
          </main>
        </div>
      </Authorization>
    </>
  );
};

export default LayoutAdmin;
