<template>
  <div class="flex min-h-screen flex-col">
    <Navbar />
    <main class="flex-grow">
      <NuxtPage />
    </main>
    <footer class="bg-background py-6 text-foreground">
      <!-- Footer content can be added here -->
      <div class="container mx-auto text-center">
        <p>
          &copy; {{ new Date().getFullYear() }} DUV Ultramarathon Statistics
        </p>
      </div>
    </footer>
  </div>
</template>

<script setup lang="ts">
// No setup logic needed for now
</script>
