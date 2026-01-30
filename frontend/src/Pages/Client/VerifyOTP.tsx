import React from "react";
import { useForm } from "react-hook-form";
import { useLocation, useNavigate } from "react-router";
import { message } from "antd";
import Api from "../../Api/Api";

type OTPForm = {
  otp: string;
};

const VerifyOTP = () => {
  const { register, handleSubmit } = useForm<OTPForm>();
  const location = useLocation();
  const navigate = useNavigate();

  const { session_id, org_id, dev_otp } = location.state || {};

  if (!session_id || !org_id) {
    message.error("Phiên xác thực không hợp lệ, vui lòng đăng nhập lại");
    navigate("/auth");
    return null;
  }

  const handleOTP = async (data: OTPForm) => {
    try {
      await Api.post("auth/otp/verify", {
        session_id,
        org_id,
        otp: data.otp,
      });

      message.success("Xác thực OTP thành công");

      navigate("/admin");
    } catch (err: any) {
      message.error(err?.response?.data?.message || "OTP không hợp lệ");
    }
  };

  return (
    <div className="p-3 h-180">
      <section className="flex w-full h-179">
        <div className="w-[70%] h-179 rounded-bl-2xl rounded-tl-2xl overflow-hidden bg-blue-400">
          <img
            src="/images/logo_du_an.jpg"
            alt=""
            className="object-cover w-100 h-100 rounded-full ml-[28%] mt-[15%]"
          />
        </div>
        <div className="flex flex-col justify-center w-[30%] bg-white border rounded-tr-2xl rounded-br-2xl border-gray-300 items-start p-5">
          <div className="text-center">
            <h2 className="p-10 pl-13 text-3xl font-bold text-blue-600">
              Welcome to Hostech
            </h2>
          </div>
          <div className="flex flex-col justify-center w-full bg-white border shadow-2xl shadow-blue-500 rounded-[10px] items-start p-10">
            <h2 className="w-full text-center text-2xl font-semibold pb-5">
              Xác thực OTP
            </h2>

            {dev_otp && (
              <p className="text-sm text-gray-500 mb-2">
                OTP dev: <b>{dev_otp}</b>
              </p>
            )}

            <form
              onSubmit={handleSubmit(handleOTP)}
              className="w-full flex flex-col gap-2"
            >
              <div className="flex flex-col w-full">
                <label className="p-1 pl-3">Xác thực OTP(*)</label>
                <input
                  type="text"
                  placeholder="Nhập mã OTP vừa nhận..."
                  {...register("otp", { required: true })}
                  className="w-full border border-gray-400 rounded-[10px] p-2"
                />
              </div>
              <div className="flex flex-col items-center gap-2 mt-3">
                <button
                  type="submit"
                  className="w-35 p-2 bg-red-500 rounded-[10px] text-white hover:bg-red-600 cursor-pointer"
                >
                  Xác nhận
                </button>
              </div>
            </form>
          </div>
        </div>
      </section>
    </div>
  );
};

export default VerifyOTP;
