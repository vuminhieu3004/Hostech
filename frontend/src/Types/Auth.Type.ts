export interface ILogin {
  login: string;
  password: string;
}

export interface ILoginResponse {
  org_id: string;
  session_id: string;
  otp_ttl: number;
  otp_method: "email" | "sms";
  dev_otp?: string;
}

export interface IOtpVerify {
  otp: string;
}

export interface IDecodeJWT {
  id: number;
  role: string;
}

export interface IMe {
  full_name: string;
  org_id: string;
  role: string;
  phone: string;
  email: string;
}
