import React, { useEffect, useState, type ReactElement } from "react";
import { useOpenStore } from "../../Stores/OpenStore";
import { Eye, EyeClosed } from "lucide-react";
import type { TGlobalProp } from "../../Types/ReactType";
import { useMeStore } from "../../Stores/AuthStore";

const Register = ({ children }: TGlobalProp<{ open: boolean }>) => {
  const { me } = useMeStore();
  const { openRegister, eyePassword, setOpenRegister, setEyePassword } =
    useOpenStore();

  return (
    <>
      {React.cloneElement(
        children as ReactElement,
        {
          onClick: () => {
            setOpenRegister(true);
          },
        } as { onClick: () => void },
      )}

      <div
        className={`fixed w-screen h-screen top-0 left-0 bg-black/40 duration-500 z-10 ${openRegister ? "opacity-100 visited" : "opacity-0 invisible"}`}
        onClick={() => setOpenRegister(!open)}
      ></div>
      {openRegister && (
        <section
          className={`fixed top-[3%] left-[32%] z-50 w-150
    transition-transform duration-500 ease-in-out
  `}
        >
          <div
            className={`flex flex-col justify-center bg-white border shadow-2xl shadow-blue-500 border-gray-300
             rounded-[10px] items-start p-10 transition-all duration-500 ease-in-out `}
          >
            <h2 className="w-full text-start text-2xl font-semibold pb-7">
              Đăng ký
            </h2>
            <div className="w-full flex flex-col gap-2">
              <div className="flex flex-col w-full">
                <label htmlFor="" className="p-1 pl-3 ">
                  Tài khoản(*)
                </label>
                <input
                  type="email"
                  placeholder="Nhập email đăng ký..."
                  className="w-full border border-gray-400 rounded-[10px] focus:outline-none p-2"
                />
              </div>
              <div className="flex flex-col w-full">
                <label htmlFor="" className="p-1 pl-3 ">
                  Email(*)
                </label>
                <input
                  type="email"
                  placeholder="Nhập email đăng ký..."
                  className="w-full border border-gray-400 rounded-[10px] focus:outline-none p-2"
                />
              </div>
              <div className="flex flex-col w-full">
                <label htmlFor="" className="p-1 pl-3 ">
                  Số điện thoại(*)
                </label>
                <input
                  type="email"
                  placeholder="Nhập email đăng ký..."
                  className="w-full border border-gray-400 rounded-[10px] focus:outline-none p-2"
                />
              </div>
              <div className="relative flex flex-col w-full">
                <label htmlFor="" className="p-1 pl-3">
                  Mật khẩu(*)
                </label>
                <input
                  type={eyePassword ? "text" : "password"}
                  placeholder="Nhập mật khẩu đăng ký..."
                  className="w-full border border-gray-400 rounded-[10px] focus:outline-none p-2"
                />
                <div className="absolute inset-y-10 right-4">
                  {eyePassword ? (
                    <Eye
                      onClick={() => setEyePassword(false)}
                      className="cursor-pointer"
                    />
                  ) : (
                    <EyeClosed
                      onClick={() => setEyePassword(true)}
                      className="cursor-pointer"
                    />
                  )}
                </div>
              </div>
              <div className="relative flex flex-col w-full">
                <label htmlFor="" className="p-1 pl-3">
                  Nhập lại mật khẩu(*)
                </label>
                <input
                  type={eyePassword ? "text" : "password"}
                  placeholder="Nhập lại mật khẩu đăng ký..."
                  className="w-full border border-gray-400 rounded-[10px] focus:outline-none p-2"
                />
                <div className="absolute inset-y-10 right-4">
                  {eyePassword ? (
                    <Eye
                      onClick={() => setEyePassword(false)}
                      className="cursor-pointer"
                    />
                  ) : (
                    <EyeClosed
                      onClick={() => setEyePassword(true)}
                      className="cursor-pointer"
                    />
                  )}
                </div>
              </div>
              <div className="mt-2 flex flex-col gap-2 items-center">
                <button className="w-full cursor-pointer flex items-center justify-center hover:font-semibold border border-gray-300 p-2 rounded-[10px]">
                  Đăng ký bằng google
                  <img
                    src="/images/Auth/gg.png"
                    alt="google"
                    className="w-10 rounded-full"
                  />
                </button>
              </div>
              <div className="flex justify-end items-center gap-2 mt-3">
                <button
                  type="button"
                  className="w-35 p-2 bg-red-500 rounded-[10px] cursor-pointer text-white hover:font-semibold hover:bg-red-600"
                >
                  Đăng ký
                </button>

                <button
                  type="button"
                  onClick={() => setOpenRegister(false)}
                  className=" border border-gray-300 p-2 rounded-2xl w-35 hover:bg-gray-200 text-green-500 font-semibold cursor-pointer hover:font-bold"
                >
                  Đóng
                </button>
              </div>
            </div>
          </div>
        </section>
      )}
    </>
  );
};

export default Register;
