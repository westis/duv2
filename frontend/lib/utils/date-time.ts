import { useLocale } from "next-intl";

/**
 * Formats a Date or ISO string into a locale-aware date+time string.
 */
export function formatDateTime(
  date: Date | string,
  locale: string,
  options?: Intl.DateTimeFormatOptions
): string {
  const d = typeof date === "string" ? new Date(date) : date;
  return new Intl.DateTimeFormat(locale, {
    dateStyle: "medium",
    timeStyle: "short",
    ...options,
  }).format(d);
}

/**
 * React hook to get a locale-aware date/time formatter.
 */
export function useFormattedDateTime(
  options?: Intl.DateTimeFormatOptions
): (value: Date | string) => string {
  const locale = useLocale();
  return (value) => formatDateTime(value, locale, options);
}
