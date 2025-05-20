import { useLocale } from "next-intl";

/**
 * Formats a number into a locale-aware string.
 */
export function formatNumber(
  value: number,
  locale: string,
  options?: Intl.NumberFormatOptions
): string {
  return new Intl.NumberFormat(locale, options).format(value);
}

/**
 * React hook to get a locale-aware number formatter.
 */
export function useFormattedNumber(
  options?: Intl.NumberFormatOptions
): (value: number) => string {
  const locale = useLocale();
  return (value) => new Intl.NumberFormat(locale, options).format(value);
}
