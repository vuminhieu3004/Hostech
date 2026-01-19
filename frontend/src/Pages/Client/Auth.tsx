import { useOpenStore } from "../../Stores/OpenStore";
import { Eye, EyeClosed } from "lucide-react";

const AuthPage = () => {
  const { open, eyePassword, setOpen, setEyePassword } = useOpenStore();
  return (
    <>
      <div className="fixed w-screen h-screen bg-[url(./images/Auth/bg.jpg)] bg-no-repeat bg-cover"></div>
      <div className="relative max-w-7xl mx-auto h-screen overflow-hidden p-[10%] pt-[5%]">
        <section className="relative w-[50%] mx-auto flex">
          <div
            className={`absolute inset-0 flex flex-col justify-center h-120 bg-white border shadow-sm border-gray-300 rounded-2xl items-start p-10 transition-all duration-500 ease-in-out
        ${open ? "-translate-x-full opacity-0" : "translate-x-0 opacity-100"}
      `}
          >
            <h2 className="w-full text-center text-2xl font-semibold pb-7">
              Đăng nhập
            </h2>
            <div className="w-full flex flex-col gap-2">
              <div className="flex flex-col w-full">
                <label htmlFor="" className="p-1 pl-3">
                  Tài khoản(*)
                </label>
                <input
                  type="email"
                  placeholder="Nhập email đăng nhập..."
                  className="w-full border border-gray-400 rounded-2xl focus:outline-none p-2"
                />
              </div>
              <div className="relative flex flex-col w-full">
                <label htmlFor="" className="p-1 pl-3">
                  Mật khẩu(*)
                </label>
                <input
                  type={eyePassword ? "text" : "password"}
                  placeholder="Nhập mật khẩu đăng nhập..."
                  className="w-full border border-gray-400 rounded-2xl focus:outline-none p-2"
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
                <button className="w-full cursor-pointer flex items-center justify-center hover:font-semibold border border-gray-300 p-2 rounded-3xl">
                  Đăng nhập bằng google
                  <img
                    src="./images/Auth/gg.png"
                    alt="google"
                    className="w-10 rounded-full"
                  />
                </button>
                <button className="w-full cursor-pointer flex items-center justify-center hover:font-semibold border border-gray-300 p-2 rounded-3xl">
                  Đăng nhập bằng facebook
                  <img
                    src="./images/Auth/fb.png"
                    alt="google"
                    className="w-10 rounded-full"
                  />
                </button>
              </div>
              <div className="flex flex-col items-center gap-2 mt-3">
                <button
                  type="button"
                  className="w-35 p-2 bg-red-500 rounded-2xl cursor-pointer text-white hover:font-semibold hover:bg-red-600"
                >
                  Đăng nhập
                </button>
                <p>
                  Nếu chưa có tài khoản đăng ký?{" "}
                  <span
                    onClick={() => setOpen(true)}
                    className="cursor-pointer text-red-500 font-semibold hover:font-bold"
                  >
                    tại đây!
                  </span>
                </p>
              </div>
            </div>
          </div>

          <div
            className={`absolute inset-0 flex flex-col justify-center h-140 bg-white border shadow-sm border-gray-300 rounded-2xl items-start p-10 transition-all duration-500 ease-in-out
        ${open ? "translate-x-0 opacity-100" : "translate-x-full opacity-0"}
      `}
          >
            <h2 className="w-full text-center text-2xl font-semibold pb-7">
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
                  className="w-full border border-gray-400 rounded-2xl focus:outline-none p-2"
                />
              </div>
              <div className="relative flex flex-col w-full">
                <label htmlFor="" className="p-1 pl-3">
                  Mật khẩu(*)
                </label>
                <input
                  type={eyePassword ? "text" : "password"}
                  placeholder="Nhập mật khẩu đăng ký..."
                  className="w-full border border-gray-400 rounded-2xl focus:outline-none p-2"
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
                  className="w-full border border-gray-400 rounded-2xl focus:outline-none p-2"
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
                <button className="w-full cursor-pointer flex items-center justify-center hover:font-semibold border border-gray-300 p-2 rounded-3xl">
                  Đăng ký bằng google
                  <img
                    src="./images/Auth/gg.png"
                    alt="google"
                    className="w-10 rounded-full"
                  />
                </button>
                <button className="w-full cursor-pointer flex items-center justify-center hover:font-semibold border border-gray-300 p-2 rounded-3xl">
                  Đăng ký bằng facebook
                  <img
                    src="./images/Auth/fb.png"
                    alt="google"
                    className="w-10 rounded-full"
                  />
                </button>
              </div>
              <div className="flex flex-col items-center gap-2 mt-3">
                <button
                  type="button"
                  className="w-35 p-2 bg-red-500 rounded-2xl cursor-pointer text-white hover:font-semibold hover:bg-red-600"
                >
                  Đăng ký
                </button>
                <p>
                  Nếu đã có tài khoản đăng nhập?{" "}
                  <span
                    onClick={() => setOpen(false)}
                    className="cursor-pointer text-green-500 font-semibold hover:font-bold"
                  >
                    tại đây!
                  </span>
                </p>
              </div>
            </div>
          </div>
        </section>
      </div>
    </>
  );
};

export default AuthPage;
