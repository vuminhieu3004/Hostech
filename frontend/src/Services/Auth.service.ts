import Api from "../Api/Api";
import type { ILogin, IOtpVerify } from "../Types/Auth.Type";

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
export const verifyOTP = async (data: IOtpVerify) => {
  return await Api.post("auth/otp/verify", data);
};
