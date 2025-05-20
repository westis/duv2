export function buildUrl(
  base: string,
  params: Record<string, string | number | undefined>
) {
  // TODO: Implement URL construction utilities
  const query = Object.entries(params)
    .filter(([, value]) => value !== undefined)
    .map(
      ([key, value]) =>
        `${encodeURIComponent(key)}=${encodeURIComponent(String(value))}`
    )
    .join("&");
  return query ? `${base}?${query}` : base;
}
