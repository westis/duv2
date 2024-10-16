<script setup lang="ts">
import { computed } from "vue";
import {
  Table,
  TableBody,
  TableCell,
  TableHead,
  TableHeader,
  TableRow,
} from "@/components/ui/table";
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
import { Button } from "@/components/ui/button";
import { Badge } from "@/components/ui/badge";
import {
  CalendarIcon,
  MapPinIcon,
  RulerIcon,
  ClockIcon,
} from "lucide-vue-next";

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

const getTypeColor = (typeNumber: string) => {
  const colors = [
    "bg-neutral-200/80 dark:bg-neutral-800/80 text-neutral-800 dark:text-neutral-200",
    "bg-green-200/80 dark:bg-green-800/80 text-green-800 dark:text-green-200",
    "bg-neutral-300/80 dark:bg-neutral-700/80 text-neutral-700 dark:text-neutral-300",
    "bg-pink-200/80 dark:bg-pink-800/80 text-pink-800 dark:text-pink-200",
    "bg-orange-200/80 dark:bg-orange-800/80 text-orange-800 dark:text-orange-200",
    "bg-indigo-200/80 dark:bg-indigo-800/80 text-indigo-800 dark:text-indigo-200",
    "bg-purple-200/80 dark:bg-purple-800/80 text-purple-800 dark:text-purple-200",
    "bg-yellow-200/80 dark:bg-yellow-800/80 text-yellow-800 dark:text-yellow-200",
    "bg-teal-200/80 dark:bg-teal-800/80 text-teal-800 dark:text-teal-200",
    "bg-cyan-200/80 dark:bg-cyan-800/80 text-cyan-800 dark:text-cyan-200",
    "bg-neutral-300/80 dark:bg-neutral-700/80 text-neutral-700 dark:text-neutral-300",
    "bg-neutral-200/80 dark:bg-neutral-800/80 text-neutral-800 dark:text-neutral-200",
    "bg-orange-200/80 dark:bg-orange-800/80 text-orange-800 dark:text-orange-200",
    "bg-indigo-200/80 dark:bg-indigo-800/80 text-indigo-800 dark:text-indigo-200",
  ];
  const index = parseInt(typeNumber) - 1;
  return colors[index] || colors[0];
};

const getDurationLengthColor = (event: Event) => {
  return event.Length
    ? "bg-sky-200/80 dark:bg-sky-800/80 text-sky-800 dark:text-sky-200"
    : "bg-amber-200/80 dark:bg-amber-800/80 text-amber-800 dark:text-amber-200";
};

const typeNameMap = {
  "1": "Road",
  "2": "Trail",
  "3": "Road Loop",
  "4": "Stage",
  "5": "Track",
  "6": "Indoor",
  "7": "Friendship",
  "8": "Invitational",
  "9": "Elimination Race",
  "10": "Backyard Ultra",
  "11": "Walk (Road)",
  "12": "Walk Loop",
  "13": "Walk (Track)",
  "14": "Walk (Indoor)",
};

const getEventType = (typeNumber: string) => {
  return typeNameMap[typeNumber as keyof typeof typeNameMap] || "Unknown";
};
</script>

<template>
  <div v-if="loading" class="text-center py-8">Loading events...</div>
  <div v-else-if="events.length === 0" class="text-center py-8">
    No events found.
  </div>
  <div v-else>
    <div class="overflow-x-auto">
      <Table>
        <TableHeader>
          <TableRow>
            <TableHead class="w-[100px] text-base">Date</TableHead>
            <TableHead class="text-base">Event</TableHead>
            <TableHead class="w-[120px] text-base">Distance/Duration</TableHead>
            <TableHead class="w-[100px] text-base">Type</TableHead>
            <TableHead class="w-[200px] text-base">Location</TableHead>
            <TableHead class="text-right w-[200px] text-base"
              >Actions</TableHead
            >
          </TableRow>
        </TableHeader>
        <TableBody>
          <TableRow v-for="event in events" :key="event.EventID">
            <TableCell class="font-medium whitespace-nowrap text-base">
              <div class="flex items-center space-x-2">
                <CalendarIcon class="h-4 w-4 text-muted-foreground" />
                <span>{{ event.Startdate }}</span>
              </div>
            </TableCell>
            <TableCell class="text-base">
              <div class="flex flex-wrap items-center gap-2">
                <span>{{ event.EventName }}</span>
                <Badge
                  v-if="['G', 'S', 'B'].includes(event.IAULabel)"
                  variant="secondary"
                  :class="`${
                    iauLabelColors[event.IAULabel]
                  } text-xs px-1 py-0 leading-5 font-normal`"
                >
                  IAU {{ event.IAULabel }}
                </Badge>
              </div>
            </TableCell>
            <TableCell class="text-base">
              <Badge
                variant="secondary"
                :class="`whitespace-nowrap ${getDurationLengthColor(
                  event
                )} text-xs`"
              >
                <RulerIcon v-if="event.Length" class="h-3 w-3 mr-1" />
                <ClockIcon v-else class="h-3 w-3 mr-1" />
                <span>{{ event.Length || event.Duration }}</span>
              </Badge>
            </TableCell>
            <TableCell class="text-base">
              <Badge
                variant="secondary"
                :class="`whitespace-nowrap ${getTypeColor(
                  event.EventType
                )} text-xs`"
              >
                {{ getEventType(event.EventType) }}
              </Badge>
            </TableCell>
            <TableCell class="text-base">
              <div class="flex items-center space-x-2">
                <MapPinIcon class="h-4 w-4 text-muted-foreground" />
                <span>{{ event.City }} ({{ event.Country }})</span>
              </div>
            </TableCell>
            <TableCell class="text-right text-base">
              <div class="flex justify-end space-x-2">
                <Button variant="outline" size="sm">Details</Button>
                <Button size="sm">Results</Button>
              </div>
            </TableCell>
          </TableRow>
        </TableBody>
      </Table>
    </div>

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
