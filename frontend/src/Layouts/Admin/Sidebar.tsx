import {
  ChartSpline,
  ChevronDown,
  ChevronRight,
  House,
  LayoutDashboard,
  MapPinCheck,
  MapPinHouse,
  PanelRightClose,
  PanelRightOpen,
  StretchHorizontal,
  Warehouse,
} from "lucide-react";
import { useOpenMenu, useOpenStore } from "../../Stores/OpenStore";
import { Link } from "react-router";
import { useState } from "react";
import { useLocation } from "react-router";

const SidebarAdmin = () => {
  const { open, setOpen } = useOpenStore();
  const {
    openMenu1,
    openMenu2,
    openMenu3,
    setOpenMenu1,
    setOpenMenu2,
    setOpenMenu3,
  } = useOpenMenu();
  const location = useLocation();
  const [action, setAction] = useState<string>(location.pathname);
  return (
    <div
      className={`h-auto duration-500 ease-in-out shadow ${
        open ? "w-[6%]" : "w-[20%]"
      }`}
    >
      <section className="relative">
        <div className="border-b border-gray-300 shadow-sm">
          <div className="flex items-center overflow-hidden">
            <img
              src="/images/logo_du_an.jpg"
              alt=""
              className="w-13 h-13 rounded-full m-4 "
            />
            <p
              className={`font-semibold text-blue-900 text-2xl ${open ? "pl-2" : ""}`}
            >
              Hostech
            </p>
          </div>
          <div>
            {open ? (
              <PanelRightClose
                onClick={() => setOpen(!open)}
                className="absolute cursor-pointer text-blue-900 inset-y-9 -right-3"
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
          <ul className="ul p-5 pt-4 flex flex-col gap-2 max-h-150 overflow-scroll">
            <Link
              to="/admin"
              onClick={() => setAction("/admin")}
              className={`${
                action == "/admin"
                  ? "bg-blue-500 p-4 text-white font-bold shadow-sm shadow-gray-300 rounded-2xl"
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
              onClick={() => setAction("/admin/statistical")}
              className={`${
                action == "/admin/statistical"
                  ? "bg-blue-500 p-4 text-white font-bold shadow-sm shadow-gray-300 rounded-2xl"
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
            <ul className="w-full flex flex-col gap-3  border border-gray-300 p-1 rounded-2xl shadow-sm shadow-gray-400">
              {open === false ? (
                <li>
                  <div
                    className="flex items-center gap-2 cursor-pointer p-2.5 p-2.5"
                    onClick={() => setOpenMenu1(!openMenu1)}
                  >
                    <h2 className="font-semibold">Quản lý khu nhà</h2>
                    {openMenu1 ? (
                      <ChevronDown className="w-4.5" />
                    ) : (
                      <ChevronRight className="w-4.5" />
                    )}
                  </div>
                </li>
              ) : (
                <div
                  title="Quản lý khu nhà"
                  onClick={() => setOpenMenu1(!openMenu1)}
                >
                  <MapPinHouse className="cursor-pointer ml-3" />
                </div>
              )}
              {openMenu1 && (
                <ul
                  className={`flex flex-col justify-center gap-1 border p-2 pl-3.5 border-gray-300 inset-shadow-sm inset-shadow-gray-300 overflow-hidden rounded-2xl ${
                    open == true ? "items-center" : ""
                  }`}
                >
                  <li>
                    <Link
                      to="/admin/properties"
                      onClick={() => setAction("/admin/properties")}
                    >
                      {" "}
                      <div
                        className={`flex items-center gap-2  ${
                          action == "/admin/properties"
                            ? "bg-blue-500 p-4 text-white shadow-sm shadow-gray-300 font-bold rounded-2xl"
                            : "hover:bg-blue-300 rounded-2xl p-4 hover:text-white"
                        }`}
                      >
                        {open ? (
                          <span title="Quản lý khu nhà">
                            <House className="pr-1" />
                          </span>
                        ) : (
                          <>
                            <House />
                            Quản lý nhà
                          </>
                        )}
                      </div>
                    </Link>
                  </li>
                  <li>
                    <Link
                      to="/admin/zones"
                      onClick={() => setAction("/admin/zones")}
                    >
                      {" "}
                      <div
                        className={`flex items-center gap-2 ${
                          action == "/admin/zones"
                            ? "bg-blue-500 p-4 text-white font-bold shadow-sm shadow-gray-300 rounded-2xl"
                            : "hover:bg-blue-300 rounded-2xl p-4 hover:text-white"
                        }`}
                      >
                        {open ? (
                          <span title="Quản lý khu">
                            <MapPinCheck className="pr-1" />
                          </span>
                        ) : (
                          <>
                            <MapPinCheck />
                            Quản lý khu
                          </>
                        )}
                      </div>
                    </Link>
                  </li>
                  <li>
                    <Link
                      to="/admin/floors"
                      onClick={() => setAction("/admin/floors")}
                    >
                      {" "}
                      <div
                        className={`flex items-center gap-2 ${
                          action == "/admin/floors"
                            ? "bg-blue-500 p-4 text-white font-bold shadow-sm shadow-gray-300 rounded-2xl"
                            : "hover:bg-blue-300 rounded-2xl p-4 hover:text-white"
                        }`}
                      >
                        {open ? (
                          <span title="Quản lý tầng">
                            <StretchHorizontal className="pr-1" />
                          </span>
                        ) : (
                          <>
                            <StretchHorizontal />
                            Quản lý tầng
                          </>
                        )}
                      </div>
                    </Link>
                  </li>
                  <li>
                    <Link
                      to="/admin/rooms"
                      onClick={() => setAction("/admin/rooms")}
                    >
                      {" "}
                      <div
                        className={`flex items-center gap-2 ${
                          action == "/admin/rooms"
                            ? "bg-blue-500 p-4 text-white font-bold shadow-sm shadow-gray-300 rounded-2xl"
                            : "hover:bg-blue-300 rounded-2xl p-4 hover:text-white"
                        }`}
                      >
                        {open ? (
                          <span title="Quản lý phòng">
                            <Warehouse className="pr-1" />
                          </span>
                        ) : (
                          <>
                            <Warehouse />
                            Quản lý phòng
                          </>
                        )}
                      </div>
                    </Link>
                  </li>
                </ul>
              )}
            </ul>
            <ul className="w-full flex flex-col gap-3 border border-gray-300 p-1 rounded-2xl shadow-sm shadow-gray-400">
              {open === false ? (
                <li>
                  <div
                    className="flex items-center gap-2 cursor-pointer p-2.5"
                    onClick={() => setOpenMenu2(!openMenu2)}
                  >
                    <h2 className="font-semibold">Quản lý khu nhà</h2>
                    {openMenu2 ? (
                      <ChevronDown className="w-4.5" />
                    ) : (
                      <ChevronRight className="w-4.5" />
                    )}
                  </div>
                </li>
              ) : (
                <div
                  title="Quản lý khu nhà"
                  onClick={() => setOpenMenu2(!openMenu2)}
                >
                  <MapPinHouse className="cursor-pointer ml-3" />
                </div>
              )}
              {openMenu2 && (
                <ul
                  className={`flex flex-col justify-center gap-1 border p-2 pl-3.5 border-gray-300 inset-shadow-sm inset-shadow-gray-300 overflow-hidden rounded-2xl ${
                    open == true ? "items-center" : ""
                  }`}
                >
                  <li>
                    <Link
                      to="/admin/properties"
                      onClick={() => setAction("/admin/properties")}
                    >
                      {" "}
                      <div
                        className={`flex items-center gap-2 ${
                          action == "/admin/properties"
                            ? "bg-blue-500 p-4 text-white shadow-sm shadow-gray-300 font-bold rounded-2xl"
                            : "hover:bg-blue-300 rounded-2xl p-4 hover:text-white"
                        }`}
                      >
                        {open ? (
                          <span title="Quản lý khu nhà">
                            <House className="pr-1" />
                          </span>
                        ) : (
                          <>
                            <House />
                            Quản lý nhà
                          </>
                        )}
                      </div>
                    </Link>
                  </li>
                  <li>
                    <Link
                      to="/admin/zones"
                      onClick={() => setAction("/admin/zones")}
                    >
                      {" "}
                      <div
                        className={`flex items-center gap-2 ${
                          action == "/admin/zones"
                            ? "bg-blue-500 p-4 text-white font-bold shadow-sm shadow-gray-300 rounded-2xl"
                            : "hover:bg-blue-300 rounded-2xl p-4 hover:text-white"
                        }`}
                      >
                        {open ? (
                          <span title="Quản lý khu">
                            <MapPinCheck className="pr-1" />
                          </span>
                        ) : (
                          <>
                            <MapPinCheck />
                            Quản lý khu
                          </>
                        )}
                      </div>
                    </Link>
                  </li>
                  <li>
                    <Link
                      to="/admin/floors"
                      onClick={() => setAction("/admin/floors")}
                    >
                      {" "}
                      <div
                        className={`flex items-center gap-2 ${
                          action == "/admin/floors"
                            ? "bg-blue-500 p-4 text-white font-bold shadow-sm shadow-gray-300 rounded-2xl"
                            : "hover:bg-blue-300 rounded-2xl p-4 hover:text-white"
                        }`}
                      >
                        {open ? (
                          <span title="Quản lý tầng">
                            <StretchHorizontal className="pr-1" />
                          </span>
                        ) : (
                          <>
                            <StretchHorizontal />
                            Quản lý tầng
                          </>
                        )}
                      </div>
                    </Link>
                  </li>
                  <li>
                    <Link
                      to="/admin/rooms"
                      onClick={() => setAction("/admin/rooms")}
                    >
                      {" "}
                      <div
                        className={`flex items-center gap-2 ${
                          action == "/admin/rooms"
                            ? "bg-blue-500 p-4 text-white font-bold shadow-sm shadow-gray-300 rounded-2xl"
                            : "hover:bg-blue-300 rounded-2xl p-4 hover:text-white"
                        }`}
                      >
                        {open ? (
                          <span title="Quản lý phòng">
                            <Warehouse className="pr-1" />
                          </span>
                        ) : (
                          <>
                            <Warehouse />
                            Quản lý phòng
                          </>
                        )}
                      </div>
                    </Link>
                  </li>
                </ul>
              )}
            </ul>
            <ul className="w-full flex flex-col gap-3 border border-gray-300 p-1 rounded-2xl shadow-sm shadow-gray-400">
              {open === false ? (
                <li>
                  <div
                    className="flex items-center gap-2 cursor-pointer p-2.5"
                    onClick={() => setOpenMenu3(!openMenu3)}
                  >
                    <h2 className="font-semibold">Quản lý khu nhà</h2>
                    {openMenu1 ? (
                      <ChevronDown className="w-4.5" />
                    ) : (
                      <ChevronRight className="w-4.5" />
                    )}
                  </div>
                </li>
              ) : (
                <div
                  title="Quản lý khu nhà"
                  onClick={() => setOpenMenu3(!openMenu3)}
                >
                  <MapPinHouse className="cursor-pointer ml-3" />
                </div>
              )}
              {openMenu3 && (
                <ul
                  className={`flex flex-col justify-center gap-1 border p-2 pl-3.5 border-gray-300 inset-shadow-sm inset-shadow-gray-300 overflow-hidden rounded-2xl ${
                    open == true ? "items-center" : ""
                  }`}
                >
                  <li>
                    <Link
                      to="/admin/properties"
                      onClick={() => setAction("/admin/properties")}
                    >
                      {" "}
                      <div
                        className={`flex items-center gap-2 ${
                          action == "/admin/properties"
                            ? "bg-blue-500 p-4 text-white shadow-sm shadow-gray-300 font-bold rounded-2xl"
                            : "hover:bg-blue-300 rounded-2xl p-4 hover:text-white"
                        }`}
                      >
                        {open ? (
                          <span title="Quản lý khu nhà">
                            <House className="pr-1" />
                          </span>
                        ) : (
                          <>
                            <House />
                            Quản lý nhà
                          </>
                        )}
                      </div>
                    </Link>
                  </li>
                  <li>
                    <Link
                      to="/admin/zones"
                      onClick={() => setAction("/admin/zones")}
                    >
                      {" "}
                      <div
                        className={`flex items-center gap-2 ${
                          action == "/admin/zones"
                            ? "bg-blue-500 p-4 text-white font-bold shadow-sm shadow-gray-300 rounded-2xl"
                            : "hover:bg-blue-300 rounded-2xl p-4 hover:text-white"
                        }`}
                      >
                        {open ? (
                          <span title="Quản lý khu">
                            <MapPinCheck className="pr-1" />
                          </span>
                        ) : (
                          <>
                            <MapPinCheck />
                            Quản lý khu
                          </>
                        )}
                      </div>
                    </Link>
                  </li>
                  <li>
                    <Link
                      to="/admin/floors"
                      onClick={() => setAction("/admin/floors")}
                    >
                      {" "}
                      <div
                        className={`flex items-center gap-2 ${
                          action == "/admin/floors"
                            ? "bg-blue-500 p-4 text-white font-bold shadow-sm shadow-gray-300 rounded-2xl"
                            : "hover:bg-blue-300 rounded-2xl p-4 hover:text-white"
                        }`}
                      >
                        {open ? (
                          <span title="Quản lý tầng">
                            <StretchHorizontal className="pr-1" />
                          </span>
                        ) : (
                          <>
                            <StretchHorizontal />
                            Quản lý tầng
                          </>
                        )}
                      </div>
                    </Link>
                  </li>
                  <li>
                    <Link
                      to="/admin/rooms"
                      onClick={() => setAction("/admin/rooms")}
                    >
                      {" "}
                      <div
                        className={`flex items-center gap-2 ${
                          action == "/admin/rooms"
                            ? "bg-blue-500 p-4 text-white font-bold shadow-sm shadow-gray-300 rounded-2xl"
                            : "hover:bg-blue-300 rounded-2xl p-4 hover:text-white"
                        }`}
                      >
                        {open ? (
                          <span title="Quản lý phòng">
                            <Warehouse className="pr-1" />
                          </span>
                        ) : (
                          <>
                            <Warehouse />
                            Quản lý phòng
                          </>
                        )}
                      </div>
                    </Link>
                  </li>
                </ul>
              )}
            </ul>
          </ul>
        </div>
      </section>
    </div>
  );
};

export default SidebarAdmin;
