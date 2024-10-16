interface EventData {
  EventType: string;
  Length: string;
  Duration: string;
}

interface MappedEventType {
  type: string;
  surface: string;
}

export function useEventTypeMapper() {
  const mapEventType = (event: EventData): MappedEventType => {
    const eventTypeNumber = parseInt(event.EventType);
    let type = "Other";
    let surface = "Unknown";

    // Determine event type
    if (eventTypeNumber === 10) {
      type = "Backyard Ultra";
    } else if (eventTypeNumber === 4) {
      type = "Stage race";
    } else if (eventTypeNumber >= 11 && eventTypeNumber <= 14) {
      type = "Walking";
    } else if (event.Duration) {
      type = "Fixed-time";
    } else if (event.Length) {
      type = "Fixed-distance";
    }

    // Determine surface
    switch (eventTypeNumber) {
      case 1:
      case 3:
      case 11:
      case 12:
        surface = "Road";
        break;
      case 2:
        surface = "Trail";
        break;
      case 5:
      case 13:
        surface = "Track";
        break;
      case 6:
      case 14:
        surface = "Indoor";
        break;
    }

    return { type, surface };
  };

  const getEventTypeColor = (event: EventData) => {
    const { type } = mapEventType(event);
    switch (type) {
      case "Fixed-distance":
        return "bg-blue-200/80 dark:bg-blue-800/80 text-blue-800 dark:text-blue-200";
      case "Fixed-time":
        return "bg-yellow-200/80 dark:bg-yellow-800/80 text-yellow-800 dark:text-yellow-200";
      case "Backyard Ultra":
        return "bg-violet-200/80 dark:bg-violet-800/80 text-violet-800 dark:text-violet-200";
      case "Stage race":
        return "bg-rose-200/80 dark:bg-rose-800/80 text-rose-800 dark:text-rose-200";
      case "Walking":
        return "bg-emerald-200/80 dark:bg-emerald-800/80 text-emerald-800 dark:text-emerald-200";
      default:
        return "bg-neutral-200/80 dark:bg-neutral-800/80 text-neutral-800 dark:text-neutral-200";
    }
  };

  const getSurfaceColor = (event: EventData) => {
    const { surface } = mapEventType(event);
    switch (surface) {
      case "Road":
        return "bg-gray-200/80 dark:bg-gray-700/80 text-gray-800 dark:text-gray-200";
      case "Trail":
        return "bg-green-200/80 dark:bg-green-800/80 text-green-800 dark:text-green-200";
      case "Track":
        return "bg-red-200/80 dark:bg-red-800/80 text-red-800 dark:text-red-200";
      case "Indoor":
        return "bg-purple-200/80 dark:bg-purple-800/80 text-purple-800 dark:text-purple-200";
      default:
        return "bg-neutral-200/80 dark:bg-neutral-800/80 text-neutral-800 dark:text-neutral-200";
    }
  };

  const getIAULabelColor = (iauLabel: string) => {
    const colors = {
      G: "bg-yellow-200 text-yellow-800",
      S: "bg-gray-200 text-gray-800",
      B: "bg-amber-200 text-amber-800",
    };
    return colors[iauLabel as keyof typeof colors] || "";
  };

  return {
    mapEventType,
    getEventTypeColor,
    getSurfaceColor,
    getIAULabelColor,
  };
}
