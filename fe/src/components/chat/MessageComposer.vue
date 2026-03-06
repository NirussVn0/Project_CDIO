<template>
  <footer class="composer" v-if="s.activeConversation">
    <input ref="fileRef" type="file" accept="image/*" class="file" @change="onPick" />

    <button class="icon" title="Gửi ảnh" @click="fileRef.click()" :disabled="s.sending">🖼️</button>

    <input
      v-model.trim="text"
      class="input"
      placeholder="Nhập tin nhắn..."
      :disabled="s.sending"
      @keydown.enter.prevent="send"
    />

    <button class="send" :disabled="!text || s.sending" @click="send">
      {{ s.sending ? "Đang gửi..." : "Gửi" }}
    </button>
  </footer>
</template>

<script setup>
import { ref } from "vue";
import { chatStore as s } from "@/store/chat.store.js";

const text = ref("");
const fileRef = ref(null);

async function send() {
  if (!text.value) return;
  const t = text.value;
  text.value = "";
  await s.sendText(t);
}

async function onPick(e) {
  const file = e.target.files?.[0];
  if (!file) return;
  await s.sendImage(file);
  e.target.value = "";
}
</script>

<style scoped>
.composer{
  display:flex;
  gap:10px;
  padding:12px;
  border-top:1px solid #e5e7eb;
  background:#fff;
}
.file{ display:none; }
.icon{
  width: 42px; height: 42px;
  border-radius: 14px;
  border:1px solid #e5e7eb;
  background:#fff;
  cursor:pointer;
}
.input{
  flex:1;
  border:1px solid #e5e7eb;
  border-radius:14px;
  padding:10px 12px;
  outline:none;
}
.send{
  border:none;
  background:#111827;
  color:#fff;
  padding:10px 14px;
  border-radius:14px;
  cursor:pointer;
}
.send:disabled, .icon:disabled{
  opacity:.5;
  cursor:not-allowed;
}
</style>
