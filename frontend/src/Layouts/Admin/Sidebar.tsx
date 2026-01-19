import { LayoutDashboard, PanelRightClose, PanelRightOpen } from "lucide-react";
import { useOpenStore } from "../../Stores/OpenStore";
import { Link } from "react-router";

const SidebarAdmin = () => {
  const { open, setOpen } = useOpenStore();
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
                className="absolute cursor-pointer inset-y-9 -right-3 "
              />
            ) : (
              <PanelRightOpen
                onClick={() => setOpen(!open)}
                className="absolute cursor-pointer inset-y-9 -right-3"
              />
            )}
          </div>
        </div>
        <div>
          <ul className="p-5 mt-5">
            <Link to="/admin">
              {" "}
              <li className="flex items-center gap-2">
                {open ? (
                  <LayoutDashboard className="ml-3" />
                ) : (
                  <>
                    <LayoutDashboard />
                    Trang quản trị
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
