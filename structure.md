# Cấu trúc dự án Project CDIO

Đây là một **Hệ thống Quản lý Bảo trì Tòa nhà/Chung cư (Building Maintenance Management System)**.
Dự án được chia làm 2 phần rõ rệt: Frontend chạy bằng **Vue.js 3** và Backend chạy bằng **PHP Laravel 12**.

Hệ thống phục vụ 3 nhóm đối tượng (Role) chính:
- **Admin (Ban quản lý):** Quản lý toàn bộ tòa nhà, điều phối công việc.
- **Client (Cư dân/Chủ căn hộ):** Xem thông tin, báo cáo sự cố và đánh giá dịch vụ.
- **Technician (Kỹ thuật viên):** Nhận task, cập nhật tiến độ công việc bảo trì.

## 1. Chức năng chính (Features)

*   **Dành cho Admin (Ban quản lý):**
    *   Quản lý sơ đồ nhà, danh sách căn hộ và cư dân.
    *   Quét yêu cầu bảo trì từ cư dân và **phân công** cho kỹ thuật viên.
    *   Quản lý tài chính: theo dõi hóa đơn và chi phí sửa chữa.
    *   Quản lý kho vật tư thiết bị.
    *   Cài đặt cấu hình hệ thống tòa nhà.
    *   Nhận và trả lời tin nhắn hỗ trợ từ cư dân (Chat).
*   **Dành cho Client (Cư dân):**
    *   Đăng ký, đăng nhập tài khoản.
    *   Gửi **Yêu cầu bảo trì** (kèm mô tả và upload hình ảnh sự cố).
    *   Theo dõi lịch sử và trạng thái sửa chữa các sự cố trong nhà mình.
    *   Đánh giá (Rating) và gửi phản hồi về chất lượng sửa chữa.
    *   Chat hỗ trợ với Ban Quản Lý qua Popup Chat (kèm Suggestion gợi ý).
*   **Dành cho Technician (Kỹ thuật viên):**
    *   Đăng nhập vào luồng dành riêng cho thợ.
    *   Xem danh sách công việc được Admin phân công.
    *   Cập nhật trạng thái công việc (đang làm, hoàn thành...).
    *   Ghi chú nhật ký công việc.

## 2. Cấu trúc thư mục từ Root (File Structure)

```text
Project_CDIO/
├── be/                                 # Backend (Laravel 12, PHP 8.2+)
│   ├── app/
│   │   ├── Http/
│   │   │   └── Controllers/
│   │   │       ├── Api/
│   │   │       │   ├── ChatController.php          # API Chat (hỗ trợ cư dân)
│   │   │       │   ├── TechnicianAuthController.php
│   │   │       │   └── TechnicianJobController.php
│   │   │       ├── AuthController.php
│   │   │       ├── CanHoController.php
│   │   │       ├── LoaiSuCoController.php
│   │   │       ├── NguoiDungController.php
│   │   │       ├── NhatKyCongViecController.php
│   │   │       ├── PhanCongController.php
│   │   │       ├── PhanHoiController.php
│   │   │       ├── ToaNhaController.php
│   │   │       └── YeuCauBaoTriController.php
│   │   └── Models/
│   │       ├── CanHo.php               # Căn hộ
│   │       ├── Conversation.php        # Cuộc hội thoại Chat
│   │       ├── ConversationUser.php    # Thành viên trong Chat
│   │       ├── Message.php             # Tin nhắn Chat
│   │       ├── ChatConversation.php    # (Legacy) Chat conversation
│   │       ├── ChatConversationMember.php
│   │       ├── ChatMessage.php
│   │       ├── ChatMessageDelete.php
│   │       ├── HinhAnhYeuCau.php       # Hình ảnh đính kèm yêu cầu
│   │       ├── LoaiSuCo.php            # Loại sự cố
│   │       ├── NguoiDung.php           # Người dùng
│   │       ├── NhatKyCongViec.php      # Nhật ký công việc
│   │       ├── PhanCong.php            # Phân công kỹ thuật viên
│   │       ├── PhanHoi.php             # Phản hồi / Đánh giá
│   │       ├── TechnicianJob.php       # Công việc kỹ thuật viên
│   │       ├── ToaNha.php              # Tòa nhà
│   │       ├── User.php
│   │       └── YeuCauBaoTri.php        # Yêu cầu bảo trì
│   ├── bootstrap/
│   ├── config/
│   ├── database/                       # Migrations & Seeders
│   ├── public/
│   ├── resources/
│   ├── routes/
│   │   ├── api.php                     # Tất cả API endpoints
│   │   ├── console.php
│   │   └── web.php
│   ├── storage/
│   ├── tests/
│   ├── .env                            # Biến môi trường (DB, APP_KEY...)
│   ├── artisan
│   └── composer.json
│
└── fe/                                 # Frontend (Vue.js 3 + Vite 7)
    ├── public/
    ├── src/
    │   ├── assets/                     # Hình ảnh, icon...
    │   ├── components/
    │   │   ├── Admin/                  # Giao diện Ban Quản Lý
    │   │   │   ├── Auth/               #   Đăng nhập Admin
    │   │   │   ├── BANQUANLY/          #   Quản lý cư dân, bảo trì, tài chính, kho
    │   │   │   ├── HeThong/            #   Cài đặt hệ thống
    │   │   │   └── TrangChu.vue        #   Dashboard Admin
    │   │   ├── Client/                 # Giao diện Cư Dân
    │   │   │   ├── Auth/               #   Đăng nhập / Đăng ký
    │   │   │   ├── Dashboard/          #   Dashboard cư dân
    │   │   │   ├── History/            #   Lịch sử bảo trì
    │   │   │   ├── Home/               #   Trang chủ
    │   │   │   ├── Reviews/            #   Đánh giá dịch vụ
    │   │   │   └── Services/           #   Dịch vụ tòa nhà
    │   │   ├── chat/                   # Popup Chat hỗ trợ
    │   │   │   ├── ChatWidget.vue      #   Nút bấm + Panel chính
    │   │   │   ├── ChatWindow.vue      #   Cửa sổ tin nhắn + Suggestion
    │   │   │   ├── ChatLayout.vue      #   Layout wrapper
    │   │   │   ├── ChatPage.vue
    │   │   │   ├── ConversationList.vue #   Danh sách hội thoại
    │   │   │   ├── GroupCreateModal.vue #   Tạo nhóm chat
    │   │   │   ├── MessageBubble.vue   #   Bong bóng tin nhắn
    │   │   │   └── MessageComposer.vue #   Ô nhập + gửi tin nhắn
    │   │   └── technician/             # Giao diện Kỹ thuật viên
    │   │       ├── auth/               #   Đăng nhập
    │   │       ├── features/           #   Jobs, Status, WorkLog, History, Profile
    │   │       └── home/               #   Dashboard
    │   ├── layouts/                    # Layout chung (Sidebar, Header)
    │   │   ├── compoments/             #   Các thành phần layout
    │   │   └── wrapper/                #   Layout wrapper theo role
    │   ├── router/
    │   │   └── index.js                # Định tuyến + Route Guards
    │   ├── services/
    │   │   └── chat.api.js             # HTTP client gọi API Chat
    │   ├── store/
    │   │   └── chat.store.js           # State management cho Chat
    │   ├── App.vue
    │   ├── main.js
    │   └── style.css
    ├── index.html
    ├── package.json
    └── vite.config.js
```

## 3. Mô tả chi tiết từng phần

### A. Backend (`be/`)
*   `app/Models/`: Các bảng dữ liệu phản ánh logic nghiệp vụ.
*   `app/Http/Controllers/`: Xử lý API. Thư mục `Api/` chứa các controller riêng cho Technician và Chat.
*   `routes/api.php`: Định nghĩa toàn bộ API endpoints, chia nhóm: Public, Resident, Technician, Staff, Admin, Chat.
*   `database/`: Migrations và Seeders.
*   `composer.json`: PHP 8.2+, Laravel 12.

### B. Frontend (`fe/`)
*   `src/components/`: Chia theo 4 nhóm: `Admin/`, `Client/`, `technician/`, `chat/`.
*   `src/router/index.js`: Định tuyến + Route Guards phân quyền (Admin, Client, Technician).
*   `src/services/chat.api.js`: HTTP client (Axios) gọi API Chat backend.
*   `src/store/chat.store.js`: Reactive store quản lý state Chat (conversations, messages, polling).
*   `package.json`: Vue 3, Vue Router, Pinia, Axios, v-calendar, Vite 7.
