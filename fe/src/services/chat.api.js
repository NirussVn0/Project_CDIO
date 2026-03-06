import axios from "axios";

const BASE_URL = import.meta.env.VITE_API_BASE_URL || "http://127.0.0.1:8000";

const http = axios.create({
  baseURL: BASE_URL,
  withCredentials: false,
});

function safeParse(value) {
  try {
    return JSON.parse(value);
  } catch {
    return null;
  }
}

function getActiveUser() {
  if (localStorage.getItem("admin_auth")) {
    const u = safeParse(localStorage.getItem("admin_user"));
    if (u) return { id: u.id_nguoi_dung || u.id, role: u.vai_tro };
  }

  if (localStorage.getItem("tech_auth")) {
    const u = safeParse(localStorage.getItem("tech_user"));
    if (u) return { id: u.id_nguoi_dung || u.id, role: u.vai_tro };
  }

  const u = safeParse(localStorage.getItem("user"));
  if (u) return { id: u.id_nguoi_dung || u.id, role: u.vai_tro };

  return null;
}

http.interceptors.request.use((config) => {
  const token = localStorage.getItem("token");
  if (token) config.headers.Authorization = `Bearer ${token}`;

  const active = getActiveUser();
  if (active?.id) config.headers["X-User-Id"] = active.id;
  if (active?.role) config.headers["X-User-Role"] = active.role;

  return config;
});

export const chatApi = {
  listConversations(params = {}) {
    return http.get("/api/chat/conversations", { params }).then((r) => r.data);
  },

  createConversation(payload) {
    return http.post("/api/chat/conversations", payload).then((r) => r.data);
  },

  startAdminChat() {
    return http.post("/api/chat/start-admin").then((r) => r.data);
  },

  addMembers(conversationId, payload) {
    return http.post(`/api/chat/conversations/${conversationId}/members`, payload).then((r) => r.data);
  },

  removeMember(conversationId, userId) {
    return http.delete(`/api/chat/conversations/${conversationId}/members/${userId}`).then((r) => r.data);
  },

  listMessages(conversationId, params = {}) {
    return http.get(`/api/chat/conversations/${conversationId}/messages`, { params }).then((r) => r.data);
  },

  sendText(conversationId, payload) {
    return http.post(`/api/chat/conversations/${conversationId}/messages`, {
      type: "text",
      content: payload.content,
    }).then((r) => r.data);
  },

  uploadImage(conversationId, file) {
    const fd = new FormData();
    fd.append("image", file);
    return http.post(`/api/chat/conversations/${conversationId}/messages/image`, fd, {
      headers: { "Content-Type": "multipart/form-data" },
    }).then((r) => r.data);
  },

  markRead(messageId) {
    return http.post(`/api/chat/messages/${messageId}/read`).then((r) => r.data);
  },

  recall(messageId) {
    return http.post(`/api/chat/messages/${messageId}/recall`).then((r) => r.data);
  },

  deleteForMe(messageId) {
    return http.post(`/api/chat/messages/${messageId}/delete-for-me`).then((r) => r.data);
  },

  deleteForAll(messageId) {
    return http.delete(`/api/chat/messages/${messageId}`).then((r) => r.data);
  },
};
