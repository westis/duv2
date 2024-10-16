<script setup lang="ts">
import { useEventTypeMapper } from "~/composables/useEventTypeMapper";

const { data: events } = await useFetch("/api/events");

const { mapEventType } = useEventTypeMapper();

const mappedEvents = computed(() => {
  if (!events.value) return [];
  return events.value.map((event) => ({
    ...event,
    mappedType: mapEventType(event),
  }));
});
</script>

<template>
  <div>
    <h2>Events</h2>
    <ul>
      <li v-for="event in mappedEvents" :key="event.EventID">
        {{ event.EventName }} - Type: {{ event.mappedType.type }}, Surface:
        {{ event.mappedType.surface }}
      </li>
    </ul>
  </div>
</template>
