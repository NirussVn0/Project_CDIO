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
.list {
  overflow: auto;
  padding: 6px;
}

.item {
  display: flex;
  gap: 8px;
  padding: 10px 8px;
  border-radius: 10px;
  cursor: pointer;
  transition: all 0.2s cubic-bezier(.4,0,.2,1);
}
.item:hover {
  background: #f1f5f9;
  transform: translateX(2px);
}
.item.active {
  background: #e0e7ff;
  box-shadow: inset 3px 0 0 #2563eb;
}

.avatar {
  width: 34px;
  height: 34px;
  background: #f8fafc;
  border: 1px solid #e2e8f0;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 14px;
  flex-shrink: 0;
  transition: transform 0.2s;
}
.item:hover .avatar {
  transform: scale(1.08);
}

.info {
  flex: 1;
  min-width: 0;
}
.name {
  font-weight: 600;
  font-size: 12px;
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
}
.last {
  font-size: 11px;
  color: #94a3b8;
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
  margin-top: 2px;
}

.empty {
  text-align: center;
  padding: 24px 12px;
  color: #94a3b8;
  font-size: 13px;
}
</style>
