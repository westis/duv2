<script setup lang="ts">
import { computed } from "vue";
import { Card, CardContent } from "@/components/ui/card";
import { Button } from "@/components/ui/button";
import { Badge } from "@/components/ui/badge";
import {
  MapPinIcon,
  RulerIcon,
  ClockIcon,
  TimerIcon,
  InfoIcon,
  BarChartIcon,
} from "lucide-vue-next";
import {
  Pagination,
  PaginationList,
  PaginationListItem,
} from "@/components/ui/pagination";
import {
  PaginationFirst,
  PaginationLast,
  PaginationNext,
  PaginationPrev,
  PaginationEllipsis,
} from "@/components/ui/pagination";
import { useEventTypeMapper } from "@/composables/useEventTypeMapper";

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

const props = defineProps<{
  events: Event[];
  loading: boolean;
  total: number;
  page: number;
  perPage: number;
}>();

const emit = defineEmits<{
  (e: "update:page", page: number): void;
}>();

const totalPages = computed(() => Math.ceil(props.total / props.perPage));

const pageNumbers = computed(() => {
  const pages = [];
  const start = Math.max(1, props.page - 2);
  const end = Math.min(totalPages.value, props.page + 2);

  for (let i = start; i <= end; i++) {
    pages.push(i);
  }

  return pages;
});

const { mapEventType, getEventTypeColor, getSurfaceColor, getIAULabelColor } =
  useEventTypeMapper();
</script>

<template>
  <div v-if="loading" class="text-center py-8">Loading events...</div>
  <div v-else-if="events.length === 0" class="text-center py-8">
    No events found.
  </div>
  <div v-else class="space-y-4">
    <Card v-for="event in events" :key="event.EventID">
      <CardContent class="p-0">
        <div class="flex flex-col sm:flex-row">
          <div
            class="sm:w-40 p-4 bg-gray-50 dark:bg-gray-800 flex flex-col justify-center items-start"
          >
            <div
              class="text-base font-semibold text-gray-800 dark:text-gray-200"
            >
              {{ event.Startdate }}
            </div>
            <div
              class="flex items-center text-sm text-gray-600 dark:text-gray-400 mt-1"
            >
              <MapPinIcon class="h-4 w-4 mr-1" />
              {{ event.City }}, {{ event.Country }}
            </div>
          </div>
          <div class="flex-grow p-4">
            <div class="flex items-center justify-between mb-2">
              <div class="flex items-center">
                <h3
                  class="text-lg font-semibold text-gray-800 dark:text-gray-200 mr-2"
                >
                  {{ event.EventName }}
                </h3>
                <Badge
                  v-if="['G', 'S', 'B'].includes(event.IAULabel)"
                  :class="[
                    getIAULabelColor(event.IAULabel),
                    'text-xs px-2 py-1 rounded-full',
                  ]"
                >
                  IAU {{ event.IAULabel }}
                </Badge>
              </div>
            </div>
            <div class="flex flex-wrap gap-2 mb-3">
              <Badge
                variant="secondary"
                :class="[
                  getEventTypeColor(event),
                  'text-xs px-2 py-1 rounded-full',
                ]"
              >
                <template
                  v-if="
                    ['Backyard Ultra', 'Elimination Race'].includes(
                      mapEventType(event).type
                    )
                  "
                >
                  <TimerIcon class="h-3 w-3 mr-1 inline" />
                  <span>{{ mapEventType(event).type }}</span>
                </template>
                <template v-else>
                  <RulerIcon v-if="event.Length" class="h-3 w-3 mr-1 inline" />
                  <ClockIcon v-else class="h-3 w-3 mr-1 inline" />
                  <span>{{ event.Length || event.Duration }}</span>
                </template>
              </Badge>
              <Badge
                v-if="
                  mapEventType(event).type === 'Stage Race' ||
                  mapEventType(event).surface !== 'Unknown'
                "
                variant="secondary"
                :class="[
                  getSurfaceColor(event),
                  'text-xs px-2 py-1 rounded-full',
                ]"
              >
                {{
                  mapEventType(event).type === "Stage Race"
                    ? "Stage Race"
                    : mapEventType(event).surface
                }}
              </Badge>
            </div>
          </div>
          <div
            class="flex sm:flex-col justify-end p-4 space-y-2 space-x-2 sm:space-x-0"
          >
            <Button class="flex-1 sm:w-full" variant="outline">
              <InfoIcon class="h-4 w-4 mr-2" />
              Details
            </Button>
            <Button class="flex-1 sm:w-full">
              <BarChartIcon class="h-4 w-4 mr-2" />
              Results
            </Button>
          </div>
        </div>
      </CardContent>
    </Card>

    <div class="mt-4">
      <Pagination>
        <PaginationList class="flex items-center justify-center gap-1">
          <PaginationListItem :value="1">
            <PaginationFirst
              @click="$emit('update:page', 1)"
              :disabled="page === 1"
            />
          </PaginationListItem>
          <PaginationListItem :value="Math.max(1, page - 1)">
            <PaginationPrev
              @click="$emit('update:page', page - 1)"
              :disabled="page === 1"
            />
          </PaginationListItem>

          <template v-for="pageNum in pageNumbers" :key="pageNum">
            <PaginationListItem :value="pageNum">
              <Button
                class="w-10 h-10 p-0 text-base"
                :variant="pageNum === page ? 'default' : 'outline'"
                @click="$emit('update:page', pageNum)"
              >
                {{ pageNum }}
              </Button>
            </PaginationListItem>
          </template>

          <PaginationListItem v-if="page < totalPages - 2" :value="page + 3">
            <PaginationEllipsis />
          </PaginationListItem>

          <PaginationListItem :value="Math.min(totalPages, page + 1)">
            <PaginationNext
              @click="$emit('update:page', page + 1)"
              :disabled="page === totalPages"
            />
          </PaginationListItem>
          <PaginationListItem :value="totalPages">
            <PaginationLast
              @click="$emit('update:page', totalPages)"
              :disabled="page === totalPages"
            />
          </PaginationListItem>
        </PaginationList>
      </Pagination>
    </div>
  </div>
</template>
