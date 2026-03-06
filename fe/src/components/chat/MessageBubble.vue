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
.row{ display:flex; margin: 10px 0; }
.row.me{ justify-content:flex-end; }
.bubble{
  max-width: 78%;
  background:#fff;
  border:1px solid #e5e7eb;
  border-radius:16px;
  padding:10px 12px;
  box-shadow: 0 6px 18px rgba(0,0,0,.05);
}
.row.me .bubble{
  background:#111827;
  color:#fff;
  border-color:#111827;
}
.head{
  display:flex;
  align-items:center;
  gap:10px;
  font-size:11px;
  opacity:.85;
}
.sender{ font-weight:800; }
.time{ margin-left:auto; }
.menu-wrap{ position:relative; }
.menu{
  border:none;
  background:transparent;
  cursor:pointer;
  color: inherit;
  padding:2px 6px;
  border-radius:8px;
}
.dropdown{
  position:absolute;
  right:0;
  top:22px;
  background:#fff;
  border:1px solid #e5e7eb;
  border-radius:12px;
  overflow:hidden;
  min-width: 170px;
  z-index: 50;
  box-shadow: 0 14px 30px rgba(0,0,0,.12);
}
.row.me .dropdown{ color:#111827; }
.dropdown button{
  width:100%;
  text-align:left;
  border:none;
  background:#fff;
  padding:10px 12px;
  cursor:pointer;
  font-size:12px;
}
.dropdown button:hover{ background:#f3f4f6; }
.body{ margin-top: 8px; }
.text{ margin:0; font-size:13px; line-height:1.45; white-space:pre-wrap; }
.recalled{ font-size:12px; opacity:.8; font-style:italic; }
.img img{
  width: 240px;
  max-width: 100%;
  border-radius: 12px;
  border: 1px solid rgba(0,0,0,.08);
  display:block;
}
</style>
