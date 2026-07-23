<script setup lang="ts">
import AppToast from "@/components/common/AppToast.vue";
import AppConfirmDialog from "@/components/common/AppConfirmDialog.vue";
</script>

<template>
  <router-view />
  <AppToast />
  <AppConfirmDialog />
</template>