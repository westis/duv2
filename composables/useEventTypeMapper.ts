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

  return {
    mapEventType,
  };
}
