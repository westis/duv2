<template>
  <section
    class="mx-auto flex max-w-[980px] flex-col items-center gap-2 py-8 md:py-12 md:pb-8 lg:py-24 lg:pb-20"
  >
    <h1
      class="text-center text-4xl font-bold leading-tight tracking-tighter md:text-5xl lg:leading-[1.1] mb-4"
    >
      DUV Ultramarathon Statistics
    </h1>
    <p
      class="max-w-[750px] text-center text-lg font-light text-foreground mb-8"
    >
      Discover comprehensive statistics, results, and rankings from
      {{ eventCount }} ultramarathon events, featuring
      {{ performanceCount }} performances by {{ runnerCount }} runners
      worldwide.
    </p>
    <div class="flex flex-wrap justify-center gap-4">
      <NuxtLink
        v-for="link in links"
        :key="link.text"
        :to="link.to"
        class="px-4 py-2 rounded-md bg-primary text-primary-foreground hover:bg-primary/90 transition-colors"
      >
        {{ link.text }}
      </NuxtLink>
    </div>
  </section>
</template>

<script setup lang="ts">
const links = [
  { text: "Calendar", to: "/calendar" },
  { text: "Results", to: "/results" },
  { text: "Toplists", to: "/toplists" },
  { text: "Records", to: "/records" },
];

// These numbers are placeholders. You should replace them with actual data or fetch them from an API
const eventCount = "100,523";
const performanceCount = "8,865,545";
const runnerCount = "2,115,800";
</script>
