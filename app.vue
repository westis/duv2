<template>
  <NuxtLayout>
    <NuxtPage />
  </NuxtLayout>
</template>

<script setup lang="ts">
useHead({
  titleTemplate: "%s - DUV Ultramarathon Statistics",
  meta: [
    {
      name: "description",
      content:
        "DUV Ultramarathon Statistics - Comprehensive data on ultramarathon events worldwide",
    },
  ],
  link: [{ rel: "icon", type: "image/x-icon", href: "/favicon.ico" }],
});
</script>
