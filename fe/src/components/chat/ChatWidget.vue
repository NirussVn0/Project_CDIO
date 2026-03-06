<template>
  <div class="cw">
    <button class="fab" @click="toggle" :aria-label="open ? 'Đóng chat' : 'Mở chat'">
      <span v-if="!open">💬</span>
      <span v-else>✕</span>
      <span v-if="badge" class="badge">{{ badge }}</span>
    </button>

    <div v-if="open" class="panel">
      <header class="head">
        <div class="brand">
          <div class="logo">CM</div>
          <div class="meta">
            <div class="title">Hỗ trợ cư dân</div>
            <div class="sub">Phản hồi nhanh • 24/7</div>
          </div>
        </div>

        <div class="actions">
          <button class="icon" @click="toggleList" :title="showList ? 'Ẩn danh sách' : 'Hiện danh sách'">
            ☰
          </button>
          <button class="icon" @click="reload" title="Tải lại">⟲</button>
          <button class="icon" @click="open=false" title="Thu nhỏ">—</button>
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
.cw{
  position: fixed;
  right: 18px;
  bottom: 18px;
  z-index: 9999;
  font-family: ui-sans-serif, system-ui, -apple-system, Segoe UI, Roboto, Arial;
}

.fab{
  position: absolute;
  right: 0;
  bottom: 0;
  width: 58px;
  height: 58px;
  border: none;
  border-radius: 999px;
  cursor: pointer;
  color: #fff;
  font-size: 18px;
  background:
    radial-gradient(90px 90px at 30% 20%, rgba(255,255,255,.22), transparent 60%),
    linear-gradient(135deg, #2563eb, #0f172a);
  box-shadow: 0 24px 60px rgba(2,6,23,.35);
  display:flex;
  align-items:center;
  justify-content:center;
}
.badge{
  position:absolute;
  top: -6px;
  right: -6px;
  min-width: 18px;
  height: 18px;
  padding: 0 6px;
  border-radius: 999px;
  background:#ef4444;
  color:#fff;
  font-size: 11px;
  display:flex;
  align-items:center;
  justify-content:center;
  border: 2px solid #fff;
}

.panel{
  width: 400px;
  height: min(72vh, 540px);
  min-height: 420px;
  margin-bottom: 64px;
  border-radius: 22px;
  overflow: hidden;
  background: #fff;
  border: 1px solid rgba(148,163,184,.35);
  box-shadow: 0 30px 90px rgba(2,6,23,.35);
  display:flex;
  flex-direction: column;
}

.head{
  padding: 12px 12px;
  color:#fff;
  background:
    radial-gradient(800px 240px at 10% -20%, rgba(59,130,246,.55), transparent 60%),
    radial-gradient(700px 240px at 120% -10%, rgba(14,165,233,.45), transparent 60%),
    linear-gradient(135deg, #0b1220, #0f172a);
  display:flex;
  align-items:center;
  justify-content: space-between;
}
.brand{ display:flex; align-items:center; gap:10px; min-width:0; }
.logo{
  width: 38px; height: 38px;
  border-radius: 14px;
  background: rgba(255,255,255,.12);
  display:flex; align-items:center; justify-content:center;
  font-weight: 900;
}
.meta{ min-width:0; }
.title{ font-weight: 900; font-size: 13.5px; }
.sub{ font-size: 11px; opacity:.85; margin-top: 2px; }

.actions{ display:flex; gap:8px; }
.icon{
  width: 36px; height: 36px;
  border-radius: 14px;
  border: 1px solid rgba(255,255,255,.18);
  background: rgba(255,255,255,.10);
  color:#fff;
  cursor:pointer;
}

.content{
  flex:1;
  display:grid;
  grid-template-columns: 165px 1fr;
  min-height: 0;
}
.content.no-list{
  grid-template-columns: 1fr;
}
.left, .right{ min-height: 0; }

.left{
  background: #fff;
  border-right: 1px solid rgba(148,163,184,.35);
  display:flex;
  flex-direction: column;
}
.search{
  padding: 10px;
  border-bottom: 1px solid rgba(148,163,184,.25);
  background: #fff;
}
.search input{
  width:100%;
  border: 1px solid rgba(148,163,184,.45);
  background: #f8fafc;
  border-radius: 14px;
  padding: 9px 10px;
  outline:none;
  font-size: 12px;
}
.list{ flex:1; overflow:auto; padding: 6px; }

.right{
  display:flex;
  flex-direction: column;
  background:
    radial-gradient(900px 500px at 80% 0%, rgba(59,130,246,.08), transparent 60%),
    radial-gradient(700px 500px at 0% 100%, rgba(14,165,233,.06), transparent 60%),
    #f8fafc;
}

@media (max-width: 460px){
  .panel{
    width: calc(100vw - 24px);
    height: 72vh;
    min-height: 420px;
  }
  .content{ grid-template-columns: 150px 1fr; }
  .content.no-list{ grid-template-columns: 1fr; }
}
</style>
