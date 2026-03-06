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
.wrap{ height:100%; display:flex; flex-direction:column; }
.top{
  background:#fff;
  border-bottom:1px solid #e5e7eb;
  padding:14px;
  display:flex;
  align-items:center;
  justify-content:space-between;
  gap: 10px;
}
.name{ font-weight:900; }
.sub{ font-size:12px; color:#6b7280; margin-top:2px; }
.msgs{ flex:1; overflow:auto; padding:16px; background:#f6f7fb; }
.state{ padding: 14px; text-align:center; color:#6b7280; font-size:13px; }
.empty{ height:100%; display:flex; align-items:center; justify-content:center; color:#6b7280; }
.empty-state {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    margin-top: 40px;
}
.suggestions {
    display: flex;
    flex-direction: column;
    gap: 8px;
    margin-top: 15px;
    width: 80%;
}
.suggest-btn {
    padding: 10px 14px;
    border-radius: 12px;
    border: 1px solid #111827;
    background: transparent;
    color: #111827;
    cursor: pointer;
    font-size: 13px;
    font-weight: 500;
    transition: all 0.2s;
}
.suggest-btn:hover {
    background: #111827;
    color: #fff;
}
.btn{
  border:none;
  background:#111827;
  color:#fff;
  padding:10px 12px;
  border-radius:12px;
  cursor:pointer;
  font-size:12px;
}
.btn.ghost{
  background:#fff;
  color:#111827;
  border:1px solid #e5e7eb;
}
.err{
  padding: 10px 14px;
  color:#b91c1c;
  background:#fef2f2;
  border-bottom:1px solid #fecaca;
  font-size: 13px;
}
</style>
