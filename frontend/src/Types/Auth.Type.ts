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
  session_id: string;
  otp: string;
}
