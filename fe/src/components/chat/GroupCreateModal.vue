<template>
  <div class="backdrop" @click.self="$emit('close')">
    <div class="modal">
      <header class="top">
        <h3>Tạo nhóm chat</h3>
        <button class="x" @click="$emit('close')">✕</button>
      </header>

      <div class="body">
        <label class="label">Tên nhóm</label>
        <input v-model.trim="title" class="input" placeholder="VD: Nhóm xử lý Ticket A1205" />

        <div class="sep"></div>

        <label class="label">Member IDs (ngăn cách bởi dấu phẩy)</label>
        <input v-model.trim="membersRaw" class="input" placeholder="VD: 12,15,20" />

        <p class="note">
          * Khi bạn có API danh sách người dùng, mình sẽ đổi thành UI chọn user (checkbox/search).
        </p>
      </div>

      <footer class="actions">
        <button class="btn ghost" @click="$emit('close')">Hủy</button>
        <button class="btn" :disabled="!canCreate" @click="create">
          {{ s.sending ? "Đang tạo..." : "Tạo nhóm" }}
        </button>
      </footer>
    </div>
  </div>
</template>

<script setup>
import { computed, ref } from "vue";
import { chatStore as s } from "@/store/chat.store.js";

defineEmits(["close"]);

const title = ref("");
const membersRaw = ref("");

const canCreate = computed(() => {
  const ids = parseIds(membersRaw.value);
  return title.value && ids.length > 0 && !s.sending;
});

function parseIds(v) {
  return String(v || "")
    .split(",")
    .map((x) => x.trim())
    .filter(Boolean);
}

async function create() {
  const memberIds = parseIds(membersRaw.value);
  await s.createGroup({ title: title.value, memberIds });
}
</script>

<style scoped>
.backdrop{
  position: fixed;
  inset: 0;
  background: rgba(0,0,0,.35);
  display:flex;
  align-items:center;
  justify-content:center;
  z-index: 9999;
}
.modal{
  width: 520px;
  max-width: calc(100vw - 24px);
  background:#fff;
  border-radius: 18px;
  border: 1px solid #e5e7eb;
  box-shadow: 0 18px 50px rgba(0,0,0,.25);
  overflow:hidden;
}
.top{
  padding: 14px 16px;
  display:flex;
  align-items:center;
  justify-content: space-between;
  border-bottom: 1px solid #e5e7eb;
}
.top h3{ margin:0; font-size: 15px; }
.x{ border:none; background:transparent; cursor:pointer; font-size: 16px; }
.body{ padding: 16px; }
.label{ font-size: 12px; color:#6b7280; display:block; margin-bottom: 8px; }
.input{
  width:100%;
  border: 1px solid #e5e7eb;
  border-radius: 14px;
  padding: 10px 12px;
  outline:none;
}
.sep{ height: 1px; background:#e5e7eb; margin: 14px 0; }
.note{ margin-top:10px; font-size: 12px; color:#6b7280; }
.actions{
  padding: 14px 16px;
  display:flex;
  justify-content:flex-end;
  gap: 10px;
  border-top: 1px solid #e5e7eb;
}
.btn{
  border:none;
  background:#111827;
  color:#fff;
  border-radius: 14px;
  padding: 10px 14px;
  cursor:pointer;
}
.btn:disabled{ opacity:.5; cursor:not-allowed; }
.ghost{ background:#fff; color:#111827; border:1px solid #e5e7eb; }
</style>