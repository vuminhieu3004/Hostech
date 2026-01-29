import Api from "../Api/Api";
import type { ILogin } from "../Types/Auth.Type";

// Register
export const register = async () => {
  const { data } = await Api.post("auth/register");
  return data;
};

// Login
export const Login = async (dataForm: unknown) => {
  return await Api.post("auth/login", dataForm);
};

// Verify OTP
export const verifyOTP = async () => {
  const { data } = await Api.post("auth/otp/verify");
  return data;
};
