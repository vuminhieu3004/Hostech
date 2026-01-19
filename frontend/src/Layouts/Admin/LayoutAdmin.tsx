import React from "react";
import Header from "./Header";
import { Outlet } from "react-router";
import SidebarAdmin from "./Sidebar";

const LayoutAdmin = () => {
  return (
    <>
      <div className="flex m-4 border border-gray-300 overflow-hidden rounded-2xl">
        <SidebarAdmin />
        <main className="w-full flex flex-col">
          <Header />
          <div className="m-4">
            <Outlet />
          </div>
        </main>
      </div>
    </>
  );
};

export default LayoutAdmin;
