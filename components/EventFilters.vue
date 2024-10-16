<script setup lang="ts">
import { ref, computed, watch } from "vue";
import type { DateRange } from "radix-vue";
import { RangeCalendar } from "@/components/ui/range-calendar";
import { getLocalTimeZone, today, parseDate } from "@internationalized/date";
import { Button } from "@/components/ui/button";
import {
  Popover,
  PopoverContent,
  PopoverTrigger,
} from "@/components/ui/popover";
import { Calendar } from "lucide-vue-next";
import { Label } from "@/components/ui/label";

const props = defineProps<{
  initialFrom?: string;
  initialTo?: string;
}>();

const dateRange = ref<DateRange>({
  start: props.initialFrom
    ? parseDate(props.initialFrom)
    : today(getLocalTimeZone()),
  end: props.initialTo
    ? parseDate(props.initialTo)
    : today(getLocalTimeZone()).add({ days: 7 }),
});

const isCalendarOpen = ref(false);

const formattedDateRange = computed(() => {
  if (dateRange.value?.start && dateRange.value?.end) {
    const start = formatDate(dateRange.value.start);
    const end = formatDate(dateRange.value.end);
    return `${start} - ${end}`;
  }
  return "Pick a date range";
});

const formatDate = (date: any) => {
  return date.toString().split("T")[0];
};

const emit = defineEmits<{
  (e: "update:filters", filters: { from: string; to: string }): void;
}>();

const applyFilters = () => {
  const filters = {
    from: formatDate(dateRange.value.start),
    to: formatDate(dateRange.value.end),
  };
  emit("update:filters", filters);
};

watch(
  dateRange,
  () => {
    applyFilters();
  },
  { deep: true }
);

// Add this watch effect
watch(
  () => [props.initialFrom, props.initialTo],
  ([newFrom, newTo]) => {
    if (newFrom && newTo) {
      dateRange.value = {
        start: parseDate(newFrom),
        end: parseDate(newTo),
      };
    }
  }
);
</script>

<template>
  <div class="space-y-4">
    <div class="flex flex-col space-y-2">
      <Label for="date-range">Date Range</Label>
      <Popover v-model:open="isCalendarOpen">
        <PopoverTrigger as-child>
          <Button
            id="date-range"
            variant="outline"
            class="w-[280px] justify-start text-left font-normal"
          >
            <Calendar class="mr-2 h-4 w-4" />
            <span>{{ formattedDateRange }}</span>
          </Button>
        </PopoverTrigger>
        <PopoverContent class="w-auto p-0">
          <div class="p-3">
            <RangeCalendar v-model="dateRange" class="rounded-md border" />
          </div>
        </PopoverContent>
      </Popover>
    </div>
  </div>
</template>
