<template>
  <div class="wrap" v-if="s.activeConversation">
    <header class="top">
      <div>
        <div class="name">{{ headerTitle }}</div>
        <div class="sub">{{ headerSub }}</div>
      </div>
      <button v-if="s.messagesHasMore" class="btn ghost" @click="loadOlder" :disabled="s.loadingMessages">
        {{ s.loadingMessages ? "Đang tải..." : "Tải tin nhắn cũ" }}
      </button>
    </header>

    <div v-if="s.error" class="err">{{ s.error }}</div>

    <section ref="listRef" class="msgs">
      <MessageBubble v-for="m in s.messages" :key="m.id" :message="m" />
      
      <div v-if="s.loadingMessages && !s.messages.length" class="state">Đang tải tin nhắn...</div>
      
      <div v-if="!s.loadingMessages && !s.messages.length" class="state empty-state">
        <p>Chưa có tin nhắn nào. Bạn cần hỗ trợ gì?</p>
        <div class="suggestions">
            <button class="suggest-btn" @click="sendSuggestion('Chào Ban quản lý, tôi cần hỗ trợ')">Tôi cần hỗ trợ</button>
            <button class="suggest-btn" @click="sendSuggestion('Tôi muốn báo cáo sự cố khẩn cấp!')">Báo cáo Sự cố khẩn cấp</button>
            <button class="suggest-btn" @click="sendSuggestion('Cho tôi hỏi về dịch vụ tòa nhà')">Dịch vụ tòa nhà</button>
        </div>
      </div>
    </section>

    <MessageComposer />
  </div>

  <div v-else class="empty">
    Chọn một hội thoại để bắt đầu.
  </div>
</template>

<script setup>
import { computed, nextTick, ref, watch, onMounted, onUnmounted } from "vue";
import { chatStore as s } from "@/store/chat.store.js";
import MessageBubble from "./MessageBubble.vue";
import MessageComposer from "./MessageComposer.vue";

const listRef = ref(null);

const headerTitle = computed(() => {
  const c = s.activeConversation;
  return c?.title || c?.other_name || "Hội thoại";
});
const headerSub = computed(() => {
  const c = s.activeConversation;
  if (!c) return "";
  return c.type === "group" ? "Nhóm chat" : (c.other_role ? `Đối tượng: ${c.other_role}` : "Chat 1-1");
});

async function scrollBottom() {
  await nextTick();
  const el = listRef.value;
  if (el) el.scrollTop = el.scrollHeight;
}

watch(() => s.messages.length, scrollBottom);
watch(() => s.activeConversation?.id, scrollBottom);

async function loadOlder() {
  await s.fetchMessages({ reset: false });
}

async function sendSuggestion(text) {
    if (s.sending) return;
    await s.sendText(text);
}

onMounted(() => {
    s.startPolling();
});

onUnmounted(() => {
    s.stopPolling();
});
</script>

<style scoped>
.wrap { height: 100%; display: flex; flex-direction: column; }

.top {
  background: #fff;
  border-bottom: 1px solid #e5e7eb;
  padding: 12px 14px;
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 10px;
}
.name { font-weight: 800; font-size: 14px; }
.sub { font-size: 11px; color: #6b7280; margin-top: 2px; }

.msgs {
  flex: 1;
  overflow: auto;
  padding: 16px;
  background: #f8fafc;
  scroll-behavior: smooth;
}

.state { padding: 14px; text-align: center; color: #9ca3af; font-size: 13px; }
.empty { height: 100%; display: flex; align-items: center; justify-content: center; color: #9ca3af; }

.empty-state {
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  margin-top: 32px;
  animation: fadeIn 0.4s ease-out;
}
.empty-state p {
  font-size: 14px;
  color: #6b7280;
  margin-bottom: 4px;
}

.suggestions {
  display: flex;
  flex-direction: column;
  gap: 8px;
  margin-top: 12px;
  width: 85%;
}

.suggest-btn {
  padding: 11px 16px;
  border-radius: 12px;
  border: 1px solid #cbd5e1;
  background: #fff;
  color: #334155;
  cursor: pointer;
  font-size: 13px;
  font-weight: 500;
  text-align: left;
  transition: all 0.2s cubic-bezier(.4,0,.2,1);
  animation: slideInUp 0.35s ease-out both;
  box-shadow: 0 1px 3px rgba(0,0,0,.04);
}
.suggest-btn:nth-child(1) { animation-delay: 0.05s; }
.suggest-btn:nth-child(2) { animation-delay: 0.12s; }
.suggest-btn:nth-child(3) { animation-delay: 0.19s; }

.suggest-btn:hover {
  background: #0f172a;
  color: #fff;
  border-color: #0f172a;
  transform: translateY(-1px);
  box-shadow: 0 4px 12px rgba(15, 23, 42, 0.2);
}
.suggest-btn:active {
  transform: scale(0.97);
}

.btn {
  border: none;
  background: #0f172a;
  color: #fff;
  padding: 8px 14px;
  border-radius: 10px;
  cursor: pointer;
  font-size: 12px;
  font-weight: 500;
  transition: all 0.2s;
}
.btn:hover {
  background: #1e293b;
  transform: translateY(-1px);
}
.btn:active {
  transform: scale(0.96);
}
.btn.ghost {
  background: #fff;
  color: #334155;
  border: 1px solid #e2e8f0;
}
.btn.ghost:hover {
  background: #f1f5f9;
  border-color: #cbd5e1;
}

.err {
  padding: 10px 14px;
  color: #b91c1c;
  background: #fef2f2;
  border-bottom: 1px solid #fecaca;
  font-size: 13px;
  animation: fadeIn 0.3s ease-out;
}

@keyframes fadeIn {
  from { opacity: 0; }
  to { opacity: 1; }
}
@keyframes slideInUp {
  from {
    opacity: 0;
    transform: translateY(8px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}
</style>
