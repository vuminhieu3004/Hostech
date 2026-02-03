import Api from "../Api/Api";
import type { ILogin, IMe, IOtpVerify } from "../Types/Auth.Type";

// Register
export const register = async () => {
  const { data } = await Api.post("auth/register");
  return data;
};

// Login
export const Login = async (data: any) => {
  return await Api.post("auth/login", {
    ...data,
    device_name: "web",
    device_platform: "Web",
  });
};

// Verify OTP
export const verifyOTP = async (data: any) => {
  return Api.post("auth/otp/verify", data);
};

// Gửi lại mã OTP
export const ResendOTP = (data: any) => {
  return Api.post("auth/otp/request", { ...data });
};

// Register Users
export const RegisterUser = async (data: any) => {
  return Api.post("auth/register-user", { ...data });
};

// Lây thông tin người dùng
export const GetMe = async () => {
  const { data } = await Api.get<any>("auth/me");
  return data;
};
