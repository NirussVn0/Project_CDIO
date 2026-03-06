<template>
  <div class="row" :class="{ me: isMe }">
    <div class="bubble">
      <div class="head">
        <span class="sender">{{ senderName }}</span>
        <span class="time">{{ time }}</span>

        <div class="menu-wrap">
          <button class="menu" @click="open = !open">⋯</button>
          <div v-if="open" class="dropdown" @mouseleave="open=false">
            <button @click="onDeleteMe">Xóa ở phía tôi</button>
            <button @click="onDeleteAll">Xóa cho mọi người</button>
            <button @click="onRecall">Thu hồi</button>
          </div>
        </div>
      </div>

      <div class="body">
        <div v-if="message.recalled_at" class="recalled">Tin nhắn đã bị thu hồi</div>

        <template v-else>
          <p v-if="message.type === 'text'" class="text">{{ message.content }}</p>

          <div v-else-if="message.type === 'image'" class="img">
            <img :src="message.image_url" alt="image" />
          </div>

          <p v-else class="text">{{ message.content }}</p>
        </template>
      </div>
    </div>
  </div>
</template>

<script setup>
import { computed, ref } from "vue";
import { chatStore as s } from "@/store/chat.store.js";

const props = defineProps({
  message: { type: Object, required: true },
});

const open = ref(false);

const isMe = computed(() => !!props.message.is_me);
const senderName = computed(() => props.message.sender_name || "User");
const time = computed(() => {
  const t = props.message.created_at || props.message.time;
  if (!t) return "";
  const d = new Date(t);
  return d.toLocaleTimeString([], { hour: "2-digit", minute: "2-digit" });
});

async function onRecall() {
  open.value = false;
  await s.recallMessage(props.message.id);
}
async function onDeleteMe() {
  open.value = false;
  await s.deleteForMe(props.message.id);
}
async function onDeleteAll() {
  open.value = false;
  await s.deleteForAll(props.message.id);
}
</script>

<style scoped>
.row {
  display: flex;
  margin: 8px 0;
  animation: msgAppear 0.3s cubic-bezier(.16,1,.3,1) both;
}
.row.me { justify-content: flex-end; }

.bubble {
  max-width: 78%;
  background: #fff;
  border: 1px solid #e5e7eb;
  border-radius: 16px;
  padding: 10px 12px;
  box-shadow: 0 2px 8px rgba(0,0,0,.04);
  transition: box-shadow 0.2s;
}
.bubble:hover {
  box-shadow: 0 4px 16px rgba(0,0,0,.08);
}
.row.me .bubble {
  background: #0f172a;
  color: #fff;
  border-color: #0f172a;
}

.head {
  display: flex;
  align-items: center;
  gap: 8px;
  font-size: 11px;
  opacity: .8;
}
.sender { font-weight: 700; }
.time { margin-left: auto; font-size: 10px; }

.menu-wrap { position: relative; }
.menu {
  border: none;
  background: transparent;
  cursor: pointer;
  color: inherit;
  padding: 2px 6px;
  border-radius: 6px;
  opacity: 0;
  transition: opacity 0.15s;
}
.bubble:hover .menu { opacity: 1; }

.dropdown {
  position: absolute;
  right: 0;
  top: 22px;
  background: #fff;
  border: 1px solid #e5e7eb;
  border-radius: 10px;
  overflow: hidden;
  min-width: 160px;
  z-index: 50;
  box-shadow: 0 10px 30px rgba(0,0,0,.12);
  animation: dropIn 0.2s cubic-bezier(.16,1,.3,1);
}
.row.me .dropdown { color: #0f172a; }
.dropdown button {
  width: 100%;
  text-align: left;
  border: none;
  background: #fff;
  padding: 9px 12px;
  cursor: pointer;
  font-size: 12px;
  transition: background 0.15s;
}
.dropdown button:hover { background: #f1f5f9; }

.body { margin-top: 6px; }
.text { margin: 0; font-size: 13px; line-height: 1.5; white-space: pre-wrap; }
.recalled { font-size: 12px; opacity: .7; font-style: italic; }
.img img {
  width: 240px;
  max-width: 100%;
  border-radius: 10px;
  border: 1px solid rgba(0,0,0,.06);
  display: block;
  transition: transform 0.2s;
}
.img img:hover { transform: scale(1.02); }

@keyframes msgAppear {
  from {
    opacity: 0;
    transform: translateY(6px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}
@keyframes dropIn {
  from {
    opacity: 0;
    transform: translateY(-4px) scale(0.95);
  }
  to {
    opacity: 1;
    transform: translateY(0) scale(1);
  }
}
</style>
