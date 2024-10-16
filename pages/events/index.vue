<script setup lang="ts">
import { ref, watch } from "vue";
import { useRoute, useRouter } from "vue-router";
import EventList from "@/components/events/EventList.vue";
import EventFilter from "@/components/events/EventFilter.vue";

interface Event {
  EventID: string;
  EventName: string;
  City: string;
  Country: string;
  Startdate: string;
  Length: string;
  EventType: string;
  Results: string;
}

const route = useRoute();
const router = useRouter();

const page = ref(Number(route.query.page) || 1);
const perPage = ref(20);
const events = ref<Event[]>([]);
const total = ref(0);
const loading = ref(true);

const fetchEvents = async () => {
  loading.value = true;
  try {
    const { data } = await useFetch<{ events: Event[]; total: number }>(
      "/api/events",
      {
        query: {
          page: page.value,
          perpage: perPage.value,
        },
      }
    );
    events.value = data.value?.events || [];
    total.value = data.value?.total || 0;
  } catch (error) {
    console.error("Error fetching events:", error);
  } finally {
    loading.value = false;
  }
};

watch(
  () => route.query,
  () => {
    page.value = Number(route.query.page) || 1;
    fetchEvents();
  },
  { immediate: true }
);

const handlePageChange = (newPage: number) => {
  router.push({ query: { ...route.query, page: newPage } });
};
</script>

<template>
  <div class="container mx-auto px-4 py-8">
    <h1 class="text-3xl font-bold mb-6">Upcoming Events</h1>

    <EventFilter />

    <EventList
      :events="events"
      :loading="loading"
      :total="total"
      :page="page"
      :per-page="perPage"
      @update:page="handlePageChange"
    />
  </div>
</template>
