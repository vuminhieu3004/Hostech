import {
  ChartSpline,
  LayoutDashboard,
  PanelRightClose,
  PanelRightOpen,
} from "lucide-react";
import { useOpenStore } from "../../Stores/OpenStore";
import { Link } from "react-router";
import { useState } from "react";

const SidebarAdmin = () => {
  const { open, setOpen } = useOpenStore();
  const [action, setAction] = useState<string>(window.location.pathname);
  return (
    <div
      className={`h-200 duration-500 ease-in-out shadow ${
        open ? "w-[6%]" : "w-[18%]"
      }`}
    >
      <section className="relative">
        <div className="border-b border-gray-300 shadow-sm">
          <div className="flex items-center overflow-hidden">
            <img
              src="./images/logo_du_an.jpg"
              alt=""
              className="w-15 h-15 rounded-full m-4"
            />
            <p className="font-semibold text-blue-900 text-2xl">Hostech</p>
          </div>
          <div>
            {open ? (
              <PanelRightClose
                onClick={() => setOpen(!open)}
                className="absolute cursor-pointer text-blue-900 inset-y-9 -right-3 "
              />
            ) : (
              <PanelRightOpen
                onClick={() => setOpen(!open)}
                className="absolute cursor-pointer text-blue-900 inset-y-9 -right-3"
              />
            )}
          </div>
        </div>
        <div>
          <ul className="p-5 mt-5 flex flex-col gap-3">
            <Link
              to="/admin"
              onClick={() => setAction("admin")}
              className={`${
                action == "/admin"
                  ? "bg-blue-500 p-4 text-white font-bold rounded-2xl"
                  : "hover:bg-blue-300 rounded-2xl p-4 hover:text-white"
              }`}
            >
              {" "}
              <li className="flex items-center gap-2">
                {open ? (
                  <span title="Trang quản trị">
                    <LayoutDashboard className="pr-1" />
                  </span>
                ) : (
                  <>
                    <LayoutDashboard />
                    Trang quản trị
                  </>
                )}
              </li>
            </Link>
            <Link
              to="/admin/statistical"
              onClick={() => setAction("statistical")}
              className={`${
                action == "/admin/statistical"
                  ? "bg-blue-500 p-4 text-white font-bold rounded-2xl"
                  : "hover:bg-blue-300 rounded-2xl p-4 hover:text-white"
              }`}
            >
              {" "}
              <li className="flex items-center gap-2">
                {open ? (
                  <span title="Thống kê">
                    <ChartSpline className="pr-1" />
                  </span>
                ) : (
                  <>
                    <ChartSpline />
                    Thống kê
                  </>
                )}
              </li>
            </Link>
          </ul>
        </div>
      </section>
    </div>
  );
};

export default SidebarAdmin;
