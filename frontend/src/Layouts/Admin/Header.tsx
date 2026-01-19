import { Bell, Search, UserRoundCheck } from "lucide-react";

const Header = () => {
  return (
    <div className="w-full p-4.5 border-b border-b-gray-300 shadow-sm">
      <section className="flex items-center justify-between">
        <div className="ml-5">
          <form className="relative">
            <input
              type="text"
              placeholder="Nhập nội dung cần tìm kiếm..."
              className="w-140 border border-gray-300 rounded-3xl p-2 focus:outline-none"
            />
            <button
              className="absolute inset-y-0 right-4 cursor-pointer text-blue-950"
              type="button"
              title="Tìm kiếm"
            >
              <Search />
            </button>
          </form>
        </div>
        <div className="relative flex items-center gap-3 mr-5">
          <p className="w-14 h-14 border border-gray-300 rounded-full p-4 cursor-pointer">
            <Bell />
          </p>
          <p className="absolute -inset-y-1 right-17 w-5 h-5 bg-red-500 rounded-full text-center leading-5 text-white font-semibold">
            1
          </p>
          <p className="w-14 h-14 border border-gray-300 rounded-full p-4 cursor-pointer">
            <UserRoundCheck />
          </p>
        </div>
      </section>
    </div>
  );
};

export default Header;
