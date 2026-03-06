<template>
  <div class="cw">
    <button class="fab" @click="toggle" :aria-label="open ? 'Đóng chat' : 'Mở chat'">
      <span class="fab-icon" :class="{ 'fab-open': open }">{{ open ? '✕' : '💬' }}</span>
      <span v-if="badge" class="badge">{{ badge }}</span>
    </button>

    <Transition name="slide-up">
      <div v-if="open" class="panel">
        <header class="head">
          <div class="brand">
            <div class="logo">💬</div>
            <div class="meta">
              <div class="h-title">Hỗ trợ cư dân</div>
              <div class="h-sub">Phản hồi nhanh • 24/7</div>
            </div>
          </div>

          <div class="actions">
            <button class="hd-btn" @click="toggleList" :title="showList ? 'Ẩn danh sách' : 'Hiện danh sách'">☰</button>
            <button class="hd-btn" @click="reload" title="Tải lại">↻</button>
            <button class="hd-btn" @click="open=false" title="Thu nhỏ">—</button>
          </div>
        </header>

        <div class="content" :class="{ 'no-list': !showList }">
          <Transition name="slide-left">
            <aside v-if="showList" class="left">
              <div class="search">
                <input
                  v-model.trim="s.search"
                  placeholder="🔍 Tìm hội thoại..."
                  @keydown.enter="reload"
                />
              </div>
              <div class="list">
                <ConversationList />
              </div>
            </aside>
          </Transition>

          <main class="right">
            <ChatWindow />
          </main>
        </div>
      </div>
    </Transition>
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
  font-family: 'Segoe UI', system-ui, -apple-system, sans-serif;
}

/* ── FAB ── */
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
  box-shadow: 0 8px 28px rgba(37, 99, 235, 0.4);
  display: flex;
  align-items: center;
  justify-content: center;
  transition: transform 0.25s cubic-bezier(.4,0,.2,1), box-shadow 0.25s;
}
.fab:hover {
  transform: scale(1.1);
  box-shadow: 0 12px 36px rgba(37, 99, 235, 0.55);
}
.fab:active {
  transform: scale(0.95);
}
.fab-icon {
  font-size: 22px;
  line-height: 1;
  transition: transform 0.3s;
}
.fab-close {
  font-size: 20px;
  font-style: normal;
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
  border: 1px solid rgba(148, 163, 184, 0.2);
  box-shadow: 0 20px 60px rgba(2, 6, 23, 0.22), 0 0 0 1px rgba(0,0,0,.04);
  display: flex;
  flex-direction: column;
}

/* ── Header ── */
.head {
  padding: 12px 14px;
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
  font-size: 18px;
  flex-shrink: 0;
}
.meta { min-width: 0; }
.h-title {
  font-weight: 700;
  font-size: 14px;
  line-height: 1.2;
}
.h-sub {
  font-size: 11px;
  opacity: 0.65;
  margin-top: 2px;
}

/* ── Action Buttons ── */
.actions {
  display: flex;
  gap: 4px;
  flex-shrink: 0;
}
.hd-btn {
  width: 34px;
  height: 34px;
  border-radius: 10px;
  border: 1px solid rgba(255, 255, 255, 0.15);
  background: rgba(255, 255, 255, 0.08);
  color: #fff;
  cursor: pointer;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 16px;
  line-height: 1;
  transition: background 0.2s, transform 0.15s;
}
.hd-btn:hover {
  background: rgba(255, 255, 255, 0.2);
  transform: scale(1.08);
}
.hd-btn:active {
  transform: scale(0.92);
}

/* ── Content Grid ── */
.content {
  flex: 1;
  display: grid;
  grid-template-columns: 160px 1fr;
  min-height: 0;
}
.content.no-list {
  grid-template-columns: 1fr;
}
.left, .right { min-height: 0; }

/* ── Left Sidebar ── */
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
  border-radius: 10px;
  padding: 8px 10px;
  outline: none;
  font-size: 12px;
  transition: border-color 0.2s, box-shadow 0.2s;
}
.search input:focus {
  border-color: #2563eb;
  box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.1);
}
.list { flex: 1; overflow: auto; padding: 4px; }

/* ── Right Panel ── */
.right {
  display: flex;
  flex-direction: column;
  background: #f8fafc;
}

/* ── Animations ── */

/* Panel slide up */
.slide-up-enter-active {
  animation: slideUp 0.35s cubic-bezier(.16,1,.3,1);
}
.slide-up-leave-active {
  animation: slideUp 0.25s cubic-bezier(.4,0,1,1) reverse;
}
@keyframes slideUp {
  from {
    opacity: 0;
    transform: translateY(20px) scale(0.96);
  }
  to {
    opacity: 1;
    transform: translateY(0) scale(1);
  }
}

/* FAB bounce */
.bounce-enter-active {
  animation: popIn 0.3s cubic-bezier(.16,1,.3,1);
}
.bounce-leave-active {
  animation: popIn 0.2s cubic-bezier(.4,0,1,1) reverse;
}
@keyframes popIn {
  from {
    opacity: 0;
    transform: scale(0.5) rotate(-15deg);
  }
  to {
    opacity: 1;
    transform: scale(1) rotate(0deg);
  }
}

/* Sidebar slide */
.slide-left-enter-active {
  animation: slideLeft 0.25s cubic-bezier(.16,1,.3,1);
}
.slide-left-leave-active {
  animation: slideLeft 0.2s cubic-bezier(.4,0,1,1) reverse;
}
@keyframes slideLeft {
  from {
    opacity: 0;
    transform: translateX(-20px);
  }
  to {
    opacity: 1;
    transform: translateX(0);
  }
}

/* ── Mobile ── */
@media (max-width: 460px) {
  .panel {
    width: calc(100vw - 24px);
    height: 72vh;
    min-height: 420px;
  }
  .content { grid-template-columns: 140px 1fr; }
  .content.no-list { grid-template-columns: 1fr; }
}
</style>
