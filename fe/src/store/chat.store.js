import { reactive } from "vue";
import { chatApi } from "../services/chat.api.js";

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

function ensureUserOrError(store) {
  const u = getActiveUser();
  if (!u?.id) {
    store.error = "Bạn cần đăng nhập để dùng chat.";
    return false;
  }
  return true;
}

export const chatStore = reactive({
  loadingConversations: false,
  loadingMessages: false,
  sending: false,
  error: null,

  conversations: [],
  activeConversation: null,

  messages: [],
  messagesCursor: null,
  messagesHasMore: true,

  convCursor: null,
  convHasMore: true,

  search: "",
  pollInterval: null,

  startPolling() {
    if (this.pollInterval) clearInterval(this.pollInterval);
    this.pollInterval = setInterval(() => {
      if (this.activeConversation && !this.loadingMessages) {
        this.fetchMessages({ reset: true });
      }
    }, 5000);
  },

  stopPolling() {
    if (this.pollInterval) {
      clearInterval(this.pollInterval);
      this.pollInterval = null;
    }
  },

  async fetchConversations({ reset = false } = {}) {
    this.error = null;
    if (!ensureUserOrError(this)) return;
    this.loadingConversations = true;
    try {
      const res = await chatApi.listConversations({
        q: this.search || undefined,
        cursor: reset ? undefined : this.convCursor,
        limit: 30,
      });
      const list = res.data || [];
      if (reset) this.conversations = list;
      else this.conversations = [...this.conversations, ...list];

      const u = getActiveUser();
      if (u?.role === "client" && this.conversations.length === 0 && reset) {
        const newChatRes = await chatApi.startAdminChat();
        if (newChatRes?.conversation) {
          this.conversations = [newChatRes.conversation];
        }
      }

      this.convCursor = res.next_cursor ?? null;
      this.convHasMore = !!res.has_more;

      if (!this.activeConversation && this.conversations.length) {
        this.setActiveConversation(this.conversations[0]);
      }
    } catch (e) {
      this.error = e?.response?.data?.message || e.message || "Không tải được hội thoại";
    } finally {
      this.loadingConversations = false;
    }
  },

  async setActiveConversation(conv) {
    this.activeConversation = conv;
    this.messages = [];
    this.messagesCursor = null;
    this.messagesHasMore = true;
    await this.fetchMessages({ reset: true });
  },

  async fetchMessages({ reset = false } = {}) {
    if (!this.activeConversation) return;
    if (!reset && !this.messagesHasMore) return;

    this.error = null;
    if (!ensureUserOrError(this)) return;
    this.loadingMessages = true;
    try {
      const res = await chatApi.listMessages(this.activeConversation.id, {
        cursor: reset ? undefined : this.messagesCursor,
        limit: 30,
      });
      const list = res.data || [];
      if (reset) this.messages = list;
      else this.messages = [...list, ...this.messages];

      this.messagesCursor = res.next_cursor ?? null;
      this.messagesHasMore = !!res.has_more;
    } catch (e) {
      this.error = e?.response?.data?.message || e.message || "Không tải được tin nhắn";
    } finally {
      this.loadingMessages = false;
    }
  },

  async sendText(content) {
    if (!this.activeConversation || !content) return;
    this.error = null;
    if (!ensureUserOrError(this)) return;
    this.sending = true;
    try {
      const res = await chatApi.sendText(this.activeConversation.id, { content });
      if (res.message) this.messages = [...this.messages, res.message];
      await this.fetchConversations({ reset: true });
    } catch (e) {
      this.error = e?.response?.data?.message || e.message || "Gửi tin nhắn thất bại";
    } finally {
      this.sending = false;
    }
  },

  async sendImage(file) {
    if (!this.activeConversation || !file) return;
    this.error = null;
    if (!ensureUserOrError(this)) return;
    this.sending = true;
    try {
      const res = await chatApi.uploadImage(this.activeConversation.id, file);
      if (res.message) this.messages = [...this.messages, res.message];
      await this.fetchConversations({ reset: true });
    } catch (e) {
      this.error = e?.response?.data?.message || e.message || "Gửi ảnh thất bại";
    } finally {
      this.sending = false;
    }
  },

  async recallMessage(messageId) {
    this.error = null;
    if (!ensureUserOrError(this)) return;
    try {
      const res = await chatApi.recall(messageId);
      this.messages = this.messages.map((m) =>
        m.id === messageId ? { ...m, recalled_at: Date.now() } : m
      );
      return res;
    } catch (e) {
      this.error = e?.response?.data?.message || e.message || "Thu hồi thất bại";
    }
  },

  async deleteForMe(messageId) {
    this.error = null;
    if (!ensureUserOrError(this)) return;
    try {
      await chatApi.deleteForMe(messageId);
      this.messages = this.messages.filter((m) => m.id !== messageId);
    } catch (e) {
      this.error = e?.response?.data?.message || e.message || "Xóa (chỉ mình) thất bại";
    }
  },

  async deleteForAll(messageId) {
    this.error = null;
    if (!ensureUserOrError(this)) return;
    try {
      await chatApi.deleteForAll(messageId);
      this.messages = this.messages.filter((m) => m.id !== messageId);
      await this.fetchConversations({ reset: true });
    } catch (e) {
      this.error = e?.response?.data?.message || e.message || "Xóa (mọi người) thất bại";
    }
  },

  async createGroup({ title, memberIds }) {
    this.error = null;
    if (!ensureUserOrError(this)) return;
    this.sending = true;
    try {
      const res = await chatApi.createConversation({
        type: "group",
        title,
        member_ids: memberIds,
      });
      if (res.conversation) {
        this.conversations = [res.conversation, ...this.conversations];
        await this.setActiveConversation(res.conversation);
      } else {
        await this.fetchConversations({ reset: true });
      }
    } catch (e) {
      this.error = e?.response?.data?.message || e.message || "Tạo nhóm thất bại";
    } finally {
      this.sending = false;
    }
  },
});
