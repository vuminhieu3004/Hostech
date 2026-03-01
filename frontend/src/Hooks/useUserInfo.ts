import { useQuery } from "@tanstack/react-query";
import { GetMe } from "../Services/Auth.service";
import { useTokenStore } from "../Stores/AuthStore";

export const useUserInfo = () => {
  const token = useTokenStore((state) => state.getToken());

  const { data, isLoading, error } = useQuery({
    queryKey: ["userInfo", token],
    queryFn: GetMe,
    enabled: !!token, // Chỉ gọi khi có token
    staleTime: 1000 * 60 * 5, // Cache 5 phút
  });

  return {
    user: data,
    isLoading,
    error,
  };
};
