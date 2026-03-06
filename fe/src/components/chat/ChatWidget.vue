<template>
  <div class="cw">
    <button class="fab" @click="toggle" :aria-label="open ? 'Đóng chat' : 'Mở chat'">
      <svg v-if="!open" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
        <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/>
      </svg>
      <svg v-else xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
        <line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/>
      </svg>
      <span v-if="badge" class="badge">{{ badge }}</span>
    </button>

    <div v-if="open" class="panel">
      <header class="head">
        <div class="brand">
          <div class="logo">
            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
              <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/>
            </svg>
          </div>
          <div class="meta">
            <div class="title">Hỗ trợ cư dân</div>
            <div class="subtitle">Phản hồi nhanh • 24/7</div>
          </div>
        </div>

        <div class="actions">
          <button class="hd-btn" @click="toggleList" :title="showList ? 'Ẩn danh sách' : 'Hiện danh sách'">
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
              <line x1="3" y1="6" x2="21" y2="6"/><line x1="3" y1="12" x2="21" y2="12"/><line x1="3" y1="18" x2="21" y2="18"/>
            </svg>
          </button>
          <button class="hd-btn" @click="reload" title="Tải lại">
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
              <polyline points="23 4 23 10 17 10"/><path d="M20.49 15a9 9 0 1 1-2.12-9.36L23 10"/>
            </svg>
          </button>
          <button class="hd-btn" @click="open=false" title="Thu nhỏ">
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
              <line x1="5" y1="12" x2="19" y2="12"/>
            </svg>
          </button>
        </div>
      </header>

      <div class="content" :class="{ 'no-list': !showList }">
        <aside v-if="showList" class="left">
          <div class="search">
            <input
              v-model.trim="s.search"
              placeholder="Tìm hội thoại..."
              @keydown.enter="reload"
            />
          </div>
          <div class="list">
            <ConversationList />
          </div>
        </aside>

        <main class="right">
          <ChatWindow />
        </main>
      </div>
    </div>
  </div>
</template>

<script setup>
import { computed, ref } from "vue";
import { chatStore as s } from "@/store/chat.store.js";
import ConversationList from "@/components/chat/ConversationList.vue";
import ChatWindow from "@/components/chat/ChatWindow.vue";

const open = ref(false);
const showList = ref(true);

function toggle() {
  open.value = !open.value;
  if (open.value && s.conversations.length === 0 && !s.loadingConversations) {
    s.fetchConversations({ reset: true });
  }
}

function toggleList() {
  showList.value = !showList.value;
}

async function reload() {
  await s.fetchConversations({ reset: true });
  if (s.activeConversation) await s.fetchMessages({ reset: true });
}

const badge = computed(() => 0);
</script>

<style scoped>
.cw {
  position: fixed;
  right: 18px;
  bottom: 18px;
  z-index: 9999;
  font-family: 'Inter', 'Segoe UI', system-ui, -apple-system, sans-serif;
}

/* ── FAB button ── */
.fab {
  position: absolute;
  right: 0;
  bottom: 0;
  width: 56px;
  height: 56px;
  border: none;
  border-radius: 50%;
  cursor: pointer;
  color: #fff;
  background: linear-gradient(135deg, #2563eb, #0f172a);
  box-shadow: 0 8px 32px rgba(37, 99, 235, 0.35);
  display: flex;
  align-items: center;
  justify-content: center;
  transition: transform 0.2s, box-shadow 0.2s;
}
.fab:hover {
  transform: scale(1.08);
  box-shadow: 0 12px 40px rgba(37, 99, 235, 0.45);
}

.badge {
  position: absolute;
  top: -4px;
  right: -4px;
  min-width: 18px;
  height: 18px;
  padding: 0 5px;
  border-radius: 999px;
  background: #ef4444;
  color: #fff;
  font-size: 11px;
  font-weight: 700;
  display: flex;
  align-items: center;
  justify-content: center;
  border: 2px solid #fff;
}

/* ── Panel ── */
.panel {
  width: 400px;
  height: min(72vh, 540px);
  min-height: 420px;
  margin-bottom: 66px;
  border-radius: 20px;
  overflow: hidden;
  background: #fff;
  border: 1px solid rgba(148, 163, 184, 0.25);
  box-shadow: 0 24px 64px rgba(2, 6, 23, 0.25);
  display: flex;
  flex-direction: column;
}

/* ── Header ── */
.head {
  padding: 14px 16px;
  color: #fff;
  background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%);
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 8px;
}

.brand {
  display: flex;
  align-items: center;
  gap: 10px;
  min-width: 0;
}

.logo {
  width: 36px;
  height: 36px;
  border-radius: 10px;
  background: rgba(255, 255, 255, 0.12);
  display: flex;
  align-items: center;
  justify-content: center;
  flex-shrink: 0;
}

.meta { min-width: 0; }

.title {
  font-weight: 700;
  font-size: 14px;
  line-height: 1.2;
}

.subtitle {
  font-size: 11px;
  opacity: 0.7;
  margin-top: 2px;
}

.actions {
  display: flex;
  gap: 4px;
  flex-shrink: 0;
}

.hd-btn {
  width: 32px;
  height: 32px;
  border-radius: 8px;
  border: 1px solid rgba(255, 255, 255, 0.15);
  background: rgba(255, 255, 255, 0.08);
  color: #fff;
  cursor: pointer;
  display: flex;
  align-items: center;
  justify-content: center;
  transition: background 0.15s;
}
.hd-btn:hover {
  background: rgba(255, 255, 255, 0.18);
}

/* ── Content grid ── */
.content {
  flex: 1;
  display: grid;
  grid-template-columns: 160px 1fr;
  min-height: 0;
}
.content.no-list {
  grid-template-columns: 1fr;
}
.left, .right {
  min-height: 0;
}

/* ── Left sidebar ── */
.left {
  background: #fff;
  border-right: 1px solid #e5e7eb;
  display: flex;
  flex-direction: column;
}

.search {
  padding: 10px;
  border-bottom: 1px solid #f1f5f9;
}
.search input {
  width: 100%;
  border: 1px solid #e2e8f0;
  background: #f8fafc;
  border-radius: 8px;
  padding: 8px 10px;
  outline: none;
  font-size: 12px;
  transition: border-color 0.15s;
}
.search input:focus {
  border-color: #2563eb;
}

.list {
  flex: 1;
  overflow: auto;
  padding: 4px;
}

/* ── Right panel ── */
.right {
  display: flex;
  flex-direction: column;
  background: #f8fafc;
}

/* ── Mobile ── */
@media (max-width: 460px) {
  .panel {
    width: calc(100vw - 24px);
    height: 72vh;
    min-height: 420px;
  }
  .content {
    grid-template-columns: 140px 1fr;
  }
  .content.no-list {
    grid-template-columns: 1fr;
  }
}
</style>
