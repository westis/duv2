<template>
  <header
    class="border-b border-background-200 bg-background-50 text-text-900 dark:bg-background-900 dark:text-text-50"
  >
    <div class="container mx-auto px-4 sm:px-6 lg:px-8">
      <div class="flex h-16 items-center justify-between">
        <!-- Logo -->
        <NuxtLink to="/" class="flex-shrink-0 mr-4">
          <img
            :src="logoSrc"
            alt="DUV Logo"
            class="h-8 w-auto sm:h-10 md:h-12 max-w-[150px] object-contain"
          />
        </NuxtLink>

        <!-- Centered Navigation Menu (hidden on smaller screens) -->
        <div class="hidden md:flex md:flex-1 justify-center">
          <NavigationMenu>
            <NavigationMenuList>
              <!-- Events Submenu -->
              <NavigationMenuItem>
                <NavigationMenuTrigger>Events</NavigationMenuTrigger>
                <NavigationMenuContent>
                  <ul
                    class="grid w-[400px] gap-3 p-4 md:w-[500px] md:grid-cols-2 lg:w-[600px]"
                  >
                    <li>
                      <NavigationMenuLink :as-child="true">
                        <NuxtLink
                          :to="calendarLink"
                          class="block select-none space-y-1 rounded-md p-3 leading-none no-underline outline-none transition-colors hover:bg-accent hover:text-accent-foreground focus:bg-accent focus:text-accent-foreground"
                        >
                          <div class="text-sm font-medium leading-none">
                            Calendar
                          </div>
                          <p
                            class="line-clamp-2 text-sm leading-snug text-muted-foreground"
                          >
                            View upcoming events for the next year
                          </p>
                        </NuxtLink>
                      </NavigationMenuLink>
                    </li>
                    <li>
                      <NavigationMenuLink :as-child="true">
                        <NuxtLink
                          :to="resultsLink"
                          class="block select-none space-y-1 rounded-md p-3 leading-none no-underline outline-none transition-colors hover:bg-accent hover:text-accent-foreground focus:bg-accent focus:text-accent-foreground"
                        >
                          <div class="text-sm font-medium leading-none">
                            Results
                          </div>
                          <p
                            class="line-clamp-2 text-sm leading-snug text-muted-foreground"
                          >
                            View results from the past year
                          </p>
                        </NuxtLink>
                      </NavigationMenuLink>
                    </li>
                  </ul>
                </NavigationMenuContent>
              </NavigationMenuItem>

              <!-- Statistics Submenu -->
              <NavigationMenuItem>
                <NavigationMenuTrigger>Statistics</NavigationMenuTrigger>
                <NavigationMenuContent>
                  <ul
                    class="grid w-[400px] gap-3 p-4 md:w-[500px] md:grid-cols-2 lg:w-[600px]"
                  >
                    <li v-for="item in statisticsItems" :key="item.title">
                      <NavigationMenuLink :as-child="true">
                        <NuxtLink
                          :to="item.href"
                          class="block select-none space-y-1 rounded-md p-3 leading-none no-underline outline-none transition-colors hover:bg-accent hover:text-accent-foreground focus:bg-accent focus:text-accent-foreground"
                        >
                          <div class="text-sm font-medium leading-none">
                            {{ item.title }}
                          </div>
                          <p
                            class="line-clamp-2 text-sm leading-snug text-muted-foreground"
                          >
                            {{ item.description }}
                          </p>
                        </NuxtLink>
                      </NavigationMenuLink>
                    </li>
                  </ul>
                </NavigationMenuContent>
              </NavigationMenuItem>

              <!-- About Submenu -->
              <NavigationMenuItem>
                <NavigationMenuTrigger>About</NavigationMenuTrigger>
                <NavigationMenuContent>
                  <ul
                    class="grid w-[400px] gap-3 p-4 md:w-[500px] md:grid-cols-2 lg:w-[600px]"
                  >
                    <li v-for="item in aboutItems" :key="item.title">
                      <NavigationMenuLink :as-child="true">
                        <NuxtLink
                          :to="item.href"
                          class="block select-none space-y-1 rounded-md p-3 leading-none no-underline outline-none transition-colors hover:bg-accent hover:text-accent-foreground focus:bg-accent focus:text-accent-foreground"
                        >
                          <div class="text-sm font-medium leading-none">
                            {{ item.title }}
                          </div>
                          <p
                            class="line-clamp-2 text-sm leading-snug text-muted-foreground"
                          >
                            {{ item.description }}
                          </p>
                        </NuxtLink>
                      </NavigationMenuLink>
                    </li>
                  </ul>
                </NavigationMenuContent>
              </NavigationMenuItem>
            </NavigationMenuList>
          </NavigationMenu>
        </div>

        <!-- Right side buttons including search -->
        <div class="flex items-center space-x-2 sm:space-x-4">
          <!-- Search Button (consistent across all screen sizes) -->
          <Button
            @click="toggleSearch"
            variant="outline"
            class="inline-flex items-center text-muted-foreground font-normal"
          >
            <Icon
              icon="heroicons:magnifying-glass-20-solid"
              class="me-2 h-4 w-4"
            />
            <span>Search runner, event, club...</span>
            <kbd
              class="ml-2 hidden sm:inline-flex h-5 select-none items-center gap-1 rounded border bg-background px-1.5 font-mono text-xs font-medium text-foreground"
            >
              <span class="text-xs">⌘</span><span class="text-xs">K</span>
            </kbd>
          </Button>

          <!-- Dark Mode Toggle -->
          <Button @click="toggleColorMode" variant="outline" size="icon">
            <Icon
              :icon="
                colorMode.preference === 'dark'
                  ? 'heroicons:sun-20-solid'
                  : 'heroicons:moon-20-solid'
              "
              class="h-5 w-5"
            />
            <span class="sr-only">Toggle theme</span>
          </Button>

          <!-- Hamburger Menu (visible on smaller screens) -->
          <Button
            @click="toggleMobileMenu"
            variant="outline"
            size="icon"
            class="md:hidden"
          >
            <Icon icon="heroicons:bars-3-20-solid" class="h-5 w-5" />
            <span class="sr-only">Toggle menu</span>
          </Button>
        </div>
      </div>
    </div>

    <!-- Mobile Navigation Menu -->
    <div
      v-if="isMobileMenuOpen"
      class="md:hidden fixed inset-0 z-50 bg-background overflow-y-auto"
    >
      <div class="p-4">
        <Button
          @click="toggleMobileMenu"
          variant="outline"
          size="icon"
          class="absolute top-4 right-4"
        >
          <Icon icon="heroicons:x-mark-20-solid" class="h-5 w-5" />
          <span class="sr-only">Close menu</span>
        </Button>
        <nav class="mt-8">
          <ul class="space-y-4">
            <li v-for="item in navigationItems" :key="item.title">
              <div class="text-md font-medium mb-2">{{ item.title }}</div>
              <ul class="space-y-2">
                <li
                  v-for="subItem in getSubmenuItems(item.title)"
                  :key="subItem.title"
                >
                  <NuxtLink
                    :to="subItem.href"
                    class="block px-2 py-1 rounded-md text-sm text-muted-foreground hover:bg-accent"
                    @click="toggleMobileMenu"
                  >
                    {{ subItem.title }}
                  </NuxtLink>
                </li>
              </ul>
            </li>
          </ul>
        </nav>
      </div>
    </div>

    <!-- CommandDialog for search -->
    <CommandDialog :open="isSearchOpen" @update:open="toggleSearch">
      <CommandInput placeholder="Type a command or search..." />
      <CommandList>
        <CommandEmpty>No results found.</CommandEmpty>
        <CommandGroup heading="Links">
          <CommandItem
            v-for="link in links"
            :key="link.title"
            :value="link.title"
            @select="() => handleItemSelect(link)"
          >
            {{ link.title }}
          </CommandItem>
        </CommandGroup>
        <CommandGroup heading="Search">
          <CommandItem
            v-for="option in searchOptions"
            :key="option.title"
            :value="option.title"
            @select="() => handleItemSelect(option)"
          >
            {{ option.title }}
          </CommandItem>
        </CommandGroup>
      </CommandList>
    </CommandDialog>
  </header>
</template>

<script setup lang="ts">
import { ref, computed, watch } from "vue";
import { format, subYears, addYears } from "date-fns";
import { useMagicKeys } from "@vueuse/core";
import { Button } from "@/components/ui/button";
import {
  CommandDialog,
  CommandInput,
  CommandList,
  CommandEmpty,
  CommandGroup,
  CommandItem,
} from "@/components/ui/command";
import {
  NavigationMenu,
  NavigationMenuContent,
  NavigationMenuItem,
  NavigationMenuLink,
  NavigationMenuList,
  NavigationMenuTrigger,
} from "@/components/ui/navigation-menu";
import { Icon } from "@iconify/vue";

const colorMode = useColorMode();
const isSearchOpen = ref(false);
const isMobileMenuOpen = ref(false);

const logoSrc = ref(
  colorMode.preference === "dark"
    ? "/duv_logo_with_name_white.png"
    : "/duv_logo_with_name.png"
);

watch(
  () => colorMode.preference,
  (newPreference) => {
    logoSrc.value =
      newPreference === "dark"
        ? "/duv_logo_with_name_white.png"
        : "/duv_logo_with_name.png";
  }
);

const toggleColorMode = () => {
  colorMode.preference = colorMode.preference === "dark" ? "light" : "dark";
};

const toggleSearch = () => {
  isSearchOpen.value = !isSearchOpen.value;
};

const toggleMobileMenu = () => {
  isMobileMenuOpen.value = !isMobileMenuOpen.value;
};

const navigationItems = [
  { title: "Events", href: "/events" },
  { title: "Statistics", href: "/statistics" },
  { title: "About", href: "/about" },
];

const expandedMenus = ref<Record<string, boolean>>({
  Events: false,
  Statistics: false,
  About: false,
});

const toggleSubmenu = (menuTitle: string) => {
  expandedMenus.value[menuTitle] = !expandedMenus.value[menuTitle];
};

const today = new Date();
const oneYearAgo = subYears(today, 1);
const oneYearLater = addYears(today, 1);

const calendarLink = computed(() => {
  const fromDate = format(today, "yyyy-MM-dd");
  const toDate = format(oneYearLater, "yyyy-MM-dd");
  return `/events?from=${fromDate}&to=${toDate}&order=asc`;
});

const resultsLink = computed(() => {
  const fromDate = format(oneYearAgo, "yyyy-MM-dd");
  const toDate = format(today, "yyyy-MM-dd");
  return `/events?from=${fromDate}&to=${toDate}&order=desc`;
});

// Define eventsItems, statisticsItems, and aboutItems
const eventsItems = computed(() => [
  {
    title: "Calendar",
    href: calendarLink.value,
    description: "View upcoming ultramarathon events",
  },
  {
    title: "Results",
    href: resultsLink.value,
    description: "Check results from past events",
  },
  {
    title: "Championships",
    href: "/championships",
    description: "Information about championship events",
  },
]);

const statisticsItems = [
  {
    title: "Toplists",
    href: "/toplists",
    description: "View top performers in various categories",
  },
  {
    title: "Records",
    href: "/records",
    description: "Browse ultrarunning records",
  },
  {
    title: "Country Stats",
    href: "/country-stats",
    description: "Statistics by country",
  },
];

const aboutItems = [
  {
    title: "About Website",
    href: "/about",
    description: "Learn more about our website",
  },
  {
    title: "What's New",
    href: "/whats-new",
    description: "Recent updates and changes",
  },
  { title: "FAQ", href: "/faq", description: "Frequently asked questions" },
  { title: "Contact", href: "/contact", description: "Get in touch with us" },
  {
    title: "Credits",
    href: "/credits",
    description: "Acknowledgments and credits",
  },
];

const getSubmenuItems = (menuTitle: string) => {
  switch (menuTitle) {
    case "Events":
      return eventsItems.value;
    case "Statistics":
      return statisticsItems;
    case "About":
      return aboutItems;
    default:
      return [];
  }
};

const links = computed(() => [
  { title: "Calendar", href: calendarLink.value },
  { title: "Results", href: resultsLink.value },
  { title: "Toplists", href: "/toplists" },
]);

const searchOptions = [
  { title: "Search Runner", action: "searchRunner" },
  { title: "Search Event", action: "searchEvent" },
  { title: "Search Club", action: "searchClub" },
];

const handleItemSelect = (item: any) => {
  if (item.href) {
    // Navigate to the link
    navigateTo(typeof item.href === "function" ? item.href() : item.href);
  } else if (item.action) {
    // Handle search actions
    switch (item.action) {
      case "searchRunner":
        // Implement runner search
        break;
      case "searchEvent":
        // Implement event search
        break;
      case "searchClub":
        // Implement club search
        break;
    }
  }
  toggleSearch(); // Close the search dialog after selection
};

// Keyboard shortcut functionality
const { Cmd_k, Ctrl_k } = useMagicKeys();
const shortcutPressed = computed(() => Cmd_k.value || Ctrl_k.value);

const handleKeyDown = (event: KeyboardEvent) => {
  if ((event.metaKey || event.ctrlKey) && event.key === "k") {
    event.preventDefault();
    toggleSearch();
  }
};

onMounted(() => {
  window.addEventListener("keydown", handleKeyDown);
});

onUnmounted(() => {
  window.removeEventListener("keydown", handleKeyDown);
});
</script>
