<script setup lang="ts">
import { ref, watch, onMounted } from "vue";
import { useRoute, useRouter } from "vue-router";
import EventList from "@/components/EventList.vue";
import EventFilters from "@/components/EventFilters.vue";

interface Event {
  EventID: string;
  EventName: string;
  City: string;
  Country: string;
  Startdate: string;
  Length: string;
  Duration: string;
  EventType: string;
  Results: string;
  IAULabel: string;
}

const route = useRoute();
const router = useRouter();

const page = ref(Number(route.query.page) || 1);
const perPage = ref(20);
const events = ref<Event[]>([]);
const total = ref(0);
const loading = ref(true);
const filters = ref({
  from: (route.query.from as string) || undefined,
  to: (route.query.to as string) || undefined,
  order: (route.query.order as string) || "desc",
});

const fetchEvents = async () => {
  loading.value = true;
  try {
    const { data } = await useFetch<{ events: Event[]; total: number }>(
      "/api/events",
      {
        query: {
          page: page.value,
          perpage: perPage.value,
          ...filters.value,
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
  (newQuery) => {
    page.value = Number(newQuery.page) || 1;
    filters.value = {
      from: (newQuery.from as string) || undefined,
      to: (newQuery.to as string) || undefined,
      order: (newQuery.order as string) || "desc",
    };
    fetchEvents();
  },
  { immediate: true }
);

const handlePageChange = (newPage: number) => {
  router.push({ query: { ...route.query, page: newPage } });
};

const handleFiltersChange = (newFilters: any) => {
  filters.value = { ...filters.value, ...newFilters };
  page.value = 1;
  router.push({ query: { ...filters.value, page: 1 } });
};

onMounted(() => {
  if (!route.query.from || !route.query.to) {
    const today = new Date();
    const oneYearLater = new Date(
      today.getFullYear() + 1,
      today.getMonth(),
      today.getDate()
    );
    handleFiltersChange({
      from: today.toISOString().split("T")[0],
      to: oneYearLater.toISOString().split("T")[0],
    });
  }
});
</script>

<template>
  <div class="container mx-auto px-4 py-8">
    <h1 class="text-3xl font-bold mb-6">Events</h1>

    <EventFilters
      :initial-from="filters.from"
      :initial-to="filters.to"
      :initial-order="filters.order"
      @update:filters="handleFiltersChange"
    />

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
