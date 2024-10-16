<script setup lang="ts">
import { ref, computed, watch } from "vue";
import { DatePicker } from "v-calendar";
import { format, parse, isValid, isAfter, isBefore } from "date-fns";
import { enUS } from "date-fns/locale";
import { Button } from "@/components/ui/button";
import { Input } from "@/components/ui/input";
import {
  Popover,
  PopoverContent,
  PopoverTrigger,
} from "@/components/ui/popover";
import { Calendar } from "lucide-vue-next";
import { Label } from "@/components/ui/label";
import { useRoute } from "vue-router";

const props = defineProps<{
  initialFrom?: string;
  initialTo?: string;
}>();

const route = useRoute();

const dateRange = ref({
  start: props.initialFrom ? new Date(props.initialFrom) : new Date(),
  end: props.initialTo
    ? new Date(props.initialTo)
    : new Date(new Date().setDate(new Date().getDate() + 7)),
});

const startDateInput = ref(format(dateRange.value.start, "yyyy-MM-dd"));
const endDateInput = ref(format(dateRange.value.end, "yyyy-MM-dd"));

const startDateError = ref("");
const endDateError = ref("");

const isCalendarOpen = ref(false);

const formattedDateRange = computed(() => {
  return `${startDateInput.value} - ${endDateInput.value}`;
});

const emit = defineEmits<{
  (e: "update:filters", filters: { from: string; to: string }): void;
}>();

const validateAndUpdateDate = (type: "start" | "end", value: string) => {
  const date = parse(value, "yyyy-MM-dd", new Date());
  if (isValid(date)) {
    let adjustedDate = date;
    if (isBefore(date, new Date("1700-01-01"))) {
      adjustedDate = new Date("1700-01-01");
    }

    if (type === "start") {
      dateRange.value.start = adjustedDate;
      startDateInput.value = format(adjustedDate, "yyyy-MM-dd");
      startDateError.value = "";
    } else {
      dateRange.value.end = adjustedDate;
      endDateInput.value = format(adjustedDate, "yyyy-MM-dd");
      endDateError.value = "";
    }
    applyFilters();
  } else {
    // Set error message if input is not a valid date
    if (type === "start") {
      startDateError.value = "Invalid date. Use YYYY-MM-DD format.";
      startDateInput.value = format(dateRange.value.start, "yyyy-MM-dd");
    } else {
      endDateError.value = "Invalid date. Use YYYY-MM-DD format.";
      endDateInput.value = format(dateRange.value.end, "yyyy-MM-dd");
    }
  }
};

const applyFilters = () => {
  const filters = {
    from: format(dateRange.value.start, "yyyy-MM-dd"),
    to: format(dateRange.value.end, "yyyy-MM-dd"),
  };
  emit("update:filters", filters);
};

const attrs = {
  color: "primary",
  "is-dark": { selector: "html", darkClass: "dark" },
  "first-day-of-week": 2,
  locale: enUS,
};

// Watch for changes in route query parameters
watch(
  () => route.query,
  (newQuery) => {
    if (newQuery.from && newQuery.to) {
      startDateInput.value = newQuery.from as string;
      endDateInput.value = newQuery.to as string;
      validateAndUpdateDate("start", startDateInput.value);
      validateAndUpdateDate("end", endDateInput.value);
    }
  },
  { immediate: true }
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
            <div class="flex space-x-2 mb-3">
              <div class="flex flex-col">
                <Input
                  v-model="startDateInput"
                  type="text"
                  placeholder="YYYY-MM-DD"
                  @blur="validateAndUpdateDate('start', $event.target.value)"
                  class="w-[140px]"
                  :class="{ 'border-red-500': startDateError }"
                />
                <span v-if="startDateError" class="text-red-500 text-xs mt-1">{{
                  startDateError
                }}</span>
              </div>
              <div class="flex flex-col">
                <Input
                  v-model="endDateInput"
                  type="text"
                  placeholder="YYYY-MM-DD"
                  @blur="validateAndUpdateDate('end', $event.target.value)"
                  class="w-[140px]"
                  :class="{ 'border-red-500': endDateError }"
                />
                <span v-if="endDateError" class="text-red-500 text-xs mt-1">{{
                  endDateError
                }}</span>
              </div>
            </div>
            <DatePicker
              v-model="dateRange"
              is-range
              :columns="2"
              v-bind="attrs"
              @close="isCalendarOpen = false"
              class="vc-primary"
            />
          </div>
        </PopoverContent>
      </Popover>
    </div>
  </div>
</template>
