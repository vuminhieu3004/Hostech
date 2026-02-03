import type { TGlobalProp } from "../../Types/ReactType";
import { Navigate } from "react-router";
import { message } from "antd";

const Authorization = ({
  children,
  allowRole,
  role,
}: TGlobalProp<{ role: string; allowRole: string[] }>) => {
  if (!allowRole.includes(role)) {
    message.error("Bạn ko có quyền vào chức năng này!");
    return <Navigate to={"/"} replace />;
  }

  return children;
};

export default Authorization;
