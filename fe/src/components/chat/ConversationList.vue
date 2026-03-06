<template>
  <div class="list">
    <div
      v-for="c in s.conversations"
      :key="c.id"
      class="item"
      :class="{ active: s.activeConversation?.id === c.id }"
      @click="s.setActiveConversation(c)"
    >
      <div class="avatar">💬</div>

      <div class="info">
        <div class="name">
          {{ c.title || "Hội thoại" }}
        </div>

        <div class="last">
          {{ c.last_message_preview || "Chưa có tin nhắn" }}
        </div>
      </div>
    </div>

    <div v-if="!s.conversations.length" class="empty">
      Chưa có hội thoại
    </div>
  </div>
</template>

<script setup>
import { chatStore as s } from "@/store/chat.store.js";
</script>

<style scoped>
.list{
  overflow:auto;
  padding:8px;
}

.item{
  display:flex;
  gap:10px;
  padding:10px;
  border-radius:10px;
  cursor:pointer;
}

.item:hover{
  background:#f3f4f6;
}

.item.active{
  background:#e0e7ff;
}

.avatar{
  width:36px;
  height:36px;
  background:#fff;
  border:1px solid #ddd;
  border-radius:50%;
  display:flex;
  align-items:center;
  justify-content:center;
}

.info{
  flex:1;
}

.name{
  font-weight:600;
  font-size:13px;
}

.last{
  font-size:12px;
  color:#6b7280;
}

.empty{
  text-align:center;
  padding:20px;
  color:#888;
}
</style>
