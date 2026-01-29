import Api from "../Api/Api";

// Register
export const register = async () => {
  const { data } = await Api.post("auth/register");
  return data;
};
