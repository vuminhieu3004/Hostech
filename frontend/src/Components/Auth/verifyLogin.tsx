import React from "react";
import type { TGlobalProp } from "../../Types/ReactType";
import { useNavigate } from "react-router";

const verifyLogin = ({ children, actor }: TGlobalProp<{ actor: string }>) => {
  const nav = useNavigate();
  if (!actor) {
    return nav("/auth", { replace: true });
  }
  return <div>{children}</div>;
};

export default verifyLogin;
