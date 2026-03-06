<template>
  <div class="page">
    <aside class="sidebar">
      <div class="top">
        <div class="title">
          <h2>Chat</h2>
          <p>Admin • Kỹ thuật • Khách hàng</p>
        </div>

        <button class="btn" @click="showGroup = true">+ Tạo nhóm</button>
      </div>

      <div class="search">
        <input v-model.trim="s.search" placeholder="Tìm hội thoại..." @keydown.enter="reloadConvs" />
        <button class="btn ghost" @click="reloadConvs">Tìm</button>
      </div>

      <ConversationList />
    </aside>

    <main class="main">
      <ChatWindow />
    </main>

    <GroupCreateModal v-if="showGroup" @close="showGroup=false" />
  </div>
</template>

<script setup>
import { onMounted, ref } from "vue";
import { chatStore as s } from "@/store/chat.store.js";
import ConversationList from "./ConversationList.vue";
import ChatWindow from "./ChatWindow.vue";
import GroupCreateModal from "./GroupCreateModal.vue";

const showGroup = ref(false);

async function reloadConvs() {
  await s.fetchConversations({ reset: true });
}

onMounted(async () => {
  await s.fetchConversations({ reset: true });
});
</script>

<style scoped>
.page{
  height: calc(100vh - 0px);
  display:grid;
  grid-template-columns: 340px 1fr;
  background:#f6f7fb;
}
.sidebar{
  background:#fff;
  border-right:1px solid #e5e7eb;
  display:flex;
  flex-direction:column;
}
.top{
  padding:14px;
  display:flex;
  align-items:center;
  justify-content:space-between;
  gap:12px;
}
.title h2{ margin:0; font-size:16px; }
.title p{ margin:4px 0 0; font-size:12px; color:#6b7280; }
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
.search{
  padding: 0 14px 12px;
  display:flex;
  gap:10px;
}
.search input{
  flex:1;
  border:1px solid #e5e7eb;
  border-radius:12px;
  padding:10px 12px;
  outline:none;
}
.main{ display:flex; flex-direction:column; }
@media (max-width: 900px){
  .page{ grid-template-columns: 1fr; }
  .sidebar{ display:none; }
}
</style>
