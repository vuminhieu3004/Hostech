import { useForm } from "react-hook-form";
import { useOpenStore } from "../../Stores/OpenStore";
import { Eye, EyeClosed } from "lucide-react";
import { useMutation, useQueryClient } from "@tanstack/react-query";
import { Login } from "../../Services/Auth.service";
import type { ILogin, ILoginResponse } from "../../Types/Auth.Type";
import { message, Modal } from "antd";
import { data, useNavigate } from "react-router";
import { useState } from "react";

const AuthPage = () => {
  const { eyePassword, setEyePassword } = useOpenStore();
  const nav = useNavigate();
  const queryClient = useQueryClient();
  const { register, handleSubmit, reset } = useForm<ILogin>();
  const [pendingLogin, setPendingLogin] = useState<ILogin | null>(null);
  const [orgs, setOrgs] = useState<any[]>([]);

  const loginMutation = useMutation({
    mutationFn: Login,

    onSuccess: (res) => {
      const data = res.data as ILoginResponse;

      message.success("Mật khẩu đúng, vui lòng nhập OTP");

      nav("/otp/verify", {
        state: {
          session_id: data.session_id,
          org_id: data.org_id,
          otp_ttl: data.otp_ttl,
          dev_otp: data.dev_otp,
        },
      });
      reset({});
    },

    onError: (err: any) => {
      const status = err?.response?.status;
      const data = err?.response?.data;

      if (status === 422) {
        message.error("Sai email / số điện thoại hoặc mật khẩu");
      }

      if (status === 403) {
        message.error("Tài khoản đã bị khoá");
      }

      if (status === 423) {
        message.warning(`Tài khoản bị khoá tạm đến ${data.locked_until}`);
      }

      if (status === 409) {
        setOrgs(data.orgs);
      }
    },
  });

  const handleLogin = (data: ILogin) => {
    console.log(data);
    setPendingLogin(data);
    loginMutation.mutate(data);
  };
  return (
    <>
      <div className="p-3 max-h-170">
        <section className="flex w-full max-h-170">
          <div className="w-[70%] h-170 rounded-bl-2xl rounded-tl-2xl overflow-hidden bg-gradient-to-t from-blue-300 to-blue-600">
            <img
              src="/images/logo_du_an.jpg"
              alt=""
              className="object-cover w-100 h-100 rounded-full ml-[28%] mt-[14%]"
            />
          </div>
          <div
            className={`flex flex-col justify-center  w-[30%]  bg-white border rounded-tr-2xl rounded-br-2xl border-gray-300 items-start p-5 transition-all duration-500 ease-in-out
       
      `}
          >
            <div className="text-center">
              <h2 className="p-10 pl-13 text-3xl font-bold text-blue-600">
                Welcome to Hostech
              </h2>
            </div>
            <div className="flex flex-col justify-center w-full bg-white border shadow-2xl shadow-blue-500 border-gray-300 rounded-[10px] items-start p-10 transition-all duration-500 ease-in-out">
              <h2 className="w-full text-center text-2xl font-semibold pb-5">
                Đăng nhập
              </h2>
              <form
                onSubmit={handleSubmit(handleLogin)}
                className="w-full flex flex-col gap-2"
              >
                <div className="flex flex-col w-full">
                  <label htmlFor="" className="p-1 pl-3">
                    Tài khoản(*)
                  </label>
                  <input
                    type="text"
                    placeholder="Email hoặc số điện thoại..."
                    {...register("login", { required: true })}
                    className="w-full border border-gray-400 rounded-[10px] focus:outline-none p-2 placeholder:text-[14px]"
                  />
                </div>
                <div className="relative flex flex-col w-full">
                  <label htmlFor="" className="p-1 pl-3">
                    Mật khẩu(*)
                  </label>
                  <input
                    type={eyePassword ? "text" : "password"}
                    placeholder="Nhập mật khẩu đăng nhập..."
                    {...register("password", { required: true })}
                    className="w-full border border-gray-400 rounded-[10px] focus:outline-none p-2 placeholder:text-[14px]"
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
                  <button className="w-full cursor-pointer flex items-center justify-center hover:font-semibold border border-gray-300 p-2 rounded-[10px] text-[15px]">
                    Đăng nhập bằng google
                    <img
                      src="./images/Auth/gg.png"
                      alt="google"
                      className="w-10 rounded-full"
                    />
                  </button>
                </div>
                <div className="flex flex-col items-center gap-2 mt-3">
                  <button
                    type="submit"
                    className="w-35 p-2 bg-red-500 rounded-[10px] cursor-pointer text-white hover:font-semibold hover:bg-red-600"
                  >
                    Đăng nhập
                  </button>
                </div>
              </form>
            </div>
          </div>
        </section>
      </div>

      <Modal
        open={orgs.length > 0}
        footer={null}
        onCancel={() => setOrgs([])}
        title="Chọn tổ chức"
      >
        {orgs.map((org) => (
          <button
            key={org.org_id}
            onClick={() => {
              if (!pendingLogin) return;

              loginMutation.mutate({
                ...pendingLogin,
                org_id: org.org_id,
              });
            }}
            className="w-full text-left p-2 hover:bg-gray-100"
          >
            {org.org_name}
          </button>
        ))}
      </Modal>
    </>
  );
};

export default AuthPage;
